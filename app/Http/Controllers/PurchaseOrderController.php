<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function indexPurchaseOrder()
    {

        $purchase_orders = PurchaseOrder::with('purchaseOrderItems')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Purchases.PO.index', compact('purchase_orders'));
    }

    public function createPurchaseOrder()
    {

        $prefix = 'PO';
        $date = date('Ymd');
        $latestOrder = PurchaseOrder::query()
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($latestOrder) {
            $lastNumber = intval(substr($latestOrder->order_number, -4));
            $nextNumber = $lastNumber + 1;
        }

        $poNumber = sprintf('%s/%s/%04d', $prefix, $date, $nextNumber);

        $suppliers = Vendor::all();
        $allItems = Item::with(['itemUnits', 'itemCosts'])->get();
        $units = Unit::all();
        $taxes = Tax::all();
        return view('Purchases.PO.auto_generate.create', compact('suppliers', 'allItems', 'units', 'taxes', 'poNumber'));
    }

    public function storePurchaseOrder(Request $request)
    {
        try {
            Log::info('Starting Purchase Order creation.', ['request_data' => $request->all()]);

            // Validate request data
            $validated = $request->validate([
                'supplier_id' => 'required|exists:vendors,id',
                'order_number' => 'required|string',
                // 'price_setup' => 'required|string|in:retail,wholesale', // Assuming this is commented out intentionally
                'order_date' => 'required|date',
                'items.*.item_id' => 'required|exists:items,id', // Corrected validation rule to use item_id
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount' => 'nullable|numeric|min:0',
                'items.*.tax_id' => 'nullable|exists:taxes,id',
                'items.*.total_price' => 'required|numeric|min:0',
                'tax' => 'nullable|numeric|min:0', // Assuming this is for overall tax, if needed - not in request data example
                'expected_delivery_date' => 'required|date',
                'discount' => 'nullable|numeric|min:0', // Assuming this is for overall discount, if needed - not in request data example
                'total' => 'required|numeric|min:0',
            ]);

            Log::info('Validation successful.', ['validated_data' => $validated]);

            // Create Purchase Order
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'order_number' => $validated['order_number'],
                'order_date' => $validated['order_date'],
                'total_amount' => $validated['total'],
                'notes' => $request->input('notes'),
                'expected_delivery_date' => $request->input('expected_delivery_date'),
            ]);

            Log::info('Purchase Order created.', ['purchase_order_id' => $purchaseOrder->id]);

            // Add Purchase Order Items
            foreach ($validated['items'] as $item) {
                $purchaseOrderItem = PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item['item_id'], // Use item_id here as well for consistency
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? null,
                    'tax_id' => $item['tax_id'] ?? null,
                    'total_price' => $item['total_price'],
                ]);

                Log::info('Purchase Order Item created.', ['purchase_order_item_id' => $purchaseOrderItem->id]);
            }

            Log::info('Purchase Order creation completed successfully.', ['purchase_order_id' => $purchaseOrder->id]);

            return redirect()->route('list_po')->with('success', 'Purchase Order created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error while creating Purchase Order.', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error while creating Purchase Order.', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.')->withInput();
        }
    }

    public function previewPurchaseOrder($id)
    {
        // Fetch the purchase order with all related details
        $purchase_orders = PurchaseOrder::with([
            'purchaseOrderItems' => function ($query) {
                $query->with([
                    'item',
                    'unit',
                    'tax'
                ]);
            },
            'supplier'
        ])->findOrFail($id);

        // Calculate summary totals
        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;
        $total = 0;

        foreach ($purchase_orders->purchaseOrderItems as $item) {
            // Calculate item-level totals
            $itemSubtotal = $item->quantity * $item->unit_price;
            $itemDiscount = $item->discount;

            // Calculate tax
            $taxAmount = 0;
            if ($item->tax) {
                $taxAmount = $item->tax->tax_mode === 'percentage'
                    ? ($itemSubtotal * ($item->tax->tax_percentage / 100))
                    : $item->tax->tax_amount;
            }

            // Accumulate totals
            $subtotal += $itemSubtotal;
            $totalTax += $taxAmount;
            $totalDiscount += $itemDiscount;
        }

        // Calculate final total
        $total = $subtotal + $totalTax - $totalDiscount;

        // Prepare additional data
        $additionalData = [
            'subtotal' => round($subtotal, 2),
            'tax' => round($totalTax, 2),
            'total_discount' => round($totalDiscount, 2),
            'total' => round($total, 2),
            'order_number' => $purchase_orders->order_number,
            'order_date' => $purchase_orders->order_date,
            'expected_delivery_date' => $purchase_orders->expected_delivery_date,
            'purchase_order_id' => $purchase_orders->id,
        ];


        $items = Item::with(['itemUnits.unit', 'itemCosts'])->get();
        $taxes = Tax::all();
        $units = Unit::all();

        // Merge additional data with purchase orders
        $purchase_orders = collect($purchase_orders)->merge($additionalData);

        return view('Purchases.PO.auto_generate.preview', compact('purchase_orders', 'items', 'taxes', 'units'));
    }

    public function storeNewPurchaseOrderItem(Request $request)
    {
        // Validate the request data for the new item
        $validatedData = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'item_id' => 'required|exists:items,id',
            'unit_id' => 'required|exists:units,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            'total_price' => 'required|numeric|min:0', // Add validation for total_price
        ]);
    
        PurchaseOrderItem::create($validatedData);
    
        return response()->json(['message' => 'Item added to Purchase Order successfully!']);
    }

    public function updatePoStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Approved,Rejected,Partially_received,Cancelled,Completed',
        ]);

        // Find the purchase order by ID and update the status
        $purchaseOrder = PurchaseOrder::find($id);
        if ($purchaseOrder) {
            $purchaseOrder->status = $request->input('status');
            $purchaseOrder->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Purchase Order not found.'], 404);
    }

    public function storePurchaseOrderItems(Request $request, $id)
    {
        \Log::info('Incoming request data: ', $request->all());

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:items,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_id' => 'nullable|exists:taxes,id',
            'items.*.total_price' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::with('purchaseOrderItems')->findOrFail($id);

            foreach ($request->items as $item) {
                // Check if the item already exists for this purchase order
                $existingItem = PurchaseOrderItem::where('purchase_order_id', $id)
                    ->where('item_id', $item['product_id'])
                    ->first();

                if ($existingItem) {
                    // Handle duplicate item (e.g., return an error or update quantity)
                    DB::rollBack(); // Rollback to prevent partial updates
                    return response()->json([
                        'success' => false,
                        'message' => 'Item ' . $item['product_id'] . ' is already added to this purchase order.'
                    ], 422);

                } else {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $id,
                        'item_id' => $item['product_id'],
                        'unit_id' => $item['unit_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?? 0,
                        'tax_id' => $item['tax_id'],
                        'total_price' => $item['total_price']
                    ]);
                }
            }

            $this->updatePurchaseOrderTotals($id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order Items registered successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error registering purchase order items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error registering purchase order items.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function updatePurchaseOrderTotals($id)
    {
        $purchaseOrder = PurchaseOrder::with('purchaseOrderItems')->find($id);

        if (!$purchaseOrder) {
            throw new \Exception('Purchase order not found');
        }

        // Log the items being processed
        $items = $purchaseOrder->purchaseOrderItems; // Use the correct relationship name

        // Check if items is null or not a collection
        if ($items === null || !$items instanceof \Illuminate\Database\Eloquent\Collection) {
            throw new \Exception('No items found for this purchase order.');
        }

        \Log::info('Updating totals for purchase order: ' . $id, ['items' => $items]);

        // Calculate totals
        $subtotal = $items->sum('total_price');
        $totalDiscount = $items->sum('discount');
        $totalTax = $items->sum(function ($item) {
            if ($item->tax) {
                return $item->tax->tax_mode === 'percentage'
                    ? ($item->total_price * $item->tax->tax_percentage / 100)
                    : $item->tax->tax_amount;
            }
            return 0;
        });

        // Update purchase order
        $purchaseOrder->update([
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'total_tax' => $totalTax,
            'grand_total' => $subtotal - $totalDiscount + $totalTax
        ]);
    }
    public function updatePurchaseOrderItem(Request $request, $id)
    {

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
        ]);

        $purchaseOrderItem = PurchaseOrderItem::find($id);

        // Check if the item exists
        if (!$purchaseOrderItem) {
            return response()->json(['success' => false, 'message' => 'Purchase Order Item not found.'], 404);
        }

        // Update the item with the new data
        $purchaseOrderItem->item_id = $request->input('item_id');
        $purchaseOrderItem->quantity = $request->input('quantity');
        $purchaseOrderItem->unit_price = $request->input('unit_price');
        $purchaseOrderItem->discount = $request->input('discount', 0);
        $purchaseOrderItem->tax_id = $request->input('tax_id');

        // Save the changes
        $purchaseOrderItem->save();

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Purchase Order Item updated successfully.']);
    }

    public function deletePurchaseOrderItem($id)
    {
        // Find the purchase order item by ID
        $purchaseOrderItem = PurchaseOrderItem::find($id);

        if (!$purchaseOrderItem) {
            return response()->json(['success' => false, 'message' => 'Purchase Order Item not found.'], 404);
        }

        $purchaseOrderItem->delete();

        return redirect()->back()->with('message', 'Purchase Order Item deleted successfully.');
    }
}
