<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Order;
use App\Models\Reward;
use App\Models\InventoryItem;
use App\Models\Expense;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Services
        $services = [
            ['name' => 'Wash & Fold', 'description' => 'Regular wash and fold service', 'price' => 2.50, 'unit' => 'kg', 'is_active' => true],
            ['name' => 'Wash & Iron', 'description' => 'Wash and professional ironing', 'price' => 4.00, 'unit' => 'kg', 'is_active' => true],
            ['name' => 'Dry Cleaning', 'description' => 'Professional dry cleaning', 'price' => 8.00, 'unit' => 'piece', 'is_active' => true],
            ['name' => 'Ironing Only', 'description' => 'Professional ironing service', 'price' => 2.00, 'unit' => 'kg', 'is_active' => true],
            ['name' => 'Comforter Cleaning', 'description' => 'Specialized comforter cleaning', 'price' => 15.00, 'unit' => 'piece', 'is_active' => true],
            ['name' => 'Curtain Cleaning', 'description' => 'Professional curtain cleaning', 'price' => 10.00, 'unit' => 'piece', 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Create Rewards
        $rewards = [
            [
                'name' => '10% Off Next Order',
                'description' => 'Get 10% discount on your next order',
                'points_required' => 100,
                'discount_percentage' => 10,
                'is_active' => true
            ],
            [
                'name' => '$5 Off',
                'description' => 'Get $5 discount on any order',
                'points_required' => 250,
                'discount_amount' => 5.00,
                'is_active' => true
            ],
            [
                'name' => 'Free Wash & Fold',
                'description' => 'Free wash and fold service up to 5kg',
                'points_required' => 500,
                'discount_amount' => 12.50,
                'is_active' => true
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }

        // Create Inventory Items
        $inventoryItems = [
            ['name' => 'Laundry Detergent', 'sku' => 'DET-001', 'category' => 'detergent', 'quantity' => 50, 'unit' => 'bottle', 'unit_price' => 12.00, 'reorder_level' => 10, 'is_active' => true],
            ['name' => 'Fabric Softener', 'sku' => 'SOFT-001', 'category' => 'fabric_softener', 'quantity' => 30, 'unit' => 'bottle', 'unit_price' => 8.00, 'reorder_level' => 10, 'is_active' => true],
            ['name' => 'Bleach', 'sku' => 'BLEACH-001', 'category' => 'bleach', 'quantity' => 20, 'unit' => 'bottle', 'unit_price' => 5.00, 'reorder_level' => 8, 'is_active' => true],
            ['name' => 'Starch Spray', 'sku' => 'STARCH-001', 'category' => 'starch', 'quantity' => 15, 'unit' => 'can', 'unit_price' => 6.00, 'reorder_level' => 5, 'is_active' => true],
            ['name' => 'Plastic Hangers', 'sku' => 'HANG-001', 'category' => 'hangers', 'quantity' => 200, 'unit' => 'piece', 'unit_price' => 0.50, 'reorder_level' => 50, 'is_active' => true],
            ['name' => 'Laundry Bags', 'sku' => 'BAG-001', 'category' => 'bags', 'quantity' => 100, 'unit' => 'piece', 'unit_price' => 0.75, 'reorder_level' => 30, 'is_active' => true],
        ];

        foreach ($inventoryItems as $item) {
            InventoryItem::create($item);
        }

        // Create Sample Customers
        $customers = [
            ['name' => 'John Smith', 'phone' => '555-0101', 'email' => 'john.smith@example.com', 'address' => '123 Main St, City, State', 'loyalty_points' => 150],
            ['name' => 'Maria Garcia', 'phone' => '555-0102', 'email' => 'maria.garcia@example.com', 'address' => '456 Oak Ave, City, State', 'loyalty_points' => 300],
            ['name' => 'David Johnson', 'phone' => '555-0103', 'email' => 'david.j@example.com', 'address' => '789 Pine Rd, City, State', 'loyalty_points' => 75],
            ['name' => 'Sarah Williams', 'phone' => '555-0104', 'email' => 'sarah.w@example.com', 'address' => '321 Elm St, City, State', 'loyalty_points' => 600],
            ['name' => 'Michael Brown', 'phone' => '555-0105', 'email' => 'michael.b@example.com', 'address' => '654 Maple Dr, City, State', 'loyalty_points' => 1200],
        ];

        foreach ($customers as $customerData) {
            $customer = Customer::create($customerData);
            $customer->updateMembershipTier();
        }

        // Create Sample Orders
        $customer = Customer::first();
        $service = Service::first();

        $order = Order::create([
            'customer_id' => $customer->id,
            'pickup_date' => now(),
            'delivery_date' => now()->addDays(2),
            'status' => 'received',
        ]);

        $order->items()->create([
            'service_id' => $service->id,
            'quantity' => 5,
            'unit_price' => $service->price,
        ]);

        $order->calculateTotal();
        $order->save();

        $this->command->info('Database seeded successfully!');
    }
}
