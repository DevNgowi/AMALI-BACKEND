<!DOCTYPE html>
<html>
<head>
    <title>{{ $letter_title ?? 'Stock Level Report' }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($companyInfo)
            <h1>{{ $companyInfo->company_name ?? 'Company Name' }}</h1>
            <p>{{ $companyInfo->address ?? 'Company Address' }}</p>
            <p>Date Range: {{ $selectedDateRange ?? '' }}</p>
        @endif
        <h2>{{ $letter_title ?? 'Stock Level Report' }}</h2>
    </div>

    <table class="table table-stripped">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Item Group</th>
                <th>Unit</th>
                <th>Barcode</th>
                <th>Cost</th>
                {{-- <th>Brand</th> --}}
                <th>Stock Level</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr>
                    <td>{{ $item->item_name ?? '' }}</td>
                    <td>{{ $item->item_group_name ?? '' }}</td>
                    <td>{{ $item->unit_name ?? '' }}</td>
                    <td>{{ $item->item_barcode ?? '' }}</td>
                    <td>{{ number_format($item->amount) }}</td>
                    <td>{{ number_format($item->stock_level_quantity)}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No stock level data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>