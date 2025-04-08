<?php

namespace App\Http\Controllers;

use App\Models\ExtraCharge;
use App\Models\GoodReceiptNote;
use App\Models\GoodReceiptNoteExtraCharge;
use App\Models\GoodReceiveNote;
use App\Models\GoodReceiveNoteItem;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodReceiveNoteController extends Controller
{
    public function indexGoodReceiveNote()
    {
        $good_receipt_notes = GoodReceiptNote::with(['purchaseOrder', 'supplier', 'goodReceiveNoteItems', 'goodReceiveNoteExtraCharges'])
            ->get()
            ->map(function ($grn) {
                $grnItemsTotalAmount = $grn->goodReceiveNoteItems->sum(function ($grnItem) {
                    return $grnItem->received_quantity * $grnItem->unit_price;
                });

                $grnExtraChargesTotalAmount = $grn->goodReceiveNoteExtraCharges->sum('amount');
                $grn->grand_total_amount = $grnItemsTotalAmount + $grnExtraChargesTotalAmount; // Add a new attribute to the GRN model for grand total
                return $grn; 
            });

        return view('Purchases.GRN.index', compact('good_receipt_notes'));
    }


    public function createGoodReceiveNote()
    {
        $prefix = 'GRN';
        $date = date('Ymd');
        $latestGrn = GoodReceiptNote::query()
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($latestGrn) {
            $lastNumber = intval(substr($latestGrn->grn_number, -4));
            $nextNumber = $lastNumber + 1;
        }
        $grnNumber = sprintf('%s/%s/%04d', $prefix, $date, $nextNumber);

        $purchaseOrders = PurchaseOrder::where(function ($query) {
            $query->where('status', 'Completed')
                ->orWhere('status', 'Partially_received');
        })
            ->whereDoesntHave('goodReceiveNotes')
            ->with(['supplier', 'purchaseOrderItems.item', 'purchaseOrderItems.unit'])
            ->get();


        $vendors = Vendor::all();
        $units = Unit::all();
        $extra_charges = ExtraCharge::all();
        $allItems = Item::with(['itemUnits', 'itemCosts'])->get();

        return view('Purchases.GRN.auto_generate.create', compact(
            'grnNumber',
            'vendors',
            'purchaseOrders',
            'units',
            'allItems',
            'extra_charges'
        ));
    }


    public function storeGoodReceiveNote(Request $request)
    {
        Log::info('Starting GRN creation attempt.', ['request_data' => $request->all()]);

        try {
            Log::info('Entering try block for GRN creation.');

            $validatedData = $request->validate([
                'vendor_id' => 'required|exists:vendors,id',
                'po_reference_number' => 'nullable|exists:purchase_orders,id',
                'grn_number' => 'required|unique:good_receipt_notes,grn_number',
                'received_date' => 'required|date',
                'delivery_note_number' => 'required|string|max:255',
                'received_condition' => 'nullable|in:Good,Damaged,Expired',
                'remarks' => 'nullable|string',
                'total_items' => 'required|integer|min:0',
                'total_accepted' => 'required|integer|min:0',
                'total_rejected' => 'required|integer|min:0',
                'total_amount' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'nullable|exists:items,id', // corrected from product_id to item_id to match your JS and likely DB field
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.received_qty' => 'required|numeric|min:0',
                'items.*.accepted_qty' => 'required|numeric|min:0',
                'items.*.rejected_qty' => 'required|numeric|min:0',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.total_price' => 'required|numeric|min:0',
                'items.*.received_condition' => 'nullable|in:Good,Damaged,Expired', // Add validation for received_condition for items
                'extra_charges' => 'nullable|array',
                'extra_charges.*.extra_charge_id' => 'required|exists:extra_charges,id',
                'extra_charges.*.unit_id' => 'required|exists:units,id',
                'extra_charges.*.amount' => 'required|numeric|min:0',
            ]);

            Log::info('Data validated successfully.', ['validated_data' => $validatedData]);

            DB::beginTransaction(); // Start transaction for data integrity

            try {
                $po = null; // Initialize $po to null
                if ($validatedData['po_reference_number']) { // Check if po_reference_number is provided
                    $po = PurchaseOrder::find($validatedData['po_reference_number']); // Use find() instead of findOrFail()
                    if (!$po) { // Handle case where PO is not found even if po_reference_number is given
                        throw new \Exception('Purchase Order not found with the provided reference number.'); // Or handle as needed
                    }
                }
                $grn = new GoodReceiptNote();
                $grn->grn_number = $validatedData['grn_number'];
                $grn->purchase_order_id = $validatedData['po_reference_number'];
                $grn->supplier_id = $validatedData['vendor_id'];
                $grn->received_by = Auth::id();
                $grn->received_date = $validatedData['received_date'];
                $grn->status = 'Pending';
                $grn->remarks = $validatedData['remarks'];
                $grn->delivery_note_number = $validatedData['delivery_note_number'];
                $grn->save();

                Log::info('GoodReceiveNote created.', ['grn_id' => $grn->id]);

                // Process Items
                foreach ($validatedData['items'] as $itemData) {
                    $poItem = null; // Initialize $poItem to null
                    if ($po) { // Only try to find PO item if $po is not null
                        $poItem = $po->purchaseOrderItems()->where('item_id', $itemData['item_id'])->first();
                    }
                    $grnItem = new GoodReceiveNoteItem();
                    $grnItem->grn_id = $grn->id;
                    $grnItem->purchase_order_item_id = $poItem ? $poItem->id : null;
                    $grnItem->item_id = $itemData['item_id'];
                    $grnItem->ordered_quantity = $poItem ? $poItem->quantity : 0;
                    $grnItem->received_quantity = $itemData['received_qty'];
                    $grnItem->accepted_quantity = $itemData['accepted_qty'];
                    $grnItem->rejected_quantity = $itemData['rejected_qty'];
                    $grnItem->received_condition = $itemData['received_condition'];
                    $grnItem->unit_price = $itemData['unit_price'];
                    $grnItem->save();

                }

                // Process Extra Charges
                if (isset($validatedData['extra_charges'])) {
                    foreach ($validatedData['extra_charges'] as $extraChargeData) {
                        $grnExtraCharge = new GoodReceiptNoteExtraCharge();
                        $grnExtraCharge->grn_id = $grn->id;
                        $grnExtraCharge->extra_charge_id = $extraChargeData['extra_charge_id'];
                        $grnExtraCharge->unit_id = $extraChargeData['unit_id'];
                        $grnExtraCharge->amount = $extraChargeData['amount'];
                        $grnExtraCharge->save();
                    }
                }

                DB::commit();
                Log::info('GRN and related data saved successfully.', ['grn_number' => $grn->grn_number]);

                return redirect()->route('list_grn')->with('success', 'GRN created successfully');

            } catch (\Exception $dbException) {
                DB::rollback();
                Log::error('Database error during GRN creation.', [
                    'exception' => $dbException,
                    'message' => $dbException->getMessage(),
                    'trace' => $dbException->getTraceAsString(),
                    'request_data' => $request->all()
                ]);
                return redirect()->back()->with('error', 'Database error occurred while creating GRN. Please check logs for details');
            }


        } catch (\Illuminate\Validation\ValidationException $validationException) {
            // Catch validation exceptions specifically
            Log::warning('GRN Validation failed.', [
                'errors' => $validationException->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json(['errors' => $validationException->errors()], 422); // 422 Unprocessable Entity for validation errors

        } catch (\Exception $e) {
            // Catch all other exceptions
            Log::error('Error creating GRN.', [
                'exception' => $e,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Failed to create GRN. Please check the logs for details.');
        }
    }

    public function previewGoodReceiveNote($id)
    {
        $good_receipt_note = GoodReceiptNote::findOrFail($id);

        $good_receipt_note->load([
            'purchaseOrder',
            'supplier',
            'goodReceiveNoteItems.item.units',
            'goodReceiveNoteExtraCharges.extraCharge',
            'goodReceiveNoteExtraCharges.unit',
        ]);
        // 1. Calculate total amount for GRN Items
        $grnItemsTotalAmount = $good_receipt_note->goodReceiveNoteItems->sum(function ($grnItem) {
            return $grnItem->received_quantity * $grnItem->unit_price;
        });

        // 2. Calculate total amount for GRN Extra Charges
        $grnExtraChargesTotalAmount = $good_receipt_note->goodReceiveNoteExtraCharges->sum('amount');

        // 3. Calculate Grand Total Amount (sum of items total and extra charges total)
        $grandTotalAmount = $grnItemsTotalAmount + $grnExtraChargesTotalAmount;

        // Pass the calculated grandTotalAmount to the view along with the good_receipt_note
        return view('Purchases.GRN.auto_generate.preview', compact('good_receipt_note', 'grandTotalAmount'));
    }

    public function updateGrnStatus(Request $request, $id) // Generic status update function - can be reused if needed later
    {
        $request->validate([
            'status' => 'required|in:Pending,Inspected,Verified,Accepted,Rejected,Completed,Cancelled,Reopened', // Add 'Reopened' and 'Inspected', 'Verified' and more GRN statuses
        ]);

        $grn = GoodReceiptNote::find($id);
        if ($grn) {
            $grn->status = $request->input('status');
            $grn->save();
            return response()->json(['success' => true, 'message' => 'GRN status updated to ' . $grn->status . ' successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Good Receive Note not found.'], 404);
    }

    public function verifyGRN(Request $request, $id)
    {
        return $this->updateGrnStatusSpecific($id, 'Verified', 'GRN Verified Successfully'); // Status changed to 'Verified'
    }

    public function acceptGRN(Request $request, $id)
    {
        return $this->updateGrnStatusSpecific($id, 'Accepted', 'GRN Accepted Successfully'); // Status changed to 'Accepted'
    }

    public function rejectGRN(Request $request, $id)
    {
        return $this->updateGrnStatusSpecific($id, 'Rejected', 'GRN Rejected Successfully'); // Status changed to 'Rejected'
    }

    public function completeGRN(Request $request, $id)
    {
        return $this->updateGrnStatusSpecific($id, 'Completed', 'GRN Completed Successfully'); // Status changed to 'Completed'
    }

    public function reopenGRN(Request $request, $id)
    {
        return $this->updateGrnStatusSpecific($id, 'Reopened', 'GRN Reopened Successfully'); // Status changed to 'Reopened'
    }

    public function cancelGRN(Request $request, $id)
    {
        return $this->updateGrnStatusSpecific($id, 'Cancelled', 'GRN Cancelled Successfully'); // Status changed to 'Cancelled'
    }


    private function updateGrnStatusSpecific($id, $status, $message)
    {
        $grn = GoodReceiptNote::find($id);
        if ($grn) {
            $grn->status = $status;

            if ($status === 'Completed') {
                // Load the GRN items relationship to access the items in this GRN
                $grn->load('goodReceiveNoteItems');

                // Start database transaction for data integrity
                DB::beginTransaction();
                try {
                    foreach ($grn->goodReceiveNoteItems as $grnItem) {
                        // Get item_id and accepted_quantity from GRN item
                        $itemId = $grnItem->item_id;
                        $acceptedQuantity = $grnItem->accepted_quantity;

                        $itemStock = ItemStock::where('item_id', $itemId)->first();

                        if ($itemStock) {
                            // Increase the stock_quantity by the accepted quantity
                            $itemStock->stock_quantity += $acceptedQuantity;
                            $itemStock->save();
                        } else {
                            \Log::warning("ItemStock record not found for item_id: {$itemId} when completing GRN: {$grn->grn_number}. Stock quantity not updated for this item in GRN completion process.");
                        }
                    }

                    $grn->save(); // Save the GRN status after processing items - moved inside try block
                    DB::commit(); // Commit transaction if all item stock updates are successful
                    return response()->json(['success' => true, 'message' => $message]);

                } catch (\Exception $e) {
                    DB::rollBack(); // Rollback transaction in case of any error
                    \Log::error("Error updating ItemStock for GRN completion: " . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Error updating item stock during GRN completion: ' . $e->getMessage()], 500); // Return error response
                }

            } else {
                $grn->save();
                return response()->json(['success' => true, 'message' => $message]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Good Receive Note not found.'], 404);
    }

}
