<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('authors')) {
            Schema::create('authors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('bio')->nullable();
                $table->string('photo')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('genres')) {
            Schema::create('genres', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('book_author')) {
            Schema::create('book_author', function (Blueprint $table) {
                $table->foreignId('book_id')->constrained()->onDelete('cascade');
                $table->foreignId('author_id')->constrained()->onDelete('cascade');
                $table->primary(['book_id', 'author_id']);
            });
        }

        if (!Schema::hasTable('book_genre')) {
            Schema::create('book_genre', function (Blueprint $table) {
                $table->foreignId('book_id')->constrained()->onDelete('cascade');
                $table->foreignId('genre_id')->constrained()->onDelete('cascade');
                $table->primary(['book_id', 'genre_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('book_genre');
        Schema::dropIfExists('book_author');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('authors');
    }
};
