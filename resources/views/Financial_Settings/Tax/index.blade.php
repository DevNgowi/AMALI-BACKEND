@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Tax Management') }}</h1>
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
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerTaxModal">
                            Add Tax
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Tax Type</th>
                                    <th>Tax Mode</th>
                                    <th>Tax Percentage</th>
                                    <th>Tax Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($taxes as $key => $tax)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $tax->name }}</td>
                                        <td>{{ ucfirst($tax->tax_type) }}</td>
                                        <td>{{ $tax->tax_mode }}</td>
                                        <td>{{ $tax->tax_mode == 'percentage' ? $tax->tax_percentage . '%' : 'N/A' }}</td>
                                        <td>{{ $tax->tax_mode == 'amount' ? number_format($tax->tax_amount, 2) : 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="text-secondary pr-3" data-bs-toggle="modal"
                                                data-bs-target="#editTaxModal"
                                                onclick="populateEditTaxModal({{ json_encode($tax) }})">
                                                <i class="fas fa-pen"></i>
                                            </span>
                                            <span class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('delete_tax', $tax->id) }}', 'Are you sure you want to delete this tax?')">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No taxes available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Tax Modal -->
    <div class="modal fade" id="registerTaxModal" tabindex="-1" aria-labelledby="registerTaxModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerTaxModalLabel">Register Tax</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="registerTaxForm" action="{{ route('store_tax') }}" method="POST"
                    onsubmit="return validateTaxForm()">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Tax Name</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="text" name="name" id="name" class="form-control"
                                placeholder="Enter tax name" required>
                            </div>
                            <div class="col-md-3">
                                <label for="tax_type" class="form-label">Tax Type</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="tax_type" id="tax_type" class="form-select" required
                                onchange="toggleTaxFields()">
                                <option value="">Select Tax Type</option>
                                <option value="inclusive">Inclusive</option>
                                <option value="exclusive">Exclusive</option>
                            </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tax_mode" class="form-label">Tax Mode</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="tax_mode" id="tax_mode" class="form-select" required
                                onchange="toggleTaxFields()">
                                <option value="">Select Tax Mode</option>
                                <option value="percentage">Percentage</option>
                                <option value="amount">Fixed Amount</option>
                            </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tax_percentage" class="form-label">Tax Percentage (%)</label>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3 tax-percentage-field" style="display: none;">
                                    <input type="number" step="0.01" name="tax_percentage" id="tax_percentage"
                                        class="form-control" placeholder="Enter percentage (e.g., 15)">
                                </div>
                                <div class="mb-3 tax-amount-field" style="display: none;">
                                    <label for="tax_amount" class="form-label">Tax Amount</label>
                                    <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control"
                                        placeholder="Enter fixed amount (e.g., 100)">
                                </div>
                            </div>
                        </div>
                
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Tax</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Tax Modal -->
    <div class="modal fade" id="editTaxModal" tabindex="-1" role="dialog" aria-labelledby="editTaxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editTaxForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaxModalLabel">Edit Tax</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Tax Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_tax_type">Tax Type</label>
                            <select name="tax_type" id="edit_tax_type" class="form-control" required
                                onchange="toggleEditTaxFields()">
                                <option value="inclusive">Inclusive</option>
                                <option value="exclusive">Exclusive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tax_mode" class="form-label">Tax Mode</label>
                            <select name="tax_mode" id="edit_tax_mode" class="form-select" required
                                onchange="toggleEditTaxFields()">
                                <option value="">Select Tax Mode</option>
                                <option value="percentage">Percentage</option>
                                <option value="amount">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="form-group edit-tax-percentage-field" style="display: none;">
                            <label for="edit_tax_percentage">Tax Percentage (%)</label>
                            <input type="number" step="0.01" name="tax_percentage" id="edit_tax_percentage"
                                class="form-control">
                        </div>
                        <div class="form-group edit-tax-amount-field" style="display: none;">
                            <label for="edit_tax_amount">Tax Amount</label>
                            <input type="number" step="0.01" name="tax_amount" id="edit_tax_amount"
                                class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
