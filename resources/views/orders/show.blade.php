@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">Order {{ $order->order_number }}</h1>
        <div class="flex flex-wrap gap-2">
            @if($order->status === 'ready' && $order->whatsappUrl())
                <a href="{{ $order->whatsappUrl() }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded inline-flex items-center">
                    <i class="fab fa-whatsapp text-lg mr-2"></i> WhatsApp Customer
                </a>
            @endif
            <a href="{{ route('orders.edit', $order) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                <i class="fas fa-edit"></i> Ubah
            </a>
            <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Pesanan</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Customer</p>
                        <p class="font-semibold">
                            <a href="{{ route('customers.index') }}" class="text-blue-600 hover:underline">
                                {{ $order->customer->name }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status</p>
                        <p>
                            <span class="px-3 py-1 rounded-full text-sm
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($order->status === 'ready') bg-blue-100 text-blue-800
                                @elseif($order->status === 'in_progress') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status Pembayaran</p>
                        <p>
                            <span class="px-3 py-1 rounded-full text-sm
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'partial') bg-yellow-100 text-yellow-800
                                @elseif($order->payment_status === 'refunded') bg-purple-100 text-purple-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ statusLabel($order->payment_status, 'payment') }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Layanan Express</p>
                        <p class="font-semibold">{{ $order->express_service ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Pickup Tanggal</p>
                        <p class="font-semibold">{{ $order->pickup_date ? $order->pickup_date->format('M d, Y') : 'Tidak ada' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Delivery Tanggal</p>
                        <p class="font-semibold">{{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'Tidak ada' }}</p>
                    </div>
                </div>
                @if($order->special_instructions)
                    <div class="mt-4">
                        <p class="text-gray-600">Instruksi Khusus</p>
                        <p class="font-semibold">{{ $order->special_instructions }}</p>
                    </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Item Pesanan</h2>
                
                <!-- Mobile Card View -->
                <div class="md:hidden space-y-3">
                    @foreach($order->items as $item)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="font-semibold text-gray-900 mb-2">{{ $item->service->name }}</div>
                            @if($item->notes)
                                <p class="text-xs text-gray-500 mb-3">{{ $item->notes }}</p>
                            @endif
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
                            <div class="mt-2 pt-2 border-t border-gray-300 text-right">
                                <span class="text-gray-600 text-sm">Subtotal:</span>
                                <span class="font-bold text-blue-600">Rp {{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        </div>
                    @endforeach

                    <!-- Mobile Summary -->
                    <div class="border-t-2 border-gray-300 pt-3 mt-3 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold">Rp {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="flex justify-between text-sm">
                            <div>
                                <span class="text-gray-600">Diskon:</span>
                                @php
                                    $customerReward = $order->customer->customerRewards()->where('order_id', $order->id)->with('reward')->first();
                                @endphp
                                @if($customerReward)
                                    <br><small class="text-purple-600">
                                        <i class="fas fa-gift"></i> {{ $customerReward->reward->name }}
                                    </small>
                                @endif
                            </div>
                            <span class="font-semibold text-red-600">-Rp {{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Pajak:</span>
                            <span class="font-semibold">Rp {{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Total:</span>
                            <span class="text-blue-600">Rp {{ number_format($order->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumlah Dibayar:</span>
                            <span class="font-semibold text-green-600">Rp {{ number_format($order->paid_amount, 2) }}</span>
                        </div>
                        @if($order->getRemainingBalance() > 0)
                        <div class="flex justify-between text-sm font-bold text-red-600">
                            <span>Sisa Saldo:</span>
                            <span>Rp {{ number_format($order->getRemainingBalance(), 2) }}</span>
                        </div>
                        @endif
                    </div>
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
                                    <td class="px-4 py-3">
                                        {{ $item->service->name }}
                                        @if($item->notes)
                                            <br><small class="text-gray-500">{{ $item->notes }}</small>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ $item->quantity }} {{ $item->service->unit }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right">Subtotal:</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right">
                                    Diskon:
                                    @php
                                        $customerReward = $order->customer->customerRewards()->where('order_id', $order->id)->with('reward')->first();
                                    @endphp
                                    @if($customerReward)
                                        <br><small class="text-purple-600">
                                            <i class="fas fa-gift"></i> {{ $customerReward->reward->name }}
                                        </small>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right text-red-600">-Rp {{ number_format($order->discount, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right">Pajak:</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($order->tax, 2) }}</td>
                            </tr>
                            <tr class="text-lg">
                                <td colspan="3" class="px-4 py-3 text-right">Total:</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($order->total, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right">Jumlah Dibayar:</td>
                                <td class="px-4 py-2 text-right text-green-600">Rp {{ number_format($order->paid_amount, 2) }}</td>
                            </tr>
                            @if($order->getRemainingBalance() > 0)
                            <tr class="text-red-600">
                                <td colspan="3" class="px-4 py-2 text-right">Sisa Saldo:</td>
                                <td class="px-4 py-2 text-right font-bold">Rp {{ number_format($order->getRemainingBalance(), 2) }}</td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payments -->
            @if($order->payments->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Riwayat Pembayaran</h2>
                
                <!-- Mobile Card View -->
                <div class="md:hidden space-y-3">
                    @foreach($order->payments as $payment)
                        <div class="border border-gray-200 rounded-lg p-4 bg-green-50">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs text-gray-500">{{ $payment->paid_at->format('M d, Y H:i') }}</p>
                                    <p class="font-semibold text-sm mt-1">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                </div>
                                <p class="text-lg font-bold text-green-600">Rp {{ number_format($payment->amount, 2) }}</p>
                            </div>
                            @if($payment->notes)
                                <div class="mt-2 pt-2 border-t border-gray-300">
                                    <p class="text-xs text-gray-600">Catatan: {{ $payment->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-left">Metode</th>
                                <th class="px-4 py-2 text-right">Jumlah</th>
                                <th class="px-4 py-2 text-left">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->payments as $payment)
                                <tr class="border-t hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">{{ $payment->paid_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-3">{{ $payment->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Status History -->
            @if($order->statusHistories->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Riwayat Status</h2>
                <div class="space-y-3">
                    @foreach($order->statusHistories as $history)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-blue-500 mr-3"></div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                <p class="text-sm text-gray-600">{{ $history->changed_at->format('M d, Y H:i') }}</p>
                                @if($history->notes)
                                    <p class="text-sm mt-1">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Aksi Panel -->
        <div class="lg:col-span-1">
            <!-- Perbarui Status -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Perbarui Status</h2>
                <form action="{{ route('orders.update-status', $order) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border rounded px-3 py-2" required>
                            <option value="in_progress" {{ $order->status === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Siap Diambil</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" class="w-full border rounded px-3 py-2" rows="3"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Perbarui Status
                    </button>
                </form>
            </div>

            <!-- Add Payment -->
            @if($order->getRemainingBalance() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Add Payment</h2>
                <form action="{{ route('orders.add-payment', $order) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Jumlah</label>
                        <input type="number" name="amount" step="0.01" max="{{ $order->getRemainingBalance() }}" 
                               value="{{ $order->getRemainingBalance() }}" 
                               class="w-full border rounded px-3 py-2" required>
                        <small class="text-gray-500">Sisa: Rp {{ number_format($order->getRemainingBalance(), 2) }}</small>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full border rounded px-3 py-2" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="e-wallet">E-Wallet</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" class="w-full border rounded px-3 py-2" rows="2"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-plus"></i> Add Payment
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
