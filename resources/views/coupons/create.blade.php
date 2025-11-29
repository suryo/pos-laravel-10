@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Add Coupon</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('coupons.store') }}" method="POST" id="couponForm">
            @csrf
            <div class="mb-3">
                <label>Coupon Code</label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Type</label>
                <select name="type" id="couponType" class="form-control" required onchange="toggleCouponFields()">
                    <option value="">Select Type</option>
                    <option value="discount">Discount</option>
                    <option value="free_product">Free Product</option>
                </select>
            </div>
            
            <!-- Discount Fields -->
            <div id="discountFields" style="display: none;">
                <div class="mb-3">
                    <label>Discount Type</label>
                    <select name="discount_type" class="form-control">
                        <option value="percentage">Percentage</option>
                        <option value="nominal">Nominal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Discount Value</label>
                    <input type="number" step="0.01" name="discount_value" class="form-control">
                </div>
            </div>
            
            <!-- Free Product Fields -->
            <div id="freeProductFields" style="display: none;">
                <div class="mb-3">
                    <label>Free Product</label>
                    <select name="free_product_id" class="form-control">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Quantity</label>
                    <input type="number" name="free_product_qty" class="form-control" value="1" min="1">
                </div>
            </div>
            
            <div class="mb-3">
                <label>Minimum Purchase</label>
                <input type="number" step="0.01" name="min_purchase" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label>Max Uses (leave empty for unlimited)</label>
                <input type="number" name="max_uses" class="form-control">
            </div>
            <div class="mb-3">
                <label>Valid From</label>
                <input type="date" name="valid_from" class="form-control">
            </div>
            <div class="mb-3">
                <label>Valid Until</label>
                <input type="date" name="valid_until" class="form-control">
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
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
