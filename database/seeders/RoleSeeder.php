<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrator role
        $administrator = Role::create([
            'name' => 'Administrator',
            'description' => 'Full system access',
        ]);

        // Give administrator all permissions
        $administrator->permissions()->attach(Permission::all());

        // Librarian role
        $librarian = Role::create([
            'name' => 'Librarian',
            'description' => 'Library management access',
        ]);

        // Give librarian library-related permissions
        $librarian->permissions()->attach(
            Permission::whereIn('name', [
                'view_books', 'create_books', 'edit_books', 'delete_books', 'download_books',
                'view_authors', 'create_authors', 'edit_authors', 'delete_authors',
                'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
                'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
            ])->get()
        );

        // Reader role
        $reader = Role::create([
            'name' => 'Reader',
            'description' => 'Basic reading access',
        ]);

        // Give reader view and download permissions
        $reader->permissions()->attach(
            Permission::whereIn('name', [
                'view_books',
                'download_books',
                'view_authors',
                'view_categories',
            ])->get()
        );
    }
} 