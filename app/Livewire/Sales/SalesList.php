<?php

namespace App\Livewire\Sales;

use App\Models\ActivityLog;
use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class SalesList extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $status        = '';
    public string $paymentMethod = '';
    public string $dateFrom      = '';
    public string $dateTo        = '';
    public string $sortField     = 'created_at';
    public string $sortDir       = 'desc';
    public int    $perPage       = 15;

    public bool    $showCancelModal = false;
    public ?int    $cancellingId    = null;
    public string  $cancelReason   = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo   = now()->toDateString();
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function sort(string $field): void
    {
        $this->sortDir   = ($this->sortField === $field && $this->sortDir === 'desc') ? 'asc' : 'desc';
        $this->sortField = $field;
    }

    public function confirmCancel(int $id): void
    {
        $this->cancellingId   = $id;
        $this->cancelReason   = '';
        $this->showCancelModal = true;
    }

    public function cancelSale(): void
    {
        $this->validate(['cancelReason' => 'required|string|min:5|max:500']);

        $sale = Sale::findOrFail($this->cancellingId);

        if ($sale->status === 'cancelled') {
            $this->dispatch('toast', type: 'warning', message: 'Esta venta ya fue anulada.');
            $this->showCancelModal = false;
            return;
        }

        // Restaurar stock
        foreach ($sale->items as $item) {
            if ($item->product_id && $item->product) {
                $product = $item->product;
                if ($product->track_stock) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }
        }

        $sale->update([
            'status'        => 'cancelled',
            'cancel_reason' => $this->cancelReason,
            'cancelled_at'  => now(),
            'cancelled_by'  => auth()->id(),
        ]);

        ActivityLog::record('sale_cancelled', "Venta {$sale->invoice_number} anulada. Motivo: {$this->cancelReason}", $sale);

        $this->showCancelModal = false;
        $this->cancellingId    = null;
        $this->dispatch('toast', type: 'success', message: 'Venta anulada. Stock restaurado.');
    }

    public function render()
    {
        // Filtros compartidos
        $filters = function ($q) {
            $q->when($this->search, fn($q) => $q->where(function ($q) {
                    $q->where('invoice_number', 'like', "%{$this->search}%")
                      ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                }))
              ->when($this->status, fn($q) => $q->where('status', $this->status))
              ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
              ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
              ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo));
        };

        // Consulta principal con paginación
        $sales = Sale::with(['customer', 'user'])
            ->tap($filters)
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        // Totales con consulta limpia (sin eager loading ni orderBy)
        $totals = Sale::where('status', 'completed')
            ->tap($filters)
            ->selectRaw('SUM(total) as total_revenue, COUNT(*) as total_count')
            ->first();

        return view('livewire.sales.sales-list', [
            'sales'        => $sales,
            'totalRevenue' => $totals->total_revenue ?? 0,
            'totalCount'   => $totals->total_count ?? 0,
        ]);
    }
}
