<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

abstract class BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected $fields;
    protected $query;

    public function __construct(array $fields = [], $query = null)
    {
        $this->fields = $fields;
        $this->query = $query;
    }

    public function collection()
    {
        $query = $this->query ?: $this->getQuery();
        return $query instanceof \Illuminate\Database\Eloquent\Builder ? $query->get() : $query;
    }

    public function headings(): array
    {
        return $this->fields;
    }

    abstract protected function getQuery();

    abstract public function map($row): array;
} 