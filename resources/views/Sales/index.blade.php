@extends('layouts.sales')

@section('content')
    <div class="sidebar">
        <div class="form-group">
            <input type="text" name="search_item_group" class="form-control" placeholder="Search Here">
        </div>
        <div class="item_group">
            <ul class="item-group-list">
                @foreach ($item_groups as $index => $item_group)
                    <li class="item-group-list-item">
                        <a href="#" onclick="loadItemGroup('{{ $item_group->id }}')">
                            {{ strtoupper($item_group->name) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <p class="no-record" style="display: none;">No Record Found!</p>
    </div>

    <div class="content-area" id="content-area">
        <nav class="item_category">
            <ul class="item-category-list" id="item-category-list">
            
            </ul>
        </nav>

        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <input type="text" id="search-items-input" class="form-control rounded-3 col-md-8 p-2"
                        placeholder="Search Items" oninput="searchItems()">
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-barcode barcode_search"></i>
                        </span>
                    </div>
                    <input type="text" id="barcode" class="form-control rounded-3 p-2 col-md-8"
                        placeholder="Search Barcode Here" oninput="searchBarcode()">
                </div>
            </div>
        </div>

        <div id="items-container">
            <p>Loading items...</p>
        </div>
    </div>

    <div class="process-payment-area">
        <div class="payment-card">
            <div class="card-body payment-details" id="payment-details-area">
                <div class="customer-section">
                    <h4 class="customer-area mb-3">Select Customer Type</h4>
                    <select class="form-control rounded-3 p-3 mb-3" id="customer_type_id" name="customer_type_id">
                        @foreach ($customer_types as $customer_type)
                            <option value="{{ $customer_type->id }}">{{ $customer_type->name }}</option>
                        @endforeach
                    </select>
        
                    <div class="form-group" id="registeredCustomerGroup" style="display: none;">
                        <div class="input-group">
                            <select class="form-control" name="customer_id" id="customer_id">
                                <option value="" selected>---Select---</option>
                                @foreach ($customer_details as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-secondary btn-sm" type="button">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="payment-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="extra-charge mb-3">
                    <button class="btn btn-default" style="border: 1px solid gray;" id="extra-charge-button"
                        data-bs-toggle="modal" data-bs-target="#extra-charge-modal">
                        <i class="fas fa-plus"></i><br> Extra Charges
                    </button>
                </div>

                <div class="modal fade" id="extra-charge-modal" tabindex="-1" aria-labelledby="extraChargeModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="extraChargeModalLabel">Extra Charges</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="extra-charge-form">
                                    <div id="extra-charge-checkboxes"></div>
                                    <button type="button" class="btn btn-primary" id="apply-extra-charge">Apply</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="total-amount">
                    <label>Total</label>
                    <div class="amount" id="total-amount">0.00</div>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-fast-cash" id="add-to-cart">Add to Cart</button>
                    <button class="btn btn-check-out" id="check-out-button">Check Out</button>
                </div>
            </div>

            <div class="checkout-process" id="checkout-process" style="display: none;">
                <div class="payment-info p-2">
                    <div class="payment-type">
                        <label for="payment-type-select">Payment Method <span class="text-danger">*</span> </label>
                        <select class="form-control" name="payment_id" id="payment-type-select" required>
                            <option selected>Select Payment</option>
                            @foreach ($payments as $payment)
                                <option value="{{ $payment->id }}">{{ $payment->short_code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="checkout-header">
                    <div class="row">
                        <div class="col-md-6">
                            <span class="text-bold">Order No: {{ $orderNumber }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-bold">Date: {{ date('d/m/Y') }}</span>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="payment-details">
                    <div class="form-group mb-3">
                        <label for="total" class="form-label text-lg" style="font-size: 18px">Total Amount: <span
                                style="font-size: 28px" id="checkout-total-amount">0.00</span></label>
                    </div>
                    <hr>
                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="tip" class="form-label">Add Tip:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="rounded-0 form-control" id="tip" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="discount" class="form-label">Add Discount:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="rounded-0 form-control" id="discount" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="ground_total" class="form-label">Ground Total Amount: <span style="font-size: 28px"
                                id="ground-total-amount">0.00</span></label>
                    </div>
                    <hr>
                    <div class="keypad-container">
                        <div class="keypad">
                            <button>1</button>
                            <button>2</button>
                            <button>3</button>
                            <button class="bg-orange-500 text-white">More..</button>
                            <button>4</button>
                            <button>5</button>
                            <button>6</button>
                            <button class="clear bg-red-500 text-white">C</button>
                            <button>7</button>
                            <button>8</button>
                            <button>9</button>
                            <button class="bg-blue-600 text-white pay">Pay</button>
                            <button>0</button>
                            <button>00</button>
                            <button>.</button>
                            <button class="bg-blue-500 text-white">X</button>
                        </div>
                        <div class="actions">
                            <button class="bg-blue-100 text-blue-800 split">Split Bill</button>
                            <button class="bg-orange-500 text-white checkout" id="print-checkout-button">Check Out</button>
                            <button class="bg-blue-500 text-white hide" id="hide-checkout-button">Hide</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const defaultImageUrl = "{{ asset('images/shopping-bag.jpg') }}";
        const firstItemGroupId = "{{ $item_groups->first()->id ?? '' }}";
        window.orderNumber = "{{ $orderNumber ?? '' }}";
        window.csrfToken = "{{ csrf_token() }}"; 
        console.log('Order Number from Blade:', window.orderNumber); 
        console.log('CSRF Token from Blade:', window.csrfToken);
    </script>

   

@endsection