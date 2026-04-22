@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="stat-card col-span-1">
            <div class="stat-icon bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['sales_today']) }}</div>
                <div class="stat-label">Ventas hoy</div>
            </div>
        </div>

        <div class="stat-card col-span-1">
            <div class="stat-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-value text-lg">{{ number_format($stats['revenue_today'], 0, ',', '.') }}</div>
                <div class="stat-label">Ingresos hoy</div>
            </div>
        </div>

        <div class="stat-card col-span-1">
            <div class="stat-icon bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <div class="stat-value text-lg">{{ number_format($stats['revenue_month'], 0, ',', '.') }}</div>
                <div class="stat-label">Ingresos mes</div>
            </div>
        </div>

        <div class="stat-card col-span-1">
            <div class="stat-icon bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['low_stock_products'] }}</div>
                <div class="stat-label">Stock bajo</div>
            </div>
        </div>

        <div class="stat-card col-span-1">
            <div class="stat-icon bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['active_customers'] }}</div>
                <div class="stat-label">Clientes</div>
            </div>
        </div>

        <div class="stat-card col-span-1">
            <div class="stat-icon bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['workshop_pending'] }}</div>
                <div class="stat-label">Taller activo</div>
            </div>
        </div>
    </div>

    {{-- Chart + Top Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="card lg:col-span-2">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Ventas — últimos 30 días</h2>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Top productos (mes)</h2>
            </div>
            <div class="card-body p-0">
                @forelse($topProducts as $i => $product)
                <div class="flex items-center gap-3 px-5 py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                    <span class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-xs font-bold rounded-full
                        {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' : 'bg-gray-50 dark:bg-gray-800 text-gray-500') }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $product->total_qty }} uds</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                        ${{ number_format($product->total_revenue, 0, ',', '.') }}
                    </span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-xs text-gray-400">Sin ventas este mes</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Sales + Low Stock --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Últimas ventas</h2>
                <a href="{{ route('sales.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Ver todas</a>
            </div>
            <div class="table-wrapper rounded-none border-0">
                <table class="table">
                    <thead><tr>
                        <th>Factura</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr></thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                        <tr>
                            <td><a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 dark:text-indigo-400 font-mono text-xs hover:underline">{{ $sale->invoice_number }}</a></td>
                            <td class="text-xs">{{ $sale->customer?->name ?? 'Consumidor Final' }}</td>
                            <td class="text-xs font-semibold">${{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td>
                                <span class="{{ $sale->status === 'completed' ? 'badge-green' : ($sale->status === 'cancelled' ? 'badge-red' : 'badge-yellow') }}">
                                    {{ $sale->status === 'completed' ? 'Completada' : ($sale->status === 'cancelled' ? 'Anulada' : 'Pendiente') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-gray-400 py-6 text-xs">Sin ventas registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Productos con stock bajo</h2>
                <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Ver todos</a>
            </div>
            <div class="table-wrapper rounded-none border-0">
                <table class="table">
                    <thead><tr>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Mínimo</th>
                    </tr></thead>
                    <tbody>
                        @forelse($lowStockProducts as $product)
                        <tr>
                            <td>
                                <a href="{{ route('products.index') }}" class="text-xs font-medium text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $product->name }}</a>
                                @if($product->category)
                                    <span class="badge-gray text-[10px] ml-1">{{ $product->category->name }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $product->stock === 0 ? 'badge-red' : 'badge-yellow' }} font-mono">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="text-xs text-gray-400">{{ $product->min_stock }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-gray-400 py-6 text-xs">¡Todo el inventario está en orden!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('salesChart');
if (ctx) {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Ventas ($)',
                data: @json($chartData),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 2,
                pointHoverRadius: 5,
                pointBackgroundColor: '#6366f1',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 }, maxRotation: 0 } },
                y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 }, callback: v => '$' + Intl.NumberFormat('es-CO').format(v) } }
            }
        }
    });
}
</script>
@endpush
