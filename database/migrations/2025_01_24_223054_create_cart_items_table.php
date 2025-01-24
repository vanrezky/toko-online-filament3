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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
