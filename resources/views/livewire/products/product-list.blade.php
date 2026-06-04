<div>
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Inventario de Productos</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ $totalCount }} productos activos
                @if($lowCount > 0)
                    &bull; <span class="text-orange-600 dark:text-orange-400 font-medium">{{ $lowCount }} con stock bajo</span>
                @endif
            </p>
        </div>
        @can('create products')
        <button wire:click="createProduct" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Producto
        </button>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-48 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre, SKU o código..." class="input pl-9">
                </div>
                <select wire:model.live="category" class="input w-44">
                    <option value="">Todas las categorías</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filter" class="input w-40">
                    <option value="">Todos</option>
                    <option value="low_stock">Stock bajo</option>
                    <option value="inactive">Inactivos</option>
                </select>
                <select wire:model.live="perPage" class="input w-20">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-wrapper rounded-2xl">
            <table class="table">
                <thead><tr>
                    <th wire:click="sort('name')" class="cursor-pointer select-none">
                        <div class="flex items-center gap-1">Producto @if($sortField==='name')<span class="text-indigo-500">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</div>
                    </th>
                    <th>Categoría</th>
                    <th>SKU</th>
                    <th wire:click="sort('sale_price')" class="cursor-pointer select-none">
                        <div class="flex items-center gap-1">P. Venta @if($sortField==='sale_price')<span class="text-indigo-500">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</div>
                    </th>
                    <th wire:click="sort('purchase_price')" class="cursor-pointer select-none">
                        <div class="flex items-center gap-1">P. Compra @if($sortField==='purchase_price')<span class="text-indigo-500">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</div>
                    </th>
                    <th wire:click="sort('stock')" class="cursor-pointer select-none">
                        <div class="flex items-center gap-1">Stock @if($sortField==='stock')<span class="text-indigo-500">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</div>
                    </th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr></thead>
                <tbody>
                @forelse($products as $product)
                <tr wire:key="prod-{{ $product->id }}">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-xs leading-tight">{{ $product->name }}</p>
                                @if($product->barcode)
                                    <p class="text-[11px] text-gray-400 font-mono">{{ $product->barcode }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($product->category)
                            <span class="badge-gray text-xs gap-1.5">
                                <span class="w-2 h-2 rounded-full inline-block" style="background:{{ $product->category->color }}"></span>
                                {{ $product->category->name }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $product->sku ?? '—' }}</td>
                    <td class="font-semibold text-xs text-gray-900 dark:text-gray-100">${{ number_format($product->sale_price,0,',','.') }}</td>
                    <td class="text-xs text-gray-500 dark:text-gray-400">${{ number_format($product->purchase_price,0,',','.') }}</td>
                    <td>
                        @if(!$product->track_stock)
                            <span class="badge-gray text-xs">Sin control</span>
                        @elseif($product->stock === 0)
                            <span class="badge-red font-mono font-bold text-xs">AGOTADO</span>
                        @elseif($product->isLowStock())
                            <span class="badge-yellow font-mono text-xs" title="Stock mínimo: {{ $product->min_stock }}">{{ $product->stock }}</span>
                        @else
                            <span class="badge-green font-mono text-xs">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="{{ $product->is_active ? 'badge-green' : 'badge-red' }} text-xs">
                            {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            @can('manage stock')
                            <button wire:click="openStockModal({{ $product->id }})" title="Ajustar stock"
                                    class="btn-icon btn-sm text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </button>
                            @endcan
                            @can('edit products')
                            <button wire:click="editProduct({{ $product->id }})" title="Editar"
                                    class="btn-icon btn-sm text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endcan
                            @can('delete products')
                            <button wire:click="confirmDelete({{ $product->id }})" title="Eliminar"
                                    class="btn-icon btn-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-sm text-gray-400">No se encontraron productos</p>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="card-footer">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    {{-- Product Form Modal --}}
    @if($showForm)
    <div class="modal-overlay" x-data x-on:keydown.escape.window="$wire.onCloseModal()">
        <div class="modal modal-lg" @click.stop>
            @livewire('products.product-form', ['productId' => $editingId], key('pf-'.($editingId ?? 'new')))
        </div>
    </div>
    @endif

    {{-- Stock Modal --}}
    @if($showStockModal && $stockProduct)
    <div class="modal-overlay" x-data>
        <div class="modal modal-sm p-6" @click.stop>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Ajustar Stock</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                {{ $stockProduct->name }} — Stock actual: <strong class="text-gray-900 dark:text-white">{{ $stockProduct->stock }}</strong>
            </p>
            <div class="space-y-3">
                <div>
                    <label class="label">Tipo de movimiento</label>
                    <select wire:model="stockType" class="input">
                        <option value="in">Entrada (sumar al stock)</option>
                        <option value="out">Salida (restar del stock)</option>
                        <option value="adjustment">Ajuste (establecer cantidad)</option>
                    </select>
                </div>
                <div>
                    <label class="label">Cantidad</label>
                    <input wire:model="stockQty" type="number" min="1" class="input">
                    @error('stockQty') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Nota (opcional)</label>
                    <input wire:model="stockNotes" type="text" class="input" placeholder="Motivo del ajuste...">
                </div>
            </div>
            <div class="flex gap-2 mt-5">
                <button wire:click="adjustStock" class="btn-primary flex-1">Aplicar</button>
                <button wire:click="$set('showStockModal', false)" class="btn-secondary flex-1">Cancelar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if($showDeleteModal)
    <div class="modal-overlay">
        <div class="modal modal-sm p-6" @click.stop>
            <div class="text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">¿Eliminar producto?</h3>
                <p class="text-xs text-gray-500 mb-5">El producto será eliminado (soft delete). Se puede restaurar si es necesario.</p>
                <div class="flex gap-2">
                    <button wire:click="deleteProduct" class="btn-danger flex-1">Sí, eliminar</button>
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary flex-1">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
