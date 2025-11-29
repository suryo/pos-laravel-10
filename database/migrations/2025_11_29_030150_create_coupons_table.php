<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Coupon code
            $table->string('type'); // 'discount' or 'free_product'
            $table->decimal('discount_value', 10, 2)->nullable(); // For discount type (percentage or nominal)
            $table->string('discount_type')->nullable(); // 'percentage' or 'nominal'
            $table->foreignId('free_product_id')->nullable()->constrained('products'); // For free product type
            $table->integer('free_product_qty')->default(1); // Quantity of free product
            $table->decimal('min_purchase', 12, 2)->default(0); // Minimum purchase to use coupon
            $table->integer('max_uses')->nullable(); // Max total uses (null = unlimited)
            $table->integer('used_count')->default(0); // How many times used
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
