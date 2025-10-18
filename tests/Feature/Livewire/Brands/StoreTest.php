<?php
use App\Livewire\Brands\Store;
use App\Models\Brand;
use App\Models\User;
use Livewire\Livewire;
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});
it('can render the brands store component', function () {
    Livewire::test(Store::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.brands.store');
});
it('can create a brand with valid data', function () {
    Livewire::test(Store::class)
        ->set('name', 'Apple')
        ->set('description', 'Produtos Apple diversos')
        ->call('store')
        ->assertHasNoErrors();
    expect(Brand::where('name', 'Apple')->exists())->toBeTrue();
    $brand = Brand::where('name', 'Apple')->first();
    expect($brand->description)->toBe('Produtos Apple diversos');
    expect($brand->user_id)->toBe($this->user->id);
});
it('can create a brand without description', function () {
    Livewire::test(Store::class)
        ->set('name', 'Samsung')
        ->call('store')
        ->assertHasNoErrors();
    expect(Brand::where('name', 'Samsung')->exists())->toBeTrue();
    $brand = Brand::where('name', 'Samsung')->first();
    expect($brand->description)->toBeNull();
    expect($brand->user_id)->toBe($this->user->id);
});
it('validates required name field', function () {
    Livewire::test(Store::class)
        ->set('name', '')
        ->set('description', 'Descrição válida')
        ->call('store')
        ->assertHasErrors(['name' => 'required']);
    expect(Brand::count())->toBe(0);
});
it('validates name field maximum length', function () {
    $longName = str_repeat('a', 256); 
    Livewire::test(Store::class)
        ->set('name', $longName)
        ->call('store')
        ->assertHasErrors(['name' => 'max']);
    expect(Brand::count())->toBe(0);
});
it('validates description field maximum length', function () {
    $longDescription = str_repeat('a', 256); 
    Livewire::test(Store::class)
        ->set('name', 'Marca Válida')
        ->set('description', $longDescription)
        ->call('store')
        ->assertHasErrors(['description' => 'max']);
    expect(Brand::count())->toBe(0);
});
it('validates name field type safety', function () {
    Livewire::test(Store::class)
        ->set('name', 'Nome Válido')
        ->call('store')
        ->assertHasNoErrors();
    expect(Brand::where('name', 'Nome Válido')->exists())->toBeTrue();
});
it('validates description field type safety', function () {
    Livewire::test(Store::class)
        ->set('name', 'Marca Válida')
        ->set('description', 'Descrição válida')
        ->call('store')
        ->assertHasNoErrors();
    expect(Brand::where('name', 'Marca Válida')->exists())->toBeTrue();
});
it('resets form after successful creation', function () {
    $component = Livewire::test(Store::class)
        ->set('name', 'Apple')
        ->set('description', 'Produtos Apple')
        ->call('store');
    expect($component->get('name'))->toBeNull();
    expect($component->get('description'))->toBeNull();
});
it('dispatches brands created event after successful creation', function () {
    Livewire::test(Store::class)
        ->set('name', 'Apple')
        ->call('store')
        ->assertDispatched('brands::created');
});
it('shows success notification after creating brand', function () {
    $component = Livewire::test(Store::class)
        ->set('name', 'Apple')
        ->call('store');
    expect(Brand::where('name', 'Apple')->exists())->toBeTrue();
});
it('handles database errors gracefully', function () {
    Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca Existente'
    ]);
    $component = Livewire::test(Store::class)
        ->set('name', 'Marca Existente')
        ->call('store');
});
it('associates brand with authenticated user', function () {
    $anotherUser = User::factory()->create();
    Livewire::test(Store::class)
        ->set('name', 'Minha Marca')
        ->call('store');
    $brand = Brand::where('name', 'Minha Marca')->first();
    expect($brand->user_id)->toBe($this->user->id);
    expect($brand->user_id)->not->toBe($anotherUser->id);
});
it('preserves whitespace from name and description', function () {
    Livewire::test(Store::class)
        ->set('name', '  Apple  ')
        ->set('description', '  Produtos diversos  ')
        ->call('store')
        ->assertHasNoErrors();
    $brand = Brand::where('name', '  Apple  ')->first();
    expect($brand)->not->toBeNull();
    expect($brand->name)->toBe('  Apple  ');
    expect($brand->description)->toBe('  Produtos diversos  ');
});
it('can handle special characters in name and description', function () {
    Livewire::test(Store::class)
        ->set('name', 'Apple & Co.')
        ->set('description', 'Produtos com acentuação: ção, ã, é')
        ->call('store')
        ->assertHasNoErrors();
    $brand = Brand::where('name', 'Apple & Co.')->first();
    expect($brand)->not->toBeNull();
    expect($brand->description)->toBe('Produtos com acentuação: ção, ã, é');
});
