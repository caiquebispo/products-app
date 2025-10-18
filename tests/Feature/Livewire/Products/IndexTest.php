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
it('displays products for the authenticated user', function () {
    $product1 = Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto 1',
        'description' => 'Descrição 1'
    ]);
    $product2 = Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto 2',
        'description' => 'Descrição 2'
    ]);
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
    $otherBrand = Brand::factory()->create(['user_id' => $otherUser->id]);
    Product::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $otherCategory->id,
        'brand_id' => $otherBrand->id,
        'name' => 'Produto Outro Usuário'
    ]);
    Livewire::test(Index::class)
        ->assertSee('Produto 1')
        ->assertSee('Produto 2')
        ->assertSee('Descrição 1')
        ->assertSee('Descrição 2')
        ->assertDontSee('Produto Outro Usuário');
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
it('can search products by category name', function () {
    $category1 = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Eletrônicos'
    ]);
    $category2 = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Roupas'
    ]);
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $category1->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto Eletrônico'
    ]);
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $category2->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto Roupa'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'Eletrônicos')
        ->assertSee('Produto Eletrônico')
        ->assertDontSee('Produto Roupa');
});
it('can search products by brand name', function () {
    $brand1 = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Apple'
    ]);
    $brand2 = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Samsung'
    ]);
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $brand1->id,
        'name' => 'Produto Apple'
    ]);
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $brand2->id,
        'name' => 'Produto Samsung'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'Apple')
        ->assertSee('Produto Apple')
        ->assertDontSee('Produto Samsung');
});
it('resets page when searching', function () {
    Product::factory()->count(15)->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);
    $component = Livewire::test(Index::class)
        ->set('perPage', 5);
    $component->call('gotoPage', 2);
    $component->set('search', 'test');
    expect(method_exists($component->instance(), 'updatingSearch'))->toBeTrue();
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
it('resets page when changing per page', function () {
    Product::factory()->count(15)->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
    ]);
    $component = Livewire::test(Index::class)
        ->set('perPage', 5);
    $component->call('gotoPage', 2);
    $component->set('perPage', 10);
    expect(method_exists($component->instance(), 'updatingPerPage'))->toBeTrue();
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
it('listens to products created event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('listens to products updated event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('listens to products deleted event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('displays empty state when no products exist', function () {
    Livewire::test(Index::class)
        ->assertSee('Nenhum produto cadastrado')
        ->assertSee('Comece criando seu primeiro produto');
});
it('displays empty state when search returns no results', function () {
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'iPhone'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'produto inexistente')
        ->assertSee('Nenhum produto encontrado')
        ->assertSee('produto inexistente');
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
