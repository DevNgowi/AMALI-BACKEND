@extends('layouts.app')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Customers') }}</h1>
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
                        @can('can create customer')
                            <a href="{{ route('create_customer') }}" class="btn btn-primary btn-sm text-bold">
                                <i class="fas fa-plus"></i> Add Customer
                            </a>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>City</th>
                                    <th>Mobile</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $key => $customer)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $customer->customer_code }}</td>
                                        <td>{{ $customer->customer_name }}</td>
                                        <td>{{ $customer->customerType->name }}</td>
                                        <td>{{ $customer->city->name }}</td>
                                        <td>{{ $customer->mobile_sms_no }}</td>
                                        <td>{{ $customer->opening_balance }}</td>
                                        <td>
                                            <span class="badge {{ $customer->active == 1 ? 'badge-success' : 'badge-danger' }}">
                                                {{ $customer->active == 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('can edit customer')
                                                <a class="pr-3" href="{{ route('edit_customer', $customer->id) }}">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('can delete customer')
                                                <button type="button" class="btn btn-sm bg-none" data-bs-toggle="modal"
                                                    data-bs-target="#confirmationModal"
                                                    onclick="showConfirmationModal('{{ route('delete_customer', $customer->id) }}', '{{ __('Are you sure you want to delete this expense?') }}')">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
