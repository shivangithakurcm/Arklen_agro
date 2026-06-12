@extends('layouts.app')
@section('title', 'Create Order — Arklen Agro')
@section('page-title', 'Create Order')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
.select2-container .select2-selection--single{height:36px!important;border:1px solid #dde8cc!important;border-radius:7px!important;font-size:12px!important;font-family:inherit!important;}
.select2-container .select2-selection--single .select2-selection__rendered{line-height:36px!important;padding-left:10px!important;color:#1e2a14!important;}
.select2-container .select2-selection--single .select2-selection__arrow{height:34px!important;}
.select2-container--open .select2-selection--single{border-color:#4b7c20!important;box-shadow:0 0 0 3px rgba(109,184,42,.12)!important;}
.select2-dropdown{border:1px solid #dcdcdc!important;border-radius:8px!important;font-size:12px!important;font-family:inherit!important;box-shadow:0 4px 16px rgba(0,0,0,.10)!important;z-index:9999!important;}
.select2-results__option--highlighted{background:#4b7c20!important;color:#fff!important;}
.select2-results__option{padding:7px 10px!important;}
</style>
@endpush

@section('content')
<style>
.form-control{width:100%;padding:9px 11px;border:1px solid #dcdcdc;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;transition:border-color .15s;}
.form-control:focus{border-color:#4b7c20;box-shadow:0 0 0 3px rgba(109,184,42,.10);}
.form-label{display:block;margin-bottom:5px;font-size:12px;font-weight:600;color:#555;}
.page-btn{padding:10px 22px;border:none;border-radius:9px;cursor:pointer;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;text-decoration:none;}
.btn-green{background:#4b7c20;color:#fff;}
.btn-green:hover{background:#3a6110;}
.btn-grey{background:#e8e8e8;color:#444;}
.btn-grey:hover{background:#ddd;}

.ort-wrap{overflow-x:auto;border:1px solid #e8f0dc;border-radius:12px;}
.ort{width:100%;border-collapse:collapse;min-width:860px;}
.ort thead tr{background:#f4faee;}
.ort th{font-size:11px;font-weight:700;color:#5a7c30;text-transform:uppercase;letter-spacing:.06em;padding:10px;border-bottom:2px solid #e0edcc;text-align:left;white-space:nowrap;}
.ort td{padding:7px;vertical-align:middle;border-bottom:1px solid #f5f5f5;}
.ort tbody tr:last-child td{border-bottom:none;}
.ort tbody tr:hover td{background:#fafff5;}
.rnum{font-size:12px;color:#bbb;font-weight:700;text-align:center;width:30px;}

.ri{width:100%;padding:7px 9px;border:1px solid #dde8cc;border-radius:7px;font-size:12px;outline:none;box-sizing:border-box;transition:border-color .15s;background:#fff;}
.ri:focus{border-color:#4b7c20;box-shadow:0 0 0 2px rgba(75,124,32,.10);}

.leg-sel{padding:7px 9px;border:1px solid #dde8cc;border-radius:7px;font-size:12px;outline:none;width:95px;cursor:pointer;background:#fff;}
.leg-sel:focus{border-color:#4b7c20;}

.rbtn{width:30px;height:30px;border-radius:8px;border:none;cursor:pointer;font-size:15px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;transition:all .15s;}
.rbtn-add{background:#dcfce7;color:#166534;border:1.5px dashed #6ee7a0;}
.rbtn-add:hover{background:#bbf7d0;}
.rbtn-del{background:#fee2e2;color:#991b1b;border:1.5px dashed #fca5a5;}
.rbtn-del:hover{background:#fecaca;}
.rac{display:flex;gap:5px;align-items:center;justify-content:center;}
</style>

<div style="max-width:1200px;margin:0 auto;">

    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
        <a href="{{ route('orders.index') }}" class="page-btn btn-grey">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>

        <div style="margin-left:auto;background:#f4faee;border:1px solid #d0e8b0;border-radius:10px;padding:8px 16px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-user-circle" style="color:#4b7c20;font-size:16px;"></i>
            <div>
                <div style="font-size:10px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Punch By</div>
                <div style="font-size:13px;font-weight:700;color:#3a6110;">
                    {{ auth()->user()->seller_id ?? 'N/A' }} — {{ auth()->user()->name ?? auth()->user()->first_name ?? '' }}
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('orders.store') }}" id="createForm">
        @csrf
        <input type="hidden" name="punch_by" value="{{ auth()->user()->seller_id }}">

        <div class="card card-pad" style="padding:0;overflow:hidden;">

            <div class="ort-wrap">
                <table class="ort">
                    <thead>
                        <tr>
                            <th style="width:32px;">SNO.</th>
                            <th style="min-width:130px;">Name</th>
                            <th style="min-width:125px;">Aadhar No</th>
                            <th style="min-width:115px;">Mobile No</th>
                            <th style="min-width:200px;">Sponsor</th>
                            <th style="min-width:100px;">Leg</th>
                            <th style="min-width:120px;">Purchase Amt (₹)</th>
                            <th style="width:68px;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="rowsBody"></tbody>
                </table>
            </div>

            <div style="padding:16px 18px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;border-top:1px solid #eef5e4;">
                <div>
                    <label class="form-label">Order Date *</label>
                    <input type="date" name="order_date"
                        value="{{ old('order_date', date('Y-m-d')) }}"
                        required class="form-control" style="max-width:180px;">
                </div>
                <div style="margin-left:auto;display:flex;align-items:center;gap:14px;">
                    <div style="text-align:right;">
                        <div style="font-size:11px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Grand Total</div>
                        <div id="grandTotal" style="font-size:22px;font-weight:800;color:#3a6110;">₹0.00</div>
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
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const sponsorOptions = [
    { id: '', text: '-- Select Sponsor --' },
    @foreach($members->where('seller_id', '!=', 'ADMIN001') as $m)
    { id: '{{ $m->seller_id }}', text: '{{ $m->seller_id }} \u2014 {{ $m->first_name }} {{ $m->last_name }}' },
    @endforeach
];

let rowCount = 0;

function addRow(prefill = {}) {
    rowCount++;
    const idx = rowCount;
    const tbody = document.getElementById('rowsBody');
    const tr = document.createElement('tr');
    tr.id = 'row-' + idx;

    // ✅ Blade comment JS ke andar nahi — clean HTML string
    tr.innerHTML =
        '<td class="rnum">' + idx + '</td>' +
        '<td><input type="text" name="rows[' + idx + '][name]" class="ri" placeholder="Full name" value="' + (prefill.name || '') + '" autocomplete="off"></td>' +
        '<td><input type="text" name="rows[' + idx + '][aadhar]" class="ri" placeholder="12-digit" value="' + (prefill.aadhar || '') + '" maxlength="12" oninput="this.value=this.value.replace(/[^0-9]/g,\'\').slice(0,12)"></td>' +
        '<td><input type="text" name="rows[' + idx + '][mobile]" class="ri" placeholder="Mobile" value="' + (prefill.mobile || '') + '" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,\'\').slice(0,10)"></td>' +
        '<td style="min-width:200px;"><select name="rows[' + idx + '][sponsor_id]" id="sponsor-' + idx + '" style="width:100%;"><option value="">-- Select Sponsor --</option></select></td>' +
        '<td><select name="rows[' + idx + '][leg]" class="leg-sel">' +
            '<option value="left" ' + ((prefill.leg === 'right') ? '' : 'selected') + '>&#9664; Left</option>' +
            '<option value="right" ' + ((prefill.leg === 'right') ? 'selected' : '') + '>&#9654; Right</option>' +
        '</select></td>' +
        '<td><input type="number" name="rows[' + idx + '][amount]" class="ri" placeholder="0.00" min="0" step="0.01" value="' + (prefill.amount || '') + '" oninput="recalc()" style="font-weight:700;color:#3a6110;"></td>' +
        '<td><div class="rac">' +
            '<button type="button" class="rbtn rbtn-del" onclick="removeRow(' + idx + ')" title="Remove">&minus;</button>' +
            '<button type="button" class="rbtn rbtn-add" onclick="addRow()" title="Add">+</button>' +
        '</div></td>';

    tbody.appendChild(tr);

    $(`#sponsor-${idx}`).select2({
        data: sponsorOptions,
        placeholder: 'Search sponsor...',
        allowClear: true,
        width: '100%',
        dropdownParent: $(`#row-${idx}`)
    });

    if (prefill.sponsor_id) {
        $(`#sponsor-${idx}`).val(prefill.sponsor_id).trigger('change');
    }

    renum();
}

function removeRow(idx) {
    if (document.querySelectorAll('#rowsBody tr').length <= 1) return;
    document.getElementById('row-' + idx)?.remove();
    renum(); recalc();
}

function renum() {
    document.querySelectorAll('#rowsBody tr').forEach((tr, i) => {
        const c = tr.querySelector('.rnum');
        if (c) c.textContent = i + 1;
    });
}

function recalc() {
    let t = 0;
    document.querySelectorAll('#rowsBody input[name$="[amount]"]').forEach(i => t += parseFloat(i.value) || 0);
    document.getElementById('grandTotal').textContent = '₹' + t.toFixed(2);
}

addRow();
</script>
@endpush
@endsection