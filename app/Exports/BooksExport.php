<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BooksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $fields;
    protected $isTemplate;

    public function __construct(array $fields, bool $isTemplate = false)
    {
        $this->fields = $fields;
        $this->isTemplate = $isTemplate;
    }

    public function collection()
    {
        if ($this->isTemplate) {
            return collect([new Book()]);
        }
        return Book::with(['authors', 'categories'])->get();
    }

    public function headings(): array
    {
        return $this->fields;
    }

    public function map($book): array
    {
        if ($this->isTemplate) {
            return array_fill_keys($this->fields, '');
        }

        $data = [];
        foreach ($this->fields as $field) {
            switch ($field) {
                case 'title':
                    $data[$field] = $book->title;
                    break;
                case 'isbn':
                    $data[$field] = $book->isbn;
                    break;
                case 'description':
                    $data[$field] = $book->description;
                    break;
                case 'published_at':
                    $data[$field] = $book->published_at?->format('Y-m-d');
                    break;
                case 'author':
                    $data[$field] = $book->authors->pluck('name')->join(', ');
                    break;
                case 'categories':
                    $data[$field] = $book->categories->pluck('name')->join(', ');
                    break;
            }
        }
        return $data;
    }
} 