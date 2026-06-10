@extends('layouts.app')
@section('title', 'Edit Order — Arklen Agro')
@section('page-title', 'Edit Order')

@section('content')
<style>
.form-control{ width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box; }
.form-control:focus{ border-color:#4b7c20; }
</style>

<div style="margin-bottom:16px;">
    <a href="{{ route('orders.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;font-size:13px;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card card-pad" style="max-width:600px;">
    <h3 style="margin:0 0 20px;color:var(--green-800);">Edit Order — {{ $order->order_no }}</h3>

    <form method="POST" action="{{ route('orders.update', $order) }}">
        @csrf @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">

            {{-- Member --}}
            <div style="grid-column:span 2;">
                <label class="form-label">Select Member *</label>
                <select name="member_id" required class="form-control" style="height:38px;">
                    @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ $order->member_id == $m->id ? 'selected' : '' }}>
                        {{ $m->full_name }} — {{ $m->seller_id }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Product Dropdown --}}
            <div style="grid-column:span 2;">
                <label class="form-label">Order Product *</label>
                <select name="product_id" required class="form-control" style="height:38px;" onchange="fillPrice(this)">
                    <option value="">-- Select Product --</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}"
                        data-price="{{ $p->product_price }}"
                        {{ $order->product_id == $p->id ? 'selected' : '' }}>
                        {{ $p->product_name }} — ₹{{ number_format($p->product_price, 2) }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Quantity --}}
            <div>
                <label class="form-label">Order Quantity *</label>
                <input type="number" name="order_quantity" value="{{ $order->order_quantity }}" required min="1" class="form-control">
            </div>

            {{-- Order Value --}}
            <div>
                <label class="form-label">Order Value (₹) *</label>
                <input type="number" name="order_value" id="orderValue" value="{{ $order->order_value }}" required min="0" step="0.01" class="form-control">
            </div>

            {{-- Date --}}
            <div>
                <label class="form-label">Order Date *</label>
                <input type="date" name="order_date" value="{{ $order->order_date->format('Y-m-d') }}" required class="form-control">
            </div>

            {{-- City --}}
            <div>
                <label class="form-label">City *</label>
                <input type="text" name="city" value="{{ $order->city }}" required class="form-control">
            </div>

            {{-- Status --}}
            <div style="grid-column:span 2;">
                <label class="form-label">Status *</label>
                <select name="status" required class="form-control" style="height:38px;">
                    @foreach(['pending','processing','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px;">
            <a href="{{ route('orders.index') }}" style="padding:10px 22px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;font-size:13px;font-weight:600;">Close</a>
            <button type="submit" style="padding:10px 22px;border:none;border-radius:8px;background:var(--green-700);color:#fff;font-size:13px;font-weight:600;cursor:pointer;">Update Order</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function fillPrice(select) {
    const price = select.options[select.selectedIndex].dataset.price;
    if (price) {
        document.getElementById('orderValue').value = price;
    }
}
</script>
@endpush

@endsection