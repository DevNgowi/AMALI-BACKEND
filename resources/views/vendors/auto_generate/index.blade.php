@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Edit Vendors') }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('list_vendors') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
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

                    <div class="card">
                        <div class="card-body">
                            <form id="editVendorForm" method="POST" action="{{ route('update_vendor', $vendor->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-md-3">
                                        <label for="name">Name</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" id="name" name="name" class="form-control"
                                            value="{{ old('name', $vendor->name) }}" required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-3">
                                        <label for="email">Email</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="email" id="email" name="email" class="form-control"
                                            value="{{ old('email', $vendor->email) }}" required>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-3">
                                        <label for="phone">Phone</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" id="phone" name="phone" class="form-control"
                                            value="{{ old('phone', $vendor->phone) }}" required>
                                        @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Contact Person -->
                                    <div class="col-md-3">
                                        <label for="contact_person">Contact Person</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" id="contact_person" name="contact_person"
                                            class="form-control"
                                            value="{{ old('contact_person', $vendor->contact_person) }}" required>
                                        @error('contact_person')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-12">
                                        <div class="mb-3 ">
                                            <label for="address">Address</label>
                                            <textarea id="address" name="address" class="form-control" rows="3" required>{{ old('address', $vendor->address) }}</textarea>
                                            @error('address')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- City -->
                                    <div class="col-md-3">
                                        <label for="city_id">City</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select id="city_id" name="city_id" class="form-control" required>
                                            <option value="">Select City</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}"
                                                    {{ $city->id == $vendor->city_id ? 'selected' : '' }}>
                                                    {{ $city->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-3">
                                        <label for="state">State</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" id="state" name="state" class="form-control"
                                            value="{{ old('state', $vendor->state) }}">
                                        @error('state')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Country -->
                                    <div class="col-md-3">
                                        <label for="country_id">Country</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select id="country_id" name="country_id" class="form-control" required>
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ $country->id == $vendor->country_id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Postal Code -->
                                    <div class="col-md-3">
                                        <label for="postal_code">Postal Code</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" id="postal_code" name="postal_code" class="form-control"
                                            value="{{ old('postal_code', $vendor->postal_code) }}">
                                        @error('postal_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- TIN -->
                                    <div class="col-md-3">
                                        <label for="tin">TIN</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" id="tin" name="tin" class="form-control"
                                            value="{{ old('tin', $vendor->tin) }}" required>
                                        @error('tin')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- VRN -->
                                    <div class="col-md-3">
                                        <label for="vrn">VRN</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" id="vrn" name="vrn" class="form-control"
                                            value="{{ old('vrn', $vendor->vrn) }}" required>
                                        @error('vrn')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-3">
                                        <label for="status">Status</label>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="status" name="status" class="form-control" required>
                                            <option value="active"
                                                {{ $vendor->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive"
                                                {{ $vendor->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
