<div class="p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ $customerId ? 'Editar Cliente' : 'Nuevo Cliente' }}
        </h3>
        <button wire:click="cancel" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="label">Nombre completo / Razón social <span class="text-red-500">*</span></label>
            <input wire:model="name" type="text" class="input" placeholder="Nombre del cliente o empresa">
            @error('name') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Tipo documento</label>
                <select wire:model="document_type" class="input">
                    <option value="CC">Cédula (CC)</option>
                    <option value="NIT">NIT</option>
                    <option value="CE">Cédula Extranjería</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
            </div>
            <div>
                <label class="label">Número documento</label>
                <input wire:model="document_number" type="text" class="input" placeholder="0000000000">
                @error('document_number') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Teléfono</label>
                <input wire:model="phone" type="text" class="input" placeholder="300 000 0000">
            </div>
            <div>
                <label class="label">Email</label>
                <input wire:model="email" type="email" class="input" placeholder="cliente@email.com">
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Ciudad</label>
                <input wire:model="city" type="text" class="input" placeholder="Bogotá">
            </div>
            <div>
                <label class="label">Dirección</label>
                <input wire:model="address" type="text" class="input" placeholder="Calle 10 #25-30">
            </div>
        </div>

        <div>
            <label class="label">Notas</label>
            <textarea wire:model="notes" rows="2" class="input resize-none" placeholder="Observaciones del cliente..."></textarea>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">Cliente activo</span>
        </label>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $customerId ? 'Actualizar' : 'Guardar' }}</span>
                <span wire:loading>Guardando...</span>
            </button>
            <button type="button" wire:click="cancel" class="btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
