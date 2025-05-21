<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fiction',
                'description' => 'Works of fiction including novels, short stories, and novellas',
                'is_active' => true,
            ],
            [
                'name' => 'Non-Fiction',
                'description' => 'Factual works including biographies, history, and science',
                'is_active' => true,
            ],
            [
                'name' => 'Science Fiction',
                'description' => 'Fiction dealing with futuristic concepts and technology',
                'is_active' => true,
            ],
            [
                'name' => 'Fantasy',
                'description' => 'Fiction involving magical and supernatural elements',
                'is_active' => true,
            ],
            [
                'name' => 'Mystery',
                'description' => 'Fiction involving crime and detective work',
                'is_active' => true,
            ],
            [
                'name' => 'Romance',
                'description' => 'Fiction focusing on romantic relationships',
                'is_active' => true,
            ],
            [
                'name' => 'Biography',
                'description' => 'Non-fiction works about people\'s lives',
                'is_active' => true,
            ],
            [
                'name' => 'History',
                'description' => 'Non-fiction works about past events',
                'is_active' => true,
            ],
            [
                'name' => 'Science',
                'description' => 'Non-fiction works about scientific topics',
                'is_active' => true,
            ],
            [
                'name' => 'Technology',
                'description' => 'Non-fiction works about technological advancements',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 