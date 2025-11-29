@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Coupons</h4>
        <a href="{{ route('coupons.create') }}" class="btn btn-primary">Add Coupon</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min Purchase</th>
                    <th>Uses</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coupons as $coupon)
                <tr>
                    <td><strong>{{ $coupon->code }}</strong></td>
                    <td>
                        @if($coupon->type == 'discount')
                            <span class="badge bg-info">Discount</span>
                        @else
                            <span class="badge bg-success">Free Product</span>
                        @endif
                    </td>
                    <td>
                        @if($coupon->type == 'discount')
                            @if($coupon->discount_type == 'percentage')
                                {{ $coupon->discount_value }}%
                            @else
                                ${{ number_format($coupon->discount_value, 2) }}
                            @endif
                        @else
                            {{ $coupon->freeProduct->name ?? 'N/A' }} ({{ $coupon->free_product_qty }}x)
                        @endif
                    </td>
                    <td>${{ number_format($coupon->min_purchase, 2) }}</td>
                    <td>{{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}</td>
                    <td>{{ $coupon->valid_until ? $coupon->valid_until->format('Y-m-d') : 'No Limit' }}</td>
                    <td>
                        @if($coupon->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('coupons.edit', $coupon->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $coupons->links() }}
    </div>
</div>
@endsection
