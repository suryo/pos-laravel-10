<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $products = $query->paginate(12);
        $cart = session()->get('cart', []);
        $shippings = \App\Models\Shipping::where('is_active', true)->get();
        return view('pos.index', compact('cart', 'products', 'shippings'));
    }

    public function addToCart($id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function updateCart(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
        
        return redirect()->back();
    }

    public function applyCoupon(Request $request)
    {
        $coupon = \App\Models\Coupon::where('code', $request->coupon_code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Invalid coupon code!');
        }

        // Check expiry
        if ($coupon->valid_from && now()->lt($coupon->valid_from)) {
            return redirect()->back()->with('error', 'Coupon is not yet valid!');
        }
        if ($coupon->valid_until && now()->gt($coupon->valid_until)) {
            return redirect()->back()->with('error', 'Coupon has expired!');
        }

        // Check max uses
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return redirect()->back()->with('error', 'Coupon usage limit reached!');
        }

        // Calculate cart total
        $cart = session()->get('cart', []);
        $cartTotal = 0;
        foreach ($cart as $details) {
            $cartTotal += $details['price'] * $details['quantity'];
        }

        // Check minimum purchase
        if ($cartTotal < $coupon->min_purchase) {
            return redirect()->back()->with('error', 'Minimum purchase of $' . number_format($coupon->min_purchase, 2) . ' required!');
        }

        // Calculate coupon discount
        $couponDiscount = 0;
        $successMessage = 'Coupon applied successfully!';
        
        if ($coupon->type == 'discount') {
            if ($coupon->discount_type == 'percentage') {
                $couponDiscount = ($cartTotal * $coupon->discount_value) / 100;
            } else {
                $couponDiscount = $coupon->discount_value;
            }
            $successMessage = 'Coupon applied! You saved $' . number_format($couponDiscount, 2);
        } elseif ($coupon->type == 'free_product') {
            // Add free product to cart
            $freeProduct = \App\Models\Product::find($coupon->free_product_id);
            if ($freeProduct) {
                $freeProductId = 'free_' . $freeProduct->id;
                $cart[$freeProductId] = [
                    'name' => $freeProduct->name . ' (FREE)',
                    'quantity' => $coupon->free_product_qty,
                    'price' => 0,
                    'image' => $freeProduct->image,
                    'is_free' => true
                ];
                // Important: Save cart immediately after adding free product
                session()->put('cart', $cart);
                $successMessage = 'Coupon applied! Free product "' . $freeProduct->name . '" added to cart!';
            }
        }

        // Store coupon in session
        session()->put('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'discount' => $couponDiscount
        ]);

        return redirect()->back()->with('success', $successMessage);
    }

    public function removeCoupon()
    {
        // Remove free products from cart
        $cart = session()->get('cart', []);
        foreach ($cart as $id => $item) {
            if (isset($item['is_free']) && $item['is_free']) {
                unset($cart[$id]);
            }
        }
        session()->put('cart', $cart);
        
        // Remove coupon from session
        session()->forget('applied_coupon');
        
        return redirect()->back()->with('success', 'Coupon removed successfully!');
    }
}
