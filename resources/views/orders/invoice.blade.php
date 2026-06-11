<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $order->order_no }}</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            padding:30px;
            color:#333;
        }

        .header{
            text-align:center;
            margin-bottom:30px;
        }

        .header h1{
            margin:0;
            color:#3a6110;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        th,td{
            border:1px solid #ddd;
            padding:10px;
            text-align:left;
        }

        th{
            background:#f4f8ef;
        }

        .total{
            text-align:right;
            margin-top:20px;
            font-size:18px;
            font-weight:bold;
        }

        .print-btn{
            margin-bottom:20px;
        }

        @media print{
            .print-btn{
                display:none;
            }
        }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">
    Print Invoice
</button>

<div class="header">
    <h1>ARKLEN AGRO</h1>
    <h3>ORDER INVOICE</h3>
</div>

<p><strong>Order No:</strong> {{ $order->order_no }}</p>
<p><strong>Date:</strong> {{ $order->order_date->format('d-m-Y') }}</p>
<p><strong>Member:</strong> {{ $order->member->full_name }}</p>
<p><strong>Seller ID:</strong> {{ $order->member->seller_id }}</p>

<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Sponsor ID</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>

    @forelse($order->subOrders as $i => $sub)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $sub->name }}</td>
            <td>{{ $sub->mobile }}</td>
            <td>{{ $sub->sponsor_id }}</td>
            <td>₹{{ number_format($sub->amount,2) }}</td>
        </tr>
    @empty
        <tr>
            <td>1</td>
            <td>{{ $order->member->full_name }}</td>
            <td>{{ $order->member->contact }}</td>
            <td>{{ $order->member->sponsor_id }}</td>
            <td>₹{{ number_format($order->order_value,2) }}</td>
        </tr>
    @endforelse

    </tbody>
</table>

<div class="total">
    Grand Total: ₹{{ number_format($order->order_value,2) }}
</div>

</body>
</html>