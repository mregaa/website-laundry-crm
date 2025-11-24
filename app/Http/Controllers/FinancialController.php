<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    /**
     * Display financial dashboard.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'transactions');
        
        $summary = [
            'total_income' => Transaction::where('type', 'income')
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount'),
            'total_expenses' => Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount'),
            'pending_payments' => Order::where('payment_status', '!=', 'paid')
                ->sum(DB::raw('total - paid_amount')),
        ];
        
        $transactions = Transaction::orderBy('transaction_date', 'desc')->paginate(20);
        $expenses = Expense::orderBy('expense_date', 'desc')->paginate(20);

        return view('financial.index', compact('summary', 'transactions', 'expenses'));
    }

    /**
     * Display transactions.
     */
    public function transactions(Request $request)
    {
        $query = Transaction::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('transaction_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $transactions = $query->latest('transaction_date')->paginate(20);

        return view('financial.transactions', compact('transactions'));
    }

    /**
     * Display expenses.
     */
    public function expenses(Request $request)
    {
        $query = Expense::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('expense_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $expenses = $query->latest('expense_date')->paginate(20);

        return view('financial.expenses', compact('expenses'));
    }

    /**
     * Show form to create expense.
     */
    public function createExpense()
    {
        return view('financial.create-expense');
    }

    /**
     * Store a new expense.
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:salary,utilities,supplies,maintenance,marketing,rent,equipment,transportation,other',
            'amount' => 'required|numeric|min:0.01',
            'vendor' => 'nullable|string|max:255',
            'description' => 'required|string',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        Expense::create($validated);

        // Create corresponding transaction
        Transaction::create([
            'transaction_number' => 'TXN-' . date('Ymd') . '-' . str_pad(Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
            'type' => 'expense',
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['expense_date'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('financial.expenses')
                        ->with('success', 'Expense recorded successfully.');
    }

    /**
     * Generate financial report.
     */
    public function report(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:daily,weekly,monthly,custom',
        ]);

        $stats = $this->getFinancialStats($validated['start_date'], $validated['end_date']);
        
        $dailyRevenue = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$validated['start_date'], $validated['end_date']])
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expensesByCategory = Expense::whereBetween('expense_date', [$validated['start_date'], $validated['end_date']])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return view('financial.report', compact('stats', 'dailyRevenue', 'expensesByCategory', 'validated'));
    }

    /**
     * Get financial statistics.
     */
    private function getFinancialStats($startDate, $endDate)
    {
        $totalIncome = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $totalExpenses = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $profit = $totalIncome - $totalExpenses;

        $ordersRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $pendingPayments = Order::whereIn('payment_status', ['pending', 'partial'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('total - paid_amount'));

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'profit' => $profit,
            'orders_revenue' => $ordersRevenue,
            'pending_payments' => $pendingPayments,
            'profit_margin' => $totalIncome > 0 ? ($profit / $totalIncome) * 100 : 0,
        ];
    }
}
