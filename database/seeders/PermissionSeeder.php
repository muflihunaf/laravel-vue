<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // User permissions
        Permission::create(['name' => 'view_users', 'description' => 'Can view users']);
        Permission::create(['name' => 'create_users', 'description' => 'Can create users']);
        Permission::create(['name' => 'edit_users', 'description' => 'Can edit users']);
        Permission::create(['name' => 'delete_users', 'description' => 'Can delete users']);

        // Role permissions
        Permission::create(['name' => 'view_roles', 'description' => 'Can view roles']);
        Permission::create(['name' => 'create_roles', 'description' => 'Can create roles']);
        Permission::create(['name' => 'edit_roles', 'description' => 'Can edit roles']);
        Permission::create(['name' => 'delete_roles', 'description' => 'Can delete roles']);

        // Book permissions
        Permission::create(['name' => 'view_books', 'description' => 'Can view books']);
        Permission::create(['name' => 'create_books', 'description' => 'Can create books']);
        Permission::create(['name' => 'edit_books', 'description' => 'Can edit books']);
        Permission::create(['name' => 'delete_books', 'description' => 'Can delete books']);
        Permission::create(['name' => 'download_books', 'description' => 'Can download book files']);

        // Author permissions
        Permission::create(['name' => 'view_authors', 'description' => 'Can view authors']);
        Permission::create(['name' => 'create_authors', 'description' => 'Can create authors']);
        Permission::create(['name' => 'edit_authors', 'description' => 'Can edit authors']);
        Permission::create(['name' => 'delete_authors', 'description' => 'Can delete authors']);

        // Category permissions
        Permission::create(['name' => 'view_categories', 'description' => 'Can view categories']);
        Permission::create(['name' => 'create_categories', 'description' => 'Can create categories']);
        Permission::create(['name' => 'edit_categories', 'description' => 'Can edit categories']);
        Permission::create(['name' => 'delete_categories', 'description' => 'Can delete categories']);
    }
} 