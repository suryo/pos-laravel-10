<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$coupon = App\Models\Coupon::where('code', 'FREEBIE')->first();

if ($coupon) {
    echo "Coupon Code: " . $coupon->code . PHP_EOL;
    echo "Type: " . $coupon->type . PHP_EOL;
    echo "Free Product ID: " . $coupon->free_product_id . PHP_EOL;
    echo "Min Purchase: $" . $coupon->min_purchase . PHP_EOL;
    echo "Is Active: " . ($coupon->is_active ? 'Yes' : 'No') . PHP_EOL;
    
    if ($coupon->free_product_id) {
        $product = App\Models\Product::find($coupon->free_product_id);
        if ($product) {
            echo "Free Product Name: " . $product->name . PHP_EOL;
            echo "Free Product Price: $" . $product->price . PHP_EOL;
        } else {
            echo "ERROR: Free product not found!" . PHP_EOL;
        }
    }
} else {
    echo "Coupon FREEBIE not found!" . PHP_EOL;
}
