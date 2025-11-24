@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Financial Management</h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Income</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($summary['total_income'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Expenses</p>
                    <p class="text-2xl font-bold text-red-600">${{ number_format($summary['total_expenses'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Net Profit</p>
                    <p class="text-2xl font-bold text-blue-600">${{ number_format(($summary['total_income'] ?? 0) - ($summary['total_expenses'] ?? 0), 2) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Payments</p>
                    <p class="text-2xl font-bold text-yellow-600">${{ number_format($summary['pending_payments'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="border-b">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('financial.index', ['tab' => 'transactions']) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ (!request('tab') || request('tab') == 'transactions') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Transactions
                </a>
                <a href="{{ route('financial.index', ['tab' => 'expenses']) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request('tab') == 'expenses' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Expenses
                </a>
                <a href="{{ route('financial.index', ['tab' => 'report']) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request('tab') == 'report' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Reports
                </a>
            </nav>
        </div>
    </div>

    <!-- Transactions Tab -->
    @if(!request('tab') || request('tab') == 'transactions')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Recent Transactions</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $transaction->transaction_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $transaction->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ ucfirst($transaction->category) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                        <p class="text-lg">No transactions found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $transactions->links() }}
        </div>
    </div>
    @endif

    <!-- Expenses Tab -->
    @if(request('tab') == 'expenses')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Expenses</h2>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-plus mr-2"></i>Add Expense
            </button>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($expenses as $expense)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $expense->expense_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ ucfirst($expense->category) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                        ${{ number_format($expense->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ ucfirst($expense->payment_method) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-receipt text-4xl mb-3"></i>
                        <p class="text-lg">No expenses found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $expenses->links() }}
        </div>
    </div>
    @endif

    <!-- Reports Tab -->
    @if(request('tab') == 'report')
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Financial Report</h2>
        <div class="space-y-4">
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-700 font-medium">Monthly Income</span>
                <span class="text-green-600 font-bold">${{ number_format($summary['total_income'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-700 font-medium">Monthly Expenses</span>
                <span class="text-red-600 font-bold">${{ number_format($summary['total_expenses'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-700 font-medium">Net Profit/Loss</span>
                <span class="text-blue-600 font-bold text-xl">
                    ${{ number_format(($summary['total_income'] ?? 0) - ($summary['total_expenses'] ?? 0), 2) }}
                </span>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
