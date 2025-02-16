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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('customer_id')->constrained('customers')->noActionOnDelete();
            $table->dateTime('timelimit')->nullable();
            $table->foreignId('customer_address_id')->constrained('customer_addresses')->noActionOnDelete();
            $table->unsignedBigInteger('weight')->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->unsignedBigInteger('courier_id');
            $table->foreignId('from_district_id')->constrained('districts')->noActionOnDelete();
            $table->foreignId('to_district_id')->constrained('districts')->noActionOnDelete();
            $table->boolean('cod')->default(false);
            $table->decimal('cod_fee', 15, 2)->default(0);
            $table->string('receipt_code')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->enum('status', ['unpaid', 'shipped', 'delivered', 'rejected', 'completed'])->default('unpaid');
            $table->dateTime('complete_date')->nullable();
            $table->boolean('request_cancellation')->default(false);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id']);
            $table->index(['customer_address_id']);
            $table->index(['courier_id']);
            $table->index(['from_district_id']);
            $table->index(['to_district_id']);
            $table->index(['receipt_code']);
            $table->index(['delivery_date']);
            $table->index(['status']);
            $table->index(['complete_date']);
            $table->index(['request_cancellation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
