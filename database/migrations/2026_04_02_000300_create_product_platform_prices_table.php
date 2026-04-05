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
        Schema::create('product_platform_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained()->restrictOnDelete();
            $table->decimal('total_deduction_percent', 5, 2);
            $table->decimal('fixed_fee_amount', 14, 2)->default(0);
            $table->decimal('selling_price', 14, 2);
            $table->decimal('net_profit', 14, 2);
            $table->timestamps();

            $table->unique(['product_id', 'platform_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_platform_prices');
    }
};
