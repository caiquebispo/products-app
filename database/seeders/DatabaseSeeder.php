<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()
            ->has(Category::factory()->count(5))
            ->has(Brand::factory()->count(5))
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password')
            ]);


        $categories = $user->categories;
        $brands = $user->brands;

        Product::factory()->count(30)->create([
            'user_id' => $user->id,
            'category_id' => fn() => $categories->random()->id,
            'brand_id' => fn() => $brands->random()->id,
        ]);
    }
}
