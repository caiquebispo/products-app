<x-card title="Novo Produto">
    <form wire:submit="store" class="space-y-4">
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

        <div class="flex justify-end">
            <x-button 
                type="submit"
                color="primary"
                class="w-full sm:w-auto"
            >Cadastrar Produto
            </x-button>
        </div>
    </form>
</x-card>
