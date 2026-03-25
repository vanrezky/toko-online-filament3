<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['status', 'complete_date']);
            $table->index(['created_at', 'status']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index(['created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'stock']);
            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['status', 'complete_date']);
            $table->dropIndex(['created_at', 'status']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'stock']);
            $table->dropIndex(['category_id', 'is_active']);
        });
    }
};