@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Shipping Methods</h4>
        <a href="{{ route('shippings.create') }}" class="btn btn-primary">Add Shipping Method</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Cost</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shippings as $shipping)
                <tr>
                    <td>{{ $shipping->id }}</td>
                    <td>{{ $shipping->name }}</td>
                    <td>${{ number_format($shipping->cost, 2) }}</td>
                    <td>{{ $shipping->description ?? 'N/A' }}</td>
                    <td>
                        @if($shipping->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('shippings.edit', $shipping->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('shippings.destroy', $shipping->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $shippings->links() }}
    </div>
</div>
@endsection
