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
        Schema::table('carts', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->unique()->nullable();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
