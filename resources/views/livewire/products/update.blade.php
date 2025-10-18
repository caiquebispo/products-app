<div>
    <x-modal-card wire:model="showModal" name="edit-product-modal" primary title="Editar Produto">
        <form wire:submit="update" class="space-y-4">
            <div>
                <x-input 
                    wire:model="name"
                    label="Nome do Produto"
                    placeholder="Digite o nome do produto"
                    class="w-full"
                />
            </div>

            <div>
                <x-textarea 
                    wire:model="description"
                    label="Descrição"
                    placeholder="Digite uma descrição para o produto (opcional)"
                    rows="3"
                    class="w-full"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input 
                        wire:model="price"
                        label="Preço"
                        placeholder="0,00"
                        type="number"
                        step="0.01"
                        min="0"

                        class="w-full"
                    />
                </div>

                <div>
                    <x-input 
                        wire:model="stock"
                        label="Estoque"
                        placeholder="0"
                        type="number"
                        min="0"

                        class="w-full"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-select label="Categorias" placeholder="Selecione uma categoria"
                        wire:model='category_id'
                        :options="$categories" option-label="name" option-value="id"
                    />
                </div>

                <div>
                    <x-select label="Select Status" placeholder="Select one status"
                        wire:model='brand_id'
                        :options="$brands" option-label="name" option-value="id"
                    />
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50">
                <h4 class="mb-2 font-medium text-gray-900">Informações do Produto:</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Categoria atual:</span>
                        <span class="font-medium text-gray-900">{{ $product?->category->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Marca atual:</span>
                        <span class="font-medium text-gray-900">{{ $product?->brand->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Criado em:</span>
                        <span class="font-medium text-gray-900">{{ $product?->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Atualizado em:</span>
                        <span class="font-medium text-gray-900">{{ $product?->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 space-x-3 border-t">
                <x-button 
                    type="button"
                    wire:click="$toggle('showModal')"
                    variant="outline"
                    class="px-4 py-2"
                >
                    Cancelar
                </x-button>
                
                <x-button 
                    type="submit"
                    color="primary"
                    class="px-4 py-2"
                >Atualizar Produto
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
