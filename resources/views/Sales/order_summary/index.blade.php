@extends('layouts.sales')
@section('content')
    <div class="order-summary">
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" class="form-control date-picker" value="{{ $date }}"
                    max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" id="dateFilter">
            </div>
            <div class="col-md-6">
                <div class="status-tabs">
                    <button class="tab-btn active" data-type="all">All <span
                            class="count">{{ $count_all }}</span></button>
                    <button class="tab-btn" data-type="cart" data-status="in-cart">In Cart <span
                            class="count orange">{{ $count_in_cart }}</span></button>
                    <button class="tab-btn" data-type="order" data-status="settled">Settled <span
                            class="count teal">{{ $count_settled }}</span></button>
                    <button class="tab-btn" data-type="order" data-status="voided">Voided <span
                            class="count red">{{ $count_voided }}</span></button>
                </div>
            </div>
            <div class="col-md-3">
                <div class="search-container">
                    <input type="search" class="form-control" placeholder="Search" id="searchInput">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>
        </div>

        <div class="action-wrapper mb-3">
            <div class="selected-count d-none">0 record(s) selected</div>
            <div class="action-toolbar">
                <div style="border-right: 1px solid gray"></div>
                <button class="tool-btn" id="recallButton"><i class="fas fa-undo"></i> Recall</button>
                <div style="border-right: 1px solid gray"></div>
                <button class="tool-btn" id="settleButton"><i class="fas fa-check-circle"></i> Settle</button>
                <div style="border-right: 1px solid gray"></div>
                <button class="tool-btn" id="voidButton"><i class="fas fa-ban"></i> Void</button>
                <div style="border-right: 1px solid gray"></div>
                <button class="tool-btn"><i class="fas fa-print"></i> Reprint Receipt</button>
                <div style="border-right: 1px solid gray"></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Order No</th>
                        <th>Time</th>
                        <th>Receipt No</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody id="order-table-body">
                    @if ($order_summaries->isEmpty() && $cart_summaries->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">No Record Found!</td>
                        </tr>
                    @else
                        @foreach ($order_summaries as $order)
                            <tr data-type="order" data-date="{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}"
                                data-status="{{ $order->status }}" data-order-number="{{ $order->order_number }}">
                                <td><input type="checkbox" class="rowCheckbox"></td>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->date)->format('h:i A') }}</td>
                                <td>{{ $order->receipt_number ?? '-' }}</td>
                                <td>{{ $order->status }}</td>
                                <td>{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                        @foreach ($cart_summaries as $cart)
                            <tr data-type="cart" data-date="{{ \Carbon\Carbon::parse($cart->date)->format('Y-m-d') }}"
                                data-status="{{ $cart->status }}" data-order-number="{{ $cart->order_number }}"
                                data-total-amount="{{ $cart->total_amount }}">
                                <td><input type="checkbox" class="rowCheckbox"></td>
                                <td>{{ $cart->order_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($cart->date)->format('h:i A') }}</td>
                                <td>-</td>
                                <td>{{ $cart->status }}</td>
                                <td>{{ number_format($cart->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Recall Modal -->
        <div class="modal fade" id="recallModal" tabindex="-1" aria-labelledby="recallModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recallModalLabel">Edit Items</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody"></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveItemsButton">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settle Modal -->
        <div class="modal fade" id="settleModal" tabindex="-1" aria-labelledby="settleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="settleModalLabel">Settle Cart Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="payment-info p-2">
                            <div class="payment-type">
                                <label for="settle-payment-type-select">Payment Method <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" name="payment_id" id="settle-payment-type-select" required>
                                    <option value="" selected>Select Payment</option>
                                    @foreach ($payments as $payment)
                                        <option value="{{ $payment->id }}">{{ $payment->short_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="checkout-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <span class="text-bold" id="settle-order-number"></span>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-bold">Date: {{ date('d/m/Y') }}</span>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="payment-details">
                            <div class="form-group mb-3">
                                <label for="settle-total" class="form-label text-lg" style="font-size: 18px">Total
                                    Amount: <span style="font-size: 28px" id="settle-total-amount">0.00</span></label>
                            </div>
                            <hr>
                            <div class="form-group mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="settle-tip" class="form-label">Add Tip:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="number" class="rounded-0 form-control" id="settle-tip"
                                            value="0" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="settle-discount" class="form-label">Add Discount:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="number" class="rounded-0 form-control" id="settle-discount"
                                            value="0" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="settle-ground-total" class="form-label">Ground Total Amount: <span
                                        style="font-size: 28px" id="settle-ground-total-amount">0.00</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitSettleButton">Settle Payment</button>
                    </div>
                </div>
            </div>
        </div>
       <!-- Void Modal -->
       <div class="modal fade" id="voidModal" tabindex="-1" aria-labelledby="voidModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voidModalLabel">Void Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="voidForm">
                        <div class="mb-3">
                            <label for="void-reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <select class="form-control" id="void-reason" required>
                                <option value="" selected>Select a reason</option>
                                @foreach (\App\Models\Reason::with('reasonType')->get() as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->reasonType->name }} - {{ $reason->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="void-details" class="form-label">Details <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="void-details" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="submitVoidButton">Void Order</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
       document.addEventListener('DOMContentLoaded', function() {
            const dateFilter = document.getElementById('dateFilter');
            const statusTabs = document.querySelectorAll('.tab-btn');
            const searchInput = document.getElementById('searchInput');
            const recallButton = document.getElementById('recallButton');
            const settleButton = document.getElementById('settleButton');
            const voidButton = document.getElementById('voidButton');
            const saveItemsButton = document.getElementById('saveItemsButton');
            const submitSettleButton = document.getElementById('submitSettleButton');
            const submitVoidButton = document.getElementById('submitVoidButton');
            let currentOrderNumber = null;
            let currentType = null;

            // Date filter
            dateFilter.addEventListener('change', function() {
                const selectedDate = this.value;
                window.location.href = `?date=${selectedDate}`;
            });

            // Status filter
            statusTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    statusTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    const type = this.getAttribute('data-type');
                    const status = this.getAttribute('data-status');
                    filterTable(type, status);
                });
            });

            // Search filter
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#order-table-body tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            function filterTable(type, status) {
                const rows = document.querySelectorAll('#order-table-body tr');
                rows.forEach(row => {
                    const rowType = row.getAttribute('data-type');
                    const rowStatus = row.getAttribute('data-status');
                    if (type === 'all' || (rowType === type && (!status || rowStatus === status))) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            // Checkbox functionality
            const selectAll = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
            const selectedCount = document.querySelector('.selected-count');

            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
                updateSelectedCount();
            });

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            function updateSelectedCount() {
                const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
                selectedCount.textContent = `${checkedCount} record(s) selected`;
                selectedCount.classList.toggle('d-none', checkedCount === 0);
            }

            // Recall button
            recallButton.addEventListener('click', function() {
                const selectedRows = document.querySelectorAll('.rowCheckbox:checked');
                if (selectedRows.length !== 1) {
                    Swal.fire('Error', 'Please select exactly one order or cart to recall.', 'error');
                    return;
                }

                const row = selectedRows[0].closest('tr');
                currentOrderNumber = row.getAttribute('data-order-number');
                currentType = row.getAttribute('data-type');

                fetchItems(currentOrderNumber, currentType);
            });

            settleButton.addEventListener('click', function() {
                const selectedRows = document.querySelectorAll('.rowCheckbox:checked');
                if (selectedRows.length !== 1) {
                    Swal.fire('Error', 'Please select exactly one cart to settle.', 'error');
                    return;
                }

                const row = selectedRows[0].closest('tr');
                currentOrderNumber = row.getAttribute('data-order-number');
                currentType = row.getAttribute('data-type');

                if (currentType !== 'cart') {
                    Swal.fire('Error', 'Settle is only available for cart orders.', 'error');
                    return;
                }

                const totalAmount = parseFloat(row.getAttribute('data-total-amount'));
                if (totalAmount <= 0) {
                    Swal.fire('Error', 'Cannot settle an empty cart.', 'error');
                    return;
                }

                document.getElementById('settle-order-number').textContent =
                    `Order No: ${currentOrderNumber}`;
                document.getElementById('settle-total-amount').textContent = totalAmount.toFixed(2);
                document.getElementById('settle-ground-total-amount').textContent = totalAmount.toFixed(2);
                document.getElementById('settle-tip').value = 0;
                document.getElementById('settle-discount').value = 0;

                new bootstrap.Modal(document.getElementById('settleModal')).show();
            });

            // Void button
            voidButton.addEventListener('click', function() {
                const selectedRows = document.querySelectorAll('.rowCheckbox:checked');
                if (selectedRows.length !== 1) {
                    Swal.fire('Error', 'Please select exactly one order to void.', 'error');
                    return;
                }
                const row = selectedRows[0].closest('tr');
                currentOrderNumber = row.getAttribute('data-order-number');
                currentType = row.getAttribute('data-type');
                if (currentType !== 'order') {
                    Swal.fire('Error', 'Void is only available for orders.', 'error');
                    return;
                }
                if (row.getAttribute('data-status') === 'voided') {
                    Swal.fire('Error', 'This order is already voided.', 'error');
                    return;
                }
                document.getElementById('void-reason').value = '';
                document.getElementById('void-details').value = '';
                new bootstrap.Modal(document.getElementById('voidModal')).show();
            });
            // Fetch items via AJAX (for Recall)
            function fetchItems(orderNumber, type) {
                fetch('/point_of_sale/order_summary/get-items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_number: orderNumber, type: type })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Fetch Items Response:', data);
                    if (data.success) {
                        populateItemsTable(data.items);
                        new bootstrap.Modal(document.getElementById('recallModal')).show();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to fetch items', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An error occurred while fetching items', 'error');
                });
            }

            function populateItemsTable(items) {
                const tbody = document.getElementById('itemsTableBody');
                tbody.innerHTML = '';
                items.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><input type="text" class="form-control" value="${item.name}" data-field="name"></td>
                        <td><input type="text" class="form-control" value="${item.unit}" data-field="unit"></td>
                        <td><input type="number" class="form-control" value="${item.quantity}" data-field="quantity"></td>
                        <td><input type="number" class="form-control" value="${item.amount}" data-field="amount"></td>
                    `;
                    tr.setAttribute('data-id', item.id);
                    tbody.appendChild(tr);
                });
            }

            // Existing saveItemsButton unchanged
            saveItemsButton.addEventListener('click', function() {
                const items = [];
                document.querySelectorAll('#itemsTableBody tr').forEach(row => {
                    items.push({
                        id: row.getAttribute('data-id'),
                        name: row.querySelector('[data-field="name"]').value,
                        unit: row.querySelector('[data-field="unit"]').value,
                        quantity: parseInt(row.querySelector('[data-field="quantity"]').value),
                        amount: parseFloat(row.querySelector('[data-field="amount"]').value)
                    });
                });
                fetch('/point_of_sale/order_summary/update-items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_number: currentOrderNumber, type: currentType, items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Items updated successfully', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('recallModal')).hide();
                        window.location.reload();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to update items', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An error occurred while updating items', 'error');
                });
            });

            // Settle modal calculations (unchanged)
            const settleTip = document.getElementById('settle-tip');
            const settleDiscount = document.getElementById('settle-discount');
            const settleTotalAmount = document.getElementById('settle-total-amount');
            const settleGroundTotalAmount = document.getElementById('settle-ground-total-amount');

            function calculateGroundTotal(totalAmount, tip, discount) {
                return totalAmount + tip - discount;
            }

            function updateGroundTotal() {
                const totalAmount = parseFloat(settleTotalAmount.textContent.replace(/,/g, ''));
                const tip = parseFloat(settleTip.value) || 0;
                const discount = parseFloat(settleDiscount.value) || 0;
                const groundTotal = calculateGroundTotal(totalAmount, tip, discount);
                settleGroundTotalAmount.textContent = groundTotal.toFixed(2);
            }

            settleTip.addEventListener('input', updateGroundTotal);
            settleDiscount.addEventListener('input', updateGroundTotal);

            // Submit settle payment
            submitSettleButton.addEventListener('click', function() {
                const paymentId = document.getElementById('settle-payment-type-select').value;
                const totalAmount = parseFloat(settleTotalAmount.textContent.replace(/,/g, ''));
                const tip = parseFloat(settleTip.value) || 0;
                const discount = parseFloat(settleDiscount.value) || 0;
                const groundTotal = calculateGroundTotal(totalAmount, tip, discount);

                if (!paymentId) {
                    Swal.fire('Error', 'Please select a payment method.', 'error');
                    return;
                }

                const settleData = {
                    order_number: currentOrderNumber,
                    payment_id: paymentId,
                    total_amount: totalAmount,
                    tip: tip,
                    discount: discount,
                    grand_total: groundTotal
                };

                fetch('/point_of_sale/order_summary/settle-cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(settleData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Cart settled successfully', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('settleModal')).hide();
                        window.location.reload();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to settle cart', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An error occurred while settling cart', 'error');
                });
            });

            // Submit void order
            submitVoidButton.addEventListener('click', function() {
        const reasonId = document.getElementById('void-reason').value;
        const details = document.getElementById('void-details').value.trim();

        if (!reasonId || !details) {
            Swal.fire('Error', 'Please select a reason and provide details.', 'error');
            return;
        }

        const voidData = {
            order_number: currentOrderNumber, // Send order_number
            reason_id: reasonId,
            details: details
        };

        fetch('/point_of_sale/order_summary/void-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(voidData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', 'Order voided successfully', 'success');
                bootstrap.Modal.getInstance(document.getElementById('voidModal')).hide();
                window.location.reload();
            } else {
                Swal.fire('Error', data.message || 'Failed to void order', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An error occurred while voiding order', 'error');
        });
    });
            // Initial filter
            filterTable('all', null);
        });
    </script>
@endsection
