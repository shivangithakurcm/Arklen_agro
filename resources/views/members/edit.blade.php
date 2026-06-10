@extends('layouts.app')

@section('title', 'Edit Member — Arklen Agro')
@section('page-title', 'Edit Member')

@section('content')

{{-- Confirm Save Modal --}}
<div id="confirmModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:9999;">
    <div style="background:#fff;border-radius:16px;padding:30px;max-width:380px;width:90%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <div style="width:56px;height:56px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i class="fas fa-floppy-disk" style="font-size:24px;color:#d97706;"></i>
        </div>
        <h3 style="margin:0 0 8px;color:#1e2a14;font-size:17px;">Save Changes?</h3>
        <p style="color:#6b7280;font-size:13px;margin-bottom:24px;">Are you sure you want to save these changes?</p>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button type="button" onclick="document.getElementById('confirmModal').style.display='none'"
                    class="btn btn-secondary" style="min-width:100px;">
                Cancel
            </button>
            <button type="button" onclick="document.getElementById('memberForm').submit();"
                    class="btn btn-primary" style="min-width:100px;">
                Yes, Save
            </button>
        </div>
    </div>
</div>

<div class="card card-pad" style="max-width:700px;margin:0 auto;">

    <div style="margin-bottom:20px;">
        <h3 style="margin:0;color:var(--green-800);"><i class="fas fa-user-edit"></i> Edit Member</h3>
        <small style="color:#777;">Update member details</small>
    </div>

    <form id="memberForm" method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Profile Image --}}
        <div style="display:flex;justify-content:center;margin-bottom:25px;">
            <label for="profile_image_input" style="cursor:pointer;text-align:center;">
                <div id="imagePreview" style="width:90px;height:90px;border-radius:50%;border:2px dashed #9ac46b;background:#f4faee;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                    @if($member->profile_image)
                        <img src="{{ Storage::url($member->profile_image) }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        <i class="fas fa-camera" style="font-size:22px;color:#4b7c20;"></i>
                    @endif
                </div>
                <div style="margin-top:8px;font-size:12px;color:#777;">Upload Photo</div>
                <input type="file" id="profile_image_input" name="profile_image" accept="image/*" style="display:none;" onchange="previewImage(this)">
            </label>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
            <div>
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name', $member->first_name) }}" required class="form-control">
            </div>
            <div>
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" value="{{ old('last_name', $member->last_name) }}" required class="form-control">
            </div>
            <div>
                <label class="form-label">Contact *</label>
                <input type="text" name="contact" value="{{ old('contact', $member->contact) }}"
                    required class="form-control"
                    maxlength="10"
                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
            </div>
           <div>
    <label class="form-label">Seller ID</label>
    <input type="text" value="{{ $member->seller_id }}" disabled class="form-control" style="background:#f5f5f5;color:#666;font-weight:600;">
    <small style="color:#999;">cannot be changed</small>
</div>
           <div>
    <label class="form-label">Sponsor</label>
    <select name="sponsor_id" class="form-control" id="sponsorSelect">
        <option value="">-- Select Sponsor --</option>
        @foreach($sponsors as $sponsor)
            <option value="{{ $sponsor->seller_id }}"
                {{ old('sponsor_id', $member->sponsor_id) == $sponsor->seller_id ? 'selected' : '' }}>
                {{ $sponsor->seller_id }} — {{ $sponsor->first_name }} {{ $sponsor->last_name }}
            </option>
        @endforeach
    </select>
</div>
            <div>
                <label class="form-label">Position</label>
                <select name="position" class="form-control">
                    <option value="left"  {{ $member->position == 'left'  ? 'selected' : '' }}>Left</option>
                    <option value="right" {{ $member->position == 'right' ? 'selected' : '' }}>Right</option>
                </select>
            </div>
            <div>
                <label class="form-label">Date of Joining *</label>
                <input type="date" name="date_of_joining" value="{{ old('date_of_joining', $member->date_of_joining->format('Y-m-d')) }}" required class="form-control">
            </div>
            <div>
                <label class="form-label">Aadhar No</label>
                <input type="text" name="aadhar_no" value="{{ old('aadhar_no', $member->aadhar_no) }}"
                    class="form-control"
                    maxlength="12"
                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,12)">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $member->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$member->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div style="grid-column:span 2;">
                <label class="form-label">Address *</label>
                <textarea name="address" rows="3" required class="form-control" style="resize:none;">{{ old('address', $member->address) }}</textarea>
            </div>
        </div>

        @if($errors->any())
        <div style="margin-top:15px;padding:10px;border-radius:8px;background:#ffe9e9;color:#d11;">
            {{ $errors->first() }}
        </div>
        @endif

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px;">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
            <button type="button" onclick="document.getElementById('confirmModal').style.display='flex'" class="btn btn-primary">
                Update Member
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link  href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script>
$(document).ready(function() {
    $('#sponsorSelect').select2({
        placeholder: 'Search by ID or Name...',
        allowClear: true,
        width: '100%'
    });
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
</script>
@endpush

@endsection