@extends('layouts.app')

@section('title', 'Action — 2APL Marketing')
@section('page-title', 'Action')

@section('content')

<div class="card card-pad" style="max-width:650px;margin:0 auto;">

    <div style="margin-bottom:20px;">
        <h3 style="margin:0;color:var(--green-800);">
            <i class="fas fa-sliders-h"></i> {{ $member->full_name }}
        </h3>
        <small style="color:#777;">Seller ID: {{ $member->seller_id }}</small>
    </div>

    <form method="POST" action="{{ route('members.action.update', $member) }}">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">

            <div>
                <label class="form-label">Left Members</label>
                <input type="number" name="left_members"
                    value="{{ count($member->leftMembers()) }}"
                    class="form-control" disabled
                    style="background:#f5f5f5;">
            </div>
            <div>
                <label class="form-label">Right Members</label>
                <input type="number" name="right_members"
                    value="{{ count($member->rightMembers()) }}"
                    class="form-control" disabled
                    style="background:#f5f5f5;">
            </div>

            <div>
                <label class="form-label">BV Left</label>
                <input type="number" step="0.01" name="bv_left"
                    value="{{ old('bv_left', $member->bv_left) }}"
                    class="form-control">
            </div>
            <div>
                <label class="form-label">BV Right</label>
                <input type="number" step="0.01" name="bv_right"
                    value="{{ old('bv_right', $member->bv_right) }}"
                    class="form-control">
            </div>

            <div>
                <label class="form-label">Direct Sponsor Income</label>
                <input type="number" step="0.01" name="direct_sponsor_income"
                    value="{{ old('direct_sponsor_income', $member->direct_sponsor_income) }}"
                    class="form-control">
            </div>
            <div>
                <label class="form-label">Team Income</label>
                <input type="number" step="0.01" name="team_income"
                    value="{{ old('team_income', $member->team_income) }}"
                    class="form-control">
            </div>

            <div>
                <label class="form-label">Pay Income</label>
                <input type="number" step="0.01" name="pay_income"
                    value="{{ old('pay_income', $member->pay_income) }}"
                    class="form-control">
            </div>
            <div>
                <label class="form-label">Total Income</label>
                <input type="number" step="0.01" name="total_income"
                    value="{{ old('total_income', $member->total_income) }}"
                    class="form-control">
            </div>

            <div style="grid-column:span 2;">
                <label class="form-label">Team BV</label>
                <input type="number" step="0.01" name="team_bv"
                    value="{{ old('team_bv', $member->team_bv) }}"
                    class="form-control">
            </div>

        </div>

        @if($errors->any())
        <div style="margin-top:15px;padding:10px;border-radius:8px;background:#ffe9e9;color:#d11;">
            {{ $errors->first() }}
        </div>
        @endif

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px;">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Close
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add
            </button>
        </div>

    </form>
</div>

@endsection