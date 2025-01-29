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
        Schema::create('product_attribute_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_global')->default(false)->index();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->timestamps();

            // Jika opsi global, pastikan tidak ada duplikasi nama dalam opsi atribut
            $table->unique(['name', 'product_attribute_id', 'is_global'], 'unique_global_attribute_options');
            // Jika opsi hanya untuk produk tertentu, pastikan tidak ada duplikasi dalam satu produk
            $table->unique(['name', 'product_attribute_id', 'product_id'], 'unique_product_specific_attribute_option');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_options');
    }
};
