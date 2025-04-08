@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Company Details') }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    @can('can create company details')
                        <a class="btn btn-primary btn-sm" href="{{ route('create_company_details') }}">
                            Add Company Detail
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <!-- Main content -->
    @include('message')
    <div class="content">
        <div class="container-fluid mb-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="dataTable" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Company Name</th>
                                    <th>Country</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>TIN</th>
                                    <th>Financial Year</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($company_details as $key => $company)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $company->company_name }}</td>
                                        <td>{{ $company->country->name }}</td>
                                        <td>{{ $company->email }}</td>
                                        <td>{{ $company->phone ?? $company->mobile_number }}</td>
                                        <td>{{ $company->tin_no ?? 'N/A' }}</td>
                                        <td>
                                            @if ($company->fiscalYears->isNotEmpty())
                                                @foreach ($company->fiscalYears as $year)
                                                    {{ \Carbon\Carbon::parse($year->financial_year_from)->format('M Y') }} -
                                                    {{ \Carbon\Carbon::parse($year->financial_year_to)->format('M Y') }}<br>
                                                @endforeach
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @can('can edit company details')
                                                <a href="{{ route('edit_company_details', $company->id) }}"
                                                    class="text-secondary mr-3">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('can delete company details')
                                                <a href="javascript:void(0);" class="text-danger"
                                                    onclick="showConfirmationModal(
                                                '{{ route('delete_company_details', $company->id) }}',
                                                'Are you sure you want to delete {{ addslashes($company->company_name) }}?',
                                                'Unable to delete {{ addslashes($company->company_name) }}.',
                                                '{{ addslashes($company->company_name) }}'
                                            )">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No companies available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
