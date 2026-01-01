@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">Transaksi</h1>
        <div class="space-x-2">
            <a href="{{ route('financial.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('financial.transactions') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 mb-2">Status</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pendapatan</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('financial.transactions') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-times"></i> Bersihkan
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Cards (< md) -->
    <div class="md:hidden space-y-4">
        @forelse($transactions as $transaction)
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 {{ $transaction->type === 'income' ? 'border-green-500' : 'border-red-500' }}">
            
            <!-- Transaction Number & Type -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="text-lg font-bold text-gray-900">{{ $transaction->transaction_number }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="far fa-calendar mr-1"></i>
                        {{ $transaction->transaction_date->format('d M Y') }}
                    </p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ statusLabel($transaction->type, 'transaction') }}
                </span>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <p class="text-xs text-gray-500 mb-1">Kategori</p>
                <span class="px-2 py-1 rounded text-sm bg-gray-100 text-gray-700">
                    {{ ucfirst(str_replace('_', ' ', $transaction->category)) }}
                </span>
            </div>

            <!-- Description -->
            <div class="mb-3 pb-3 border-b border-gray-200">
                <p class="text-xs text-gray-500 mb-1">Deskripsi</p>
                <p class="text-sm text-gray-900">{{ $transaction->description }}</p>
            </div>

            <!-- Amount -->
            <div class="text-right">
                <p class="text-xs text-gray-500 mb-1">Jumlah</p>
                <p class="text-2xl font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type === 'income' ? '+' : '-' }}{{ rupiah($transaction->amount) }}
                </p>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-md p-12 text-center text-gray-500">
            <i class="fas fa-receipt text-5xl mb-4 text-gray-400"></i>
            <p class="text-lg font-medium">Tidak ada transaksi ditemukan</p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table (>= md) -->
    <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="px-4 py-3 text-left">Nomor Transaksi</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Tipe</th>
                    <th class="px-4 py-3 text-left">Kategori</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ $transaction->transaction_number }}</td>
                        <td class="px-4 py-3">{{ $transaction->transaction_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-sm
                                {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ statusLabel($transaction->type, 'transaction') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</td>
                        <td class="px-4 py-3">{{ $transaction->description }}</td>
                        <td class="px-4 py-3 text-right font-semibold
                            {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}{{ rupiah($transaction->amount) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada transaksi ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
