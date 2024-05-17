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
        Schema::create('product_wholesales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->nullable()->references('id')->on('resellers')->onDelete('cascade');
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('min_qty');
            $table->unsignedInteger('max_qty');
            $table->double('price', 15, 2);
            $table->timestamps();

            // Tambahkan unique constraint untuk reseller_id dan product_id jika reseller_id tidak null
            $table->unique(['reseller_id', 'product_id', 'min_qty', 'max_qty'], 'reseller_product_qty_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_wholesales');
    }
};
