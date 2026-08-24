<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?int $productId = null;

    public string  $name           = '';
    public string  $sku            = '';
    public string  $barcode        = '';
    public string  $description    = '';
    public ?int    $category_id    = null;
    public float   $purchase_price = 0;
    public float   $sale_price     = 0;
    public int     $stock          = 0;
    public int     $min_stock      = 5;
    public string  $unit           = 'unidad';
    public bool    $is_active      = true;
    public bool    $track_stock    = true;

    public $photo = null;
    public ?string $existingImage = null;

    public function mount(?int $productId = null): void
    {
        $this->productId = $productId;

        if ($productId) {
            $product = Product::findOrFail($productId);
            $this->name           = $product->name;
            $this->sku            = $product->sku ?? '';
            $this->barcode        = $product->barcode ?? '';
            $this->description    = $product->description ?? '';
            $this->category_id    = $product->category_id;
            $this->purchase_price = (float) $product->purchase_price;
            $this->sale_price     = (float) $product->sale_price;
            $this->stock          = $product->stock;
            $this->min_stock      = $product->min_stock;
            $this->unit           = $product->unit;
            $this->is_active      = $product->is_active;
            $this->track_stock    = $product->track_stock;
            $this->existingImage  = $product->image;
        }
    }

    public function removePhoto(): void
    {
        $this->photo = null;
        $this->existingImage = null;
    }

    public function save(): void
    {
        $this->validate([
            'name'           => 'required|string|max:200',
            'sku'            => 'nullable|string|max:100|unique:products,sku,' . ($this->productId ?? 'NULL'),
            'barcode'        => 'nullable|string|max:100',
            'category_id'    => 'nullable|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price'     => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'min_stock'      => 'required|integer|min:0',
            'unit'           => 'required|string|max:50',
            'photo'          => 'nullable|image|max:2048',
        ]);

        $data = [
            'name'           => $this->name,
            'sku'            => $this->sku ?: null,
            'barcode'        => $this->barcode ?: null,
            'description'    => $this->description,
            'category_id'    => $this->category_id,
            'purchase_price' => $this->purchase_price,
            'sale_price'     => $this->sale_price,
            'stock'          => $this->stock,
            'min_stock'      => $this->min_stock,
            'unit'           => $this->unit,
            'is_active'      => $this->is_active,
            'track_stock'    => $this->track_stock,
        ];

        $previousImage = $this->productId ? Product::findOrFail($this->productId)->image : null;

        if ($this->photo) {
            $data['image'] = $this->photo->store('products', 'public');
        } elseif ($this->productId && $this->existingImage === null) {
            $data['image'] = null;
        }

        if ($this->productId) {
            Product::findOrFail($this->productId)->update($data);
        } else {
            Product::create($data);
        }

        if ($previousImage && isset($data['image']) && $data['image'] !== $previousImage) {
            Storage::disk('public')->delete($previousImage);
        }

        $this->dispatch('product-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.products.product-form', [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
