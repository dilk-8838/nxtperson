@props([
    'variant' => 'amber', // amber, emerald, blue, rose
    'title' => null,
])

@php
    $styles = [
        'amber' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-900', 'icon' => '⚠️'],
        'emerald' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-900', 'icon' => '✓'],
        'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-900', 'icon' => 'ℹ️'],
        'purple' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-900', 'icon' => '🛰️'],
        'rose' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-900', 'icon' => '✕'],
    ][$variant] ?? ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-900', 'icon' => '⚠️'];
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-xl border {$styles['bg']} {$styles['border']} {$styles['text']} text-xs sm:text-sm flex items-start gap-3 shadow-xs"]) }}>
    <span class="text-lg leading-none shrink-0">{{ $styles['icon'] }}</span>
    <div class="flex-grow">
        @if($title)
            <strong class="font-bold block mb-0.5">{{ $title }}</strong>
        @endif
        <div class="leading-relaxed">
            {{ $slot }}
        </div>
    </div>
</div>
