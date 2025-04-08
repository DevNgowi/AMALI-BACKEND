@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Create GRN') }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('list_grn') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form method="POST" action="{{ route('store_grn') }}" id="grnForm">
                        @csrf
                        <div class="card rounded-0">
                            <div class="card-body m-2">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="vendor_id">Supplier</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="vendor_id" id="vendor_id" class="form-control" required>
                                            <option value="" disabled selected>Select Supplier</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}"
                                                    {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                    {{ $vendor->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2"></div>

                                    <div class="col-md-3">
                                        <label for="grn_number">GRN No</label>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <input type="text" name="grn_number" id="grn_number" class="form-control"
                                            value="{{ $grnNumber }}" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="po_reference_number">PO Reference</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="po_reference_number" id="po_reference_number" class="form-control">
                                            <option value="" selected>Select PO</option>
                                            @foreach ($purchaseOrders as $po)
                                                <option value="{{ $po->id }}"
                                                    data-items='@json($po->purchaseOrderItems)'>
                                                    {{ $po->order_number }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('po_reference_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2"></div>

                                    <div class="col-md-3">
                                        <label for="received_date">Received Date</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="received_date" id="received_date" class="form-control"
                                            value="{{ old('received_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                                            required>
                                        @error('received_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2 mt-3">
                                        <label for="delivery_note_number">Delivery Note No</label>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <input type="text" name="delivery_note_number" id="delivery_note_number"
                                            class="form-control" value="{{ old('delivery_note_number') }}" required>
                                        @error('delivery_note_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <table class="table table-bordered" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item Name</th>
                                                    <th>UOM</th>
                                                    <th>Ordered Qty</th>
                                                    <th>Received Qty</th>
                                                    <th>Accepted Qty</th>
                                                    <th>Rejected Qty</th>
                                                    <th>Received Condition</th>
                                                    <th>Unit Price</th>
                                                    <th>Total Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="items-table-body">
                                                <tr>
                                                    <td colspan='11' class='text-center'>Please select PO Reference to
                                                        load items.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-12">

                                        <div class="row mt-4"> {{-- ADD THIS NEW ROW AND TABLE FOR EXTRA CHARGES --}}
                                            <div class="col-12">
                                                <table class="table table-bordered" id="extra-charge-items-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Extra Charge Name</th>
                                                            <th>UOM</th>
                                                            <th>Amount</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="extra-charge-items-table-body">
                                                        {{-- Extra charge items will be added here by Javascript --}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>


                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-success btn-sm" id="add-item-row-btn">
                                                    <i class="fas fa-plus"></i> Add Item</button>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    id="add-extra-charge-item-row-btn"> <i class="fas fa-plus"></i> Add
                                                    Extra
                                                    Charge</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-3">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" rows="4">{{ old('remarks') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Total Items</th>
                                                <td><input type="number" name="total_items" id="total_items"
                                                        class="form-control" readonly></td>
                                            </tr>
                                            <tr>
                                                <th>Total Accepted</th>
                                                <td><input type="number" name="total_accepted" id="total_accepted"
                                                        class="form-control" readonly></td>
                                            </tr>
                                            <tr>
                                                <th>Total Rejected</th>
                                                <td><input type="number" name="total_rejected" id="total_rejected"
                                                        class="form-control" readonly></td>
                                            </tr>
                                            <tr>
                                                <th>Total Amount</th>
                                                <td><input type="number" name="total_amount" id="total_amount"
                                                        class="form-control" readonly></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="reset" class="btn btn-danger">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const poDropdown = document.getElementById("po_reference_number");
            const itemsTableBody = document.getElementById("items-table-body");
            const extraChargeItemsTableBody = document.getElementById("extra-charge-items-table-body");
            const addItemRowButton = document.getElementById("add-item-row-btn");
            const addExtraChargeRowButton = document.getElementById("add-extra-charge-item-row-btn");

            // Get references to the total fields
            const totalItemsInput = document.getElementById('total_items');
            const totalAcceptedInput = document.getElementById('total_accepted');
            const totalRejectedInput = document.getElementById('total_rejected');
            const totalAmountInput = document.getElementById('total_amount');

            // Initialize newItemRowIndex to 0 - Corrected Initialization
            let newItemRowIndex = 0;

            // Function to calculate values for a single item row (Rejected Qty, Total Amount)
            function calculateRowValues(row) {
                const receivedQty = parseFloat(row.querySelector('.received-qty')?.value) || 0;
                const acceptedQty = parseFloat(row.querySelector('.accepted-qty')?.value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price')?.value) || 0;
                const rejectedQtyInput = row.querySelector('.rejected-qty');
                const totalInput = row.querySelector('.total-input');
                if (rejectedQtyInput && totalInput) {
                    // Calculate rejected quantity
                    const rejectedQty = Math.max(0, receivedQty - acceptedQty);
                    rejectedQtyInput.value = rejectedQty;
                    // Calculate total amount
                    const totalAmount = receivedQty * unitPrice;
                    totalInput.value = totalAmount.toFixed(2);
                }
            }


            function calculateTotals() {
                let totalItems = 0;
                let totalAccepted = 0;
                let totalRejected = 0;
                let totalAmount = 0;

                document.querySelectorAll('#items-table-body tr').forEach(row => {
                    const receivedQtyInput = row.querySelector('.received-qty');
                    const acceptedQtyInput = row.querySelector('.accepted-qty');
                    const rejectedQtyInput = row.querySelector('.rejected-qty');
                    const totalInput = row.querySelector('.total-input');

                    if (receivedQtyInput && acceptedQtyInput && rejectedQtyInput && totalInput) {
                        totalItems++;
                        totalAccepted += parseFloat(acceptedQtyInput.value) || 0;
                        totalRejected += parseFloat(rejectedQtyInput.value) || 0;
                        totalAmount += parseFloat(totalInput.value) || 0;
                    }
                });

                // Also include extra charges in total amount
                document.querySelectorAll('#extra-charge-items-table-body tr').forEach(row => {
                    const amountInput = row.querySelector('.extra-charge-amount');
                    if (amountInput) {
                        totalAmount += parseFloat(amountInput.value) || 0;
                    }
                });

                // Update total fields
                document.getElementById('total_items').value = totalItems;
                document.getElementById('total_accepted').value = totalAccepted;
                document.getElementById('total_rejected').value = totalRejected;
                document.getElementById('total_amount').value = totalAmount.toFixed(2);
            }

            poDropdown.addEventListener("change", function() {
                const selectedOption = poDropdown.options[poDropdown.selectedIndex];
                let poItems = [];

                try {
                    const itemsData = selectedOption.getAttribute("data-items") || "[]";
                    poItems = JSON.parse(itemsData);
                } catch (error) {
                    console.error("Error parsing PO items:", error);
                    poItems = [];
                }

                itemsTableBody.innerHTML = "";

                // Reset the index counter when changing PO
                newItemRowIndex = 0;

                if (!poItems || poItems.length === 0) {
                    itemsTableBody.innerHTML =
                        "<tr><td colspan='11' class='text-center'>No items found for this PO.</td></tr>";
                    calculateTotals();
                    return;
                }

                poItems.forEach((item, index) => {
                    const itemId = item.item?.id || '';
                    const itemName = item.item?.name || 'N/A';
                    const unitId = item.unit?.id || '';
                    const quantity = item.quantity || 0;
                    const unitPrice = item.unit_price || 0;
                    const received_condition = item.received_condition || '';

                    // Important: Use the actual index for the name attribute
                    const rowHTML = `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <input type="text" class="form-control d-none" name="items[${index}][item_id]" value="${itemId}" readonly>
                        <input type="text" class="form-control po-item-name" value="${itemName}" readonly>
                    </td>
                    <td>
                        <select class="form-control" name="items[${index}][unit_id]">
                            <option value="">Select UOM</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" ${unitId == {{ $unit->id }} ? 'selected' : ''}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>${quantity}</td>
                    <td><input type="number" name="items[${index}][received_qty]" class="form-control received-qty" value="${quantity}" min="0" required></td>
                    <td><input type="number" name="items[${index}][accepted_qty]" class="form-control accepted-qty" value="${quantity}" min="0" required></td>
                    <td><input type="number" name="items[${index}][rejected_qty]" class="form-control rejected-qty" value="0" readonly></td>
                    <td>
                        <select class="form-control" name="items[${index}][received_condition]">
                            <option value="Good" ${received_condition === 'Good' ? 'selected' : ''}>Good</option>
                            <option value="Damaged" ${received_condition === 'Damaged' ? 'selected' : ''}>Damaged</option>
                            <option value="Expired" ${received_condition === 'Expired' ? 'selected' : ''}>Expired</option>
                        </select>
                    </td>
                    <td><input type="number" name="items[${index}][unit_price]" class="form-control unit-price" value="${unitPrice}" readonly></td>
                    <td><input type="number" name="items[${index}][total_price]" class="form-control total-input" value="0.00" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
                    itemsTableBody.insertAdjacentHTML("beforeend", rowHTML);

                    // Update newItemRowIndex to match the last index used
                    newItemRowIndex = index + 1;
                });

                // Reattach event listeners and calculate totals
                attachRemoveItemEventListeners();
                calculateTotals();
            });

            function attachRemoveItemEventListeners() {
                document.querySelectorAll(".remove-item").forEach(button => {
                    button.addEventListener("click", function() {
                        this.closest("tr").remove();
                        calculateTotals();
                    });
                });
            }
            attachRemoveItemEventListeners();


            function getExistingItemIds() {
                const existingItemIds = new Set(); 
                const rows = itemsTableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const itemIdInput = row.querySelector('select[name*="[item_id]"]') || row.querySelector(
                        'input[name*="[item_id]"]'); // Check for both select and input
                    if (itemIdInput && itemIdInput.value) {
                        existingItemIds.add(itemIdInput.value);
                    }
                });
                return existingItemIds;
            }


            addItemRowButton.addEventListener('click', function() {
                newItemRowIndex++;

                const existingItemIds = getExistingItemIds();
                let availableItemsOptions = '';
                let hasAvailableItems = false;

                @if (isset($allItems))
                    const allItems = @json($allItems);
                    let availableItems = allItems.filter(item => !existingItemIds.has(String(item.id)));

                    if (availableItems.length === 0) {
                        Swal.fire('Warning', 'All unique items have already been added.', 'warning');
                        return;
                    } else {
                        hasAvailableItems = true;
                        availableItemsOptions = availableItems.map(item => {
                            const buyingUnitId = item.item_units && item.item_units.buying_unit_id ?
                                item.item_units.buying_unit_id : (item.item_units ? item.item_units
                                    .unit_id : null);
                            return `<option value="${item.id}" data-unit-id="${buyingUnitId}" data-unit-price="${item.item_costs && item.item_costs[0] ? item.item_costs[0].amount : 0}">${item.name}</option>`;
                        }).join('');
                    }
                @endif


                if (hasAvailableItems) {
                    const newRow = `
                        <tr>
                            <td>${newItemRowIndex}</td>
                            <td>
                                <select name="items[${newItemRowIndex - 1}][item_id]" class="form-control product-select po-item-name" required>
                                    <option value="" disabled selected>Select Items</option>
                                    ${availableItemsOptions}
                                </select>
                            </td>
                            <td>
                                <select class="form-control unit-select" name="items[${newItemRowIndex - 1}][unit_id]" required>
                                    <option value="" disabled selected>Select UOM</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[${newItemRowIndex - 1}][ordered_qty]" class="form-control ordered-qty"></td>
                            <td><input type="number" name="items[${newItemRowIndex - 1}][received_qty]" class="form-control received-qty" value="0" min="0" required></td>
                            <td><input type="number" name="items[${newItemRowIndex - 1}][accepted_qty]" class="form-control accepted-qty" value="0" min="0" required></td>
                            <td><input type="number" name="items[${newItemRowIndex - 1}][rejected_qty]" class="form-control rejected-qty" value="0" readonly></td>
                            <td>
                                <select class="form-control" name="items[${newItemRowIndex - 1}][received_condition]">
                                    <option value="" disabled selected>Select Quality</option>
                                    <option value="Good">Good</option>
                                    <option value="Damaged">Damaged</option>
                                    <option value="Expired">Expired</option>
                                </select>
                            </td>
                            <td><input type="number" name="items[${newItemRowIndex - 1}][unit_price]" class="form-control unit-price" value="0.00"></td>
                            <td><input type="number" name="items[${newItemRowIndex - 1}][total_price]" class="form-control total-input" value="0.00" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `;

                    itemsTableBody.insertAdjacentHTML('beforeend', newRow);
                    attachRemoveItemEventListeners();
                    calculateTotals();

                    // Add event listener to the item dropdown in the newly added row
                    const newItemRow = itemsTableBody.lastElementChild;
                    const itemDropdown = newItemRow.querySelector('.product-select');

                    itemDropdown.addEventListener('change', function() {
                        const selectedItemId = this.value;
                        if (selectedItemId) {
                            // Get unit_id and unit_price from data attributes of the selected option
                            const selectedOption = this.options[this.selectedIndex];
                            const unitId = selectedOption.dataset.unitId;
                            const unitPrice = selectedOption.dataset.unitPrice;


                            // Populate unit and unit price
                            const unitDropdown = newItemRow.querySelector('.unit-select');
                            const unitPriceInput = newItemRow.querySelector('.unit-price');

                            if (unitDropdown) {
                                unitDropdown.value = unitId; // Set UOM from data attribute
                            }
                            if (unitPriceInput) {
                                unitPriceInput.value = unitPrice ||
                                    '0.00'; // Set Unit Price from data attribute
                            }
                        }
                    });
                } else if (!hasAvailableItems) {
                    Swal.fire('Warning', 'No more unique items available to add.',
                        'warning'); // Updated message
                }
            });


            let newExtraChargeRowIndex = 0;
            addExtraChargeRowButton.addEventListener('click', function() {
                newExtraChargeRowIndex++;
                const extraChargeRow = `
            <tr>
                <td>EC-${newExtraChargeRowIndex}</td>
                <td>
                    <select name="extra_charges[${newExtraChargeRowIndex - 1}][extra_charge_id]" class="form-control extra-charge-select" required>
                        <option value="" disabled selected>Select Extra Charge</option>
                        @foreach ($extra_charges as $extraCharge)
                            <option value="{{ $extraCharge->id }}">{{ $extraCharge->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="form-control unit-select" name="extra_charges[${newExtraChargeRowIndex - 1}][unit_id]" required>
                        <option value="" disabled selected>Select UOM</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="extra_charges[${newExtraChargeRowIndex - 1}][amount]" class="form-control extra-charge-amount" value="0.00" required>
                <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
                extraChargeItemsTableBody.insertAdjacentHTML('beforeend', extraChargeRow);
                attachRemoveItemEventListeners();
                calculateTotals();
            });

            document.getElementById('items-table-body').addEventListener('input', function(e) {
                // Log the event target for debugging
                console.log('Input event triggered on:', e.target);

                if (e.target.classList.contains('received-qty') ||
                    e.target.classList.contains('accepted-qty')) {
                    const row = e.target.closest('tr');
                    // Validate accepted quantity
                    if (e.target.classList.contains('accepted-qty')) {
                        const receivedQty = parseFloat(row.querySelector('.received-qty')?.value) || 0;
                        const acceptedQty = parseFloat(e.target.value) || 0;
                        if (acceptedQty > receivedQty) {
                            Swal.fire('Warning',
                                'Accepted quantity cannot be greater than received quantity', 'Warning');
                            e.target.value = receivedQty;
                        }
                    }

                    // Ensure we're finding these elements in the row
                    const receivedQtyInput = row.querySelector('.received-qty');
                    const acceptedQtyInput = row.querySelector('.accepted-qty');
                    const rejectedQtyInput = row.querySelector('.rejected-qty');
                    const unitPriceInput = row.querySelector('.unit-price');
                    const totalInput = row.querySelector('.total-input');

                    if (receivedQtyInput && acceptedQtyInput && rejectedQtyInput && unitPriceInput &&
                        totalInput) {
                        const receivedQty = parseFloat(receivedQtyInput.value) || 0;
                        const acceptedQty = parseFloat(acceptedQtyInput.value) || 0;
                        const unitPrice = parseFloat(unitPriceInput.value) || 0;

                        // Calculate rejected quantity
                        const rejectedQty = Math.max(0, receivedQty - acceptedQty);
                        rejectedQtyInput.value = rejectedQty;

                        // Calculate total amount
                        const totalAmount = receivedQty * unitPrice;
                        totalInput.value = totalAmount.toFixed(2);

                        // Update grand totals
                        calculateTotals();
                    }
                } else if (e.target.classList.contains('unit-price')) {
                    const row = e.target.closest('tr');
                    calculateRowValues(row);
                    calculateTotals();
                }
            });

            extraChargeItemsTableBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('extra-charge-amount')) {
                    calculateTotals();
                }
            });

            if (!poDropdown.value) {
                itemsTableBody.innerHTML =
                    "<tr><td colspan='11' class='text-center'>Please select PO Reference to load items.</td></tr>";
            }

            calculateTotals();
        });
    </script>
@endsection
