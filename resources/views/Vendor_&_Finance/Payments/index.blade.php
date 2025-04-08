@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Payments') }}</h1>
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
                            data-bs-target="#registerPaymentModal">
                            Add Payment
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Short Code</th>
                                    <th>Payment Type</th>
                                    <th>Payment Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $key => $payment)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $payment->short_code }}</td>
                                        <td>{{ $payment->payment_type->name }}</td>
                                        <td>{{ $payment->payment_method }}</td> 
                                        <td>
                                            <span class="text-secondary mr-3"
                                                onclick="editPaymentModal({{ $payment->id }}, 
                                        '{{ $payment->short_code }}', '{{ $payment->payment_method }}', '{{ $payment->payment_type_id }}')">
                                                <i class="fas fa-pen"></i>
                                            </span>

                                            <span class="text-danger" data-toggle="modal" data-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('delete_payment', $payment->id) }}', 'Are you sure you want to delete this payment?')">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No payments available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="registerPaymentModal" tabindex="-1" aria-labelledby="registerPaymentModalLabel"
        aria-hidden="true"> 
        <div class="modal-dialog">
            <form action="{{ route('store_payment') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerPaymentModalLabel">Add New Payment</h5> {{-- Added modal title id for aria-labelledby --}}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        {{-- Changed class="close", data-dismiss, and <span> to btn-close and data-bs-dismiss --}}
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                            <label for="short_code" class="form-label">Short Code</label> {{-- Added form-label class for better accessibility in Bootstrap 5 --}}
                            <input type="text" name="short_code" value="{{ old('short_code') }}" id="short_code"
                                class="form-control" required>
                            @error('short_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                            <label for="payment_type_id" class="form-label">Payment Type</label> {{-- Added form-label class --}}
                            <select name="payment_type_id" id="payment_type_id" class="form-select" required>
                                {{-- Changed form-control to form-select for Bootstrap 5 select --}}
                                <option value="">Select Payment Type</option>
                                @foreach ($payment_types as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('payment_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                            <label for="payment_method" class="form-label">Payment Description</label> {{-- Added form-label class --}}
                            <input type="text" name="payment_method" value="{{ old('payment_method') }}"
                                id="payment_method" class="form-control" required>
                            @error('payment_method')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        {{-- Changed data-dismiss to data-bs-dismiss --}}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
        {{-- Updated role="dialog" to aria-labelledby for Bootstrap 5 --}}
        <div class="modal-dialog">
            <form id="editPaymentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPaymentModalLabel">Edit Payment</h5> {{-- Added modal title id for aria-labelledby --}}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                            <label for="edit_short_code" class="form-label">Short Code</label> {{-- Added form-label class --}}
                            <input type="text" name="short_code" id="shortCode" class="form-control" required>
                        </div>
                        <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                            <label for="edit_payment_type" class="form-label">Payment Type</label> {{-- Added form-label class --}}
                            <select name="payment_type_id" id="paymentType" class="form-select" required>
                                {{-- Changed form-control to form-select for Bootstrap 5 select --}}
                                <option value="">Select Payment Type</option>
                                @foreach ($payment_types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                            <label for="edit_payment_method" class="form-label">Payment Description</label>
                            {{-- Added form-label class --}}
                            <input type="text" name="payment_method" id="paymentMethod" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        {{-- Changed data-dismiss to data-bs-dismiss --}}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editPaymentModal(paymentId, shortCode, paymentMethod, paymentType) {
            event.preventDefault();
            // Corrected form action to match your defined route
            document.getElementById('editPaymentForm').action = `/vendors_finance/payments/${paymentId}`;
            document.getElementById('shortCode').value = shortCode;
            document.getElementById('paymentType').value = paymentType;
            document.getElementById('paymentMethod').value = paymentMethod;

            // Show the modal
            $('#editPaymentModal').modal('show');
        }
    </script>
@endsection
