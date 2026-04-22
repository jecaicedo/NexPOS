@extends('layouts.app')
@section('title', 'Reportes y Estadísticas')

@section('content')
<div class="space-y-6">

    {{-- Filters --}}
    <form method="GET" action="{{ route('reports.index') }}" class="card">
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="label">Desde</label>
                    <input type="date" name="from" value="{{ $from }}" class="input w-36">
                </div>
                <div>
                    <label class="label">Hasta</label>
                    <input type="date" name="to" value="{{ $to }}" class="input w-36">
                </div>
                <div>
                    <label class="label">Agrupación</label>
                    <select name="period" class="input w-32">
                        <option value="daily" {{ $period==='daily' ? 'selected' : '' }}>Diaria</option>
                        <option value="weekly" {{ $period==='weekly' ? 'selected' : '' }}>Semanal</option>
                        <option value="monthly" {{ $period==='monthly' ? 'selected' : '' }}>Mensual</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Aplicar filtros</button>
                @can('export reports')
                <a href="{{ route('reports.export', ['from' => $from, 'to' => $to]) }}" class="btn-success">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar Excel
                </a>
                @endcan
            </div>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($summary->total_sales ?? 0) }}</div>
                <div class="stat-label">Ventas completadas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-value text-lg">${{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}</div>
                <div class="stat-label">Ingresos totales</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="stat-value text-lg">${{ number_format($summary->avg_sale ?? 0, 0, ',', '.') }}</div>
                <div class="stat-label">Ticket promedio</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div>
                <div class="stat-value text-lg">${{ number_format($summary->total_discount ?? 0, 0, ',', '.') }}</div>
                <div class="stat-label">Descuentos otorgados</div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="card lg:col-span-2">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Evolución de Ventas</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="120"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Por Método de Pago</h3>
            </div>
            <div class="card-body flex items-center justify-center">
                <canvas id="paymentChart" style="max-height:200px"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Top Products --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Top 10 Productos</h3>
            </div>
            <div class="table-wrapper rounded-none border-0">
                <table class="table">
                    <thead><tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Unidades</th>
                        <th>Ingresos</th>
                    </tr></thead>
                    <tbody>
                        @forelse($topProducts as $i => $prod)
                        <tr>
                            <td class="text-xs font-bold text-gray-400">{{ $i + 1 }}</td>
                            <td class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $prod->name }}</td>
                            <td class="text-xs text-gray-500">{{ number_format($prod->total_qty) }}</td>
                            <td class="text-xs font-semibold">${{ number_format($prod->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-6 text-xs text-gray-400">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Hourly pattern --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Patrón por Hora del Día</h3>
            </div>
            <div class="card-body">
                <canvas id="hourChart" height="160"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
$paymentLabels = $byPayment->map(function($p) {
    return match($p->payment_method) {
        'cash'     => 'Efectivo',
        'card'     => 'Tarjeta',
        'transfer' => 'Transferencia',
        'mixed'    => 'Mixto',
        default    => $p->payment_method,
    };
})->values();
@endphp
<script>
const isDark = document.documentElement.classList.contains('dark');
const grid   = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
const text   = isDark ? '#9ca3af' : '#6b7280';

// Sales chart
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: @json($salesByPeriod->pluck('period')),
        datasets: [{
            label: 'Ingresos',
            data: @json($salesByPeriod->pluck('total')),
            backgroundColor: 'rgba(99,102,241,0.7)',
            borderColor: '#6366f1',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: grid }, ticks: { color: text, font: { size: 10 }, maxRotation: 45 } },
            y: { grid: { color: grid }, ticks: { color: text, font: { size: 10 }, callback: v => '$' + Intl.NumberFormat('es-CO').format(v) } }
        }
    }
});

// Payment chart
new Chart(document.getElementById('paymentChart'), {
    type: 'doughnut',
    data: {
        labels: @json($paymentLabels),
        datasets: [{
            data: @json($byPayment->pluck('total')),
            backgroundColor: ['#6366f1','#22c55e','#3b82f6','#f59e0b'],
            borderWidth: 0,
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: text, font: { size: 11 } } } } }
});

// Hour chart
new Chart(document.getElementById('hourChart'), {
    type: 'line',
    data: {
        labels: @json($hourLabels),
        datasets: [{
            label: 'Ventas',
            data: @json($hourData),
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34,197,94,0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: grid }, ticks: { color: text, font: { size: 9 }, maxRotation: 45 } },
            y: { grid: { color: grid }, ticks: { color: text, font: { size: 9 }, callback: v => '$' + Intl.NumberFormat('es-CO').format(v) } }
        }
    }
});
</script>
@endpush
