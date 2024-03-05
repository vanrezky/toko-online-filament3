<?php

use App\Enums\KycStatus;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->double('balance', 15, 2)->default(0);
            $table->string('image')->nullable();
            $table->string('is_active')->default(StatusType::ACTIVE->value);
            $table->string('password');
            $table->unsignedBigInteger('distributor_level_id')->nullable();
            $table->foreign('distributor_level_id')->references('id')->on('distributor_levels')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
