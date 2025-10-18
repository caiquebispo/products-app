<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div>
                <livewire:products.store />
            </div>

            <div class="col-span-1 lg:col-span-2">
                <x-card title="Meus Produtos">
                    <!-- Search and Filter Section -->
                    <div class="flex flex-col items-start justify-between gap-4 mb-6 sm:flex-row sm:items-center">
                        <div class="flex-1 max-w-md">
                            <x-input 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Buscar produtos..."
                                icon="magnifying-glass"
                                class="w-full"
                            />
                        </div>
                        
                        <div class="flex items-center gap-2">
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
                    </div>
                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[150px]">
                                        Nome
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                                        Categoria
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                                        Marca
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">
                                        Preço
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[80px]">
                                        Estoque
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
                                @forelse ($products as $product)
                                    <tr class="transition-colors duration-150 hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap min-w-[150px]">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $product->name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap min-w-[120px]">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $product->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap min-w-[120px]">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $product->brand->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 min-w-[100px]">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap min-w-[80px]">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $product->stock }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 min-w-[140px]">
                                            {{ $product->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium min-w-[100px]">
                                            <div class="flex items-center justify-end gap-2">
                                                <button 
                                                    wire:click="$dispatch('products::edit', { id: {{ $product->id }} })"
                                                    class="text-indigo-600 transition-colors duration-150 hover:text-indigo-900"
                                                    title="Editar produto"
                                                >
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button 
                                                    wire:click="$dispatch('products::delete', { id: {{ $product->id }} })"
                                                    class="text-red-600 transition-colors duration-150 hover:text-red-900"
                                                    title="Excluir produto"
                                                >
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-8a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="min-w-full px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($products->hasPages())
                        <div class="mt-6">
                            {{ $products->links() }}
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>

    <!-- Modais para Editar e Excluir -->
    <livewire:products.update />
    <livewire:products.delete />
</div>
