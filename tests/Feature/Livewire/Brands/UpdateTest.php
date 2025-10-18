<?php
use App\Livewire\Brands\Update;
use App\Models\Brand;
use App\Models\User;
use Livewire\Livewire;
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->brand = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca Original',
        'description' => 'Descrição original'
    ]);
});
it('can render the brands update component', function () {
    Livewire::test(Update::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.brands.update');
});
it('can open edit modal with brand data', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->assertSet('showModal', true)
        ->assertSet('name', 'Marca Original')
        ->assertSet('description', 'Descrição original')
        ->assertSet('brand.id', $this->brand->id);
});
it('can update brand with valid data', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', 'Marca Atualizada')
        ->set('description', 'Nova descrição')
        ->call('update')
        ->assertHasNoErrors();
    $this->brand->refresh();
    expect($this->brand->name)->toBe('Marca Atualizada');
    expect($this->brand->description)->toBe('Nova descrição');
});
it('can update brand name only', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', 'Novo Nome')
        ->call('update')
        ->assertHasNoErrors();
    $this->brand->refresh();
    expect($this->brand->name)->toBe('Novo Nome');
    expect($this->brand->description)->toBe('Descrição original');
});
it('can clear brand description', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('description', '')
        ->call('update')
        ->assertHasNoErrors();
    $this->brand->refresh();
    expect($this->brand->description)->toBe('');
});
it('validates required name field on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', '')
        ->call('update')
        ->assertHasErrors(['name' => 'required']);
    $this->brand->refresh();
    expect($this->brand->name)->toBe('Marca Original');
});
it('validates name field maximum length on update', function () {
    $longName = str_repeat('a', 256);
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', $longName)
        ->call('update')
        ->assertHasErrors(['name' => 'max']);
    $this->brand->refresh();
    expect($this->brand->name)->toBe('Marca Original');
});
it('validates description field maximum length on update', function () {
    $longDescription = str_repeat('a', 256);
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('description', $longDescription)
        ->call('update')
        ->assertHasErrors(['description' => 'max']);
    $this->brand->refresh();
    expect($this->brand->description)->toBe('Descrição original');
});
it('prevents updating brand that does not exist', function () {
    Livewire::test(Update::class)
        ->call('edit', 99999); 
});
it('prevents updating brand from another user', function () {
    $otherUser = User::factory()->create();
    $otherBrand = Brand::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Marca de Outro Usuário'
    ]);
    Livewire::test(Update::class)
        ->call('edit', $otherBrand->id)
        ->set('name', 'Tentativa de Hack')
        ->call('update');
    $otherBrand->refresh();
    expect($otherBrand->name)->toBe('Marca de Outro Usuário');
});
it('closes modal after successful update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', 'Nome Atualizado')
        ->call('update')
        ->assertSet('showModal', false);
});
it('resets form data after closing modal', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->call('closeModal');
    expect($component->get('name'))->toBeNull();
    expect($component->get('description'))->toBeNull();
    expect($component->get('brand'))->toBeNull();
    expect($component->get('showModal'))->toBeFalse();
});
it('dispatches brands updated event after successful update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', 'Nome Atualizado')
        ->call('update')
        ->assertDispatched('brands::updated');
});
it('shows success notification after updating brand', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', 'Nome Atualizado')
        ->call('update');
    $this->brand->refresh();
    expect($this->brand->name)->toBe('Nome Atualizado');
});
it('handles database errors gracefully during update', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', $this->brand->id);
    expect($component->get('brand'))->not->toBeNull();
});
it('preserves whitespace from name and description on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', '  Nome com Espaços  ')
        ->set('description', '  Descrição com espaços  ')
        ->call('update')
        ->assertHasNoErrors();
    $this->brand->refresh();
    expect($this->brand->name)->toBe('  Nome com Espaços  ');
    expect($this->brand->description)->toBe('  Descrição com espaços  ');
});
it('can handle special characters in update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->set('name', 'Apple & Co.')
        ->set('description', 'Acentuação: ção, ã, é, ü')
        ->call('update')
        ->assertHasNoErrors();
    $this->brand->refresh();
    expect($this->brand->name)->toBe('Apple & Co.');
    expect($this->brand->description)->toBe('Acentuação: ção, ã, é, ü');
});
it('does not update if no changes are made', function () {
    $originalUpdatedAt = $this->brand->updated_at;
    sleep(1);
    Livewire::test(Update::class)
        ->call('edit', $this->brand->id)
        ->call('update'); 
    $this->brand->refresh();
    expect($this->brand->name)->toBe('Marca Original');
    expect($this->brand->description)->toBe('Descrição original');
});
it('can edit brand without description', function () {
    $brandWithoutDescription = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Sem Descrição',
        'description' => null
    ]);
    Livewire::test(Update::class)
        ->call('edit', $brandWithoutDescription->id)
        ->assertSet('name', 'Sem Descrição')
        ->assertSet('description', null)
        ->set('name', 'Com Nome Novo')
        ->call('update')
        ->assertHasNoErrors();
    $brandWithoutDescription->refresh();
    expect($brandWithoutDescription->name)->toBe('Com Nome Novo');
    expect($brandWithoutDescription->description)->toBeNull();
});
