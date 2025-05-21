<?php

namespace App\Exports;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;

class BooksExport extends BaseExport
{
    protected function getQuery()
    {
        $allowed = ['uuid', 'title', 'description', 'isbn', 'publication_year', 'file_path', 'metadata', 'is_available', 'created_at', 'updated_at', 'deleted_at'];
        $fields = array_intersect($this->fields, $allowed);
        return Book::with(['authors', 'categories'])
            ->when(!empty($fields), function (Builder $query) use ($fields) {
                $query->select(array_merge(['uuid'], $fields));
            });
    }

    public function map($book): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            switch ($field) {
                case 'author':
                    $data[] = $book->author?->name;
                    break;
                case 'categories':
                    $data[] = $book->categories->pluck('name')->join(', ');
                    break;
                default:
                    $data[] = $book->{$field};
            }
        }
        return $data;
    }
} 