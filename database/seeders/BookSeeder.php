<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'description' => 'The first book in the Harry Potter series',
                'isbn' => '978-0747532743',
                'publication_year' => 1997,
                'is_available' => true,
                'author' => 'J.K. Rowling',
                'categories' => ['Fantasy', 'Fiction'],
            ],
            [
                'title' => 'A Game of Thrones',
                'description' => 'The first book in A Song of Ice and Fire series',
                'isbn' => '978-0553103540',
                'publication_year' => 1996,
                'is_available' => true,
                'author' => 'George R.R. Martin',
                'categories' => ['Fantasy', 'Fiction'],
            ],
            [
                'title' => 'The Shining',
                'description' => 'A horror novel by Stephen King',
                'isbn' => '978-0385121675',
                'publication_year' => 1977,
                'is_available' => true,
                'author' => 'Stephen King',
                'categories' => ['Horror', 'Fiction'],
            ],
            [
                'title' => 'Murder on the Orient Express',
                'description' => 'A detective novel by Agatha Christie',
                'isbn' => '978-0062073495',
                'publication_year' => 1934,
                'is_available' => true,
                'author' => 'Agatha Christie',
                'categories' => ['Mystery', 'Fiction'],
            ],
            [
                'title' => 'Foundation',
                'description' => 'The first book in the Foundation series',
                'isbn' => '978-0553293357',
                'publication_year' => 1951,
                'is_available' => true,
                'author' => 'Isaac Asimov',
                'categories' => ['Science Fiction', 'Fiction'],
            ],
            [
                'title' => 'Pride and Prejudice',
                'description' => 'A romantic novel by Jane Austen',
                'isbn' => '978-0141439518',
                'publication_year' => 1813,
                'is_available' => true,
                'author' => 'Jane Austen',
                'categories' => ['Romance', 'Fiction'],
            ],
            [
                'title' => 'The Old Man and the Sea',
                'description' => 'A novel by Ernest Hemingway',
                'isbn' => '978-0684801223',
                'publication_year' => 1952,
                'is_available' => true,
                'author' => 'Ernest Hemingway',
                'categories' => ['Fiction'],
            ],
            [
                'title' => 'To the Lighthouse',
                'description' => 'A novel by Virginia Woolf',
                'isbn' => '978-0156907392',
                'publication_year' => 1927,
                'is_available' => true,
                'author' => 'Virginia Woolf',
                'categories' => ['Fiction'],
            ],
            [
                'title' => 'The Great Gatsby',
                'description' => 'A novel by F. Scott Fitzgerald',
                'isbn' => '978-0743273565',
                'publication_year' => 1925,
                'is_available' => true,
                'author' => 'F. Scott Fitzgerald',
                'categories' => ['Fiction'],
            ],
            [
                'title' => 'Beloved',
                'description' => 'A novel by Toni Morrison',
                'isbn' => '978-1400033416',
                'publication_year' => 1987,
                'is_available' => true,
                'author' => 'Toni Morrison',
                'categories' => ['Fiction'],
            ],
        ];

        foreach ($books as $bookData) {
            $author = Author::where('name', $bookData['author'])->first();
            $categories = Category::whereIn('name', $bookData['categories'])->get();

            if ($author) {
                $book = Book::create([
                    'title' => $bookData['title'],
                    'description' => $bookData['description'],
                    'isbn' => $bookData['isbn'],
                    'publication_year' => $bookData['publication_year'],
                    'is_available' => $bookData['is_available'],
                ]);

                $book->authors()->attach($author);
                $book->categories()->attach($categories);
            }
        }
    }
} 