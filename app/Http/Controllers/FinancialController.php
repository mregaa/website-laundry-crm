<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Order;
use App\Services\FinancialExportService;
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
                ->sum('amount') ?? 0,
            'total_expenses' => Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount') ?? 0,
            'pending_payments' => Order::where('payment_status', '!=', 'paid')
                ->sum(DB::raw('total - paid_amount')) ?? 0,
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

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $transactions = $query->latest('transaction_date')->paginate(20)->appends($request->all());

        return view('financial.transactions', compact('transactions'));
    }

    /**
     * Display expenses.
     */
    public function expenses(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $expenses = $query->latest('expense_date')->paginate(20)->appends($request->all());

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
            'amount' => 'required|numeric|min:0.01|max:999999999',
            'vendor' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
            'expense_date' => 'required|date|before_or_equal:today',
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
        // Provide defaults if not provided
        $defaultStartDate = now()->startOfMonth()->toDateString();
        $defaultEndDate = now()->toDateString();
        $defaultType = 'monthly';

        $validated = $request->validate([
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
            'type' => 'nullable|in:daily,weekly,monthly,custom',
        ]);

        // Use defaults if not provided
        $validated['start_date'] = $validated['start_date'] ?? $defaultStartDate;
        $validated['end_date'] = $validated['end_date'] ?? $defaultEndDate;
        $validated['type'] = $validated['type'] ?? $defaultType;

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
            ->sum('amount') ?? 0;

        $totalExpenses = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount') ?? 0;

        $profit = $totalIncome - $totalExpenses;

        $ordersRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total') ?? 0;

        $pendingPayments = Order::whereIn('payment_status', ['pending', 'partial'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('total - paid_amount')) ?? 0;

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'profit' => $profit,
            'orders_revenue' => $ordersRevenue,
            'pending_payments' => $pendingPayments,
            'profit_margin' => $totalIncome > 0 ? ($profit / $totalIncome) * 100 : 0,
        ];
    }
    
    /**
     * Export transactions to CSV.
     */
    public function exportTransactions(Request $request)
    {
        $query = Transaction::query();

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $query->whereBetween('transaction_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $filename = 'transaksi-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nomor Transaksi', 'Tanggal', 'Tipe', 'Kategori', 'Jumlah', 'Deskripsi']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_number,
                    $transaction->transaction_date->format('Y-m-d'),
                    $transaction->type,
                    $transaction->category,
                    $transaction->amount,
                    $transaction->description,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export expenses to CSV.
     */
    public function exportExpenses(Request $request)
    {
        $query = Expense::query();

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $query->whereBetween('expense_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $filename = 'pengeluaran-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Kategori', 'Vendor', 'Jumlah', 'Deskripsi']);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->expense_date->format('Y-m-d'),
                    $expense->category,
                    $expense->vendor ?? '-',
                    $expense->amount,
                    $expense->description,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export financial report to Excel (.xlsx) with 3 sheets:
     * 1. Ringkasan (Summary)
     * 2. Transaksi Laundry (Transactions)
     * 3. Pengeluaran (Expenses)
     */
    public function exportReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $filename = 'laporan-keuangan-laundry-' . date('Y-m-d') . '.xlsx';
        
        $exportService = new FinancialExportService();
        $spreadsheet = $exportService->generateReport($startDate, $endDate);
        $exportService->export($spreadsheet, $filename);
    }
}
