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
        Schema::table('provinces', function (Blueprint $table) {
            $table->string('apicoid_code', 20)->nullable();
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->string('apicoid_code', 20)->nullable();
        });

        Schema::table('sub_districts', function (Blueprint $table) {
            $table->string('apicoid_code', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn('apicoid_code');
        });
        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn('apicoid_code');
        });
        Schema::table('sub_districts', function (Blueprint $table) {
            $table->dropColumn('apicoid_code');
        });
    }
};
