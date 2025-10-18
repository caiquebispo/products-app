<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\View;

class Delete extends Component
{
    use WireUiActions;

    public ?Category $category = null;
    public bool $showModal = false;

    #[On('categories::delete')]
    public function confirmDelete($id): void
    {
        $this->category = Category::find($id);

        if (!$this->category) {
            $this->notification()->error('Categoria não encontrada!');
            return;
        }

        $this->showModal = true;
    }

    public function delete(#[CurrentUser] $user): void
    {
        if (!$this->category) {
            $this->notification()->error('Categoria não encontrada!');
            return;
        }
        try {

            if ($this->category->products()->count() > 0) {
                $this->notification()->error(
                    'Não é possível excluir esta categoria pois ela possui produtos associados!'
                );
                return;
            }

            $categoryName = $this->category->name;
            $this->category->delete();

            $this->notification()->success("Categoria '{$categoryName}' excluída com sucesso!");
            $this->showModal = false;
            $this->category = null;
            $this->dispatch('categories::deleted');
        } catch (\Exception $e) {
            $this->notification()->error('Erro ao excluir categoria. Tente novamente.');
        }
    }
    public function render(): View
    {
        return view('livewire.categories.delete');
    }
}
