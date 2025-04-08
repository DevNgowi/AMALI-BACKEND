@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Users') }}</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="add_btn justify-content-end pb-2 d-flex">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerUserModal">Add New
                            User</button>
                    </div>
                    <table class="table" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fullname</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>User Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $user->fullname }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>  @foreach($user->roles as $role)
                                        {{ $role->name }}@if(!$loop->last), @endif
                                    @endforeach</td>
                                    <td>
                                        <!-- Edit Button -->
                                        {{-- <button class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#editUserModal"
                                            data-user="{{ json_encode($user) }}">Edit</button> --}}
                                        <a href="{{ route('edit_users_with_permission', $user) }}" class="mr-2">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <!-- Delete Button -->
                                    
                                        <a href="javascript:void(0);" 
                                        class="text-danger"
                                        onclick="showConfirmationModal(
                                            '{{ route('delete_user', $user->id) }}',
                                            'Are you sure you want to delete {{ addslashes($user->username) }}?',
                                            'Unable to delete {{ addslashes($user->username) }}.',
                                            '{{ addslashes($user->username) }}'
                                        )">
                                         <i class="fas fa-trash"></i>
                                     </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                        </div>

                       


                        <div class="modal fade " id="registerUserModal" tabindex="-1" role="dialog"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('store_users') }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Register New User</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <!-- Full Name -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="fullname">Name</label>
                                                        <input type="text" class="form-control" id="fullname"
                                                            name="fullname" value="{{ old('fullname') }}" required>
                                                        @error('fullname')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Username -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="username">Username</label>
                                                        <input type="text" class="form-control" id="username"
                                                            name="username" value="{{ old('username') }}" required>
                                                        @error('username')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Email -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input type="email" class="form-control" id="email"
                                                            name="email" value="{{ old('email') }}" required>
                                                        @error('email')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Phone -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="phone">Phone</label>
                                                        <input type="tel" class="form-control" id="phone"
                                                            name="phone" value="{{ old('phone') }}" required>
                                                        @error('phone')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Password -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="password">Password</label>
                                                        <input type="password" class="form-control" id="password"
                                                            name="password" required>
                                                        @error('password')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="confirmpassword">Confirm Password</label>
                                                        <input type="password" class="form-control" id="confirmpassword"
                                                            name="confirmpassword" required>
                                                        @error('confirmpassword')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- PIN -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="pin">PIN (4 Digits)</label>
                                                        <input type="number" class="form-control" id="pin"
                                                            name="pin" value="{{ old('pin') }}" min="1000"
                                                            max="9999" required>
                                                        @error('pin')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- User Role -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="role">User Role</label>
                                                        <select class="form-control" id="role" name="role_id"
                                                            required>
                                                            <option value="">Select Role</option>
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}"
                                                                    {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                                    {{ $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('role_id')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save User</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="card-footer clearfix">
                            {{ $users->links() }}
                        </div> --}}
            

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <script>
        document.getElementById('pin').addEventListener('input', function(e) {
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4);
            }
        });
        $('#editUserModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var user = button.data('user'); // Extract user data from data-user attribute

            // Populate modal fields with user data
            $('#editFullname').val(user.fullname);
            $('#editUsername').val(user.username);
            $('#editEmail').val(user.email);
            $('#editPhone').val(user.phone);
            $('#editPin').val(user.pin);
            $('#editRole').val(user.role_id);

            // Set the form action to include the user ID for updating
            $('#editUserForm').attr('action', '/users/' + user.id); // Update the form action to include the user ID
        });
    </script>
@endsection
