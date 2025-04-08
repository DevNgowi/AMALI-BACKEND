@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="content-header">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Good Receive Note Preview') }}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('list_grn') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Good Receive Note Details</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">GRN Number: {{ $good_receipt_note->grn_number }}</span>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <input type="hidden" id="good-receive-note-id" value="{{ $good_receipt_note->id }}">

                    <div class="col-md-6">
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Supplier</div>
                            <div class="col-md-8">
                                {{ $good_receipt_note->supplier->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">PO Reference</div>
                            <div class="col-md-8">
                                {{ $good_receipt_note->purchaseOrder->order_number ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">GRN Date</div>
                            <div class="col-md-8">
                                {{ $good_receipt_note->received_date }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Delivery Note No</div>
                            <div class="col-md-8">
                                {{ $good_receipt_note->delivery_note_number }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Status</div>
                            <div class="col-md-8">
                                {{ ucfirst($good_receipt_note->status) }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Remarks</div>
                            <div class="col-md-8">
                                {{ $good_receipt_note->remarks ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Total Items</th>
                                <td class="text-right">
                                    {{ $good_receipt_note->goodReceiveNoteItems->count() }} {{-- Count GRN items --}}
                                </td>
                            </tr>
                            <tr>
                                <th>Total Accepted Qty</th>
                                <td class="text-right">
                                    {{ $good_receipt_note->goodReceiveNoteItems->sum('accepted_quantity') }} {{-- Sum accepted quantities --}}
                                </td>
                            </tr>
                            <tr>
                                <th>Total Rejected Qty</th>
                                <td class="text-right">
                                    {{ $good_receipt_note->goodReceiveNoteItems->sum('rejected_quantity') }} {{-- Sum rejected quantities --}}
                                </td>
                            </tr>
                            <tr class="table-active">
                                <th>Grand Total Amount</th>
                                <td class="text-right font-weight-bold">
                                    {{ number_format($grandTotalAmount, 2) }}  
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h4>Received Items</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Item Name</th>
                                    <th>UOM</th>
                                    <th>Ordered Qty</th>
                                    <th>Received Qty</th>
                                    <th>Accepted Qty</th>
                                    <th>Rejected Qty</th>
                                    <th>Unit Price</th>
                                    <th>Received Condition</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($good_receipt_note->goodReceiveNoteItems as $key => $grnItem)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $grnItem->item->name }}</td>
                                        <td>{{ $grnItem->item->units->first()->name ?? 'N/A' }}</td> 
                                        <td>{{ number_format($grnItem->ordered_quantity, 2) }}</td> 
                                        <td>{{ number_format($grnItem->received_quantity, 2) }}</td>
                                        <td>{{ number_format($grnItem->accepted_quantity, 2) }}</td>
                                        <td>{{ number_format($grnItem->rejected_quantity, 2) }}</td>
                                        <td>{{ number_format($grnItem->unit_price, 2) }}</td>
                                        <td>{{ ucfirst($grnItem->received_condition) }}</td>
                                        <td class="font-weight-bold">
                                            {{ number_format($grnItem->received_quantity * $grnItem->unit_price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h4>Extra Charges</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Charge Name</th>
                                    <th>UOM</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($good_receipt_note->goodReceiveNoteExtraCharges->isNotEmpty())
                                    @foreach ($good_receipt_note->goodReceiveNoteExtraCharges as $key => $extraCharge)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $extraCharge->extraCharge->name }}</td>
                                            <td>{{ $extraCharge->unit->name }}</td>
                                            <td>{{ number_format($extraCharge->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No extra charges added.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="btn-group" role="group">
                            @php
                                $grnId = $good_receipt_note->id;
                                $grnStatus = $good_receipt_note->status; // Get GRN status
                                if($grnStatus === 'Pending'){
                
                            @endphp
                                @can('verify GRN')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-info" style="border: 1px solid gray" onclick="verifyGRN({{ $grnId }})">
                                        <i class="fas fa-clipboard-check mr-2"></i>Verify GRN
                                    </button>
                                @endcan
                
                                @can('reject GRN')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-danger" style="border: 1px solid gray" onclick="rejectGRN({{ $grnId }})">
                                        <i class="fas fa-times mr-2"></i>Reject GRN
                                    </button>
                                @endcan
                            @php
                                } elseif($grnStatus === 'Inspected' || $grnStatus === 'Verified'){ // Assuming 'Inspected' or 'Verified' status means ready to accept
                            @endphp
                                @can('accept GRN')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-success" style="border: 1px solid gray" onclick="acceptGRN({{ $grnId }})">
                                        <i class="fas fa-check mr-2"></i>Accept GRN
                                    </button>
                                @endcan
                                @can('reject GRN')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-danger" style="border: 1px solid gray" onclick="rejectGRN({{ $grnId }})">
                                        <i class="fas fa-times mr-2"></i>Reject GRN
                                    </button>
                                @endcan
                            @php
                                } elseif($grnStatus === 'Accepted'){
                            @endphp
                                @can('complete GRN')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-secondary" style="border: 1px solid gray" onclick="completeGRN({{ $grnId }})">
                                        <i class="fas fa-check-double mr-2"></i>Complete GRN
                                    </button>
                                @endcan
                            @php
                                } elseif($grnStatus === 'Rejected'){
                            @endphp
                                @can('reopen GRN')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-warning" style="border: 1px solid gray" onclick="reopenGRN({{ $grnId }})">
                                        <i class="fas fa-undo mr-2"></i>Reopen GRN
                                    </button>
                                @endcan
                            @php
                                } elseif($grnStatus === 'Cancelled' || $grnStatus === 'Completed'){ 
                            @endphp
                                {{-- No action buttons if GRN is Completed or Cancelled --}}
                            @php
                                } else { 
                            @endphp
                                {{-- Default buttons or no buttons if status is unexpected --}}
                                @can('cancel GRN')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-danger" style="border: 1px solid gray" onclick="cancelGRN({{ $grnId }})">
                                        <i class="fas fa-ban mr-2"></i>Cancel GRN
                                    </button>
                                @endcan
                            @php
                                }
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection