@extends('layouts.app')
@section('title', 'Orders — Arklen Agro')
@section('page-title', 'Order List')

@section('content')
<style>
.form-control{ width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box; }
.form-control:focus{ border-color:#4b7c20; }
.modal-btn{ padding:10px 22px; border:none; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; }
.add-btn{ background:var(--green-700); color:#fff; }
.close-btn{ background:#e5e5e5; color:#333; }
.action-btns{ display:flex; gap:6px; }
.action-btn{ display:inline-flex; align-items:center; gap:4px; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none; border:none; cursor:pointer; }
.action-btn-edit{ background:#EAF3DE; color:var(--green-800); border:1px solid #c0dd97; }
.action-btn-action{ background:#fff3e0; color:#b45309; border:1px solid #fcd59a; }
.badge-pending{ background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
.badge-processing{ background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; }
.badge-delivered{ background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
.badge-cancelled{ background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
</style>

<div class="card card-pad">
    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="margin:0;color:var(--green-800);"><i class="fas fa-box"></i> Order List</h3>
        <button onclick="document.getElementById('addModal').style.display='flex'" class="modal-btn add-btn">
            <i class="fas fa-plus"></i> Create Order
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('orders.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name / order no" class="form-control" style="max-width:200px;">
       <div style="display:flex;align-items:center;gap:6px;">
    <input type="date" name="date_from" value="{{ request('date_from') }}" 
        class="form-control" style="max-width:160px;" title="From Date"
        placeholder="From Date">
    <span style="color:#888;font-size:13px;white-space:nowrap;">to</span>
    <input type="date" name="date_to" value="{{ request('date_to') }}" 
        class="form-control" style="max-width:160px;" title="To Date"
        placeholder="To Date">
</div>
        <input type="text" name="city" value="{{ request('city') }}" placeholder="City" class="form-control" style="max-width:140px;">
        <button type="submit" class="modal-btn add-btn">Search</button>
        @if(request()->anyFilled(['search','date_from','date_to','city']))
        <a href="{{ route('orders.index') }}" style="padding:10px 15px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Order No</th>
                    <th>Person Name</th>
                    <th>Contact</th>
                    <th>City</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $i => $o)
                <tr>
                    <td>{{ $orders->firstItem() + $i }}</td>
                    <td><span class="badge badge-green">{{ $o->order_no }}</span></td>
                    <td>{{ $o->member->full_name }}</td>
                    <td>{{ $o->member->contact }}</td>
                    <td>{{ $o->city }}</td>
                    <td>{{ $o->order_date->format('d M Y') }}</td>
                    <td><span class="badge badge-{{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('orders.edit', $o) }}" class="action-btn action-btn-edit"><i class="fas fa-pen"></i></a>
                            <a href="{{ route('orders.action', $o) }}" class="action-btn action-btn-action"><i class="fas fa-sliders-h"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:30px;color:#888;">No orders found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:15px;">{{ $orders->withQueryString()->links() }}</div>
</div>

{{-- CREATE ORDER MODAL --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:999;">
    <div style="background:#fff;width:500px;max-width:95vw;border-radius:16px;overflow-y:auto;max-height:92vh;">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid #eee;">
            <div>
                <h3 style="margin:0;color:var(--green-800);">Create Order</h3>
                <small style="color:#777;">Fill order details</small>
            </div>
            <button type="button" onclick="document.getElementById('addModal').style.display='none'" style="border:none;background:none;font-size:18px;cursor:pointer;">×</button>
        </div>
        <form method="POST" action="{{ route('orders.store') }}" style="padding:22px;">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div style="grid-column:span 2;">
                    <label class="form-label">Select Member *</label>
                    <select name="member_id" required class="form-control" style="height:38px;">
                        <option value="">-- Select Member --</option>
                        @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->full_name }} — {{ $m->seller_id }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-label">Order Product *</label>
                    <input type="text" name="order_product" value="{{ old('order_product') }}" required class="form-control">
                </div>
                <div>
                    <label class="form-label">Order Quantity *</label>
                    <input type="number" name="order_quantity" value="{{ old('order_quantity') }}" required min="1" class="form-control">
                </div>
                <div>
                    <label class="form-label">Order Value (₹) *</label>
                    <input type="number" name="order_value" value="{{ old('order_value') }}" required min="0" step="0.01" class="form-control">
                </div>
                <div>
                    <label class="form-label">Order Date *</label>
                    <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required class="form-control">
                </div>
                <div>
                    <label class="form-label">City *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required class="form-control">
                </div>
            </div>

            @if($errors->any())
            <div style="margin-top:15px;padding:10px;border-radius:8px;background:#ffe9e9;color:#d11;">
                {{ $errors->first() }}
            </div>
            @endif

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="modal-btn close-btn">Close</button>
                <button type="submit" class="modal-btn add-btn">Create</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
@if($errors->any())
document.getElementById('addModal').style.display = 'flex';
@endif
</script>
@endpush
@endsection