@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Goods Receive Note (GRN)') }}</h1>
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
                        <a href="{{ route('create_grn') }}" class="btn btn-secondary btn-sm"> Add New GRN</a>
                    </div>
                    <table class="table" id="dataTable">
                        <thead>
                            <tr>
                                <th class="pl-4">#</th>
                                <th>GRN No</th>
                                <th>GRN Date</th>
                                <th>Delivery Note No</th>
                                <th>Vendor</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($good_receipt_notes as $key => $grn)
                                <tr>
                                    <td class="pl-4">{{ $key + 1 }}</td>
                                    <td>{{ $grn->grn_number }}</td>
                                    <td>{{ $grn->received_date }}</td>
                                    <td>{{ $grn->delivery_note_number }}</td>
                                    <td>{{ $grn->supplier->name ?? 'N/A' }}</td>

                                    <td> {{ number_format($grn->grand_total_amount, 2) }} </td> {{-- Display grand_total_amount here --}}
                                    <td>
                                        @php
                                            $status = ucfirst($grn->status);
                                            $badgeClass = '';
                                    
                                            switch ($grn->status) {
                                                case 'Pending':
                                                    $badgeClass = 'badge-warning';
                                                    break;
                                                case 'Inspected':
                                                case 'Verified':
                                                    $badgeClass = 'badge-info';
                                                    break;
                                                case 'Accepted':
                                                    $badgeClass = 'badge-success';
                                                    break;
                                                case 'Rejected':
                                                    $badgeClass = 'badge-danger';
                                                    break;
                                                case 'Completed':
                                                    $badgeClass = 'badge-secondary';
                                                    break;
                                                case 'Cancelled':
                                                    $badgeClass = 'badge-dark';
                                                    break;
                                                case 'Reopened':
                                                    $badgeClass = 'badge-primary'; 
                                                    break;
                                                default:
                                                    $badgeClass = 'badge-success'; 
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('preview_grn', $grn->id) }}" >
                                            <i class="fas fa-eye"></i> 
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No Good Receive Notes available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
@endsection
