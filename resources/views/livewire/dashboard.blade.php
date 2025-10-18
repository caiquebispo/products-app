<div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
  
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Visão geral dos seus produtos</p>
    </div>
    <x-card title="Filtros" class="my-6">
        <div class="flex flex-col items-end gap-4 lg:flex-row">
            <div class="flex-1">
                <x-select
                    label="Categorias"
                    placeholder="Selecione as categorias"
                    multiselect
                    :options="$categories"
                    option-label="name" option-value="id"
                    wire:model.live="categoryFilters" 
                />
            </div>
            <div class="flex-1">
                <x-select
                    label="Marcas"
                    placeholder="Selecione as marcas"
                    multiselect
                    :options="$brands"
                    option-label="name" option-value="id"
                    wire:model.live="brandFilters" 
                />
            </div>
            <div class="flex-1">
                <x-input label="Pesquisar" wire:model.live.debounce.300ms="searchFilter"  placeholder="Nome ou descrição..." />
            </div>
            <div class="flex-shrink-0">
                <x-select label="Por página" placeholder="..."
                    wire:model.live='perPage'
                    :options="[
                        ['name' => '5', 'value' => 5],
                        ['name' => '10', 'value' => 10],
                        ['name' => '25', 'value' => 25],
                        ['name' => '50', 'value' => 50],
                    ]" option-label="name" option-value="value"
                />
            </div>
            <div class="flex-shrink-0">
                <x-button wire:click="clearFilters">
                    Limpar
                </x-button>
            </div>
        </div>
        @if($this->hasActiveFilters())
            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-600">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtros aplicados:</span>
                
                    @if($searchFilter)
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-200">
                            Pesquisa: "{{ $searchFilter }}"
                            <button wire:click="$set('searchFilter', '')" class="ml-1.5 hover:text-blue-600 dark:hover:text-blue-300">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    @foreach($categoryFilters as $categoryId)
                        @php
                            $category = $categories->firstWhere('id', $categoryId);
                        @endphp
                        @if($category)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200">
                                Categoria: {{ $category->name }}
                            </span>
                        @endif
                    @endforeach
                    @foreach($brandFilters as $brandId)
                        @php
                            $brand = $brands->firstWhere('id', $brandId);
                        @endphp
                        @if($brand)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-200">
                                Marca: {{ $brand->name }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </x-card>
   
    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <!-- Header da Tabela -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Produtos
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                        ({{ $products->total() }} {{ $products->total() === 1 ? 'produto' : 'produtos' }})
                    </span>
                </h2>
            </div>
        </div>

        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[200px]">
                                Produto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                                Categoria
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                                Marca
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[100px]">
                                Preço
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[80px]">
                                Estoque
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                                Criado em
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach($products as $product)
                            <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $product->name }}
                                            </div>
                                            @if($product->description)
                                                <div class="max-w-xs text-sm text-gray-500 truncate dark:text-gray-400">
                                                    {{ $product->description }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ $product->brand->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        R$ {{ number_format($product->price, 2, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $product->stock > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $product->stock }}
                                        {{ $product->stock === 1 ? 'unidade' : 'unidades' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $product->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $product->created_at->format('H:i') }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                
                @if(!empty($categoryFilters) || !empty($brandFilters) || $searchFilter)
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhum produto encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Nenhum produto corresponde aos filtros aplicados.
                    </p>
                    <div class="mt-6">
                        <button wire:click="clearFilters" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md shadow-sm bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Limpar filtros
                        </button>
                    </div>
                @else
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhum produto cadastrado</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Comece criando seu primeiro produto.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md shadow-sm bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            Criar produto
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
</div>
