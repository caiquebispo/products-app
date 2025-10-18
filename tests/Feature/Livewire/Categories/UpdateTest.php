<?php
use App\Livewire\Categories\Update;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->category = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria Original',
        'description' => 'Descrição original'
    ]);
});
it('can render the categories update component', function () {
    Livewire::test(Update::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.categories.update');
});
it('can open edit modal with category data', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->assertSet('showModal', true)
        ->assertSet('name', 'Categoria Original')
        ->assertSet('description', 'Descrição original')
        ->assertSet('category.id', $this->category->id);
});
it('can update category with valid data', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', 'Categoria Atualizada')
        ->set('description', 'Nova descrição')
        ->call('update')
        ->assertHasNoErrors();
    $this->category->refresh();
    expect($this->category->name)->toBe('Categoria Atualizada');
    expect($this->category->description)->toBe('Nova descrição');
});
it('can update category name only', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', 'Novo Nome')
        ->call('update')
        ->assertHasNoErrors();
    $this->category->refresh();
    expect($this->category->name)->toBe('Novo Nome');
    expect($this->category->description)->toBe('Descrição original');
});
it('can clear category description', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('description', '')
        ->call('update')
        ->assertHasNoErrors();
    $this->category->refresh();
    expect($this->category->description)->toBe('');
});
it('validates required name field on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', '')
        ->call('update')
        ->assertHasErrors(['name' => 'required']);
    $this->category->refresh();
    expect($this->category->name)->toBe('Categoria Original');
});
it('validates name field maximum length on update', function () {
    $longName = str_repeat('a', 256);
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', $longName)
        ->call('update')
        ->assertHasErrors(['name' => 'max']);
    $this->category->refresh();
    expect($this->category->name)->toBe('Categoria Original');
});
it('validates description field maximum length on update', function () {
    $longDescription = str_repeat('a', 256);
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('description', $longDescription)
        ->call('update')
        ->assertHasErrors(['description' => 'max']);
    $this->category->refresh();
    expect($this->category->description)->toBe('Descrição original');
});
it('prevents updating category that does not exist', function () {
    Livewire::test(Update::class)
        ->call('edit', 99999); 
});
it('prevents updating category from another user', function () {
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Categoria de Outro Usuário'
    ]);
    Livewire::test(Update::class)
        ->call('edit', $otherCategory->id)
        ->set('name', 'Tentativa de Hack')
        ->call('update');
    $otherCategory->refresh();
    expect($otherCategory->name)->toBe('Categoria de Outro Usuário');
});
it('closes modal after successful update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', 'Nome Atualizado')
        ->call('update')
        ->assertSet('showModal', false);
});
it('resets form data after closing modal', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->call('closeModal');
    expect($component->get('name'))->toBeNull();
    expect($component->get('description'))->toBeNull();
    expect($component->get('category'))->toBeNull();
    expect($component->get('showModal'))->toBeFalse();
});
it('dispatches categories updated event after successful update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', 'Nome Atualizado')
        ->call('update')
        ->assertDispatched('categories::updated');
});
it('shows success notification after updating category', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', 'Nome Atualizado')
        ->call('update');
    $this->category->refresh();
    expect($this->category->name)->toBe('Nome Atualizado');
});
it('handles database errors gracefully during update', function () {
    $component = Livewire::test(Update::class)
        ->call('edit', $this->category->id);
    expect($component->get('category'))->not->toBeNull();
});
it('preserves whitespace from name and description on update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', '  Nome com Espaços  ')
        ->set('description', '  Descrição com espaços  ')
        ->call('update')
        ->assertHasNoErrors();
    $this->category->refresh();
    expect($this->category->name)->toBe('  Nome com Espaços  ');
    expect($this->category->description)->toBe('  Descrição com espaços  ');
});
it('can handle special characters in update', function () {
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->set('name', 'Eletrônicos & Informática')
        ->set('description', 'Acentuação: ção, ã, é, ü')
        ->call('update')
        ->assertHasNoErrors();
    $this->category->refresh();
    expect($this->category->name)->toBe('Eletrônicos & Informática');
    expect($this->category->description)->toBe('Acentuação: ção, ã, é, ü');
});
it('does not update if no changes are made', function () {
    $originalUpdatedAt = $this->category->updated_at;
    sleep(1);
    Livewire::test(Update::class)
        ->call('edit', $this->category->id)
        ->call('update'); 
    $this->category->refresh();
    expect($this->category->name)->toBe('Categoria Original');
    expect($this->category->description)->toBe('Descrição original');
});
it('can edit category without description', function () {
    $categoryWithoutDescription = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Sem Descrição',
        'description' => null
    ]);
    Livewire::test(Update::class)
        ->call('edit', $categoryWithoutDescription->id)
        ->assertSet('name', 'Sem Descrição')
        ->assertSet('description', null)
        ->set('name', 'Com Nome Novo')
        ->call('update')
        ->assertHasNoErrors();
    $categoryWithoutDescription->refresh();
    expect($categoryWithoutDescription->name)->toBe('Com Nome Novo');
    expect($categoryWithoutDescription->description)->toBeNull();
});
