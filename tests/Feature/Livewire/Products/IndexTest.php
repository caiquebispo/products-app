<?php

use App\Livewire\Products\Index;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->category = Category::factory()->create(['user_id' => $this->user->id]);
    $this->brand = Brand::factory()->create(['user_id' => $this->user->id]);
});
it('can render the products index component', function () {
    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.products.index');
});
it('can search products by name', function () {
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'iPhone 13',
        'description' => 'Smartphone Apple'
    ]);
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'Galaxy S21',
        'description' => 'Smartphone Samsung'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'iPhone')
        ->assertSee('iPhone 13')
        ->assertDontSee('Galaxy S21');
});
it('can search products by description', function () {
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto A',
        'description' => 'Smartphone Apple'
    ]);
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto B',
        'description' => 'Notebook Dell'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'Apple')
        ->assertSee('Produto A')
        ->assertDontSee('Produto B');
});
it('can change items per page', function () {
    Product::factory()->count(15)->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);
    Livewire::test(Index::class)
        ->set('perPage', 5)
        ->assertSet('perPage', 5)
        ->set('perPage', 25)
        ->assertSet('perPage', 25);
});
it('displays category and brand information', function () {
    $category = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Eletrônicos'
    ]);
    $brand = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Apple'
    ]);
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'name' => 'iPhone'
    ]);
    Livewire::test(Index::class)
        ->assertSee('Eletrônicos')
        ->assertSee('Apple');
});
it('can clear search', function () {
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'iPhone'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'busca')
        ->assertSet('search', 'busca')
        ->call('$set', 'search', '')
        ->assertSet('search', '');
});
it('orders products by creation date descending', function () {
    $oldProduct = Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto Antigo',
        'created_at' => now()->subDays(2)
    ]);
    $newProduct = Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto Novo',
        'created_at' => now()
    ]);
    Livewire::test(Index::class)
        ->assertSeeInOrder(['Produto Novo', 'Produto Antigo']);
});
it('displays price and stock information', function () {
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'iPhone',
        'price' => 1999.99,
        'stock' => 10
    ]);
    $component = Livewire::test(Index::class);
    $component->assertSee('1.999,99');
    $component->assertSee('10');
});
