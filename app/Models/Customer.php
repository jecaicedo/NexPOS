<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'document_type', 'document_number', 'email', 'phone',
        'address', 'city', 'is_generic', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_generic' => 'boolean',
            'is_active'  => 'boolean',
        ];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function workshopJobs()
    {
        return $this->hasMany(WorkshopJob::class);
    }

    public function getTotalPurchasesAttribute(): float
    {
        return $this->sales()->where('status', 'completed')->sum('total');
    }
}
