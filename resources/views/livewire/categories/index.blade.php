
<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div>
                <livewire:categories.store />
            </div>

            <div class="col-span-1 lg:col-span-2">
                <x-card title="Minhas Categorias">
                    <!-- Search and Filter Section -->
                    <div class="flex flex-col items-start justify-between gap-4 mb-6 sm:flex-row sm:items-center">
                        <div class="flex-1 max-w-md">
                            <x-input 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Buscar categorias..."
                                icon="magnifying-glass"
                                class="w-full"
                            />
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Por página:</label>
                            <select wire:model.live="perPage" class="text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                    <!-- Categories Table -->
                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[150px]">
                                        Nome
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">
                                        Descrição
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                                        Produtos
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[140px]">
                                        Criado em
                                    </th>
                                    <th scope="col" class="relative px-6 py-3 min-w-[100px]">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($categories as $category)
                                    <tr class="transition-colors duration-150 hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap min-w-[150px]">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $category->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 min-w-[200px]">
                                            <div class="max-w-xs text-sm text-gray-500 truncate">
                                                {{ $category->description ?? 'Sem descrição' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap min-w-[120px]">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $category->products_count }} {{ $category->products_count === 1 ? 'produto' : 'produtos' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 min-w-[140px]">
                                            {{ $category->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium min-w-[100px]">
                                            <div class="flex items-center justify-end gap-2">
                                                <button 
                                                    wire:click="$dispatch('categories::edit', { id: {{ $category->id }} })"
                                                    class="text-indigo-600 transition-colors duration-150 hover:text-indigo-900"
                                                    title="Editar categoria"
                                                >
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button 
                                                    wire:click="$dispatch('categories::delete', { id: {{ $category->id }} })"
                                                    class="text-red-600 transition-colors duration-150 hover:text-red-900"
                                                    title="Excluir categoria"
                                                >
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="min-w-full px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                <h3 class="mb-2 text-lg font-medium text-gray-900">
                                                    @if($search)
                                                        Nenhuma categoria encontrada
                                                    @else
                                                        Nenhuma categoria cadastrada
                                                    @endif
                                                </h3>
                                                <p class="max-w-md text-center text-gray-500">
                                                    @if($search)
                                                        Não encontramos categorias que correspondam à sua busca "{{ $search }}".
                                                    @else
                                                        Comece criando sua primeira categoria usando o formulário ao lado.
                                                    @endif
                                                </p>
                                                @if($search)
                                                    <button 
                                                        wire:click="$set('search', '')"
                                                        class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-indigo-700 transition-colors duration-150 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200"
                                                    >
                                                        Limpar busca
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($categories->hasPages())
                        <div class="mt-6">
                            {{ $categories->links() }}
                        </div>
                    @endif

                    <!-- Results Summary -->
                    <div class="flex items-center justify-between mt-4 text-sm text-gray-700">
                        <div>
                            Mostrando {{ $categories->firstItem() ?? 0 }} a {{ $categories->lastItem() ?? 0 }} 
                            de {{ $categories->total() }} {{ $categories->total() === 1 ? 'categoria' : 'categorias' }}
                        </div>
                        @if($search)
                            <div class="flex items-center gap-2">
                                <span>Filtrado por: "{{ $search }}"</span>
                                <button 
                                    wire:click="$set('search', '')"
                                    class="font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Limpar
                                </button>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </div>

    <!-- Modais para Editar e Excluir -->
    <livewire:categories.update />
    <livewire:categories.delete />
</div>

