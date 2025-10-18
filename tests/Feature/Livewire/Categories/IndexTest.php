<?php
use App\Livewire\Categories\Index;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});
it('can render the categories index component', function () {
    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.categories.index');
});
it('displays categories for the authenticated user', function () {
    $category1 = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria 1',
        'description' => 'Descrição 1'
    ]);
    $category2 = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria 2',
        'description' => 'Descrição 2'
    ]);
    $otherUser = User::factory()->create();
    Category::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Categoria Outro Usuário'
    ]);
    Livewire::test(Index::class)
        ->assertSee('Categoria 1')
        ->assertSee('Categoria 2')
        ->assertSee('Descrição 1')
        ->assertSee('Descrição 2')
        ->assertDontSee('Categoria Outro Usuário');
});
it('can search categories by name', function () {
    Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Eletrônicos',
        'description' => 'Produtos eletrônicos'
    ]);
    Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Roupas',
        'description' => 'Vestuário em geral'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'Eletrônicos')
        ->assertSee('Eletrônicos')
        ->assertDontSee('Roupas');
});
it('can search categories by description', function () {
    Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria A',
        'description' => 'Produtos eletrônicos'
    ]);
    Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria B',
        'description' => 'Vestuário em geral'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'eletrônicos')
        ->assertSee('Categoria A')
        ->assertDontSee('Categoria B');
});
it('resets page when searching', function () {
    Category::factory()->count(15)->create([
        'user_id' => $this->user->id,
    ]);
    $component = Livewire::test(Index::class)
        ->set('perPage', 5);
    $component->call('gotoPage', 2);
    $component->set('search', 'test');
    expect(method_exists($component->instance(), 'updatingSearch'))->toBeTrue();
});
it('can change items per page', function () {
    Category::factory()->count(15)->create([
        'user_id' => $this->user->id,
    ]);
    Livewire::test(Index::class)
        ->set('perPage', 5)
        ->assertSet('perPage', 5)
        ->set('perPage', 25)
        ->assertSet('perPage', 25);
});
it('resets page when changing per page', function () {
    Category::factory()->count(15)->create([
        'user_id' => $this->user->id,
    ]);
    $component = Livewire::test(Index::class)
        ->set('perPage', 5);
    $component->call('gotoPage', 2);
    $component->set('perPage', 10);
    expect(method_exists($component->instance(), 'updatingPerPage'))->toBeTrue();
});
it('displays product count for each category', function () {
    $category = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Eletrônicos'
    ]);
    \App\Models\Product::factory()->count(2)->create([
        'category_id' => $category->id,
        'user_id' => $this->user->id
    ]);
    Livewire::test(Index::class)
        ->assertSee('2 produtos');
});
it('listens to categories created event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('listens to categories updated event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('listens to categories deleted event', function () {
    $component = Livewire::test(Index::class);
    expect(method_exists($component->instance(), 'refresh'))->toBeTrue();
});
it('displays empty state when no categories exist', function () {
    Livewire::test(Index::class)
        ->assertSee('Nenhuma categoria cadastrada')
        ->assertSee('Comece criando sua primeira categoria');
});
it('displays empty state when search returns no results', function () {
    Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Eletrônicos'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'categoria inexistente')
        ->assertSee('Nenhuma categoria encontrada')
        ->assertSee('categoria inexistente');
});
it('can clear search', function () {
    Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Eletrônicos'
    ]);
    Livewire::test(Index::class)
        ->set('search', 'busca')
        ->assertSet('search', 'busca')
        ->call('$set', 'search', '')
        ->assertSet('search', '');
});
it('orders categories by creation date descending', function () {
    $oldCategory = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria Antiga',
        'created_at' => now()->subDays(2)
    ]);
    $newCategory = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Categoria Nova',
        'created_at' => now()
    ]);
    Livewire::test(Index::class)
        ->assertSeeInOrder(['Categoria Nova', 'Categoria Antiga']);
});
