<div class="p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $employeeId ? 'Editar Empleado' : 'Nuevo Empleado' }}</h3>
        <button wire:click="cancel" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="label">Usuario del sistema vinculado</label>
            <select wire:model="user_id" class="input">
                <option value="">— Sin usuario vinculado —</option>
                @foreach($availableUsers as $u)
                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            <p class="text-[11px] text-gray-400 mt-1">Permite al empleado iniciar sesión y ver sus órdenes de taller.</p>
        </div>
        <div>
            <label class="label">Nombre completo <span class="text-red-500">*</span></label>
            <input wire:model="name" type="text" class="input" placeholder="Juan Mecánico">
            @error('name') <p class="error-msg">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Documento</label>
                <input wire:model="document_number" type="text" class="input" placeholder="10001001">
                @error('document_number') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Teléfono</label>
                <input wire:model="phone" type="text" class="input" placeholder="310 100 1001">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Email</label>
                <input wire:model="email" type="email" class="input" placeholder="mecanico@taller.com">
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Especialidad</label>
                <input wire:model="specialty" type="text" class="input" placeholder="Motor, Frenos, General...">
            </div>
        </div>
        <div>
            <label class="label">Tarifa por trabajo ($)</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                <input wire:model="labor_rate" type="number" min="0" step="1000" class="input pl-7">
            </div>
        </div>
        <div>
            <label class="label">Notas</label>
            <textarea wire:model="notes" rows="2" class="input resize-none"></textarea>
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">Empleado activo</span>
        </label>
        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $employeeId ? 'Actualizar' : 'Guardar' }}</span>
                <span wire:loading>Guardando...</span>
            </button>
            <button type="button" wire:click="cancel" class="btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
