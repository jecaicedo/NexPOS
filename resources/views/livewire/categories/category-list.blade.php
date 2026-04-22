<div>
    <div class="page-header">
        <h1 class="page-title">Categorías</h1>
        @can('create categories')
        <button wire:click="create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva Categoría
        </button>
        @endcan
    </div>

    <div class="card mb-4"><div class="card-body py-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar categoría..." class="input pl-9 max-w-sm">
        </div>
    </div></div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th class="text-center">Productos</th>
                        <th class="text-center">Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($categories as $cat)
                <tr wire:key="cat-{{ $cat->id }}">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->color ?? '#6366f1' }}"></span>
                            <span class="font-medium text-sm text-gray-900 dark:text-gray-100">{{ $cat->name }}</span>
                        </div>
                    </td>
                    <td class="text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate">
                        {{ $cat->description ?? '—' }}
                    </td>
                    <td class="text-center">
                        <span class="badge-blue">{{ $cat->products_count }}</span>
                    </td>
                    <td class="text-center">
                        @can('edit categories')
                        <button wire:click="toggleActive({{ $cat->id }})"
                                class="{{ $cat->is_active ? 'badge-green' : 'badge-red' }} cursor-pointer hover:opacity-80 transition-opacity">
                            {{ $cat->is_active ? 'Activa' : 'Inactiva' }}
                        </button>
                        @else
                        <span class="{{ $cat->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $cat->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                        @endcan
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            @can('edit categories')
                            <button wire:click="edit({{ $cat->id }})" class="btn-icon btn-sm text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30" title="Editar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endcan
                            @can('delete categories')
                            <button wire:click="confirmDelete({{ $cat->id }})" class="btn-icon btn-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30" title="Eliminar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-400">No se encontraron categorías</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="card-footer">{{ $categories->links() }}</div>
        @endif
    </div>

    {{-- Form Modal --}}
    @if($showForm)
    <div class="modal-overlay" x-data x-on:keydown.escape.window="$wire.onClose()">
        <div class="modal modal-sm" @click.stop>
            @livewire('categories.category-form', ['categoryId' => $editingId], key('cf-'.($editingId ?? 'new')))
        </div>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if($showDeleteModal)
    <div class="modal-overlay">
        <div class="modal modal-sm p-6 text-center" @click.stop>
            <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-semibold dark:text-white mb-1">¿Eliminar categoría?</h3>
            <p class="text-xs text-gray-500 mb-4">Solo se puede eliminar si no tiene productos asignados.</p>
            <div class="flex gap-2">
                <button wire:click="delete" class="btn-danger flex-1">Eliminar</button>
                <button wire:click="$set('showDeleteModal', false)" class="btn-secondary flex-1">Cancelar</button>
            </div>
        </div>
    </div>
    @endif
</div>
