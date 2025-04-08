<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Item;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class InventoryReportController extends Controller
{
    public function previewStockLevel()
    {
        $stores = Store::all();
        return view('Reports.Inventory_Reports.Stock_Level.preview', compact('stores'));
    }

    public function generateStockLevel(Request $request)
    {

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $store_id = $request->input('store_id');


        $stock_level_items = \DB::table('items')
            ->join('item_groups', 'items.item_group_id', '=', 'item_groups.id')
            ->join('item_units', 'item_units.item_id', '=', 'items.id')
            ->join('units', 'item_units.buying_unit_id', '=', 'units.id')
            ->join('item_barcodes', 'item_barcodes.item_id', '=', 'items.id')
            ->join('barcodes', 'item_barcodes.barcode_id', '=', 'barcodes.id')
            ->leftJoinSub(function ($query) {
                $query->from('item_costs as ic_latest')
                    ->select('ic_latest.item_id', 'ic_latest.amount')
                    ->whereIn('ic_latest.id', function ($subQuery) {
                        $subQuery->selectRaw('MAX(ic.id)')
                            ->from('item_costs as ic')
                            ->groupBy('ic.item_id');
                    });
            }, 'latest_cost', 'items.id', '=', 'latest_cost.item_id')
            ->leftJoin('brand_applicable_items', 'brand_applicable_items.item_id', '=', 'items.id')
            ->leftJoin('item_brands', 'brand_applicable_items.brand_id', '=', 'item_brands.id')
            ->join('item_stocks', 'items.id', '=', 'item_stocks.item_id')
            ->join('stocks', 'item_stocks.stock_id', '=', 'stocks.id')

            ->where('stocks.store_id', $store_id)

            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                Log::debug('Filtering itemStocks by date range: ' . $start_date . ' to ' . $end_date);
                return $query->whereBetween('item_stocks.created_at', [$start_date, $end_date]);
            })
            ->select(
                [
                    'items.name as item_name',
                    'item_groups.name as item_group_name',
                    'units.name as unit_name',
                    'barcodes.code as item_barcode',
                    'latest_cost.amount as amount',
                    'item_brands.name as brand_name',
                    \DB::raw('SUM(item_stocks.stock_quantity) as stock_level_quantity')
                ]
            )
            ->groupBy(
                'items.name',
                'barcodes.code',
                'item_groups.name',
                'item_brands.name',
                'units.name',
                'latest_cost.amount',
                'item_stocks.stock_quantity'
            )
            ->get();


        $startDateFormatted = date('M j, Y', strtotime($start_date));
        $endDateFormatted = date('M j, Y', strtotime($end_date));

        $companyInfo = Company::query()->first();
        $selectedDateRange = "$startDateFormatted TO $endDateFormatted";
        $letter_title = 'Stock Level Report';

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->AddPage('P', '', '', '', '', 15, 15, 16, 16, 10, 10);
        $mpdf->SetProtection(['print']);
        $mpdf->showWatermarkImage = false;
        $mpdf->SetDisplayMode('fullpage');
        $currentDate = date('Y-m-d');
        $pdfName = 'stock_level report' . $currentDate . '.pdf';

        $viewData = [
            'companyInfo' => $companyInfo,
            'data' => $stock_level_items,
            'selectedDateRange' => $selectedDateRange,
            'letter_title' => $letter_title,
        ];

        $html = view('Reports.Inventory_Reports.Stock_Level.generate', $viewData)->render();
        $mpdf->WriteHTML($html);

        $mpdf->Output($pdfName, \Mpdf\Output\Destination::INLINE);
    }

}
