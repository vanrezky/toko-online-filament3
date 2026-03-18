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
        Schema::create('template_sections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('template_id')->constrained('templates')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->comment('e.g. hero, stories, banner, gallery, cta, testimonials, products, etc.');
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->comment('Heroicon or icon identifier for display');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order_priority')->default(0)->comment('SortableJS ordering');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_sections');
    }
};
