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
            if (!isset($row[$field])) {
                continue;
            }

            switch ($field) {
                case 'author':
                    $author = Author::firstOrCreate(
                        ['name' => $row['author']],
                        ['uuid' => (string) Str::uuid()]
                    );
                    $data['author_uuid'] = $author->uuid;
                    break;
                case 'category':
                    $category = Category::firstOrCreate(
                        ['name' => $row['category']],
                        ['uuid' => (string) Str::uuid()]
                    );
                    $data['category_uuid'] = $category->uuid;
                    break;
                case 'isbn':
                    $data[$field] = (string) $row[$field];
                    break;
                case 'published_at':
                    $data['publication_year'] = (int) $row[$field];
                    break;
                default:
                    $data[$field] = $row[$field];
            }
        }

        return Book::create(array_merge($data, ['uuid' => (string) Str::uuid()]));
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'isbn' => 'required|max:13',
            'published_at' => 'required|integer|min:1800|max:' . (date('Y') + 1),
        ];
    }
} 