@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Edit New Expense') }}</h1>
                </div>
                <div class="col-sm-6">
                   <a href="{{ route('list_expenses') }}" class="btn btn-secondary btn-sm">Back to list</a>
                </div>
            </div>
        </div>
    </div>

    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <form action="{{ route('update_expenses', $expense->id) }}" method="POST" enctype="multipart/form-data"> {{-- Corrected route to include expense ID for update --}}
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="list-unstyled">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="expense_category_id" class="form-label">{{ __('Expense Category') }}
                                            <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <select class="form-control @error('expense_category_id') is-invalid @enderror"
                                                id="expense_category_id" name="expense_category_id" required>
                                                <option value="">{{ __('Select Expense Category') }}</option> {{-- Added default option --}}
                                                @foreach ($expenseCategories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}> {{-- Corrected selected logic --}}
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                                 <i class="fas fa-plus"></i>
                                            </button>

                                              @error('expense_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="payment_type_id" class="form-label">{{ __('Payment Method') }}
                                            <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <select class="form-control @error('payment_type_id') is-invalid @enderror"
                                                id="payment_type_id" name="payment_type_id" required>
                                                <option value="">{{ __('Select Payment Method') }}</option>
                                                @foreach ($paymentTypes as $method) {{-- Corrected variable name to $paymentTypes --}}
                                                    <option value="{{ $method->id }}"
                                                        {{ old('payment_type_id', $expense->payment_type_id) == $method->id ? 'selected' : '' }}> {{-- Corrected selected logic --}}
                                                        {{ $method->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('payment_type_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="vendor_id" class="form-label">{{ __('Vendor') }}</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <select class="form-select select2 @error('vendor_id') is-invalid @enderror"
                                                id="vendor_id" name="vendor_id">
                                                <option value="">{{ __('Select Vendor (Optional)') }}</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}"
                                                        {{ old('vendor_id', $expense->vendor_id) == $vendor->id ? 'selected' : '' }}> {{-- Corrected selected logic --}}
                                                        {{ $vendor->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('vendor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="expense_date" class="form-label">{{ __('Expense Date') }} <span
                                                class="text-red">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <div class="input-group date" id="expense_date_picker">
                                                <input type="date"
                                                    class="form-control datetimepicker-input @error('expense_date') is-invalid @enderror"
                                                    name="expense_date" id="expense_date" data-target="#expense_date_picker"
                                                    value="{{ old('expense_date', $expense->expense_date ? $expense->expense_date : '') }}" required />
                                                <span class="input-group-text" data-target="#expense_date_picker" data-bs-toggle="datetimepicker">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            @error('expense_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                        <div class="col-md-3">
                                            <label for="amount" class="form-label">{{ __('Amount') }} <span
                                                    class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <input type="number"
                                                    class="form-control @error('amount') is-invalid @enderror"
                                                    id="amount" name="amount" placeholder="{{ __('Enter Amount') }}"
                                                    value="{{ old('amount', $expense->amount) }}" step="0.01" required> {{-- Corrected value to use existing expense amount --}}
                                                @error('amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="currency" class="form-label">{{ __('Currency') }} <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                                    <option value="">{{ __('Select Currency') }}</option>
                                                    @foreach ($currencies as $currency)
                                                        <option value="{{ $currency->sign }}" 
                                                            {{ old('currency', $expense->currency) === $currency->sign ? 'selected' : '' }}>
                                                            {{ $currency->sign }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                
                                                @error('currency')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="description"
                                                class="form-label">{{ __('Description') }}</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                    placeholder="{{ __('Enter Description') }}">{{ old('description', $expense->description) }}</textarea> {{-- Corrected value to use existing description --}}
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="reference_number"
                                                class="form-label">{{ __('Reference Number (Optional)') }}</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <input type="text"
                                                    class="form-control @error('reference_number') is-invalid @enderror"
                                                    id="reference_number" name="reference_number"
                                                    placeholder="{{ __('Enter Reference Number') }}"
                                                    value="{{ old('reference_number', $expense->reference_number) }}"> {{-- Corrected value to use existing reference_number --}}
                                                @error('reference_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="receipt_path" class="form-label">{{ __('Receipt') }}</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="input-group">
                                                    <input type="file"
                                                        class="form-control @error('receipt_path') is-invalid @enderror"
                                                        id="receipt_path" name="receipt_path" aria-describedby="receipt_path_label">
                                                    <label class="input-group-text" for="receipt_path" id="receipt_path_label">{{ __('Choose file') }}</label>
                                                </div>
                                                @error('receipt_path')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="purchase_order_id"
                                                class="form-label">{{ __('Purchase Order (Optional)') }}</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <select
                                                    class="form-select select2 @error('purchase_order_id') is-invalid @enderror"
                                                    id="purchase_order_id" name="purchase_order_id">
                                                    <option value="">{{ __('Select Purchase Order') }}</option>
                                                    @foreach ($purchaseOrders as $po)
                                                        <option value="{{ $po->id }}"
                                                            {{ old('purchase_order_id', $expense->purchase_order_id) == $po->id ? 'selected' : '' }}> {{-- Corrected selected logic --}}
                                                            {{ $po->po_number }} - {{ $po->supplier->name ?? 'N/A' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('purchase_order_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                            <div class="col-md-3">
                                                <label for="inventory_item_id"
                                                    class="form-label">{{ __('Inventory Item (Optional)') }}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <select
                                                        class="form-select select2 @error('inventory_item_id') is-invalid @enderror"
                                                        id="inventory_item_id" name="inventory_item_id">
                                                        <option value="">{{ __('Select Inventory Item') }}</option>
                                                        @foreach ($inventoryItems as $item)
                                                            <option value="{{ $item->id }}"
                                                                {{ old('inventory_item_id', $expense->inventory_item_id) == $item->id ? 'selected' : '' }}> {{-- Corrected selected logic --}}
                                                                {{ $item->item_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('inventory_item_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="pos_location_id"
                                                    class="form-label">{{ __('POS Location (Optional)') }}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <select
                                                        class="form-select select2 @error('pos_location_id') is-invalid @enderror"
                                                        id="pos_location_id" name="pos_location_id">
                                                        <option value="">{{ __('Select POS Location') }}</option>
                                                        @foreach ($posLocations as $location)
                                                            <option value="{{ $location->id }}"
                                                                {{ old('pos_location_id', $expense->pos_location_id) == $location->id ? 'selected' : '' }}> {{-- Corrected selected logic --}}
                                                                {{ $location->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addPosLocationModal">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    @error('pos_location_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="is_reimbursable"
                                                    class="form-label">{{ __('Reimbursable') }}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="is_reimbursable" name="is_reimbursable"
                                                            {{ old('is_reimbursable', $expense->is_reimbursable) ? 'checked' : '' }}> {{-- Corrected checked logic --}}
                                                        <label class="form-check-label" for="is_reimbursable"></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="notes"
                                                    class="form-label">{{ __('Notes (Optional)') }}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                                        placeholder="{{ __('Enter Notes') }}">{{ old('notes', $expense->notes) }}</textarea> {{-- Corrected value to use existing notes --}}
                                                    @error('notes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">{{ __('Save Expense') }}</button>
                                        <a href="{{ route('list_expenses') }}"
                                            class="btn btn-secondary">{{ __('Cancel') }}</a>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Category Modal --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">{{ __('Add New Expense Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form id="addCategoryForm" action="{{ route('store_category_expenses') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">{{ __('Category Name') }} <span class="required">*</span></label>
                            <input type="text" class="form-control" id="category_name" name="name" required>
                            <div class="error text-danger" id="category_name_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="category_description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="category_description" name="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Category') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Add Category Modal --}}

    {{-- Add POS Location Modal --}}
    <div class="modal fade" id="addPosLocationModal" tabindex="-1" role="dialog" aria-labelledby="addPosLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPosLocationModalLabel">{{ __('Add New POS Location') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addPosLocationForm" action="{{ route('store_location') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="location_name" class="form-label">{{ __('Location Name') }} <span class="required">*</span></label>
                            <input type="text" class="form-control" id="location_name" name="name" required>
                            <div class="error text-danger" id="location_name_error"></div>
                        </div>
                        <div class="mb-3">
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Location') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Add POS Location Modal --}}
@endsection