@props([
    'variant' => 'primary',
    'size' => 'sm',
])

@php
    $baseClasses = 'inline-flex items-center font-medium rounded-full';

    $variantClasses = match ($variant) {
        'primary' => 'bg-accent-100 text-accent-800 dark:bg-accent-900 dark:text-accent-300',
        'secondary' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
        'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'info' => 'bg-accent-100 text-accent-800 dark:bg-accent-800 dark:text-accent-300',
        default => 'bg-accent-100 text-accent-800 dark:bg-accent-900 dark:text-accent-300',
    };

    $sizeClasses = match ($size) {
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-0.5 text-sm',
        'md' => 'px-3 py-1 text-base',
        'lg' => 'px-3.5 py-1.5 text-lg',
        default => 'px-2.5 py-0.5 text-sm',
    };
@endphp

<span {{ $attributes->merge(['class' => "$baseClasses $variantClasses $sizeClasses"]) }}>
    {{ $slot }}
</span>
