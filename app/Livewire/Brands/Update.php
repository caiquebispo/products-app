<?php

namespace App\Livewire\Brands;

use App\Models\Brand;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\View;

class Update extends Component
{
    use WireUiActions;

    public ?Brand $brand = null;
    public bool $showModal = false;

    #[Validate('required|string|max:255')]
    public ?string $name = null;

    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    #[On('brands::edit')]
    public function edit(int $id): void
    {
        $this->brand = Brand::find($id);

        if (!$this->brand) {
            $this->notification()->error('Marca não encontrada!');
            return;
        }

        $this->name = $this->brand->name;
        $this->description = $this->brand->description;
        $this->showModal = true;
    }

    public function update(): void
    {
        if (!$this->brand) {
            $this->notification()->error('Marca não encontrada!');
            return;
        }

        $data = $this->validate();

        try {

            $this->brand->update($data);

            $this->notification()->success("Marca '{$this->brand->name}' atualizada com sucesso!");

            $this->reset();

            $this->dispatch('brands::updated');
        } catch (\Exception $e) {
            $this->notification()->error('Erro ao atualizar marca. Tente novamente.');
        }
    }

    public function render(): View
    {
        return view('livewire.brands.update');
    }
}
