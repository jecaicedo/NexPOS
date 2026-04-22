<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Clientes</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $total }} clientes registrados</p>
        </div>
        @can('create customers')
        <button wire:click="createCustomer" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Cliente
        </button>
        @endcan
    </div>

    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-48 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre, documento, teléfono..." class="input pl-9">
                </div>
                <select wire:model.live="perPage" class="input w-20">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper rounded-2xl">
            <table class="table">
                <thead><tr>
                    <th wire:click="sort('name')" class="cursor-pointer select-none">
                        <div class="flex items-center gap-1">Cliente @if($sortField==='name')<span class="text-indigo-500">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</div>
                    </th>
                    <th>Documento</th>
                    <th>Contacto</th>
                    <th>Ciudad</th>
                    <th>Compras</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr></thead>
                <tbody>
                @forelse($customers as $customer)
                <tr wire:key="cust-{{ $customer->id }}">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold text-xs uppercase">
                                {{ mb_substr($customer->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-xs text-gray-900 dark:text-gray-100">{{ $customer->name }}</p>
                                @if($customer->email)
                                    <p class="text-[11px] text-gray-400">{{ $customer->email }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-xs">
                        <span class="badge-gray">{{ $customer->document_type }}</span>
                        <span class="ml-1 font-mono text-gray-600 dark:text-gray-300">{{ $customer->document_number ?? '—' }}</span>
                    </td>
                    <td class="text-xs text-gray-600 dark:text-gray-300">{{ $customer->phone ?? '—' }}</td>
                    <td class="text-xs text-gray-600 dark:text-gray-300">{{ $customer->city ?? '—' }}</td>
                    <td class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $customer->sales_count }}</td>
                    <td>
                        <span class="{{ $customer->is_active ? 'badge-green' : 'badge-red' }} text-xs">
                            {{ $customer->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            @can('edit customers')
                            <button wire:click="editCustomer({{ $customer->id }})" class="btn-icon btn-sm text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endcan
                            @can('delete customers')
                            <button wire:click="confirmDelete({{ $customer->id }})" class="btn-icon btn-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-10 text-sm text-gray-400">No se encontraron clientes</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
        <div class="card-footer">{{ $customers->links() }}</div>
        @endif
    </div>

    @if($showForm)
    <div class="modal-overlay" x-data x-on:keydown.escape.window="$wire.onCloseModal()">
        <div class="modal modal-md" @click.stop>
            @livewire('customers.customer-form', ['customerId' => $editingId], key('cf-'.($editingId ?? 'new')))
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal-overlay">
        <div class="modal modal-sm p-6" @click.stop>
            <div class="text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-base font-semibold dark:text-white mb-1">¿Eliminar cliente?</h3>
                <p class="text-xs text-gray-500 mb-5">El historial de ventas se conservará.</p>
                <div class="flex gap-2">
                    <button wire:click="deleteCustomer" class="btn-danger flex-1">Sí, eliminar</button>
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary flex-1">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
