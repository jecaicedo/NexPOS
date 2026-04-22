<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        $settings = $this->getSettings();

        return view('invoices.show', compact('sale', 'settings'));
    }

    public function pdf(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        $settings = $this->getSettings();

        $pdf = Pdf::loadView('invoices.pdf', compact('sale', 'settings'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("factura-{$sale->invoice_number}.pdf");
    }

    public function thermal(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        $settings = $this->getSettings();
        $width    = BusinessSetting::get('thermal_width', '80');

        $pdf = Pdf::loadView('invoices.thermal', compact('sale', 'settings'))
            ->setPaper([0, 0, ($width === '58' ? 164.409 : 226.772), 841.89], 'portrait');

        return $pdf->stream("tirilla-{$sale->invoice_number}.pdf");
    }

    private function getSettings(): array
    {
        $keys = ['business_name', 'business_address', 'business_phone', 'business_email',
                 'business_nit', 'business_city', 'currency_symbol', 'tax_name',
                 'invoice_footer', 'thermal_width'];

        return collect($keys)->mapWithKeys(fn($k) => [$k => BusinessSetting::get($k)])->all();
    }
}
