<div class="flex flex-col lg:flex-row gap-4 h-full" x-data="{ serviceModal: false, serviceName: '', servicePrice: 0 }">

    {{-- ── LEFT: Product Search + Cart ── --}}
    <div class="flex-1 flex flex-col gap-4 min-w-0">

        {{-- Search bar --}}
        <div class="card">
            <div class="card-body py-3">
                <div class="flex gap-2 items-center">
                    <div class="flex-1 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input wire:model.live.debounce.200ms="productSearch"
                               type="text"
                               class="input pl-10 text-base"
                               placeholder="Buscar producto por nombre, SKU o código de barras..."
                               autofocus>
                        @if(count($searchResults))
                        <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl z-50 max-h-72 overflow-y-auto">
                            @foreach($searchResults as $product)
                            <button wire:click="addProduct({{ $product['id'] }})"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $product['name'] }}</p>
                                    <p class="text-xs text-gray-400">SKU: {{ $product['sku'] ?? '—' }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($product['sale_price'], 0, ',', '.') }}</p>
                                    @if($product['track_stock'])
                                        <p class="text-xs {{ $product['stock'] <= 0 ? 'text-red-500' : ($product['stock'] <= 5 ? 'text-orange-500' : 'text-gray-400') }}">
                                            Stock: {{ $product['stock'] }}
                                        </p>
                                    @endif
                                </div>
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Add service button --}}
                    <button @click="serviceModal = true" class="btn-secondary btn-sm whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Servicio
                    </button>
                </div>
            </div>
        </div>

        {{-- Cart --}}
        <div class="card flex-1 flex flex-col min-h-0">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                    Carrito
                    @if(count($cart))
                        <span class="ml-1.5 badge-blue text-xs">{{ array_sum(array_column($cart, 'quantity')) }} ítems</span>
                    @endif
                </h2>
                @if(count($cart))
                <button wire:click="clearCart" class="text-xs text-red-500 hover:text-red-700 font-medium">Limpiar</button>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto">
                @if(empty($cart))
                <div class="flex flex-col items-center justify-center h-full py-12 text-gray-300 dark:text-gray-600">
                    <svg class="w-16 h-16 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <p class="text-sm">El carrito está vacío</p>
                    <p class="text-xs mt-1">Busca un producto para agregar</p>
                </div>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($cart as $key => $item)
                    <div wire:key="cart-{{ $key }}" class="flex items-start gap-3 px-4 py-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $item['name'] }}</p>
                                    <p class="text-xs text-gray-400">
                                        ${{ number_format($item['price'], 0, ',', '.') }} / {{ $item['unit'] }}
                                        @if($item['item_type'] === 'service') <span class="badge-blue ml-1">servicio</span> @endif
                                    </p>
                                </div>
                                <button wire:click="removeItem('{{ $key }}')" class="text-red-400 hover:text-red-600 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    @if($item['quantity'] <= 1)
                                        <button wire:click="removeItem('{{ $key }}')"
                                                class="px-2.5 py-1 text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @else
                                        <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                                class="px-2.5 py-1 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-bold text-sm">−</button>
                                    @endif
                                    <span class="px-3 text-sm font-semibold text-gray-900 dark:text-gray-100 min-w-[2rem] text-center">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                            class="px-2.5 py-1 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-bold text-sm">+</button>
                                </div>
                                <span class="text-xs text-gray-400">Desc: $</span>
                                <input type="number" min="0"
                                       wire:change="updateItemDiscount('{{ $key }}', $event.target.value)"
                                       value="{{ $item['discount'] }}"
                                       class="w-20 input-sm text-center">
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                ${{ number_format($item['subtotal'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Cart totals --}}
            @if(count($cart))
            <div class="card-footer space-y-1.5">
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                @if($taxEnabled)
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ \App\Models\BusinessSetting::get('tax_name','IVA') }} ({{ $taxPercent }}%)</span>
                    <span>${{ number_format($taxAmount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm font-bold text-gray-900 dark:text-white pt-1 border-t border-gray-200 dark:border-gray-700">
                    <span>TOTAL</span>
                    <span class="text-lg text-indigo-600 dark:text-indigo-400">${{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── RIGHT: Customer + Payment ── --}}
    <div class="w-full lg:w-80 flex flex-col gap-4">

        {{-- Sale Type --}}
        <div class="card">
            <div class="card-body py-3">
                <label class="label">Tipo de venta</label>
                <div class="grid grid-cols-3 gap-1 mt-1">
                    @foreach(['product' => 'Repuesto', 'service' => 'Servicio', 'mixed' => 'Mixto'] as $val => $label)
                    <button wire:click="$set('saleType', '{{ $val }}')"
                            class="py-2 text-xs font-medium rounded-lg transition-all
                                   {{ $saleType === $val ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Discount + Notes --}}
        <div class="card">
            <div class="card-body py-3 space-y-3">
                <div>
                    <label class="label">Descuento global ($)</label>
                    <input wire:model.live="discount" type="number" min="0" class="input" placeholder="0">
                </div>
                <div>
                    <label class="label">Notas / observaciones</label>
                    <input wire:model="notes" type="text" class="input" placeholder="Opcional...">
                </div>
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="card">
            <div class="card-body py-3">
                <label class="label">Método de pago</label>
                <div class="grid grid-cols-2 gap-1 mt-1">
                    @foreach(['cash' => '💵 Efectivo', 'card' => '💳 Tarjeta', 'transfer' => '🏦 Transferencia', 'mixed' => '🔀 Mixto'] as $val => $label)
                    <button wire:click="$set('paymentMethod', '{{ $val }}')"
                            class="py-2 text-xs font-medium rounded-lg transition-all
                                   {{ $paymentMethod === $val ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Checkout button --}}
        <button wire:click="openPaymentModal"
                class="btn-primary btn-lg w-full {{ empty($cart) ? 'opacity-50 cursor-not-allowed' : '' }}"
                @if(empty($cart)) disabled @endif>
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Cobrar ${{ number_format($total, 0, ',', '.') }}
        </button>
    </div>

    {{-- ── PAYMENT MODAL ── --}}
    @if($showPaymentModal)
    <div class="modal-overlay" x-data>
        <div class="modal modal-sm p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Confirmar Pago</h3>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-medium">${{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                @if($discount > 0)
                <div class="flex justify-between text-sm text-emerald-600">
                    <span>Descuento</span>
                    <span>-${{ number_format($discount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($taxEnabled)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">IVA ({{ $taxPercent }}%)</span>
                    <span>${{ number_format($taxAmount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span>TOTAL</span>
                    <span class="text-indigo-600 dark:text-indigo-400">${{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($paymentMethod === 'cash')
            <div class="mb-4">
                <label class="label">Monto recibido</label>
                <input wire:model.live="amountPaid" type="number" min="{{ $total }}" step="0.01"
                       class="input text-xl font-bold text-center" placeholder="{{ number_format($total, 0) }}">
                @error('amountPaid') <p class="error-msg">{{ $message }}</p> @enderror

                @if($changeAmount > 0)
                <div class="mt-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-xl p-3 text-center">
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Cambio a devolver</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($changeAmount, 0, ',', '.') }}</p>
                </div>
                @endif
            </div>

            {{-- Quick cash buttons --}}
            <div class="grid grid-cols-4 gap-1.5 mb-4">
                @php $quickAmounts = [5000, 10000, 20000, 50000, 100000, 200000]; @endphp
                @foreach($quickAmounts as $amount)
                    @if($amount >= $total)
                    <button wire:click="$set('amountPaid', {{ $amount }})"
                            class="py-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-gray-700
                                   {{ $amountPaid == $amount ? 'bg-indigo-600 text-white border-indigo-600' : 'hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                        ${{ number_format($amount / 1000, 0) }}k
                    </button>
                    @endif
                @endforeach
            </div>
            @endif

            <div class="flex gap-2">
                <button wire:click="processPayment" class="btn-success flex-1" wire:loading.attr="disabled">
                    <span wire:loading.remove>Confirmar venta</span>
                    <span wire:loading>Procesando...</span>
                </button>
                <button wire:click="$set('showPaymentModal', false)" class="btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── SUCCESS MODAL ── --}}
    @if($showSuccessModal && $lastSaleId)
    @php $sale = \App\Models\Sale::with(['customer'])->find($lastSaleId); @endphp
    <div class="modal-overlay">
        <div class="modal modal-sm p-6 text-center" @click.stop>
            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">¡Venta completada!</h3>
            <p class="text-sm text-gray-500 mb-1">{{ $sale?->invoice_number }}</p>
            <p class="text-2xl font-bold text-emerald-600 mb-4">${{ number_format($sale?->total ?? 0, 0, ',', '.') }}</p>

            <div class="flex gap-2 justify-center flex-wrap">
                <a href="{{ route('sales.invoice', $lastSaleId) }}" target="_blank"
                   class="btn-secondary btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimir
                </a>
                <a href="{{ route('sales.invoice.pdf', $lastSaleId) }}" target="_blank"
                   class="btn-secondary btn-sm">PDF</a>
                <button wire:click="closeSuccessModal" class="btn-primary btn-sm">Nueva venta</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── SERVICE MODAL ── --}}
    <div x-show="serviceModal" class="modal-overlay" x-cloak>
        <div class="modal modal-sm p-6" @click.stop x-on:keydown.escape.window="serviceModal = false">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Agregar Servicio / Mano de Obra</h3>
            <div class="space-y-3">
                <div>
                    <label class="label">Descripción del servicio</label>
                    <input x-model="serviceName" type="text" class="input" placeholder="Ej: Cambio de aceite, Revisión frenos...">
                </div>
                <div>
                    <label class="label">Precio</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                        <input x-model="servicePrice" type="number" min="0" step="1000" class="input pl-7">
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button @click="$wire.addService(serviceName, parseFloat(servicePrice)); serviceModal = false; serviceName = ''; servicePrice = 0;" class="btn-primary flex-1">Agregar al carrito</button>
                <button @click="serviceModal = false" class="btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>
</div>
