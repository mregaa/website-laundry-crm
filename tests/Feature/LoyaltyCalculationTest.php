<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoyaltyCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test loyalty points calculation: 10 base + 2 per kg.
     */
    public function test_loyalty_points_calculation(): void
    {
        $customer = Customer::factory()->create(['loyalty_points' => 0]);
        $service = Service::factory()->create(['price' => 100, 'unit' => 'kg']);
        
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'received',
        ]);
        
        // 5 kg of laundry
        $order->items()->create([
            'service_id' => $service->id,
            'quantity' => 5,
            'unit_price' => 100,
        ]);

        // Complete order
        $this->post(route('orders.update-status', $order), [
            'status' => 'completed',
        ]);

        $customer->refresh();
        // 10 (base) + 10 (5kg * 2) = 20 points
        $this->assertEquals(20, $customer->loyalty_points);
    }

    /**
     * Test membership tier updates based on points.
     */
    public function test_membership_tier_updates(): void
    {
        $customer = Customer::factory()->create([
            'loyalty_points' => 0,
            'membership_tier' => 'bronze',
        ]);

        // Add 600 points
        $customer->addLoyaltyPoints(600, 'Test points');
        $customer->refresh();

        $this->assertEquals('silver', $customer->membership_tier);
    }

    /**
     * Test points redemption decreases balance.
     */
    public function test_points_redemption_decreases_balance(): void
    {
        $customer = Customer::factory()->create(['loyalty_points' => 100]);

        $success = $customer->redeemLoyaltyPoints(30, 'Test redemption');

        $this->assertTrue($success);
        $customer->refresh();
        $this->assertEquals(70, $customer->loyalty_points);
    }

    /**
     * Test cannot redeem more points than available.
     */
    public function test_cannot_redeem_more_points_than_available(): void
    {
        $customer = Customer::factory()->create(['loyalty_points' => 50]);

        $success = $customer->redeemLoyaltyPoints(100, 'Test redemption');

        $this->assertFalse($success);
        $customer->refresh();
        $this->assertEquals(50, $customer->loyalty_points);
    }
}
