<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'customer_id', 'user_id', 'sale_type',
        'status', 'payment_method', 'subtotal', 'tax_percent',
        'tax_amount', 'discount', 'total', 'amount_paid',
        'change_amount', 'notes', 'cancel_reason',
        'cancelled_at', 'cancelled_by',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'     => 'decimal:2',
            'tax_percent'  => 'decimal:2',
            'tax_amount'   => 'decimal:2',
            'discount'     => 'decimal:2',
            'total'        => 'decimal:2',
            'amount_paid'  => 'decimal:2',
            'change_amount'=> 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function workshopJob()
    {
        return $this->hasOne(WorkshopJob::class);
    }

    public static function generateInvoiceNumber(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $last  = static::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count() + 1;

        return sprintf('INV-%s%s-%05d', $year, $month, $last);
    }
}
