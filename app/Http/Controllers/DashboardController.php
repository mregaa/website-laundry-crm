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
    public function index(Request $request)
    {
        // Get date range from request or use defaults
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Validate dates
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $request->validate([
                'start_date' => 'required|date|before_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:today',
            ]);
        }

        // Today's statistics
        $todayStats = [
            'orders' => Order::whereDate('created_at', today())->count(),
            'deadline_today' => Order::whereDate('delivery_date', today())->count(),
            'revenue' => Transaction::where('type', 'income')
                                   ->whereDate('transaction_date', today())
                                   ->sum('amount'),
            'completed_orders' => Order::where('status', 'completed')
                                       ->whereDate('updated_at', today())
                                       ->count(),
            'pending_payments' => Order::whereIn('payment_status', ['pending', 'partial'])
                                       ->whereDate('created_at', today())
                                       ->sum(DB::raw('total - paid_amount')),
        ];

        // Statistics for selected date range
        $monthStats = [
            'orders' => Order::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])->count(),
            'revenue' => Transaction::where('type', 'income')
                                   ->whereBetween('transaction_date', [$startDate, $endDate])
                                   ->sum('amount'),
            'expenses' => Transaction::where('type', 'expense')
                                    ->whereBetween('transaction_date', [$startDate, $endDate])
                                    ->sum('amount'),
            'new_customers' => Customer::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])->count(),
        ];

        //last 30 days statistics
        $monthStats['profit'] = $monthStats['revenue'] - $monthStats['expenses'];
        
        // Order status distribution
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->whereIn('status', ['in_progress', 'ready', 'completed', 'cancelled'])
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Recent orders
        $recentOrders = Order::with('customer')
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->latest()
            ->limit(10)
            ->get();

        // Pending payments
        $pendingPayments = Order::whereIn('payment_status', ['pending', 'partial'])
            ->with('customer')
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
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
            ->whereBetween('orders.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone', 'customers.loyalty_points')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // Revenue trend (last 7 days)
        $revenueTrend = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate . ' 23:59:59'])
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Overdue orders
        $overdueOrders = Order::where('delivery_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
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
            'overdueOrders',
            'startDate',
            'endDate'
        ));
    }
}
