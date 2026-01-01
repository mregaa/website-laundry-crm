<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\Reward;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test order cannot be created without items.
     */
    public function test_order_cannot_be_created_without_items(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->post(route('orders.store'), [
            'customer_id' => $customer->id,
            'items' => [], // Empty items array
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertEquals(0, Order::count());
    }

    /**
     * Test loyalty points are awarded only once.
     */
    public function test_loyalty_points_awarded_only_once_on_completion(): void
    {
        $customer = Customer::factory()->create(['loyalty_points' => 0]);
        $service = Service::factory()->create(['price' => 100, 'unit' => 'kg']);
        
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'received',
            'subtotal' => 100,
            'total' => 110,
        ]);
        
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
        $initialPoints = $customer->loyalty_points;
        
        // Try to complete again (idempotency check)
        $this->post(route('orders.update-status', $order), [
            'status' => 'completed',
        ]);

        $customer->refresh();
        $this->assertEquals($initialPoints, $customer->loyalty_points);
    }

    /**
     * Test order status cannot be changed if completed.
     */
    public function test_completed_order_status_cannot_be_changed(): void
    {
        $order = Order::factory()->create(['status' => 'completed']);

        $response = $this->post(route('orders.update-status', $order), [
            'status' => 'washing',
        ]);

        $response->assertSessionHas('error');
        $order->refresh();
        $this->assertEquals('completed', $order->status);
    }

    /**
     * Test payment cannot exceed remaining balance.
     */
    public function test_payment_cannot_exceed_remaining_balance(): void
    {
        $order = Order::factory()->create([
            'total' => 100,
            'paid_amount' => 0,
        ]);

        $response = $this->post(route('orders.add-payment', $order), [
            'amount' => 150, // More than total
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    /**
     * Test reward redemption validates sufficient points.
     */
    public function test_reward_redemption_validates_sufficient_points(): void
    {
        $customer = Customer::factory()->create(['loyalty_points' => 50]);
        $reward = Reward::factory()->create(['points_required' => 100]);
        $service = Service::factory()->create();

        $response = $this->post(route('orders.store'), [
            'customer_id' => $customer->id,
            'reward_id' => $reward->id,
            'items' => [
                [
                    'service_id' => $service->id,
                    'quantity' => 1,
                ]
            ],
        ]);

        $response->assertSessionHasErrors('reward_id');
    }

    /**
     * Test inventory stock cannot go negative.
     */
    public function test_inventory_stock_cannot_go_negative(): void
    {
        $inventory = \App\Models\InventoryItem::factory()->create([
            'quantity' => 10,
        ]);

        $response = $this->post(route('inventory.adjust', $inventory), [
            'type' => 'stock_out',
            'quantity' => 20, // More than available
        ]);

        $response->assertSessionHas('error');
        $inventory->refresh();
        $this->assertEquals(10, $inventory->quantity);
    }
}
