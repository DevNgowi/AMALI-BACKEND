@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Company Registration') }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('list_company_details') }}" class="btn btn-secondary">
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

                    <form action="{{ route('store_company_details') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Company Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Company Name -->
                                    <div class="col-md-3">
                                        <label for="company_name" class="text-muted">Company Name <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="company_name" id="company_name" class="form-control"
                                            value="{{ old('company_name') }}">
                                        @error('company_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Country -->
                                    <div class="col-md-3">
                                        <label for="country_id" class="text-muted">Country <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="country_id" id="country_id" class="form-control">
                                            <option value="" disabled selected>Select Country</option>
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

                                    <!-- Email -->
                                    <div class="col-md-3">
                                        <label for="email" class="text-muted">Email <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="email" name="email" id="email" class="form-control"
                                            value="{{ old('email') }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-3">
                                        <label for="state" class="text-muted">State</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="state" id="state" class="form-control"
                                            value="{{ old('state') }}">
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-3">
                                        <label for="phone" class="text-muted">Phone</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="tel" name="phone" id="phone" class="form-control"
                                            value="{{ old('phone') }}">
                                    </div>

                                    <!-- Post Code -->
                                    <div class="col-md-3">
                                        <label for="post_code" class="text-muted">Post Code</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="post_code" id="post_code" class="form-control"
                                            value="{{ old('post_code') }}">
                                    </div>

                                    <!-- vrn Number -->
                                    <div class="col-md-3">
                                        <label for="phone" class="text-muted">VRN Number</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="vrn_no" id="vrn_no" class="form-control"
                                            value="{{ old('vrn_no') }}">
                                    </div>

                                    <!-- Tin Number -->
                                    <div class="col-md-3">
                                        <label for="tin_no" class="text-muted">TIN Number</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="tin_no" id="tin_no" class="form-control"
                                            value="{{ old('tin_no') }}">
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address" class="text-muted">Address</label>
                                            <textarea name="address" id="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="website" class="text-muted">Website</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="url" name="website" id="website" class="form-control"
                                            value="{{ old('website') }}" placeholder="https://example.com">
                                        @error('website')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <hr>

                                    <!-- Financial Year From -->
                                    <div class="col-md-3">
                                        <label for="financial_year_from" class="text-muted">Financial Year From <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="date" name="financial_year_from" id="financial_year_from"
                                            class="form-control" value="{{ old('financial_year_from') }}">
                                        @error('financial_year_from')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <!-- Financial Year To -->
                                    <div class="col-md-3">
                                        <label for="financial_year_to" class="text-muted">Financial Year To <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="date" name="financial_year_to" id="financial_year_to"
                                            class="form-control" value="{{ old('financial_year_to') }}">
                                        @error('financial_year_to')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- is active --}}
                                    <div class="col-md-3">
                                        <label for="financial_year_from" class="text-muted">Is Active? <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="is_active" class="form-control" id="is_active">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>

                                    {{-- Update company logo field --}}
                                    <div class="col-md-3">
                                        <label for="company_logo" class="text-muted">Company Logo</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="file" class="form-control" name="company_logo"
                                            accept="image/jpeg,image/png,image/jpg">
                                        @error('company_logo')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Description -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description" class="text-muted">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <!-- Form Footer -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save Company</button>
                                <a href="{{ route('list_company_details') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
