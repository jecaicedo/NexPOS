<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Ventas</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ $totalCount }} ventas &bull; Total: <span class="font-semibold text-gray-700 dark:text-gray-200">${{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </p>
        </div>
        @can('create sales')
        <a href="{{ route('pos.index') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva Venta
        </a>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-48 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por factura o cliente..." class="input pl-9">
                </div>
                <input wire:model.live="dateFrom" type="date" class="input w-36">
                <input wire:model.live="dateTo" type="date" class="input w-36">
                <select wire:model.live="status" class="input w-36">
                    <option value="">Todos los estados</option>
                    <option value="completed">Completadas</option>
                    <option value="cancelled">Anuladas</option>
                    <option value="pending">Pendientes</option>
                </select>
                <select wire:model.live="paymentMethod" class="input w-36">
                    <option value="">Todos los métodos</option>
                    <option value="cash">Efectivo</option>
                    <option value="card">Tarjeta</option>
                    <option value="transfer">Transferencia</option>
                    <option value="mixed">Mixto</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper rounded-2xl">
            <table class="table">
                <thead><tr>
                    <th wire:click="sort('invoice_number')" class="cursor-pointer select-none">
                        <div class="flex items-center gap-1">Factura @if($sortField==='invoice_number')<span class="text-indigo-500">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</div>
                    </th>
                    <th>Cliente</th>
                    <th>Método</th>
                    <th wire:click="sort('total')" class="cursor-pointer select-none">
                        <div class="flex items-center gap-1">Total @if($sortField==='total')<span class="text-indigo-500">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</div>
                    </th>
                    <th>Estado</th>
                    <th>Cajero</th>
                    <th wire:click="sort('created_at')" class="cursor-pointer select-none">
                        <div class="flex items-center gap-1">Fecha @if($sortField==='created_at')<span class="text-indigo-500">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</div>
                    </th>
                    <th class="text-right">Acciones</th>
                </tr></thead>
                <tbody>
                @forelse($sales as $sale)
                <tr wire:key="sale-{{ $sale->id }}" class="{{ $sale->status === 'cancelled' ? 'opacity-60' : '' }}">
                    <td>
                        <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 dark:text-indigo-400 font-mono text-xs hover:underline font-semibold">
                            {{ $sale->invoice_number }}
                        </a>
                    </td>
                    <td class="text-xs text-gray-700 dark:text-gray-300">{{ $sale->customer?->name ?? 'Consumidor Final' }}</td>
                    <td>
                        <span class="badge-gray text-xs capitalize">
                            {{ match($sale->payment_method) {
                                'cash' => '💵 Efectivo',
                                'card' => '💳 Tarjeta',
                                'transfer' => '🏦 Transf.',
                                'mixed' => '🔀 Mixto',
                                default => $sale->payment_method
                            } }}
                        </span>
                    </td>
                    <td class="font-bold text-sm text-gray-900 dark:text-gray-100">${{ number_format($sale->total, 0, ',', '.') }}</td>
                    <td>
                        <span class="{{ match($sale->status) {
                            'completed' => 'badge-green',
                            'cancelled' => 'badge-red',
                            default => 'badge-yellow'
                        } }} text-xs">
                            {{ match($sale->status) {
                                'completed' => 'Completada',
                                'cancelled' => 'Anulada',
                                default => 'Pendiente'
                            } }}
                        </span>
                    </td>
                    <td class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->user?->name }}</td>
                    <td class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('sales.show', $sale) }}" title="Ver detalle"
                               class="btn-icon btn-sm text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('sales.invoice', $sale) }}" target="_blank" title="Imprimir"
                               class="btn-icon btn-sm text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </a>
                            @if($sale->status === 'completed')
                            @can('cancel sales')
                            <button wire:click="confirmCancel({{ $sale->id }})" title="Anular"
                                    class="btn-icon btn-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </button>
                            @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-10 text-sm text-gray-400">No se encontraron ventas</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
        <div class="card-footer">{{ $sales->links() }}</div>
        @endif
    </div>

    {{-- Cancel Modal --}}
    @if($showCancelModal)
    <div class="modal-overlay">
        <div class="modal modal-sm p-6" @click.stop>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Anular Venta</h3>
            <p class="text-xs text-gray-500 mb-3">Esta acción restaurará el stock de los productos vendidos.</p>
            <div>
                <label class="label">Motivo de anulación <span class="text-red-500">*</span></label>
                <textarea wire:model="cancelReason" rows="3" class="input resize-none" placeholder="Describe el motivo..."></textarea>
                @error('cancelReason') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 mt-4">
                <button wire:click="cancelSale" class="btn-danger flex-1">Anular Venta</button>
                <button wire:click="$set('showCancelModal', false)" class="btn-secondary flex-1">Cancelar</button>
            </div>
        </div>
    </div>
    @endif
</div>
