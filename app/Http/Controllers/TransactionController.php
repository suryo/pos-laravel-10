<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::latest();

        if ($request->has('search')) {
            $query->where('id', $request->search);
        }

        $transactions = $query->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart');
        if (!$cart) {
            return redirect()->back()->with('error', 'Cart is empty!');
        }

        // Calculate total
        $total_amount = 0;
        foreach ($cart as $id => $details) {
            $total_amount += $details['price'] * $details['quantity'];
        }

        // Process discount
        $discount_type = $request->discount_type ?? 'none';
        $discount_value = floatval($request->discount_value ?? 0);
        $discount_amount = floatval($request->discount_amount ?? 0);
        
        // Process shipping
        $shipping_id = $request->shipping_id;
        $shipping_cost = floatval($request->shipping_cost ?? 0);
        
        // Process coupon
        $applied_coupon = session()->get('applied_coupon');
        $coupon_id = $applied_coupon['id'] ?? null;
        $coupon_code = $applied_coupon['code'] ?? null;
        $coupon_discount = floatval($applied_coupon['discount'] ?? 0);
        
        // Apply discount, shipping, and coupon to total
        $final_total = $total_amount - $discount_amount + $shipping_cost - $coupon_discount;

        // Validate paid amount against final total
        if ($request->paid_amount < $final_total) {
            return redirect()->back()->with('error', 'Nominal payment lebih kecil dari total!');
        }

        DB::beginTransaction();

        try {
            // The total_amount calculation is already done above, no need to repeat
            // $total_amount = 0;
            // foreach ($cart as $id => $details) {
            //     $total_amount += $details['price'] * $details['quantity'];
            // }

            $transaction = Transaction::create([
                'user_id' => auth()->id() ?? 1, // Default to 1 if no auth yet
                'customer_id' => $request->customer_id,
                'shipping_id' => $shipping_id,
                'coupon_id' => $coupon_id,
                'coupon_code' => $coupon_code,
                'total_amount' => $final_total,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->paid_amount - $final_total,
                'payment_method' => $request->payment_method ?? 'cash',
                'discount_type' => $discount_type,
                'discount_value' => $discount_value,
                'discount_amount' => $discount_amount,
                'shipping_cost' => $shipping_cost,
                'coupon_discount' => $coupon_discount
            ]);

            foreach ($cart as $id => $details) {
                // Skip free products from coupon
                if (isset($details['is_free']) && $details['is_free']) {
                    continue;
                }
                
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                    'subtotal' => $details['price'] * $details['quantity']
                ]);

                $product = Product::find($id);
                $product->decrement('stock', $details['quantity']);
            }

            // Increment coupon used_count
            if ($coupon_id) {
                \App\Models\Coupon::where('id', $coupon_id)->increment('used_count');
            }

            DB::commit();

            session()->forget('cart');
            session()->forget('applied_coupon');

            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Transaction completed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        return view('transactions.show', compact('transaction'));
    }
}
