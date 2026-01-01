<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'order_id',
        'amount',
        'payment_method',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = 'PAY-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($payment) {
            // Prevent duplicate updates - check if order's paid_amount already includes this payment
            $order = $payment->order;
            $totalPayments = $order->payments()->sum('amount');
            
            // Only update if there's a discrepancy (shouldn't happen with proper controller logic)
            if ($totalPayments != $order->paid_amount) {
                $order->paid_amount = $totalPayments;
                
                if ($order->paid_amount >= $order->total) {
                    $order->payment_status = 'paid';
                } elseif ($order->paid_amount > 0) {
                    $order->payment_status = 'partial';
                }
                
                $order->save();
            }
        });
    }

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
