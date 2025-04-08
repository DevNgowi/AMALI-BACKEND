@extends('layouts.app')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Category') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="add_btn justify-content-end pb-2 d-flex">
                        @can('can create item category')
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#registerCategoryModal">
                                Add New Category
                            </button>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Group name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $key => $category)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->itemGroup->name ?? 'N/A' }}</td>
                                        <td>
                                            @can('can edit item category')
                                                <span class="text-secondary pr-3" data-bs-toggle="modal"
                                                    data-bs-target="#editCategoryModal"
                                                    onclick="populateEditCategoryModal({{ json_encode($category) }})">
                                                    <i class="fas fa-pen"></i>
                                                </span>
                                            @endcan
                                            @can('can delete item category')
                                                <span class="text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#confirmationModal"
                                                    onclick="showConfirmationModal('{{ route('delete_category', $category->id) }}', 'Are you sure you want to delete this category?')">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No Categories available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Category Modal -->
    <div class="modal fade" id="registerCategoryModal" tabindex="-1" aria-labelledby="registerCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('store_category') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerCategoryModalLabel">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" id="name"
                                class="form-control" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="item_group_id" class="form-label">Item Group</label>
                            <select name="item_group_id" id="item_group_id" class="form-control" required>
                                <option value="">--- Select ---</option>
                                @foreach ($item_groups as $item_group)
                                    <option value="{{ $item_group->id }}">{{ $item_group->name }}</option>
                                @endforeach
                            </select>
                            @error('item_group_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Category Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_item_group_id" class="form-label">Item Group</label>
                            <select name="item_group_id" id="edit_item_group_id" class="form-control" required>
                                <option value="">--- Select ---</option>
                                @foreach ($item_groups as $item_group)
                                    <option value="{{ $item_group->id }}">{{ $item_group->name }}</option>
                                @endforeach
                            </select>
                            @error('item_group_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control"></textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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

    <!-- JavaScript -->
    <script>
        function populateEditCategoryModal(category) {
            document.getElementById('editCategoryForm').action = `/inventory/categories/update/${category.id}`;
            document.getElementById('edit_name').value = category.name || '';
            document.getElementById('edit_description').value = category.description || '';
            const select = document.getElementById('edit_item_group_id');
            select.value = category.item_group_id || ''; // Pre-select the current item group
        }

        function showConfirmationModal(url, message) {
            const modal = document.getElementById('confirmationModal');
            modal.querySelector('.modal-body').textContent = message;
            modal.querySelector('form').action = url;
            new bootstrap.Modal(modal).show();
        }
    </script>
@endsection