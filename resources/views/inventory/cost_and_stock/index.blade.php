@extends('layouts.app')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Update Cost & Stock') }}</h1>
                </div>
            </div>
        </div>
    </div>

    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="filter-cost_stock">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <select name="store_id" class="form-control px-4" id="selected_store_id">
                            <option value="">---Select Store---</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 search-items" style="display: none;">
                        <input type="text" class="form-control" placeholder="Search Items">
                    </div>
                </div>
            </div>

            @can('can view cost & stock')
                <input type="hidden" id="fetch-cost-stock-url" value="{{ route('fetch_cost_stock') }}">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table" id="cost-stock-table dataTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" style="width: 20px; height: 20px;" class="text-lg"
                                            id="select-all"></th>
                                    <th>ITEM NAME</th>
                                    <th>STOCK</th>
                                    <th>MIN QUANTITY</th>
                                    <th>MAX QUANTITY</th>
                                    <th>PURCHASE RATE</th>
                                    <th>SELLING RATE</th>
                                    <th>TAX</th>
                                </tr>
                            </thead>
                            <tbody id="cost-stock-body">
                                </tbody>
                        </table>
                    </div>
                </div>
            @endcan

            @can('can update cost & stock')
                <input type="hidden" id="update-cost-stock-url" value="{{ route('update_cost_stock') }}">
                <div class="row">
                    <div class="col-lg-12">
                        <button class="btn btn-primary" type="submit" id="update-btn" disabled>{{ __('Update') }}</button>
                    </div>
                </div>
            @else
                @cannot('can view cost & stock')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-warning">
                                {{ __("You Don't have permission to view or update Cost & Stock.") }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-warning">
                                {{ __("You have permission to view Cost & Stock, but not to update.") }}
                            </div>
                        </div>
                    </div>
                @endcannot
            @endcan


        </div>
    </div>

    <script>
        const taxes = @json($taxes);
        const units = @json($units);
    </script>
@endsection