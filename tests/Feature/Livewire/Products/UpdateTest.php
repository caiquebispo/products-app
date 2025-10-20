<?php

use App\Livewire\Products\Update;
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
    $this->product = Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'brand_id' => $this->brand->id,
        'name' => 'Produto Original',
        'description' => 'Descrição original',
        'price' => 100.00,
        'stock' => 10
    ]);
});
it('can render the products update component', function () {
    Livewire::test(Update::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.products.update');
});
it('can open edit modal with product data', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->assertSet('showModal', true)
        ->assertSet('name', 'Produto Original')
        ->assertSet('description', 'Descrição original')
        ->assertSet('price', 100.00)
        ->assertSet('stock', 10)
        ->assertSet('category_id', $this->category->id)
        ->assertSet('brand_id', $this->brand->id)
        ->assertSet('product.id', $this->product->id);
});
it('can update product with valid data', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('name', 'Produto Atualizado')
        ->set('description', 'Nova descrição')
        ->set('price', 200.00)
        ->set('stock', 20)
        ->call('update')
        ->assertHasNoErrors();
    $this->product->refresh();
    expect($this->product->name)->toBe('Produto Atualizado');
    expect($this->product->description)->toBe('Nova descrição');
    expect($this->product->price)->toBe(200);
    expect($this->product->stock)->toBe(20);
});
it('can update product name only', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('name', 'Novo Nome')
        ->call('update')
        ->assertHasNoErrors();
    $this->product->refresh();
    expect($this->product->name)->toBe('Novo Nome');
    expect($this->product->description)->toBe('Descrição original');
    expect($this->product->price)->toBe(100);
});
it('can clear product description', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('description', '')
        ->call('update')
        ->assertHasNoErrors();
    $this->product->refresh();
    expect($this->product->description)->toBe('');
});
it('can update category and brand', function () {
    $newCategory = Category::factory()->create(['user_id' => $this->user->id]);
    $newBrand = Brand::factory()->create(['user_id' => $this->user->id]);
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('category_id', $newCategory->id)
        ->set('brand_id', $newBrand->id)
        ->call('update')
        ->assertHasNoErrors();
    $this->product->refresh();
    expect($this->product->category_id)->toBe($newCategory->id);
    expect($this->product->brand_id)->toBe($newBrand->id);
});
it('validates required name field on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('name', '')
        ->call('update')
        ->assertHasErrors(['name' => 'required']);
    $this->product->refresh();
    expect($this->product->name)->toBe('Produto Original');
});
it('validates required price field on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('price', null)
        ->call('update')
        ->assertHasErrors(['price' => 'required']);
    $this->product->refresh();
    expect($this->product->price)->toBe(100);
});
it('validates required stock field on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('stock', null)
        ->call('update')
        ->assertHasErrors(['stock' => 'required']);
    $this->product->refresh();
    expect($this->product->stock)->toBe(10);
});
it('validates name field maximum length on update', function () {
    $longName = str_repeat('a', 256);
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('name', $longName)
        ->call('update')
        ->assertHasErrors(['name' => 'max']);
    $this->product->refresh();
    expect($this->product->name)->toBe('Produto Original');
});
it('validates price is positive on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('price', -10.00)
        ->call('update')
        ->assertHasErrors(['price' => 'min']);
    $this->product->refresh();
    expect($this->product->price)->toBe(100);
});
it('validates stock is positive on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('stock', -1)
        ->call('update')
        ->assertHasErrors(['stock' => 'min']);
    $this->product->refresh();
    expect($this->product->stock)->toBe(10);
});
it('prevents updating product that does not exist', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', 99999);
    expect($component->get('showModal'))->toBeFalse();
    expect($component->get('product'))->toBeNull();
});
it('closes modal after successful update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('name', 'Nome Atualizado')
        ->call('update')
        ->assertSet('showModal', false);
});
it('dispatches products updated event after successful update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('name', 'Nome Atualizado')
        ->call('update')
        ->assertDispatched('products::updated');
});
it('shows success notification after updating product', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('name', 'Nome Atualizado')
        ->call('update');
    $this->product->refresh();
    expect($this->product->name)->toBe('Nome Atualizado');
});
it('loads categories and brands for the authenticated user', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', $this->product->id);
    $component->assertViewHas('categories');
    $component->assertViewHas('brands');
    expect($this->user->categories()->count())->toBe(1);
    expect($this->user->brands()->count())->toBe(1);
});
it('can handle special characters in update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('name', 'iPhone & Acessórios')
        ->set('description', 'Acentuação: ção, ã, é, ü')
        ->call('update')
        ->assertHasNoErrors();
    $this->product->refresh();
    expect($this->product->name)->toBe('iPhone & Acessórios');
    expect($this->product->description)->toBe('Acentuação: ção, ã, é, ü');
});
it('can update image field', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->product->id)
        ->set('image', 'https://example.com/new-image.jpg')
        ->call('update')
        ->assertHasNoErrors();
    $this->product->refresh();
    expect($this->product->image)->toBe('https://example.com/new-image.jpg');
});
