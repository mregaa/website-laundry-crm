{{-- Card Component for Summary Cards --}}
@props([
    'title' => '',
    'value' => '',
    'icon' => 'fas fa-box',
    'iconBg' => 'bg-blue-100',
    'iconColor' => 'text-blue-600',
    'href' => null,
    'subtitle' => null
])

@php
    $cardClasses = $href 
        ? 'bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-all duration-200 cursor-pointer transform hover:-translate-y-1' 
        : 'bg-white rounded-xl shadow-md p-6';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $cardClasses]) }}>
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-gray-600 text-sm font-medium mb-1">{{ $title }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
                @if($subtitle)
                    <p class="text-xs text-gray-500 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="{{ $iconBg }} rounded-full p-4">
                <i class="{{ $icon }} {{ $iconColor }} text-2xl"></i>
            </div>
        </div>
    </a>
@else
    <div {{ $attributes->merge(['class' => $cardClasses]) }}>
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-gray-600 text-sm font-medium mb-1">{{ $title }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
                @if($subtitle)
                    <p class="text-xs text-gray-500 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="{{ $iconBg }} rounded-full p-4">
                <i class="{{ $icon }} {{ $iconColor }} text-2xl"></i>
            </div>
        </div>
    </div>
@endif
