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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('variant_name')->comment('Red - XL');
            $table->string('sku', 100)->unique();
            $table->decimal('price', 15, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('weight')->nullable();
            $table->string('dimensions')->nullable();
            $table->boolean('status')->default(true);
            $table->text('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
