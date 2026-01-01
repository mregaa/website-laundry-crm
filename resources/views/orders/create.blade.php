@extends('layouts.app')

@section('title', 'Tambah Order')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-plus-circle"></i> Tambah Pesanan Baru
            </h1>
        </div>

        <form method="POST" action="{{ route('orders.store') }}" id="orderForm" class="space-y-6">
            @csrf

            <!-- Customer Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <select name="customer_id" id="customer_id" required onchange="updateCustomerInfo()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('customer_id') border-red-500 @enderror">
                        <option value="">Pilih Pelanggan</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                    data-points="{{ $customer->loyalty_points }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p id="customer_points" class="text-sm text-gray-600 mt-1"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Layanan Express
                    </label>
                    <div class="flex items-center mt-3">
                        <input type="checkbox" name="express_service" value="1" {{ old('express_service') ? 'checked' : '' }}
                               class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 text-gray-700">
                            Tandai sebagai layanan express (+20% biaya tambahan)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Reward Selection -->
            <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-gift text-purple-600 text-xl mr-2"></i>
                    <label class="block text-sm font-medium text-gray-700">
                        Gunakan Hadiah (Opsional)
                    </label>
                </div>
                <select name="reward_id" id="reward_id" onchange="updateRewardInfo()"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @error('reward_id') border-red-500 @enderror">
                    <option value="">Tanpa hadiah - Bayar penuh</option>
                    @foreach($rewards as $reward)
                        <option value="{{ $reward->id }}" 
                                data-points="{{ $reward->points_required }}"
                                data-amount="{{ $reward->discount_amount ?? 0 }}"
                                data-percentage="{{ $reward->discount_percentage ?? 0 }}"
                                {{ old('reward_id') == $reward->id ? 'selected' : '' }}>
                            {{ $reward->name }} - {{ $reward->points_required }} poin
                            @if($reward->discount_amount)
                                ({{ rupiah($reward->discount_amount) }} potongan)
                            @elseif($reward->discount_percentage)
                                ({{ $reward->discount_percentage }}% potongan)
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('reward_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p id="reward_info" class="text-sm text-gray-600 mt-2"></p>
            </div>

            <!-- Tanggals -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pickup Tanggal
                    </label>
                    <input type="date" name="pickup_date" value="{{ old('pickup_date', date('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delivery Tanggal
                    </label>
                    <input type="date" name="delivery_date" value="{{ old('delivery_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Order Items -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Item Pesanan <span class="text-red-500">*</span>
                    </label>
                    <button type="button" onclick="addOrderItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>
                </div>

                <div id="orderItems" class="space-y-4">
                    <div class="order-item border rounded-lg p-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Layanan</label>
                                <select name="items[0][service_id]" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Layanan</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-unit="{{ $service->unit }}">
                                            {{ $service->name }} - ${{ number_format($service->price, 2) }}/{{ $service->unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                                <input type="number" name="items[0][quantity]" step="0.01" min="0.01" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                <input type="text" name="items[0][notes]" placeholder="Catatan opsional"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-1 flex items-end">
                                <button type="button" onclick="removeOrderItem(this)" class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Instructions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Instruksi Khusus
                </label>
                <textarea name="special_instructions" rows="3" placeholder="Persyaratan penanganan khusus..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('special_instructions') }}</textarea>
            </div>

            <!-- Kirim Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-save"></i> Tambah Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let itemIndex = 1;

function updateCustomerInfo() {
    const select = document.getElementById('customer_id');
    const option = select.options[select.selectedIndex];
    const points = option.getAttribute('data-points');
    const pointsDisplay = document.getElementById('customer_points');
    
    if (points) {
        pointsDisplay.textContent = `Poin Loyalitas: ${points}`;
        pointsDisplay.classList.remove('hidden');
    } else {
        pointsDisplay.textContent = '';
    }
    
    // Perbarui reward availability
    updateRewardInfo();
}

function updateRewardInfo() {
    const customerSelect = document.getElementById('customer_id');
    const rewardSelect = document.getElementById('reward_id');
    const rewardInfo = document.getElementById('reward_info');
    
    const customerOption = customerSelect.options[customerSelect.selectedIndex];
    const rewardOption = rewardSelect.options[rewardSelect.selectedIndex];
    
    const customerPoints = parseInt(customerOption.getAttribute('data-points')) || 0;
    const rewardPoints = parseInt(rewardOption.getAttribute('data-points')) || 0;
    
    if (rewardPoints > 0) {
        if (customerPoints >= rewardPoints) {
            rewardInfo.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle"></i> Anda memiliki cukup poin untuk hadiah ini!</span>';
        } else {
            rewardInfo.innerHTML = '<span class="text-red-600"><i class="fas fa-times-circle"></i> Poin tidak cukup. Anda memerlukan ' + rewardPoints + ' poin tetapi hanya memiliki ' + customerPoints + ' poin.</span>';
        }
    } else {
        rewardInfo.textContent = '';
    }
}

function addOrderItem() {
    const container = document.getElementById('orderItems');
    const newItem = `
        <div class="order-item border rounded-lg p-4 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Layanan</label>
                    <select name="items[${itemIndex}][service_id]" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Layanan</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-unit="{{ $service->unit }}">
                                {{ $service->name }} - ${{ number_format($service->price, 2) }}/{{ $service->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                    <input type="number" name="items[${itemIndex}][quantity]" step="0.01" min="0.01" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <input type="text" name="items[${itemIndex}][notes]" placeholder="Catatan opsional"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-1 flex items-end">
                    <button type="button" onclick="removeOrderItem(this)" class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newItem);
    itemIndex++;
}

function removeOrderItem(button) {
    const items = document.querySelectorAll('.order-item');
    if (items.length > 1) {
        button.closest('.order-item').remove();
    } else {
        alert('Minimal satu item diperlukan');
    }
}
</script>
@endpush
@endsection
