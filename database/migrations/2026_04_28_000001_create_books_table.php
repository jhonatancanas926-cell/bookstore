<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            
            $table->integer('price_cents')->default(0);
            $table->integer('original_price_cents')->nullable();
            $table->boolean('is_on_sale')->default(false);
            $table->integer('discount_percent')->default(0);
            
            $table->boolean('has_pdf')->default(false);
            $table->boolean('has_epub')->default(false);
            $table->boolean('has_physical')->default(false);
            $table->integer('stock')->default(0);
            
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            
            $table->integer('pages')->nullable();
            $table->string('language')->default('es');
            $table->string('publisher')->nullable();
            $table->timestamp('published_at')->nullable();
            
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_new_arrival')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
