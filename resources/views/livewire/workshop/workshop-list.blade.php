<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Taller / Órdenes de Trabajo</h1>
            <div class="flex gap-2 mt-1 flex-wrap">
                @foreach(['pending' => ['Pendiente','badge-yellow'], 'in_progress' => ['En Proceso','badge-blue'], 'completed' => ['Completado','badge-green'], 'delivered' => ['Entregado','badge-purple'], 'cancelled' => ['Cancelado','badge-red']] as $s => [$label, $cls])
                    <span class="{{ $cls }} text-xs">{{ $label }}: {{ $counts[$s] ?? 0 }}</span>
                @endforeach
            </div>
        </div>
        @can('create workshop')
        <button wire:click="create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva Orden
        </button>
        @endcan
    </div>

    <div class="card mb-4"><div class="card-body py-3">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por orden, placa o cliente..." class="input pl-9">
            </div>
            <select wire:model.live="status" class="input w-40">
                <option value="">Todos los estados</option>
                <option value="pending">Pendiente</option>
                <option value="in_progress">En Proceso</option>
                <option value="completed">Completado</option>
                <option value="delivered">Entregado</option>
                <option value="cancelled">Cancelado</option>
            </select>
        </div>
    </div></div>

    <div class="card">
        <div class="table-wrapper rounded-2xl">
            <table class="table">
                <thead><tr>
                    <th>Orden</th>
                    <th>Cliente / Vehículo</th>
                    <th>Mecánico</th>
                    <th>Descripción</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-right">Acciones</th>
                </tr></thead>
                <tbody>
                @forelse($jobs as $job)
                <tr wire:key="job-{{ $job->id }}">
                    <td class="font-mono text-xs font-semibold text-indigo-600 dark:text-indigo-400">{{ $job->job_number }}</td>
                    <td>
                        <p class="text-xs font-medium text-gray-900 dark:text-gray-100">{{ $job->customer?->name ?? '—' }}</p>
                        @if($job->vehicle_plate)
                            <p class="text-[11px] text-gray-400 font-mono">🏍 {{ $job->vehicle_plate }} {{ $job->vehicle_brand }} {{ $job->vehicle_model }}</p>
                        @endif
                    </td>
                    <td class="text-xs text-gray-600 dark:text-gray-300">{{ $job->employee?->name ?? '—' }}</td>
                    <td class="text-xs text-gray-600 dark:text-gray-300 max-w-xs">
                        <p class="truncate" title="{{ $job->description }}">{{ $job->description }}</p>
                    </td>
                    <td class="font-bold text-sm text-gray-900 dark:text-gray-100">${{ number_format($job->total, 0, ',', '.') }}</td>
                    <td>
                        @php $badge = $job->status_badge; @endphp
                        <span class="badge badge-{{ $badge['color'] }} text-xs">{{ $badge['label'] }}</span>
                    </td>
                    <td class="text-xs text-gray-400 whitespace-nowrap">{{ $job->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            {{-- Quick status change --}}
                            @if($job->status === 'pending')
                                <button wire:click="updateStatus({{ $job->id }}, 'in_progress')" class="btn-sm btn-secondary text-xs">Iniciar</button>
                            @elseif($job->status === 'in_progress')
                                <button wire:click="updateStatus({{ $job->id }}, 'completed')" class="btn-sm btn-success text-xs">Completar</button>
                            @elseif($job->status === 'completed')
                                <button wire:click="updateStatus({{ $job->id }}, 'delivered')" class="btn-sm btn-secondary text-xs">Entregar</button>
                            @endif

                            @can('edit workshop')
                            <button wire:click="edit({{ $job->id }})" class="btn-icon btn-sm text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endcan
                            @can('delete workshop')
                            <button wire:click="confirmDelete({{ $job->id }})" class="btn-icon btn-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-10 text-sm text-gray-400">No se encontraron órdenes</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($jobs->hasPages())<div class="card-footer">{{ $jobs->links() }}</div>@endif
    </div>

    @if($showForm)
    <div class="modal-overlay" x-data x-on:keydown.escape.window="$wire.onClose()">
        <div class="modal modal-xl max-h-[90vh] overflow-y-auto" @click.stop>
            @livewire('workshop.workshop-form', ['jobId' => $editingId], key('wf-'.($editingId ?? 'new')))
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal-overlay">
        <div class="modal modal-sm p-6 text-center" @click.stop>
            <h3 class="text-base font-semibold dark:text-white mb-1">¿Eliminar orden de trabajo?</h3>
            <p class="text-xs text-gray-500 mb-4">Esta acción no se puede deshacer.</p>
            <div class="flex gap-2">
                <button wire:click="delete" class="btn-danger flex-1">Eliminar</button>
                <button wire:click="$set('showDeleteModal', false)" class="btn-secondary flex-1">Cancelar</button>
            </div>
        </div>
    </div>
    @endif
</div>
