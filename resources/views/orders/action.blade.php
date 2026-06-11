@extends('layouts.app')

@section('title', 'Order Action')
@section('page-title', 'Order Action')

@section('content')

<style>
.form-card{
    background:#fff;
    border-radius:12px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.form-control{
    width:100%;
    padding:10px 12px;
    border:1px solid #ddd;
    border-radius:8px;
    font-size:14px;
}

.form-label{
    display:block;
    margin-bottom:6px;
    font-weight:600;
    color:#555;
}

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.btn-save{
    background:#4b7c20;
    color:#fff;
    border:none;
    padding:10px 25px;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}

.btn-back{
    background:#eee;
    color:#333;
    padding:10px 20px;
    border-radius:8px;
    text-decoration:none;
}

.order-info{
    background:#f8faf5;
    border:1px solid #dce9c9;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

@media(max-width:768px){
    .grid{
        grid-template-columns:1fr;
    }
}
</style>

<div class="card card-pad">

    {{-- Order Info --}}
    <div class="order-info">
        <h4 style="margin-bottom:10px;">
            Order #{{ $order->order_no }}
        </h4>

        <p><strong>Product:</strong> {{ $order->order_product }}</p>
        <p><strong>Quantity:</strong> {{ $order->order_quantity }}</p>
        <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_value,2) }}</p>
        <p><strong>Date:</strong> {{ $order->order_date->format('d M Y') }}</p>
    </div>

    <div class="form-card">

        <h3 style="margin-top:0;color:#3a6110;">
            Member Details
        </h3>

        <form method="POST" action="{{ route('members.update',$member->id) }}">
            @csrf
            @method('PUT')

            <div class="grid">

                <div>
                    <label class="form-label">First Name</label>
                    <input type="text"
                           name="first_name"
                           class="form-control"
                           value="{{ $member->first_name }}">
                </div>

                <div>
                    <label class="form-label">Last Name</label>
                    <input type="text"
                           name="last_name"
                           class="form-control"
                           value="{{ $member->last_name }}">
                </div>

                <div>
                    <label class="form-label">Contact</label>
                    <input type="text"
                           name="contact"
                           class="form-control"
                           value="{{ $member->contact }}">
                </div>

                <div>
                    <label class="form-label">Seller ID</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $member->seller_id }}"
                           readonly>
                </div>

                <div>
                    <label class="form-label">Sponsor ID</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $member->sponsor_id }}"
                           readonly>
                </div>

                <div>
                    <label class="form-label">City</label>
                    <input type="text"
                           name="city"
                           class="form-control"
                           value="{{ $member->city }}">
                </div>

                <div>
                    <label class="form-label">Date Of Joining</label>
                    <input type="date"
                           name="date_of_joining"
                           class="form-control"
                           value="{{ optional($member->date_of_joining)->format('Y-m-d') }}">
                </div>

                <div>
                    <label class="form-label">Aadhar No</label>
                    <input type="text"
                           name="aadhar_no"
                           class="form-control"
                           value="{{ $member->aadhar_no }}">
                </div>

                <div style="grid-column:span 2;">
                    <label class="form-label">Address</label>
                    <textarea name="address"
                              rows="3"
                              class="form-control">{{ $member->address }}</textarea>
                </div>

            </div>

            <div style="margin-top:25px;display:flex;gap:10px;">
                <a href="{{ route('orders.index') }}"
                   class="btn-back">
                    Back
                </a>

                <button type="submit"
                        class="btn-save">
                    Save Changes
                </button>
            </div>

        </form>

    </div>

</div>

@endsection