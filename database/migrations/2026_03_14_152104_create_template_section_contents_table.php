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
        Schema::create('template_section_contents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('section_id')->constrained('template_sections')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('template_section_fields')->cascadeOnDelete();
            $table->text('value')->nullable()->comment('Stored value for the field');
            $table->json('meta')->nullable()->comment('Additional metadata, e.g. image alt text, link target, etc.');
            $table->timestamps();

            $table->unique(['section_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_section_contents');
    }
};
