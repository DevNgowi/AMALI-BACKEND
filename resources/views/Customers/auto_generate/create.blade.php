@extends('layouts.app')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Customers') }}</h1>
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
                            <form action="{{ route('store_customer') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Customer Code:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="customer_code" 
                                                       value="{{ $customer_code }}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Customer Name  <span class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="customer_name"
                                                    value="{{ old('customer_name') }}" required>
                                            </div>
                                        </div>
            
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Customer Type:</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="customer_type_id">
                                                    <option value="">Select Customer</option>
                                                    @foreach ($customer_types as $customer_type)
                                                    <option value="{{ $customer_type->id }}">{{ $customer_type->name }}</option>
                                                    @endforeach
                                                </select> 
                                            </div>
                                        </div>
            
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Opening Balance:</label>
                                            <div class="col-sm-8 input-group">
                                                <input type="number" class="form-control" name="opening_balance"
                                                    value="{{ old('opening_balance', '0.00') }}">
                                                <select class="form-control" name="dr_cr">
                                                    <option value="Dr">Dr</option>
                                                    <option value="Cr">Cr</option>
                                                </select>
                                            </div>
                                        </div>
            
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Contact Details:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control mb-2" name="mobile_sms_no"
                                                    placeholder="Mobile Number" value="{{ old('mobile_sms_no') }}">
                                                <input type="email" class="form-control" name="email" placeholder="Email"
                                                    value="{{ old('email') }}">
                                            </div>
                                        </div>
                                    </div>
            
                                    <!-- Business & Location Details -->
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Address:</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" name="address" rows="2">{{ old('address') }}</textarea>
                                            </div>
                                        </div>
            
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">City:</label>
                                            <div class="col-sm-8">
                                                <select name="city_id" class="form-control" id="city_id">
                                                    <option value="" selected>---Select City---</option>
                                                    @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
            
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Credit Control:</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control mb-2" name="credit_limit"
                                                    placeholder="Credit Limit" value="{{ old('credit_limit') }}">
                                                <input type="number" class="form-control" name="credit_period"
                                                    placeholder="Credit Period (days)" value="{{ old('credit_period') }}">
                                            </div>
                                        </div>
            
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Account Settings:</label>
                                            <div class="col-sm-8">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="active" name="active"
                                                        value="1" checked>
                                                    <label class="custom-control-label" for="active">Active Account</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mt-2">
                                                    <input type="checkbox" class="custom-control-input" id="account_ledger_creation"
                                                        name="account_ledger_creation" value="1" checked>
                                                    <label class="custom-control-label" for="account_ledger_creation">Create Ledger
                                                        Account</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
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
            bsCustomFileInput.init(); // Enable bs-custom-file-input for file browse
        });
    </script>
@endsection