@extends('layouts.app')
@section('title', 'Members — Arklen Agro')
@section('page-title', 'Members')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
/* ── Select2 theme ── */
.select2-container .select2-selection--single{height:38px!important;border:1px solid #dcdcdc!important;border-radius:8px!important;font-size:13px!important;font-family:inherit!important;}
.select2-container .select2-selection--single .select2-selection__rendered{line-height:38px!important;padding-left:12px!important;color:#1e2a14!important;}
.select2-container .select2-selection--single .select2-selection__arrow{height:36px!important;}
.select2-container--open .select2-selection--single{border-color:#4b7c20!important;box-shadow:0 0 0 3px rgba(109,184,42,.12)!important;}
.select2-dropdown{border:1px solid #dcdcdc!important;border-radius:8px!important;font-size:13px!important;font-family:inherit!important;box-shadow:0 4px 16px rgba(0,0,0,.1)!important;z-index:99999!important;}
.select2-search--dropdown .select2-search__field{border:1px solid #dcdcdc!important;border-radius:6px!important;padding:6px 10px!important;font-size:13px!important;font-family:inherit!important;outline:none!important;}
.select2-search--dropdown .select2-search__field:focus{border-color:#4b7c20!important;}
.select2-results__option--highlighted{background:var(--green-600,#569321)!important;}
.select2-results__option{padding:8px 12px!important;}
</style>
@endpush

@section('content')
<style>
/* ── Utilities ── */
.table-wrap{overflow-x:auto;}
.form-control{width:100%;padding:10px 12px;border:1px solid #dcdcdc;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;transition:border-color .15s;}
.form-control:focus{border-color:#4b7c20;box-shadow:0 0 0 3px rgba(109,184,42,.10);}
.form-control:disabled{background:#f5f5f5;color:#999;cursor:not-allowed;}
.form-label{display:block;margin-bottom:5px;font-size:12px;font-weight:600;color:#555;}
.modal-btn{padding:10px 22px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;}
.close-btn{background:#e5e5e5;color:#333;}
.add-btn{background:var(--green-700);color:#fff;}
.section-title{font-size:11px;font-weight:700;color:#777;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid #eee;}
.action-btns{display:flex;gap:6px;align-items:center;}
.action-btn{display:inline-flex;align-items:center;justify-content:center;gap:4px;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all .15s;white-space:nowrap;}
.action-btn-edit{background:#EAF3DE;color:var(--green-800);border:1px solid #c0dd97;}
.action-btn-edit:hover{background:#d4eab8;}
.action-btn-view{background:#e8f0fe;color:#1a56b0;border:1px solid #c3d4f8;}
.action-btn-view:hover{background:#d0e0fc;}
.action-btn-action{background:#fff3e0;color:#b45309;border:1px solid #fcd59a;}
.action-btn-action:hover{background:#ffe5b4;}

/* ── Earning toggle card ── */
.earn-toggle-row{display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f4faee;border:1px solid #c0dd97;border-radius:10px;margin-bottom:18px;}
.earn-toggle-row label{font-size:13px;font-weight:600;color:#3a6110;cursor:pointer;user-select:none;}
.earn-toggle-row input[type=checkbox]{width:16px;height:16px;accent-color:#4b7c20;cursor:pointer;}
.earn-section-label{display:flex;align-items:center;gap:7px;font-size:11px;font-weight:700;color:#777;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid #eee;}
.earn-section-label .earn-lock{font-size:12px;color:#aaa;}
.earn-section-label .earn-lock.unlocked{color:#4b7c20;}

/* ── Seller ID compact inline ── */
.sid-wrap{position:relative;}
.sid-wrap input{padding-right:60px;}
.sid-regen-btn{position:absolute;right:4px;top:50%;transform:translateY(-50%);padding:3px 8px;height:26px;border:1px solid #c0dd97;background:#EAF3DE;color:var(--green-800);border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:3px;line-height:1;}
.sid-regen-btn:hover{background:#d4eab8;}
</style>

<div class="card card-pad">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="margin:0;color:var(--green-800);"><i class="fas fa-users"></i> Member List</h3>
        <button onclick="document.getElementById('addModal').style.display='flex'" class="modal-btn add-btn">
            <i class="fas fa-user-plus"></i> Add Member
        </button>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('members.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <input type="text" name="sponsor_id" value="{{ request('sponsor_id') }}"
            placeholder="Search by Sponsor ID" class="form-control" style="max-width:220px;">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by name / contact" class="form-control" style="max-width:220px;">
        <button type="submit" class="btn btn-primary">Search</button>
        @if(request('sponsor_id') || request('search'))
            <a href="{{ route('members.index') }}" class="btn btn-secondary">Clear</a>
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
                    <th>Total Commission</th>
                    <th>Total Income</th>
                    <th>Joining Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $i => $m)
                <tr onclick="window.location='{{ route('members.show', $m) }}'" style="cursor:pointer;">
                    <td>{{ $members->firstItem() + $i }}</td>
                    <td>
                        @if($m->profile_image)
                            <img src="{{ Storage::url($m->profile_image) }}"
                                style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
                        @else
                            <div class="avatar">{{ $m->initials }}</div>
                        @endif
                    </td>
                    <td>{{ $m->full_name }}</td>
                    <td>{{ $m->contact }}</td>
                    <td><span class="badge badge-green">{{ $m->seller_id }}</span></td>
                    <td>
                        @if($m->sponsor_id)
                            <span style="font-size:13px;font-weight:600;color:#1e2a14;">{{ $m->sponsor_id }}</span>
                        @else
                            <span style="color:#aaa;">-</span>
                        @endif
                    </td>
                    <td>
                      @if($m->position)
    <span style="padding:3px 12px;border-radius:20px;font-size:12px;font-weight:600;
        background:{{ $m->position == 'left' ? '#e8f0fe' : '#fff3e0' }};
        color:{{ $m->position == 'left' ? '#1a56b0' : '#b45309' }};">
        {{ ucfirst($m->position) }}
    </span>
@else
    <span style="color:#aaa;">-</span>
@endif
                    </td>
                 <td>{{ number_format($m->bv_left, 0) }}</td>
<td>{{ number_format($m->bv_right, 0) }}</td>
<td>₹{{ number_format($m->direct_commission + $m->new_joinee_income + $m->level1_commission + $m->level2_commission, 2) }}</td>
                    <td>₹{{ number_format($m->total_income, 2) }}</td>
                    <td>{{ $m->date_of_joining->format('d M Y') }}</td>
                    <td onclick="event.stopPropagation();">
                        <div class="action-btns">
                            <a href="{{ route('members.edit', $m) }}" class="action-btn action-btn-edit" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="{{ route('members.show', $m) }}" class="action-btn action-btn-view" title="Profile">
                                <i class="fas fa-id-card"></i> Seller Profile
                            </a>
                            <a href="{{ route('members.action', $m) }}" class="action-btn action-btn-action" title="Action">
                                <i class="fas fa-sliders-h"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" style="text-align:center;padding:30px;color:#888;">No members found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:15px;">
        {{ $members->withQueryString()->links('vendor.pagination.custom') }}
    </div>
</div>

{{-- ══════════════════════════════════════════
     ADD MEMBER MODAL
══════════════════════════════════════════ --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:999;">
    <div style="background:#fff;width:740px;max-width:95vw;border-radius:16px;overflow-y:auto;max-height:92vh;">

        {{-- Modal header --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid #eee;position:sticky;top:0;background:#fff;z-index:10;">
            <div>
                <h3 style="margin:0;color:var(--green-800);">Add New Member</h3>
                <small style="color:#777;">Fill all required details</small>
            </div>
            <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                style="border:none;background:none;font-size:20px;cursor:pointer;line-height:1;">×</button>
        </div>

        <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data" style="padding:22px;">
            @csrf

            {{-- Profile photo --}}
            <div style="display:flex;justify-content:center;margin-bottom:24px;">
                <label for="profile_image_input" style="cursor:pointer;text-align:center;">
                    <div id="imagePreview"
                        style="width:90px;height:90px;border-radius:50%;border:2px dashed #9ac46b;background:#f4faee;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <i class="fas fa-camera" style="font-size:22px;color:#4b7c20;"></i>
                    </div>
                    <div style="margin-top:8px;font-size:12px;color:#777;">Upload Photo</div>
                    <input type="file" id="profile_image_input" name="profile_image" accept="image/*"
                        style="display:none;" onchange="previewImage(this)">
                </label>
            </div>

            {{-- ── Personal Information ── --}}
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
                    <input type="text" name="contact" value="{{ old('contact') }}" required class="form-control"
                        maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                </div>

                <div>
                    <label class="form-label">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="form-control" placeholder="e.g. Durg">
                </div>

                {{-- Seller ID --}}
                <div>
                    <label class="form-label">Seller ID <span style="font-weight:400;color:#bbb;font-size:10px;">auto-generated</span></label>
                    <div class="sid-wrap">
                        <input type="text"
                            id="sellerIdField"
                            name="seller_id"
                            value="{{ old('seller_id', \App\Models\Member::generateSellerId()) }}"
                            class="form-control"
                            style="font-weight:600;color:#1e2a14;">
                        <button type="button" class="sid-regen-btn" onclick="regenSellerId()" title="Generate new ID">
                            <i class="fas fa-rotate"></i> New
                        </button>
                    </div>
                </div>

                {{-- Sponsor ID --}}
                <div>
                    <label class="form-label">Sponsor ID</label>
                    <select name="sponsor_id" id="sponsor_select" class="form-control" style="width:100%;">
                        <option value="">---- Select Sponsor ----</option>
                        @foreach(\App\Models\Member::orderBy('first_name')->get() as $sponsor)
                            <option value="{{ $sponsor->seller_id }}"
                                {{ old('sponsor_id') == $sponsor->seller_id ? 'selected' : '' }}>
                                {{ $sponsor->full_name }} ({{ $sponsor->seller_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Sponsor Leg</label>
                    <select name="sponsor_leg" class="form-control" style="height:38px;">
                        <option value="left"  {{ old('sponsor_leg') == 'left'  ? 'selected' : '' }}>Left</option>
                        <option value="right" {{ old('sponsor_leg') == 'right' ? 'selected' : '' }}>Right</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Date of Joining *</label>
                    <input type="date" name="date_of_joining"
                        value="{{ old('date_of_joining', date('Y-m-d')) }}" required class="form-control">
                </div>

                <div>
                    <label class="form-label">Aadhar No</label>
                    <input type="text" name="aadhar_no" value="{{ old('aadhar_no') }}" class="form-control"
                        maxlength="12" autocomplete="new-password"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,12)">
                </div>

                <div>
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" required class="form-control" autocomplete="new-password">
                </div>

                <div>
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required class="form-control">
                </div>

                <div style="grid-column:span 2;">
                    <label class="form-label">Address *</label>
                    <textarea name="address" rows="3" required class="form-control"
                        style="resize:none;">{{ old('address') }}</textarea>
                </div>

            </div>{{-- /personal grid --}}

            {{-- ══════════════════════════════════════
                 MANUAL EARNINGS TOGGLE
            ══════════════════════════════════════ --}}
            <div style="margin-top:26px;">
                <div class="earn-toggle-row">
                    <input type="checkbox" id="addEarningManually" onchange="toggleEarningFields(this)">
                    <label for="addEarningManually">
                        <i class="fas fa-coins" style="margin-right:5px;color:#4b7c20;"></i>
                        Add Earning Manually
                    </label>
                    <span style="font-size:11px;color:#888;margin-left:auto;">Enable to fill BV &amp; Commission fields</span>
                </div>

                {{-- BV Section --}}
                <div class="earn-section-label">
                    <span>BV (Business Volume)</span>
                    <span class="earn-lock" id="bvLockIcon"><i class="fas fa-lock"></i></span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Left BV</label>
                        <input type="number" name="left_bv" value="{{ old('left_bv', 0) }}"
                            min="0" step="0.01" class="form-control earn-field" disabled>
                    </div>
                    <div>
                        <label class="form-label">Right BV</label>
                        <input type="number" name="right_bv" value="{{ old('right_bv', 0) }}"
                            min="0" step="0.01" class="form-field earn-field form-control" disabled>
                    </div>
                </div>

                {{-- Commission Section --}}
                <div class="earn-section-label">
                    <span>Commission</span>
                    <span class="earn-lock" id="commLockIcon"><i class="fas fa-lock"></i></span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                    <div>
                        <label class="form-label">Direct Commission</label>
                        <input type="number" name="direct_commission" value="{{ old('direct_commission', 0) }}"
                            min="0" step="0.01" class="form-control earn-field" disabled>
                    </div>
                    <div>
                        <label class="form-label">New Joinee Bonus</label>
                        <input type="number" name="new_joinee_bonus" value="{{ old('new_joinee_bonus', 0) }}"
                            min="0" step="0.01" class="form-control earn-field" disabled>
                    </div>
                    <div>
                        <label class="form-label">Level 1 Commission</label>
                        <input type="number" name="level1_commission" value="{{ old('level1_commission', 0) }}"
                            min="0" step="0.01" class="form-control earn-field" disabled>
                    </div>
                    <div>
                        <label class="form-label">Level 2 Commission</label>
                        <input type="number" name="level2_commission" value="{{ old('level2_commission', 0) }}"
                            min="0" step="0.01" class="form-control earn-field" disabled>
                    </div>
                </div>
            </div>{{-- /earnings section --}}

            {{-- Validation errors --}}
            @if($errors->any())
            <div style="margin-top:15px;padding:12px 15px;border-radius:8px;background:#fff5f5;border:1px solid #fed7d7;color:#c53030;">
                <div style="font-weight:600;margin-bottom:6px;font-size:13px;">⚠ Please fix the following:</div>
                @foreach($errors->all() as $error)
                <div style="font-size:12px;margin-top:3px;">• {{ $error }}</div>
                @endforeach
            </div>
            @endif

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                    class="modal-btn close-btn">Close</button>
                <button type="submit" class="modal-btn add-btn">
                    <i class="fas fa-user-plus" style="margin-right:5px;"></i> Add Member
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
/* ── Select2 init ── */
$(document).ready(function() {
    $('#sponsor_select').select2({
        placeholder: '---- Select Sponsor ----',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#addModal')
    });
});

/* ── Profile image preview ── */
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML =
                `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* ── Seller ID regen ── */
function regenSellerId() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let id = 'SL';
    for (let i = 0; i < 4; i++) {
        id += Math.floor(Math.random() * 10);
    }
    document.getElementById('sellerIdField').value = id;
}

/* ── Manual earning toggle ── */
function toggleEarningFields(checkbox) {
    const fields   = document.querySelectorAll('.earn-field');
    const bvIcon   = document.getElementById('bvLockIcon');
    const commIcon = document.getElementById('commLockIcon');
    const enabled  = checkbox.checked;

    fields.forEach(function(f) {
        f.disabled = !enabled;
        if (enabled) {
            f.style.background = '#fff';
            f.style.color      = '#1e2a14';
        } else {
            f.style.background = '';
            f.style.color      = '';
        }
    });

    const lockHtml   = '<i class="fas fa-lock"></i>';
    const unlockHtml = '<i class="fas fa-lock-open"></i>';
    bvIcon.innerHTML   = enabled ? unlockHtml : lockHtml;
    commIcon.innerHTML = enabled ? unlockHtml : lockHtml;
    bvIcon.classList.toggle('unlocked', enabled);
    commIcon.classList.toggle('unlocked', enabled);
}

/* ── Re-open modal on validation error ── */
@if($errors->any())
    document.getElementById('addModal').style.display = 'flex';
    const hasEarnings = {{ (old('left_bv') || old('right_bv') || old('direct_commission') || old('new_joinee_bonus') || old('level1_commission') || old('level2_commission')) ? 'true' : 'false' }};
    if (hasEarnings) {
        const cb = document.getElementById('addEarningManually');
        cb.checked = true;
        toggleEarningFields(cb);
    }
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