<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $category  = '';
    public string $filter    = '';
    public string $sortField = 'name';
    public string $sortDir   = 'asc';
    public int    $perPage   = 15;

    public bool $showForm        = false;
    public bool $showStockModal  = false;
    public bool $showDeleteModal = false;
    public ?int $editingId       = null;
    public ?int $deletingId      = null;

    public ?int   $stockProductId = null;
    public string $stockType      = 'in';
    public int    $stockQty       = 1;
    public string $stockNotes     = '';

    public function mount(): void
    {
        $this->filter = request('filter', '');
    }

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingCategory(): void { $this->resetPage(); }
    public function updatingFilter(): void   { $this->resetPage(); }

    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir   = 'asc';
        }
    }

    public function createProduct(): void
    {
        $this->editingId = null;
        $this->showForm  = true;
    }

    public function editProduct(int $id): void
    {
        $this->editingId = $id;
        $this->showForm  = true;
    }

    #[On('product-saved')]
    public function onProductSaved(): void
    {
        $this->showForm = false;
        $this->dispatch('toast', type: 'success', message: 'Producto guardado correctamente.');
    }

    #[On('close-modal')]
    public function onCloseModal(): void
    {
        $this->showForm = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function deleteProduct(): void
    {
        Product::findOrFail($this->deletingId)->delete();
        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->dispatch('toast', type: 'success', message: 'Producto eliminado.');
    }

    public function openStockModal(int $id): void
    {
        $this->stockProductId = $id;
        $this->stockType      = 'in';
        $this->stockQty       = 1;
        $this->stockNotes     = '';
        $this->showStockModal = true;
    }

    public function adjustStock(): void
    {
        $this->validate([
            'stockQty'   => 'required|integer|min:1',
            'stockType'  => 'required|in:in,out,adjustment',
            'stockNotes' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($this->stockProductId);
        $before  = $product->stock;

        match($this->stockType) {
            'in'         => $product->stock += $this->stockQty,
            'out'        => $product->stock  = max(0, $product->stock - $this->stockQty),
            'adjustment' => $product->stock  = $this->stockQty,
        };

        $product->save();

        StockMovement::create([
            'product_id'   => $product->id,
            'user_id'      => auth()->id(),
            'type'         => $this->stockType,
            'quantity'     => $this->stockQty,
            'stock_before' => $before,
            'stock_after'  => $product->stock,
            'notes'        => $this->stockNotes,
        ]);

        $this->showStockModal = false;
        $this->dispatch('toast', type: 'success', message: 'Stock actualizado.');
    }

    public function render()
    {
        $query = Product::with('category')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%")
                  ->orWhere('barcode', 'like', "%{$this->search}%");
            }))
            ->when($this->category, fn($q) => $q->where('category_id', $this->category))
            ->when($this->filter === 'low_stock', fn($q) => $q->where('track_stock', true)->whereRaw('stock <= min_stock')->where('is_active', true))
            ->when($this->filter === 'inactive', fn($q) => $q->where('is_active', false))
            ->when(in_array($this->filter, ['', 'low_stock']), fn($q) => $q->where('is_active', true))
            ->orderBy($this->sortField, $this->sortDir);

        return view('livewire.products.product-list', [
            'products'   => $query->paginate($this->perPage),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'totalCount' => Product::where('is_active', true)->count(),
            'lowCount'   => Product::where('track_stock', true)->whereRaw('stock <= min_stock')->where('is_active', true)->count(),
            'stockProduct' => $this->stockProductId ? Product::find($this->stockProductId) : null,
        ]);
    }
}
