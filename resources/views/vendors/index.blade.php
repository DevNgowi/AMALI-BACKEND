@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Vendors') }}</h1>
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
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#registerVendorModal">Add
                            New Vendor</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendors as $key => $vendor)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $vendor->name }}</td>
                                        <td>{{ $vendor->contact_person }}</td>
                                        <td>{{ $vendor->email }}</td>
                                        <td>{{ $vendor->phone }}</td>
                                        <td>{{ $vendor->city->name ?? '' }}</td>
                                        <td>{{ $vendor->country->name ?? '' }}</td>
                                        <td>{{ $vendor->status }}</td>
                                        <td>
                                            <a href="{{ route('edit_vendor', $vendor->id) }}"><i
                                                    class="fas fa-pen text-secondary pr-3"></i></a>
                                            <span data-toggle="modal" data-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('delete_vendor', $vendor->id) }}', 'Are you sure you want to delete this Vendor?')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </span>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No vendors available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                    <!-- Add New Vendor Modal -->
                    <div class="modal fade" id="registerVendorModal" tabindex="-1" aria-labelledby="registerVendorModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <form action="{{ route('store_vendors') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="registerVendorModalLabel">Register New Vendor</h5> 
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="name" class="form-label">Name</label> 
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" id="name" name="name" class="form-control"
                                                       value="{{ old('name') }}" required>
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="email" class="form-label">Email</label> 
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="email" id="email" name="email" class="form-control"
                                                       value="{{ old('email') }}" required>
                                                @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="phone" class="form-label">Phone</label> {{-- Added form-label class --}}
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" id="phone" name="phone" class="form-control"
                                                       value="{{ old('phone') }}" required>
                                                @error('phone')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="contact_person" class="form-label">Contact Person</label> {{-- Added form-label class --}}
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" id="contact_person" name="contact_person"
                                                       class="form-control" value="{{ old('contact_person') }}" required>
                                                @error('contact_person')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-12 mb-3">
                                                <div class="mb-3"> {{-- Changed form-group to mb-3 for Bootstrap 5 spacing --}}
                                                    <label for="address" class="form-label">Address</label> {{-- Added form-label class --}}
                                                    <textarea id="address" name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                                                    @error('address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="city_id" class="form-label">City</label> {{-- Added form-label class --}}
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select id="city_id" name="city_id" class="form-select" required> {{-- Changed form-control to form-select for Bootstrap 5 select --}}
                                                    <option value="">Select City</option>
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->id }}"
                                                                {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                            {{ $city->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('city_id')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="state" class="form-label">State</label> {{-- Added form-label class --}}
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" id="state" name="state" class="form-control"
                                                       value="{{ old('state') }}">
                                                @error('state')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="country_id" class="form-label">Country</label> {{-- Added form-label class --}}
                                            </div>
                    
                                            <div class="col-md-3 mb-3">
                                                <select id="country_id" name="country_id" class="form-select" required> {{-- Changed form-control to form-select for Bootstrap 5 select --}}
                                                    <option value="">Select Country</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}"
                                                                {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('country_id')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="postal_code" class="form-label">Postal Code</label> {{-- Added form-label class --}}
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" id="postal_code" name="postal_code"
                                                       class="form-control" value="{{ old('postal_code') }}">
                                                @error('postal_code')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="tin" class="form-label">TIN</label> {{-- Added form-label class --}}
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" id="tin" name="tin" class="form-control"
                                                       value="{{ old('tin') }}" required>
                                                @error('tin')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="vrn" class="form-label">VRN</label> {{-- Added form-label class --}}
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" id="vrn" name="vrn" class="form-control"
                                                       value="{{ old('vrn') }}" required>
                                                @error('vrn')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                    
                                            <div class="col-md-3">
                                                <label for="status" class="form-label">Status</label> {{-- Added form-label class --}}
                                            </div>
                                            <div class="col-md-3 mb-3"> {{-- Added mb-3 for consistent spacing --}}
                                                <select id="status" name="status" class="form-select" required> {{-- Changed form-control to form-select for Bootstrap 5 select --}}
                                                    <option value="active"
                                                            {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                                    </option>
                                                    <option value="inactive"
                                                            {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                                @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button> {{-- Changed data-dismiss to data-bs-dismiss --}}
                                        <button type="submit" class="btn btn-primary">Save Vendor</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Get all edit buttons
                const editButtons = document.querySelectorAll(".edit-vendor-btn");

                editButtons.forEach(button => {
                    button.addEventListener("click", function() {
                        // Get vendor data from the button's data attribute
                        const vendor = JSON.parse(this.getAttribute("data-vendor"));

                        // Populate modal fields
                        document.getElementById("editVendorForm").setAttribute("action",
                            `/vendors/${vendor.id}`);
                        document.getElementById("name").value = vendor.name;
                        document.getElementById("email").value = vendor.email;
                        document.getElementById("phone").value = vendor.phone;
                        document.getElementById("contact_person").value = vendor.contact_person;
                        document.getElementById("address").value = vendor.address;
                        document.getElementById("city_id").value = vendor.city_id;
                        document.getElementById("country_id").value = vendor.country_id;
                        document.getElementById("postal_code").value = vendor.postal_code || '';
                        document.getElementById("tin").value = vendor.tin;
                        document.getElementById("vrn").value = vendor.vrn;
                        document.getElementById("status").value = vendor.status;

                        // Add state if available
                        if (vendor.state) {
                            document.getElementById("state").value = vendor.state;
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
