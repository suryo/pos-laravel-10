@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Transaction #{{ $transaction->id }}</h4>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back</a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Date:</strong> {{ $transaction->created_at->format('Y-m-d H:i') }}</p>
                <p><strong>Customer:</strong> {{ $transaction->customer_id ?? 'Guest' }}</p>
            </div>
            <div class="col-md-6 text-end">
                <p><strong>Total Amount:</strong> ${{ number_format($transaction->total_amount, 2) }}</p>
                <p><strong>Paid Amount:</strong> ${{ number_format($transaction->paid_amount, 2) }}</p>
                <p><strong>Change:</strong> ${{ number_format($transaction->change_amount, 2) }}</p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $detail)
                <tr>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>${{ number_format($detail->price, 2) }}</td>
                    <td>${{ number_format($detail->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
