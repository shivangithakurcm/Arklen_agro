@extends('layouts.app')
@section('title', 'Create Order — Arklen Agro')
@section('page-title', 'Create Order')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
.select2-container .select2-selection--single{height:36px!important;border:1px solid #dcdcdc!important;border-radius:7px!important;font-size:12px!important;font-family:inherit!important;}
.select2-container .select2-selection--single .select2-selection__rendered{line-height:36px!important;padding-left:10px!important;color:#1e2a14!important;}
.select2-container .select2-selection--single .select2-selection__arrow{height:34px!important;}
.select2-container--open .select2-selection--single{border-color:#4b7c20!important;box-shadow:0 0 0 3px rgba(109,184,42,.12)!important;}
.select2-dropdown{border:1px solid #dcdcdc!important;border-radius:8px!important;font-size:12px!important;font-family:inherit!important;box-shadow:0 4px 16px rgba(0,0,0,.10)!important;z-index:9999!important;}
.select2-results__option--highlighted{background:#4b7c20!important;}
.select2-results__option{padding:7px 10px!important;}
</style>
@endpush

@section('content')
<style>
.form-control{width:100%;padding:9px 11px;border:1px solid #dcdcdc;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;transition:border-color .15s;}
.form-control:focus{border-color:#4b7c20;box-shadow:0 0 0 3px rgba(109,184,42,.10);}
.form-label{display:block;margin-bottom:5px;font-size:12px;font-weight:600;color:#555;}
.page-btn{padding:10px 22px;border:none;border-radius:9px;cursor:pointer;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;text-decoration:none;}
.btn-green{background:var(--green-700,#4b7c20);color:#fff;}
.btn-green:hover{background:#3a6110;}
.btn-grey{background:#e8e8e8;color:#444;}
.btn-grey:hover{background:#ddd;}

/* ── order row table ── */
.ort-wrap{overflow-x:auto;border:1px solid #e8f0dc;border-radius:12px;}
.ort{width:100%;border-collapse:collapse;min-width:860px;}
.ort thead tr{background:#f4faee;}
.ort th{font-size:11px;font-weight:700;color:#5a7c30;text-transform:uppercase;letter-spacing:.06em;padding:10px 10px;border-bottom:2px solid #e0edcc;text-align:left;white-space:nowrap;}
.ort td{padding:7px 7px;vertical-align:middle;border-bottom:1px solid #f5f5f5;}
.ort tbody tr:last-child td{border-bottom:none;}
.ort tbody tr:hover td{background:#fafff5;}
.rnum{font-size:12px;color:#bbb;font-weight:700;text-align:center;width:30px;}

/* ── inline row inputs ── */
.ri{width:100%;padding:7px 9px;border:1px solid #dde8cc;border-radius:7px;font-size:12px;outline:none;box-sizing:border-box;transition:border-color .15s;background:#fff;}
.ri:focus{border-color:#4b7c20;box-shadow:0 0 0 2px rgba(75,124,32,.10);}

/* ── sponsor cell ── */
.sc{display:flex;flex-direction:column;gap:4px;min-width:160px;}
.sc .sm{display:flex;align-items:center;gap:4px;font-size:11px;color:#aaa;}
.sc .sm input{flex:1;padding:4px 7px;border:1px solid #e0e0e0;border-radius:6px;font-size:11px;outline:none;background:#fafafa;color:#555;}
.sc .sm input:focus{border-color:#4b7c20;}

/* ── leg ── */
.leg-sel{padding:7px 9px;border:1px solid #dde8cc;border-radius:7px;font-size:12px;outline:none;width:95px;cursor:pointer;background:#fff;}
.leg-sel:focus{border-color:#4b7c20;}

/* ── +/- ── */
.rbtn{width:30px;height:30px;border-radius:8px;border:none;cursor:pointer;font-size:15px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;transition:all .15s;}
.rbtn-add{background:#dcfce7;color:#166534;border:1.5px dashed #6ee7a0;}
.rbtn-add:hover{background:#bbf7d0;}
.rbtn-del{background:#fee2e2;color:#991b1b;border:1.5px dashed #fca5a5;}
.rbtn-del:hover{background:#fecaca;}
.rac{display:flex;gap:5px;align-items:center;justify-content:center;}
</style>

<div style="max-width:1200px;margin:0 auto;">

    {{-- Back + title --}}
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
        <a href="{{ route('orders.index') }}" class="page-btn btn-grey">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
      <!--  <div>
            <h3 style="margin:0;color:var(--green-800,#3a6110);"><i class="fas fa-cart-plus"></i> Create Order</h3>
            <small style="color:#888;">Add one or more members per order</small>
        </div>-->
    </div>

    <form method="POST" action="{{ route('orders.store') }}" id="createForm">
        @csrf

        <div class="card card-pad" style="padding:0;overflow:hidden;">

            {{-- table --}}
            <div class="ort-wrap">
                <table class="ort">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th style="min-width:130px;">Name</th>
                            <th style="min-width:125px;">Aadhar No</th>
                            <th style="min-width:115px;">Mobile No</th>
                            <th style="min-width:170px;">Sponsor ID</th>
                            <th style="min-width:100px;">Leg</th>
                            <th style="min-width:120px;">Purchase Amt (₹)</th>
                            <th style="width:68px;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="rowsBody">
                        {{-- JS injects rows --}}
                    </tbody>
                </table>
            </div>

            {{-- footer bar --}}
            <div style="padding:16px 18px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;border-top:1px solid #eef5e4;">
                <div>
                    <label class="form-label">Order Date *</label>
                    <input type="date" name="order_date"
                        value="{{ old('order_date', date('Y-m-d')) }}"
                        required class="form-control" style="max-width:180px;">
                </div>

                 <div class="sponsor-sw-${idx}" style="width:100%;"></div>
                <div style="margin-left:auto;display:flex;align-items:center;gap:14px;">
                    <div style="text-align:right;">
                        <div style="font-size:11px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Grand Total</div>
                        <div id="grandTotal" style="font-size:22px;font-weight:800;color:var(--green-800,#3a6110);">₹0.00</div>
                    </div>
                    <a href="{{ route('orders.index') }}" class="page-btn btn-grey">Cancel</a>
                    <button type="submit" class="page-btn btn-green">
                        <i class="fas fa-floppy-disk"></i> Save Order
                    </button>
                </div>
            </div>

        </div>

        @if($errors->any())
        <div style="margin-top:14px;padding:12px 15px;border-radius:10px;background:#fff5f5;border:1px solid #fecaca;color:#c53030;font-size:13px;">
            <strong>⚠</strong> {{ $errors->first() }}
        </div>
        @endif

    </form>
</div>

@push('scripts')
$(`#ss-${idx}`).select2({...});
@endpush
@endsection