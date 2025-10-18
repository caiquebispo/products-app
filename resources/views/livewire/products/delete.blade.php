<div>
    <x-modal-card wire:model="showModal" name="delete-product-modal" primary title="Excluir Produto">
        <div class="space-y-4">

            <div class="p-4 rounded-lg bg-gray-50">
                <h4 class="mb-2 font-medium text-gray-900">Detalhes do Produto:</h4>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <h5 class="font-medium text-gray-900">{{ $product?->name }}</h5>
                            @if($product?->description)
                                <p class="mt-1 text-sm text-gray-500">{{ $product?->description }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Categoria:</span>
                            <span class="font-medium text-gray-900">{{ $product?->category->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Marca:</span>
                            <span class="font-medium text-gray-900">{{ $product?->brand->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Preço:</span>
                            <span class="font-medium text-gray-900">R$ {{ number_format($product?->price, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estoque:</span>
                            <span class="font-medium text-gray-900">{{ $product?->stock }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 space-x-3 border-t">
                <x-button 
                    wire:click="$toggle('showModal')"
                    variant="outline"
                    class="px-4 py-2"
                >
                    Cancelar
                </x-button>
                
                <x-button 
                    wire:click="delete"
                    color="red"
                    class="px-4 py-2"
                >
                    Excluir Produto
                </x-button>
            </div>
        </div>
    </x-modal-card>
</div>
