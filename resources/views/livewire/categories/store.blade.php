<div>
    <x-card title="Cadastrar Categorias">
        <form class="space-y-4" wire:submit.prevent="store">

            <x-input label="Name" icon="tag" wire:model="name"/>

            <x-input label="Descrição" icon="document" wire:model="description"/>

            <div class="my-4">
                <x-button type="submit" icon="plus-circle" position="left" class="w-full">Salvar</x-ts-button>
            </div>
        </form>
    </x-card>

</div>
