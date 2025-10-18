<?php
use App\Livewire\Products\Store;
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
it('can render the products store component', function () {
    Livewire::test(Store::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.products.store');
});
it('can create a product with valid data', function () {
    Livewire::test(Store::class)
        ->set('name', 'iPhone 13')
        ->set('description', 'Smartphone Apple')
        ->set('price', 1999.99)
        ->set('stock', 10)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->set('image', 'https://example.com/iphone.jpg')
        ->call('store')
        ->assertHasNoErrors();
    expect(Product::where('name', 'iPhone 13')->exists())->toBeTrue();
    $product = Product::where('name', 'iPhone 13')->first();
    expect($product->description)->toBe('Smartphone Apple');
    expect($product->price)->toBe(1999.99);
    expect($product->stock)->toBe(10);
    expect($product->category_id)->toBe($this->category->id);
    expect($product->brand_id)->toBe($this->brand->id);
    expect($product->user_id)->toBe($this->user->id);
});
it('can create a product without description and image', function () {
    Livewire::test(Store::class)
        ->set('name', 'Galaxy S21')
        ->set('price', 1500.00)
        ->set('stock', 5)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasNoErrors();
    expect(Product::where('name', 'Galaxy S21')->exists())->toBeTrue();
    $product = Product::where('name', 'Galaxy S21')->first();
    expect($product->description)->toBeNull();
    expect($product->image)->toBeNull();
    expect($product->user_id)->toBe($this->user->id);
});
it('validates required name field', function () {
    Livewire::test(Store::class)
        ->set('name', '')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['name' => 'required']);
    expect(Product::count())->toBe(0);
});
it('validates required price field', function () {
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['price' => 'required']);
    expect(Product::count())->toBe(0);
});
it('validates required stock field', function () {
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', 100.00)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['stock' => 'required']);
    expect(Product::count())->toBe(0);
});
it('validates required category field', function () {
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['category_id' => 'required']);
    expect(Product::count())->toBe(0);
});
it('validates required brand field', function () {
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->call('store')
        ->assertHasErrors(['brand_id' => 'required']);
    expect(Product::count())->toBe(0);
});
it('validates name field maximum length', function () {
    $longName = str_repeat('a', 256); 
    Livewire::test(Store::class)
        ->set('name', $longName)
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['name' => 'max']);
    expect(Product::count())->toBe(0);
});
it('validates price is numeric and positive', function () {
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', -10.00)
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['price' => 'min']);
    expect(Product::count())->toBe(0);
});
it('validates stock is integer and positive', function () {
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', 100.00)
        ->set('stock', -1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['stock' => 'min']);
    expect(Product::count())->toBe(0);
});
it('validates category exists', function () {
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', 99999) 
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['category_id' => 'exists']);
    expect(Product::count())->toBe(0);
});
it('validates brand exists', function () {
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', 99999) 
        ->call('store')
        ->assertHasErrors(['brand_id' => 'exists']);
    expect(Product::count())->toBe(0);
});
it('prevents using category from another user', function () {
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', $otherCategory->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasErrors(['category_id']);
    expect(Product::count())->toBe(0);
});
it('prevents using brand from another user', function () {
    $otherUser = User::factory()->create();
    $otherBrand = Brand::factory()->create(['user_id' => $otherUser->id]);
    Livewire::test(Store::class)
        ->set('name', 'Produto Teste')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $otherBrand->id)
        ->call('store')
        ->assertHasErrors(['brand_id']);
    expect(Product::count())->toBe(0);
});
it('resets form after successful creation', function () {
    $component = Livewire::test(Store::class)
        ->set('name', 'iPhone 13')
        ->set('description', 'Smartphone Apple')
        ->set('price', 1999.99)
        ->set('stock', 10)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store');
    expect($component->get('name'))->toBeNull();
    expect($component->get('description'))->toBeNull();
    expect($component->get('price'))->toBeNull();
    expect($component->get('stock'))->toBeNull();
    expect($component->get('category_id'))->toBeNull();
    expect($component->get('brand_id'))->toBeNull();
});
it('dispatches products created event after successful creation', function () {
    Livewire::test(Store::class)
        ->set('name', 'iPhone 13')
        ->set('price', 1999.99)
        ->set('stock', 10)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertDispatched('products::created');
});
it('shows success notification after creating product', function () {
    $component = Livewire::test(Store::class)
        ->set('name', 'iPhone 13')
        ->set('price', 1999.99)
        ->set('stock', 10)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store');
    expect(Product::where('name', 'iPhone 13')->exists())->toBeTrue();
});
it('associates product with authenticated user', function () {
    $anotherUser = User::factory()->create();
    Livewire::test(Store::class)
        ->set('name', 'Meu Produto')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store');
    $product = Product::where('name', 'Meu Produto')->first();
    expect($product->user_id)->toBe($this->user->id);
    expect($product->user_id)->not->toBe($anotherUser->id);
});
it('loads categories and brands for the authenticated user', function () {
    $component = Livewire::test(Store::class);
    $component->assertViewHas('categories');
    $component->assertViewHas('brands');
    expect($this->user->categories()->count())->toBe(1);
    expect($this->user->brands()->count())->toBe(1);
});
it('can handle special characters in name and description', function () {
    Livewire::test(Store::class)
        ->set('name', 'iPhone & Acessórios')
        ->set('description', 'Produto com acentuação: ção, ã, é')
        ->set('price', 100.00)
        ->set('stock', 1)
        ->set('category_id', $this->category->id)
        ->set('brand_id', $this->brand->id)
        ->call('store')
        ->assertHasNoErrors();
    $product = Product::where('name', 'iPhone & Acessórios')->first();
    expect($product)->not->toBeNull();
    expect($product->description)->toBe('Produto com acentuação: ção, ã, é');
});
