<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private string $from,
        private string $to
    ) {}

    public function query()
    {
        return Sale::with(['customer', 'user'])
            ->whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to)
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return ['#', 'Factura', 'Fecha', 'Cliente', 'Método Pago', 'Estado', 'Subtotal', 'Descuento', 'IVA', 'Total', 'Cajero'];
    }

    public function map($sale): array
    {
        static $i = 1;
        return [
            $i++,
            $sale->invoice_number,
            $sale->created_at->format('d/m/Y H:i'),
            $sale->customer?->name ?? 'Consumidor Final',
            match($sale->payment_method) { 'cash'=>'Efectivo','card'=>'Tarjeta','transfer'=>'Transferencia','mixed'=>'Mixto',default=>$sale->payment_method },
            match($sale->status) { 'completed'=>'Completada','cancelled'=>'Anulada',default=>'Pendiente' },
            $sale->subtotal,
            $sale->discount,
            $sale->tax_amount,
            $sale->total,
            $sale->user?->name,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '6366F1']], 'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]],
        ];
    }
}
