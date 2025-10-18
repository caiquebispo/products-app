<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\View;

class Update extends Component
{
    use WireUiActions;

    public ?Product $product = null;
    public bool $showModal = false;

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

    #[On('products::edit')]
    public function edit($id): void
    {
        $this->product = Product::find($id);

        if (!$this->product) {
            $this->notification()->error('Produto não encontrado!');
            return;
        }

        $this->name = $this->product->name;
        $this->description = $this->product->description;
        $this->price = $this->product->price;
        $this->stock = $this->product->stock;
        $this->category_id = $this->product->category_id;
        $this->brand_id = $this->product->brand_id;
        $this->image = $this->product->image;
        $this->showModal = true;
    }

    public function update(#[CurrentUser] $user): void
    {
        if (!$this->product) {
            $this->notification()->error('Produto não encontrado!');
            return;
        }

        $data = $this->validate();

        try {

            $this->product->update($data);

            $this->notification()->success("Produto '{$this->product->name}' atualizado com sucesso!");

            $this->reset();

            $this->dispatch('products::updated');
        } catch (\Exception $e) {
            $this->notification()->error('Erro ao atualizar produto. Tente novamente.');
        }
    }
    public function render(#[CurrentUser] $user): View
    {
        return view('livewire.products.update', [
            'categories' => $user->categories()->orderBy('name')->get(),
            'brands' => $user->brands()->orderBy('name')->get(),
        ]);
    }
}
