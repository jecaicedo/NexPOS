@extends('layouts.app')
@section('title', 'Venta ' . $sale->invoice_number)
@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('sales.index') }}" class="btn-secondary btn-sm">← Volver</a>
        <h1 class="page-title">Venta {{ $sale->invoice_number }}</h1>
        <span class="{{ match($sale->status) { 'completed'=>'badge-green','cancelled'=>'badge-red',default=>'badge-yellow' } }}">
            {{ match($sale->status) { 'completed'=>'Completada','cancelled'=>'Anulada',default=>'Pendiente' } }}
        </span>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-1">Cliente</p>
            <p class="font-semibold text-sm">{{ $sale->customer?->name ?? 'Consumidor Final' }}</p>
            @if($sale->customer?->document_number)
            <p class="text-xs text-gray-400">{{ $sale->customer->document_type }}: {{ $sale->customer->document_number }}</p>
            @endif
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-1">Pago</p>
            <p class="font-semibold text-sm">{{ match($sale->payment_method) { 'cash'=>'Efectivo','card'=>'Tarjeta','transfer'=>'Transferencia','mixed'=>'Mixto',default=>$sale->payment_method } }}</p>
            <p class="text-xs text-gray-400">{{ $sale->created_at->format('d/m/Y H:i') }} &bull; {{ $sale->user?->name }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="table-wrapper rounded-2xl">
            <table class="table">
                <thead><tr><th>Ítem</th><th>Cant.</th><th>Precio Unit.</th><th>Desc.</th><th>Subtotal</th></tr></thead>
                <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>
                        <p class="text-xs font-medium">{{ $item->name }}</p>
                        <p class="text-[11px] text-gray-400">{{ $item->item_type === 'service' ? 'Servicio' : 'Producto' }}</p>
                    </td>
                    <td class="text-xs">{{ $item->quantity }}</td>
                    <td class="text-xs">${{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-xs text-gray-400">{{ $item->discount > 0 ? '$'.number_format($item->discount,0,',','.') : '—' }}</td>
                    <td class="text-xs font-semibold">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="flex justify-end">
                <div class="w-56 space-y-1 text-xs">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>${{ number_format($sale->subtotal,0,',','.') }}</span></div>
                    @if($sale->discount > 0)
                    <div class="flex justify-between text-emerald-600"><span>Descuento</span><span>-${{ number_format($sale->discount,0,',','.') }}</span></div>
                    @endif
                    @if($sale->tax_amount > 0)
                    <div class="flex justify-between"><span>IVA ({{ $sale->tax_percent }}%)</span><span>${{ number_format($sale->tax_amount,0,',','.') }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-sm pt-1 border-t border-gray-200 dark:border-gray-700">
                        <span>TOTAL</span>
                        <span class="text-indigo-600 dark:text-indigo-400">${{ number_format($sale->total,0,',','.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('sales.invoice', $sale) }}" target="_blank" class="btn-secondary btn-sm">🖨 Imprimir factura</a>
        <a href="{{ route('sales.invoice.pdf', $sale) }}" target="_blank" class="btn-secondary btn-sm">📄 PDF</a>
        <a href="{{ route('sales.invoice.thermal', $sale) }}" target="_blank" class="btn-secondary btn-sm">🧾 Tirilla</a>
    </div>
</div>
@endsection
