@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="content-header">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Purchase Order Preview') }}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('list_po') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Purchase Order Details</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">PO Number: {{ $purchase_orders['order_number'] }}</span>
                </div>
            </div>


            <div class="card-body">
                <div class="row">
                    <input type="hidden" id="purchase-order-id" value="{{ $purchase_orders['id'] }}">

                    <div class="col-md-6">
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Supplier </div>
                            <div class="col-md-8">
                                {{ $purchase_orders['supplier']['name'] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Order Date</div>
                            <div class="col-md-8">
                                {{ \Carbon\Carbon::parse($purchase_orders['order_date'])->format('d M Y') }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Expected Delivery Date</div>
                            <div class="col-md-8">
                                {{ \Carbon\Carbon::parse($purchase_orders['expected_delivery_date'])->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Sub Total</th>
                                <td class="text-right">
                                    {{ number_format($purchase_orders['subtotal'], 2) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Total Tax</th>
                                <td class="text-right">
                                    {{ number_format($purchase_orders['tax'], 2) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Total Discount</th>
                                <td class="text-right">
                                    {{ number_format($purchase_orders['total_discount'], 2) }}
                                </td>
                            </tr>
                            <tr class="table-active">
                                <th>Grand Total</th>
                                <td class="text-right font-weight-bold">
                                    {{ number_format($purchase_orders['total'], 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h4>Order Items</h4>
                        <table class="table table-bordered table-striped" id="items-table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Item Name</th>
                                    <th>UOM</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Discount</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase_orders['purchase_order_items'] as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item['item']['name'] }}</td>
                                        <td>{{ $item['unit']['name'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ number_format($item['unit_price'], 2) }}</td>
                                        <td>{{ number_format($item['discount'], 2) }}</td>
                                        <td>
                                            @if ($item['tax'])
                                                @php
                                                    $taxAmount = $item['tax']['tax_mode'] === 'percentage'
                                                        ? ($item['unit_price'] * $item['quantity'] * ($item['tax']['tax_percentage'] / 100))
                                                        : $item['tax']['tax_amount'];
                                                @endphp
                                                {{ number_format($taxAmount, 2) }}
                                                ({{ $item['tax']['tax_mode'] === 'percentage' ? $item['tax']['tax_percentage'] . '%' : 'Fixed' }})
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="font-weight-bold calculate-total">
                                            @php
                                                $itemTotal = ($item['unit_price'] * $item['quantity']) - $item['discount'];
                                                $taxAmount = 0;
                                                if ($item['tax']) {
                                                    if ($item['tax']['tax_mode'] === 'percentage') {
                                                        $taxAmount = $itemTotal * ($item['tax']['tax_percentage'] / 100);
                                                    } else {
                                                        $taxAmount = $item['tax']['tax_amount'];
                                                    }
                                                }
                                                $itemTotal += $taxAmount;
                                            @endphp
                                            {{ number_format($itemTotal, 2) }}
                                        </td>
                                        <td>
                                            @if ($purchase_orders['status'] !== 'Completed') {{-- Conditionally hide actions if PO is Completed --}}
                                                <a href="#"  onclick="showEditModal({{ json_encode($item) }})">
                                                    <i class="fas fa-pen text-secondary"></i>
                                                </a>
                                                <a href="#" data-toggle="modal" data-target="#confirmationModal"
                                                   onclick="showConfirmationModal('{{ route('delete_po_item', $item['id']) }}', 'Are you sure you want to delete this PO item?')">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($purchase_orders['status'] !== 'Completed') {{-- Conditionally hide "Add Item" row if PO is Completed --}}
                                    <tr id="add-item-row" style="display:none;">
                                        <td></td>
                                        <td>
                                            <select name="items[0][product_id]" id="addItemProductId" class="form-select product-select" onchange="updateItemDetails(this); calculateNewItemTotal()" required>
                                                <option value="" disabled selected>Select Product</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}"
                                                        data-default-unit="{{ $item->itemUnits->first()->unit_id ?? '' }}"
                                                        data-default-cost="{{ $item->itemCosts->first()->amount ?? 0 }}">
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger item-error item-product_id-error"></div>
                                        </td>
                                        <td>
                                            <select name="items[0][unit_id]" id="addItemUnitId" class="form-select unit-select" onchange="calculateNewItemTotal()" required>
                                                <option value="" disabled selected>Select UOM</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">
                                                        {{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger item-error item-unit_id-error"></div>
                                        </td>
                                        <td><input type="number" name="items[0][quantity]" id="addItemQuantity" class="form-control quantity-input" min="1" value="{{ old('items.0.quantity') }}" oninput="calculateNewItemTotal()" required>
                                            <div class="text-danger item-error item-quantity-error"></div>
                                        </td>
                                        <td><input type="number" name="items[0][unit_price]" id="addItemUnitPrice" class="form-control price-input" step="0.01" value="{{ old('items.0.unit_price') }}" oninput="calculateNewItemTotal()" required>
                                            <div class="text-danger item-error item-unit_price-error"></div>
                                        </td>
                                        <td><input type="number" name="items[0][discount]" id="addItemDiscount" class="form-control discount-input" step="0.01" value="{{ old('items.0.discount') }}" oninput="calculateNewItemTotal()">
                                            <div class="text-danger item-error item-discount-error"></div>
                                        </td>
                                        <td>
                                            <select name="items[0][tax_id]" id="addItemTaxId" class="form-select tax-select" onchange="calculateNewItemTotal()" required>
                                                <option value="" disabled selected>Select Tax</option>
                                                <option value="">None</option>
                                                @foreach ($taxes as $tax)
                                                    <option value="{{ $tax->id }}"
                                                        data-tax-mode="{{ $tax->tax_mode }}"
                                                        data-tax-value="{{ $tax->tax_mode === 'percentage' ? $tax->tax_percentage : $tax->tax_amount }}">
                                                        {{ $tax->name }}
                                                        {{-- @if ($tax->tax_mode == 'percentage')
                                                            ({{ $tax->tax_type }} - {{ number_format($tax->tax_percentage', 0) }}%)
                                                        @else
                                                            ({{ $tax->tax_type }} - {{ number_format($tax->tax_amount, 0) }} )
                                                        @endif --}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger item-error item-tax_id-error"></div>
                                        </td>
                                        <td class="calculate-total"><input type="number" name="items[0][total_price]" id="addItemTotalPrice" class="form-control total-input" readonly></td>
                                        <td>
                                            <div class="action-btn d-flex">
                                                <button type="button" class="btn btn-success btn-sm save-row mr-2" onclick="saveNewItem()">
                                                    <i class="fas fa-save"></i> 
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" class="text-right">
                                        @if ($purchase_orders['status'] !== 'Completed') {{-- Conditionally hide "Add Item" button if PO is Completed --}}
                                            <button type="button" class="btn btn-success btn-sm" id="add-item-row-btn">
                                                <i class="fas fa-plus"></i> Add Item
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="btn-group" role="group">
                            @php
                                $poId = $purchase_orders['id'];
                                if($purchase_orders['status'] === 'Pending'){
                            @endphp
                                @can('approve PO')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-success" style="border: 1px solid gray" onclick="approvePO({{ $poId }})">
                                        <i class="fas fa-check mr-2"></i>Approve
                                    </button>
                                @endcan
                                @can('reject PO')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-danger" style="border: 1px solid gray" onclick="rejectPO({{ $poId }})">
                                        <i class="fas fa-times mr-2"></i>Reject
                                    </button>
                                @endcan
                            @php
                                } elseif($purchase_orders['status'] === 'Approved'){
                            @endphp
                                @can('receive PO')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-info" style="border: 1px solid gray" onclick="receivePO({{ $poId }})">
                                        <i class="fas fa-truck mr-2"></i>Receive
                                    </button>
                                @endcan
                            @php
                                } elseif($purchase_orders['status'] === 'Partially_received'){
                            @endphp
                                @can('complete PO')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-secondary" style="border: 1px solid gray" onclick="completePO({{ $poId }})">
                                        <i class="fas fa-check-double mr-2"></i>Complete
                                    </button>
                                @endcan
                                @php
                                } elseif($purchase_orders['id'] === null){
                            @endphp
                                @can('cancel PO')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-secondary" style="border: 1px solid gray" onclick="cancelPO({{ $poId }})">
                                        <i class="fas fa-check-double mr-2"></i>Cancel
                                    </button>
                                @endcan
                            @php
                                } else {
                            @endphp
                                @can('reject PO')
                                    <button type="button" class="btn btn-default p-3 ml-1 text-danger" style="border: 1px solid gray" onclick="rejectPO({{ $poId }})">
                                        <i class="fas fa-times mr-2"></i>Reject
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

<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editItemModalLabel">Edit Item</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editItemForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editItemId" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editItemName" class="form-label">Item Name</label>
                                <select class="form-select" id="editItemName" name="item_id" required>
                                    <option value="">Select Item</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editItemDiscount" class="form-label">Discount</label>
                                <input type="number" class="form-control" id="editItemDiscount" name="discount" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editItemTax" class="form-label">Tax</label>
                                <select class="form-select" id="editItemTax" name="tax_id" required>
                                    <option value="">Select Tax</option>
                                    <option value="">None</option>
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editItemQuantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="editItemQuantity" name="quantity" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editItemUnitPrice" class="form-label">Unit Price</label>
                        <input type="number" class="form-control" id="editItemUnitPrice" name="unit_price" step="0.01" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateItem()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    var itemsData = @json($items);
</script>
@endsection