<?php

use App\Enums\StatusType;
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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreignId('province_id')->references('id')->on('provinces')->onDelete('cascade');
            $table->foreignId('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreignId('sub_district_id')->references('id')->on('sub_districts')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->string('postal_code');
            $table->boolean('is_active')->default(StatusType::ACTIVE->value);
            $table->boolean('is_featured')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
