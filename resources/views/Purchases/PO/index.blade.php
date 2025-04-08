@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Purchase Orders (PO)') }}</h1>
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
                        <a href="{{ route('create_po') }}" class="btn btn-secondary btn-sm"> Add New PO</a>
                    </div>
                    <table class="table" id="dataTable">
                        <thead>
                            <tr>
                                <th class="pl-4">#</th>
                                <th>PO No</th>
                                <th>PO Date</th>
                                <th>Vendor</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Sub Total</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th class="d-none">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchase_orders as $key => $purchase_order)
                            <tr>
                                <td class="pl-4">{{ $key + 1 }}</td>
                                <td>{{ $purchase_order->order_number }}</td>
                                <td>{{ $purchase_order->order_date }}</td>
                                <td>{{ $purchase_order->supplier->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                    $totalDiscount = $purchase_order->purchaseOrderItems->sum(function ($item) {
                                        return $item->discount ?? 0;
                                    });
                                    @endphp
                                    {{ format_currency($totalDiscount) }}  
                                </td>
                                <td>
                                    @php
                                    $totalTax = $purchase_order->purchaseOrderItems->sum(function ($item) {
                                        if ($item->tax && $item->tax->tax_mode === 'percentage') {
                                            $taxPercentage = $item->tax->tax_percentage ?? 0;
                                            return ($item->quantity * $item->unit_price) * ($taxPercentage / 100);
                                        } elseif ($item->tax && $item->tax->tax_mode === 'amount') {
                                            return $item->tax->tax_amount ?? 0;
                                        }
                                        return 0; 
                                    });
                                    @endphp
                                    {{ format_currency($totalTax) }}  
                                </td>
                                <td>
                                    @php
                                    $subTotal = $purchase_order->purchaseOrderItems->sum(function ($item) {
                                        return $item->quantity * $item->unit_price;
                                    });
                                    @endphp
                                    {{ format_currency($subTotal) }}  {{-- Use format_currency here --}}
                                </td>
                                <td>
                                    @php
                                    // Calculate total amount
                                    $totalAmount = $subTotal - $totalDiscount + $totalTax;
                                    @endphp
                                    {{ format_currency($totalAmount) }}  {{-- Use format_currency here --}}
                                </td>
                                <td>
                                    @php
                                        $status = ucfirst($purchase_order->status);
                                        $badgeClass = '';
                        
                                        switch ($purchase_order->status) {
                                            case 'Pending':
                                                $badgeClass = 'badge-warning';
                                                break;
                                            case 'Approved':
                                                $badgeClass = 'badge-success';
                                                break;
                                            case 'Rejected':
                                                $badgeClass = 'badge-danger';
                                                break;
                                            case 'Partially_received':
                                                $badgeClass = 'badge-info';
                                                break;
                                            case 'Cancelled':
                                                $badgeClass = 'badge-dark';
                                                break;
                                            case 'Completed':
                                                $badgeClass = 'badge-secondary';
                                                break;
                                            default:
                                                $badgeClass = 'badge-light';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                </td>
                                <td class="d-none">
                                    <a href="{{ route('preview_po', $purchase_order->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No purchase orders available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
        
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataTable = document.getElementById('dataTable');
            const tableBody = dataTable.querySelector('tbody');
            const rows = tableBody.querySelectorAll('tr');
    
            rows.forEach(row => {
                row.addEventListener('click', function(event) {
                    if (event.target.closest('td:last-child a')) {
                        return;
                    }
    
                    const previewLink = row.querySelector('td:last-child a');
                    if (previewLink) {
                        const previewUrl = previewLink.getAttribute('href');
                        if (previewUrl) {
                            window.location.href = previewUrl;
                        }
                    }
                });
            });
        });
    </script>

@endsection
