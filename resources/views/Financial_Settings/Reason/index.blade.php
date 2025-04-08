@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Reason') }}</h1>
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
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerReasonModal">
                            Add Reason
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Reason Type</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reasons as $key => $reason)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $reason->reasonType->name }}</td>
                                        <td>{{ $reason->description }}</td>
                                        <td>
                                            <span class="text-secondary pr-3" data-bs-toggle="modal"
                                            data-bs-target="#editReasonModal"
                                            onclick="populateEditModal({{ json_encode($reason) }})">
                                            <i class="fas fa-pen"></i>
                                        </span>
                                            <span class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('delete_reason', $reason->id) }}', 'Are you sure you want to delete this reason?')">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No reasons available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Reason Modal -->
    <div class="modal fade" id="registerReasonModal" tabindex="-1" aria-labelledby="registerReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerReasonModalLabel">Register Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="registerReasonForm" action="{{ route('store_reason') }}" method="POST" onsubmit="return validateReasonForm()">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason_type_id" class="form-label">Reason Type</label>
                            <select name="reason_type_id" id="reason_type_id" class="form-select" required>
                                <option value="">Select Reason Type</option>
                                @foreach($reasonTypes as $reasonType)
                                    <option value="{{ $reasonType->id }}">{{ $reasonType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" placeholder="Enter description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Reason</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

<!-- Edit Reason Modal -->
<div class="modal fade" id="editReasonModal" tabindex="-1" role="dialog" aria-labelledby="editReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editReasonForm" method="POST" action="{{ isset($reason) ? route('update_reason', $reason->id) : '#' }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReasonModalLabel">Edit Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_reason_type_id" class="form-label">Reason Type</label>
                        <select name="reason_type_id" id="edit_reason_type_id" class="form-select" required>
                            <option value="">Select Reason Type</option>
                            @foreach($reasonTypes as $reasonType)
                                <option 
                                    value="{{ $reasonType->id }}" 
                                    {{ isset($reason) && $reason->reason_type_id == $reasonType->id ? 'selected' : '' }}
                                >
                                    {{ $reasonType->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select a reason type</div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea 
                            name="description" 
                            id="edit_description" 
                            class="form-control" 
                            required
                        >{{ $reason->description ?? '' }}</textarea>
                        <div class="invalid-feedback">Please enter a valid description</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" {{ !isset($reason) ? 'disabled' : '' }}>Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
