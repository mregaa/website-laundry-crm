@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">Laporan Keuangan</h1>
        <a href="{{ route('financial.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Report Parameters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('financial.report') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 mb-2">Tipe Laporan *</label>
                <select name="type" class="w-full border rounded px-3 py-2" required>
                    <option value="daily" {{ ($validated['type'] ?? '') === 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ ($validated['type'] ?? '') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ ($validated['type'] ?? '') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="custom" {{ ($validated['type'] ?? '') === 'custom' ? 'selected' : '' }}>Rentang Kustom</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 mb-2">Start Tanggal *</label>
                <input type="date" name="start_date" 
                       value="{{ $validated['start_date'] ?? date('Y-m-01') }}" 
                       class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 mb-2">End Tanggal *</label>
                <input type="date" name="end_date" 
                       value="{{ $validated['end_date'] ?? date('Y-m-d') }}" 
                       class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-chart-bar"></i> Buat Laporan
                </button>
            </div>
        </form>
    </div>

    @if(isset($stats))
        <!-- Export Button and Summary -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Ringkasan Laporan</h2>
                <p class="text-gray-600">Periode: {{ \Carbon\Carbon::parse($validated['start_date'])->locale('id')->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($validated['end_date'])->locale('id')->translatedFormat('d F Y') }}</p>
            </div>
            <a href="{{ route('financial.report.export', request()->all()) }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition">
                <i class="fas fa-file-excel"></i> Export Laporan Excel
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-green-600">{{ rupiah($stats['total_income']) }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-red-600">{{ rupiah($stats['total_expenses']) }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Laba Bersih</p>
                        <p class="text-2xl font-bold {{ $stats['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ rupiah($stats['profit']) }}
                        </p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Margin Laba</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['profit_margin'], 1) }}%</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-percentage text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendapatan Harian Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Pendapatan Harian</h2>
            
            <!-- Mobile Card View -->
            <div class="md:hidden space-y-3">
                @php $maxPendapatan = $dailyRevenue->max('total') ?? 1; @endphp
                @forelse($dailyRevenue as $day)
                    <div class="border border-gray-200 rounded-lg p-4 bg-green-50">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-sm font-medium text-gray-700">{{ \Carbon\Carbon::parse($day->date)->locale('id')->translatedFormat('d F Y') }}</p>
                            <p class="text-lg font-bold text-green-600">{{ rupiah($day->total) }}</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-green-500 h-4 rounded-full" style="width: {{ ($day->total / $maxPendapatan) * 100 }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-bar text-4xl mb-2"></i>
                        <p>Tidak ada data pendapatan</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-right">Pendapatan</th>
                            <th class="px-4 py-2 text-left">Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxPendapatan = $dailyRevenue->max('total') ?? 1; @endphp
                        @forelse($dailyRevenue as $day)
                            <tr class="border-t hover:bg-gray-50 transition">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($day->date)->locale('id')->translatedFormat('d F Y') }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ rupiah($day->total) }}</td>
                                <td class="px-4 py-3">
                                    <div class="bg-green-200 rounded" style="width: {{ ($day->total / $maxPendapatan) * 100 }}%; height: 20px;"></div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">Tidak ada data pendapatan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pengeluaran per Kategori -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Pengeluaran per Kategori</h2>
            
            <!-- Mobile Card View -->
            <div class="md:hidden space-y-3">
                @forelse($expensesByCategory as $expense)
                    <div class="border border-gray-200 rounded-lg p-4 bg-red-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $stats['total_expenses'] > 0 ? number_format(($expense->total / $stats['total_expenses']) * 100, 1) : 0 }}% dari total
                                </p>
                            </div>
                            <p class="text-lg font-bold text-red-600">{{ rupiah($expense->total) }}</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 mt-3">
                            <div class="bg-red-500 h-4 rounded-full" 
                                 style="width: {{ $stats['total_expenses'] > 0 ? ($expense->total / $stats['total_expenses']) * 100 : 0 }}%;">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-wallet text-4xl mb-2"></i>
                        <p>Tidak ada data pengeluaran</p>
                    </div>
                @endforelse
                
                @if($expensesByCategory->count() > 0)
                <div class="border-t-2 border-gray-300 pt-3 mt-3 bg-white rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total Pengeluaran</span>
                        <span class="text-xl font-bold text-red-600">{{ rupiah($stats['total_expenses']) }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Kategori</th>
                            <th class="px-4 py-2 text-right">Jumlah</th>
                            <th class="px-4 py-2 text-right">Persentase</th>
                            <th class="px-4 py-2 text-left">Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expensesByCategory as $expense)
                            <tr class="border-t hover:bg-gray-50 transition">
                                <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ rupiah($expense->total) }}</td>
                                <td class="px-4 py-3 text-right">
                                    {{ $stats['total_expenses'] > 0 ? number_format(($expense->total / $stats['total_expenses']) * 100, 1) : 0 }}%
                                </td>
                                <td class="px-4 py-3">
                                    <div class="bg-red-200 rounded" 
                                         style="width: {{ $stats['total_expenses'] > 0 ? ($expense->total / $stats['total_expenses']) * 100 : 0 }}%; height: 20px;">
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada data pengeluaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td class="px-4 py-3">Total</td>
                            <td class="px-4 py-3 text-right">{{ rupiah($stats['total_expenses']) }}</td>
                            <td class="px-4 py-3 text-right">100%</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
