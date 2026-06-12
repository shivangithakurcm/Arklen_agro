@extends('layouts.app')
@section('title', 'Dashboard — Arklen Agro')
@section('page-title', 'Overview')

@section('content')


<div class="metric-grid">
        {{-- Total Members --}}
        <!-- Card 1 -->
        <div class="metric-card">
            <div class="metric-header">
                <span class="metric-label">Total Member</span>

                <svg class="metric-icon" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor"
                    stroke-width="2">

                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>

                    <circle cx="9" cy="7" r="4"></circle>

                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>

                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>

                </svg>

            </div>

            <div class="metric-value-wrapper">
                <span class="metric-value">{{ number_format($totalMembers) }}</span>

                <span class="trend-badge">↑ 12%</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="metric-card">

            <div class="metric-header">

                <span class="metric-label">
                    Total Order Qty
                </span>

                <svg class="metric-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <rect x="2" y="3"
                        width="20"
                        height="14"
                        rx="2">
                    </rect>

                    <line x1="8" y1="21"
                        x2="16" y2="21">
                    </line>

                    <line x1="12" y1="17"
                        x2="12" y2="21">
                    </line>

                </svg>

            </div>

            <div class="metric-value-wrapper">
                <span class="metric-value">
                    {{ number_format($totalOrderQty) }} <sub>Pcs.</sub>
                </span>
            </div>

        </div>

        <!-- Card 3 -->
        <div class="metric-card">

            <div class="metric-header">

                <span class="metric-label">
                    Total Profit
                </span>

                <svg class="metric-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <line x1="12" y1="1"
                        x2="12" y2="23">
                    </line>

                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6">
                    </path>

                </svg>

            </div>

            <div class="metric-value-wrapper">

                <span class="metric-value">
                    ₹{{ number_format($totalOrderValue) }}
                </span>

                <span class="trend-badge">
                    ↑ 8.4%
                </span>

            </div>

        </div>

        <!-- Card 4 -->
        <div class="metric-card">

            <div class="metric-header">
                <span class="metric-label">
                    Total Left
                </span>
            </div>

            <div class="metric-value-wrapper">
                <span class="metric-value">{{ number_format($totalLeft) }}</span>
            </div>

        </div>

        <!-- Card 5 -->
        <div class="metric-card ">

            <div class="metric-header">

                <span class="metric-label ">
                    Total Income
                </span>

            </div>

            <div class="metric-value-wrapper">

                <span class="metric-value ">
                   ₹{{ number_format($totalIncome) }}
                </span>

            </div>

        </div>

        <!-- Card 6 -->
        <div class="metric-card">

            <div class="metric-header">

                <span class="metric-label">
                    Total Right
                </span>

            </div>

            <div class="metric-value-wrapper">

                <span class="metric-value">
                   {{ number_format($totalRight) }}
                </span>

            </div>

        </div>

        <!-- Card 7 -->
        <div class="metric-card commission-card">

            <div class="metric-header">

                <span class="metric-label">
                    Commission Distributed
                </span>

            </div>

            <div class="metric-value-wrapper">

                <span class="metric-value">
                    ₹{{ number_format($totalCommission) }}
                </span>
                @php
                    $pct = $totalIncome > 0 ? min(100, round(($totalCommission / $totalIncome) * 100)) : 0;
                @endphp
            </div>

        <div class="progress-container">
            <div class="progress-fill" style="width:{{ $pct }}%"></div>
            

        </div>




    

</div>
@endsection