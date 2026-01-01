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

    // Status constants - centralized definition
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_READY = 'ready';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Available statuses
    public static function getStatuses(): array
    {
        return [
            self::STATUS_IN_PROGRESS => 'Diproses',
            self::STATUS_READY => 'Siap Diambil',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

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
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
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
                ]);
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
        $discount = (float)($this->discount ?? 0);
        $taxableAmount = $subtotal - $discount;
        $tax = round($taxableAmount * 0.1, 2);
        $total = round($subtotal - $discount + $tax, 2);
        
        $this->setAttribute('subtotal', $subtotal);
        $this->setAttribute('tax', $tax);
        $this->setAttribute('total', $total);
        
        return $total;
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

    /**
     * Normalize phone number for WhatsApp.
     * Converts Indonesian phone format to international format without + sign.
     */
    public function normalizedWhatsappPhone(): ?string
    {
        $phone = $this->customer->phone ?? null;
        
        if (empty($phone)) {
            return null;
        }

        // Strip all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert Indonesian format starting with 0 to 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Ensure it starts with 62 (Indonesian country code)
        if (substr($phone, 0, 2) !== '62') {
            return null;
        }

        return $phone;
    }

    /**
     * Generate WhatsApp message for ready order.
     */
    public function whatsappMessage(): string
    {
        $customerName = $this->customer->name ?? 'Pelanggan';
        $orderNumber = $this->order_number;
        $amount = rupiah($this->total);
        
        // Get payment status in Indonesian
        $paymentStatusMap = [
            'pending' => 'belum dibayar',
            'partial' => 'dibayar sebagian',
            'paid' => 'lunas',
            'refunded' => 'dikembalikan',
        ];
        $paymentStatus = $paymentStatusMap[$this->payment_status] ?? $this->payment_status;

        return "Halo {$customerName},\n\n"
            . "Kami informasikan bahwa pesanan laundry Anda sudah *SELESAI* dan *SIAP DIAMBIL*\n\n"
            . "No. Pesanan : {$orderNumber}\n"
            . "Nama        : {$customerName}\n"
            . "Total       : {$amount} ({$paymentStatus})\n"
            . "Status      : Siap diambil\n\n"
            . "Silakan datang ke outlet kami untuk mengambil pesanan Anda.\n"
            . "Terima kasih telah mempercayakan laundry Anda kepada kami";
    }

    /**
     * Generate WhatsApp Click-to-Chat URL.
     */
    public function whatsappUrl(): ?string
    {
        $phone = $this->normalizedWhatsappPhone();

        if (empty($phone)) {
            return null;
        }

        $message = $this->whatsappMessage();
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }
}
