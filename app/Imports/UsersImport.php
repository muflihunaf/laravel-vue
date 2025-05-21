<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersImport implements ToCollection, WithHeadingRow
{
    protected $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $userData = [];
            
            // Map the fields from the Excel file to user data
            foreach ($this->fields as $field) {
                if (isset($row[$field])) {
                    switch ($field) {
                        case 'name':
                            $userData['name'] = $row[$field];
                            break;
                        case 'email':
                            $userData['email'] = $row[$field];
                            break;
                        case 'roles':
                            $roleNames = explode(',', $row[$field]);
                            $roleIds = Role::whereIn('name', array_map('trim', $roleNames))->pluck('uuid')->toArray();
                            $userData['roles'] = $roleIds;
                            break;
                    }
                }
            }

            // Generate a random password for new users
            $password = Str::random(12);
            $userData['password'] = Hash::make($password);

            // Create or update the user
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password']
                ]
            );

            // Sync roles if they were provided
            if (isset($userData['roles'])) {
                $user->roles()->sync($userData['roles']);
            }
        }
    }
} 