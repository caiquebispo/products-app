<?php

namespace App\Livewire;


use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    use WithPagination;

    #[Url(as: 'categories', keep: true)]
    public array $categoryFilters = [];

    #[Url(as: 'brands', keep: true)]
    public array $brandFilters = [];

    #[Url(as: 'search', keep: true)]
    public ?string $searchFilter = '';

    #[Url(as: 'per_page', keep: true)]
    public ?int $perPage = 10;

    public function mount()
    {
        $this->categoryFilters = $this->categoryFilters ?: [];
        $this->brandFilters = $this->brandFilters ?: [];
    }

    public function clearFilters(): void
    {
        $this->categoryFilters = [];
        $this->brandFilters = [];
        $this->searchFilter = '';
        $this->resetPage();
    }
    public function hasActiveFilters()
    {
        return !empty($this->searchFilter) ||
            !empty($this->categoryFilters) ||
            !empty($this->brandFilters);
    }
    #[Layout('layouts.app')]
    public function render(#[CurrentUser] $user): View
    {

        $products = Cache::remember($this->generateCacheKey($user->id), now()->addMinute(30), function () use ($user) {

            return $user->products()->with(['category', 'brand'])
                ->where('user_id', $user->id)
                ->when(!empty($this->categoryFilters), fn($query) => $query->whereIn('category_id', $this->categoryFilters))
                ->when(!empty($this->brandFilters), fn($query) => $query->whereIn('brand_id', $this->brandFilters))
                ->when(!empty($this->searchFilter), fn($query) => $query->whereAny(['name', 'description',], 'like', '%' . trim($this->searchFilter) . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage);
        });


        return view('livewire.dashboard', [
            'products' => $products,
            'categories' => $user->categories()->orderBy('name')->get(),
            'brands' => $user->brands()->orderBy('name')->get(),
        ]);
    }
    private function generateCacheKey(int $userId): string
    {
        $filters = [
            'user_id' => $userId,
            'categories' => implode('::', $this->categoryFilters),
            'brands' => implode('::', $this->brandFilters),
            'search' => $this->searchFilter,
            'per_page' => $this->perPage,
        ];

        return 'products::' . md5(serialize($filters));
    }
}
