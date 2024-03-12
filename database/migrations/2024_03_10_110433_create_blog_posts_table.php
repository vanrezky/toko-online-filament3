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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('blog_category_id')->references('id')->on('blog_categories')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->longText('content');
            $table->date('published_at')->nullable();
            $table->boolean('is_status')->default(true);
            $table->string('image')->nullable();
            $table->bigIncrements('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
