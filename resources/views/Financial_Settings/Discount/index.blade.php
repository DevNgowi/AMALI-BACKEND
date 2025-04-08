@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Discount Management') }}</h1>
                </div>
            </div>
        </div>
    </div>

    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="add_btn justify-content-end pb-2 d-flex">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#registerDiscountModal">
                            Add Discount
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Valid Until</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($discounts as $key => $discount)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $discount->name }}</td>
                                        <td>{{ $discount->code ?? 'N/A' }}</td>
                                        <td>{{ $discount->discountType->name }}</td>
                                        <td>
                                            @if ($discount->discountType->name === 'Percentage')
                                                {{ $discount->value }}%
                                            @else
                                                {{ number_format($discount->value, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $discount->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $discount->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $discount->expires_at ? date('Y-m-d', strtotime($discount->expires_at)) : 'Never' }}
                                        </td>
                                        <td>
                                            <span class="text-secondary pr-3" data-bs-toggle="modal"
                                                data-bs-target="#editDiscountModal"
                                                onclick="populateEditModal({{ json_encode($discount) }})">
                                                <i class="fas fa-pen"></i>
                                            </span>
                                            <span class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('delete_discount', $discount->id) }}', 'Are you sure you want to delete this discount?')">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No discounts available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Register Discount Modal -->
