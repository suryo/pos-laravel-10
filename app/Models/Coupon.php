<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code', 'type', 'discount_value', 'discount_type', 'free_product_id', 
        'free_product_qty', 'min_purchase', 'max_uses', 'used_count', 
        'valid_from', 'valid_until', 'is_active', 'description'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function freeProduct()
    {
        return $this->belongsTo(\App\Models\Product::class, 'free_product_id');
    }
}
