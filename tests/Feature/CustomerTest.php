<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_customer()
    {
        $customerData = [
            'name' => 'Test Customer',
            'phone' => '555-1234',
            'email' => 'test@example.com',
            'address' => '123 Test St',
        ];

        $response = $this->post(route('customers.store'), $customerData);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'name' => 'Test Customer',
            'phone' => '555-1234',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_customer()
    {
        $response = $this->post(route('customers.store'), []);

        $response->assertSessionHasErrors(['name', 'phone']);
    }

    /** @test */
    public function it_can_add_loyalty_points_to_customer()
    {
        $customer = Customer::factory()->create([
            'loyalty_points' => 0,
        ]);

        $customer->addLoyaltyPoints(100, 'Test points');

        $this->assertEquals(100, $customer->fresh()->loyalty_points);
        $this->assertDatabaseHas('loyalty_transactions', [
            'customer_id' => $customer->id,
            'points' => 100,
            'type' => 'earned',
        ]);
    }

    /** @test */
    public function it_can_redeem_loyalty_points()
    {
        $customer = Customer::factory()->create([
            'loyalty_points' => 500,
        ]);

        $result = $customer->redeemLoyaltyPoints(100, 'Test redemption');

        $this->assertTrue($result);
        $this->assertEquals(400, $customer->fresh()->loyalty_points);
        $this->assertDatabaseHas('loyalty_transactions', [
            'customer_id' => $customer->id,
            'points' => -100,
            'type' => 'redeemed',
        ]);
    }

    /** @test */
    public function it_cannot_redeem_more_points_than_available()
    {
        $customer = Customer::factory()->create([
            'loyalty_points' => 50,
        ]);

        $result = $customer->redeemLoyaltyPoints(100, 'Test redemption');

        $this->assertFalse($result);
        $this->assertEquals(50, $customer->fresh()->loyalty_points);
    }

    /** @test */
    public function it_updates_membership_tier_based_on_points()
    {
        $customer = Customer::factory()->create([
            'loyalty_points' => 0,
            'membership_tier' => 'bronze',
        ]);

        $customer->addLoyaltyPoints(2500, 'Test points');

        $this->assertEquals('gold', $customer->fresh()->membership_tier);
    }

    /** @test */
    public function it_calculates_total_spent_correctly()
    {
        $customer = Customer::factory()->create();
        $service = Service::factory()->create(['price' => 10.00]);

        // Create paid order
        $order1 = Order::factory()->create([
            'customer_id' => $customer->id,
            'total' => 100.00,
            'payment_status' => 'paid',
        ]);

        // Create unpaid order
        $order2 = Order::factory()->create([
            'customer_id' => $customer->id,
            'total' => 50.00,
            'payment_status' => 'pending',
        ]);

        $this->assertEquals(100.00, $customer->totalSpent());
    }
}
