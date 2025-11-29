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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('payment_method'); // 'percentage', 'nominal', 'none'
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type'); // percentage value or nominal value
            $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_value'); // calculated discount amount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_amount']);
        });
    }
};
