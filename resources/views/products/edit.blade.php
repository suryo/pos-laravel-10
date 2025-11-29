@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Edit Product</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
            </div>
            <div class="mb-3">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
            </div>
            <div class="mb-3">
                <label>Barcode</label>
                <input type="text" name="barcode" class="form-control" value="{{ $product->barcode }}">
            </div>
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" width="50" class="mt-2">
                @endif
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
