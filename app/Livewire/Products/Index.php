<?php

namespace App\Livewire\Products;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Container\Attributes\CurrentUser;

class Index extends Component
{
    use WithPagination;

    public ?string $search = '';
    public ?int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    #[On('products::created')]
    #[On('products::updated')]
    #[On('products::deleted')]
    #[Layout('layouts.app')]
    public function render(#[CurrentUser] $user): View
    {
        $products = $user->products()
            ->with(['category', 'brand'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.products.index', [
            'products' => $products,
        ]);
    }
}
