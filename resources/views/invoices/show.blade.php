<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $sale->invoice_number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #1f2937; background: #f9fafb; }
        .page { max-width: 800px; margin: 20px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #6366f1; padding-bottom: 20px; margin-bottom: 24px; }
        .business-name { font-size: 22px; font-weight: bold; color: #4f46e5; }
        .business-info { font-size: 11px; color: #6b7280; line-height: 1.6; }
        .invoice-title { text-align: right; }
        .invoice-number { font-size: 20px; font-weight: bold; color: #1f2937; font-family: monospace; }
        .invoice-meta { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
        .info-box { background: #f3f4f6; border-radius: 8px; padding: 14px; }
        .info-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #6366f1; letter-spacing: 0.05em; margin-bottom: 8px; }
        .info-row { font-size: 12px; color: #374151; margin-bottom: 4px; }
        .info-label { color: #9ca3af; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th { background: #6366f1; color: white; padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        thead th:last-child { text-align: right; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        tbody td:last-child { text-align: right; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .totals { margin-left: auto; width: 280px; }
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; border-bottom: 1px solid #e5e7eb; }
        .total-row:last-child { font-size: 16px; font-weight: bold; color: #4f46e5; border-bottom: none; padding-top: 10px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .footer { text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 11px; }
        .print-actions { max-width: 800px; margin: 10px auto; text-align: right; }
        .btn { display: inline-block; padding: 8px 18px; margin-left: 8px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .btn-print { background: #6366f1; color: white; border: none; }
        .btn-pdf { background: #ef4444; color: white; border: none; }
        .btn-back { background: #e5e7eb; color: #374151; border: none; }
        @media print { .print-actions { display: none; } body { background: white; } .page { box-shadow: none; margin: 0; border-radius: 0; } }
    </style>
</head>
<body>
<div class="print-actions">
    <button onclick="window.print()" class="btn btn-print">🖨 Imprimir</button>
    <a href="{{ route('sales.invoice.pdf', $sale) }}" target="_blank" class="btn btn-pdf">📄 PDF</a>
    <a href="{{ route('sales.invoice.thermal', $sale) }}" target="_blank" class="btn btn-back">🧾 Tirilla</a>
    <a href="{{ route('sales.index') }}" class="btn btn-back">← Volver</a>
</div>

<div class="page">
    <div class="header">
        <div>
            <div class="business-name">{{ $settings['business_name'] }}</div>
            <div class="business-info">
                {{ $settings['business_nit'] ? 'NIT: '.$settings['business_nit'] : '' }}<br>
                {{ $settings['business_address'] }}, {{ $settings['business_city'] }}<br>
                Tel: {{ $settings['business_phone'] }}
                @if($settings['business_email']) &nbsp;|&nbsp; {{ $settings['business_email'] }} @endif
            </div>
        </div>
        <div class="invoice-title">
            <div style="font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.1em;">Factura de Venta</div>
            <div class="invoice-number">{{ $sale->invoice_number }}</div>
            <div class="invoice-meta">{{ $sale->created_at->format('d/m/Y H:i') }}</div>
            <span class="status-badge {{ $sale->status === 'completed' ? 'status-completed' : 'status-cancelled' }}">
                {{ $sale->status === 'completed' ? 'COMPLETADA' : 'ANULADA' }}
            </span>
        </div>
    </div>

    <div class="section-grid">
        <div class="info-box">
            <div class="info-title">Cliente</div>
            <div class="info-row"><strong>{{ $sale->customer?->name ?? 'Consumidor Final' }}</strong></div>
            @if($sale->customer && $sale->customer->document_number)
            <div class="info-row"><span class="info-label">{{ $sale->customer->document_type }}:</span> {{ $sale->customer->document_number }}</div>
            @endif
            @if($sale->customer?->phone)
            <div class="info-row"><span class="info-label">Tel:</span> {{ $sale->customer->phone }}</div>
            @endif
            @if($sale->customer?->address)
            <div class="info-row">{{ $sale->customer->address }}</div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-title">Detalle de pago</div>
            <div class="info-row"><span class="info-label">Método:</span>
                {{ match($sale->payment_method) { 'cash'=>'Efectivo','card'=>'Tarjeta','transfer'=>'Transferencia','mixed'=>'Mixto',default=>$sale->payment_method } }}
            </div>
            <div class="info-row"><span class="info-label">Cajero:</span> {{ $sale->user?->name }}</div>
            @if($sale->change_amount > 0)
            <div class="info-row"><span class="info-label">Pagó:</span> ${{ number_format($sale->amount_paid, 0, ',', '.') }}</div>
            <div class="info-row"><span class="info-label">Cambio:</span> ${{ number_format($sale->change_amount, 0, ',', '.') }}</div>
            @endif
            @if($sale->notes)
            <div class="info-row"><span class="info-label">Nota:</span> {{ $sale->notes }}</div>
            @endif
        </div>
    </div>

    <table>
        <thead><tr>
            <th style="width:40%">Descripción</th>
            <th>Cant.</th>
            <th>P. Unitario</th>
            <th>Descuento</th>
            <th>Subtotal</th>
        </tr></thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->name }}<br><small style="color:#9ca3af">{{ $item->item_type === 'service' ? 'Servicio' : 'Producto' }}</small></td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 0, ',', '.') }}</td>
                <td>{{ $item->discount > 0 ? '$'.number_format($item->discount, 0, ',', '.') : '—' }}</td>
                <td>${{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row"><span>Subtotal</span><span>${{ number_format($sale->subtotal, 0, ',', '.') }}</span></div>
        @if($sale->discount > 0)
        <div class="total-row" style="color:#059669"><span>Descuento</span><span>-${{ number_format($sale->discount, 0, ',', '.') }}</span></div>
        @endif
        @if($sale->tax_amount > 0)
        <div class="total-row"><span>{{ $settings['tax_name'] }} ({{ $sale->tax_percent }}%)</span><span>${{ number_format($sale->tax_amount, 0, ',', '.') }}</span></div>
        @endif
        <div class="total-row"><span>TOTAL</span><span>{{ $settings['currency_symbol'] }}{{ number_format($sale->total, 0, ',', '.') }}</span></div>
    </div>

    @if($sale->cancel_reason)
    <div style="background:#fee2e2; border-radius:8px; padding:12px; margin-top:16px;">
        <strong style="color:#991b1b;">Motivo de anulación:</strong> {{ $sale->cancel_reason }}
    </div>
    @endif

    <div class="footer">{{ $settings['invoice_footer'] }}</div>
</div>
</body>
</html>
