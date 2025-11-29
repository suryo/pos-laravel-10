<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PosFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_flow()
    {
        // Create User
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // 1. Create Category and Product
        $category = Category::create(['name' => 'Test Category']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'price' => 100,
            'stock' => 10,
            'barcode' => '123456'
        ]);

        // 2. Simulate Add to Cart (Session)
        $cart = [
            $product->id => [
                "name" => $product->name,
                "quantity" => 2,
                "price" => $product->price,
                "image" => null
            ]
        ];
        session()->put('cart', $cart);

        // 3. Checkout
        $response = $this->post(route('transactions.store'), [
            'paid_amount' => 200,
            'payment_method' => 'cash'
        ]);

        // 4. Assertions
        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'total_amount' => 200,
            'paid_amount' => 200,
            'change_amount' => 0
        ]);

        $this->assertDatabaseHas('transaction_details', [
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 200
        ]);

        // Check stock deduction
        $this->assertEquals(8, $product->fresh()->stock);
    }
}
