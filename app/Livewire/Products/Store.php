<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\View;
use WireUi\Traits\WireUiActions;

class Store extends Component
{
    use WireUiActions;

    #[Validate('required|string|max:255')]
    public ?string $name = null;

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required|numeric|min:0')]
    public ?float $price = null;

    #[Validate('required|integer|min:0')]
    public ?int $stock = null;

    #[Validate('required|exists:categories,id')]
    public ?int $category_id = null;

    #[Validate('required|exists:brands,id')]
    public ?int $brand_id = null;

    #[Validate('nullable|string|max:255')]
    public ?string $image = null;

    public function store(#[CurrentUser] $user): void
    {
        $data = $this->validate();

        try {
            $user->products()->create($data);

            $this->dialog()->show([
                'icon' => 'success',
                'title' => 'Produto Cadastrado!',
                'description' => 'O produto foi cadastrado com sucesso.',
            ]);

            $this->dispatch('products::created');

            $this->reset();
        } catch (\Exception $e) {
            $this->dialog()->show([
                'icon' => 'error',
                'title' => 'Erro ao Cadastrar Produto!',
                'description' => 'Ocorreu um erro ao cadastrar o produto. Tente novamente.',
            ]);
        }
    }

    public function render(#[CurrentUser] $user): View
    {

        return view('livewire.products.store', [
            'categories' => $user->categories()->orderBy('name')->get(),
            'brands' => $user->brands()->orderBy('name')->get(),
        ]);
    }
}
