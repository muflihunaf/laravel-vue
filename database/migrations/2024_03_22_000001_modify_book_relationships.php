<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to books table
        Schema::table('books', function (Blueprint $table) {
            $table->uuid('author_uuid')->nullable()->after('is_available');
            $table->uuid('category_uuid')->nullable()->after('author_uuid');
            
            $table->foreign('author_uuid')
                ->references('uuid')
                ->on('authors')
                ->onDelete('set null');
                
            $table->foreign('category_uuid')
                ->references('uuid')
                ->on('categories')
                ->onDelete('set null');
        });

        // Migrate existing data
        DB::statement('
            UPDATE books b
            LEFT JOIN author_book ab ON b.uuid = ab.book_uuid
            LEFT JOIN authors a ON ab.author_uuid = a.uuid
            SET b.author_uuid = a.uuid
            WHERE ab.author_uuid IS NOT NULL
        ');

        DB::statement('
            UPDATE books b
            LEFT JOIN book_category bc ON b.uuid = bc.book_uuid
            LEFT JOIN categories c ON bc.category_uuid = c.uuid
            SET b.category_uuid = c.uuid
            WHERE bc.category_uuid IS NOT NULL
        ');

        // Drop old pivot tables
        Schema::dropIfExists('author_book');
        Schema::dropIfExists('book_category');
    }

    public function down(): void
    {
        // Recreate pivot tables
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

        // Migrate data back to pivot tables
        DB::statement('
            INSERT INTO author_book (author_uuid, book_uuid, created_at, updated_at)
            SELECT author_uuid, uuid, NOW(), NOW()
            FROM books
            WHERE author_uuid IS NOT NULL
        ');

        DB::statement('
            INSERT INTO book_category (category_uuid, book_uuid, created_at, updated_at)
            SELECT category_uuid, uuid, NOW(), NOW()
            FROM books
            WHERE category_uuid IS NOT NULL
        ');

        // Remove new columns
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['author_uuid']);
            $table->dropForeign(['category_uuid']);
            $table->dropColumn(['author_uuid', 'category_uuid']);
        });
    }
}; 