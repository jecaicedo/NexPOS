<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'sku', 'barcode', 'description', 'image',
        'purchase_price', 'sale_price', 'stock', 'min_stock',
        'unit', 'is_active', 'track_stock',
    ];

    protected function casts(): array
    {
        return [
            'is_active'      => 'boolean',
            'track_stock'    => 'boolean',
            'purchase_price' => 'decimal:2',
            'sale_price'     => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function workshopJobParts()
    {
        return $this->hasMany(WorkshopJobPart::class);
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock <= $this->min_stock;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/no-product.svg');
    }
}
