<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\OrderItem;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_order()
    {
        $customer = Customer::factory()->create();
        $service = Service::factory()->create(['price' => 10.00]);

        $orderData = [
            'customer_id' => $customer->id,
            'pickup_date' => now()->format('Y-m-d'),
            'delivery_date' => now()->addDays(2)->format('Y-m-d'),
            'items' => [
                [
                    'service_id' => $service->id,
                    'quantity' => 5,
                ]
            ]
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
        ]);
    }

    /** @test */
    public function it_generates_unique_order_number()
    {
        $customer = Customer::factory()->create();

        $order1 = Order::create([
            'customer_id' => $customer->id,
        ]);

        $order2 = Order::create([
            'customer_id' => $customer->id,
        ]);

        $this->assertNotEquals($order1->order_number, $order2->order_number);
        $this->assertStringStartsWith('ORD-', $order1->order_number);
    }

    /** @test */
    public function it_calculates_order_total_correctly()
    {
        $customer = Customer::factory()->create();
        $service = Service::factory()->create(['price' => 10.00]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'discount' => 5.00,
        ]);

        $order->items()->create([
            'service_id' => $service->id,
            'quantity' => 5,
            'unit_price' => 10.00,
            'subtotal' => 50.00,
        ]);

        $total = $order->calculateTotal();

        // Subtotal: 50.00, Discount: 5.00, Tax (10%): 4.50, Total: 49.50
        $this->assertEquals(49.50, $total);
    }

    /** @test */
    public function it_tracks_status_changes()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'received',
        ]);

        $order->update(['status' => 'washing']);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => 'washing',
        ]);
    }

    /** @test */
    public function it_can_add_payment_to_order()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total' => 100.00,
            'paid_amount' => 0,
            'payment_status' => 'pending',
        ]);

        $paymentData = [
            'amount' => 50.00,
            'payment_method' => 'cash',
        ];

        $response = $this->post(route('orders.add-payment', $order), $paymentData);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 50.00,
        ]);

        $order->refresh();
        $this->assertEquals(50.00, $order->paid_amount);
        $this->assertEquals('partial', $order->payment_status);
    }

    /** @test */
    public function it_marks_order_as_paid_when_full_amount_received()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total' => 100.00,
            'paid_amount' => 0,
            'payment_status' => 'pending',
        ]);

        $paymentData = [
            'amount' => 100.00,
            'payment_method' => 'cash',
        ];

        $this->post(route('orders.add-payment', $order), $paymentData);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
    }

    /** @test */
    public function it_detects_overdue_orders()
    {
        $customer = Customer::factory()->create();
        
        $overdueOrder = Order::factory()->create([
            'customer_id' => $customer->id,
            'delivery_date' => now()->subDays(1),
            'status' => 'washing',
        ]);

        $onTimeOrder = Order::factory()->create([
            'customer_id' => $customer->id,
            'delivery_date' => now()->addDays(1),
            'status' => 'washing',
        ]);

        $this->assertTrue($overdueOrder->isOverdue());
        $this->assertFalse($onTimeOrder->isOverdue());
    }
}
