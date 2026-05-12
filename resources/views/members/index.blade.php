@extends('layouts.app')

@section('title', 'Members — Arklen Agro')
@section('page-title', 'Members')

@section('content')

<style>
.table-wrap{ overflow-x:auto; }
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
        <h3 style="margin:0;color:var(--green-800);"><i class="fas fa-users"></i> All Members</h3>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('members.index') }}" style="display:flex;gap:10px;margin-bottom:20px;">
        <input type="text" name="sponsor_id" value="{{ request('sponsor_id') }}"
            placeholder="Search by Sponsor ID" class="form-control" style="max-width:250px;">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by name / contact" class="form-control" style="max-width:250px;">
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
                    <th>Total Income</th>
                    <th>Joining Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $i => $m)
                <tr>
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
                    <td>{{ $m->sponsor_id ?? '-' }}</td>
                    <td><span class="badge {{ $m->position == 'left' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($m->position) }}</span></td>
                    <td>{{ count($m->leftMembers()) }}</td>
                    <td>{{ count($m->rightMembers()) }}</td>
                    <td>₹{{ number_format($m->total_income,2) }}</td>
                    <td>{{ $m->date_of_joining->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $m->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $m->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('members.edit',$m) }}" class="action-btn action-btn-edit" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="{{ route('members.show',$m) }}" class="action-btn action-btn-view" title="Seller Profile">
                                <i class="fas fa-id-card"></i> Profile
                            </a>
                            <a href="{{ route('members.action',$m) }}" class="action-btn action-btn-action" title="Action">
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

   <div style="margin-top:15px;">
    {{ $members->withQueryString()->links('pagination::bootstrap-5') }}
</div>

</div>

@endsection