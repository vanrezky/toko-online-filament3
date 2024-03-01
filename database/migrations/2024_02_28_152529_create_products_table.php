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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug');
            $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreignId('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->boolean('digital')->default(false);
            $table->tinyText('digital_url')->nullable();
            $table->text('description');
            $table->string('code');
            $table->tinyText('images');
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('weight');
            $table->double('price', 15, 2);
            $table->double('sale_price', 15, 2)->default(0);
            $table->double('afiliate_price', 15, 2)->default(0);
            $table->unsignedInteger('min_order')->default(1);
            $table->string('varian')->nullable();
            $table->string('subvarian')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
