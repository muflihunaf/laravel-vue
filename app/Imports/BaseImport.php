<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;

abstract class BaseImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $fields;

    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    abstract public function model(array $row);

    public function rules(): array
    {
        return [];
    }

    public function customValidationMessages()
    {
        return [];
    }
} 