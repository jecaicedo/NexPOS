<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_number', 'customer_id', 'employee_id', 'user_id',
        'vehicle_brand', 'vehicle_model', 'vehicle_plate', 'vehicle_year',
        'description', 'diagnosis', 'status', 'labor_cost',
        'parts_cost', 'total', 'sale_id', 'started_at',
        'completed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'labor_cost'   => 'decimal:2',
            'parts_cost'   => 'decimal:2',
            'total'        => 'decimal:2',
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function parts()
    {
        return $this->hasMany(WorkshopJobPart::class);
    }

    public static function generateJobNumber(): string
    {
        $year  = now()->format('Y');
        $last  = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('TLR-%s-%04d', $year, $last);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending'    => ['label' => 'Pendiente',   'color' => 'yellow'],
            'in_progress'=> ['label' => 'En proceso',  'color' => 'blue'],
            'completed'  => ['label' => 'Completado',  'color' => 'green'],
            'delivered'  => ['label' => 'Entregado',   'color' => 'purple'],
            'cancelled'  => ['label' => 'Cancelado',   'color' => 'red'],
            default      => ['label' => $this->status, 'color' => 'gray'],
        };
    }
}
