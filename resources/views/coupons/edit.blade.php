@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Edit Coupon</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('coupons.update', $coupon->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Coupon Code</label>
                <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" required>
            </div>
            <div class="mb-3">
                <label>Type</label>
                <select name="type" id="couponType" class="form-control" required onchange="toggleCouponFields()">
                    <option value="discount" {{ $coupon->type == 'discount' ? 'selected' : '' }}>Discount</option>
                    <option value="free_product" {{ $coupon->type == 'free_product' ? 'selected' : '' }}>Free Product</option>
                </select>
            </div>
            
            <div id="discountFields" style="display: {{ $coupon->type == 'discount' ? 'block' : 'none' }};">
                <div class="mb-3">
                    <label>Discount Type</label>
                    <select name="discount_type" class="form-control">
                        <option value="percentage" {{ $coupon->discount_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="nominal" {{ $coupon->discount_type == 'nominal' ? 'selected' : '' }}>Nominal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Discount Value</label>
                    <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ $coupon->discount_value }}">
                </div>
            </div>
            
            <div id="freeProductFields" style="display: {{ $coupon->type == 'free_product' ? 'block' : 'none' }};">
                <div class="mb-3">
                    <label>Free Product</label>
                    <select name="free_product_id" class="form-control">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $coupon->free_product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Quantity</label>
                    <input type="number" name="free_product_qty" class="form-control" value="{{ $coupon->free_product_qty }}" min="1">
                </div>
            </div>
            
            <div class="mb-3">
                <label>Minimum Purchase</label>
                <input type="number" step="0.01" name="min_purchase" class="form-control" value="{{ $coupon->min_purchase }}">
            </div>
            <div class="mb-3">
                <label>Max Uses</label>
                <input type="number" name="max_uses" class="form-control" value="{{ $coupon->max_uses }}">
            </div>
            <div class="mb-3">
                <label>Valid From</label>
                <input type="date" name="valid_from" class="form-control" value="{{ $coupon->valid_from ? $coupon->valid_from->format('Y-m-d') : '' }}">
            </div>
            <div class="mb-3">
                <label>Valid Until</label>
                <input type="date" name="valid_until" class="form-control" value="{{ $coupon->valid_until ? $coupon->valid_until->format('Y-m-d') : '' }}">
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $coupon->description }}</textarea>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ $coupon->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('coupons.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
function toggleCouponFields() {
    const type = document.getElementById('couponType').value;
    document.getElementById('discountFields').style.display = type === 'discount' ? 'block' : 'none';
    document.getElementById('freeProductFields').style.display = type === 'free_product' ? 'block' : 'none';
}
</script>
@endsection
