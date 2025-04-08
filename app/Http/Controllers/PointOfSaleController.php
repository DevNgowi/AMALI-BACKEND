<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\CustomerType;
use App\Models\ExtraCharge;
use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\ItemStock;
use App\Models\Order;
use App\Models\OrderExtraCharge;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\PrinterSetting;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class PointOfSaleController extends Controller
{

    public function generateOrderNumberLocal()
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6));
            $exists = Order::where('order_number', $orderNumber)->exists();
        } while ($exists);

        return response()->json([
            'data' => $orderNumber,
        ]);
    }

    public function getItemCategories(Request $request)
    {
        $groupId = $request->query('item_group_id');
        $categories = Category::where('item_group_id', $groupId)->get();
        return response()->json($categories);
    }
    public function printReceipt(Request $request)
    {
        try {
            // Log the receipt data received from the frontend
            \Log::info('Receipt data received:', $request->all());

            // Get the printer settings from the database
            $printerSetting = PrinterSetting::first(); // Adjust this query as needed to get the correct printer

            if (!$printerSetting) {
                \Log::error('No printer settings found in the database.');
                return response()->json(['success' => false, 'message' => 'No printer settings found.']);
            }

            // Get the printer IP Address and Port
            $printerIp = $printerSetting->printer_ip; // Assuming the column name is printer_ip
            $port = 9100;
            $data = $request->all();

            // Log the attempt to connect to the printer
            \Log::info('Attempting to connect to printer at ' . $printerIp . ':' . $port);

            // Connect to the printer
            $connector = new NetworkPrintConnector($printerIp, $port);
            $printer = new Printer($connector);

            // Log successful connection
            \Log::info('Successfully connected to printer.');

            // Print receipt header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text($data['company_name'] . "\n");
            $printer->setTextSize(1, 1);
            $printer->text("Phone: " . $data['phone'] . "\n");
            $printer->text("Email: " . $data['email'] . "\n");
            $printer->text("TIN: " . $data['tin_no'] . "\n");
            $printer->text("--------------------------------\n");

            // Print invoice details
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Invoice No: " . $data['invoiceNumber'] . "\n");
            $printer->text("Date: " . date("Y-m-d H:i:s") . "\n");
            $printer->text("Payment Method: " . $data['payment_method'] . "\n");
            $printer->text("--------------------------------\n");

            // Print items
            foreach ($data['items'] as $item) {
                $printer->text($item['item'] . "\n");
                $printer->text(" " . $item['qty'] . " x " . $item['price'] . " = " . $item['total'] . "\n");
            }

            $printer->text("--------------------------------\n");

            // Print totals
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("Sub Total: " . $data['totalAmount'] . "\n");
            $printer->text("Tip: " . $data['tip'] . "\n");
            $printer->text("Discount: " . $data['discount'] . "\n");
            $printer->text("Grand Total: " . $data['groundTotal'] . "\n");
            $printer->text("Amount Paid: " . $data['groundTotal'] . "\n");
            $printer->text("Balance: 0.00\n");
            $printer->text("--------------------------------\n");

            // Print footer
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Software By: Japango Tech Solution\n");
            $printer->text("www.japango.co.tz | +255652387671\n");

            // Cut the receipt and close the printer
            $printer->cut();
            $printer->close();

            // Log successful printing
            \Log::info('Receipt printed successfully.');

            return response()->json(['success' => true, 'message' => 'Receipt printed successfully']);
        } catch (\Exception $e) {
            // Log the error message for debugging
            \Log::error('Printing failed: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Printing failed: ' . $e->getMessage()]);
        }
    }

    public function getPaymentOptions($paymentTypeId)
    {
        $payments = Payment::where('payment_type_id', $paymentTypeId)->get();

        return response()->json($payments);
    }

    public function salePOS(Request $request)
    {
        $customer_details = Customer::all();
        $customer_types = CustomerType::all();
        $item_groups = ItemGroup::all();
        $itemGroup = $request->input('item_group_id');

        $payments = Payment::all();

        $sales_items = Item::query()
            ->join('item_groups', 'items.item_group_id', '=', 'item_groups.id')
            ->join('item_units', 'items.id', '=', 'item_units.item_id')
            ->join('units', 'item_units.selling_unit_id', '=', 'units.id')
            ->join('item_prices', 'item_prices.item_id', '=', 'items.id')
            ->join('item_stocks', 'item_stocks.item_id', '=', 'items.id')
            ->join('stocks', 'item_stocks.stock_id', '=', 'stocks.id')
            ->leftJoin('item_images', 'item_images.item_id', '=', 'items.id')
            ->leftJoin('images', 'item_images.image_id', '=', 'images.id')
            ->select([
                'items.id',
                'items.name as item_name',
                'item_groups.name as group_name',
                'units.name as item_unit',
                'item_prices.amount as item_price',
                'item_stocks.stock_quantity as stock_quantity',
                DB::raw("CONCAT('/uploads/item_images/', COALESCE(images.file_path, 'default.jpg')) as image_url")
            ])
            ->when($itemGroup, function ($query) use ($itemGroup) {
                $query->where('items.item_group_id', $itemGroup);
            })
            ->distinct()
            ->get();

        if ($request->ajax()) {
            return response()->json($sales_items);
        }
        // generate invoice number 
        $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6));
        return view('Sales.index', compact('item_groups', 'sales_items', 'orderNumber', 'payments', 'customer_types', 'customer_details'));
    }

    public function getItemsByCategory(Request $request)
    {
        $itemCategoryId = $request->query('item_category_id');

        if (!$itemCategoryId) {
            return response()->json(['error' => 'Missing item_category_id'], 400);
        }

        $sales_items = Item::query()
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->join('item_units', 'items.id', '=', 'item_units.item_id')
            ->join('units', 'item_units.selling_unit_id', '=', 'units.id')
            ->join('item_prices', 'item_prices.item_id', '=', 'items.id')
            ->join('item_stocks', 'item_stocks.item_id', '=', 'items.id')
            ->join('stocks', 'item_stocks.stock_id', '=', 'stocks.id')
            ->leftJoin('item_images', 'item_images.item_id', '=', 'items.id')
            ->leftJoin('images', 'item_images.image_id', '=', 'images.id')
            ->select([
                'items.id',
                'items.name as item_name',
                'units.name as item_unit',
                'item_prices.amount as item_price',
                'item_stocks.stock_quantity as stock_quantity',
                DB::raw("CONCAT('/uploads/item_images/', COALESCE(images.file_path, 'default.jpg')) as image_url")
            ])
            ->where('items.category_id', $itemCategoryId)
            ->distinct()
            ->get();


        return response()->json($sales_items);
    }

    public function getItemsByCategoryLocal(Request $request)
    {
        $itemCategoryId = $request->query('item_category_id');
        if (!$itemCategoryId) {
            return response()->json(['error' => 'Missing item_category_id'], 400);
        }

        $sales_items = Item::query()
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->join('item_units', 'items.id', '=', 'item_units.item_id')
            ->join('units as selling_unit', 'item_units.selling_unit_id', '=', 'selling_unit.id')
            ->join('units as buying_unit', 'item_units.selling_unit_id', '=', 'buying_unit.id')
            ->join('item_prices', 'item_prices.item_id', '=', 'items.id')
            ->join('item_costs', 'item_costs.item_id', '=', 'items.id')
            ->join('item_stocks', 'item_stocks.item_id', '=', 'items.id')
            ->join('stocks', 'item_stocks.stock_id', '=', 'stocks.id')
            ->leftJoin('item_barcodes', 'items.id', '=', 'item_barcodes.item_id')
            ->leftJoin('barcodes', 'item_barcodes.barcode_id', '=', 'barcodes.id')
            ->leftJoin('item_images', 'item_images.item_id', '=', 'items.id')
            ->leftJoin('images', 'item_images.image_id', '=', 'images.id')
            ->leftJoin('item_stores', 'item_stores.item_id', '=', 'items.id')
            ->leftJoin('stores', 'item_stores.store_id', '=', 'stores.id')
            ->select([
                'items.id',
                'items.name as item_name',
                'items.exprire_date as expire_date',
                'selling_unit.name as selling_unit',
                'buying_unit.name as buying_unit',
                'selling_unit.id as selling_unit_id',
                'buying_unit.id as buying_unit_id',
                'item_prices.amount as item_price',
                'item_costs.amount as item_cost',
                'item_stocks.stock_quantity as stock_quantity',
                'barcodes.code as barcode',
                'stocks.min_quantity as min_quantity',
                'stocks.max_quantity as max_quantity',
                'stores.id as store_id',
                'stores.name as store_name',
                DB::raw("CONCAT('/uploads/item_images/', COALESCE(images.file_path, 'default.jpg')) as image_url")
            ])
            ->where('items.category_id', $itemCategoryId)
            ->distinct()
            ->get();

        return response()->json($sales_items);
    }

    public function getItemsUnitsList(Request $request)
    {
        $units = DB::table('units')
            ->select('id', 'name')
            ->distinct()
            ->get();

        return response()->json([
            'data' => $units,
            'success' => true,
        ]);
    }

    public function getItemsBarcodeList(Request $request)
    {
       
        $items = Item::query()
            ->leftJoin('item_barcodes', 'items.id', '=', 'item_barcodes.item_id')
            ->leftJoin('barcodes', 'item_barcodes.barcode_id', '=', 'barcodes.id')
            ->select([
                'items.id',
                'items.name as item_name',
                'barcodes.code as barcode', 
            ])
            ->get();

        return response()->json($items);
    }

    public function getItemGroups(Request $request)
    {
        $search = $request->input('search');
        Log::info("Search query: $search");

        $itemGroups = ItemGroup::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->get(['id', 'name']);

        Log::info('Item groups returned:', $itemGroups->toArray());

        return response()->json($itemGroups);
    }

    public function extraCharge()
    {
        $charge_type = 'Sales'; // Filter based on the charge type name
        $extra_charges = ExtraCharge::query()
            ->join('extra_charge_types', 'extra_charges.charge_type_id', '=', 'extra_charge_types.id')
            ->select([
                'extra_charges.id as extra_charge_id',
                'extra_charges.amount as amount',
                'extra_charges.name as name',
                'extra_charge_types.name as charge_type_name', // Include the charge type name for clarity
                'extra_charges.charge_type_id'
            ])
            ->where('extra_charge_types.name', $charge_type) // Filter by charge type name
            ->get();

        return response()->json([
            'data' => $extra_charges,
            'message' => 'success'
        ], 200);
    }

    public function receiptCompanyDetails()
    {
        $company_details = Company::query()->select('company_name', 'phone', 'email', 'tin_no')->first();

        if (!$company_details) {
            return response()->json([
                'error' => 'Company details not found.'
            ], 404);
        }

        return response()->json([
            'data' => $company_details,
        ], 200);
    }
    public function storePointOfSales(Request $request)
    {
        \Log::info('Incoming request data', $request->all());

        try {
            $validated = $request->validate([
                'customer_type_id' => 'nullable|exists:customer_types,id',
                'customer_id' => 'nullable|exists:customers,id',
                'payment_id' => 'required|exists:payments,id',
                'total_amount' => 'required|numeric',
                'tip' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'items' => 'required|array',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'extra_charges' => 'nullable|array',
                'extra_charges.*.extra_charge_id' => 'required|exists:extra_charges,id',
                'extra_charges.*.amount' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6));
            $receiptNumber = 'REC-' . strtoupper(substr(uniqid(), -5));

            // Calculate grand total
            $discount = $validated['discount'] ?? 0;
            $tip = $validated['tip'] ?? 0;
            $grandTotal = $validated['total_amount'] + $tip - $discount;

            // Create the order
            $order = Order::create([
                'order_number' => $orderNumber,
                'receipt_number' => $receiptNumber,
                'date' => now(),
                'customer_type_id' => $validated['customer_type_id'] ?? null,
                'total_amount' => $validated['total_amount'],
                'tip' => $tip,
                'discount' => $discount,
                'grand_total' => $grandTotal,
            ]);

            $order_payments = OrderPayment::create([
                'payment_id' => $validated['payment_id'],
                'order_id' => $order->id
            ]);

            if ($validated['customer_id'] !== null) {
                $customer_order = CustomerOrder::create([
                    'customer_id' => $validated['customer_id'],
                    'order_id' => $order->id
                ]);
            }

            foreach ($validated['items'] as $item) {
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);

                // Deduct from stock
                $stock = ItemStock::where('item_id', $item['item_id'])->first();
                if (!$stock) {
                    throw new \Exception('Stock not found for item ID: ' . $item['item_id']);
                }
                if ($stock->stock_quantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock for item ID: ' . $item['item_id']);
                }
                $stock->decrement('stock_quantity', $item['quantity']);

                // Record stock movement
                StockMovement::create([
                    'item_id' => $item['item_id'],
                    'order_id' => $order->id,
                    'movement_type' => 'sale',
                    'quantity' => -$item['quantity'],
                    'movement_date' => now(),
                ]);
            }

            // Add extra charges to the order
            if (isset($validated['extra_charges'])) {
                foreach ($validated['extra_charges'] as $extraCharge) {
                    OrderExtraCharge::create([
                        'order_id' => $order->id,
                        'extra_charge_id' => $extraCharge['extra_charge_id'],
                        'amount' => $extraCharge['amount'],
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order created successfully!', 'order_id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create order.', 'error' => $e->getMessage()], 500);
        }
    }

    public function storePointOfSalesLocal(Request $request)
    {
        \Log::info('Incoming request data', $request->all());

        try {
            $validated = $request->validate([
                'customer_type_id' => 'nullable|exists:customer_types,id',
                'customer_id' => 'nullable|exists:customers,id',
                'payment_id' => 'required|exists:payments,id',
                'total_amount' => 'required|numeric',
                'tip' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'extra_charges' => 'nullable|array',
                'extra_charges.*.extra_charge_id' => 'required_with:extra_charges|exists:extra_charges,id',
                'extra_charges.*.amount' => 'required_with:extra_charges|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6));
            $receiptNumber = 'REC-' . strtoupper(substr(uniqid(), -5));

            // Calculate grand total
            $discount = $validated['discount'] ?? 0;
            $tip = $validated['tip'] ?? 0;
            $grandTotal = $validated['total_amount'] + $tip - $discount;

            // Create the order
            $order = Order::updateOrCreate([
                'order_number' => $orderNumber,
                'receipt_number' => $receiptNumber,
                'date' => now(),
                'customer_type_id' => $validated['customer_type_id'] ?? null,
                'total_amount' => $validated['total_amount'],
                'tip' => $tip,
                'discount' => $discount,
                'grand_total' => $grandTotal,
            ]);

            $order_payments = OrderPayment::updateOrCreate([
                'payment_id' => $validated['payment_id'],
                'order_id' => $order->id,
            ]);

            if ($validated['customer_id'] !== null) {
                $customer_order = CustomerOrder::updateOrCreate([
                    'customer_id' => $validated['customer_id'],
                    'order_id' => $order->id,
                ]);
            }

            foreach ($validated['items'] as $item) {
                // Create order item
                OrderItem::updateOrCreate([
                    'order_id' => $order->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);

                // Deduct from stock
                $stock = ItemStock::where('item_id', $item['item_id'])->first();
                if (!$stock) {
                    throw new \Exception('Stock not found for item ID: ' . $item['item_id']);
                }
                if ($stock->stock_quantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock for item ID: ' . $item['item_id']);
                }
                $stock->decrement('stock_quantity', $item['quantity']);

                // Record stock movement
                StockMovement::updateOrCreate([
                    'item_id' => $item['item_id'],
                    'order_id' => $order->id,
                    'movement_type' => 'sale',
                    'quantity' => -$item['quantity'],
                    'movement_date' => now(),
                ]);
            }

            // Handle extra charges if present
            if (isset($validated['extra_charges'])) {
                foreach ($validated['extra_charges'] as $charge) {
                    OrderExtraCharge::updateOrCreate([
                        'order_id' => $order->id,
                        'extra_charge_id' => $charge['extra_charge_id'],
                        'amount' => $charge['amount'],
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully!',
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'receipt_number' => $receiptNumber,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create order', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to create order.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStock(Request $request)
{
    \Log::info('Incoming stock update request data', $request->all());

    try {
        $validated = $request->validate([
            'stock_updates' => 'required|array|min:1',
            'stock_updates.*.item_id' => 'required|exists:items,id',
            'stock_updates.*.quantity' => 'required|integer',  // Negative to deduct, positive to add
        ]);

        DB::beginTransaction();

        foreach ($validated['stock_updates'] as $update) {
            $stock = ItemStock::where('item_id', $update['item_id'])->first();
            if (!$stock) {
                throw new \Exception('Stock not found for item ID: ' . $update['item_id']);
            }
            $new_quantity = $stock->stock_quantity + $update['quantity'];  // quantity can be negative
            if ($new_quantity < 0) {
                throw new \Exception('Insufficient stock for item ID: ' . $update['item_id']);
            }
            $stock->update(['stock_quantity' => $new_quantity]);

            // Optionally record stock movement
            StockMovement::create([
                'item_id' => $update['item_id'],
                'movement_type' => $update['quantity'] < 0 ? 'sale' : 'adjustment',
                'quantity' => $update['quantity'],
                'movement_date' => now(),
            ]);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully!',
        ], 200);
    } catch (ValidationException $e) {
        DB::rollBack();
        \Log::error('Validation failed', ['errors' => $e->errors()]);
        return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Failed to update stock', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed to update stock', 'error' => $e->getMessage()], 500);
    }
}

}
