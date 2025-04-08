@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Extra Charge') }}</h1>
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
                            data-bs-target="#registerExtraChargeModal">
                            Add Extra Charge
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Tax</th>
                                    <th>Charge Type</th>
                                    <th>Charge Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($extraCharges as $key => $extraCharge)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $extraCharge->name }}</td>
                                        <td>{{ $extraCharge->tax->name ?? 'N/A' }}</td>
                                        <td>{{ $extraCharge->extraChargeType->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($extraCharge->amount, 2) }}</td>
                                        <td>
                                            <span class="text-secondary pr-3" data-bs-toggle="modal"
                                                data-bs-target="#editExtraChargeModal"
                                                onclick="populateEditExtraChargeModal({{ json_encode($extraCharge) }})">
                                                <i class="fas fa-pen"></i>
                                            </span>
                                            <span class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('delete_extra_charge', $extraCharge->id) }}', 'Are you sure you want to delete this extra charge?')">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No extra charges available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Extra Charge Modal -->
    <div class="modal fade" id="registerExtraChargeModal" tabindex="-1" aria-labelledby="registerExtraChargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerExtraChargeModalLabel">Register Extra Charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="registerExtraChargeForm" action="{{ route('store_extra_charge') }}" method="POST"
                    onsubmit="return validateExtraChargeForm()">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Extra Charge Name</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter extra charge name" required>

                            </div>
                            <div class="col-md-3">
                                <label for="tax_id" class="form-label">Tax</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="tax_id" id="tax_id" class="form-select">
                                    <option value="">Select Tax</option>
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="charge_type_id" class="form-label">Charge Type</label>

                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="charge_type_id" id="charge_type_id" class="form-select" required>
                                    <option value="">Select Charge Type</option>
                                    @foreach ($chargeTypes as $chargeType)
                                        <option value="{{ $chargeType->id }}">{{ $chargeType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="amount" class="form-label">Charge Amount</label>

                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                    placeholder="Enter fixed amount (e.g., 100)" required>

                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Extra Charge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Extra Charge Modal -->
    <div class="modal fade" id="editExtraChargeModal" tabindex="-1" role="dialog"
        aria-labelledby="editExtraChargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form id="editExtraChargeForm" method="POST"
                action="{{ isset($extraCharge) ? route('update_extra_charge', $extraCharge->id) : '#' }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editExtraChargeModalLabel">Edit Extra Charge</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="edit_name" class="form-label">Extra Charge Name</label>

                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="text" name="name" id="edit_name" class="form-control"
                                    value="{{ $extraCharge->name ?? '' }}" required>
                                <div class="invalid-feedback">Please enter a valid name</div>
                            </div>
                            <div class="col-md-3">
                                <label for="edit_tax_id" class="form-label">Tax</label>

                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="tax_id" id="edit_tax_id" class="form-select">
                                    <option value="">Select Tax</option>
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}"
                                            {{ isset($extraCharge) && $extraCharge->tax_id == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a tax</div>
                            </div>
                            <div class="col-md-3">
                                <label for="edit_charge_type_id" class="form-label">Charge Type</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="charge_type_id" id="edit_charge_type_id" class="form-select" required>
                                    <option value="">Select Charge Type</option>
                                    @foreach ($chargeTypes as $chargeType)
                                        <option value="{{ $chargeType->id }}"
                                            {{ isset($extraCharge) && $extraCharge->charge_type_id == $chargeType->id ? 'selected' : '' }}>
                                            {{ $chargeType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a charge type</div>
                            </div>
                            <div class="col-md-3">
                                <label for="edit_amount" class="form-label">Charge Amount</label>

                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="number" step="0.01" min="0" name="amount" id="edit_amount"
                                    class="form-control" value="{{ $extraCharge->amount ?? '' }}" required>
                                <div class="invalid-feedback">Please enter a valid amount</div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"
                            {{ !isset($extraCharge) ? 'disabled' : '' }}>Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
