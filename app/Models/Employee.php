<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'document_number', 'phone', 'email',
        'specialty', 'labor_rate', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'labor_rate' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workshopJobs()
    {
        return $this->hasMany(WorkshopJob::class);
    }

    public function getCompletedJobsCountAttribute(): int
    {
        return $this->workshopJobs()->where('status', 'completed')->count();
    }
}
