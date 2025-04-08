<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Item;
use App\Models\Order;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function saleCounter(Request $request)
    {
        $filter = $request->input('filter');

        $sales = Order::query()
            ->select([
                DB::raw('SUM(orders.total_amount) as total_sales'),

            ])
            ->when($filter === 'today', function ($query) {
                return $query->whereDate('orders.created_at', now()->toDateString());
            })
            ->when($filter === 'week', function ($query) {
                return $query->whereBetween('orders.created_at', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]);
            })
            ->when($filter === 'month', function ($query) {
                return $query->whereMonth('orders.created_at', now()->month);
            })
            ->first();

        return response()->json($sales);
    }

    public function purchaseCounter(Request $request)
    {
        $filter = $request->input('filter');
        $po_status = 'Completed';

        $sales = PurchaseOrder::query()
            ->select([
                DB::raw('COALESCE(SUM(purchase_orders.total_amount), 0) as total_purchases')
            ])
            ->when($filter === 'today', function ($query) {
                return $query->whereDate('purchase_orders.created_at', now()->toDateString());
            })
            ->when($filter === 'week', function ($query) {
                return $query->whereBetween('purchase_orders.created_at', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]);
            })
            ->when($filter === 'month', function ($query) {
                return $query->whereMonth('purchase_orders.created_at', now()->month);
            })
            ->where('status', $po_status)
            ->first();

        // Ensure a valid response structure
        return response()->json([
            'total_purchases' => $sales->total_purchases ?? 0  // Avoid null response
        ]);
    }

    public function expensesCounter(Request $request)
    {
        $filter = $request->input('filter');

        $expenses = Expense::query()
            ->select([
                DB::raw('SUM(expenses.amount) as total_expenses'),

            ])
            ->when($filter === 'today', function ($query) {
                return $query->whereDate('expenses.created_at', now()->toDateString());
            })
            ->when($filter === 'week', function ($query) {
                return $query->whereBetween('expenses.created_at', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]);
            })
            ->when($filter === 'month', function ($query) {
                return $query->whereMonth('expenses.created_at', now()->month);
            })
            ->first();

        return response()->json($expenses);
    }


    public function topSellingItem(Request $request)
    {
        $filter = $request->input('filter');

        $top_selling_items = Order::query()
            ->join('order_items', 'order_items.order_id', '=', 'orders.id') // Fixed typo
            ->join('items', 'order_items.item_id', '=', 'items.id')
            ->select([
                'items.name as item_name',
                DB::raw('SUM(order_items.quantity) as item_quantity'), // Aggregate quantity
                DB::raw('SUM(order_items.total_price) as total_amount')
            ])
            ->when($filter === 'today', function ($query) {
                return $query->whereDate('orders.created_at', now()->toDateString());
            })
            ->when($filter === 'week', function ($query) {
                return $query->whereBetween('orders.created_at', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]);
            })
            ->when($filter === 'month', function ($query) {
                return $query->whereMonth('orders.created_at', now()->month);
            })
            ->groupBy('items.id', 'items.name') // Group by item ID and name
            ->orderByDesc('item_quantity') 
            ->limit(10)
            ->get(); // Get all results instead of first()

        return response()->json($top_selling_items);
    }


    public function salesAndPurchasesChart(Request $request) {
        $filter = $request->input('filter');
    
        // Fetch sales data
        $salesData = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total_sales')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->when($filter === 'today', function ($query) {
                return $query->whereDate('created_at', now()->toDateString());
            })
            ->when($filter === 'week', function ($query) {
                return $query->whereBetween('created_at', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]);
            })
            ->when($filter === 'month', function ($query) {
                return $query->whereMonth('created_at', now()->month);
            })
            ->get();
    
        // Fetch purchase data
        $purchaseData = PurchaseOrder::selectRaw('DATE(created_at) as date, COALESCE(SUM(total_amount), 0) as total_purchases')
            ->where('status', 'Completed')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->when($filter === 'today', function ($query) {
                return $query->whereDate('created_at', now()->toDateString());
            })
            ->when($filter === 'week', function ($query) {
                return $query->whereBetween('created_at', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]);
            })
            ->when($filter === 'month', function ($query) {
                return $query->whereMonth('created_at', now()->month);
            })
            ->get();
    
        // Combine the data into a single response
        $combinedData = [];
    
        // Combine sales data
        foreach ($salesData as $sale) {
            $combinedData[$sale->date]['total_sales'] = $sale->total_sales;
            $combinedData[$sale->date]['total_purchases'] = 0; // Initialize purchases to 0
        }
    
        // Combine purchase data
        foreach ($purchaseData as $purchase) {
            if (!isset($combinedData[$purchase->date])) {
                $combinedData[$purchase->date]['total_sales'] = 0; // Initialize sales to 0 if not set
            }
            $combinedData[$purchase->date]['total_purchases'] = $purchase->total_purchases;
        }
    
        // Prepare the final response
        return response()->json(array_values($combinedData));
    }

   

    public function profitCounter(Request $request)
    {
        $filter = $request->input('filter');

        $profit = Order::query()
            ->join('order_items', 'order_items.order_id', '=', 'order_items.id')
            ->join('items', 'order_items.item_id', '=', 'items.id')
              ->join('item_costs', 'order_items.item_id', '=', 'item_costs.id')
              ->join('item_prices', 'order_items.item_id', '=', 'item_prices.id')
            ->select([
                'orders.id as order_id',
                'items.id as item_id',
                'items.name as item_name',
                'item_costs.amount as item_cost_amount',
                'item_prices.amount as item_price_amount',
                DB::raw('SUM(orders.total_amount) as total_profit'),

            ])
            ->groupBy([
                'orders.id',
                'items.id',
                'items.name',
                'item_costs.amount',
                'item_prices.amount',
            ])
            // ->when($filter === 'today', function ($query) {
            //     return $query->whereDate('orders.created_at', now()->toDateString());
            // })
            // ->when($filter === 'week', function ($query) {
            //     return $query->whereBetween('orders.created_at', [
            //         now()->startOfWeek()->toDateString(),
            //         now()->endOfWeek()->toDateString()
            //     ]);
            // })
            // ->when($filter === 'month', function ($query) {
            //     return $query->whereMonth('orders.created_at', now()->month);
            // })
            ->get();

        return response()->json($profit);
    }


    public function stockValueCounter(Request $request)
    {
        $day = $request->input('filter_by_day');
        $week = $request->input('filter_by_week');
        $month = $request->input('filter_by_month');
    }


    public function cashOnHandCounter(Request $request)
    {
        $day = $request->input('filter_by_day');
        $week = $request->input('filter_by_week');
        $month = $request->input('filter_by_month');
    }


    public function employeeCounter(Request $request)
    {
        $day = $request->input('filter_by_day');
        $week = $request->input('filter_by_week');
        $month = $request->input('filter_by_month');
    }

    public function lossCounter(Request $request)
    {
        $day = $request->input('filter_by_day');
        $week = $request->input('filter_by_week');
        $month = $request->input('filter_by_month');
    }





}
