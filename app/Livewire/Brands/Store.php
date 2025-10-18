<?php

namespace App\Livewire\Brands;

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

            $user->brands()->create($data);

            $this->dialog()->show([
                'icon' => 'success',
                'title' => 'Marca Cadastrada!',
                'description' => 'A marca foi cadastrada com sucesso.',
            ]);

            $this->dispatch('brands::created');

            $this->reset();
        } catch (\Exception $e) {

            $this->dialog()->show([
                'icon' => 'error',
                'title' => 'Erro ao Cadastrar Marca!',
                'description' => 'Ocorreu um erro ao cadastrar a marca. Tente novamente.',
            ]);
        }
    }
    public function render(): View
    {
        return view('livewire.brands.store');
    }
}
