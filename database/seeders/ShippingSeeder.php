<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shipping;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippings = [
            [
                'name' => 'Regular Shipping',
                'cost' => 5.00,
                'description' => 'Standard delivery in 5-7 business days',
                'is_active' => true,
            ],
            [
                'name' => 'Express Shipping',
                'cost' => 15.00,
                'description' => 'Fast delivery in 2-3 business days',
                'is_active' => true,
            ],
            [
                'name' => 'Same Day Delivery',
                'cost' => 25.00,
                'description' => 'Delivery on the same day (order before 12 PM)',
                'is_active' => true,
            ],
            [
                'name' => 'Next Day Delivery',
                'cost' => 20.00,
                'description' => 'Guaranteed delivery next business day',
                'is_active' => true,
            ],
            [
                'name' => 'Free Shipping',
                'cost' => 0.00,
                'description' => 'Free standard shipping (for orders over $100)',
                'is_active' => true,
            ],
        ];

        foreach ($shippings as $shipping) {
            Shipping::create($shipping);
        }
    }
}
