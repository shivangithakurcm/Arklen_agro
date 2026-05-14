@extends('layouts.app')
@section('title', 'Order Action — Arklen Agro')
@section('page-title', 'Order Action')

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('orders.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;font-size:13px;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card card-pad" style="max-width:400px;">
    <h3 style="margin:0 0 6px;color:var(--green-800);">Update Status</h3>
    <p style="color:#888;font-size:13px;margin-bottom:20px;">{{ $order->order_no }} — {{ $order->member->full_name }}</p>

    <form method="POST" action="{{ route('orders.action.update', $order) }}">
        @csrf @method('PUT')
        <div style="margin-bottom:20px;">
            <label class="form-label">Status *</label>
            <select name="status" required class="form-control" style="height:38px;">
                @foreach(['pending','processing','delivered','cancelled'] as $s)
                <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;">
            <a href="{{ route('orders.index') }}" style="padding:10px 22px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;font-size:13px;font-weight:600;">Close</a>
            <button type="submit" style="padding:10px 22px;border:none;border-radius:8px;background:var(--green-700);color:#fff;font-size:13px;font-weight:600;cursor:pointer;">Update</button>
        </div>
    </form>
</div>
@endsection