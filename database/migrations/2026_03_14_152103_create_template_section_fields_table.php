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
        Schema::create('template_section_fields', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('section_id')->constrained('template_sections')->cascadeOnDelete();
            $table->string('key')->comment('Field key/identifier, e.g. title, subtitle, description, image_url, button_text, button_link');
            $table->string('label')->comment('Human-readable label for the field');
            $table->string('type')->default('text')->comment('text, textarea, image, url, select, toggle, color, number, richtext');
            $table->string('placeholder')->nullable();
            $table->text('default_value')->nullable();
            $table->json('options')->nullable()->comment('For select fields: [{label, value}]');
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('order_priority')->default(0);
            $table->timestamps();

            $table->unique(['section_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_section_fields');
    }
};
