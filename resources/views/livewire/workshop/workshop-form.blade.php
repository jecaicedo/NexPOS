<div class="p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $jobId ? 'Editar Orden de Trabajo' : 'Nueva Orden de Trabajo' }}</h3>
        <button wire:click="cancel" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>

    <form wire:submit="save" class="space-y-5">

        {{-- Client + Employee --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Cliente</label>
                <select wire:model="customer_id" class="input">
                    <option value="">Sin cliente</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Mecánico asignado</label>
                <select wire:model="employee_id" class="input">
                    <option value="">Sin asignar</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}">{{ $e->name }} ({{ $e->specialty }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Vehicle --}}
        <div>
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2 uppercase tracking-wide">Datos del vehículo</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="label">Marca</label>
                    <input wire:model="vehicle_brand" type="text" class="input" placeholder="Honda, Yamaha...">
                </div>
                <div>
                    <label class="label">Modelo</label>
                    <input wire:model="vehicle_model" type="text" class="input" placeholder="CB190, FZ125...">
                </div>
                <div>
                    <label class="label">Placa</label>
                    <input wire:model="vehicle_plate" type="text" class="input" placeholder="ABC-123" style="text-transform:uppercase">
                </div>
                <div>
                    <label class="label">Año</label>
                    <input wire:model="vehicle_year" type="text" class="input" placeholder="2022">
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="label">Descripción del trabajo <span class="text-red-500">*</span></label>
            <textarea wire:model="description" rows="3" class="input resize-none" placeholder="Describe el trabajo a realizar..."></textarea>
            @error('description') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Diagnóstico</label>
            <textarea wire:model="diagnosis" rows="2" class="input resize-none" placeholder="Diagnóstico técnico..."></textarea>
        </div>

        {{-- Parts --}}
        <div>
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2 uppercase tracking-wide">Repuestos / Partes</p>
            <div class="relative mb-3">
                <input wire:model.live.debounce.200ms="partSearch" type="text" class="input" placeholder="Buscar y agregar repuesto...">
                @if(count($partResults))
                <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl z-50 max-h-48 overflow-y-auto">
                    @foreach($partResults as $pr)
                    <button type="button" wire:click="addPart({{ $pr['id'] }})"
                            class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm transition-colors">
                        <span class="text-gray-900 dark:text-gray-100">{{ $pr['name'] }}</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-xs">${{ number_format($pr['sale_price'],0,',','.') }}</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            @if(count($parts))
            <div class="space-y-2">
                @foreach($parts as $i => $part)
                <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="flex-1 text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ $part['name'] }}</span>
                    <span class="text-xs text-gray-500">${{ number_format($part['price'],0,',','.') }}</span>
                    <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded overflow-hidden">
                        <button type="button" wire:click="updatePartQty({{ $i }}, {{ $part['quantity'] - 1 }})" class="px-2 py-1 text-xs hover:bg-gray-100 dark:hover:bg-gray-700">−</button>
                        <span class="px-2 text-xs font-semibold">{{ $part['quantity'] }}</span>
                        <button type="button" wire:click="updatePartQty({{ $i }}, {{ $part['quantity'] + 1 }})" class="px-2 py-1 text-xs hover:bg-gray-100 dark:hover:bg-gray-700">+</button>
                    </div>
                    <span class="text-xs font-bold text-gray-900 dark:text-gray-100 w-20 text-right">${{ number_format($part['subtotal'],0,',','.') }}</span>
                    <button type="button" wire:click="removePart({{ $i }})" class="text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Costs --}}
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="label">Mano de obra ($)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                    <input wire:model.live="labor_cost" type="number" min="0" step="1000" class="input pl-7">
                </div>
            </div>
            <div>
                <label class="label">Repuestos</label>
                <div class="input bg-gray-50 dark:bg-gray-800 font-semibold">${{ number_format($parts_cost, 0, ',', '.') }}</div>
            </div>
            <div>
                <label class="label">Total</label>
                <div class="input bg-indigo-50 dark:bg-indigo-900/30 font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($total, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Estado</label>
                <select wire:model="status" class="input">
                    <option value="pending">Pendiente</option>
                    <option value="in_progress">En Proceso</option>
                    <option value="completed">Completado</option>
                    <option value="delivered">Entregado</option>
                    <option value="cancelled">Cancelado</option>
                </select>
            </div>
            <div>
                <label class="label">Notas</label>
                <input wire:model="notes" type="text" class="input" placeholder="Observaciones...">
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $jobId ? 'Actualizar Orden' : 'Crear Orden' }}</span>
                <span wire:loading>Guardando...</span>
            </button>
            <button type="button" wire:click="cancel" class="btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
