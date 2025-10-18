<div>
    
    <x-modal-card wire:model="showModal" name="edit-brand-modal" primary title="Edit Marca">
        <form wire:submit="update" class="space-y-4">
            <div>
                <x-input 
                    wire:model="name"
                    label="Nome da Marca"
                    placeholder="Digite o nome da marca"
                    required
                    class="w-full"
                />
            </div>

            <div>
                <x-textarea 
                    wire:model="description"
                    label="Descrição"
                    placeholder="Digite uma descrição para a marca (opcional)"
                    rows="3"
                    class="w-full"
                />
            </div>

            <div class="flex justify-end pt-4 space-x-3 border-t">
                <x-button  type="button" wire:click="$toggle('showModal')" variant="outline" class="px-4 py-2">
                    Cancelar
                </x-button>
                
                <x-button  type="submit" color="primary" class="px-4 py-2" wire:loading.attr="disabled">
                    <span>Editar</span>
                </x-button>
            </div>
        </form>
    </x-modal-card>
</div>
