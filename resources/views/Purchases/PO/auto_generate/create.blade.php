@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Create PO') }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('list_po') }}" class="btn btn-secondary">
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
                    <form method="POST" action="{{ route('store_po') }}" id="purchaseOrderForm">
                        @csrf
                        <div class="card rounded-0">
                            <div class="card-body m-2">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="supplier_id">Supplier</label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="supplier_id" id="supplier_id" class="form-control" required>
                                            <option value="" disabled selected>Select Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2"></div>

                                    <div class="col-md-3">
                                        <label for="order_number">Purchase Order No</label>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <input type="text" name="order_number" id="order_number" class="form-control"
                                            value="{{ $poNumber }}" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="order_date">Current Date</label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" name="order_date" id="order_date" class="form-control"
                                            value="{{ old('order_date', date('Y-m-d')) }}" required>
                                        @error('order_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2"></div>

                                    <div class="col-md-3">
                                        <label for="expected_delivery_date">Expected Delivery Date</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="expected_delivery_date" id="expected_delivery_date"
                                            class="form-control" value="{{ old('expected_delivery_date') }}" required>
                                        @error('expected_delivery_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <table class="table table-bordered" id="items-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Item Name</th>
                                                <th>UOM</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th>Unit Discount</th>
                                                <th>Select Tax</th>
                                                <th>Total Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tbody" id="tbody"> {{-- Added id="tbody" here --}}
                                        </tbody>
                                    </table>
                                    <div class="add-button d-flex">
                                        <button type="button" class="btn btn-success btn-sm" id="add-item-row-btn">
                                            <i class="fas fa-plus"></i> Add Item</button>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="notes">Remarks</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Sub total</th>
                                                <td><input type="number" name="subtotal" id="subtotal"
                                                        class="form-control" readonly></td>
                                            </tr>
                                            <tr>
                                                <th>Tax</th>
                                                <td><input type="number" name="tax" id="tax" class="form-control"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <th>Total Discount</th>
                                                <td><input type="number" name="total_discount" id="total_discount"
                                                        class="form-control" readonly></td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td><input type="number" name="total" id="total" class="form-control"
                                                        readonly></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary">Save & Print</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemsTableBody = document.getElementById(
                "tbody");
            const addItemRowButton = document.getElementById("add-item-row-btn");
            let newItemRowIndex = 0;


            function createTaxSelectDropdown() {
                const select = document.createElement('select');
                select.name = `items[${newItemRowIndex - 1}][tax_id]`;
                select.classList.add('form-control', 'tax-select');
                select.required = true;

                const defaultOption = document.createElement('option');
                defaultOption.value = "";
                defaultOption.disabled = true;
                defaultOption.selected = true;
                defaultOption.textContent = "Select Tax";
                select.appendChild(defaultOption);

                const noneOption = document.createElement('option');
                noneOption.value = "";
                noneOption.textContent = "None";
                select.appendChild(noneOption);

                @foreach ($taxes as $tax)
                    const taxOption = document.createElement('option');
                    taxOption.value = "{{ $tax->id }}";
                    taxOption.dataset.taxMode = "{{ $tax->tax_mode }}";
                    taxOption.dataset.taxValue = "{{ $tax->tax_mode === 'percentage' ? $tax->tax_percentage : $tax->tax_amount }}";
                    taxOption.textContent = `{{ $tax->name }} @if ($tax->tax_mode == 'percentage') ({{ $tax->tax_type }} - {{ number_format($tax->tax_percentage, 0) }}%) @else ({{ $tax->tax_type }} - {{ number_format($tax->tax_amount, 0) }} ) @endif`;
                    select.appendChild(taxOption);
                @endforeach
                return select;
            }

            function getExistingItemIds() {
                const existingItemIds = new Set();
                const rows = itemsTableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const itemIdInput = row.querySelector('select[name*="[item_id]"]') || row.querySelector(
                        'input[name*="[item_id]"]');
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
                    const taxSelectDropdown = createTaxSelectDropdown(); // Create tax dropdown here

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
                            <td><input type="number" name="items[${newItemRowIndex - 1}][quantity]" class="form-control quantity-input" min="1" value="1" required></td> {{-- Added default value 1 --}}
                            <td><input type="number" name="items[${newItemRowIndex - 1}][unit_price]" class="form-control price-input" step="0.01" value="0.00" required></td> {{-- Added default value 0.00 --}}
                            <td><input type="number" name="items[${newItemRowIndex - 1}][discount]" class="form-control discount-input" step="0.01" value="0.00"></td> {{-- Added default value 0.00 --}}
                            <td></td>  <td><input type="number" name="items[${newItemRowIndex - 1}][total_price]" class="form-control total-input" readonly></td>
                            <td></td> {{-- Action cell will be populated by attachRemoveItemEventListeners --}}
                        </tr>
                        `;

                    itemsTableBody.insertAdjacentHTML('beforeend', newRow);

                    // Insert tax dropdown into the new row
                    const newRowElement = itemsTableBody.lastElementChild;
                    const taxCell = newRowElement.querySelector('td:nth-child(7)'); // 7th cell is for tax
                    taxCell.appendChild(taxSelectDropdown);


                    attachRemoveItemEventListeners();
                    calculateTotals();


                    const newItemRow = itemsTableBody.lastElementChild;
                    const itemDropdown = newItemRow.querySelector('.product-select');

                    itemDropdown.addEventListener('change', function() {
                        const selectedItemId = this.value;
                        if (selectedItemId) {
                            const selectedOption = this.options[this.selectedIndex];
                            const unitId = selectedOption.dataset.unitId;
                            const unitPrice = selectedOption.dataset.unitPrice;

                            const unitDropdown = newItemRow.querySelector('.unit-select');
                            const unitPriceInput = newItemRow.querySelector('.price-input');

                            if (unitDropdown) {
                                unitDropdown.value = unitId;
                            }
                            if (unitPriceInput) {
                                unitPriceInput.value = unitPrice || '0.00';
                            }
                        }
                    });
                } else if (!hasAvailableItems) {
                    Swal.fire('Warning', 'No more unique items available to add.',
                        'warning');
                }
            });

            function calculateTotals() {
                let subtotal = 0;
                let totalDiscount = 0;
                let totalTax = 0;

                const rows = itemsTableBody.querySelectorAll('tr');
                rows.forEach(row => {
                    const quantityInput = row.querySelector('.quantity-input');
                    const priceInput = row.querySelector('.price-input');
                    const discountInput = row.querySelector('.discount-input');
                    const totalInput = row.querySelector('.total-input');
                    const taxSelect = row.querySelector('.tax-select');

                    if (quantityInput && priceInput && discountInput && totalInput && taxSelect) {
                        const quantity = parseFloat(quantityInput.value) || 0;
                        const unitPrice = parseFloat(priceInput.value) || 0;
                        const discount = parseFloat(discountInput.value) || 0;

                        let itemTaxAmount = 0;
                        const selectedTaxOption = taxSelect.options[taxSelect.selectedIndex];
                        if (selectedTaxOption && selectedTaxOption.value !== "") { // Check if a tax is selected (not default or none)
                            const taxMode = selectedTaxOption.dataset.taxMode;
                            const taxValue = parseFloat(selectedTaxOption.dataset.taxValue) || 0;

                            const itemTotal = quantity * unitPrice;
                            const itemDiscountAmount = itemTotal * (discount / 100);
                            const discountedTotal = itemTotal - itemDiscountAmount;


                            if (taxMode === 'percentage') {
                                itemTaxAmount = discountedTotal * (taxValue / 100);
                            } else if (taxMode === 'fixed') {
                                itemTaxAmount = taxValue * quantity; // Apply fixed tax per quantity
                            }
                            totalTax += itemTaxAmount;

                        }


                        const itemTotal = quantity * unitPrice;
                        const itemDiscountAmount = itemTotal * (discount / 100);
                        const discountedTotal = itemTotal - itemDiscountAmount;


                        totalInput.value = discountedTotal.toFixed(2);
                        subtotal += itemTotal;
                        totalDiscount += itemDiscountAmount;

                    }
                });

                document.getElementById('subtotal').value = subtotal.toFixed(2);
                document.getElementById('tax').value = totalTax.toFixed(2);
                document.getElementById('total_discount').value = totalDiscount.toFixed(2);
                document.getElementById('total').value = (subtotal + totalTax - totalDiscount).toFixed(2);
            }

            function attachRemoveItemEventListeners() {
                const rows = itemsTableBody.querySelectorAll('tr');
                rows.forEach(row => {
                    let actionCell = row.querySelector('td:last-child');
                    if (actionCell && !actionCell.querySelector('.remove-item-btn')) {
                        actionCell.innerHTML =
                            '<button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-trash"></i></button>';
                        const removeItemButton = actionCell.querySelector('.remove-item-btn');
                        removeItemButton.addEventListener('click', function() {
                            row.remove();
                            calculateTotals();
                            renumberItemRows();
                        });
                    }
                });
            }

            function renumberItemRows() {
                const rows = itemsTableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    const rowNumberCell = row.querySelector('td:first-child');
                    if (rowNumberCell) {
                        rowNumberCell.textContent = index + 1;
                        row.querySelectorAll('input, select').forEach(element => {
                            if (element.name.startsWith('items[')) {
                                element.name = element.name.replace(/items\[\d+\]/,
                                    `items[${index}]`);
                            }
                        });
                    }
                });
                newItemRowIndex = rows.length;
                if (newItemRowIndex === 0) newItemRowIndex = 0;
            }


            itemsTableBody.addEventListener('input', function(event) {
                if (event.target.classList.contains('quantity-input') ||
                    event.target.classList.contains('price-input') ||
                    event.target.classList.contains('discount-input') ||
                    event.target.classList.contains('tax-select')
                ) {
                    calculateTotals();
                }
            });


            attachRemoveItemEventListeners();
            calculateTotals();


        });
    </script>
@endsection