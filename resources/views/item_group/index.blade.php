@extends('layouts.app')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Item Group') }}</h1>
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
                        @can('can create item group')
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#registerItemGroupModal">
                                Add Item Group
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
                                @forelse ($item_groups as $key => $item_group)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item_group->name }}</td>
                                        <td>
                                            @can('can edit item group')
                                                <button class="btn btn-sm text-secondary mr-3" data-bs-toggle="modal"
                                                    data-bs-target="#editItemGroupModal"
                                                    onclick="populateEditItemGroupModal({{ json_encode($item_group) }})">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                            @endcan

                                            @can('can delete item group')
                                                <button class="btn btn-sm text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#confirmationModal"
                                                    onclick="showConfirmationModal('{{ route('delete_item_group', $item_group->id) }}', 'Are you sure you want to delete this item group?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No item group available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerItemGroupModal" tabindex="-1" aria-labelledby="registerItemGroupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('store_item_group') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerItemGroupModalLabel">Add New Item Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Item Group Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" id="name"
                                class="form-control" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
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

    <div class="modal fade" id="editItemGroupModal" tabindex="-1" aria-labelledby="editItemGroupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editItemGroupForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editItemGroupModalLabel">Edit Item Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_item_group_name" class="form-label">Item Group Name</label>
                            <input type="text" name="name" id="edit_item_group_name" class="form-control" required>
                        </div>
                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
