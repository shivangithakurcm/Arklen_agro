@extends('layouts.app')

@section('title', 'Cities — 2APL Marketing')
@section('page-title', 'City')

@section('content')

{{-- Add City Modal --}}
<div id="addCityModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:28px 28px 24px; width:100%; max-width:420px; box-shadow:0 8px 32px rgba(0,0,0,.18); position:relative;">
        <button onclick="closeModal()" style="position:absolute; top:14px; right:16px; background:none; border:none; font-size:18px; color:#888; cursor:pointer;">
            <i class="fas fa-xmark"></i>
        </button>
        <h3 style="font-size:15px; font-weight:800; color:var(--green-800); margin-bottom:20px;">
            <i class="fas fa-city" style="color:var(--green-500); margin-right:6px;"></i>
            Add New City
        </h3>

        <form method="POST" action="{{ route('cities.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">City Name <span style="color:red">*</span></label>
                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="e.g. Raipur"
                       value="{{ old('name') }}"
                       autofocus>
                @error('name')
                    <div style="color:#dc2626; font-size:12px; margin-top:5px;">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </div>
                @enderror
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:4px;">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add City
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Page Card --}}
<div class="card card-pad">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <i class="fas fa-city" style="color:var(--green-600); font-size:18px;"></i>
            <span style="font-size:15px; font-weight:800; color:var(--green-800);">City List</span>
            <span class="badge badge-green" style="margin-left:4px;">{{ $cities->count() }}</span>
        </div>
        <button onclick="openModal()" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add City
        </button>
    </div>

    {{-- Table --}}
    @if($cities->isEmpty())
        <div style="text-align:center; padding:48px 0; color:var(--text-muted);">
            <i class="fas fa-city" style="font-size:36px; opacity:.3; display:block; margin-bottom:12px;"></i>
            <p style="font-size:14px; font-weight:600;">no city found.</p>
            <p style="font-size:12px; margin-top:4px;"> "+ Add City".</p>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px;">S.No</th>
                        <th>Name</th>
                        <th>Created Date</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cities as $index => $city)
                        <tr>
                            <td style="font-weight:700; color:var(--text-muted);">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge badge-green">
                                    <i class="fas fa-location-dot" style="margin-right:5px; font-size:10px;"></i>
                                    {{ $city->name }}
                                </span>
                            </td>
                            <td style="color:var(--text-muted); font-size:13px;">
                                <i class="fas fa-calendar-alt" style="margin-right:5px; color:var(--green-400);"></i>
                                {{ $city->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('cities.destroy', $city->id) }}"
                                      onsubmit="return confirm('\'{{ $city->name }}\' city delete karna chahte ho?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
    function openModal() {
        const m = document.getElementById('addCityModal');
        m.style.display = 'flex';
        setTimeout(() => m.querySelector('input[name=name]').focus(), 100);
    }
    function closeModal() {
        document.getElementById('addCityModal').style.display = 'none';
    }
    document.getElementById('addCityModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // Agar validation error aaya to modal auto-open
    @if($errors->has('name'))
        openModal();
    @endif
</script>
@endpush