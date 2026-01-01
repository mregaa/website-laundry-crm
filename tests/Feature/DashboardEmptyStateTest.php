<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardEmptyStateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test dashboard loads with empty database.
     */
    public function test_dashboard_loads_with_empty_database(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('todayStats');
        $response->assertViewHas('monthStats');
    }

    /**
     * Test dashboard stats return zero for empty data.
     */
    public function test_dashboard_stats_return_zero_for_empty_data(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        
        $todayStats = $response->viewData('todayStats');
        $this->assertEquals(0, $todayStats['orders']);
        $this->assertEquals(0, $todayStats['revenue']);
        $this->assertEquals(0, $todayStats['new_customers']);
    }

    /**
     * Test customers index loads with no customers.
     */
    public function test_customers_index_loads_with_no_customers(): void
    {
        $response = $this->get(route('customers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('customers');
    }

    /**
     * Test orders index loads with no orders.
     */
    public function test_orders_index_loads_with_no_orders(): void
    {
        $response = $this->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertViewHas('orders');
    }

    /**
     * Test services index loads with no services.
     */
    public function test_services_index_loads_with_no_services(): void
    {
        $response = $this->get(route('services.index'));

        $response->assertStatus(200);
        $response->assertViewHas('services');
    }

    /**
     * Test inventory index loads with no items.
     */
    public function test_inventory_index_loads_with_no_items(): void
    {
        $response = $this->get(route('inventory.index'));

        $response->assertStatus(200);
        $response->assertViewHas('items');
    }

    /**
     * Test financial transactions loads with no data.
     */
    public function test_financial_transactions_loads_with_no_data(): void
    {
        $response = $this->get(route('financial.transactions'));

        $response->assertStatus(200);
        $response->assertViewHas('transactions');
    }
}
