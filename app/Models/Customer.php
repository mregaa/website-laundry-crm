<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'loyalty_points',
        'membership_tier',
        'birthdate',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    /**
     * Get the orders for the customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the loyalty transactions for the customer.
     */
    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    /**
     * Get the customer rewards.
     */
    public function customerRewards(): HasMany
    {
        return $this->hasMany(CustomerReward::class);
    }

    /**
     * Calculate total spent by customer.
     */
    public function totalSpent(): float
    {
        return $this->orders()
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    /**
     * Add loyalty points to customer.
     */
    public function addLoyaltyPoints(int $points, string $description, ?int $orderId = null): void
    {
        $this->increment('loyalty_points', $points);
        
        $this->loyaltyTransactions()->create([
            'points' => $points,
            'type' => 'earned',
            'description' => $description,
            'order_id' => $orderId,
        ]);

        $this->updateMembershipTier();
    }

    /**
     * Redeem loyalty points.
     */
    public function redeemLoyaltyPoints(int $points, string $description, ?int $orderId = null): bool
    {
        if ($this->loyalty_points < $points) {
            return false;
        }

        $this->decrement('loyalty_points', $points);
        
        $this->loyaltyTransactions()->create([
            'points' => -$points,
            'type' => 'redeemed',
            'description' => $description,
            'order_id' => $orderId,
        ]);

        $this->updateMembershipTier();
        return true;
    }

    /**
     * Update membership tier based on loyalty points.
     */
    protected function updateMembershipTier(): void
    {
        $tier = match (true) {
            $this->loyalty_points >= 5000 => 'platinum',
            $this->loyalty_points >= 2000 => 'gold',
            $this->loyalty_points >= 500 => 'silver',
            default => 'bronze',
        };

        $this->update(['membership_tier' => $tier]);
    }
}
