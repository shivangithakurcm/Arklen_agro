@extends('layouts.app')

@section('title', 'Dashboard — Arklen Agro')
@section('page-title', 'Dashboard')

@section('content')

<style>
.table-wrap{ overflow-x:auto; }
.form-control{ width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box; }
.form-control:focus{ border-color:#4b7c20; }
.form-label{ display:block; margin-bottom:5px; font-size:12px; font-weight:600; color:#555; }
.modal-btn{ padding:10px 22px; border:none; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; }
.close-btn{ background:#e5e5e5; color:#333; }
.add-btn{ background:var(--green-700); color:#fff; }
.section-title{ font-size:11px; font-weight:700; color:#777; text-transform:uppercase; letter-spacing:.08em; margin-bottom:12px; padding-bottom:6px; border-bottom:1px solid #eee; }
.action-btns{ display:flex; gap:6px; align-items:center; }
.action-btn{ display:inline-flex; align-items:center; justify-content:center; gap:4px; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none; border:none; cursor:pointer; transition:all .15s; white-space:nowrap; }
.action-btn-edit{ background:#EAF3DE; color:var(--green-800); border:1px solid #c0dd97; }
.action-btn-edit:hover{ background:#d4eab8; }
.action-btn-view{ background:#e8f0fe; color:#1a56b0; border:1px solid #c3d4f8; }
.action-btn-view:hover{ background:#d0e0fc; }
.action-btn-action{ background:#fff3e0; color:#b45309; border:1px solid #fcd59a; }
.action-btn-action:hover{ background:#ffe5b4; }
</style>

<div class="card card-pad">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="margin:0;color:var(--green-800);"><i class="fas fa-users"></i> Member List</h3>
        <button onclick="document.getElementById('addModal').style.display='flex'" class="modal-btn add-btn">
            <i class="fas fa-user-plus"></i> Add Member
        </button>
    </div>

    <form method="GET" action="{{ route('dashboard') }}" style="display:flex;gap:10px;margin-bottom:20px;">
        <input type="text" name="sponsor_id" value="{{ request('sponsor_id') }}"
            placeholder="Search by Sponsor ID" class="form-control" style="max-width:250px;">
        <button type="submit" class="modal-btn add-btn">Search</button>
        @if(request('sponsor_id'))
        <a href="{{ route('dashboard') }}" style="padding:10px 15px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;">Clear</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Seller ID</th>
                    <th>Sponsor ID</th>
                    <th>Position</th>
                    <th>Left</th>
                    <th>Right</th>
                    <th>Total Income</th>
                    <th>Joining Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $i => $m)
                <tr>
                    <td>{{ $members->firstItem() + $i }}</td>
                    <td>
                        @if($m->profile_image)
                        <img src="{{ Storage::url($m->profile_image) }}" style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
                        @else
                        <div class="avatar">{{ $m->initials }}</div>
                        @endif
                    </td>
                    <td>{{ $m->full_name }}</td>
                    <td>{{ $m->contact }}</td>
                    <td><span class="badge badge-green">{{ $m->seller_id }}</span></td>
                    <td>{{ $m->sponsor_id ?? '-' }}</td>
                    <td><span class="badge {{ $m->position == 'left' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($m->position) }}</span></td>
                    <td>{{ count($m->leftMembers()) }}</td>
                    <td>{{ count($m->rightMembers()) }}</td>
                    <td>₹{{ number_format($m->total_income,2) }}</td>
                    <td>{{ $m->date_of_joining->format('d M Y') }}</td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('members.edit',$m) }}" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                            <a href="{{ route('members.show',$m) }}" class="action-btn action-btn-view" title="Seller Profile"><i class="fas fa-id-card"></i> Seller Profile</a>
                            <a href="{{ route('members.action',$m) }}" class="action-btn action-btn-action" title="Action"><i class="fas fa-sliders-h"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="text-align:center;padding:30px;color:#888;">No members found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:15px;">{{ $members->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>

{{-- ADD MEMBER MODAL --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:999;">
    <div style="background:#fff;width:700px;max-width:95vw;border-radius:16px;overflow-y:auto;max-height:92vh;">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid #eee;">
            <div>
                <h3 style="margin:0;color:var(--green-800);">Add New Member</h3>
                <small style="color:#777;">Fill all required details</small>
            </div>
            <button type="button" onclick="document.getElementById('addModal').style.display='none'" style="border:none;background:none;font-size:18px;cursor:pointer;">×</button>
        </div>

        <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data" style="padding:22px;">
            @csrf

            <div style="display:flex;justify-content:center;margin-bottom:25px;">
                <label for="profile_image_input" style="cursor:pointer;text-align:center;">
                    <div id="imagePreview" style="width:90px;height:90px;border-radius:50%;border:2px dashed #9ac46b;background:#f4faee;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <i class="fas fa-camera" style="font-size:22px;color:#4b7c20;"></i>
                    </div>
                    <div style="margin-top:8px;font-size:12px;color:#777;">Upload Photo</div>
                    <input type="file" id="profile_image_input" name="profile_image" accept="image/*" style="display:none;" onchange="previewImage(this)">
                </label>
            </div>

            <div class="section-title">Personal Information</div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="form-control">
                </div>
                <div>
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="form-control">
                </div>
               <div>
    <label class="form-label">Contact *</label>
    <input type="text" name="contact" value="{{ old('contact') }}" 
        required class="form-control"
        maxlength="10"
        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
</div>
                <div>
    <label class="form-label">Seller ID *</label>
    <input type="text" 
           name="seller_id" 
           value="{{ \App\Models\Member::generateSellerId() }}"
           readonly
           class="form-control" 
           style="background:#f5f5f5;color:#666;font-weight:600;cursor:not-allowed;">
    <small style="color:#999;">Auto generated</small>
</div>
                <div>
                    <label class="form-label">Sponsor ID</label>
                    <select name="sponsor_id" id="sponsor_select" class="form-control" style="width:100%;">
                        <option value="">---- Select Sponsor ----</option>
                        @foreach($allMembers as $m)
                        <option value="{{ $m->seller_id }}" {{ old('sponsor_id') == $m->seller_id ? 'selected' : '' }}>
                            {{ $m->seller_id }} — {{ $m->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Sponsor Leg</label>
                    <select name="sponsor_leg" class="form-control">
                        <option value="left" {{ old('sponsor_leg') == 'left' ? 'selected' : '' }}>Left</option>
                        <option value="right" {{ old('sponsor_leg') == 'right' ? 'selected' : '' }}>Right</option>
                    </select>
                </div>
                <div>
    <label class="form-label">Product *</label>
    <select name="product_id" required class="form-control" style="height:38px;">
        <option value="">-- Select Product --</option>
        @foreach($products as $p)
        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
            {{ $p->product_name }} — ₹{{ number_format($p->product_price, 2) }}
        </option>
        @endforeach
    </select>
</div>
                <div>
                    <label class="form-label">Date of Joining *</label>
                    <input type="date" name="date_of_joining" value="{{ old('date_of_joining', date('Y-m-d')) }}" required class="form-control">
                </div>
               <div>
    <label class="form-label">Aadhar No</label>
    <input type="text" name="aadhar_no" value="{{ old('aadhar_no') }}" 
        class="form-control"
        maxlength="12"
        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,12)">
</div>
                <div>
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <div>
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required class="form-control">
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-label">Address *</label>
                    <textarea name="address" rows="3" required class="form-control" style="resize:none;">{{ old('address') }}</textarea>
                </div>
            </div>

           @if($errors->any())
<div style="margin-top:15px;padding:12px 15px;border-radius:8px;background:#fff5f5;border:1px solid #fed7d7;color:#c53030;">
    <div style="font-weight:600;margin-bottom:6px;font-size:13px;">⚠ Please fix the following:</div>
    @foreach($errors->all() as $error)
    <div style="font-size:12px;margin-top:3px;">• {{ $error }}</div>
    @endforeach
</div>
@endif
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="modal-btn close-btn">Close</button>
                <button type="submit" class="modal-btn add-btn">Add Member</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>

$('#sponsor_select').select2({
    placeholder: '---- Select Sponsor ----',
    allowClear: true,
    width: '100%',
    dropdownParent: $('#addModal')
});   
function previewImage(input) {
    if(input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML =
            `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

@if($errors->any())
document.getElementById('addModal').style.display = 'flex';
@foreach($errors->keys() as $field)
(function() {
    var el = document.querySelector('[name="{{ $field }}"]');
    if (el) {
        el.style.borderColor = '#e53e3e';
        el.style.background  = '#fff5f5';
    }
})();
@endforeach
@endif
</script>
@endpush
@endsection