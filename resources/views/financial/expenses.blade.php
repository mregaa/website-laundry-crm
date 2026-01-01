@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">Pengeluaran</h1>
        <div class="space-x-2">
            <a href="{{ route('financial.create-expense') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                <i class="fas fa-plus"></i> Tambah Pengeluaran
            </a>
            <a href="{{ route('financial.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('financial.expenses') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 mb-2">Kategori</label>
                <select name="category" class="w-full border rounded px-3 py-2">
                    <option value="">Semua Kategori</option>
                    <option value="salary" {{ request('category') === 'salary' ? 'selected' : '' }}>Gaji</option>
                    <option value="utilities" {{ request('category') === 'utilities' ? 'selected' : '' }}>Utilitas</option>
                    <option value="supplies" {{ request('category') === 'supplies' ? 'selected' : '' }}>Perlengkapan</option>
                    <option value="maintenance" {{ request('category') === 'maintenance' ? 'selected' : '' }}>Pemeliharaan</option>
                    <option value="marketing" {{ request('category') === 'marketing' ? 'selected' : '' }}>Pemasaran</option>
                    <option value="rent" {{ request('category') === 'rent' ? 'selected' : '' }}>Sewa</option>
                    <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Peralatan</option>
                    <option value="transportation" {{ request('category') === 'transportation' ? 'selected' : '' }}>Transportasi</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Lainnya</option>
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
                <a href="{{ route('financial.expenses') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-times"></i> Bersihkan
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Cards (< md) -->
    <div class="md:hidden space-y-4">
        @forelse($expenses as $expense)
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
            
            <!-- Date & Category -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="text-lg font-bold text-gray-900">{{ $expense->expense_date->format('d M Y') }}</p>
                    <span class="inline-block mt-2 px-2 py-1 rounded text-sm bg-gray-100 text-gray-700">
                        {{ ucfirst(str_replace('_', ' ', $expense->category)) }}
                    </span>
                </div>
                @if($expense->receipt_path)
                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-file"></i> Bukti
                    </a>
                @endif
            </div>

            <!-- Vendor -->
            @if($expense->vendor)
            <div class="mb-3">
                <p class="text-xs text-gray-500 mb-1">Vendor</p>
                <p class="text-sm font-medium text-gray-900">{{ $expense->vendor }}</p>
            </div>
            @endif

            <!-- Description -->
            <div class="mb-3 pb-3 border-b border-gray-200">
                <p class="text-xs text-gray-500 mb-1">Deskripsi</p>
                <p class="text-sm text-gray-900">{{ $expense->description }}</p>
            </div>

            <!-- Amount -->
            <div class="text-right">
                <p class="text-xs text-gray-500 mb-1">Jumlah Pengeluaran</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ rupiah($expense->amount) }}
                </p>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-md p-12 text-center text-gray-500">
            <i class="fas fa-wallet text-5xl mb-4 text-gray-400"></i>
            <p class="text-lg font-medium">Tidak ada pengeluaran ditemukan</p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table (>= md) -->
    <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Kategori</th>
                    <th class="px-4 py-3 text-left">Vendor</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-right">Jumlah</th>
                    <th class="px-4 py-3 text-center">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-sm bg-gray-100">
                                {{ ucfirst(str_replace('_', ' ', $expense->category)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $expense->vendor ?? 'Tidak ada' }}</td>
                        <td class="px-4 py-3">{{ $expense->description }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-600">
                            {{ rupiah($expense->amount) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($expense->receipt_path)
                                <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" 
                                   class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-file"></i> Lihat
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada pengeluaran ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right">Total:</td>
                    <td class="px-4 py-3 text-right text-red-600">
                        {{ rupiah($expenses->sum('amount')) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
</div>
@endsection
