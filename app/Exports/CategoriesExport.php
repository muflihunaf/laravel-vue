<?php

namespace App\Exports;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CategoriesExport extends BaseExport
{
    protected function getQuery()
    {
        $allowed = ['uuid', 'name', 'description', 'metadata', 'is_active', 'created_at', 'updated_at', 'deleted_at'];
        $fields = array_intersect($this->fields, $allowed);
        return Category::when(!empty($fields), function (Builder $query) use ($fields) {
            $query->select(array_merge(['uuid'], $fields));
        });
    }

    public function map($category): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            $data[] = $category->{$field};
        }
        return $data;
    }
} 