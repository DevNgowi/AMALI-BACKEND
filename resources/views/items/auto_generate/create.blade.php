@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Item') }}</h1>
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
                            <form action="{{ route('store_item') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <!-- Item Name -->
                                    <div class="col-md-3">
                                        <label class="text-muted" for="name">Item Name <span
                                                class="text-danger">*</span> </label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="name" value="{{ old('name') }}" id="name"
                                            class="form-control" required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Item Category -->
                                    <div class="col-md-3">
                                        <label class="text-muted" for="category_id">Item Category <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <select name="category_id" class="form-control" id="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @can('can create item category')
                                                <button class="btn btn-secondary" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#addCategoryModal">+</button>
                                            @endcan
                                        </div>
                                        @error('category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Barcode -->
                                    <div class="col-md-3">
                                        <label class="text-muted" for="barcode">Barcode </label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <input type="text" name="barcode" value="{{ old('barcode') }}" id="barcode"
                                                class="form-control" aria-describedby="generateBarcodeBtn">
                                            <button class="btn btn-secondary" id="generateBarcodeBtn"
                                                type="button">Generate</button>
                                            @error('barcode')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Item Type -->
                                    <div class="col-md-3">
                                        <label class="text-muted" for="item_type_id">Item Type <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="item_type_id" class="form-control" id="item_type_id">
                                            <option value="">Select Item Type</option>
                                            @foreach ($item_types as $item_type)
                                                <option value="{{ $item_type->id }}"
                                                    {{ old('item_type_id') == $item_type->id ? 'selected' : '' }}>
                                                    {{ $item_type->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('item_type_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Item Group -->

                                    {{-- <div class="col-md-3">
                                        <label class="text-muted" for="item_group_id">Item Group <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="d-flex">
                                            <select name="item_group_id" class="form-control" id="item_group_id">
                                                <option value="None">None</option>
                                                @foreach ($item_groups as $item_group)
                                                    <option value="{{ $item_group->id }}"
                                                        {{ old('item_group_id') == $item_group->id ? 'selected' : '' }}>
                                                        {{ $item_group->name }}
                                                    </option>
                                                @endforeach

                                            </select>

                                            @can('can create item group')
                                                <button class="btn btn-secondary ms-2" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#addItemGroupModal">+</button>
                                            @endcan
                                        </div>
                                        @error('item_group_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div> --}}

                                    <!-- Brand Name -->
                                    <div class="col-md-3">
                                        <label class="text-muted" for="brand_id">Brand Name</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <select name="brand_id" class="form-control" id="brand_id">
                                                <option value="None">None</option>
                                                @foreach ($item_brands as $brand)
                                                    <option value="{{ $brand->id }}"
                                                        {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                            @can('can create item brand')
                                                <button class="btn btn-secondary" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#addBrandModal">+</button>
                                            @endcan
                                            @error('brand_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Buying Unit -->
                                    <div class="col-md-3">
                                        <label class="text-muted" for="buying_unit_id">Buying Unit <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <select name="buying_unit_id" class="form-control" id="buying_unit_id"
                                                required>
                                                <option value="">Select Unit</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @can('can create unit')
                                                <button class="btn btn-secondary" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#addUnitModal">+</button>
                                            @endcan
                                        </div>
                                        @error('buying_unit_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Selling Unit -->
                                    <div class="col-md-3">
                                        <label class="text-muted" for="selling_unit_id">Selling Unit <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <select name="selling_unit_id" class="form-control" id="selling_unit_id"
                                                required>
                                                <option value="">Select Unit</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @can('can create unit')
                                                <button class="btn btn-secondary" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#addUnitModal">+</button>
                                            @endcan
                                        </div>
                                        @error('selling_unit_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <!-- Item Code -->
                                    <div class="col-md-3">
                                        <label class="text-muted" for="code">Expire Date</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="date" class="form-control" name="exprire_date" id="exprire_date"
                                            value="{{ old('exprire_date') }}">
                                        @error('exprire_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Item Image -->
                                    <div class="col-md-3 mb-3">
                                        <label class="text-muted" for="item_image">Item Image</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="file" class="form-control" name="item_image" id="item_image">
                                        @error('item_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Navigation Tabs -->
                                        <ul class="nav nav-tabs" id="navTabs" role="tablist">

                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="store-tab" data-bs-toggle="tab"
                                                    data-bs-target="#store" type="button" role="tab"
                                                    aria-controls="store" aria-selected="true">Store, Cost &
                                                    Stock</button>
                                            </li>
                                        </ul>
                                        <!-- Store Tab -->
                                        <div class="tab-pane fade show active" id="store" role="tabpanel"
                                            aria-labelledby="store-tab">
                                            <table class="table" id="storeTable">
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
                                                    <!-- Dynamic rows will be added here -->
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-default" id="addStoreRow">+ Add
                                                New</button>
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <div class="form-button justify-content-end d-flex">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            
        </div>
    </div>
    </div>


    {{-- modals --}}
    @include('layouts.item-modals.category')
    @include('layouts.item-modals.brand')
    @include('layouts.item-modals.units')
    @include('layouts.item-modals.item_group')

    <script>
        document.getElementById('unit_id').addEventListener('change', function() {
            document.getElementById('base_unit_id').value = this.value;
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("unit_id").value = ""; 
            });
        });
    </script>
@endsection
