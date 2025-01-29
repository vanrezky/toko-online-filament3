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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_global')->default(false)->index();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();

            // Jika global, nama harus unik agar tidak ada duplikasi
            $table->unique(['name', 'is_global'], 'unique_global_product_attributes');

            // Jika custom untuk produk tertentu, nama tidak boleh duplicate di dalam 1 produk
            $table->unique(['name', 'product_id'], 'unique_product_specific_attribute');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
