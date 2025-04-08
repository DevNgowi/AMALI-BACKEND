<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases</title>

    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">

</head>
<body class="main-body">
    <div class="header-bar">
        <div>
            <input type="date" value="{{ date('Y-m-d') }}" />
            <select>
                <option selected disabled>Select Vendor</option>
                <!-- Dynamic options -->
            </select>
        </div>
        <div>
            <input type="text" placeholder="Search" />
        </div>
    </div>
    
    <div class="purchase-order-container">
        <div class="sidebar">
            <p class="no-record">No Record Found!</p>
        </div>
        <div class="content-area">
            <p class="no-record">Click on the purchase order to view the details.</p>
        </div>
    </div>
    
    
</body>
</html>