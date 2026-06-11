@extends('layouts.app')
@section('title', 'Order Detail — Arklen Agro')
@section('page-title', 'Order Detail')

@section('content')
<style>
/* ── layout ── */
.od-wrap{max-width:1000px;margin:0 auto;}

/* ── top bar ── */
.od-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
.btn-back{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border:1.5px solid #dde8cc;border-radius:9px;background:#fff;color:#555;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .15s;}
.btn-back:hover{background:#f5f5f5;}
.btn-invoice{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border:none;border-radius:9px;background:linear-gradient(135deg,#4b7c20,#6db82a);color:#fff;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;box-shadow:0 2px 8px rgba(75,124,32,.25);transition:all .15s;}
.btn-invoice:hover{box-shadow:0 4px 14px rgba(75,124,32,.35);transform:translateY(-1px);}

/* ── header card ── */
.od-head-card{background:#fff;border:1px solid #e8f0dc;border-radius:14px;padding:22px 26px;margin-bottom:16px;}
.od-order-id{font-size:20px;font-weight:800;color:#1e2a14;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.od-placed{font-size:12px;color:#aaa;margin-top:4px;}

/* ── status badge ── */
.badge-completed{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:3px 11px;border-radius:20px;font-size:12px;font-weight:700;}
.badge-pending{background:#fef9c3;color:#854d0e;border:1px solid #fde68a;padding:3px 11px;border-radius:20px;font-size:12px;font-weight:700;}
.badge-processing{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe;padding:3px 11px;border-radius:20px;font-size:12px;font-weight:700;}
.badge-delivered{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:3px 11px;border-radius:20px;font-size:12px;font-weight:700;}
.badge-cancelled{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:3px 11px;border-radius:20px;font-size:12px;font-weight:700;}

/* ── stats row ── */
.od-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:0;border-top:1px solid #f0f5ea;margin-top:18px;}
@media(max-width:560px){.od-stats{grid-template-columns:1fr;}}
.od-stat{padding:16px 20px;}
.od-stat:not(:last-child){border-right:1px solid #f0f5ea;}
@media(max-width:560px){.od-stat:not(:last-child){border-right:none;border-bottom:1px solid #f0f5ea;}}
.od-stat-label{font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
.od-stat-val{font-size:26px;font-weight:800;color:#1e2a14;line-height:1.1;}
.od-stat-val.green{color:#3a6110;}

/* ── line items card ── */
.od-items-card{background:#fff;border:1px solid #e8f0dc;border-radius:14px;overflow:hidden;}
.od-items-title{font-size:15px;font-weight:700;color:#1e2a14;padding:18px 22px 14px;border-bottom:1px solid #f0f5ea;}
.od-table{width:100%;border-collapse:collapse;}
.od-table thead tr{background:#f7fbf2;}
.od-table th{font-size:11px;font-weight:700;color:#7a9a50;text-transform:uppercase;letter-spacing:.06em;padding:10px 16px;text-align:left;border-bottom:1px solid #eef5e4;white-space:nowrap;}
.od-table th:last-child{text-align:right;}
.od-table td{padding:13px 16px;font-size:13px;color:#333;border-bottom:1px solid #f8f8f8;vertical-align:middle;}
.od-table tbody tr:last-child td{border-bottom:none;}
.od-table tbody tr:hover td{background:#fafff5;}
.od-table td:last-child{text-align:right;font-weight:700;color:#3a6110;}
.od-table .sno{color:#bbb;font-weight:700;font-size:12px;}
.leg-badge-left{display:inline-block;background:#EAF3DE;color:#3a6110;border:1px solid #c0dd97;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.leg-badge-right{display:inline-block;background:#fff3e0;color:#b45309;border:1px solid #fcd59a;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;}

/* ── grand total row ── */
.od-grand-row td{padding:12px 16px;background:#f7fbf2;font-size:13px;}
.od-grand-label{font-weight:700;color:#555;text-align:right;}
.od-grand-val{font-size:16px;font-weight:800;color:#3a6110;text-align:right;}
</style>

<div class="od-wrap">

    {{-- top bar --}}
    <div class="od-topbar">
        <a href="{{ route('orders.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
        <a href="{{ route('orders.invoice', $order) }}" class="btn-invoice" target="_blank">
            <i class="fas fa-download"></i> Download Invoice
        </a>
    </div>

    {{-- header card --}}
    <div class="od-head-card">
        <div class="od-order-id">
            Order #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
            <span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
        </div>
        <div class="od-placed">
            Placed on {{ $order->order_date->format('d/m/Y') }}
            @if($order->created_at)
                at {{ $order->created_at->format('h:i A') }}
            @endif
        </div>

        <div class="od-stats">
            <div class="od-stat">
                <div class="od-stat-label">Total Amount</div>
                <div class="od-stat-val green">₹{{ number_format($order->order_value, 0) }}</div>
            </div>
            <div class="od-stat">
                <div class="od-stat-label">Total Quantity</div>
                <div class="od-stat-val">
                   {{ $order->subOrders->count() ?: $order->order_quantity }} Units
                </div>
            </div>
            <div class="od-stat">
                <div class="od-stat-label">Punched By</div>
                <div class="od-stat-val" style="font-size:18px;">
                    {{ $order->member->full_name }} ({{ $order->member->seller_id }})
                </div>
            </div>
        </div>
    </div>

    {{-- line items --}}
    <div class="od-items-card">
        <div class="od-items-title">Order Line Items</div>
        <div style="overflow-x:auto;">
            <table class="od-table">
                <thead>
                    <tr>
                        <th style="width:48px;">S.No</th>
                        <th>Name</th>
                        <th>Aadhar</th>
                        <th>Mobile No</th>
                        <th>Sponsor ID</th>
                        <th>Leg</th>
                        <th style="text-align:right;">Purchase Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->subOrders as $i => $sub)
                    <tr>
                        <td class="sno">{{ $i + 1 }}</td>
                        <td style="font-weight:600;">{{ $sub->name }}</td>
                        <td style="font-family:monospace;font-size:12px;">{{ $sub->aadhar ?? '—' }}</td>
                        <td>{{ $sub->mobile ?? '—' }}</td>
                        <td>
                            <span style="font-weight:600;color:#1a56b0;">{{ $sub->sponsor_id ?? '—' }}</span>
                        </td>
                        <td>
                            @if($sub->leg === 'left')
                                <span class="leg-badge-left">◀ Left</span>
                            @elseif($sub->leg === 'right')
                                <span class="leg-badge-right">▶ Right</span>
                            @else
                                <span style="color:#aaa;">—</span>
                            @endif
                        </td>
                        <td>₹{{ number_format($sub->amount, 0) }}</td>
                    </tr>
                    @empty
                    {{-- fallback: single order with no sub-orders --}}
                    <tr>
                        <td class="sno">1</td>
                        <td style="font-weight:600;">{{ $order->member->full_name }}</td>
                        <td style="font-family:monospace;font-size:12px;">
                            {{ $order->member->aadhar_no ?? '—' }}
                        </td>
                        <td>{{ $order->member->contact ?? '—' }}</td>
                        <td>
                            <span style="font-weight:600;color:#1a56b0;">
                                {{ $order->member->sponsor_id ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if($order->member->position === 'left')
                                <span class="leg-badge-left">◀ Left</span>
                            @elseif($order->member->position === 'right')
                                <span class="leg-badge-right">▶ Right</span>
                            @else
                                <span style="color:#aaa;">—</span>
                            @endif
                        </td>
                        <td>₹{{ number_format($order->order_value, 0) }}</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="od-grand-row">
                        <td colspan="6" class="od-grand-label">Grand Total:</td>
                        <td class="od-grand-val">₹{{ number_format($order->order_value, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection