<?php
use App\Livewire\Products\Delete;
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
        'name' => 'Produto para Deletar',
        'description' => 'Descrição do produto'
    ]);
});
it('can render the products delete component', function () {
    Livewire::test(Delete::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.products.delete');
});
it('can open delete confirmation modal with product data', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->assertSet('showModal', true)
        ->assertSet('product.id', $this->product->id)
        ->assertSet('product.name', 'Produto para Deletar');
});
it('displays product details in confirmation modal', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id);
    $product = $component->get('product');
    expect($product->id)->toBe($this->product->id);
    expect($product->name)->toBe('Produto para Deletar');
    expect($product->description)->toBe('Descrição do produto');
    expect($product->category)->not->toBeNull();
    expect($product->brand)->not->toBeNull();
});
it('can successfully delete a product', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->call('delete')
        ->assertHasNoErrors();
    expect(Product::find($this->product->id))->toBeNull();
});
it('closes modal after successful deletion', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->call('delete')
        ->assertSet('showModal', false)
        ->assertSet('product', null);
});
it('dispatches products deleted event after successful deletion', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->call('delete')
        ->assertDispatched('products::deleted');
});
it('shows success notification after deleting product', function () {
    $productName = $this->product->name;
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->call('delete');
    expect(Product::find($this->product->id))->toBeNull();
});
it('prevents deleting product that does not exist', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', 99999) 
        ->assertSet('showModal', false)
        ->assertSet('product', null);
});
it('prevents deleting product from another user', function () {
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
    $otherBrand = Brand::factory()->create(['user_id' => $otherUser->id]);
    $otherProduct = Product::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $otherCategory->id,
        'brand_id' => $otherBrand->id,
        'name' => 'Produto de Outro Usuário'
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $otherProduct->id)
        ->call('delete');
    expect(Product::find($otherProduct->id))->not->toBeNull();
    expect(Product::find($otherProduct->id)->name)->toBe('Produto de Outro Usuário');
});
it('can close modal without deleting', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->assertSet('showModal', true)
        ->call('closeModal')
        ->assertSet('showModal', false)
        ->assertSet('product', null);
    expect(Product::find($this->product->id))->not->toBeNull();
});
it('handles deletion of product with relationships', function () {
    expect($this->product->category_id)->toBe($this->category->id);
    expect($this->product->brand_id)->toBe($this->brand->id);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->call('delete')
        ->assertHasNoErrors();
    expect(Product::find($this->product->id))->toBeNull();
    expect(Category::find($this->category->id))->not->toBeNull();
    expect(Brand::find($this->brand->id))->not->toBeNull();
});
it('preserves other user products when deleting', function () {
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
    $otherBrand = Brand::factory()->create(['user_id' => $otherUser->id]);
    $otherProduct = Product::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $otherCategory->id,
        'brand_id' => $otherBrand->id,
        'name' => 'Produto Preservado'
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->call('delete');
    expect(Product::find($this->product->id))->toBeNull();
    expect(Product::find($otherProduct->id))->not->toBeNull();
});
it('handles concurrent deletion attempts', function () {
    $productId = $this->product->id;
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $productId);
    expect($component->get('product'))->not->toBeNull();
    $component->call('delete');
    expect(Product::find($productId))->toBeNull();
    expect($component->get('showModal'))->toBeFalse();
});
it('loads product with category and brand relationships', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id);
    $product = $component->get('product');
    expect($product->category)->not->toBeNull();
    expect($product->brand)->not->toBeNull();
    expect($product->category->name)->toBe($this->category->name);
    expect($product->brand->name)->toBe($this->brand->name);
});
it('resets component state after closing modal', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id)
        ->assertSet('showModal', true)
        ->call('closeModal');
    expect($component->get('showModal'))->toBeFalse();
    expect($component->get('product'))->toBeNull();
});
it('handles error during deletion gracefully', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->product->id);
    expect($component->get('product'))->not->toBeNull();
});
it('validates user ownership before showing delete modal', function () {
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
    $otherBrand = Brand::factory()->create(['user_id' => $otherUser->id]);
    $otherProduct = Product::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $otherCategory->id,
        'brand_id' => $otherBrand->id,
        'name' => 'Produto Restrito'
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $otherProduct->id);
});
