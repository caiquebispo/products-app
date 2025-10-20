<?php

use App\Livewire\Categories\Delete;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->category = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria para Deletar',
        'description' => 'Descrição da categoria'
    ]);
});
it('can render the categories delete component', function () {
    Livewire::test(Delete::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.categories.delete');
});
it('can open delete confirmation modal', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->assertSet('showModal', true)
        ->assertSet('category.id', $this->category->id);
});
it('can delete category without products', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete')
        ->assertHasNoErrors();
    expect(Category::find($this->category->id))->toBeNull();
});
it('prevents deletion of category with products', function () {
    $product = Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete');
    expect(Category::find($this->category->id))->not->toBeNull();
    expect(Product::find($product->id))->not->toBeNull();
});
it('prevents deleting category that does not exist', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', 99999);
});
it('closes modal after successful deletion', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete')
        ->assertSet('showModal', false);
});
it('dispatches categories deleted event after successful deletion', function () {
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete')
        ->assertDispatched('categories::deleted');
});
it('shows success notification after deleting category', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete');
    expect(Category::find($this->category->id))->toBeNull();
});
it('shows error notification when trying to delete category with products', function () {
    Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id
    ]);
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete');
    expect(Category::find($this->category->id))->not->toBeNull();
});
it('handles database errors gracefully during deletion', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id);
    expect($component->get('category'))->not->toBeNull();
});
it('displays category information in confirmation modal', function () {
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id);
    expect($component->get('category.name'))->toBe('Categoria para Deletar');
    expect($component->get('category.description'))->toBe('Descrição da categoria');
});
it('counts products correctly for category', function () {
    Product::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id
    ]);
    $component = Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id);
    expect($component->get('category')->products()->count())->toBe(3);
});
it('can delete category after removing all products', function () {
    $product = Product::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete');
    expect(Category::find($this->category->id))->not->toBeNull();
    $product->delete();
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete');
    expect(Category::find($this->category->id))->toBeNull();
});
it('maintains referential integrity when deleting', function () {
    $categoryId = $this->category->id;
    Livewire::test(Delete::class)
        ->call('confirmDelete', $categoryId)
        ->call('delete');
    expect(Category::find($categoryId))->toBeNull();
    expect(Category::where('id', $categoryId)->exists())->toBeFalse();
});
it('handles concurrent deletion attempts gracefully', function () {
    $categoryId = $this->category->id;
    $this->category->delete();
    Livewire::test(Delete::class)
        ->call('confirmDelete', $categoryId)
        ->call('delete');
    expect(Category::find($categoryId))->toBeNull();
});
it('preserves other user categories when deleting', function () {
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Categoria de Outro Usuário'
    ]);
    Livewire::test(Delete::class)
        ->call('confirmDelete', $this->category->id)
        ->call('delete');
    expect(Category::find($this->category->id))->toBeNull();
    expect(Category::find($otherCategory->id))->not->toBeNull();
});
