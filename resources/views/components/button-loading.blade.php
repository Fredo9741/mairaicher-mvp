{{-- Bouton avec indicateur de chargement intégré --}}
@props([
    'type' => 'submit',
    'variant' => 'primary', // 'primary', 'secondary', 'danger'
    'size' => 'default', // 'small', 'default', 'large'
    'loadingText' => 'Chargement...',
    'wire' => null, // Pour Livewire: 'target="methodName"'
])

@php
    $variantClasses = [
        'primary' => 'bg-gradient-to-r from-emerald-600 to-green-600 text-white hover:from-emerald-700 hover:to-green-700',
        'secondary' => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
    ];

    $sizeClasses = [
        'small' => 'px-4 py-2 text-sm',
        'default' => 'px-6 py-3',
        'large' => 'px-8 py-4 text-lg',
    ];

    $classes = ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['default']);
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "$classes rounded-xl font-semibold transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none"]) }}
    @if($wire)
        wire:loading.attr="disabled"
    @endif
>
    {{-- Contenu normal du bouton --}}
    <span @if($wire) wire:loading.remove wire:target="{{ $wire }}" @endif class="flex items-center gap-2">
        {{ $slot }}
    </span>

    {{-- Indicateur de chargement (caché par défaut) --}}
    @if($wire)
        <span wire:loading wire:target="{{ $wire }}" class="flex items-center gap-2">
            <x-loading-spinner size="default" color="white" />
            <span>{{ $loadingText }}</span>
        </span>
    @endif
</button>
