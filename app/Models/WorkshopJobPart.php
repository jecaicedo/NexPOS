<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopJobPart extends Model
{
    protected $fillable = [
        'workshop_job_id', 'product_id', 'name', 'price', 'quantity', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'price'    => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function workshopJob()
    {
        return $this->belongsTo(WorkshopJob::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
