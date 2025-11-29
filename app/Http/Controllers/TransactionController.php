<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart');
        if (!$cart) {
            return redirect()->back()->with('error', 'Cart is empty!');
        }

        DB::beginTransaction();

        try {
            $total_amount = 0;
            foreach ($cart as $id => $details) {
                $total_amount += $details['price'] * $details['quantity'];
            }

            $transaction = Transaction::create([
                'user_id' => auth()->id() ?? 1, // Default to 1 if no auth yet
                'customer_id' => $request->customer_id,
                'total_amount' => $total_amount,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->paid_amount - $total_amount,
                'payment_method' => $request->payment_method ?? 'cash'
            ]);

            foreach ($cart as $id => $details) {
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

            DB::commit();
            session()->forget('cart');

            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Transaction successful!');

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
