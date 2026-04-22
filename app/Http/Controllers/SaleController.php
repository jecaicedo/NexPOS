<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class SaleController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        return view('sales.show', compact('sale'));
    }
}