<div class="modal fade" id="registerDiscountModal" tabindex="-1" aria-labelledby="registerDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerDiscountModalLabel">Create New Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registerDiscountForm" action="{{ route('store_discount') }}" method="POST" onsubmit="return validateDiscountForm()">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="name" class="form-label">Discount Name <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="name" id="name" class="form-control" required>
                            <div class="invalid-feedback" id="nameFeedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="code" class="form-label">Discount Code</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="code" id="code" class="form-control">
                            <div class="invalid-feedback" id="codeFeedback"></div>
                        </div>
                    
                        <div class="col-md-3">
                            <label for="discount_type_id" class="form-label">Discount Type <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select name="discount_type_id" id="discount_type_id" class="form-select" required onchange="toggleValueField()">
                                @foreach ($discountTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="discountTypeFeedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" step="0.01" name="value" id="value" class="form-control" required>
                            <div class="invalid-feedback" id="valueFeedback"></div>
                        </div>
                   
                        <div class="col-md-3">
                            <label for="minimum_purchase_amount" class="form-label">Minimum Purchase Amount</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" step="0.01" name="minimum_purchase_amount" id="minimum_purchase_amount" class="form-control">
                            <div class="invalid-feedback" id="minPurchaseFeedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="maximum_discount_amount" class="form-label">Maximum Discount Amount</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" step="0.01" name="maximum_discount_amount" id="maximum_discount_amount" class="form-control">
                            <div class="invalid-feedback" id="maxDiscountFeedback"></div>
                        </div>
                   
                        <div class="col-md-3">
                            <label for="starts_at" class="form-label">Start Date</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="datetime-local" name="starts_at" id="starts_at" class="form-control">
                            <div class="invalid-feedback" id="startDateFeedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="expires_at" class="form-label">Expiry Date</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
                            <div class="invalid-feedback" id="expiryDateFeedback"></div>
                        </div>
                   
                        <div class="col-md-3">
                            <label for="usage_limit" class="form-label">Usage Limit</label>  
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" name="usage_limit" id="usage_limit" class="form-control">
                            <div class="invalid-feedback" id="usageLimitFeedback"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Is Active ? <span class="text-danger">*</span></label>
                            <select name="is_active" id="is_active" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            @error('is_active')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Can be combined with other discounts</label>
                            <select name="is_combinable" id="is_combinable" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Discount Modal -->
<div class="modal fade" id="editDiscountModal" tabindex="-1" aria-labelledby="editDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDiscountModalLabel">Edit Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDiscountForm" action="{{ isset($discount) ? route('update_discount', $discount->id) : '#' }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="edit_name" class="form-label">Discount Name <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="name" value="{{ $discount->name ?? '' }}" id="edit_name" class="form-control" required>
                            <div class="invalid-feedback" id="editNameFeedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_code" class="form-label">Discount Code</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="code" value="{{ $discount->code ?? '' }}" id="edit_code" class="form-control">
                            <div class="invalid-feedback" id="editCodeFeedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_discount_type_id" class="form-label">Discount Type <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select name="discount_type_id" id="edit_discount_type_id" class="form-select" required onchange="toggleEditValueField()">
                                @foreach ($discountTypes as $type)
                                    <option value="{{ $type->id }}" {{ isset($discount) && $type->id == $discount->discount_type_id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="editDiscountTypeFeedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_value" class="form-label">Value <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" step="0.01" name="value" value="{{ $discount->value ?? '' }}" id="edit_value" class="form-control" required>
                            <div class="invalid-feedback" id="editValueFeedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_minimum_purchase_amount" class="form-label">Minimum Purchase Amount</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" step="0.01" name="minimum_purchase_amount" value="{{ $discount->minimum_purchase_amount ?? '' }}" id="edit_minimum_purchase_amount" class="form-control">
                            <div class="invalid-feedback" id="editMinPurchaseFeedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_maximum_discount_amount" class="form-label">Maximum Discount Amount</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" step="0.01" name="maximum_discount_amount" value="{{ $discount->maximum_discount_amount ?? '' }}" id="edit_maximum_discount_amount" class="form-control">
                            <div class="invalid-feedback" id="editMaxDiscountFeedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_starts_at" class="form-label">Start Date</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="datetime-local" name="starts_at" value="{{ $discount->starts_at ?? '' }}" id="edit_starts_at" class="form-control">
                            <div class="invalid-feedback" id="editStartDateFeedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_expires_at" class="form-label">Expiry Date</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="datetime-local" name="expires_at" value="{{ $discount->expires_at ?? '' }}" id="edit_expires_at" class="form-control">
                            <div class="invalid-feedback" id="editExpiryDateFeedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_usage_limit" class="form-label">Usage Limit</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" name="usage_limit" value="{{ $discount->usage_limit ?? '' }}" id="edit_usage_limit" class="form-control">
                            <div class="invalid-feedback" id="editUsageLimitFeedback"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Is Active ? <span class="text-danger">*</span></label>
                            <select name="is_active" id="edit_is_active" class="form-control">
                                <option value="1" {{ isset($discount) && $discount->is_active ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ isset($discount) && !$discount->is_active ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Can be combined with other discounts</label>
                            <select name="is_combinable" id="edit_is_combinable" class="form-control">
                                <option value="1" {{ isset($discount) && $discount->is_combinable ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ isset($discount) && !$discount->is_combinable ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3">{{ $discount->description ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" {{ !isset($discount) ? 'disabled' : '' }}>Update Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script>
        function toggleValueField() {
            const discountType = document.getElementById('discount_type_id');
            const valueInput = document.getElementById('value');
            const valueLabel = valueInput.previousElementSibling;

            if (discountType.options[discountType.selectedIndex].text === 'Percentage') {
                valueInput.setAttribute('max', '100');
                valueLabel.textContent = 'Percentage Value';
                valueInput.placeholder = 'Enter percentage (e.g., 10)';
            } else {
                valueInput.removeAttribute('max');
                valueLabel.textContent = 'Fixed Amount';
                valueInput.placeholder = 'Enter amount';
            }
        }

        function validateDiscountForm() {

            let isValid = true;


            // Clear previous feedback

            document.querySelectorAll('.form-control').forEach(input => {

                input.classList.remove('is-invalid');

                const feedback = document.getElementById(input.id + 'Feedback');

                if (feedback) feedback.innerText = '';

            });


            // Validate Discount Name

            const name = document.getElementById('name');

            if (!name.value.trim()) {

                name.classList.add('is-invalid');

                document.getElementById('nameFeedback').innerText = 'Discount Name is required.';

                isValid = false;

            }


            // Validate Discount Type

            const discountType = document.getElementById('discount_type_id');

            if (!discountType.value) {

                discountType.classList.add('is-invalid');

                document.getElementById('discountTypeFeedback').innerText = 'Discount Type is required.';

                isValid = false;

            }

            // Validate Value

            const value = document.getElementById('value');

            if (!value.value) {

                value.classList.add('is-invalid');

                document.getElementById('valueFeedback').innerText = 'Value is required.';

                isValid = false;

            }

            return isValid;

        }
    </script>

    <style>
        .is-invalid {

            border-color: #dc3545;
            /* Bootstrap's danger color */

        }
    </style>
@endsection
