<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CustomerOrder;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Order;
use App\Models\OrderExtraCharge;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\VoidReason;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderSummaryController extends Controller
{
    public function indexOrderSummary(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));


        //payments
        $payments = Payment::all();
        // Fetch Orders
        $order_query = Order::query()
            ->select(['order_number', 'receipt_number', 'date', 'total_amount', 'status']);
        if ($date) {
            $order_query->whereDate('date', $date);
        }
        $order_summaries = $order_query->get();

        // Fetch Carts
        $cart_query = Cart::query()
            ->select(['order_number', 'date', 'total_amount', 'status']);
        if ($date) {
            $cart_query->whereDate('date', $date);
        }
        $cart_summaries = $cart_query->get();

        // Define status constants
        $in_cart_status = 'in-cart';
        $voided_status = 'voided';
        $settled_status = 'settled';

        // Counts
        $count_all_orders = Order::whereDate('date', $date)->count();
        $count_all_carts = Cart::whereDate('date', $date)->count();
        $count_all = $count_all_orders + $count_all_carts;
        $count_in_cart = Cart::whereDate('date', $date)->where('status', $in_cart_status)->count();
        $count_voided = Order::whereDate('date', $date)->where('status', $voided_status)->count();
        $count_settled = Order::whereDate('date', $date)->where('status', $settled_status)->count();

        return view('Sales.order_summary.index', compact(
            'order_summaries',
            'cart_summaries',
            'count_all',
            'count_in_cart',
            'count_voided',
            'count_settled',
            'date',
            'payments'
        ));
    }

    public function getItems(Request $request)
    {
        $order_number = $request->input('order_number');
        $type = $request->input('type'); // 'order' or 'cart'

        if ($type === 'order') {
            $order = Order::where('order_number', $order_number)->firstOrFail();
            $items = $order->orderItems; // Line causing the issue if null
        } else if ($type === 'cart') {
            $cart = Cart::where('order_number', $order_number)->firstOrFail();
            $items = $cart->cartItem; // Line causing the issue if null
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
        }

        return response()->json([
            'success' => true,
            'items' => $items->map(function ($item) { // Line 73: Error here if $items is null
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'unit' => $item->unit,
                    'quantity' => $item->quantity,
                    'amount' => $item->amount
                ];
            })
        ]);
    }

    public function updateItems(Request $request)
    {
        $order_number = $request->input('order_number');
        $type = $request->input('type');
        $items = $request->input('items');

        if ($type === 'order') {
            $order = Order::where('order_number', $order_number)->firstOrFail();
            foreach ($items as $itemData) {
                $order->orderItems()->updateOrCreate(
                    ['id' => $itemData['id']],
                    ['name' => $itemData['name'], 'unit' => $itemData['unit'], 'quantity' => $itemData['quantity'], 'amount' => $itemData['amount']]
                );
            }
        } else if ($type === 'cart') {
            $cart = Cart::where('order_number', $order_number)->firstOrFail();
            foreach ($items as $itemData) {
                $cart->cartItem()->updateOrCreate(
                    ['id' => $itemData['id']],
                    ['name' => $itemData['name'], 'unit' => $itemData['unit'], 'quantity' => $itemData['quantity'], 'amount' => $itemData['amount']]
                );
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Items updated successfully']);
    }

    public function settleCart(Request $request)
    {
        Log::info('Incoming settle cart request data', $request->all());

        try {
            $validated = $request->validate([
                'order_number' => 'required|string|exists:carts,order_number',
                'payment_id' => 'required|exists:payments,id',
                'total_amount' => 'required|numeric',
                'tip' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();

            $cart = Cart::where('order_number', $validated['order_number'])->with('cartItem')->firstOrFail();
            Log::info('Cart Data:', $cart->toArray());
            Log::info('Cart Items:', $cart->cartItem->toArray());

            $items = $cart->cartItem->map(function ($cartItem) {
                $item = Item::where('name', $cartItem->name)->first();
                if (!$item) {
                    throw new \Exception('Item not found for name: ' . $cartItem->name);
                }
                return [
                    'item_id' => $item->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->amount / $cartItem->quantity,
                ];
            })->all();

            if (empty($items)) {
                throw new \Exception('No items found in the cart to settle.');
            }

            $orderNumber = $cart->order_number;
            $receiptNumber = 'REC-' . strtoupper(substr(uniqid(), -5));
            $discount = $validated['discount'] ?? 0;
            $tip = $validated['tip'] ?? 0;
            $grandTotal = $validated['total_amount'] + $tip - $discount;

            $order = Order::create([
                'order_number' => $orderNumber,
                'receipt_number' => $receiptNumber,
                'date' => $cart->date ?? now(),
                'customer_type_id' => $cart->customer_type_id ?? null,
                'total_amount' => $validated['total_amount'],
                'tip' => $tip,
                'discount' => $discount,
                'grand_total' => $grandTotal,
                'status' => 'settled',
                'is_active' => 1,
            ]);

            OrderPayment::create([
                'payment_id' => $validated['payment_id'],
                'order_id' => $order->id,
            ]);

            if ($cart->customer_id) {
                CustomerOrder::create([
                    'customer_id' => $cart->customer_id,
                    'order_id' => $order->id,
                ]);
            }

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);

                $stock = ItemStock::where('item_id', $item['item_id'])->first();
                if (!$stock || $stock->stock_quantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock for item ID: ' . $item['item_id']);
                }
                $stock->decrement('stock_quantity', $item['quantity']);

                StockMovement::create([
                    'item_id' => $item['item_id'],
                    'order_id' => $order->id,
                    'movement_type' => 'sale',
                    'quantity' => -$item['quantity'],
                    'movement_date' => now(),
                ]);
            }

            $extraCharges = $cart->extraCharges ?? [];
            foreach ($extraCharges as $extraCharge) {
                OrderExtraCharge::create([
                    'order_id' => $order->id,
                    'extra_charge_id' => $extraCharge['extra_charge_id'],
                    'amount' => $extraCharge['amount'],
                ]);
            }

            $cart->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cart settled successfully!',
                'order_id' => $order->id
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to settle cart.', 'error' => $e->getMessage()], 500);
        }
    }

    public function voidOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id', 
                'reason_id' => 'required|exists:reasons,id',
                'details' => 'required|string',
            ]);

            DB::beginTransaction();

            $order = Order::findOrFail($validated['order_id']); // Fetch by order_id
            if ($order->status === 'voided') {
                throw new \Exception('Order is already voided.');
            }

            $order->update([
                'status' => 'voided',
                'is_active' => 0,
            ]);

            VoidReason::create([
                'order_id' => $order->id, // Use order_id
                'reason_id' => $validated['reason_id'],
                'details' => $validated['details'],
            ]);

            $orderItems = $order->orderItems;
            foreach ($orderItems as $orderItem) {
                $stock = ItemStock::where('item_id', $orderItem->item_id)->first();
                if ($stock) {
                    $stock->increment('stock_quantity', $orderItem->quantity);
                }

                StockMovement::create([
                    'item_id' => $orderItem->item_id,
                    'order_id' => $order->id,
                    'movement_type' => 'void',
                    'quantity' => $orderItem->quantity,
                    'movement_date' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order voided successfully']);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to void order.', 'error' => $e->getMessage()], 500);
        }
    }
}
