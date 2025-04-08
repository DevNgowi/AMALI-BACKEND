<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="45x45" href="{{ asset('images/favicon.png') }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>POS Sales</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/sales.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order_summary.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}">

    <style>
        /* Ensure modal is on top */
        .modal {
            z-index: 2000 !important;
        }
        .modal-backdrop {
            z-index: 1999 !important;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="cashout-button">
            <a href="{{ route('main_pos') }}" class="btn custom-btn">
                <i class="fas fa-money-bill-wave"></i><br>
                <span>Cash Out</span>
            </a>
        </div>
        <div class="cashout-button">
            <a href="{{ route('order_summary') }}" class="btn custom-btn">
                <i class="fas fa-shopping-cart"></i><br>
                <span>Order Summary</span>
            </a>
        </div>
        <div class="cashout-button">
            <a href="{{ route('main_pos') }}" class="btn custom-btn">
                <i class="fas fa-truck"></i><br>
                <span>Delivery</span>
            </a>
        </div>
        <h1 class="header-title">
            <img src="{{ asset('images/amali_logo_white.png') }}" alt="Amali-Logo">
        </h1>
        <div class="logout-button">
            <a href="{{ route('list_peripheral_setting') }}" class="btn custom-btn">
                <i class="fas fa-cog"></i><br>
                <span>Settings</span>
            </a>
        </div>
        <div class="logout-button">
            <a href="{{ route('home') }}" class="btn custom-btn">
                <i class="fas fa-sign-out-alt"></i><br>
                <span>Return</span>
            </a>
        </div>
    </div>

    <!-- Main Sales Container -->
    <div class="main-sales-container">
        @yield('content')
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sales/item_payments.js') }}"></script>
    <script src="{{ asset('js/sales/item_group_sales.js') }}"></script>
    <script src="{{ asset('js/sales/checkout_process.js') }}"></script>
    <script src="{{ asset('js/sales/extra_charge.js') }}"></script>
    <script src="{{ asset('js/sales/receipt_printout.js') }}"></script>
    <script src="{{ asset('js/sales/post_order_data.js') }}"></script>
    <script src="{{ asset('js/sales/payment_type.js') }}"></script>
    <script src="{{ asset('js/sales/customers.js') }}"></script>
    <script src="{{ asset('js/sales/add_to_cart.js') }}"></script>
</body>
</html>