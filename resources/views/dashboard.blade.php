@extends('layouts.app')
@section('title', 'Dashboard — 2APL Marketing')
@section('page-title', 'Overview')

@section('content')
<style>
.dash-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    width: 100%;
}
.dash-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    transition: transform 0.2s, box-shadow 0.2s;
}
.dash-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
}
.dash-card.highlight {
    background: var(--green-50);
    border-color: var(--green-200);
}
.dash-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.dash-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.6px;
}
.highlight .dash-label { color: var(--green-600); }
.dash-icon { color: var(--green-500); opacity: 0.8; }
.dash-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--green-800);
    letter-spacing: -0.5px;
    line-height: 1;
}
.highlight .dash-value { color: var(--green-700); }
.dash-value sub {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-muted);
    vertical-align: baseline;
}
.progress-wrap {
    width: 100%;
    height: 5px;
    background: var(--green-100);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 0.25rem;
}
.progress-fill {
    height: 100%;
    background: var(--green-500);
    border-radius: 4px;
}
.dash-card.col-center { grid-column: 2; }

@media (max-width: 900px) {
    .dash-grid { grid-template-columns: repeat(2, 1fr); }
    .dash-card.col-center { grid-column: span 2; }
}
@media (max-width: 580px) {
    .dash-grid { grid-template-columns: 1fr; }
    .dash-card.col-center { grid-column: 1; }
}
</style>

<div class="dash-grid">

    {{-- Total Members --}}
    <div class="dash-card">
        <div class="dash-card-header">
            <span class="dash-label">Total Member</span>
            <svg class="dash-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <span class="dash-value">{{ number_format($totalMembers) }}</span>
    </div>

    <!-- {{-- Total Order Qty --}}
    <div class="dash-card">
        <div class="dash-card-header">
            <span class="dash-label">Total Order Qty</span>
            <svg class="dash-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
        </div>
        <span class="dash-value">{{ number_format($totalOrderQty) }} <sub>Pcs.</sub></span>
    </div> -->

    {{-- Total Order Value --}}
    <!-- <div class="dash-card">
        <div class="dash-card-header">
            <span class="dash-label">Total Order Value</span>
            <svg class="dash-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
        </div>
        <span class="dash-value">₹{{ number_format($totalOrderValue) }}</span>
    </div> -->

    {{-- Total Left --}}
    <div class="dash-card">
        <div class="dash-card-header">
            <span class="dash-label">Total Left</span>
            <svg class="dash-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </div>
        <span class="dash-value">{{ number_format($totalLeft) }}</span>
    </div>

      {{-- Total Right --}}
    <div class="dash-card">
        <div class="dash-card-header">
            <span class="dash-label">Total Right</span>
            <svg class="dash-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </div>
        <span class="dash-value">{{ number_format($totalRight) }}</span>
    </div>
    
    {{-- Total Income — highlighted center --}}
    <div class="dash-card highlight col-left">
        <div class="dash-card-header">
            <span class="dash-label">Total Income</span>
        </div>
        <span class="dash-value">₹{{ number_format($totalIncome) }}</span>
    </div>

  


</div>
@endsection