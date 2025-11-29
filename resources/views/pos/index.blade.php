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
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        {{ $products->onEachSide(1)->withQueryString()->links() }}
                    </nav>
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
                    <form action="{{ route('transactions.store') }}" method="POST" id="checkoutForm">
                        @csrf
                        <div class="mb-3">
                            <label>Customer ID (Optional)</label>
                            <input type="number" name="customer_id" class="form-control">
                        </div>
                    </form>
                    
                    <!-- Coupon Section (Outside checkout form) -->
                    <div class="card mb-3 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Coupon</h6>
                            @if(session('applied_coupon'))
                                <div class="alert alert-success">
                                    <strong>Applied: {{ session('applied_coupon')['code'] }}</strong>
                                    @if(session('applied_coupon')['type'] == 'discount')
                                        <p class="mb-0">Discount: ${{ number_format(session('applied_coupon')['discount'], 2) }}</p>
                                    @else
                                        <p class="mb-0">Free Product Added!</p>
                                    @endif
                                    <form action="{{ route('cart.removeCoupon') }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Remove Coupon</button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('cart.applyCoupon') }}" method="POST" class="d-flex">
                                    @csrf
                                    <input type="text" name="coupon_code" class="form-control me-2" placeholder="Enter coupon code">
                                    <button type="submit" class="btn btn-primary">Apply</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    <form action="{{ route('transactions.store') }}" method="POST" id="checkoutForm">
                        @csrf
                        
                        <!-- Discount Section -->
                        <div class="card mb-3 bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Discount</h6>
                                <div class="mb-3">
                                    <label>Discount Type</label>
                                    <select name="discount_type" id="discountType" class="form-control" onchange="calculateDiscount()">
                                        <option value="none">No Discount</option>
                                        <option value="percentage">Percentage (%)</option>
                                        <option value="nominal">Nominal ($)</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="discountValueDiv" style="display: none;">
                                    <label id="discountValueLabel">Discount Value</label>
                                    <input type="number" step="0.01" name="discount_value" id="discountValue" class="form-control" value="0" oninput="calculateDiscount()">
                                </div>
                                <div id="discountInfo" class="alert alert-info d-none">
                                    <strong>Discount Amount:</strong> <span id="discountAmountDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shipping Section -->
                        <div class="card mb-3 bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Shipping</h6>
                                <div class="mb-3">
                                    <label>Shipping Method</label>
                                    <select name="shipping_id" id="shippingMethod" class="form-control" onchange="calculateTotal()">
                                        <option value="">No Shipping</option>
                                        @foreach($shippings as $shipping)
                                            <option value="{{ $shipping->id }}" data-cost="{{ $shipping->cost }}">
                                                {{ $shipping->name }} - ${{ number_format($shipping->cost, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="shippingInfo" class="alert alert-info d-none">
                                    <strong>Shipping Cost:</strong> <span id="shippingCostDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Total After Discount</label>
                            <input type="text" id="finalTotal" class="form-control fw-bold" value="${{ number_format($total, 2) }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label>Paid Amount</label>
                            <input type="number" step="0.01" name="paid_amount" id="paidAmount" class="form-control" required>
                        </div>
                        <input type="hidden" id="totalAmount" value="{{ $total }}">
                        <input type="hidden" id="discountAmount" name="discount_amount" value="0">
                        <input type="hidden" id="shippingCost" name="shipping_cost" value="0">
                        <button type="button" class="btn btn-success w-100" onclick="showCheckoutModal()">Checkout</button>
                    </form>
                @else
                    <p class="text-center">Cart is empty</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Checkout Confirmation Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">Konfirmasi Checkout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Subtotal:</strong>
                    <p class="fs-5 mb-0" id="modalSubtotal">$0.00</p>
                </div>
                <div class="mb-3" id="modalDiscountSection" style="display: none;">
                    <strong>Discount:</strong>
                    <p class="fs-5 text-danger mb-0" id="modalDiscount">- $0.00</p>
                </div>
                <div class="mb-3" id="modalShippingSection" style="display: none;">
                    <strong>Shipping:</strong>
                    <p class="fs-5 text-success mb-0" id="modalShipping">$0.00</p>
                </div>
                <hr>
                <div class="mb-3">
                    <strong>Total Cart:</strong>
                    <p class="fs-4 text-primary mb-0" id="modalTotal">$0.00</p>
                </div>
                <div class="mb-3">
                    <strong>Nominal Payment:</strong>
                    <p class="fs-4 text-success mb-0" id="modalPaid">$0.00</p>
                </div>
                <div class="mb-3">
                    <strong>Kembalian:</strong>
                    <p class="fs-4 text-info mb-0" id="modalChange">$0.00</p>
                </div>
                <div id="errorMessage" class="alert alert-danger d-none">
                    <strong>Error!</strong> Nominal payment lebih kecil dari total!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmCheckout">Konfirmasi Checkout</button>
            </div>
        </div>
    </div>
</div>

<script>
    let checkoutModal;
    
    document.addEventListener('DOMContentLoaded', function() {
        checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    });
    
    function calculateDiscount() {
        calculateTotal();
    }
    
    function calculateTotal() {
        const discountType = document.getElementById('discountType').value;
        const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;
        const subtotal = parseFloat(document.getElementById('totalAmount').value) || 0;
        
        let discountAmount = 0;
        const discountValueDiv = document.getElementById('discountValueDiv');
        const discountInfo = document.getElementById('discountInfo');
        const discountValueLabel = document.getElementById('discountValueLabel');
        
        if (discountType === 'none') {
            discountValueDiv.style.display = 'none';
            discountInfo.classList.add('d-none');
            discountAmount = 0;
        } else {
            discountValueDiv.style.display = 'block';
            discountInfo.classList.remove('d-none');
            
            if (discountType === 'percentage') {
                discountValueLabel.textContent = 'Discount Percentage (%)';
                discountAmount = (subtotal * discountValue) / 100;
            } else if (discountType === 'nominal') {
                discountValueLabel.textContent = 'Discount Amount ($)';
                discountAmount = discountValue;
            }
        }
        
        // Get shipping cost
        const shippingSelect = document.getElementById('shippingMethod');
        const shippingCost = shippingSelect.selectedIndex > 0 
            ? parseFloat(shippingSelect.options[shippingSelect.selectedIndex].dataset.cost) || 0 
            : 0;
        
        // Show/hide shipping info
        const shippingInfo = document.getElementById('shippingInfo');
        if (shippingCost > 0) {
            shippingInfo.classList.remove('d-none');
            document.getElementById('shippingCostDisplay').textContent = '$' + shippingCost.toFixed(2);
        } else {
            shippingInfo.classList.add('d-none');
        }
        
        // Update discount display
        document.getElementById('discountAmountDisplay').textContent = '$' + discountAmount.toFixed(2);
        document.getElementById('discountAmount').value = discountAmount.toFixed(2);
        document.getElementById('shippingCost').value = shippingCost.toFixed(2);
        
        // Calculate final total (subtotal - discount + shipping)
        const finalTotal = subtotal - discountAmount + shippingCost;
        document.getElementById('finalTotal').value = '$' + finalTotal.toFixed(2);
    }
    
    function showCheckoutModal() {
        const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;
        const subtotal = parseFloat(document.getElementById('totalAmount').value) || 0;
        const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;
        const shippingCost = parseFloat(document.getElementById('shippingCost').value) || 0;
        const totalAmount = subtotal - discountAmount + shippingCost;
        const changeAmount = paidAmount - totalAmount;
        
        // Update modal content
        document.getElementById('modalSubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('modalDiscount').textContent = '- $' + discountAmount.toFixed(2);
        document.getElementById('modalShipping').textContent = '$' + shippingCost.toFixed(2);
        document.getElementById('modalTotal').textContent = '$' + totalAmount.toFixed(2);
        document.getElementById('modalPaid').textContent = '$' + paidAmount.toFixed(2);
        document.getElementById('modalChange').textContent = '$' + changeAmount.toFixed(2);
        
        // Show/hide discount section
        const modalDiscountSection = document.getElementById('modalDiscountSection');
        if (discountAmount > 0) {
            modalDiscountSection.style.display = 'block';
        } else {
            modalDiscountSection.style.display = 'none';
        }
        
        // Show/hide shipping section
        const modalShippingSection = document.getElementById('modalShippingSection');
        if (shippingCost > 0) {
            modalShippingSection.style.display = 'block';
        } else {
            modalShippingSection.style.display = 'none';
        }
        
        // Show/hide error message
        const errorDiv = document.getElementById('errorMessage');
        const confirmBtn = document.getElementById('confirmCheckout');
        
        if (paidAmount < totalAmount) {
            errorDiv.classList.remove('d-none');
            confirmBtn.disabled = true;
        } else {
            errorDiv.classList.add('d-none');
            confirmBtn.disabled = false;
        }
        
        checkoutModal.show();
    }
    
    document.getElementById('confirmCheckout').addEventListener('click', function() {
        document.getElementById('checkoutForm').submit();
    });
</script>
@endsection
