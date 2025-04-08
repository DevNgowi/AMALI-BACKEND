@extends('layouts.app')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Update Customers') }}</h1>
                </div>
                <div class="col-sm-6">
                    <div class="add_btn justify-content-end pb-2 d-flex">
                        @can('can view customer')
                            <a href="{{ route('list_customer') }}" class="btn btn-secondary btn-sm text-bold">
                                <i class="fas fa-right-arrow"></i> Back to list
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('update_customer', $customers->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Customer Code:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="customer_code"
                                                       value="{{ $customers->customer_code ?? old('customer_code') }}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Customer Name  <span class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" name="customer_name"
                                                    value="{{ $customers->customer_name ?? old('customer_name') }}" required>
                                                @error('customer_name')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Customer Type:</label>
                                            <div class="col-sm-8">
                                                <select class="form-control @error('customer_type_id') is-invalid @enderror" name="customer_type_id">
                                                    <option value="">Select Customer Type</option>
                                                    @foreach ($customer_types as $customer_type)
                                                        <option value="{{ $customer_type->id }}" 
                                                            {{ (isset($customers->customer_type_id) && $customers->customer_type_id == $customer_type->id) 
                                                            || old('customer_type_id') == $customer_type->id ? 'selected' : '' }}>
                                                            {{ $customer_type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('customer_type_id')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Opening Balance:</label>
                                            <div class="col-sm-8 input-group">
                                                <input type="number" class="form-control @error('opening_balance') is-invalid @enderror" name="opening_balance"
                                                    value="{{ $customers->opening_balance ?? old('opening_balance', '0.00') }}">
                                                <select class="form-control @error('dr_cr') is-invalid @enderror" name="dr_cr">
                                                    <option value="Dr" {{ old('dr_cr', $customers->opening_balance_type) == 'Dr' ? 'selected' : '' }}>Dr</option>
                                                    <option value="Cr" {{ old('dr_cr', $customers->opening_balance_type) == 'Cr' ? 'selected' : '' }}>Cr</option>
                                                </select>
                                                @error('opening_balance')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                @error('dr_cr')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Contact Details:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control mb-2 @error('mobile_sms_no') is-invalid @enderror" name="mobile_sms_no"
                                                    placeholder="Mobile Number" value="{{ $customers->mobile_sms_no ?? old('mobile_sms_no') }}">
                                                @error('mobile_sms_no')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email"
                                                    value="{{ $customers->email ?? old('email') }}">
                                                @error('email')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Address:</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="2">{{ $customers->address ?? old('address') }}</textarea>
                                                @error('address')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">City:</label>
                                            <div class="col-sm-8">
                                                <select name="city_id" class="form-control @error('city_id') is-invalid @enderror" id="city_id">
                                                    <option value="" selected>---Select City---</option>
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->id }}" 
                                                            {{ (isset($customers->city_id) && $customers->city_id == $city->id) || old('city_id') == $city->id ? 'selected' : '' }}>
                                                            {{ $city->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('city_id')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Credit Control:</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control mb-2 @error('credit_limit') is-invalid @enderror" name="credit_limit"
                                                    placeholder="Credit Limit" value="{{ $customers->credit_limit ?? old('credit_limit') }}">
                                                @error('credit_limit')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <input type="number" class="form-control @error('credit_period') is-invalid @enderror" name="credit_period"
                                                    placeholder="Credit Period (days)" value="{{ $customers->credit_period ?? old('credit_period') }}">
                                                @error('credit_period')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Account Settings:</label>
                                            <div class="col-sm-8">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ old('active', $customers->active) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="active">Active Account</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mt-2">
                                                    <input type="checkbox" class="custom-control-input" id="account_ledger_creation"
                                                        name="account_ledger_creation" value="1" {{ old('account_ledger_creation', $customers->account_ledger_creation) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="account_ledger_creation">Create Ledger Account</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                    <button type="button" class="btn btn-secondary">{{ __('Cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // No bsCustomFileInput.init() needed here as there's no file input
        });
    </script>
@endsection
