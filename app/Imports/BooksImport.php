<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Support\Str;

class BooksImport extends BaseImport
{
    public function model(array $row)
    {
        $data = [];
        foreach ($this->fields as $field) {
            switch ($field) {
                case 'author':
                    $author = Author::firstOrCreate(
                        ['name' => $row['author']],
                        ['uuid' => (string) Str::uuid()]
                    );
                    $data['author_id'] = $author->id;
                    break;
                case 'categories':
                    $categoryIds = collect(explode(',', $row['categories']))
                        ->map(function ($name) {
                            return Category::firstOrCreate(
                                ['name' => trim($name)],
                                ['uuid' => (string) Str::uuid()]
                            )->id;
                        });
                    $data['category_ids'] = $categoryIds;
                    break;
                default:
                    $data[$field] = $row[$field];
            }
        }

        $book = Book::create(array_merge($data, ['uuid' => (string) Str::uuid()]));
        
        if (isset($data['category_ids'])) {
            $book->categories()->sync($data['category_ids']);
        }

        return $book;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:13',
            'published_at' => 'required|date',
            'categories' => 'required|string',
        ];
    }
} 