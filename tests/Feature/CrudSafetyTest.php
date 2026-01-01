<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CrudSafetyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test cannot delete customer with orders.
     */
    public function test_cannot_delete_customer_with_orders(): void
    {
        $customer = Customer::factory()->create();
        Order::factory()->create(['customer_id' => $customer->id]);

        $response = $this->delete(route('customers.destroy', $customer));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    /**
     * Test cannot delete service with order items.
     */
    public function test_cannot_delete_service_with_order_items(): void
    {
        $service = Service::factory()->create();
        $order = Order::factory()->create();
        $order->items()->create([
            'service_id' => $service->id,
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        $response = $this->delete(route('services.destroy', $service));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }

    /**
     * Test cannot delete order with payments.
     */
    public function test_cannot_delete_order_with_payments(): void
    {
        $order = Order::factory()->create();
        $order->payments()->create([
            'amount' => 100,
            'payment_method' => 'cash',
            'paid_at' => now(),
        ]);

        $response = $this->delete(route('orders.destroy', $order));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    /**
     * Test cannot delete inventory item with transactions.
     */
    public function test_cannot_delete_inventory_item_with_transactions(): void
    {
        $inventory = \App\Models\InventoryItem::factory()->create();
        $inventory->inventoryTransactions()->create([
            'type' => 'stock_in',
            'quantity' => 10,
            'balance_after' => 10,
        ]);

        $response = $this->delete(route('inventory.destroy', $inventory));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('inventory_items', ['id' => $inventory->id]);
    }

    /**
     * Test customer validation prevents duplicate email.
     */
    public function test_customer_validation_prevents_duplicate_email(): void
    {
        $existingCustomer = Customer::factory()->create(['email' => 'test@example.com']);

        $response = $this->post(route('customers.store'), [
            'name' => 'New Customer',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test service validation requires positive price.
     */
    public function test_service_validation_requires_positive_price(): void
    {
        $response = $this->post(route('services.store'), [
            'name' => 'Test Service',
            'price' => -10,
            'unit' => 'kg',
        ]);

        $response->assertSessionHasErrors('price');
    }
}
