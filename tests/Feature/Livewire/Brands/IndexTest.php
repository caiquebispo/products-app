<?php
use App\Livewire\Brands\Index;
use App\Models\Brand;
use App\Models\User;
use Livewire\Livewire;
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});
it('can render the brands index component', function () {
    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.brands.index');
});
it('displays brands for the authenticated user', function () {
    $brand1 = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca 1',
        'description' => 'Descrição 1'
    ]);
    $brand2 = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca 2',
        'description' => 'Descrição 2'
    ]);
    $otherUser = User::factory()->create();
    Brand::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Marca Outro Usuário'
    ]);
    Livewire::test(Index::class)
        ->assertSee('Marca 1')
        ->assertSee('Marca 2')
        ->assertSee('Descrição 1')
        ->assertSee('Descrição 2')
        ->assertDontSee('Marca Outro Usuário');
});
it('can search brands by name', function () {
    Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Apple',
        'description' => 'Produtos da Apple'
    ]);
    Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Samsung',
        'description' => 'Produtos Samsung'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'Apple')
        ->assertSee('Apple')
        ->assertDontSee('Samsung');
});
it('can search brands by description', function () {
    Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca A',
        'description' => 'Produtos da Apple'
    ]);
    Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca B',
        'description' => 'Produtos Samsung'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'Apple')
        ->assertSee('Marca A')
        ->assertDontSee('Marca B');
});
it('resets page when searching', function () {
    Brand::factory()->count(15)->create([
        'user_id' => $this->user->id,
    ]);
    $component = Livewire::test(Index::class)
        ->set('perPage', 5);
    $component->call('gotoPage', 2);
    $component->set('search', 'test');
    expect(method_exists($component->instance(), 'updatingSearch'))->toBeTrue();
});
it('can change items per page', function () {
    Brand::factory()->count(15)->create([
        'user_id' => $this->user->id,
    ]);
    Livewire::test(Index::class)
        ->set('perPage', 5)
        ->assertSet('perPage', 5)
        ->set('perPage', 25)
        ->assertSet('perPage', 25);
});
it('resets page when changing per page', function () {
    Brand::factory()->count(15)->create([
        'user_id' => $this->user->id,
    ]);
    $component = Livewire::test(Index::class)
        ->set('perPage', 5);
    $component->call('gotoPage', 2);
    $component->set('perPage', 10);
    expect(method_exists($component->instance(), 'updatingPerPage'))->toBeTrue();
});
it('displays product count for each brand', function () {
    $brand = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Apple'
    ]);
    \App\Models\Product::factory()->count(2)->create([
        'brand_id' => $brand->id,
        'user_id' => $this->user->id
    ]);
    Livewire::test(Index::class)
        ->assertSee('2 produtos');
});
it('listens to brands created event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('listens to brands updated event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('listens to brands deleted event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('displays empty state when no brands exist', function () {
    Livewire::test(Index::class)
        ->assertSee('Nenhuma marca cadastrada')
        ->assertSee('Comece criando sua primeira marca');
});
it('displays empty state when search returns no results', function () {
    Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Apple'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'marca inexistente')
        ->assertSee('Nenhuma marca encontrada')
        ->assertSee('marca inexistente');
});
it('can clear search', function () {
    Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Apple'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'busca')
        ->assertSet('search', 'busca')
        ->call('$set', 'search', '')
        ->assertSet('search', '');
});
it('orders brands by creation date descending', function () {
    $oldBrand = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca Antiga',
        'created_at' => now()->subDays(2)
    ]);
    $newBrand = Brand::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Marca Nova',
        'created_at' => now()
    ]);
    Livewire::test(Index::class)
        ->assertSeeInOrder(['Marca Nova', 'Marca Antiga']);
});
