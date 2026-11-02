@extends('layouts.app')

@section('title', $crop->localized_name . ' (' . $crop->en_name . ') - Gujarat Agronomic Protocol • NxtPerson AgriTech')

@section('content')
<div class="py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Crops</span>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">{{ $crop->en_name }}</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Agronomic package of practices, pest threshold triggers, and APMC benchmark prices for {{ $crop->en_name }} are curated for demo evaluation in Gujarat agricultural clusters.
            </div>
        </div>

        <!-- Crop Header Card -->
        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xs p-6 sm:p-8 mb-8 overflow-hidden relative">
            <div class="absolute -right-10 -bottom-10 text-9xl opacity-10 select-none pointer-events-none">
                {{ $crop->icon }}
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="flex items-start gap-4 sm:gap-6">
                    <div class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl overflow-hidden border border-emerald-200 shrink-0 shadow-sm bg-stone-100">
                        <img src="{{ asset('images/crops/' . $crop->slug . '.jpg') }}" alt="{{ $crop->en_name }}" class="h-full w-full object-cover">
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                {{ $crop->category }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-stone-100 text-stone-700 border border-stone-200">
                                🗓️ {{ $crop->season }}
                            </span>
                            @if($crop->badge)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                    {{ $crop->badge }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight flex flex-wrap items-baseline gap-2">
                            <span>{{ $crop->localized_name }}</span>
                            <span class="text-lg sm:text-xl font-normal text-stone-500">({{ $crop->en_name }} / {{ $crop->gu_name }} / {{ $crop->hi_name }})</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-stone-500 italic mt-1 font-mono">
                            Botanical: {{ $crop->botanical_name }}
                        </p>
                    </div>
                </div>

                <!-- Live APMC Benchmark Card -->
                <div class="bg-stone-50 rounded-2xl border border-stone-200 p-4 sm:p-5 md:min-w-[260px] shrink-0">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-stone-500 mb-1">
                        APMC Market Yard Rate
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-extrabold text-emerald-800">{{ $crop->mandi_price }}</span>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                            {{ $crop->mandi_trend ?? '+2.4%' }}
                        </span>
                    </div>
                    <div class="text-xs text-stone-500 mt-1 flex items-center gap-1">
                        <span>📍</span> Yard: <strong class="text-stone-700">{{ $crop->mandi_yard ?? 'Rajkot APMC' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6 pt-6 border-t border-stone-100 text-stone-700 text-sm sm:text-base leading-relaxed">
                {{ $crop->description }}
            </div>
        </div>

        <!-- 3 Column Agronomic Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Optimal Conditions -->
            <div class="bg-white rounded-2xl border border-stone-200/80 p-6 shadow-xs">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center text-xl">
                        🌡️
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-stone-900">Climate Requirements</h2>
                        <span class="text-xs text-stone-500">Thermal & moisture tolerance</span>
                    </div>
                </div>
                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between py-2 border-b border-stone-100">
                        <span class="text-stone-600">Optimal Temperature:</span>
                        <strong class="text-stone-900 font-semibold">{{ $crop->optimal_temp ?? '22°C - 34°C' }}</strong>
                    </div>
                    <div class="flex justify-between py-2 border-b border-stone-100">
                        <span class="text-stone-600">Primary Soil Type:</span>
                        <strong class="text-stone-900 font-semibold">Deep Black Cotton / Alluvial</strong>
                    </div>
                    <div class="flex justify-between py-2 border-b border-stone-100">
                        <span class="text-stone-600">Irrigation Regimen:</span>
                        <strong class="text-stone-900 font-semibold">Drip with fertigation schedule</strong>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-stone-600">Gujarat Hubs:</span>
                        <strong class="text-stone-900 font-semibold">Saurashtra, Anand, Deesa</strong>
                    </div>
                </div>
            </div>

            <!-- Key Pests & Disease Threats -->
            <div class="bg-white rounded-2xl border border-stone-200/80 p-6 shadow-xs md:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="h-10 w-10 rounded-xl bg-red-50 text-red-700 border border-red-200 flex items-center justify-center text-xl">
                            🔬
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-stone-900">High-Risk Pests & Pathogens</h2>
                            <span class="text-xs text-stone-500">Monitored by NxtPerson AI Crop Diagnostic Engine</span>
                        </div>
                    </div>
                    <a href="{{ route('advisory.upload') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition-colors">
                        <span>📸 Scan Crop Leaf</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($crop->pests ?? [] as $pest)
                        <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/70 hover:border-emerald-300 transition-colors">
                            <div class="flex items-center gap-2 font-bold text-stone-900 text-sm mb-1">
                                <span class="text-red-500">⚠️</span>
                                <span>{{ $pest }}</span>
                            </div>
                            <p class="text-xs text-stone-600">
                                Automated image-based recognition active. AI will assess leaf discoloration and propose approved bio-fungicides or neem extracts.
                            </p>
                        </div>
                    @empty
                        <div class="p-4 rounded-xl bg-stone-50 text-xs text-stone-500 col-span-2">
                            Pest profiles active in the next sync cycle.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Action CTAs -->
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('advisory.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-700 text-white font-semibold text-sm hover:bg-emerald-800 shadow-xs transition-all">
                <span>🌱 Run AI Crop Advisory for {{ $crop->en_name }}</span>
            </a>
            <a href="{{ route('mandi.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-stone-800 border border-stone-300 font-semibold text-sm hover:bg-stone-50 transition-all">
                <span>📊 View All Mandi Rates</span>
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-stone-600 text-sm hover:text-stone-900 transition-colors">
                ← Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
