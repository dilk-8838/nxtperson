@props([
    'crop',
    'locale' => 'en',
])

@php
    $locale = app()->getLocale();
    $name = match($locale) {
        'gu' => $crop->gu_name,
        'hi' => $crop->hi_name,
        default => $crop->en_name,
    };
    $subName = match($locale) {
        'gu' => "({$crop->en_name})",
        'hi' => "({$crop->en_name})",
        default => "({$crop->gu_name})",
    };
@endphp

<div class="bg-white rounded-3xl border border-stone-200/90 overflow-hidden shadow-xs hover:shadow-lg hover:border-emerald-400/80 transition-all flex flex-col justify-between group">
    <div>
        <!-- Crop Photographic Header (Replaces emoji-only illustration) -->
        <div class="relative h-44 w-full overflow-hidden bg-stone-100">
            <img src="{{ asset('images/crops/' . $crop->slug . '.jpg') }}" 
                 alt="{{ $crop->en_name }} field in Gujarat" 
                 loading="lazy" 
                 class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 via-transparent to-transparent"></div>
            <div class="absolute top-3 left-3">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/90 backdrop-blur-xs text-stone-800 shadow-xs border border-white/40">
                    {{ $crop->category }}
                </span>
            </div>
            <div class="absolute bottom-2.5 left-3 right-3 flex items-baseline justify-between text-white">
                <span class="text-xs font-semibold text-emerald-200">📍 {{ $crop->mandi_yard }}</span>
                <span class="text-xs font-bold text-white bg-emerald-950/70 px-2 py-0.5 rounded-md backdrop-blur-xs border border-emerald-500/30">{{ $crop->mandi_price }}</span>
            </div>
        </div>

        <div class="p-5">
            <div class="flex items-start justify-between gap-2 mb-1">
                <h3 class="text-lg font-bold text-stone-900 group-hover:text-emerald-800 transition-colors">
                    {{ $name }}
                </h3>
                <span class="text-xs font-normal text-stone-500 shrink-0">{{ $subName }}</span>
            </div>

            <div class="mt-2.5 space-y-1.5 text-xs text-stone-600">
                <div class="flex items-center justify-between">
                    <span class="text-stone-400">📅 Season:</span>
                    <span class="font-semibold text-stone-800">{{ $crop->season }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-stone-400">🌡️ Optimal Temp:</span>
                    <span class="font-semibold text-stone-800">{{ $crop->optimal_temp }}</span>
                </div>
            </div>

            @if(!empty($crop->pests))
                <div class="mt-3 pt-3 border-t border-stone-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1">Target Pests / Threats</span>
                    <div class="flex flex-wrap gap-1">
                        @foreach($crop->pests as $pest)
                            <span class="px-1.5 py-0.5 rounded bg-stone-100 text-stone-700 text-[10px] font-medium">
                                {{ $pest }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="p-5 pt-0 border-t border-stone-100 mt-auto flex items-center justify-between">
        <div>
            <span class="text-[10px] text-stone-400 block">{{ $crop->mandi_yard }}</span>
            <span class="text-xs font-extrabold text-stone-900">{{ $crop->mandi_price }}</span>
            <span class="text-[10px] font-bold text-emerald-600 ml-1">{{ $crop->mandi_trend }}</span>
        </div>
        <div class="flex items-center gap-1.5">
            <a href="{{ route('crops.show', $crop->slug) }}" class="px-2.5 py-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold transition-colors">
                Package →
            </a>
            <a href="{{ route('advisory.index') }}" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold transition-colors">
                Advisory →
            </a>
        </div>
    </div>
</div>
