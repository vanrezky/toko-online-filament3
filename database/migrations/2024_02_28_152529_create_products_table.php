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
            $table->uuid()->index();
            $table->string('name')->index();
            $table->string('slug');
            $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->boolean('digital')->default(false);
            $table->tinyText('digital_url')->nullable();
            $table->text('description');
            $table->string('code');
            $table->text('images');
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('weight')->nullable();
            $table->double('price', 15, 2);
            $table->double('sale_price', 15, 2)->nullable();
            $table->double('afiliate_price', 15, 2)->nullable();
            $table->unsignedInteger('min_order')->default(1);
            $table->string('variant')->nullable();
            $table->string('sub_variant')->nullable();
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
