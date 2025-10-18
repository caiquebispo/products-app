<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Container\Attributes\CurrentUser;

class Update extends Component
{
    use WireUiActions;

    public ?Category $category = null;
    public bool $showModal = false;

    #[Validate('required|string|max:255')]
    public ?string $name = null;

    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    #[On('categories::edit')]
    public function edit($id): void
    {
        $this->category = Category::find($id);

        if (!$this->category) {
            $this->notification()->error('Categoria não encontrada!');
            return;
        }

        $this->name = $this->category->name;
        $this->description = $this->category->description;
        $this->showModal = true;
    }

    public function update(#[CurrentUser] $user)
    {
        if (!$this->category) {
            $this->notification()->error('Categoria não encontrada!');
            return;
        }

        $data = $this->validate();

        try {
            $this->category->update($data);

            $this->notification()->success("Categoria '{$this->category->name}' atualizada com sucesso!");

            $this->showModal = false;
            $this->category = null;
            $this->reset();

            $this->dispatch('categories::updated');
        } catch (\Exception $e) {
            $this->notification()->error('Erro ao atualizar categoria. Tente novamente.');
        }
    }
    public function render()
    {
        return view('livewire.categories.update');
    }
}
