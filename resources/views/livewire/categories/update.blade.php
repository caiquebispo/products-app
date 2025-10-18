<div>
    <x-modal-card wire:model="showModal" name="edit-category-modal" primary title="Editar Categoria">
        <form wire:submit="update" class="space-y-4">
            <div>
                <x-input 
                    wire:model="name"
                    label="Nome da Categoria"
                    placeholder="Digite o nome da categoria"
                    required
                    class="w-full"
                />
            </div>

            <div>
                <x-textarea 
                    wire:model="description"
                    label="Descrição"
                    placeholder="Digite uma descrição para a categoria (opcional)"
                    rows="3"
                    class="w-full"
                />
            </div>

            <div class="p-4 rounded-lg bg-gray-50">
                <h4 class="mb-2 font-medium text-gray-900">Informações da Categoria:</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Produtos associados:</span>
                        <span class="font-medium text-gray-900">{{ $category?->products()?->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Criada em:</span>
                        <span class="font-medium text-gray-900">{{ $category?->created_at->format('d/m/Y H:i') }}</span>
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
                >
                Atualizar Categoria
                </x-button>
            </div>
        </form>
    </x-modal-card>
</div>
