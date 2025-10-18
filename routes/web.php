<?php

use App\Livewire\Categories\Index as CategoriesIndex;
use App\Livewire\Brands\Index as BrandsIndex;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {

    Route::get('dashboard', Dashboard::class)
        ->middleware(['verified'])
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');

    Route::get('categories', CategoriesIndex::class)->name('categories.index');
    Route::get('brands', BrandsIndex::class)->name('brands.index');
    Route::get('products', ProductsIndex::class)->name('products.index');
});

require __DIR__ . '/auth.php';
