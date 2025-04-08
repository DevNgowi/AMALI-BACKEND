@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Update Item') }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('list_item') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="add_btn justify-content-end pb-2 d-flex">

                    </div>
                    <div class="card">
                        <div class="card-body p-5">
                            <form id="editItemForm" method="POST" action="{{ route('update_item', $item->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label" for="name">Item Name</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="name" value="{{ old('name', $item->name) }}"
                                            id="name" class="form-control" required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="category_id">Item Category</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="category_id" class="form-control" id="category_id" required>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="item_type_id">Item Type</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="item_type_id" class="form-control" id="item_type_id" required>
                                            <option value="">Select Item Type</option>
                                            @foreach ($item_types as $item_type)
                                                <option value="{{ $item_type->id }}"
                                                    {{ old('item_type_id', $item->item_type_id) == $item_type->id ? 'selected' : '' }}>
                                                    {{ $item_type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('item_type_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- <div class="col-md-3">
                                        <label class="form-label" for="item_group_id">Item Group</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="item_group_id" class="form-control" id="item_group_id" required>
                                            <option value="">Select Item Group</option>
                                            @foreach ($item_groups as $group)
                                                <option value="{{ $group->id }}"
                                                    {{ old('item_group_id', $item->item_group_id) == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('item_group_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div> --}}

                                    <div class="col-md-3">
                                        <label class="form-label" for="barcode">Barcode</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <input type="text" name="barcode"  value="{{ old('barcode', optional($item->barcodes->first())->code) }}" id="barcode" class="form-control">
                                            <button class="btn btn-secondary" id="generateBarcodeBtn" type="button">Generate</button>
                                        </div>
                                        @error('barcode')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="buying_price_id">Buying Unit</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="buying_unit_id" class="form-control" id="buying_unit_id" required>
                                            <option value="">Select Unit</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}"
                                                    {{ old('buying_unit_id', optional($item->itemCosts->first())->unit_id) == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('buying_price_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="selling_price_id">Selling Unit</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="selling_unit_id" class="form-control" id="selling_unit_id" required>
                                            <option value="">Select Unit</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}"
                                                    {{ old('selling_unit_id', optional($item->itemPrices->first())->unit_id) == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selling_price_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="exprire_date">Expire Date</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="date" class="form-control" name="exprire_date" id="exprire_date"
                                            value="{{ old('exprire_date', $item->exprire_date) }}" >
                                        @error('exprire_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="item_image">Item Image</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="file" class="form-control" name="item_image" id="item_image">
                                        @if ($item->image)
                                            <div class="mt-2">
                                                <small>Current Image: {{ $item->image }}</small>
                                            </div>
                                        @endif
                                        @error('item_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Store Section -->
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="store-tab" data-bs-toggle="tab"
                                                    data-bs-target="#store" type="button" role="tab"
                                                    aria-controls="store" aria-selected="true">Store</button>
                                            </li>
                                        </ul>

                                        <div class="tab-content mt-3">
                                            <div class="tab-pane fade show active" id="store" role="tabpanel" aria-labelledby="store-tab">
                                                <div class="table-responsive">
                                                    <table class="table" id="editTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Store Name</th>
                                                                <th>Min Qty</th>
                                                                <th>Max Qty</th>
                                                                <th>Stock</th>
                                                                <th>Purchase Rate/Unit</th>
                                                                <th>Selling Price/Unit</th>
                                                                <th>Tax</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($item->stocks as $stock)
                                                                @php
                                                                    $store = $stores->find($stock->store_id);
                                                                    $itemCost = $item->itemCosts->firstWhere('store_id', $stock->store_id);
                                                                    $itemPrice = $item->itemPrices->firstWhere('store_id', $stock->store_id);
                                                                    $itemTax = $item->taxes->firstWhere('store_id', $stock->store_id);
                                                                    $itemStock = $item->itemStocks->firstWhere('stock_id', $stock->id);
                                                                @endphp
                                                                <tr data-store-id="{{ $store->id }}">
                                                                    <td>
                                                                        <select name="store_id[]" class="form-control">
                                                                            @foreach ($stores as $availableStore)
                                                                                <option value="{{ $availableStore->id }}"
                                                                                    {{ $availableStore->id == $store->id ? 'selected' : '' }}>
                                                                                    {{ $availableStore->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <input type="hidden" name="stock_id[]" value="{{ $stock->id }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="min_quantity[]"
                                                                            value="{{ old('min_quantity.' . $loop->index, $stock ? $stock->min_quantity : '') }}"
                                                                            class="form-control" min="0">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="max_quantity[]"
                                                                            value="{{ old('max_quantity.' . $loop->index, $stock ? $stock->max_quantity : '') }}"
                                                                            class="form-control" min="0">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="stock_quantity[]"
                                                                            value="{{ old('stock_quantity.' . $loop->index, $itemStock ? $itemStock->stock_quantity : '') }}"
                                                                            class="form-control" min="0">
                                                                    </td>
                                                                    <td>
                                                                        <input id="buyingUnitSelect" type="number" name="purchase_rate[]"
                                                                            value="{{ old('purchase_rate.' . $loop->index, $itemCost ? $itemCost->amount : '') }}"
                                                                            class="form-control" step="0.01" min="0">
                                                                    </td>
                                                                    <td>
                                                                        <input id="sellingUnitSelect" type="number" name="selling_price[]"
                                                                            value="{{ old('selling_price.' . $loop->index, $itemPrice ? $itemPrice->amount : '') }}"
                                                                            class="form-control" step="0.01" min="0">
                                                                    </td>
                                                                    <td>
                                                                        <select name="tax_id[]" class="form-control">
                                                                            <option value="">Select Tax</option>
                                                                            @foreach ($taxes as $tax)
                                                                                <option value="{{ $tax->id }}"
                                                                                    @if (is_array(old('tax_id'))) {{ in_array($tax->id, old('tax_id')) ? 'selected' : '' }}
                                                                                    @elseif (isset($item->taxes) && $item->taxes->contains($tax->id))
                                                                                        selected @endif>
                                                                                    {{ $tax->name }}
                                                                                    ({{ $tax->tax_type }} -
                                                                                    @if ($tax->tax_mode == 'percentage')
                                                                                        {{ number_format($tax->tax_percentage) ?: 'N/A' }}%
                                                                                    @else
                                                                                        {{ number_format($tax->tax_amount ?: 0, 2) }}
                                                                                    @endif)
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger d-none btn-sm delete-row">
                                                                            <i class="bi bi-trash"></i> Delete
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <button type="button" id="addRowBtn" class="btn btn-primary mt-2">Add Row</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-button justify-content-end d-flex mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var taxes = @json($taxes);
        var oldTaxIds = @json(old('tax_id'));
        var itemTaxes = @json($item->taxes ? $item->taxes->toArray() : []);
        
    </script>
@endsection
