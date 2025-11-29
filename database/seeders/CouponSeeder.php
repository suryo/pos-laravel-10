<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\Product;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a random product for free product coupon
        $randomProduct = Product::inRandomOrder()->first();
        
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'discount',
                'discount_value' => 10.00,
                'discount_type' => 'percentage',
                'free_product_id' => null,
                'free_product_qty' => 1,
                'min_purchase' => 50.00,
                'max_uses' => 100,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'is_active' => true,
                'description' => 'Get 10% off on orders above $50',
            ],
            [
                'code' => 'SAVE50',
                'type' => 'discount',
                'discount_value' => 50.00,
                'discount_type' => 'nominal',
                'free_product_id' => null,
                'free_product_qty' => 1,
                'min_purchase' => 200.00,
                'max_uses' => 50,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(2),
                'is_active' => true,
                'description' => 'Save $50 on orders above $200',
            ],
            [
                'code' => 'FREEBIE',
                'type' => 'free_product',
                'discount_value' => null,
                'discount_type' => null,
                'free_product_id' => $randomProduct ? $randomProduct->id : 1,
                'free_product_qty' => 1,
                'min_purchase' => 100.00,
                'max_uses' => null,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonth(),
                'is_active' => true,
                'description' => 'Get a free product on orders above $100',
            ],
            [
                'code' => 'MEGA20',
                'type' => 'discount',
                'discount_value' => 20.00,
                'discount_type' => 'percentage',
                'free_product_id' => null,
                'free_product_qty' => 1,
                'min_purchase' => 150.00,
                'max_uses' => 30,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addWeeks(2),
                'is_active' => true,
                'description' => 'Mega sale! 20% off on orders above $150',
            ],
            [
                'code' => 'FLASH25',
                'type' => 'discount',
                'discount_value' => 25.00,
                'discount_type' => 'nominal',
                'free_product_id' => null,
                'free_product_qty' => 1,
                'min_purchase' => 75.00,
                'max_uses' => 200,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addDays(7),
                'is_active' => true,
                'description' => 'Flash sale! $25 off on orders above $75',
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
