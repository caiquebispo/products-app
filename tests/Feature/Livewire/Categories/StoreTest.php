<?php
use App\Livewire\Categories\Store;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});
it('can render the categories store component', function () {
    Livewire::test(Store::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.categories.store');
});
it('can create a category with valid data', function () {
    Livewire::test(Store::class)
        ->set('name', 'Eletrônicos')
        ->set('description', 'Produtos eletrônicos diversos')
        ->call('store')
        ->assertHasNoErrors();
    expect(Category::where('name', 'Eletrônicos')->exists())->toBeTrue();
    $category = Category::where('name', 'Eletrônicos')->first();
    expect($category->description)->toBe('Produtos eletrônicos diversos');
    expect($category->user_id)->toBe($this->user->id);
});
it('can create a category without description', function () {
    Livewire::test(Store::class)
        ->set('name', 'Roupas')
        ->call('store')
        ->assertHasNoErrors();
    expect(Category::where('name', 'Roupas')->exists())->toBeTrue();
    $category = Category::where('name', 'Roupas')->first();
    expect($category->description)->toBeNull();
    expect($category->user_id)->toBe($this->user->id);
});
it('validates required name field', function () {
    Livewire::test(Store::class)
        ->set('name', '')
        ->set('description', 'Descrição válida')
        ->call('store')
        ->assertHasErrors(['name' => 'required']);
    expect(Category::count())->toBe(0);
});
it('validates name field maximum length', function () {
    $longName = str_repeat('a', 256); 
    Livewire::test(Store::class)
        ->set('name', $longName)
        ->call('store')
        ->assertHasErrors(['name' => 'max']);
    expect(Category::count())->toBe(0);
});
it('validates description field maximum length', function () {
    $longDescription = str_repeat('a', 256); 
    Livewire::test(Store::class)
        ->set('name', 'Categoria Válida')
        ->set('description', $longDescription)
        ->call('store')
        ->assertHasErrors(['description' => 'max']);
    expect(Category::count())->toBe(0);
});
it('validates name field type safety', function () {
    Livewire::test(Store::class)
        ->set('name', 'Nome Válido')
        ->call('store')
        ->assertHasNoErrors();
    expect(Category::where('name', 'Nome Válido')->exists())->toBeTrue();
});
it('validates description field type safety', function () {
    Livewire::test(Store::class)
        ->set('name', 'Categoria Válida')
        ->set('description', 'Descrição válida')
        ->call('store')
        ->assertHasNoErrors();
    expect(Category::where('name', 'Categoria Válida')->exists())->toBeTrue();
});
it('resets form after successful creation', function () {
    $component = Livewire::test(Store::class)
        ->set('name', 'Eletrônicos')
        ->set('description', 'Produtos eletrônicos')
        ->call('store');
    expect($component->get('name'))->toBeNull();
    expect($component->get('description'))->toBeNull();
});
it('dispatches categories created event after successful creation', function () {
    Livewire::test(Store::class)
        ->set('name', 'Eletrônicos')
        ->call('store')
        ->assertDispatched('categories::created');
});
it('shows success notification after creating category', function () {
    $component = Livewire::test(Store::class)
        ->set('name', 'Eletrônicos')
        ->call('store');
    expect(Category::where('name', 'Eletrônicos')->exists())->toBeTrue();
});
it('handles database errors gracefully', function () {
    Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria Existente'
    ]);
    $component = Livewire::test(Store::class)
        ->set('name', 'Categoria Existente')
        ->call('store');
});
it('associates category with authenticated user', function () {
    $anotherUser = User::factory()->create();
    Livewire::test(Store::class)
        ->set('name', 'Minha Categoria')
        ->call('store');
    $category = Category::where('name', 'Minha Categoria')->first();
    expect($category->user_id)->toBe($this->user->id);
    expect($category->user_id)->not->toBe($anotherUser->id);
});
it('trims whitespace from name and description', function () {
    Livewire::test(Store::class)
        ->set('name', '  Eletrônicos  ')
        ->set('description', '  Produtos diversos  ')
        ->call('store')
        ->assertHasNoErrors();
    $category = Category::where('name', '  Eletrônicos  ')->first();
    expect($category)->not->toBeNull();
    expect($category->name)->toBe('  Eletrônicos  ');
    expect($category->description)->toBe('  Produtos diversos  ');
});
it('can handle special characters in name and description', function () {
    Livewire::test(Store::class)
        ->set('name', 'Eletrônicos & Informática')
        ->set('description', 'Produtos com acentuação: ção, ã, é')
        ->call('store')
        ->assertHasNoErrors();
    $category = Category::where('name', 'Eletrônicos & Informática')->first();
    expect($category)->not->toBeNull();
    expect($category->description)->toBe('Produtos com acentuação: ção, ã, é');
});
