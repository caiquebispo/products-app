<x-card title="Nova Marca">
    <form wire:submit="store" class="space-y-4">
        <div>
            <x-input 
                wire:model="name"
                label="Nome da Marca"
                placeholder="Digite o nome da marca"
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

        <div class="flex justify-end">
            <x-button 
                type="submit"
                color="primary"
                class="w-full sm:w-auto"
                wire:loading.attr="disabled"
            >
                <svg wire:loading class="w-4 h-4 mr-2 -ml-1 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg wire:loading.remove class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span wire:loading.remove>Cadastrar Marca</span>
                <span wire:loading>Cadastrando...</span>
            </x-button>
        </div>
    </form>
</x-card>
