<?php

use App\Enums\VoucherProductType;
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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyText('description')->nullable();
            $table->unsignedTinyInteger('voucher_type');
            $table->unsignedTinyInteger('discount_type');
            $table->unsignedTinyInteger('product_type')->default(VoucherProductType::ALL_PRODUCT->value)->comment('0 => all product, 1 => physical product, 2 => digital product');
            $table->tinyText('image')->nullable();
            $table->string('code', 50);
            $table->date('start_at');
            $table->date('end_at');
            $table->double('discount', 15, 2);
            $table->double('discount_min', 15, 2)->nullable();
            $table->double('discount_max', 15, 2)->nullable();
            $table->boolean('is_public')->default(true)->comment('0 => private, 1 => public');
            $table->unsignedInteger('max_user_used')->default(1);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
