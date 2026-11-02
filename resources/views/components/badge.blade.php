@props([
    'variant' => 'emerald', // emerald, amber, purple, stone, rose, blue
    'size' => 'sm',
])

@php
    $variantClasses = [
        'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
        'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
        'stone' => 'bg-stone-100 text-stone-700 border-stone-200',
        'rose' => 'bg-rose-100 text-rose-800 border-rose-200',
        'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
        'dark' => 'bg-stone-900 text-stone-100 border-stone-700',
    ][$variant] ?? 'bg-emerald-100 text-emerald-800 border-emerald-200';

    $sizeClasses = [
        'xs' => 'text-[10px] px-1.5 py-0.5',
        'sm' => 'text-xs px-2.5 py-0.5',
        'md' => 'text-sm px-3 py-1',
    ][$size] ?? 'text-xs px-2.5 py-0.5';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full font-semibold border {$variantClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
