<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function collection()
    {
        return User::with('roles')->get();
    }

    public function headings(): array
    {
        return $this->fields;
    }

    public function map($user): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            switch ($field) {
                case 'name':
                    $data[] = $user->name;
                    break;
                case 'email':
                    $data[] = $user->email;
                    break;
                case 'roles':
                    $data[] = $user->roles->pluck('name')->join(', ');
                    break;
                default:
                    $data[] = '';
            }
        }
        return $data;
    }
} 