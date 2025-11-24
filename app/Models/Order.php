<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'payment_status',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid_amount',
        'pickup_date',
        'delivery_date',
        'notes',
        'special_instructions',
        'express_service',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'delivery_date' => 'date',
        'express_service' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updated(function ($order) {
            if ($order->isDirty('status')) {
                $order->statusHistories()->create([
                    'status' => $order->status,
                    'changed_at' => now(),
                    'changed_by' => auth()->id(),
                ]);
            }

            if ($order->payment_status === 'paid' && $order->wasChanged('payment_status')) {
                Transaction::create([
                    'transaction_number' => 'TXN-' . date('Ymd') . '-' . str_pad(Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                    'type' => 'income',
                    'category' => 'order_payment',
                    'amount' => $order->total,
                    'order_id' => $order->id,
                    'transaction_date' => now(),
                    'description' => "Payment for order {$order->order_number}",
                ]);

                // Award loyalty points (1 point per 10 units of currency)
                $points = floor($order->total / 10);
                $order->customer->addLoyaltyPoints($points, "Order {$order->order_number}", $order->id);
            }
        });
    }

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the status histories.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Get the payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Calculate subtotal from items.
     */
    public function calculateSubtotal(): float
    {
        return $this->items()->sum('subtotal');
    }

    /**
     * Calculate total.
     */
    public function calculateTotal(): float
    {
        $subtotal = $this->calculateSubtotal();
        $this->subtotal = $subtotal;
        $this->tax = $subtotal * 0.1; // 10% tax
        $this->total = $subtotal - $this->discount + $this->tax;
        
        return $this->total;
    }

    /**
     * Check if order is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->delivery_date && 
               $this->delivery_date < now() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Get remaining balance.
     */
    public function getRemainingBalance(): float
    {
        return max(0, $this->total - $this->paid_amount);
    }
}
