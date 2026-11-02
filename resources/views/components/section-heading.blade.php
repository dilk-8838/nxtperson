@props([
    'badge' => null,
    'badgeIcon' => '🌱',
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'max-w-3xl mb-10']) }}>
    @if($badge)
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold mb-3">
            <span>{{ $badgeIcon }}</span> {{ $badge }}
        </span>
    @endif
    <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight leading-tight">
        {{ $title }}
    </h2>
    @if($subtitle)
        <p class="text-sm sm:text-base text-stone-600 mt-2 leading-relaxed">
            {{ $subtitle }}
        </p>
    @endif
</div>
