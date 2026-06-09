@extends('layouts.app')

@section('title', 'Edit Product — Arklen Agro')
@section('page-title', 'Edit Product')

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('products.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;font-size:13px;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card card-pad" style="max-width:450px;">
    <h3 style="margin:0 0 20px;color:var(--green-800);">Edit Product</h3>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div style="display:flex;justify-content:center;margin-bottom:25px;">
            <label for="edit_product_image" style="cursor:pointer;text-align:center;">
                <div id="editProductPreview" style="width:100px;height:100px;border-radius:12px;border:2px dashed #9ac46b;background:#f4faee;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                    @if($product->product_image)
                        <img src="{{ Storage::url($product->product_image) }}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
                    @else
                        <i class="fas fa-image" style="font-size:28px;color:#4b7c20;"></i>
                    @endif
                </div>
                <div style="margin-top:8px;font-size:12px;color:#777;">Change Image</div>
                <input type="file" id="edit_product_image" name="product_image" accept="image/*" style="display:none;" onchange="previewEditImage(this)">
            </label>
        </div>

        <div style="display:flex;flex-direction:column;gap:15px;">
            <div>
                <label class="form-label">Product Name *</label>
                <input type="text" name="product_name" value="{{ $product->product_name }}" required class="form-control">
            </div>
            <div>
                <label class="form-label">Product Price (₹) *</label>
                <input type="number" name="product_price" value="{{ $product->product_price }}" required min="0" step="0.01" class="form-control">
            </div>
            <div>
                <label class="form-label">Business Value *</label>
                <input type="number" name="business_value" value="{{ $product->business_value }}" required min="0" step="0.01" class="form-control">
            </div>
            <div>
                <label class="form-label">Direct Commission (%) *</label>
                <input type="number" name="direct_commission" value="{{ $product->direct_commission }}" required min="0" max="100" step="0.01" class="form-control">
            </div>
            <div>
                <label class="form-label">New Joinee (%) *</label>
                <input type="number" name="new_joinee" value="{{ $product->new_joinee }}" required min="0" max="100" step="0.01" class="form-control">
            </div>
            <div>
                <label class="form-label">Level 1 (%) *</label>
                <input type="number" name="level_1" value="{{ $product->level_1 }}" required min="0" max="100" step="0.01" class="form-control">
            </div>
            <div>
                <label class="form-label">Level 2 (%) *</label>
                <input type="number" name="level_2" value="{{ $product->level_2 }}" required min="0" max="100" step="0.01" class="form-control">
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:25px;">
            <a href="{{ route('products.index') }}" style="padding:10px 22px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;font-size:13px;font-weight:600;">Close</a>
            <button type="submit" style="padding:10px 22px;border:none;border-radius:8px;background:var(--green-700);color:#fff;font-size:13px;font-weight:600;cursor:pointer;">Update</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewEditImage(input) {
    if(input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('editProductPreview').innerHTML =
            `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

@endsection