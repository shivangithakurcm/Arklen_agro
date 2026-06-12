@extends('layouts.app')
@section('title', 'Order Action')
@section('page-title', 'Order Action')

@section('content')
<style>
.form-control{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;}
.form-control:focus{border-color:#4b7c20;box-shadow:0 0 0 3px rgba(75,124,32,.10);}
.form-label{display:block;margin-bottom:6px;font-weight:600;color:#555;font-size:13px;}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;}
.btn-save{background:#4b7c20;color:#fff;border:none;padding:10px 25px;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;}
.btn-save:hover{background:#3a6110;}
.btn-back{background:#eee;color:#333;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;}
.order-info{background:#f8faf5;border:1px solid #dce9c9;padding:15px;border-radius:10px;margin-bottom:20px;}
.alert-success{padding:12px 16px;background:#dcfce7;border:1px solid #bbf7d0;border-radius:8px;color:#166534;font-size:13px;font-weight:600;margin-bottom:16px;}
.alert-error{padding:12px 16px;background:#fff5f5;border:1px solid #fecaca;border-radius:8px;color:#c53030;font-size:13px;margin-bottom:16px;}
@media(max-width:768px){.grid{grid-template-columns:1fr;}}
</style>

<div class="card card-pad">

    {{-- ✅ Global success/error message --}}
   
    @if($errors->any())
    <div class="alert-error">
        <strong>⚠ Error:</strong> {{ $errors->first() }}
    </div>
    @endif

    {{-- Order Info --}}
    <div class="order-info">
        <h4 style="margin-bottom:10px;">Order #{{ $order->order_no }}</h4>
        <p><strong>Product:</strong> {{ $order->order_product }}</p>
        <p><strong>Quantity:</strong> {{ $order->order_quantity }}</p>
        <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_value, 2) }}</p>
        <p><strong>Date:</strong> {{ $order->order_date->format('d M Y') }}</p>
    </div>

    {{-- SubOrders Status Update --}}
    @if($order->subOrders->count() > 0)
    <div style="margin-bottom:24px;border:1px solid #e8f0dc;border-radius:12px;overflow:hidden;">
        <div style="background:#f4faee;padding:12px 18px;font-weight:700;color:#3a6110;font-size:14px;border-bottom:1px solid #e8f0dc;">
            Order Members — Status Update
        </div>
        <div style="padding:16px 18px;">
            <form method="POST" action="{{ route('orders.action.update', $order) }}">
                @csrf
                @method('PUT')
                <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                    <label class="form-label" style="margin:0;">Order Status:</label>
                    <select name="status" class="form-control" style="max-width:200px;">
                        @foreach(['pending','processing','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-save">Update Status</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- New Member Registration --}}
    <div style="font-size:15px;font-weight:700;color:#1e2a14;margin-bottom:16px;">
        <i class="fas fa-user-plus"></i> Register Member from this Order
    </div>

    @forelse($order->subOrders as $sub)
    <div style="border:1px solid #e8f0dc;border-radius:12px;margin-bottom:18px;overflow:hidden;">

        @php
            $alreadyRegistered = false;
            if (!empty($sub->aadhar) && !empty($sub->mobile)) {
                $alreadyRegistered = \App\Models\Member::where('aadhar_no', $sub->aadhar)
                                        ->where('contact', $sub->mobile)->exists();
            } elseif (!empty($sub->aadhar)) {
                $alreadyRegistered = \App\Models\Member::where('aadhar_no', $sub->aadhar)->exists();
            } elseif (!empty($sub->mobile)) {
                $alreadyRegistered = \App\Models\Member::where('contact', $sub->mobile)->exists();
            }
        @endphp

        <div style="background:#f4faee;padding:10px 18px;font-weight:700;color:#3a6110;font-size:13px;border-bottom:1px solid #e8f0dc;">
            Row {{ $loop->iteration }} — {{ $sub->name }}
            @if($alreadyRegistered)
                <span style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:2px 10px;border-radius:20px;font-size:11px;margin-left:8px;">
                    ✅ Already Registered
                </span>
            @endif
        </div>

        <div style="padding:18px;">
            @if(!$alreadyRegistered)
            <form method="POST" action="{{ route('members.store') }}">
                @csrf
                <input type="hidden" name="from_order" value="1">

                <div class="grid">
                    <div>
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control"
                               value="{{ old('first_name', explode(' ', $sub->name)[0] ?? '') }}" required>
                    </div>
                    <div>
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control"
                               value="{{ old('last_name', implode(' ', array_slice(explode(' ', $sub->name), 1))) }}">
                    </div>
                    <div>
                        <label class="form-label">Mobile *</label>
                        <input type="text" name="contact" class="form-control"
                               value="{{ old('contact', $sub->mobile) }}" required maxlength="10">
                    </div>
                    <div>
                        <label class="form-label">Aadhar No</label>
                        <input type="text" name="aadhar_no" class="form-control"
                               value="{{ old('aadhar_no', $sub->aadhar) }}" maxlength="12">
                    </div>
                    <div>
                        <label class="form-label">Sponsor ID</label>
                        <input type="text" name="sponsor_id" class="form-control"
                               value="{{ old('sponsor_id', $sub->sponsor_id) }}">
                    </div>
                    <div>
                        <label class="form-label">Leg (Position)</label>
                        <select name="sponsor_leg" class="form-control">
                            <option value="left"  {{ $sub->leg === 'left'  ? 'selected' : '' }}>◀ Left</option>
                            <option value="right" {{ $sub->leg === 'right' ? 'selected' : '' }}>▶ Right</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Date of Joining</label>
                        <input type="date" name="date_of_joining" class="form-control"
                               value="{{ old('date_of_joining', $order->order_date->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control"
                               value="{{ old('city') }}" placeholder="City">
                    </div>
                    <div>
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Min 4 characters" required>
                    </div>
                    <div>
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Re-enter password" required>
                    </div>
                    <div style="grid-column:span 2;">
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="2" class="form-control">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div style="margin-top:16px;">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-user-plus"></i> Register Member
                    </button>
                </div>
            </form>
            @else
                <p style="color:#888;font-size:13px;margin:0;">
                    This member is already registered — no need to register again.
                </p>
            @endif
        </div>
    </div>
    @empty
        <p style="color:#888;">No sub-orders found for this order.</p>
    @endforelse

    <div style="margin-top:10px;">
        <a href="{{ route('orders.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

</div>
@endsection