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
        Schema::create('cart_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->string('voucher_code');
            $table->decimal('discount_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_vouchers');
    }
};
