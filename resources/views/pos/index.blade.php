@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Products</h4>
                <form action="{{ route('pos.index') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
                </form>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 150px;">
                                    No Image
                                </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text text-primary fw-bold">${{ number_format($product->price, 2) }}</p>
                                <p class="card-text text-muted">Stock: {{ $product->stock }}</p>
                                <a href="{{ route('cart.add', $product->id) }}" class="btn btn-primary w-100">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0">Cart</h4>
            </div>
            <div class="card-body">
                @if(count($cart) > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($cart as $id => $details)
                                @php $total += $details['price'] * $details['quantity']; @endphp
                                <tr>
                                    <td>{{ $details['name'] }}</td>
                                    <td>{{ $details['quantity'] }}</td>
                                    <td>${{ number_format($details['price'] * $details['quantity'], 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart.remove') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <button type="submit" class="btn btn-danger btn-sm">X</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="fw-bold">Total</td>
                                <td colspan="2" class="fw-bold">${{ number_format($total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Customer ID (Optional)</label>
                            <input type="number" name="customer_id" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Paid Amount</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Checkout</button>
                    </form>
                @else
                    <p class="text-center">Cart is empty</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
