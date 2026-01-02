@extends('layouts.app')

@section('title', 'Ubah Order')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Ubah Order {{ $order->order_number }}</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Detail Pesanan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2">Pelanggan *</label>
                    <select name="customer_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Layanan Express</label>
                    <select name="express_service" class="w-full border rounded px-3 py-2">
                        <option value="0" {{ !$order->express_service ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ $order->express_service ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Pickup Tanggal</label>
                    <input type="date" name="pickup_date" 
                           value="{{ $order->pickup_date ? $order->pickup_date->format('Y-m-d') : '' }}" 
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Delivery Tanggal</label>
                    <input type="date" name="delivery_date" 
                           value="{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '' }}" 
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Diskon</label>
                    <input type="number" name="discount" step="0.01" min="0" 
                           value="{{ $order->discount }}" 
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-gray-700 mb-2">Instruksi Khusus</label>
                <textarea name="special_instructions" class="w-full border rounded px-3 py-2" rows="3">{{ $order->special_instructions }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Item Pesanan Saat Ini</h2>
            <p class="text-gray-600 mb-4">Catatan: Untuk mengubah item, silakan hapus pesanan ini dan buat pesanan baru.</p>
            
            <!-- Mobile Card View -->
            <div class="md:hidden space-y-3">
                @foreach($order->items as $item)
                    <div class="border border-gray-200 rounded-lg p-4 bg-blue-50">
                        <div class="font-semibold text-gray-900 mb-2">{{ $item->service->name }}</div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600">Jumlah:</span>
                                <span class="font-semibold">{{ $item->quantity }} {{ $item->service->unit }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-gray-600">Harga:</span>
                                <span class="font-semibold">Rp {{ number_format($item->unit_price, 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t border-blue-300 text-right">
                            <span class="text-gray-600 text-sm">Subtotal:</span>
                            <span class="font-bold text-blue-600">Rp {{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Layanan</th>
                            <th class="px-4 py-2 text-right">Jumlah</th>
                            <th class="px-4 py-2 text-right">Harga Satuan</th>
                            <th class="px-4 py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-t hover:bg-gray-50 transition">
                                <td class="px-4 py-3">{{ $item->service->name }}</td>
                                <td class="px-4 py-3 text-right">{{ $item->quantity }} {{ $item->service->unit }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('orders.show', $order) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                Batal
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                Perbarui Order
            </button>
        </div>
    </form>
</div>
@endsection
