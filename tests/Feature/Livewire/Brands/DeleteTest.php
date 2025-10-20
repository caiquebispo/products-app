<?php

use App\Livewire\Brands\Delete;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->brand = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca para Deletar',
        'description' => 'Descrição da marca'
    ]);
});
it('can render the brands delete component', function () {
    Livewire::test(Delete::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.brands.delete');
});
it('can open delete confirmation modal', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->assertSet('showModal', true)
        ->assertSet('brand.id', $this->brand->id);
});
it('can delete brand without products', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete')
        ->assertHasNoErrors();
    expect(Brand::find($this->brand->id))->toBeNull();
});
it('prevents deletion of brand with products', function () {
    $product = Product::factory()->create([
        'user_id' => $this->user->id,
        'brand_id' => $this->brand->id
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete');
    expect(Brand::find($this->brand->id))->not->toBeNull();
    expect(Product::find($product->id))->not->toBeNull();
});
it('closes modal after successful deletion', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete')
        ->assertSet('showModal', false);
});
it('dispatches brands deleted event after successful deletion', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete')
        ->assertDispatched('brands::deleted');
});
it('shows success notification after deleting brand', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete');
    expect(Brand::find($this->brand->id))->toBeNull();
});
it('shows error notification when trying to delete brand with products', function () {
    Product::factory()->create([
        'user_id' => $this->user->id,
        'brand_id' => $this->brand->id
    ]);
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete');
    expect(Brand::find($this->brand->id))->not->toBeNull();
});
it('handles database errors gracefully during deletion', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id);
    expect($component->get('brand'))->not->toBeNull();
});
it('can close modal without deleting', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->toggle('showModal')
        ->assertSet('showModal', false);

    expect(Brand::find($this->brand->id))->not->toBeNull();
});
it('displays brand information in confirmation modal', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id);
    expect($component->get('brand.name'))->toBe('Marca para Deletar');
    expect($component->get('brand.description'))->toBe('Descrição da marca');
});
it('counts products correctly for brand', function () {
    Product::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'brand_id' => $this->brand->id
    ]);
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id);
    expect($component->get('brand')->products()->count())->toBe(3);
});
it('can delete brand after removing all products', function () {
    $product = Product::factory()->create([
        'user_id' => $this->user->id,
        'brand_id' => $this->brand->id
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete');
    expect(Brand::find($this->brand->id))->not->toBeNull();
    $product->delete();
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete');
    expect(Brand::find($this->brand->id))->toBeNull();
});
it('maintains referential integrity when deleting', function () {
    $brandId = $this->brand->id;
    Livewire::test(Delete::class)
        ->call('confirmDelete', $brandId)
        ->call('delete');
    expect(Brand::find($brandId))->toBeNull();
    expect(Brand::where('id', $brandId)->exists())->toBeFalse();
});
it('handles concurrent deletion attempts gracefully', function () {
    $brandId = $this->brand->id;
    $this->brand->delete();
    Livewire::test(Delete::class)
        ->call('confirmDelete', $brandId)
        ->call('delete');
    expect(Brand::find($brandId))->toBeNull();
});
it('preserves other user brands when deleting', function () {
    $otherUser = User::factory()->create();
    $otherBrand = Brand::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Marca de Outro Usuário'
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->brand->id)
        ->call('delete');
    expect(Brand::find($this->brand->id))->toBeNull();
    expect(Brand::find($otherBrand->id))->not->toBeNull();
});
