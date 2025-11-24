<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Today's statistics
        $todayStats = [
            'orders' => Order::whereDate('created_at', today())->count(),
            'revenue' => Transaction::where('type', 'income')
                                   ->whereDate('transaction_date', today())
                                   ->sum('amount'),
            'new_customers' => Customer::whereDate('created_at', today())->count(),
            'completed_orders' => Order::where('status', 'completed')
                                       ->whereDate('updated_at', today())
                                       ->count(),
        ];

        // This month's statistics
        $monthStats = [
            'orders' => Order::whereMonth('created_at', now()->month)
                           ->whereYear('created_at', now()->year)
                           ->count(),
            'revenue' => Transaction::where('type', 'income')
                                   ->whereMonth('transaction_date', now()->month)
                                   ->whereYear('transaction_date', now()->year)
                                   ->sum('amount'),
            'expenses' => Transaction::where('type', 'expense')
                                    ->whereMonth('transaction_date', now()->month)
                                    ->whereYear('transaction_date', now()->year)
                                    ->sum('amount'),
            'new_customers' => Customer::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->count(),
        ];

        $monthStats['profit'] = $monthStats['revenue'] - $monthStats['expenses'];

        // Order status distribution
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->whereIn('status', ['received', 'washing', 'drying', 'ironing', 'ready'])
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Recent orders
        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(10)
            ->get();

        // Pending payments
        $pendingPayments = Order::whereIn('payment_status', ['pending', 'partial'])
            ->with('customer')
            ->orderBy('delivery_date')
            ->limit(10)
            ->get();

        // Low stock items
        $lowStockItems = InventoryItem::where('is_active', true)
            ->whereRaw('quantity <= reorder_level')
            ->orderBy('quantity')
            ->get();

        // Top customers this month
        $topCustomers = Customer::select('customers.id', 'customers.name', 'customers.email', 'customers.phone', 'customers.loyalty_points', DB::raw('SUM(orders.total) as total_spent'))
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.payment_status', 'paid')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone', 'customers.loyalty_points')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // Revenue trend (last 7 days)
        $revenueTrend = Transaction::where('type', 'income')
            ->where('transaction_date', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Overdue orders
        $overdueOrders = Order::where('delivery_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('customer')
            ->count();

        return view('dashboard', compact(
            'todayStats',
            'monthStats',
            'ordersByStatus',
            'recentOrders',
            'pendingPayments',
            'lowStockItems',
            'topCustomers',
            'revenueTrend',
            'overdueOrders'
        ));
    }
}
