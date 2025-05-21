<?php

namespace App\Exports;

use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;

class AuthorsExport extends BaseExport
{
    protected function getQuery()
    {
        $allowed = ['uuid', 'name', 'biography', 'metadata', 'is_active', 'created_at', 'updated_at', 'deleted_at'];
        $fields = array_intersect($this->fields, $allowed);
        return Author::when(!empty($fields), function (Builder $query) use ($fields) {
            $query->select(array_merge(['uuid'], $fields));
        });
    }

    public function map($author): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            $data[] = $author->{$field};
        }
        return $data;
    }
} 