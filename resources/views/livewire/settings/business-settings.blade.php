<div>
    <div class="page-header">
        <h1 class="page-title">Configuración del Negocio</h1>
    </div>

    <form wire:submit="save" class="space-y-6 max-w-3xl">

        {{-- Business Info --}}
        <div class="card">
            <div class="card-header"><h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Datos del Negocio</h3></div>
            <div class="card-body space-y-4">
                <div>
                    <label class="label">Nombre del negocio <span class="text-red-500">*</span></label>
                    <input wire:model="business_name" type="text" class="input" placeholder="Taller y Repuestos NexPOS">
                    @error('business_name') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">NIT / Documento</label>
                        <input wire:model="business_nit" type="text" class="input" placeholder="900.000.000-1">
                    </div>
                    <div>
                        <label class="label">Ciudad</label>
                        <input wire:model="business_city" type="text" class="input" placeholder="Bogotá">
                    </div>
                </div>
                <div>
                    <label class="label">Dirección</label>
                    <input wire:model="business_address" type="text" class="input" placeholder="Calle 10 #25-30, Centro">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Teléfono</label>
                        <input wire:model="business_phone" type="text" class="input" placeholder="300 000 0000">
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input wire:model="business_email" type="email" class="input" placeholder="info@negocio.com">
                        @error('business_email') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- Tax --}}
        <div class="card">
            <div class="card-header"><h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Impuestos y Moneda</h3></div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Símbolo de moneda</label>
                        <input wire:model="currency_symbol" type="text" class="input" placeholder="$">
                    </div>
                    <div>
                        <label class="label">Código de moneda</label>
                        <input wire:model="currency_code" type="text" class="input" placeholder="COP">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model.live="tax_enabled" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Activar impuesto en ventas</span>
                    </label>
                </div>

                @if($tax_enabled)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Nombre del impuesto</label>
                        <input wire:model="tax_name" type="text" class="input" placeholder="IVA">
                    </div>
                    <div>
                        <label class="label">Porcentaje (%)</label>
                        <input wire:model="tax_percent" type="number" min="0" max="100" step="0.5" class="input">
                        @error('tax_percent') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Invoice --}}
        <div class="card">
            <div class="card-header"><h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Facturación</h3></div>
            <div class="card-body space-y-4">
                <div>
                    <label class="label">Mensaje pie de factura</label>
                    <input wire:model="invoice_footer" type="text" class="input" placeholder="¡Gracias por su compra!">
                </div>
                <div>
                    <label class="label">Ancho de tirilla térmica</label>
                    <select wire:model="thermal_width" class="input w-40">
                        <option value="58">58mm (pequeña)</option>
                        <option value="80">80mm (estándar)</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove>Guardar Configuración</span>
            <span wire:loading>Guardando...</span>
        </button>
    </form>
</div>
