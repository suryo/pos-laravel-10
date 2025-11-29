<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'customer_id', 'shipping_id', 'coupon_id', 'coupon_code', 'total_amount', 'paid_amount', 'change_amount', 'payment_method', 'discount_type', 'discount_value', 'discount_amount', 'shipping_cost', 'coupon_discount'];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
