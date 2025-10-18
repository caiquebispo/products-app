<?php

namespace App\Livewire\Categories;

use App\Models\Category;
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    #[On('categories::created')]
    #[On('categories::updated')]
    #[On('categories::deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render(#[CurrentUser] $user): View
    {
        $categories = $user->categories()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.categories.index', [
            'categories' => $categories,
        ]);
    }
}
