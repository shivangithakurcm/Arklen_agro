@extends('layouts.app')

@section('title', 'Seller Profile — 2APL Marketing')
@section('page-title', 'Seller Profile')

@section('content')

<div style="max-width:850px;margin:0 auto;">

    {{-- Back Button --}}
    <div style="margin-bottom:16px;">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    {{-- Profile Card --}}
    <div class="card card-pad" style="margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            @if($member->profile_image)
                <img src="{{ Storage::url($member->profile_image) }}"
                    style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid var(--green-200);">
            @else
                <div class="avatar" style="width:90px;height:90px;font-size:30px;border-radius:50%;flex-shrink:0;">
                    {{ $member->initials }}
                </div>
            @endif
            <div style="flex:1;">
                <h2 style="margin:0 0 8px;color:var(--green-800);">{{ $member->full_name }}</h2>
                <table style="font-size:13px;">
                    <tr>
                        <td style="color:#888;padding:4px 12px 4px 0;">Contact</td>
                        <td style="font-weight:600;">{{ $member->contact }}</td>
                    </tr>
                    <tr>
                        <td style="color:#888;padding:4px 12px 4px 0;">Address</td>
                        <td style="font-weight:600;">{{ $member->address }}</td>
                    </tr>
                    <tr>
                        <td style="color:#888;padding:4px 12px 4px 0;">Seller ID</td>
                        <td><span class="badge badge-green">{{ $member->seller_id }}</span></td>
                    </tr>
                    <tr>
                        <td style="color:#888;padding:4px 12px 4px 0;">Sponsor ID</td>
                        <td style="font-weight:600;">{{ $member->sponsor_id ?? '-' }}</td>
                    </tr>
                </table>
                <div class="stat-grid" style="margin-top:15px;">
                    <div class="stat-card">
                        <div class="stat-label">Total Income</div>
                        <div class="stat-val">₹{{ number_format($totalCommission ?? 0, 2) }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Sponsor Income (%)</div>
                        <div class="stat-val">{{ number_format($sponsorIncomePercentage ?? 0, 2) }}%</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Team Income (%)</div>
                        <div class="stat-val">{{ number_format($teamIncomePercentage ?? 0, 2) }}%</div>
                    </div>
                </div>  
            </div>
            <div style="align-self:flex-start;">
                <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">
                    <i class="fas fa-pen"></i> Edit
                </a>
            </div>
        </div>
    </div>

    {{-- Commission Details --}}
    <div class="card card-pad" style="margin-bottom:20px;">
        <div style="font-size:11px;font-weight:700;color:#777;text-transform:uppercase;letter-spacing:.08em;margin-bottom:15px;padding-bottom:8px;border-bottom:1px solid #eee;">
            Commission Details
        </div>
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">Direct Commission</div>
                <div class="stat-val">₹{{ number_format($member->direct_commission ?? 0, 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Level 1 Commission</div>
                <div class="stat-val">₹{{ number_format($member->level1_commission ?? 0, 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Level 2 Commission</div>
                <div class="stat-val">₹{{ number_format($member->level2_commission ?? 0, 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">New Joinee Commission</div>
                <div class="stat-val">₹{{ number_format($member->new_joinee_income ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Team Summary --}}
    <div class="card card-pad">
        <div style="font-size:11px;font-weight:700;color:#777;text-transform:uppercase;letter-spacing:.08em;margin-bottom:15px;padding-bottom:8px;border-bottom:1px solid #eee;">
            Team Summary
        </div>
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">Members Left</div>
                <div class="stat-val">{{ count($member->leftMembers()) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Members Right</div>
                <div class="stat-val">{{ count($member->rightMembers()) }}</div>
            </div>
           <!--<div class="stat-card">
                <div class="stat-label">Total Income</div>
                <div class="stat-val">₹{{ number_format($member->total_income, 2) }}</div>
            </div> -->
          
            <div class="stat-card">
                <div class="stat-label">Left BV</div>
                <div class="stat-val">{{ number_format($member->bv_left, 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Right BV</div>
                <div class="stat-val">{{ number_format($member->bv_right, 2) }}</div>
            </div>

            <!-- <div class="stat-card">
                <div class="stat-label">Team BV</div>
                <div class="stat-val">{{ number_format($member->team_bv, 2) }}</div>
            </div> -->
        </div>
    </div>

</div>

@endsection