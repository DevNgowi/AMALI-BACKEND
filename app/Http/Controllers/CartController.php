<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartExtraCharge;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function store(Request $request)
{
    try {
        Log::info('Cart Store Request Data:', $request->all());

        $data = $request->validate([
            'order_number' => 'required|string|unique:carts,order_number',
            'customer_type_id' => 'required|exists:customer_types,id',
            'customer_id' => 'nullable|exists:customers,id',
            'total_amount' => 'required|numeric',
            'items' => 'required|array',
            'items.*.item_id' => 'nullable|exists:items,id', // Make optional
            'items.*.name' => 'required|string',
            'items.*.unit' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.amount' => 'required|numeric',
            'extra_charges' => 'array',
            'extra_charges.*.name' => 'required|string',
            'extra_charges.*.amount' => 'required|numeric',
            'date' => 'required|date'
        ]);

        DB::beginTransaction();

        $cart = Cart::create([
            'order_number' => $data['order_number'],
            'customer_type_id' => $data['customer_type_id'],
            'customer_id' => $data['customer_id'],
            'total_amount' => $data['total_amount'],
            'date' => $data['date'],
            'status' => 'in-cart'
        ]);

        foreach ($data['items'] as $item) {
            Log::info('Creating Cart Item:', $item);
            $itemId = $item['item_id'] ?? Item::where('name', $item['name'])->first()->id ?? null;
            if (!$itemId) {
                throw new \Exception('Item ID not provided and not found for name: ' . $item['name']);
            }
            CartItem::create([
                'cart_id' => $cart->id,
                'item_id' => $itemId,
                'name' => $item['name'],
                'unit' => $item['unit'],
                'quantity' => $item['quantity'],
                'amount' => $item['amount']
            ]);
        }

        foreach ($data['extra_charges'] as $charge) {
            CartExtraCharge::create([
                'cart_id' => $cart->id,
                'name' => $charge['name'],
                'amount' => $charge['amount']
            ]);
        }

        DB::commit();
        Log::info('Cart Saved:', $cart->toArray());

        return response()->json(['success' => true, 'message' => 'Cart saved successfully']);
    } catch (\Exception $e) {
        Log::error('Cart Store Error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}
