<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $sale->invoice_number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #6366f1; padding-bottom: 16px; margin-bottom: 16px; }
        .business-name { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .business-info { font-size: 10px; color: #6b7280; line-height: 1.7; }
        .invoice-title { text-align: right; }
        .invoice-number { font-size: 16px; font-weight: bold; font-family: monospace; }
        .info-grid { display: flex; gap: 16px; margin-bottom: 16px; }
        .info-box { flex: 1; background: #f3f4f6; border-radius: 6px; padding: 10px; }
        .info-title { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #6366f1; margin-bottom: 6px; }
        .info-row { font-size: 11px; color: #374151; margin-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #6366f1; color: white; padding: 8px 10px; text-align: left; font-size: 10px; }
        th:last-child { text-align: right; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        td:last-child { text-align: right; font-weight: bold; }
        tr:nth-child(even) td { background: #f9fafb; }
        .totals-table { width: 240px; margin-left: auto; }
        .totals-table td { padding: 5px 8px; border: none; }
        .totals-table .total-final td { font-size: 14px; font-weight: bold; color: #4f46e5; border-top: 2px solid #6366f1; }
        .footer { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 10px; }
    </style>
</head>
<body>
<div class="header">
    <div>
        <div class="business-name">{{ $settings['business_name'] }}</div>
        <div class="business-info">
            @if($settings['business_nit']) NIT: {{ $settings['business_nit'] }}<br> @endif
            {{ $settings['business_address'] }}, {{ $settings['business_city'] }}<br>
            Tel: {{ $settings['business_phone'] }}
        </div>
    </div>
    <div class="invoice-title">
        <div style="font-size:9px; color:#9ca3af; text-transform:uppercase;">Factura de Venta</div>
        <div class="invoice-number">{{ $sale->invoice_number }}</div>
        <div style="font-size:10px; color:#6b7280; margin-top:3px;">{{ $sale->created_at->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="info-grid">
    <div class="info-box">
        <div class="info-title">Cliente</div>
        <div class="info-row"><strong>{{ $sale->customer?->name ?? 'Consumidor Final' }}</strong></div>
        @if($sale->customer?->document_number)
        <div class="info-row">{{ $sale->customer->document_type }}: {{ $sale->customer->document_number }}</div>
        @endif
    </div>
    <div class="info-box">
        <div class="info-title">Pago</div>
        <div class="info-row">
            {{ match($sale->payment_method) { 'cash'=>'Efectivo','card'=>'Tarjeta','transfer'=>'Transferencia','mixed'=>'Mixto',default=>$sale->payment_method } }}
        </div>
        <div class="info-row">Cajero: {{ $sale->user?->name }}</div>
    </div>
</div>

<table>
    <thead><tr>
        <th>Descripción</th>
        <th style="text-align:center">Cant.</th>
        <th style="text-align:right">P. Unit.</th>
        <th style="text-align:right">Subtotal</th>
    </tr></thead>
    <tbody>
        @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td style="text-align:center">{{ $item->quantity }}</td>
            <td style="text-align:right">${{ number_format($item->price, 0, ',', '.') }}</td>
            <td>${{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals-table">
    <tr><td>Subtotal</td><td style="text-align:right">${{ number_format($sale->subtotal, 0, ',', '.') }}</td></tr>
    @if($sale->discount > 0)
    <tr><td>Descuento</td><td style="text-align:right;color:#059669">-${{ number_format($sale->discount, 0, ',', '.') }}</td></tr>
    @endif
    @if($sale->tax_amount > 0)
    <tr><td>{{ $settings['tax_name'] }} ({{ $sale->tax_percent }}%)</td><td style="text-align:right">${{ number_format($sale->tax_amount, 0, ',', '.') }}</td></tr>
    @endif
    <tr class="total-final"><td><strong>TOTAL</strong></td><td style="text-align:right"><strong>{{ $settings['currency_symbol'] }}{{ number_format($sale->total, 0, ',', '.') }}</strong></td></tr>
</table>

<div class="footer">{{ $settings['invoice_footer'] }}</div>
</body>
</html>
