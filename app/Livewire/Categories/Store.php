<?php

namespace App\Livewire\Categories;

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

    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    public function store(#[CurrentUser] $user): void
    {

        $data = $this->validate();

        try {

            $user->categories()->create($data);

            $this->dialog()->show([
                'icon' => 'success',
                'title' => 'Categoria Cadastrada!',
                'description' => 'A categoria foi cadastrada com sucesso.',
            ]);

            $this->dispatch('categories::created');

            $this->reset();
        } catch (\Exception $e) {

            $this->dialog()->show([
                'icon' => 'error',
                'title' => 'Erro ao Cadastrar Categoria!',
                'description' => 'Ocorreu um erro ao cadastrar a categoria. Tente novamente.',
            ]);
        }
    }
    public function render(): View
    {
        return view('livewire.categories.store');
    }
}
