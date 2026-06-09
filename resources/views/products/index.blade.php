@extends('layouts.app')

@section('title', 'Products — Arklen Agro')
@section('page-title', 'Products')

@section('content')
<style>
.form-control{ width:100%; padding:10px 12px; border:1px solid #dcdcdc; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box; }
.form-control:focus{ border-color:#4b7c20; }
.form-label{ display:block; margin-bottom:5px; font-size:12px; font-weight:600; color:#555; }
.modal-btn{ padding:10px 22px; border:none; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; }
.add-btn{ background:var(--green-700); color:#fff; }
.close-btn{ background:#e5e5e5; color:#333; }
.action-btns{ display:flex; gap:6px; }
.action-btn{ display:inline-flex; align-items:center; gap:4px; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none; border:none; cursor:pointer; }
.action-btn-edit{ background:#EAF3DE; color:var(--green-800); border:1px solid #c0dd97; }
.action-btn-edit:hover{ background:#d4eab8; }
.action-btn-delete{ background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
.action-btn-delete:hover{ background:#fecaca; }
</style>

<div class="card card-pad">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="margin:0;color:var(--green-800);"><i class="fas fa-box-open"></i> Product List</h3>
        <button onclick="document.getElementById('addModal').style.display='flex'" class="modal-btn add-btn">
            <i class="fas fa-plus"></i> Add Product
        </button>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Business Value</th>
                    <th>Direct Commission</th>
                    <th>New Joinee</th>
                    <th>Level 1</th>
                    <th>Level 2</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $i => $p)
                <tr>
                    <td>{{ $products->firstItem() + $i }}</td>
                    <td>
                        @if($p->product_image)
                        <img src="{{ Storage::url($p->product_image) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                        @else
                        <div style="width:40px;height:40px;border-radius:8px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-image" style="color:#aaa;"></i>
                        </div>
                        @endif
                    </td>
                    <td>{{ $p->product_name }}</td>
                    <td>₹{{ number_format($p->product_price, 2) }}</td>
                    <td>{{ $p->business_value }}</td>
                    <td>{{ $p->direct_commission }}%</td>
                    <td>{{ $p->new_joinee }}%</td>
                    <td>{{ $p->level_1 }}%</td>
                    <td>{{ $p->level_2 }}%</td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('products.edit', $p) }}" class="action-btn action-btn-edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('products.destroy', $p) }}" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn action-btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:30px;color:#888;">No products found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:15px;">{{ $products->links() }}</div>
</div>

{{-- ADD PRODUCT MODAL --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:999;">
    <div style="background:#fff;width:450px;max-width:95vw;border-radius:16px;overflow-y:auto;max-height:92vh;">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid #eee;">
            <div>
                <h3 style="margin:0;color:var(--green-800);">Product Detail</h3>
                <small style="color:#777;">Fill product information</small>
            </div>
            <button type="button" onclick="document.getElementById('addModal').style.display='none'" style="border:none;background:none;font-size:18px;cursor:pointer;">×</button>
        </div>
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" style="padding:22px;">
            @csrf

            {{-- Image Upload --}}
            <div style="display:flex;justify-content:center;margin-bottom:25px;">
                <label for="product_image_input" style="cursor:pointer;text-align:center;">
                    <div id="productImagePreview" style="width:100px;height:100px;border-radius:12px;border:2px dashed #9ac46b;background:#f4faee;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <i class="fas fa-image" style="font-size:28px;color:#4b7c20;"></i>
                    </div>
                    <div style="margin-top:8px;font-size:12px;color:#777;">Upload Image</div>
                    <input type="file" id="product_image_input" name="product_image" accept="image/*" style="display:none;" onchange="previewProductImage(this)">
                </label>
            </div>

            <div style="display:flex;flex-direction:column;gap:15px;">
                <div>
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}" required class="form-control">
                </div>
                <div>
                    <label class="form-label">Product Price (₹) *</label>
                    <input type="number" name="product_price" value="{{ old('product_price') }}" required min="0" step="0.01" class="form-control">
                </div>
                <div>
    <label class="form-label">Business Value *</label>
    <input type="number" name="business_value" value="{{ old('business_value') }}" 
        required min="0" step="0.01" class="form-control">
</div>
                <div>
                    <label class="form-label">Direct Commission (%) *</label>
                    <input type="number" name="direct_commission" value="{{ old('direct_commission') }}" required min="0" max="100" step="0.01" class="form-control">
                </div>
                <div>
                    <label class="form-label">New Joinee (%) *</label>
                    <input type="number" name="new_joinee" value="{{ old('new_joinee') }}" required min="0" max="100" step="0.01" class="form-control">
                </div>
                <div>
                    <label class="form-label">Level 1 (%) *</label>
                    <input type="number" name="level_1" value="{{ old('level_1') }}" required min="0" max="100" step="0.01" class="form-control">
                </div>
                <div>
                    <label class="form-label">Level 2 (%) *</label>
                    <input type="number" name="level_2" value="{{ old('level_2') }}" required min="0" max="100" step="0.01" class="form-control">
                </div>
            </div>

            @if($errors->any())
            <div style="margin-top:15px;padding:10px;border-radius:8px;background:#ffe9e9;color:#d11;">
                {{ $errors->first() }}
            </div>
            @endif

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="modal-btn close-btn">Close</button>
                <button type="submit" class="modal-btn add-btn">Add</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewProductImage(input) {
    if(input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('productImagePreview').innerHTML =
            `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
@if($errors->any())
document.getElementById('addModal').style.display = 'flex';
@endif
</script>
@endpush

@endsection