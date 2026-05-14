@extends('layouts.app')
@section('title', 'Edit Order — Arklen Agro')
@section('page-title', 'Edit Order')

@section('content')
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
            <div style="grid-column:span 2;">
                <label class="form-label">Order Product *</label>
                <input type="text" name="order_product" value="{{ $order->order_product }}" required class="form-control">
            </div>
            <div>
                <label class="form-label">Order Quantity *</label>
                <input type="number" name="order_quantity" value="{{ $order->order_quantity }}" required min="1" class="form-control">
            </div>
            <div>
                <label class="form-label">Order Value (₹) *</label>
                <input type="number" name="order_value" value="{{ $order->order_value }}" required min="0" step="0.01" class="form-control">
            </div>
            <div>
                <label class="form-label">Order Date *</label>
                <input type="date" name="order_date" value="{{ $order->order_date->format('Y-m-d') }}" required class="form-control">
            </div>
            <div>
                <label class="form-label">City *</label>
                <input type="text" name="city" value="{{ $order->city }}" required class="form-control">
            </div>
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
@endsection