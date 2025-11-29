<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create User
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create Categories and Products
        $categories = \App\Models\Category::factory(5)->create();
        
        $products = [];
        foreach ($categories as $category) {
            $products = array_merge($products, \App\Models\Product::factory(10)->create(['category_id' => $category->id])->all());
        }

        // Create Customers
        $customers = \App\Models\Customer::factory(20)->create();

        // Create Transactions
        \App\Models\Transaction::factory(100)->make()->each(function ($transaction) use ($customers, $products) {
            $transaction->customer_id = $customers->random()->id;
            $transaction->user_id = 1;
            $transaction->save();

            $total_amount = 0;
            $details = \App\Models\TransactionDetail::factory(rand(1, 5))->make();
            
            foreach ($details as $detail) {
                $product = collect($products)->random();
                $detail->transaction_id = $transaction->id;
                $detail->product_id = $product->id;
                $detail->price = $product->price;
                $detail->subtotal = $detail->quantity * $detail->price;
                $detail->save();

                $total_amount += $detail->subtotal;
            }

            $transaction->total_amount = $total_amount;
            $transaction->paid_amount = $total_amount + rand(0, 50);
            $transaction->change_amount = $transaction->paid_amount - $total_amount;
            $transaction->save();
        });
    }
}
