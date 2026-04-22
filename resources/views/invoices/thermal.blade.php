<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Tirilla {{ $sale->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Courier New", Courier, monospace; font-size: 10px; color: #000; width: 100%; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .big { font-size: 13px; }
        .small { font-size: 9px; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .row { display: flex; justify-content: space-between; margin: 2px 0; }
        .row-3 { display: flex; gap: 4px; }
        .row-3 .desc { flex: 1; }
        .row-3 .qty { width: 30px; text-align: center; }
        .row-3 .price { width: 60px; text-align: right; }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; }
        .space { margin: 3px 0; }
    </style>
</head>
<body>
<div class="center bold big">{{ $settings['business_name'] }}</div>
<div class="center small">{{ $settings['business_nit'] ? 'NIT: '.$settings['business_nit'] : '' }}</div>
<div class="center small">{{ $settings['business_address'] }}</div>
<div class="center small">Tel: {{ $settings['business_phone'] }}</div>

<div class="line"></div>

<div class="center bold">FACTURA DE VENTA</div>
<div class="row small">
    <span>No: <strong>{{ $sale->invoice_number }}</strong></span>
    <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
</div>
<div class="small">Cajero: {{ $sale->user?->name }}</div>

<div class="line"></div>

<div class="small bold">
    <span>Cliente: {{ $sale->customer?->name ?? 'Consumidor Final' }}</span>
</div>
@if($sale->customer?->document_number)
<div class="small">{{ $sale->customer->document_type }}: {{ $sale->customer->document_number }}</div>
@endif

<div class="line"></div>

<div class="row-3 bold small" style="border-bottom: 1px solid #000; padding-bottom: 2px;">
    <span class="desc">DESCRIPCION</span>
    <span class="qty">QTY</span>
    <span class="price">TOTAL</span>
</div>

@foreach($sale->items as $item)
<div class="space">
    <div class="small">{{ $item->name }}</div>
    <div class="row-3 small">
        <span class="desc">${{ number_format($item->price, 0, ',', '.') }} x</span>
        <span class="qty">{{ $item->quantity }}</span>
        <span class="price">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
    </div>
</div>
@endforeach

<div class="line"></div>

@if($sale->discount > 0)
<div class="row small"><span>Descuento:</span><span>-${{ number_format($sale->discount, 0, ',', '.') }}</span></div>
@endif
@if($sale->tax_amount > 0)
<div class="row small"><span>{{ $settings['tax_name'] }} ({{ $sale->tax_percent }}%):</span><span>${{ number_format($sale->tax_amount, 0, ',', '.') }}</span></div>
@endif

<div class="line"></div>
<div class="total-row">
    <span>TOTAL:</span>
    <span>{{ $settings['currency_symbol'] }}{{ number_format($sale->total, 0, ',', '.') }}</span>
</div>

@if($sale->payment_method === 'cash')
<div class="row small"><span>Efectivo:</span><span>${{ number_format($sale->amount_paid, 0, ',', '.') }}</span></div>
<div class="row small bold"><span>Cambio:</span><span>${{ number_format($sale->change_amount, 0, ',', '.') }}</span></div>
@else
<div class="row small"><span>Método:</span>
    <span>{{ match($sale->payment_method) { 'card'=>'Tarjeta','transfer'=>'Transferencia','mixed'=>'Mixto',default=>$sale->payment_method } }}</span>
</div>
@endif

<div class="line"></div>
<div class="center small">{{ $settings['invoice_footer'] }}</div>
<div class="center small">{{ now()->format('d/m/Y H:i:s') }}</div>
<br><br>
</body>
</html>
