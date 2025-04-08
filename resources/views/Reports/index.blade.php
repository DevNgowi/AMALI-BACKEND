@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('General Reports') }}</h1>
            </div>
        </div>
    </div>
</div>

@include('message')

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="m-0"><i class="fas fa-boxes mr-2"></i>Inventory Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @can('can preview inventory stock level report')
                            <a href="{{ route('preview_stock_level_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-bar mr-2"></i>Stock Level Report
                            </a>
                            @endcan
                            @can('can preview inventory stock movement report')
                            <a href="{{ route('preview_stock_movement_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-exchange-alt mr-2"></i>Stock Movement Report
                            </a>
                            @endcan
                            @can('can preview inventory low stock report')
                            <a href="{{ route('preview_low_stock_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Alert Report
                            </a>
                            @endcan
                            @can('can preview inventory dead stock report')
                            <a href="{{ route('preview_dead_stock_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-ban mr-2"></i>Dead Stock Report
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="m-0"><i class="fas fa-shopping-cart mr-2"></i>Purchase Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @can('can preview purchase order history report')
                            <a href="{{ route('preview_po_history_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-history mr-2"></i>Purchase Order History
                            </a>
                            @endcan
                            @can('can preview pending purchase orders report')
                            <a href="{{ route('preview_pending_po_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-clock mr-2"></i>Pending Purchase Orders
                            </a>
                            @endcan
                            @can('can preview supplier performance report')
                            <a href="{{ route('preview_supplier_performance_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-line mr-2"></i>Supplier Performance
                            </a>
                            @endcan
                            @can('can preview supplier payments report')
                            <a href="{{ route('preview_supplier_payment_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-money-bill-wave mr-2"></i>Supplier Payments
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="m-0"><i class="fas fa-cash-register mr-2"></i>Sales Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @can('can preview sales summary report')
                            <a href="{{ route('preview_sales_summary_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-pie mr-2"></i>Sales Summary
                            </a>
                            @endcan
                            @can('can preview top selling products report')
                            <a href="{{ route('preview_top_products_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-star mr-2"></i>Top Selling Products
                            </a>
                            @endcan
                            @can('can preview customer analysis report')
                            <a href="{{ route('preview_customer_analysis_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-users mr-2"></i>Customer Analysis
                            </a>
                            @endcan
                            @can('can preview payment methods analysis report')
                            <a href="{{ route('preview_payment_methods_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-credit-card mr-2"></i>Payment Methods Analysis
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-warning">
                        <h5 class="m-0"><i class="fas fa-calculator mr-2"></i>Financial Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @can('can preview cost of goods sold report')
                            <a href="{{ route('preview_cogs_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-dollar-sign mr-2"></i>Cost of Goods Sold
                            </a>
                            @endcan
                            @can('can preview profit margins report')
                            <a href="{{ route('preview_profit_margins_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-percentage mr-2"></i>Profit Margins
                            </a>
                            @endcan
                            @can('can preview operating expenses report')
                            <a href="{{ route('preview_expenses_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>Operating Expenses
                            </a>
                            @endcan
                            @can('can preview payment aging report')
                            <a href="{{ route('preview_payment_aging_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-hourglass-half mr-2"></i>Payment Aging
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="m-0"><i class="fas fa-file-invoice mr-2"></i>Tax Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @can('can preview sales tax summary report')
                            <a href="{{ route('preview_sales_tax_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-receipt mr-2"></i>Sales Tax Summary
                            </a>
                            @endcan
                            @can('can preview tax collected report')
                            <a href="{{ route('preview_tax_collected_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-money-bill-alt mr-2"></i>Tax Collected
                            </a>
                            @endcan
                            @can('can preview tax payments due report')
                            <a href="{{ route('preview_tax_payments_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-calendar-alt mr-2"></i>Tax Payments Due
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="m-0"><i class="fas fa-user-shield mr-2"></i>Audit Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @can('can preview price change history report')
                            <a href="{{ route('preview_price_history_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-tags mr-2"></i>Price Change History
                            </a>
                            @endcan
                            @can('can preview user activity logs report')
                            <a href="{{ route('preview_user_activity_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-clock mr-2"></i>User Activity Logs
                            </a>
                            @endcan
                            @can('can preview system access logs report')
                            <a href="{{ route('preview_system_access_report') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-shield-alt mr-2"></i>System Access Logs
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection