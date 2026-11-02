@props([
    'variant' => 'default', // 'default', 'light', 'white', 'icon-only'
    'class' => '',
    'size' => 'md', // 'xs', 'sm', 'md', 'lg', 'xl'
])

@php
    $textColor = match($variant) {
        'light', 'white' => 'text-white',
        default => 'text-[#0c365b]',
    };

    $subtextColor = match($variant) {
        'light', 'white' => 'text-slate-300',
        default => 'text-slate-600',
    };

    $pixelHeight = match($size) {
        'xs' => 20,
        'sm' => 26,
        'lg' => 44,
        'xl' => 56,
        default => 36,
    };
    $pixelWidth = (int) round($pixelHeight * (168 / 192));

    $iconSize = match($size) {
        'xs' => 'h-5 w-auto',
        'sm' => 'h-6.5 w-auto',
        'lg' => 'h-11 w-auto',
        'xl' => 'h-14 w-auto',
        default => 'h-9 w-auto',
    };

    $titleSize = match($size) {
        'xs' => 'text-[11px] leading-tight',
        'sm' => 'text-sm sm:text-base leading-tight',
        'lg' => 'text-2xl leading-tight',
        'xl' => 'text-3xl leading-tight',
        default => 'text-xl leading-tight',
    };

    $subtextSize = match($size) {
        'xs' => 'text-[6px] tracking-[0.16em]',
        'sm' => 'text-[7.5px] tracking-[0.18em]',
        'lg' => 'text-[11px] tracking-[0.25em]',
        'xl' => 'text-xs tracking-[0.25em]',
        default => 'text-[9px] tracking-[0.22em]',
    };

    $isLight = in_array($variant, ['light', 'white']);
    $logoSrc = $isLight ? asset('images/logo-mark-light.png') : asset('images/logo-mark.png');
@endphp

<div class="inline-flex items-center gap-2 select-none shrink-0 {{ $class }}">
    <!-- Official NxtPerson Brand Logo Mark -->
    <img src="{{ $logoSrc }}" 
         alt="NxtPerson Logo" 
         width="{{ $pixelWidth }}"
         height="{{ $pixelHeight }}"
         style="height: {{ $pixelHeight }}px; width: auto; max-height: {{ $pixelHeight }}px;"
         class="{{ $iconSize }} shrink-0 object-contain select-none pointer-events-none" 
         loading="eager" />

    @if($variant !== 'icon-only')
        <div class="flex flex-col leading-none">
            <span class="font-extrabold tracking-tight {{ $titleSize }} {{ $textColor }}">
                Nxtperson
            </span>
            <span class="font-semibold uppercase {{ $subtextSize }} {{ $subtextColor }} mt-0.5">
                PRIVATE LIMITED
            </span>
        </div>
    @endif
</div>

