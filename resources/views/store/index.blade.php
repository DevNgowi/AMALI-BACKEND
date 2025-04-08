@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Stores') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="add_btn justify-content-end pb-2 d-flex">
                        @can('can create store')
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerStoreModal">
                                Add New Store
                            </button>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table id="dataTable" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Store Manager</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stores as $key => $store)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $store->name }}</td>
                                        <td>{{ $store->location }}</td>
                                        <td>{{ $store->user->username }}</td>
                                        <td>
                                            <!-- Edit Button -->
                                            @can('can edit store')
                                                <span class="text-secondary me-3" data-bs-toggle="modal"
                                                    data-bs-target="#editStoreModal"
                                                    onclick="populateEditModal({{ json_encode($store) }})">
                                                    <i class="fas fa-pen"></i>
                                                </span>
                                            @endcan

                                            <!-- Delete Button -->
                                            @can('can delete store')
                                                <a href="javascript:void(0);" class="text-danger"
                                                    onclick="showConfirmationModal('{{ route('delete_store', $store->id) }}', 'Are you sure you want to delete this store?', 'This store could not be deleted.', '{{ $store->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No stores available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Store Modal -->
    <div class="modal fade" id="registerStoreModal" tabindex="-1" aria-labelledby="registerStoreModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerStoreModalLabel">Add New Store</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Store Name
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="manager" class="form-label">Store Manager
                                <span class="text-danger">*</span>
                            </label>
                            <select name="manager_id" id="manager" class="form-select" required>
                                <option value="" disabled selected>Select Manager</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Store</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Store Modal -->
    <div class="modal fade" id="editStoreModal" tabindex="-1" aria-labelledby="editStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editStoreForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStoreModalLabel">Edit Store</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Store Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_location" class="form-label">Location</label>
                            <input type="text" name="location" id="edit_location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_manager" class="form-label">Store Manager</label>
                            <select name="manager_id" id="edit_manager" class="form-select" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Store</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
