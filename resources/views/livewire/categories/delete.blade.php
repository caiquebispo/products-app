<div>
    <!-- Modal de Confirmação de Exclusão -->
    <x-modal-card wire:model="showModal" name="delete-category-modal" primary title="Excluir Categoria">
     
            @if($category)
                <div class="space-y-4">
                    <div class="p-4 rounded-lg bg-gray-50">
                        <h4 class="mb-2 font-medium text-gray-900">Detalhes da Categoria:</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nome:</span>
                                <span class="font-medium text-gray-900">{{ $category->name }}</span>
                            </div>
                            @if($category->description)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Descrição:</span>
                                    <span class="max-w-xs font-medium text-gray-900 truncate">{{ $category->description }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Produtos:</span>
                                <span class="font-medium text-gray-900">{{ $category->products()->count() ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    @if($category->products()->count() > 0)
                        <div class="p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Não é possível excluir esta categoria
                                    </h3>
                                    <p class="mt-1 text-sm text-yellow-700">
                                        Esta categoria possui {{ $category->products()->count() }} produto(s) associado(s). 
                                        Remova ou transfira os produtos antes de excluir a categoria.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

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
                            :disabled="$category->products()->count() > 0"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Excluir Categoria
                        </x-button>
                    </div>
                </div>
            @endif
    </x-modal-card>
</div>
