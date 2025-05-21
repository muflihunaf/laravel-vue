<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            [
                'name' => 'J.K. Rowling',
                'biography' => 'British author best known for the Harry Potter series',
                'is_active' => true,
            ],
            [
                'name' => 'George R.R. Martin',
                'biography' => 'American novelist and short story writer, best known for A Song of Ice and Fire series',
                'is_active' => true,
            ],
            [
                'name' => 'Stephen King',
                'biography' => 'American author of horror, supernatural fiction, suspense, and fantasy novels',
                'is_active' => true,
            ],
            [
                'name' => 'Agatha Christie',
                'biography' => 'English writer known for her detective novels',
                'is_active' => true,
            ],
            [
                'name' => 'Isaac Asimov',
                'biography' => 'American writer and professor of biochemistry, known for science fiction works',
                'is_active' => true,
            ],
            [
                'name' => 'Jane Austen',
                'biography' => 'English novelist known for romantic fiction',
                'is_active' => true,
            ],
            [
                'name' => 'Ernest Hemingway',
                'biography' => 'American novelist, short story writer, and journalist',
                'is_active' => true,
            ],
            [
                'name' => 'Virginia Woolf',
                'biography' => 'English writer, considered one of the most important modernist authors',
                'is_active' => true,
            ],
            [
                'name' => 'F. Scott Fitzgerald',
                'biography' => 'American novelist and short story writer',
                'is_active' => true,
            ],
            [
                'name' => 'Toni Morrison',
                'biography' => 'American novelist and essayist, winner of the Nobel Prize in Literature',
                'is_active' => true,
            ],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }
    }
} 