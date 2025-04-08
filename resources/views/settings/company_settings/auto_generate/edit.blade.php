@extends('layouts.app')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Edit Company') }}</h1>
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
                    <form action="{{ route('update_company_details', $company->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Company Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Company Name -->
                                    <div class="col-md-3">
                                        <label for="company_name" class="text-muted">Company Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $company->company_name) }}">
                                        @error('company_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                    
                                    <!-- Country -->
                                    <div class="col-md-3">
                                        <label for="country_name" class="text-muted">Country <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="country_id" id="country_name" class="form-control">
                                            <option value="" disabled>Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" {{ old('country_id', $company->country_id) == $country->id ? 'selected' : '' }}>
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
                                        <label for="email" class="text-muted">Email <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $company->email) }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                    
                                    <!-- State -->
                                    <div class="col-md-3">
                                        <label for="state" class="text-muted">State</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="state" id="state" class="form-control" value="{{ old('state', $company->state) }}">
                                    </div>
                    
                                    <!-- Phone -->
                                    <div class="col-md-3">
                                        <label for="phone" class="text-muted">Phone</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
                                    </div>
                    
                                    <!-- Post Code -->
                                    <div class="col-md-3">
                                        <label for="post_code" class="text-muted">Post Code</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="post_code" id="post_code" class="form-control" value="{{ old('post_code', $company->post_code) }}">
                                    </div>
                    
                                    <!-- VRN Number -->
                                    <div class="col-md-3">
                                        <label for="vrn_no" class="text-muted">VRN Number</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="vrn_no" id="vrn_no" class="form-control" value="{{ old('vrn_no', $company->vrn_no) }}">
                                    </div>
                    
                                    <!-- TIN Number -->
                                    <div class="col-md-3">
                                        <label for="tin_no" class="text-muted">TIN Number</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="text" name="tin_no" id="tin_no" class="form-control" value="{{ old('tin_no', $company->tin_no) }}">
                                    </div>
                    
                                    <!-- Address -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address" class="text-muted">Address</label>
                                            <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $company->address) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="website" class="text-muted">Website</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="url" name="website" id="website" class="form-control" value="{{ old('website', $company->website) }}" placeholder="https://example.com">
                                        @error('website')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                    
                                    <hr>
                    
                                    <!-- Financial Year From -->
                                    <div class="col-md-3">
                                        <label for="financial_year_from" class="text-muted">Financial Year From <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="date" class="form-control" name="financial_year_from" value="{{ old('financial_year_from', $company->fiscalYears->first()->financial_year_from ? \Carbon\Carbon::parse($company->fiscalYears->first()->financial_year_from)->format('Y-m-d') : '') }}">
                                        @error('financial_year_from')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                    
                                    <!-- Financial Year To -->
                                    <div class="col-md-3">
                                        <label for="financial_year_to" class="text-muted">Financial Year To <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="date" class="form-control" name="financial_year_to" value="{{ old('financial_year_to', $company->fiscalYears->first()->financial_year_to ? \Carbon\Carbon::parse($company->fiscalYears->first()->financial_year_to)->format('Y-m-d') : '') }}">
                                        @error('financial_year_to')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                    
                                    <!-- Is Active -->
                                    <div class="col-md-3">
                                        <label for="is_active" class="text-muted">Is Active? <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="is_active" class="form-control" id="is_active">
                                            <option value="1" {{ old('is_active', $company->is_active) ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ old('is_active', !$company->is_active) ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <!-- Upload Logo -->
                                    <div class="col-md-3">
                                        <label for="company_logo" class="text-muted">Change Logo</label>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input type="file" name="company_logo" id="company_logo" class="form-control" accept="image/*">
                                        @error('company_logo')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                    
                                    <!-- Description -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="description" class="text-muted">Description</label>
                                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $company->description) }}</textarea>
                                        </div>
                                    </div>
                    
                                    <!-- Company Logo -->
                                    <div class="col-md-3">
                                        <label for="company_logo" class="text-muted">Company Logo</label>
                                    </div>
                                    <div class="col-md-3">
                                        @if ($company->company_logo)
                                            <img src="{{ asset('storage/' . $company->company_logo) }}" alt="Company Logo" style="max-width: 200px; max-height: 200px; border: 1px solid gray;" />
                                        @else
                                            <span>No logo uploaded</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                    
                            <!-- Form Footer -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Company</button>
                                <a href="{{ route('list_company_details') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
