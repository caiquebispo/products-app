<?php

namespace App\Livewire\Brands;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Container\Attributes\CurrentUser;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    #[On('brands::created')]
    #[On('brands::updated')]
    #[On('brands::deleted')]
    #[Layout('layouts.app')]
    public function render(#[CurrentUser] $user): View
    {
        $brands = $user->brands()
            ->when($this->search, fn($query) => $query->whereAny(['name', 'description'], 'like', '%' . $this->search . '%'))
            ->withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.brands.index', compact('brands'));
    }
}
