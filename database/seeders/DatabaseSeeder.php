<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed the roles and permissions
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CategorySeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign Administrator role to admin user
        $admin->roles()->attach(
            \App\Models\Role::where('name', 'Administrator')->first()->uuid
        );

        // Create librarian user
        $librarian = User::create([
            'name' => 'Librarian User',
            'email' => 'librarian@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign Librarian role
        $librarian->roles()->attach(
            \App\Models\Role::where('name', 'Librarian')->first()->uuid
        );

        // Create reader user
        $reader = User::create([
            'name' => 'Reader User',
            'email' => 'reader@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign Reader role
        $reader->roles()->attach(
            \App\Models\Role::where('name', 'Reader')->first()->uuid
        );
    }
}
