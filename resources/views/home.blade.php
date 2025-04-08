@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Dashboard') }}</h1>
                </div><div class="col-sm-6">
                    <div class="float-sm-right">
                        <select id="filter" class="form-control" style="width: auto; display: inline-block;">
                            <option value="today">{{ __('Today') }}</option>
                            <option value="week">{{ __('This Week') }}</option>
                            <option value="month">{{ __('This Month') }}</option>
                        </select>
                    </div>
                </div>
            </div></div></div>
    @can('can view dashboard')
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-3">
                        <div class="card counter_color"
                            style="color: #fff; background-color: #003163; border-color: #003163; box-shadow: none; font-weight: bold;">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Sales') }}</h5><br>
                                <hr>
                                <p class="card-text" id="sales-counter">Tsh 0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card counter_color"
                            style="color: #fff; background-color: #003163; border-color: #003163; box-shadow: none; font-weight: bold;">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Purchases') }}</h5><br>
                                <hr>
                                <p class="card-text" id="purchases-counter">Tsh 0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card counter_color"
                            style="color: #fff; background-color: #003163; border-color: #003163; box-shadow: none; font-weight: bold;">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Expenses') }}</h5><br>
                                <hr>
                                <p class="card-text" id="expenses-counter">Tsh 0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card counter_color"
                            style="color: #fff; background-color: #003163; border-color: #003163; box-shadow: none; font-weight: bold;">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Profit') }}</h5><br>
                                <hr>
                                <p class="card-text" id="profit-counter">Tsh 0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card counter_color"
                            style="color: #fff; background-color: #003163; border-color: #003163; box-shadow: none; font-weight: bold;">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Stock Value') }}</h5><br>
                                <hr>
                                <p class="card-text" id="stock-value-counter">Tsh 0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card counter_color"
                            style="color: #fff; background-color: #003163; border-color: #003163; box-shadow: none; font-weight: bold;">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Cash on Hand') }}</h5><br>
                                <hr>
                                <p class="card-text" id="cash-on-hand-counter">Tsh 0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card counter_color"
                            style="color: #fff; background-color: #003163; border-color: #003163; box-shadow: none; font-weight: bold;">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Employees') }}</h5><br>
                                <hr>
                                <p class="card-text" id="employees-counter">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card counter_color"
                            style="color: #fff; background-color: #003163; border-color: #003163; box-shadow: none; font-weight: bold;">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Loss') }}</h5><br>
                                <hr>
                                <p class="card-text" id="loss-counter">Tsh 0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Sales and Purchases Overview') }}</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="salesAndPurchasesChart" style="height: 400px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Top Selling Items') }}</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item Name') }}</th>
                                            <th>{{ __('Quantity Sold') }}</th>
                                            <th>{{ __('Total Sales') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top-selling-items">
                                        <tr>
                                            <td colspan="3" class="text-center">{{ __('No data available') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div></div>
        @else
        <div class="content">
            <div class="container-fluid">
                <div class="no_permission">
                    <h4>{{ __("You Don`t have permission to access this Dashboard") }}</h4>
                </div>
            </div>
        </div>
    @endcan


@section('scripts')
    <script>
        $(document).ready(function() {
            // Function to fetch top selling items
            function fetchTopSellingItems(filter) {
                $.ajax({
                    url: '/dashboard/top_selling_item',
                    method: 'GET',
                    data: {
                        filter: filter
                    },
                    success: function(response) {
                        const topSellingItemsTable = $('#top-selling-items');
                        topSellingItemsTable.empty();
                        if (response.length > 0) {
                            response.forEach(item => {
                                const row = `<tr>
                                                <td>${item.item_name}</td>
                                                <td>${item.item_quantity}</td>
                                                <td>Tsh ${parseFloat(item.total_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                                            </tr>`;
                                topSellingItemsTable.append(row);
                            });
                        } else {
                            topSellingItemsTable.append(
                                '<tr><td colspan="3" class="text-center">{{ __('No data available') }}</td></tr>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching top selling items:', error);
                    }
                });
            }

            const initialFilter = 'today';
            fetchTopSellingItems(initialFilter);


            $('#filter').on('change', function() {
                const filterValue = $(this).val();
                fetchTopSellingItems(filterValue);
            });
        });
    </script>
@endsection

@endsection