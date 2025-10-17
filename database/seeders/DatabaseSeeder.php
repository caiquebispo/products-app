<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $categories = Category::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $brands = Brand::factory()->count(8)->create([
            'user_id' => $user->id,
        ]);

        Product::factory()->count(30)->create([
            'user_id' => $user->id,
            'category_id' => fn() => $categories->random()->id,
            'brand_id' => fn() => $brands->random()->id,
        ]);
    }
}
