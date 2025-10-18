<?php

namespace App\Livewire\Brands;

use App\Models\Brand;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\View;

class Delete extends Component
{
    use WireUiActions;

    public ?Brand $brand = null;
    public bool $showModal = false;

    #[On('brands::delete')]
    public function confirmDelete($id): void
    {
        $this->brand = Brand::find($id);

        if (!$this->brand) {
            $this->notification()->error('Marca não encontrada!');
            return;
        }

        $this->showModal = true;
    }

    public function delete(#[CurrentUser] $user): void
    {
        if (!$this->brand) {
            $this->notification()->error('Marca não encontrada!');
            return;
        }

        try {

            if ($this->brand->products()->count() > 0) {
                $this->notification()->error(
                    'Não é possível excluir esta marca pois ela possui produtos associados!'
                );
                return;
            }

            $brandName = $this->brand->name;
            $this->brand->delete();

            $this->notification()->success("Marca '{$brandName}' excluída com sucesso!");

            $this->reset();

            $this->dispatch('brands::deleted');
        } catch (\Exception $e) {
            $this->notification()->error('Erro ao excluir marca. Tente novamente.');
        }
    }

    public function render(): View
    {
        return view('livewire.brands.delete');
    }
}
