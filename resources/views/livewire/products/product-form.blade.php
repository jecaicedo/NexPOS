<div class="p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ $productId ? 'Editar Producto' : 'Nuevo Producto' }}
        </h3>
        <button wire:click="cancel" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Nombre --}}
            <div class="md:col-span-2">
                <label class="label">Nombre del producto <span class="text-red-500">*</span></label>
                <input wire:model="name" type="text" class="input" placeholder="Ej: Bujía NGK CR7HSA">
                @error('name') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            {{-- Categoría --}}
            <div>
                <label class="label">Categoría</label>
                <select wire:model="category_id" class="input">
                    <option value="">Sin categoría</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Unidad --}}
            <div>
                <label class="label">Unidad de medida</label>
                <select wire:model="unit" class="input">
                    <option value="unidad">Unidad</option>
                    <option value="par">Par</option>
                    <option value="caja">Caja</option>
                    <option value="litro">Litro</option>
                    <option value="metro">Metro</option>
                    <option value="kg">Kilogramo</option>
                    <option value="set">Set / Kit</option>
                </select>
            </div>

            {{-- SKU --}}
            <div>
                <label class="label">SKU</label>
                <input wire:model="sku" type="text" class="input" placeholder="Ej: MOT-001">
                @error('sku') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            {{-- Código de barras --}}
            <div>
                <label class="label">Código de barras</label>
                <input wire:model="barcode" type="text" class="input" placeholder="EAN, UPC...">
            </div>

            {{-- Precio venta --}}
            <div>
                <label class="label">Precio de venta <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                    <input wire:model="sale_price" type="number" min="0" step="0.01" class="input pl-7">
                </div>
                @error('sale_price') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            {{-- Precio compra --}}
            <div>
                <label class="label">Precio de compra</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                    <input wire:model="purchase_price" type="number" min="0" step="0.01" class="input pl-7">
                </div>
            </div>

            {{-- Stock --}}
            <div>
                <label class="label">Stock actual</label>
                <input wire:model="stock" type="number" min="0" class="input">
                @error('stock') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            {{-- Stock mínimo --}}
            <div>
                <label class="label">Stock mínimo (alerta)</label>
                <input wire:model="min_stock" type="number" min="0" class="input">
            </div>
        </div>

        {{-- Descripción --}}
        <div>
            <label class="label">Descripción</label>
            <textarea wire:model="description" rows="2" class="input resize-none" placeholder="Descripción o notas del producto..."></textarea>
        </div>

        {{-- Flags --}}
        <div class="flex gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Producto activo</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input wire:model="track_stock" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Controlar stock</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $productId ? 'Actualizar' : 'Guardar' }} Producto</span>
                <span wire:loading>Guardando...</span>
            </button>
            <button type="button" wire:click="cancel" class="btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
