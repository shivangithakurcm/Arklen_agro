@extends('layouts.app')
@section('title', 'Orders — Arklen Agro')
@section('page-title', 'Order List')

@section('content')
<style>
.form-control{width:100%;padding:9px 11px;border:1px solid #dcdcdc;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;}
.modal-btn{padding:10px 22px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;}
.add-btn{background:#4b7c20;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.badge-pending{background:#fef9c3;color:#854d0e;border:1px solid #fde68a;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-processing{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-delivered{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-cancelled{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.action-link{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;text-decoration:none;transition:all .15s;}
.sub-row td{background:#f8fdf4;font-size:12px;color:#555;border-bottom:1px solid #f0f0f0;}
.sub-row:last-child td{border-bottom:2px solid #e0edcc;}
.leg-l{background:#EAF3DE;color:#3a6110;border:1px solid #c0dd97;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;}
.leg-r{background:#fff3e0;color:#b45309;border:1px solid #fcd59a;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;}
</style>

<div class="card card-pad">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="margin:0;color:#3a6110;"><i class="fas fa-box"></i> Order List</h3>
        <a href="{{ route('orders.create') }}" class="modal-btn add-btn">
            <i class="fas fa-plus"></i> Create Order
        </a>
    </div>

    <form method="GET" action="{{ route('orders.index') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search order no / member" class="form-control" style="max-width:220px;">
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

    @if(session('success'))
        <div style="margin-bottom:14px;padding:12px 15px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:13px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>S.No</th>
                    <th>Order No</th>
                    <th>Punch By</th>
                    <th>Total Price</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $i => $order)
                {{-- Main order row --}}
                <tr style="cursor:pointer;" onclick="toggleSub({{ $order->id }})">
                    <td style="text-align:center;color:#4b7c20;font-size:13px;">
                        <i class="fas fa-chevron-down" id="icon-{{ $order->id }}"></i>
                    </td>
                    {{-- ✅ S.No pagination-aware --}}
                    <td>{{ $orders->firstItem() + $i }}</td>
                    <td>
                        <span style="font-weight:700;color:#1a56b0;font-family:monospace;">
                            {{ $order->order_no }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $order->member->full_name }}</div>
                        <div style="font-size:11px;color:#888;">{{ $order->member->seller_id }}</div>
                    </td>
                    <td>
                        <span style="font-weight:700;color:#3a6110;">
                            ₹{{ number_format($order->subOrders->sum('amount'), 2) }}
                        </span>
                    </td>
                    <td>{{ $order->order_date ? $order->order_date->format('d M Y') : '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td onclick="event.stopPropagation()">
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('orders.show', $order->id) }}"
                               class="action-link"
                               style="background:#dbeafe;border:1px solid #bfdbfe;color:#1e40af;">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- SubOrder rows (hidden by default) --}}
                <tr id="sub-{{ $order->id }}" style="display:none;">
                    <td colspan="8" style="padding:0;">
                        <table style="width:100%;border-collapse:collapse;background:#f8fdf4;">
                            <thead>
                                <tr style="background:#eef7e4;">
                                    <th style="padding:8px 16px;font-size:11px;color:#7a9a50;text-transform:uppercase;width:40px;">#</th>
                                    <th style="padding:8px 16px;font-size:11px;color:#7a9a50;text-transform:uppercase;">Name</th>
                                    <th style="padding:8px 16px;font-size:11px;color:#7a9a50;text-transform:uppercase;">Mobile</th>
                                    <th style="padding:8px 16px;font-size:11px;color:#7a9a50;text-transform:uppercase;">Aadhar</th>
                                    <th style="padding:8px 16px;font-size:11px;color:#7a9a50;text-transform:uppercase;">Sponsor ID</th>
                                    <th style="padding:8px 16px;font-size:11px;color:#7a9a50;text-transform:uppercase;">Leg</th>
                                    <th style="padding:8px 16px;font-size:11px;color:#7a9a50;text-transform:uppercase;text-align:right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->subOrders as $j => $sub)
                                <tr class="sub-row">
                                    <td style="padding:8px 16px;color:#bbb;font-weight:700;">{{ $j + 1 }}</td>
                                    <td style="padding:8px 16px;font-weight:600;">{{ $sub->name }}</td>
                                    <td style="padding:8px 16px;">{{ $sub->mobile ?? '—' }}</td>
                                    <td style="padding:8px 16px;font-family:monospace;font-size:11px;">{{ $sub->aadhar ?? '—' }}</td>
                                    <td style="padding:8px 16px;font-weight:600;color:#1a56b0;">{{ $sub->sponsor_id ?? '—' }}</td>
                                    <td style="padding:8px 16px;">
                                        @if($sub->leg === 'left')
                                            <span class="leg-l">◀ Left</span>
                                        @elseif($sub->leg === 'right')
                                            <span class="leg-r">▶ Right</span>
                                        @else
                                            <span style="color:#aaa;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:8px 16px;text-align:right;font-weight:700;color:#3a6110;">
                                        ₹{{ number_format($sub->amount, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" style="padding:12px 16px;color:#aaa;text-align:center;">No line items</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:30px;color:#888;">No orders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Pagination --}}
    <div style="margin-top:15px;">
        {{ $orders->withQueryString()->links('vendor.pagination.custom') }}
    </div>

</div>

<script>
function toggleSub(id) {
    const row  = document.getElementById('sub-' + id);
    const icon = document.getElementById('icon-' + id);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
        icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
    } else {
        row.style.display = 'none';
        icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
    }
}
</script>
@endsection