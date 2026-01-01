@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header dengan Quick Actions -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Keuangan</h1>
            <p class="text-gray-600">Kelola transaksi, pengeluaran, dan lihat laporan keuangan bisnis laundry Anda</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('financial.create-expense') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-plus mr-2"></i> Tambah Pengeluaran
            </a>
            <a href="{{ route('financial.report') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-chart-bar mr-2"></i> Laporan Lengkap
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600">{{ rupiah($summary['total_income'] ?? 0) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-600">{{ rupiah($summary['total_expenses'] ?? 0) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Keuntungan Bersih</p>
                    <p class="text-2xl font-bold text-blue-600">{{ rupiah(($summary['total_income'] ?? 0) - ($summary['total_expenses'] ?? 0)) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pembayaran Tertunda</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ rupiah($summary['pending_payments'] ?? 0) }}</p>
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
                    <i class="fas fa-exchange-alt mr-2"></i> Transaksi
                </a>
                <a href="{{ route('financial.index', ['tab' => 'expenses']) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request('tab') == 'expenses' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-receipt mr-2"></i> Pengeluaran
                </a>
                <a href="{{ route('financial.index', ['tab' => 'report']) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request('tab') == 'report' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-chart-line mr-2"></i> Laporan
                </a>
            </nav>
        </div>
    </div>

    <!-- Transactions Tab -->
    @if(!request('tab') || request('tab') == 'transactions')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Transaksi Terbaru</h2>
            <a href="{{ route('financial.transactions') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-list mr-2"></i> Lihat Semua Transaksi
            </a>
        </div>
        
        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3 p-4">
            @forelse($transactions as $transaction)
                <div class="border rounded-lg p-4 {{ $transaction->type == 'income' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-xs text-gray-500">{{ $transaction->transaction_date->format('d M Y') }}</p>
                            <p class="font-semibold text-gray-900 mt-1">{{ $transaction->description }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $transaction->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ statusLabel($transaction->type, 'transaction') }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        {{ ucfirst($transaction->category) }}
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-bold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'income' ? '+' : '-' }}{{ rupiah($transaction->amount) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-file-invoice-dollar text-5xl mb-3"></i>
                    <p class="text-lg">Tidak ada transaksi ditemukan</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->transaction_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $transaction->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ statusLabel($transaction->type, 'transaction') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($transaction->category) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'income' ? '+' : '-' }}{{ rupiah($transaction->amount) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                            <p class="text-lg">Tidak ada transaksi ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $transactions->links() }}
        </div>
    </div>
    @endif

    <!-- Expenses Tab -->
    @if(request('tab') == 'expenses')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Pengeluaran Terbaru</h2>
            <div class="space-x-2">
                <a href="{{ route('financial.create-expense') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Pengeluaran
                </a>
                <a href="{{ route('financial.expenses') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-list mr-2"></i> Lihat Semua
                </a>
            </div>
        </div>
        
        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3 p-4">
            @forelse($expenses as $expense)
                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-xs text-gray-500">{{ $expense->expense_date->format('d M Y') }}</p>
                            <p class="font-semibold text-gray-900 mt-1">{{ ucfirst($expense->category) }}</p>
                        </div>
                        <span class="text-lg font-bold text-red-600">{{ rupiah($expense->amount) }}</span>
                    </div>
                    <p class="text-sm text-gray-700 mb-2">{{ $expense->description }}</p>
                    <div class="pt-2 border-t border-red-300 text-sm text-gray-600">
                        <i class="fas fa-credit-card mr-1"></i>{{ ucfirst($expense->payment_method) }}
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-receipt text-5xl mb-3"></i>
                    <p class="text-lg">Tidak ada pengeluaran ditemukan</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $expense->expense_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ucfirst($expense->category) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                            {{ rupiah($expense->amount) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($expense->payment_method) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-receipt text-4xl mb-3"></i>
                            <p class="text-lg">Tidak ada pengeluaran ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $expenses->links() }}
        </div>
    </div>
    @endif

    <!-- Reports Tab -->
    @if(request('tab') == 'report')
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Ringkasan Laporan Keuangan</h2>
            <a href="{{ route('financial.report') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-chart-bar mr-2"></i> Lihat Laporan Lengkap
            </a>
        </div>
        
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Untuk laporan detail dengan filter tanggal, grafik, dan ekspor CSV, silakan klik tombol "Lihat Laporan Lengkap" di atas.
            </p>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-700 font-medium">Total Pendapatan</span>
                <span class="text-green-600 font-bold">{{ rupiah($summary['total_income'] ?? 0) }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-700 font-medium">Total Pengeluaran</span>
                <span class="text-red-600 font-bold">{{ rupiah($summary['total_expenses'] ?? 0) }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-700 font-medium">Keuntungan Bersih</span>
                <span class="text-blue-600 font-bold text-xl">
                    {{ rupiah(($summary['total_income'] ?? 0) - ($summary['total_expenses'] ?? 0)) }}
                </span>
            </div>
            <div class="flex justify-between items-center py-3">
                <span class="text-gray-700 font-medium">Margin Keuntungan</span>
                <span class="text-purple-600 font-bold">
                    @php
                        $income = $summary['total_income'] ?? 0;
                        $margin = $income > 0 ? ((($income - ($summary['total_expenses'] ?? 0)) / $income) * 100) : 0;
                    @endphp
                    {{ number_format($margin, 1) }}%
                </span>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Pembayaran Tertunda</p>
                    <p class="text-lg font-bold text-yellow-600">{{ rupiah($summary['pending_payments'] ?? 0) }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Transaksi Bulan Ini</p>
                    <p class="text-lg font-bold text-blue-600">{{ $transactions->total() ?? 0 }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Pengeluaran Bulan Ini</p>
                    <p class="text-lg font-bold text-red-600">{{ $expenses->total() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
