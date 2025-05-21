<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('isbn')->unique();
            $table->integer('publication_year');
            $table->string('file_path')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('author_book', function (Blueprint $table) {
            $table->uuid('author_uuid');
            $table->uuid('book_uuid');
            $table->timestamps();

            $table->foreign('author_uuid')
                ->references('uuid')
                ->on('authors')
                ->onDelete('cascade');

            $table->foreign('book_uuid')
                ->references('uuid')
                ->on('books')
                ->onDelete('cascade');

            $table->primary(['author_uuid', 'book_uuid']);
        });

        Schema::create('book_category', function (Blueprint $table) {
            $table->uuid('category_uuid');
            $table->uuid('book_uuid');
            $table->timestamps();

            $table->foreign('category_uuid')
                ->references('uuid')
                ->on('categories')
                ->onDelete('cascade');

            $table->foreign('book_uuid')
                ->references('uuid')
                ->on('books')
                ->onDelete('cascade');

            $table->primary(['category_uuid', 'book_uuid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_category');
        Schema::dropIfExists('author_book');
        Schema::dropIfExists('books');
    }
}; 