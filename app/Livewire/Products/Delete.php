<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\View;

class Delete extends Component
{
    use WireUiActions;

    public ?Product $product = null;
    public bool $showModal = false;

    #[On('products::delete')]
    public function confirmDelete($id): void
    {
        $this->product = Product::with(['category', 'brand'])->find($id);

        if (!$this->product) {
            $this->notification()->error('Produto não encontrado!');
            return;
        }

        $this->showModal = true;
    }

    public function delete(#[CurrentUser] $user): void
    {
        try {
            $productName = $this->product->name;
            $this->product->delete();

            $this->notification()->success("Produto '{$productName}' excluído com sucesso!");

            $this->reset();

            $this->dispatch('products::deleted');
        } catch (\Exception $e) {
            $this->notification()->error('Erro ao excluir produto. Tente novamente.');
        }
    }
    public function render(): View
    {
        return view('livewire.products.delete');
    }
}
