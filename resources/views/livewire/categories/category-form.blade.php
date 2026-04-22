<div class="p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $categoryId ? 'Editar Categoría' : 'Nueva Categoría' }}</h3>
        <button wire:click="cancel" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="label">Nombre <span class="text-red-500">*</span></label>
            <input wire:model="name" type="text" class="input" placeholder="Ej: Motor, Frenos, Transmisión...">
            @error('name') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Descripción</label>
            <input wire:model="description" type="text" class="input" placeholder="Descripción opcional">
        </div>

        <div>
            <label class="label">Color identificador</label>
            <div class="flex items-center gap-3">
                <input wire:model.live="color" type="color"
                       class="w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer p-0.5 bg-white dark:bg-gray-800">
                <span class="text-sm text-gray-600 dark:text-gray-400" x-data="{ color: @entangle('color') }" x-text="color"></span>
                <div class="flex gap-1.5 ml-auto">
                    @foreach(['#6366f1','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444','#8b5cf6','#14b8a6','#f97316','#6b7280'] as $preset)
                    <button type="button" wire:click="$set('color', '{{ $preset }}')"
                            class="w-5 h-5 rounded-full border-2 transition-transform hover:scale-110 {{ $color === $preset ? 'border-gray-900 dark:border-white scale-110' : 'border-transparent' }}"
                            style="background-color: {{ $preset }}"></button>
                    @endforeach
                </div>
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">Categoría activa</span>
        </label>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $categoryId ? 'Actualizar' : 'Guardar' }}</span>
                <span wire:loading>Guardando...</span>
            </button>
            <button type="button" wire:click="cancel" class="btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
