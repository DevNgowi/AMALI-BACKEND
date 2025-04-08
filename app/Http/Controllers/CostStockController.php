<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCost;
use App\Models\ItemPrice;
use App\Models\ItemStock;
use App\Models\ItemTax;
use App\Models\ItemUnit;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostStockController extends Controller
{

    public function indexCostStock()
    {
        $stores = Store::all();
        $taxes = Tax::all();
        $units = Unit::all();
        return view('inventory.cost_and_stock.index', compact('stores', 'taxes', 'units'));
    }

    public function fetchCostStock(Request $request)
    {
        $storeId = $request->input('store_id');

        if (!$storeId) {
            return response()->json([]);
        }

        $cost_stocks = Item::query()
            ->select([
                'items.id as item_id',
                'items.name as item_name',
                'item_stores.store_id as store_id',
                'item_costs.amount as item_buying_cost',
                'item_prices.amount as item_selling_price',
                'item_costs.unit_id as item_buying_unit_id',
                'item_prices.unit_id as item_selling_unit_id',
                'buying_unit.name as item_buying_unit_name',
                'selling_unit.name as item_selling_unit_name',
                'item_stocks.stock_quantity as stock_quantity',
                'stocks.min_quantity as min_item_quantity',
                'stocks.max_quantity as max_item_quantity',
                'item_taxes.tax_id as item_tax',
            ])
            ->join('item_stores', function ($join) use ($storeId) {
                $join->on('items.id', '=', 'item_stores.item_id')
                    ->where('item_stores.store_id', $storeId);
            })
            ->join('stocks', 'items.id', '=', 'stocks.item_id')
            ->join('item_units', 'items.id', '=', 'item_units.item_id')
            ->join('units as selling_unit', 'item_units.selling_unit_id', '=', 'selling_unit.id')
            ->join('units as buying_unit', 'item_units.buying_unit_id', '=', 'buying_unit.id')
            ->join('item_stocks', 'items.id', '=', 'item_stocks.item_id')
            ->join('item_costs', 'items.id', '=', 'item_costs.item_id')
            ->join('item_prices', 'items.id', '=', 'item_prices.item_id')
            ->leftJoin('item_taxes', 'items.id', '=', 'item_taxes.item_id')
            ->get();

        return response()->json($cost_stocks);
    }

    public function updateCostStock(Request $request)
    {
        $items = $request->input('items');

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                // Update Stock
                Stock::where('item_id', $item['item_id'])
                    ->update([
                        'min_quantity' => $item['min_quantity'],
                        'max_quantity' => $item['max_quantity']
                    ]);

                // Update Item Stock
                ItemStock::where('item_id', $item['item_id'])
                    ->update([
                        'stock_quantity' => $item['stock_quantity']
                    ]);

                // Update Item Cost
                $item_cost = ItemCost::where('item_id', $item['item_id'])
                    ->update([
                        'amount' => $item['buying_cost'],
                        'unit_id' => $item['buying_unit_id']
                    ]);

                if ($item_cost) {
                    ItemUnit::where('item_id', $item['item_id'])
                        ->update([
                            'buying_unit_id' => $item['buying_unit_id']
                        ]);
                }



                // Update Item Price
                $item_price = ItemPrice::where('item_id', $item['item_id'])
                    ->update([
                        'amount' => $item['selling_price'],
                        'unit_id' => $item['selling_unit_id']
                    ]);
                if ($item_price) {
                    ItemUnit::where('item_id', $item['item_id'])
                        ->update([
                            'selling_unit_id' => $item['selling_unit_id']
                        ]);
                }

                // Update Item Tax
                if ($item['tax_id']) {
                    ItemTax::updateOrCreate(
                        ['item_id' => $item['item_id']],
                        ['tax_id' => $item['tax_id']]
                    );
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Items updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating items'], 500);
        }
    }
}
