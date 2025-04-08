@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Create New Expense') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('list_expenses') }}">{{ __('Expenses') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Create') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <form action="{{ route('store_expenses') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="row"> {{-- Main row for the entire form content --}}


                                    <div class="col-md-3">
                                        <label for="expense_category_id" class="col-form-label">{{ __('Expense Category') }}
                                            <span class="required">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <select class="form-control @error('expense_category_id') is-invalid @enderror"
                                                id="expense_category_id" name="expense_category_id" required>
                                                <option value="">{{ __('Select Category') }}</option>
                                                @foreach ($expenseCategories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#addCategoryModal"> <i class="fas fa-plus"></i> </button>
                                            </div>
                                              @error('expense_category_id')
                                                <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="payment_method_id" class="col-form-label">{{ __('Payment Method') }}
                                            <span class="required">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select class="form-control @error('payment_method_id') is-invalid @enderror"
                                                id="payment_method_id" name="payment_method_id" required>
                                                <option value="">{{ __('Select Payment Method') }}</option>
                                                @foreach ($paymentMethods as $method)
                                                    <option value="{{ $method->id }}"
                                                        {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                                        {{ $method->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('payment_method_id')
                                                <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="vendor_id" class="col-form-label">{{ __('Vendor') }}</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select class="form-control select2 @error('vendor_id') is-invalid @enderror"
                                                id="vendor_id" name="vendor_id">
                                                <option value="">{{ __('Select Vendor (Optional)') }}</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}"
                                                        {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                        {{ $vendor->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('vendor_id')
                                                <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="expense_date" class="col-form-label">{{ __('Expense Date') }} <span
                                                class="required">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="input-group date" id="expense_date_picker"
                                                data-target-input="nearest">
                                                <input type="text"
                                                    class="form-control datetimepicker-input @error('expense_date') is-invalid @enderror"
                                                    name="expense_date" id="expense_date" data-target="#expense_date_picker"
                                                    value="{{ old('expense_date') }}" required />
                                                <div class="input-group-append" data-target="#expense_date_picker"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                            @error('expense_date')
                                                <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                        <div class="col-md-3">
                                            <label for="amount" class="col-form-label">{{ __('Amount') }} <span
                                                    class="required">*</span></label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="number"
                                                    class="form-control @error('amount') is-invalid @enderror"
                                                    id="amount" name="amount" placeholder="{{ __('Enter Amount') }}"
                                                    value="{{ old('amount', 0) }}" step="0.01" required>
                                                @error('amount')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="currency" class="col-form-label">{{ __('Currency') }} <span
                                                    class="required">*</span></label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select class="form-control @error('currency') is-invalid @enderror"
                                                    id="currency" name="currency" required>
                                                    <option value="USD"
                                                        {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD
                                                    </option>
                                                    <option value="EUR"
                                                        {{ old('currency', 'EUR') == 'EUR' ? 'selected' : '' }}>EUR
                                                    </option>
                                                    <option value="GBP"
                                                        {{ old('currency', 'GBP') == 'GBP' ? 'selected' : '' }}>GBP
                                                    </option>
                                                </select>
                                                @error('currency')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="description"
                                                class="col-form-label">{{ __('Description') }}</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                    placeholder="{{ __('Enter Description') }}">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="reference_number"
                                                class="col-form-label">{{ __('Reference Number (Optional)') }}</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text"
                                                    class="form-control @error('reference_number') is-invalid @enderror"
                                                    id="reference_number" name="reference_number"
                                                    placeholder="{{ __('Enter Reference Number') }}"
                                                    value="{{ old('reference_number') }}">
                                                @error('reference_number')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="receipt_path" class="col-form-label">{{ __('Receipt') }}</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="custom-file">
                                                    <input type="file"
                                                        class="custom-file-input @error('receipt_path') is-invalid @enderror"
                                                        id="receipt_path" name="receipt_path">
                                                    <label class="custom-file-label"
                                                        for="receipt_path">{{ __('Choose file') }}</label>
                                                </div>
                                                @error('receipt_path')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="purchase_order_id"
                                                class="col-form-label">{{ __('Purchase Order (Optional)') }}</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select
                                                    class="form-control select2 @error('purchase_order_id') is-invalid @enderror"
                                                    id="purchase_order_id" name="purchase_order_id">
                                                    <option value="">{{ __('Select Purchase Order') }}</option>
                                                    @foreach ($purchaseOrders as $po)
                                                        <option value="{{ $po->id }}"
                                                            {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                                            {{ $po->po_number }} - {{ $po->supplier->name ?? 'N/A' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('purchase_order_id')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                            <div class="col-md-3">
                                                <label for="inventory_item_id"
                                                    class="col-form-label">{{ __('Inventory Item (Optional)') }}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <select
                                                        class="form-control select2 @error('inventory_item_id') is-invalid @enderror"
                                                        id="inventory_item_id" name="inventory_item_id">
                                                        <option value="">{{ __('Select Inventory Item') }}</option>
                                                        @foreach ($inventoryItems as $item)
                                                            <option value="{{ $item->id }}"
                                                                {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                                                {{ $item->item_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('inventory_item_id')
                                                        <span class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="pos_location_id"
                                                    class="col-form-label">{{ __('POS Location (Optional)') }}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <select
                                                        class="form-control select2 @error('pos_location_id') is-invalid @enderror"
                                                        id="pos_location_id" name="pos_location_id">
                                                        <option value="">{{ __('Select POS Location') }}</option>
                                                        @foreach ($posLocations as $location)
                                                            <option value="{{ $location->id }}"
                                                                {{ old('pos_location_id') == $location->id ? 'selected' : '' }}>
                                                                {{ $location->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#addPosLocationModal"> <i class="fas fa-plus"></i> </button>
                                                    </div>
                                                    @error('pos_location_id')
                                                        <span class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="is_reimbursable"
                                                    class="col-form-label">{{ __('Reimbursable') }}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="is_reimbursable" name="is_reimbursable"
                                                            {{ old('is_reimbursable') ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="notes"
                                                    class="col-form-label">{{ __('Notes (Optional)') }}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                                        placeholder="{{ __('Enter Notes') }}">{{ old('notes') }}</textarea>
                                                    @error('notes')
                                                        <span class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>


                                        </div> {{-- End of main row --}}
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addCategoryForm" action="{{ route('store_category_expenses') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_name">{{ __('Category Name') }} <span class="required">*</span></label>
                            <input type="text" class="form-control" id="category_name" name="name" required>
                            <span class="error text-danger" id="category_name_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="category_description">{{ __('Description') }}</label>
                            <textarea class="form-control" id="category_description" name="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addPosLocationForm" action="{{ route('store_location') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="location_name">{{ __('Location Name') }} <span class="required">*</span></label>
                            <input type="text" class="form-control" id="location_name" name="name" required>
                            <span class="error text-danger" id="location_name_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="location_address">{{ __('Address') }}</label>
                            <textarea class="form-control" id="location_address" name="address"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="location_contact_phone">{{ __('Contact Phone') }}</label>
                            <input type="text" class="form-control" id="location_contact_phone" name="contact_phone">
                        </div>
                        <div class="form-group">
                            <label for="location_email">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="location_email" name="email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Location') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Add POS Location Modal --}}

@endsection



@section('scripts')
   
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('#expense_date_picker').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            bsCustomFileInput.init(); // For custom file input to show file name

             // Add Category Modal Form Submission
             $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#addCategoryModal').modal('hide');
                            $('#expense_category_id').append(new Option(response.category.name, response.category.id, false, true));
                            $('#expense_category_id').trigger('change'); // Notify Select2 of change
                            form[0].reset(); // Reset modal form
                            // Optionally display a success message
                            // alert('Category added successfully!');
                        } else {
                            // Display errors in modal
                            $('#category_name_error').text(response.errors.name ? response.errors.name[0] : '');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error (e.g., display error message)
                        console.error("AJAX error:", status, error);
                        alert('Error adding category. Please try again.');
                    }
                });
            });

            // Add POS Location Modal Form Submission
            $('#addPosLocationForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#addPosLocationModal').modal('hide');
                            $('#pos_location_id').append(new Option(response.pos_location.name, response.pos_location.id, false, true));
                            $('#pos_location_id').trigger('change'); // Notify Select2 of change
                            form[0].reset(); // Reset modal form
                            // Optionally display a success message
                            // alert('POS Location added successfully!');
                        } else {
                            // Display errors in modal
                            $('#location_name_error').text(response.errors.name ? response.errors.name[0] : '');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error (e.g., display error message)
                        console.error("AJAX error:", status, error);
                        alert('Error adding POS Location. Please try again.');
                    }
                });
            });

            // Clear modal errors on modal hidden event for Category
            $('#addCategoryModal').on('hidden.bs.modal', function () {
                $('#category_name_error').text('');
                $('#addCategoryForm')[0].reset(); // Reset form on close as well, for consistency
            });

            // Clear modal errors on modal hidden event for POS Location
            $('#addPosLocationModal').on('hidden.bs.modal', function () {
                $('#location_name_error').text('');
                $('#addPosLocationForm')[0].reset(); // Reset form on close
            });
        });
    </script>
@endsection
