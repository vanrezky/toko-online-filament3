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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->nullable();
            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
            $table->unsignedInteger('code')->nullable();
            $table->string('name')->nullable();
            $table->string('alias');
            $table->boolean('status')->comment('1 = active, 0 = inactive')->default(true);
            $table->json('gateway_parameters')->nullable();
            $table->json('support_currencies')->nullable();
            $table->json('extra_data')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });


        Schema::create('payment_gateway_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('currency', 40);
            $table->string('symbol', 40);
            $table->foreignId('payment_gateway_id')->references('id')->on('payment_gateways')->onDelete('cascade');
            $table->double('min_amount', 15, 2);
            $table->double('max_amount', 15, 2);
            $table->decimal('percent_charge', 5, 2);
            $table->double('fixed_charge', 15, 2);
            $table->decimal('rate', 28, 2);
            $table->string('image')->nullable();
            $table->json('gateway_parameters')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_currencies');
        Schema::dropIfExists('payment_gateways');
    }
};
