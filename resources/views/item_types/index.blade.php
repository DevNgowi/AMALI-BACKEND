@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Item Type') }}</h1>
                </div>
            </div>
        </div>
    </div>

    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-end pb-2">
                        @can('can create item type')
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#registerItemTypeModal">
                                Add Item Type
                            </button>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($item_types as $key => $item_type)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item_type->name }}</td>
                                        <td>
                                            @can('can edit item type')
                                                <span class="text-secondary pe-3" data-bs-toggle="modal"
                                                    data-bs-target="#editItemTypeModal"
                                                    onclick="populateEditItemTypeModal({{ json_encode($item_type) }})">
                                                    <i class="fas fa-pen"></i>
                                                </span>
                                            @endcan
                                            @can('can delete item type')
                                                <a href="" class="text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#confirmationModal"
                                                    onclick="showConfirmationModal('{{ route('delete_item_type', $item_type->id) }}', 'Are you sure you want to delete this item type?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No item type available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerItemTypeModal" tabindex="-1" aria-labelledby="registerItemTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('store_item_type') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerItemTypeModalLabel">Add New Item Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Item Type Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" id="name"
                                class="form-control" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editItemTypeModal" tabindex="-1" aria-labelledby="editItemTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editItemTypeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editItemTypeModalLabel">Edit Item Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Item Type Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
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

    <script>
        function populateEditItemTypeModal(itemType) {
            const form = document.getElementById('editItemTypeForm');
            form.action = `/inventory/item_type/update/${itemType.id}`;
            document.getElementById('edit_name').value = itemType.name;
        }
    </script>
@endsection
