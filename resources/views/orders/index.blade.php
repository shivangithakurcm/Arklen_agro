@extends('layouts.app')
@section('title', 'Orders — Arklen Agro')
@section('page-title', 'Order List')

@section('content')
<style>
.form-control{width:100%;padding:9px 11px;border:1px solid #dcdcdc;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;transition:border-color .15s;}
.form-control:focus{border-color:#4b7c20;box-shadow:0 0 0 3px rgba(109,184,42,.10);}
.modal-btn{padding:10px 22px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;}
.add-btn{background:var(--green-700,#4b7c20);color:#fff;}
.badge-pending{background:#fef9c3;color:#854d0e;border:1px solid #fde68a;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-processing{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-delivered{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-cancelled{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.action-link{display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:20px;font-size:11px;font-weight:700;text-decoration:none;border:1px solid #bbf7d0;background:#dcfce7;color:#166534;transition:all .15s;}
.action-link:hover{background:#bbf7d0;}
</style>

<div class="card card-pad">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="margin:0;color:var(--green-800,#3a6110);"><i class="fas fa-box"></i> Order List</h3>
        <a href="{{ route('orders.create') }}" class="modal-btn add-btn">
            <i class="fas fa-plus"></i> Create Order
        </a>
    </div>

    <form method="GET" action="{{ route('orders.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search order ID / punch by" class="form-control" style="max-width:220px;">
        <input type="date" name="date_from" value="{{ request('date_from') }}"
            class="form-control" style="max-width:155px;">
        <span style="color:#888;font-size:13px;align-self:center;">to</span>
        <input type="date" name="date_to" value="{{ request('date_to') }}"
            class="form-control" style="max-width:155px;">
        <button type="submit" class="modal-btn add-btn">Search</button>
        @if(request()->anyFilled(['search','date_from','date_to']))
        <a href="{{ route('orders.index') }}"
            style="padding:10px 15px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;font-size:13px;">Clear</a>
        @endif
    </form>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Order ID</th>
                    <th>Punch By</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $i => $o)
                <tr>
                    <td>{{ $orders->firstItem() + $i }}</td>
                    <td>
                        <span style="font-family:monospace;font-weight:700;font-size:12px;color:#1a56b0;">
                            #{{ str_pad($o->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $o->member->full_name }}</div>
                        <div style="font-size:11px;color:#888;">{{ $o->member->seller_id }}</div>
                    </td>
                    <td>
                        <span style="font-weight:700;color:var(--green-800,#3a6110);">
                            ₹{{ number_format($o->total_value, 2) }}
                        </span>
                    </td>
                    <td style="font-size:13px;">{{ $o->order_date->format('d M Y') }}</td>
                   <td>
    <span class="badge badge-{{ $o->status }}">
        {{ ucfirst($o->status) }}
    </span>
</td>

<td>
    <div style="display:flex;gap:8px;align-items:center;">
        
        <a href="{{ route('orders.show', $o->id) }}"
           class="action-link"
           style="background:#dbeafe;border-color:#bfdbfe;color:#1e40af;">
            <i class="fas fa-eye"></i> View
        </a>

        <a href="{{ route('orders.action', $o) }}"
           class="action-link">
            <i class="fas fa-circle-check"></i> Active
        </a>

    </div>
</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:30px;color:#888;">No orders found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:15px;">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection