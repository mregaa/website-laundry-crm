<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Reward;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\LoyaltyTransaction;
use App\Models\CustomerReward;
use App\Models\OrderStatusHistory;
use App\Models\InventoryTransaction;

class RealCaseIndonesiaSeeder extends Seeder
{
    private $users = [];
    private $customers = [];
    private $services = [];
    private $rewards = [];
    private $inventoryItems = [];
    private $orders = [];
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedUsers();
            $this->seedCustomers();
            $this->seedServices();
            $this->seedRewards();
            $this->seedInventoryItems();
            $this->seedInventoryTransactions();
            $this->seedOrders();
            $this->seedOrderItems();
            $this->seedPayments();
            $this->seedTransactions();
            $this->seedExpenses();
            $this->seedLoyaltyTransactions();
            $this->seedCustomerRewards();
            $this->seedOrderStatusHistories();
            $this->seedInventoryUsage();
        });

        $this->command->info('✅ Real Case Indonesia Seeder completed successfully!');
    }

    private function seedUsers()
    {
        $this->command->info('Seeding Users...');
        
        $usersData = [
            [
                'name' => 'Budi Santoso',
                'email' => 'admin@laundry.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'kasir@laundry.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($usersData as $userData) {
            $this->users[] = User::create($userData);
        }
    }

    private function seedCustomers()
    {
        $this->command->info('Seeding Customers...');
        
        $customersData = [
            ['name' => 'Ahmad Wijaya', 'phone' => '081234567890', 'email' => 'ahmad.w@gmail.com', 'address' => 'Jl. Sudirman No. 45, Jakarta Pusat', 'membership_tier' => 'gold'],
            ['name' => 'Dewi Lestari', 'phone' => '082345678901', 'email' => 'dewi.lestari@yahoo.com', 'address' => 'Jl. Gatot Subroto No. 12, Jakarta Selatan', 'membership_tier' => 'platinum'],
            ['name' => 'Rudi Hartono', 'phone' => '083456789012', 'email' => null, 'address' => 'Jl. Thamrin No. 88, Jakarta Pusat', 'membership_tier' => 'silver'],
            ['name' => 'Sari Indah', 'phone' => '084567890123', 'email' => 'sari.indah@gmail.com', 'address' => 'Jl. Kuningan No. 34, Jakarta Selatan', 'membership_tier' => 'gold'],
            ['name' => 'Bambang Susilo', 'phone' => '085678901234', 'email' => null, 'address' => 'Jl. Rasuna Said No. 21, Jakarta Selatan', 'membership_tier' => 'bronze'],
            ['name' => 'Rina Melati', 'phone' => '086789012345', 'email' => 'rina.m@outlook.com', 'address' => 'Jl. Casablanca No. 56, Jakarta Selatan', 'membership_tier' => 'silver'],
            ['name' => 'Eko Prasetyo', 'phone' => '087890123456', 'email' => 'eko.p@gmail.com', 'address' => 'Jl. MT Haryono No. 78, Jakarta Timur', 'membership_tier' => 'platinum'],
            ['name' => 'Linda Wijayanti', 'phone' => '088901234567', 'email' => null, 'address' => 'Jl. Pancoran No. 123, Jakarta Selatan', 'membership_tier' => 'gold'],
            ['name' => 'Hendra Kusuma', 'phone' => '089012345678', 'email' => 'hendra.k@yahoo.co.id', 'address' => 'Jl. Tebet No. 45, Jakarta Selatan', 'membership_tier' => 'bronze'],
            ['name' => 'Maya Sari', 'phone' => '081123456789', 'email' => 'maya.sari@gmail.com', 'address' => 'Jl. Kemang No. 67, Jakarta Selatan', 'membership_tier' => 'silver'],
            ['name' => 'Agus Salim', 'phone' => '082234567890', 'email' => null, 'address' => 'Jl. Senopati No. 34, Jakarta Selatan', 'membership_tier' => 'bronze'],
            ['name' => 'Fitri Handayani', 'phone' => '083345678901', 'email' => 'fitri.h@gmail.com', 'address' => 'Jl. Cilandak No. 89, Jakarta Selatan', 'membership_tier' => 'gold'],
        ];

        foreach ($customersData as $customerData) {
            $customer = Customer::create(array_merge($customerData, [
                'loyalty_points' => 0, // Will be updated after loyalty transactions
                'created_at' => now()->subDays(rand(30, 180)),
                'updated_at' => now(),
            ]));
            $this->customers[] = $customer;
        }
    }

    private function seedServices()
    {
        $this->command->info('Seeding Services...');
        
        $servicesData = [
            ['name' => 'Cuci Kering', 'description' => 'Cuci dengan mesin, belum disetrika', 'price' => 7000, 'unit' => 'kg', 'estimated_time' => 1440, 'is_active' => true],
            ['name' => 'Cuci Setrika', 'description' => 'Cuci lengkap dengan setrika rapi', 'price' => 10000, 'unit' => 'kg', 'estimated_time' => 2880, 'is_active' => true],
            ['name' => 'Setrika Saja', 'description' => 'Hanya jasa setrika', 'price' => 5000, 'unit' => 'kg', 'estimated_time' => 720, 'is_active' => true],
            ['name' => 'Cuci Bedcover Single', 'description' => 'Cuci bedcover ukuran single/twin', 'price' => 25000, 'unit' => 'item', 'estimated_time' => 2880, 'is_active' => true],
            ['name' => 'Cuci Bedcover Queen/King', 'description' => 'Cuci bedcover ukuran queen atau king', 'price' => 40000, 'unit' => 'item', 'estimated_time' => 2880, 'is_active' => true],
            ['name' => 'Cuci Sepatu', 'description' => 'Cuci sepatu dengan deep cleaning', 'price' => 35000, 'unit' => 'item', 'estimated_time' => 4320, 'is_active' => true],
            ['name' => 'Cuci Karpet', 'description' => 'Cuci karpet per meter', 'price' => 50000, 'unit' => 'item', 'estimated_time' => 5760, 'is_active' => true],
            ['name' => 'Express 6 Jam', 'description' => 'Layanan kilat cuci setrika dalam 6 jam', 'price' => 20000, 'unit' => 'kg', 'estimated_time' => 360, 'is_active' => true],
            ['name' => 'Dry Cleaning', 'description' => 'Cuci kering untuk pakaian premium', 'price' => 30000, 'unit' => 'item', 'estimated_time' => 4320, 'is_active' => true],
        ];

        foreach ($servicesData as $serviceData) {
            $this->services[] = Service::create($serviceData);
        }
    }

    private function seedRewards()
    {
        $this->command->info('Seeding Rewards...');
        
        $rewardsData = [
            ['name' => 'Diskon 10%', 'description' => 'Potongan 10% untuk total transaksi', 'points_required' => 50, 'discount_percentage' => 10, 'discount_amount' => null, 'is_active' => true],
            ['name' => 'Diskon Rp 15.000', 'description' => 'Potongan langsung Rp 15.000', 'points_required' => 75, 'discount_percentage' => null, 'discount_amount' => 15000, 'is_active' => true],
            ['name' => 'Diskon 20%', 'description' => 'Potongan 20% untuk total transaksi', 'points_required' => 100, 'discount_percentage' => 20, 'discount_amount' => null, 'is_active' => true],
            ['name' => 'Diskon Rp 30.000', 'description' => 'Potongan langsung Rp 30.000', 'points_required' => 120, 'discount_percentage' => null, 'discount_amount' => 30000, 'is_active' => true],
            ['name' => 'Gratis Cuci 3kg', 'description' => 'Gratis cuci setrika 3kg', 'points_required' => 150, 'discount_percentage' => null, 'discount_amount' => 30000, 'is_active' => true],
            ['name' => 'Diskon 25%', 'description' => 'Potongan 25% untuk total transaksi', 'points_required' => 200, 'discount_percentage' => 25, 'discount_amount' => null, 'is_active' => true],
        ];

        foreach ($rewardsData as $rewardData) {
            $this->rewards[] = Reward::create($rewardData);
        }
    }

    private function seedInventoryItems()
    {
        $this->command->info('Seeding Inventory Items...');
        
        $inventoryData = [
            ['name' => 'Detergen Rinso Matic 1kg', 'sku' => 'DET-RIN-001', 'category' => 'detergent', 'quantity' => 50, 'unit' => 'kg', 'unit_price' => 35000, 'reorder_level' => 10, 'max_stock_level' => 100],
            ['name' => 'Detergen Attack Plus 800g', 'sku' => 'DET-ATK-001', 'category' => 'detergent', 'quantity' => 40, 'unit' => 'kg', 'unit_price' => 28000, 'reorder_level' => 10, 'max_stock_level' => 80],
            ['name' => 'Pewangi Molto Ultra 900ml', 'sku' => 'SOF-MLT-001', 'category' => 'fabric_softener', 'quantity' => 30, 'unit' => 'liter', 'unit_price' => 22000, 'reorder_level' => 8, 'max_stock_level' => 60],
            ['name' => 'Pemutih Bayclin 1L', 'sku' => 'BLC-BAY-001', 'category' => 'bleach', 'quantity' => 25, 'unit' => 'liter', 'unit_price' => 18000, 'reorder_level' => 5, 'max_stock_level' => 50],
            ['name' => 'Kanji Spray Cap Lang 500ml', 'sku' => 'STA-CPL-001', 'category' => 'starch', 'quantity' => 20, 'unit' => 'botol', 'unit_price' => 15000, 'reorder_level' => 5, 'max_stock_level' => 40],
            ['name' => 'Hanger Plastik', 'sku' => 'HNG-PLS-001', 'category' => 'hangers', 'quantity' => 500, 'unit' => 'pcs', 'unit_price' => 1500, 'reorder_level' => 100, 'max_stock_level' => 1000],
            ['name' => 'Hanger Kayu Premium', 'sku' => 'HNG-KYU-001', 'category' => 'hangers', 'quantity' => 200, 'unit' => 'pcs', 'unit_price' => 5000, 'reorder_level' => 50, 'max_stock_level' => 500],
            ['name' => 'Plastik Laundry Besar', 'sku' => 'BAG-PLS-001', 'category' => 'bags', 'quantity' => 300, 'unit' => 'pcs', 'unit_price' => 500, 'reorder_level' => 50, 'max_stock_level' => 600],
            ['name' => 'Plastik Laundry Kecil', 'sku' => 'BAG-PLS-002', 'category' => 'bags', 'quantity' => 400, 'unit' => 'pcs', 'unit_price' => 300, 'reorder_level' => 80, 'max_stock_level' => 800],
            ['name' => 'Sabun Cuci Tangan Dettol', 'sku' => 'OTH-DTT-001', 'category' => 'other', 'quantity' => 15, 'unit' => 'pcs', 'unit_price' => 12000, 'reorder_level' => 5, 'max_stock_level' => 30],
            ['name' => 'Pengharum Ruangan Stella', 'sku' => 'OTH-STL-001', 'category' => 'other', 'quantity' => 10, 'unit' => 'pcs', 'unit_price' => 25000, 'reorder_level' => 3, 'max_stock_level' => 20],
            ['name' => 'Label Nama Pelanggan', 'sku' => 'OTH-LBL-001', 'category' => 'other', 'quantity' => 1000, 'unit' => 'pcs', 'unit_price' => 100, 'reorder_level' => 200, 'max_stock_level' => 2000],
        ];

        foreach ($inventoryData as $itemData) {
            $this->inventoryItems[] = InventoryItem::create(array_merge($itemData, ['is_active' => true]));
        }
    }

    private function seedInventoryTransactions()
    {
        $this->command->info('Seeding Initial Inventory Transactions (Stock In)...');
        
        foreach ($this->inventoryItems as $item) {
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type' => 'stock_in',
                'quantity' => $item->quantity,
                'balance_after' => $item->quantity,
                'reference_number' => 'PO-INIT-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                'notes' => 'Stok awal pembelian',
                'order_id' => null,
                'created_at' => now()->subDays(rand(10, 30)),
            ]);
        }
    }

    private function seedOrders()
    {
        $this->command->info('Seeding Orders...');
        
        // Current allowed statuses after simplification
        $statuses = ['in_progress', 'ready', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'partial', 'paid', 'refunded'];
        
        for ($i = 1; $i <= 35; $i++) {
            $customer = $this->customers[array_rand($this->customers)];
            $status = $statuses[array_rand($statuses)];
            
            // More completed/ready orders for realistic scenario
            if ($i % 3 === 0) $status = 'completed';
            if ($i % 5 === 0) $status = 'ready';
            if ($i % 10 === 0) $status = 'in_progress';
            if ($i === 35) $status = 'cancelled';
            
            $paymentStatus = 'paid';
            if ($status === 'in_progress' && rand(0, 1)) $paymentStatus = 'pending';
            if ($status === 'ready' && rand(0, 2) === 0) $paymentStatus = 'partial';
            if ($status === 'cancelled') $paymentStatus = 'refunded';
            
            $createdDays = rand(1, 60);
            $pickupDate = now()->subDays($createdDays)->addHours(rand(9, 17));
            $deliveryDate = $pickupDate->copy()->addDays(rand(2, 4));
            $expressService = rand(0, 10) === 0; // 10% express
            
            $order = Order::create([
                'order_number' => 'ORD-' . now()->subDays($createdDays)->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'subtotal' => 0, // Will be calculated after items
                'discount' => 0,
                'tax' => 0,
                'total' => 0,
                'paid_amount' => 0,
                'pickup_date' => $pickupDate,
                'delivery_date' => $deliveryDate,
                'notes' => rand(0, 3) === 0 ? 'Pisahkan pakaian putih dan berwarna' : null,
                'special_instructions' => rand(0, 5) === 0 ? 'Harap hati-hati, ada pakaian sensitif' : null,
                'express_service' => $expressService,
                'created_at' => now()->subDays($createdDays),
                'updated_at' => now()->subDays(max(0, $createdDays - rand(1, 3))),
            ]);
            
            $this->orders[] = $order;
        }
    }

    private function seedOrderItems()
    {
        $this->command->info('Seeding Order Items...');
        
        foreach ($this->orders as $order) {
            $numItems = rand(1, 4);
            $subtotal = 0;
            
            for ($j = 0; $j < $numItems; $j++) {
                $service = $this->services[array_rand($this->services)];
                
                // Quantity based on unit type
                if ($service->unit === 'kg') {
                    $quantity = rand(2, 10); // 2-10 kg
                } elseif ($service->unit === 'item') {
                    $quantity = rand(1, 3); // 1-3 items
                } else { // bundle
                    $quantity = rand(1, 2); // 1-2 bundles
                }
                
                $unitPrice = $service->price;
                if ($order->express_service) {
                    $unitPrice = $unitPrice * 1.2; // 20% extra for express
                }
                
                $itemSubtotal = $quantity * $unitPrice;
                $subtotal += $itemSubtotal;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'notes' => null,
                ]);
            }
            
            // Calculate total with discount and tax
            $discount = 0;
            if (rand(0, 5) === 0) { // 16% chance of discount
                $discount = $subtotal * (rand(5, 20) / 100);
            }
            
            $taxableAmount = $subtotal - $discount;
            $tax = $taxableAmount * 0.10; // 10% tax
            $total = $subtotal - $discount + $tax;
            
            $paidAmount = 0;
            if ($order->payment_status === 'paid') {
                $paidAmount = $total;
            } elseif ($order->payment_status === 'partial') {
                $paidAmount = $total * (rand(30, 70) / 100);
            } elseif ($order->payment_status === 'refunded') {
                $paidAmount = 0;
            }
            
            $order->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'paid_amount' => $paidAmount,
            ]);
        }
    }

    private function seedPayments()
    {
        $this->command->info('Seeding Payments...');
        
        $paymentMethods = ['cash', 'card', 'bank_transfer', 'e-wallet'];
        $paymentCounter = 1;
        
        foreach ($this->orders as $order) {
            if (in_array($order->payment_status, ['paid', 'partial', 'refunded']) && $order->paid_amount > 0) {
                $numPayments = ($order->payment_status === 'partial') ? rand(1, 2) : 1;
                $remainingAmount = $order->paid_amount;
                
                for ($p = 0; $p < $numPayments; $p++) {
                    $amount = ($p === $numPayments - 1) ? $remainingAmount : ($remainingAmount * (rand(40, 60) / 100));
                    $remainingAmount -= $amount;
                    
                    $paidAt = $order->created_at->copy()->addHours(rand(1, 48));
                    
                    Payment::create([
                        'payment_number' => 'PAY-' . $paidAt->format('Ymd') . '-' . str_pad($paymentCounter++, 4, '0', STR_PAD_LEFT),
                        'order_id' => $order->id,
                        'amount' => $amount,
                        'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                        'paid_at' => $paidAt,
                        'notes' => null,
                        'created_at' => $paidAt,
                    ]);
                }
            }
        }
    }

    private function seedTransactions()
    {
        $this->command->info('Seeding Transactions...');
        
        $transactionCounter = 1;
        $payments = Payment::with('order')->get();
        
        foreach ($payments as $payment) {
            Transaction::create([
                'transaction_number' => 'TRX-' . $payment->paid_at->format('Ymd') . '-' . str_pad($transactionCounter++, 4, '0', STR_PAD_LEFT),
                'type' => 'income',
                'category' => 'order_payment',
                'amount' => $payment->amount,
                'order_id' => $payment->order_id,
                'payment_method' => $payment->payment_method,
                'description' => 'Pembayaran untuk order ' . $payment->order->order_number,
                'transaction_date' => $payment->paid_at->toDateString(),
                'created_at' => $payment->paid_at,
            ]);
        }
        
        // Add some expense transactions
        $expenseCategories = ['utilities', 'supplies', 'maintenance', 'marketing'];
        for ($i = 0; $i < 8; $i++) {
            $transDate = now()->subDays(rand(1, 30));
            Transaction::create([
                'transaction_number' => 'TRX-' . $transDate->format('Ymd') . '-' . str_pad($transactionCounter++, 4, '0', STR_PAD_LEFT),
                'type' => 'expense',
                'category' => $expenseCategories[array_rand($expenseCategories)],
                'amount' => rand(50000, 500000),
                'order_id' => null,
                'payment_method' => ['cash', 'bank_transfer'][array_rand(['cash', 'bank_transfer'])],
                'description' => 'Pengeluaran operasional',
                'transaction_date' => $transDate->toDateString(),
                'created_at' => $transDate,
            ]);
        }
    }

    private function seedExpenses()
    {
        $this->command->info('Seeding Expenses...');
        
        $expensesData = [
            ['category' => 'salary', 'amount' => 3500000, 'vendor' => 'Gaji Karyawan', 'description' => 'Gaji bulanan staff laundry', 'days_ago' => 5],
            ['category' => 'utilities', 'amount' => 450000, 'vendor' => 'PLN', 'description' => 'Tagihan listrik bulan ini', 'days_ago' => 8],
            ['category' => 'utilities', 'amount' => 180000, 'vendor' => 'PDAM', 'description' => 'Tagihan air bulan ini', 'days_ago' => 8],
            ['category' => 'supplies', 'amount' => 850000, 'vendor' => 'Toko Makmur', 'description' => 'Pembelian detergen dan pewangi', 'days_ago' => 12],
            ['category' => 'supplies', 'amount' => 320000, 'vendor' => 'CV Plastik Jaya', 'description' => 'Plastik laundry dan hanger', 'days_ago' => 15],
            ['category' => 'maintenance', 'amount' => 550000, 'vendor' => 'Tukang Service', 'description' => 'Service mesin cuci dan setrika', 'days_ago' => 20],
            ['category' => 'marketing', 'amount' => 275000, 'vendor' => 'Percetakan Maju', 'description' => 'Cetak brosur dan banner promosi', 'days_ago' => 25],
            ['category' => 'rent', 'amount' => 5000000, 'vendor' => 'Pemilik Ruko', 'description' => 'Sewa tempat usaha bulan ini', 'days_ago' => 3],
            ['category' => 'equipment', 'amount' => 1200000, 'vendor' => 'Toko Elektronik', 'description' => 'Pembelian kipas angin dan rak', 'days_ago' => 18],
            ['category' => 'transportation', 'amount' => 150000, 'vendor' => 'Bensin & Parkir', 'description' => 'Biaya antar jemput laundry', 'days_ago' => 10],
            ['category' => 'other', 'amount' => 85000, 'vendor' => 'ATK Jaya', 'description' => 'Alat tulis kantor dan nota', 'days_ago' => 22],
            ['category' => 'maintenance', 'amount' => 420000, 'vendor' => 'Teknisi AC', 'description' => 'Service AC dan pembersihan', 'days_ago' => 14],
        ];
        
        $expenseCounter = 1;
        foreach ($expensesData as $expenseData) {
            $expenseDate = now()->subDays($expenseData['days_ago']);
            Expense::create([
                'expense_number' => 'EXP-' . $expenseDate->format('Ymd') . '-' . str_pad($expenseCounter++, 4, '0', STR_PAD_LEFT),
                'category' => $expenseData['category'],
                'amount' => $expenseData['amount'],
                'vendor' => $expenseData['vendor'],
                'description' => $expenseData['description'],
                'expense_date' => $expenseDate->toDateString(),
                'receipt_path' => null,
                'created_at' => $expenseDate,
            ]);
        }
    }

    private function seedLoyaltyTransactions()
    {
        $this->command->info('Seeding Loyalty Transactions...');
        
        foreach ($this->orders as $order) {
            if ($order->payment_status === 'paid' && $order->status === 'completed') {
                // Earn points: 10 base + 2 per 10,000 IDR
                $basePoints = 10;
                $amountPoints = floor($order->total / 10000) * 2;
                $totalPoints = $basePoints + $amountPoints;
                
                LoyaltyTransaction::create([
                    'customer_id' => $order->customer_id,
                    'order_id' => $order->id,
                    'type' => 'earned',
                    'points' => $totalPoints,
                    'description' => "Poin dari order {$order->order_number}",
                    'created_at' => $order->updated_at,
                ]);
            }
        }
        
        // Simulate some reward redemptions
        $completedOrders = Order::where('status', 'completed')->where('payment_status', 'paid')->limit(5)->get();
        foreach ($completedOrders as $order) {
            if (rand(0, 2) === 0) { // 33% chance
                $redeemPoints = rand(50, 100);
                LoyaltyTransaction::create([
                    'customer_id' => $order->customer_id,
                    'order_id' => $order->id,
                    'type' => 'redeemed',
                    'points' => $redeemPoints,
                    'description' => "Tukar poin untuk diskon order {$order->order_number}",
                    'created_at' => $order->created_at,
                ]);
            }
        }
        
        // Update customer loyalty points
        foreach ($this->customers as $customer) {
            $earned = LoyaltyTransaction::where('customer_id', $customer->id)
                ->where('type', 'earned')
                ->sum('points');
            $redeemed = LoyaltyTransaction::where('customer_id', $customer->id)
                ->whereIn('type', ['redeemed', 'expired'])
                ->sum('points');
            
            $customer->update(['loyalty_points' => $earned - $redeemed]);
        }
    }

    private function seedCustomerRewards()
    {
        $this->command->info('Seeding Customer Rewards...');
        
        // Grant some available rewards to customers with high points
        $highPointCustomers = Customer::where('loyalty_points', '>', 50)->get();
        foreach ($highPointCustomers as $customer) {
            // Give 1-2 available rewards
            $numRewards = rand(1, 2);
            $availableRewards = Reward::where('is_active', true)
                ->where('points_required', '<=', $customer->loyalty_points)
                ->inRandomOrder()
                ->limit($numRewards)
                ->get();
            
            foreach ($availableRewards as $reward) {
                CustomerReward::create([
                    'customer_id' => $customer->id,
                    'reward_id' => $reward->id,
                    'status' => 'available',
                    'expires_at' => now()->addMonths(3),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
        
        // Mark some rewards as redeemed (linked to orders with discounts)
        $ordersWithDiscount = Order::where('discount', '>', 0)->limit(3)->get();
        foreach ($ordersWithDiscount as $order) {
            $reward = Reward::where('points_required', '<=', 100)->inRandomOrder()->first();
            if ($reward) {
                CustomerReward::create([
                    'customer_id' => $order->customer_id,
                    'reward_id' => $reward->id,
                    'order_id' => $order->id,
                    'status' => 'redeemed',
                    'redeemed_at' => $order->created_at,
                    'expires_at' => $order->created_at->copy()->addMonths(3),
                    'created_at' => $order->created_at->subDays(rand(1, 10)),
                ]);
            }
        }
    }

    private function seedOrderStatusHistories()
    {
        $this->command->info('Seeding Order Status Histories...');
        
        foreach ($this->orders as $order) {
            $admin = $this->users[0];
            
            // Initial status
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'in_progress',
                'changed_by' => $admin->id,
                'changed_at' => $order->created_at,
                'notes' => 'Order diterima dan mulai diproses',
            ]);
            
            // Current status (if different)
            if ($order->status !== 'in_progress') {
                $changeTime = $order->created_at->copy()->addHours(rand(12, 72));
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'changed_by' => $this->users[array_rand($this->users)]->id,
                    'changed_at' => $changeTime,
                    'notes' => $this->getStatusChangeNote($order->status),
                ]);
            }
            
            // Add intermediate history for completed orders
            if ($order->status === 'completed') {
                $readyTime = $order->created_at->copy()->addHours(rand(24, 48));
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => 'ready',
                    'changed_by' => $this->users[array_rand($this->users)]->id,
                    'changed_at' => $readyTime,
                    'notes' => 'Laundry sudah selesai dan siap diambil',
                ]);
                
                // Update final completion time
                OrderStatusHistory::where('order_id', $order->id)
                    ->where('status', 'completed')
                    ->update(['changed_at' => $readyTime->copy()->addHours(rand(2, 24))]);
            }
        }
    }

    private function getStatusChangeNote($status)
    {
        $notes = [
            'in_progress' => 'Sedang dalam proses pencucian',
            'ready' => 'Laundry sudah selesai dan siap diambil',
            'completed' => 'Pesanan telah diambil pelanggan',
            'cancelled' => 'Pesanan dibatalkan atas permintaan pelanggan',
        ];
        
        return $notes[$status] ?? 'Status diupdate';
    }

    private function seedInventoryUsage()
    {
        $this->command->info('Seeding Inventory Usage...');
        
        // Get detergent and softener items
        $detergent = InventoryItem::where('category', 'detergent')->first();
        $softener = InventoryItem::where('category', 'fabric_softener')->first();
        $bags = InventoryItem::where('category', 'bags')->first();
        
        // For completed orders, deduct inventory
        $completedOrders = Order::where('status', 'completed')->get();
        foreach ($completedOrders as $order) {
            $totalWeight = $order->items()->sum('quantity');
            
            // Deduct detergent (0.05kg per kg of laundry)
            if ($detergent && $totalWeight > 0) {
                $usageQty = $totalWeight * 0.05;
                $newBalance = $detergent->quantity - $usageQty;
                
                InventoryTransaction::create([
                    'inventory_item_id' => $detergent->id,
                    'type' => 'usage',
                    'quantity' => $usageQty,
                    'balance_after' => $newBalance,
                    'reference_number' => $order->order_number,
                    'notes' => "Pemakaian untuk order {$order->order_number}",
                    'order_id' => $order->id,
                    'created_at' => $order->created_at->addHours(2),
                ]);
                
                $detergent->update(['quantity' => $newBalance]);
            }
            
            // Deduct softener (0.02L per kg)
            if ($softener && $totalWeight > 0) {
                $usageQty = $totalWeight * 0.02;
                $newBalance = $softener->quantity - $usageQty;
                
                InventoryTransaction::create([
                    'inventory_item_id' => $softener->id,
                    'type' => 'usage',
                    'quantity' => $usageQty,
                    'balance_after' => $newBalance,
                    'reference_number' => $order->order_number,
                    'notes' => "Pemakaian untuk order {$order->order_number}",
                    'order_id' => $order->id,
                    'created_at' => $order->created_at->addHours(2),
                ]);
                
                $softener->update(['quantity' => $newBalance]);
            }
            
            // Deduct bags (1 per order)
            if ($bags) {
                $newBalance = $bags->quantity - 1;
                
                InventoryTransaction::create([
                    'inventory_item_id' => $bags->id,
                    'type' => 'usage',
                    'quantity' => 1,
                    'balance_after' => $newBalance,
                    'reference_number' => $order->order_number,
                    'notes' => "Plastik untuk order {$order->order_number}",
                    'order_id' => $order->id,
                    'created_at' => $order->updated_at,
                ]);
                
                $bags->update(['quantity' => $newBalance]);
            }
        }
    }
}
