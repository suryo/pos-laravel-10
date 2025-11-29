@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Sales Report</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.index') }}" method="GET" class="row mb-4">
            <div class="col-md-4">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="alert alert-info">
            <h4>Total Sales: ${{ number_format($totalSales, 2) }}</h4>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $transaction->id }}</td>
                    <td>${{ number_format($transaction->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
