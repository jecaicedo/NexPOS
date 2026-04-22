<div>
    <div class="page-header">
        <h1 class="page-title">Empleados / Mecánicos</h1>
        @can('create employees')
        <button wire:click="create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Empleado
        </button>
        @endcan
    </div>

    <div class="card mb-4"><div class="card-body py-3">
        <div class="flex gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar empleado..." class="input pl-9">
            </div>
        </div>
    </div></div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($employees as $emp)
        <div wire:key="emp-{{ $emp->id }}" class="card p-5">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-700 dark:text-amber-300 font-bold text-sm uppercase">
                        {{ mb_substr($emp->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-gray-900 dark:text-gray-100">{{ $emp->name }}</p>
                        <p class="text-xs text-gray-400">{{ $emp->specialty ?? 'General' }}</p>
                    </div>
                </div>
                <span class="{{ $emp->is_active ? 'badge-green' : 'badge-red' }} text-xs">{{ $emp->is_active ? 'Activo' : 'Inactivo' }}</span>
            </div>

            <div class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                @if($emp->phone) <p>📱 {{ $emp->phone }}</p> @endif
                @if($emp->document_number) <p>🪪 {{ $emp->document_number }}</p> @endif
                <p>💰 Tarifa: <span class="font-semibold text-gray-700 dark:text-gray-200">${{ number_format($emp->labor_rate, 0, ',', '.') }}</span></p>
                <p>🔧 Trabajos: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $emp->workshop_jobs_count }}</span></p>
                @if($emp->user)
                <p class="flex items-center gap-1 mt-1 pt-1 border-t border-gray-100 dark:border-gray-800">
                    <svg class="w-3 h-3 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-indigo-500 dark:text-indigo-400 font-medium">{{ $emp->user->name }}</span>
                </p>
                @endif
            </div>

            <div class="flex gap-2 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                @can('edit employees')
                <button wire:click="edit({{ $emp->id }})" class="btn-secondary btn-sm flex-1">Editar</button>
                @endcan
                @can('delete employees')
                <button wire:click="confirmDelete({{ $emp->id }})" class="btn-icon btn-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-span-3 card p-10 text-center text-gray-400">
            <p>No se encontraron empleados</p>
        </div>
        @endforelse
    </div>

    @if($employees->hasPages())
    <div class="mt-4">{{ $employees->links() }}</div>
    @endif

    {{-- Form Modal --}}
    @if($showForm)
    <div class="modal-overlay" x-data x-on:keydown.escape.window="$wire.onClose()">
        <div class="modal modal-md" @click.stop>
            @livewire('employees.employee-form', ['employeeId' => $editingId], key('ef-'.($editingId ?? 'new')))
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal-overlay">
        <div class="modal modal-sm p-6 text-center" @click.stop>
            <h3 class="text-base font-semibold dark:text-white mb-1">¿Eliminar empleado?</h3>
            <p class="text-xs text-gray-500 mb-4">Los trabajos del taller se conservarán.</p>
            <div class="flex gap-2">
                <button wire:click="delete" class="btn-danger flex-1">Eliminar</button>
                <button wire:click="$set('showDeleteModal', false)" class="btn-secondary flex-1">Cancelar</button>
            </div>
        </div>
    </div>
    @endif
</div>
