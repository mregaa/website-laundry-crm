<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'quantity',
        'unit',
        'unit_price',
        'reorder_level',
        'max_stock_level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the inventory transactions.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Check if item is low in stock.
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }

    /**
     * Add stock.
     */
    public function addStock(float $quantity, string $referenceNumber = null, string $notes = null): void
    {
        $this->quantity += $quantity;
        $this->save();

        $this->inventoryTransactions()->create([
            'type' => 'stock_in',
            'quantity' => $quantity,
            'balance_after' => $this->quantity,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
        ]);
    }

    /**
     * Remove stock.
     */
    public function removeStock(float $quantity, string $type = 'stock_out', ?int $orderId = null, string $notes = null): bool
    {
        if ($this->quantity < $quantity) {
            return false;
        }

        $this->quantity -= $quantity;
        $this->save();

        $this->inventoryTransactions()->create([
            'type' => $type,
            'quantity' => -$quantity,
            'balance_after' => $this->quantity,
            'order_id' => $orderId,
            'notes' => $notes,
        ]);

        return true;
    }
}
