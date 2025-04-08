@extends('layouts.app')
@section('content')
    @include('message')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Roles') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid pt-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="add_btn justify-content-end pb-2 d-flex">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerRoleModal">
                            Add New Role
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <!-- Edit Button -->
                                            <span class="pr-3"
                                                onclick="openEditModal({{ $role->id }}, '{{ $role->name }}')">
                                                <i class="fas fa-pen"></i>
                                            </span>
                                            <span data-toggle="modal" data-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('role_delete', $role->id) }}', 'Are you sure you want to delete this role?')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="editRoleForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Role</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                                        <label for="roleName" class="form-label">Role Name</label> {{-- Added form-label class for better accessibility in Bootstrap 5 --}}
                                        <input type="text" class="form-control" id="roleName" name="name" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="registerRoleModal" tabindex="-1" aria-labelledby="registerRoleModalLabel"
                    aria-hidden="true"> {{-- Changed role="dialog" to aria-labelledby for Bootstrap 5 --}}
                    <div class="modal-dialog"> {{-- Removed role="document" as it's not strictly needed and Bootstrap 5 handles modal structure --}}
                        <div class="modal-content">
                            <form action="{{ route('store_route') }}" method="POST" id="registerRoleForm">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="registerRoleModalLabel">Register New Role</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button> {{-- Changed class="close", data-dismiss, and <span> to btn-close and data-bs-dismiss --}}
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                                        <label for="roleName" class="form-label">Role Name</label> {{-- Added form-label class for better accessibility in Bootstrap 5 --}}
                                        <input type="text" class="form-control" id="roleName" name="name"
                                            placeholder="Enter role name" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Role</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    </div>

    <script>
        function openEditModal(roleId, roleName) {
            document.getElementById('editRoleForm').action = `/roles/update/${roleId}`;
            document.getElementById('roleName').value = roleName;
            $('#editModal').modal('show');
        }
    </script>
@endsection
