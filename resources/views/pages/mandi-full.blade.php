@extends('layouts.app')

@section('title', 'Gujarat APMC Mandi Rates & Live Price Discovery • NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Markets</span>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">APMC Mandi Intelligence</span>
        </nav>

        <!-- Prominent Sample Data Notice -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">📈</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                APMC price benchmarks, arrival tonnages, and modal realization values are simulated demonstration records compiled from Gujarat State Agricultural Marketing Board yards for October 2026.
            </div>
        </div>

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                    <span>🏛️</span> Gujarat State APMC Yard Network
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Daily Agricultural Commodity Price Discovery
                </h1>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">
                    Transparent spot prices across Rajkot, Gondal, Deesa, Unjha, Anand, and Surat market yards.
                </p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('mandi.index') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
                @if(request('yard'))
                    <input type="hidden" name="yard" value="{{ request('yard') }}">
                @endif
                @if(request('crop'))
                    <input type="hidden" name="crop" value="{{ request('crop') }}">
                @endif
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search commodity, yard, variety..." class="w-full rounded-xl border border-stone-300 py-2.5 pl-3.5 pr-8 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-hidden">
                    @if(request('search'))
                        <a href="{{ route('mandi.index', array_diff_key(request()->query(), ['search' => ''])) }}" class="absolute right-2.5 top-2.5 text-stone-400 hover:text-stone-600 text-sm">✕</a>
                    @endif
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm transition-colors shrink-0">
                    Search
                </button>
            </form>
        </div>

        <!-- Filter Pills Bar -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs mb-8 space-y-4">
            <!-- Yard Filters -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                <span class="font-bold text-stone-500 shrink-0 mr-1">Market Yard:</span>
                <a href="{{ route('mandi.index', array_diff_key(request()->query(), ['yard' => ''])) }}" class="px-3 py-1.5 rounded-xl font-bold whitespace-nowrap {{ !request('yard') ? 'bg-emerald-800 text-white shadow-2xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                    All APMC Yards
                </a>
                @foreach($yards as $yd)
                    <a href="{{ route('mandi.index', array_merge(request()->query(), ['yard' => $yd])) }}" class="px-3 py-1.5 rounded-xl font-bold whitespace-nowrap {{ request('yard') === $yd ? 'bg-emerald-800 text-white shadow-2xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                        {{ $yd }}
                    </a>
                @endforeach
            </div>

            <!-- Crop Filters -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs border-t border-stone-100 pt-3">
                <span class="font-bold text-stone-500 shrink-0 mr-1">Commodity:</span>
                <a href="{{ route('mandi.index', array_diff_key(request()->query(), ['crop' => ''])) }}" class="px-3 py-1.5 rounded-xl font-bold whitespace-nowrap {{ !request('crop') ? 'bg-emerald-800 text-white shadow-2xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                    All Commodities
                </a>
                @foreach($crops as $cr)
                    <a href="{{ route('mandi.index', array_merge(request()->query(), ['crop' => $cr->slug])) }}" class="px-3 py-1.5 rounded-xl font-bold whitespace-nowrap {{ request('crop') === $cr->slug ? 'bg-emerald-800 text-white shadow-2xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                        {{ $cr->en_name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Mandi Benchmark Table -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-bold text-stone-900 flex items-center gap-2">
                        <span>📊</span> Real-Time Spot Market Realizations
                    </h2>
                    <p class="text-xs text-stone-500 mt-0.5">
                        Prices quoted in Indian Rupees (₹) per Standard Quintal (100 kg).
                    </p>
                </div>
                <span class="text-xs font-semibold text-stone-400">
                    Showing {{ $mandiRates->total() }} benchmark records
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 text-stone-700 font-bold uppercase text-[10px] tracking-wider border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-4">Commodity & Grade</th>
                            <th class="py-3 px-4">APMC Yard & District</th>
                            <th class="py-3 px-4">Daily Arrivals</th>
                            <th class="py-3 px-4">Min Price</th>
                            <th class="py-3 px-4 font-bold text-stone-900">Modal Price</th>
                            <th class="py-3 px-4">Max Price</th>
                            <th class="py-3 px-4 text-right">Day Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($mandiRates as $mr)
                            <tr class="hover:bg-emerald-50/40 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-stone-900">
                                    <div class="text-sm font-extrabold text-stone-900">{{ $mr->commodity_name }}</div>
                                    <div class="text-[11px] font-normal text-stone-500">Grade: {{ $mr->variety ?? 'Standard Market' }}</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-stone-800">{{ $mr->yard_name }}</div>
                                    <div class="text-[10px] text-stone-400">📍 {{ $mr->district }}, {{ $mr->state }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-medium text-stone-700 whitespace-nowrap">
                                    {{ $mr->arrival_quantity ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-stone-600">
                                    ₹{{ number_format((float)$mr->min_price) }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-black text-emerald-900 text-sm whitespace-nowrap">
                                    ₹{{ number_format((float)$mr->modal_price) }} <span class="text-[10px] font-normal text-stone-400">/ Qtl</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-stone-600">
                                    ₹{{ number_format((float)$mr->max_price) }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ str_contains($mr->trend_percentage, '+') ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $mr->trend_percentage }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-400">
                                    No mandi market rates match your search criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-stone-100">
                {{ $mandiRates->links() }}
            </div>
        </div>

        <!-- Mandi MSP vs Realization Intelligence -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                <div class="flex items-center gap-2 text-emerald-800 font-bold text-sm mb-2">
                    <span>⚖️</span> Government MSP Benchmark Tracking
                </div>
                <p class="text-xs text-stone-600 leading-relaxed mb-4">
                    For Kharif 2026, Government Minimum Support Price (MSP) for Medium Staple Cotton is <strong>₹7,122 / Quintal</strong>. Spot trade in APMC Rajkot is currently clearing above MSP at <strong>₹7,150 / Qtl</strong>, reflecting steady domestic ginning demand.
                </p>
                <a href="{{ route('crops.show', 'cotton') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 hover:text-emerald-950">
                    View Complete Cotton Agronomic Package →
                </a>
            </div>

            <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                <div class="flex items-center gap-2 text-stone-900 font-bold text-sm mb-2">
                    <span>💡</span> Price Realization & Harvest Planning
                </div>
                <p class="text-xs text-stone-600 leading-relaxed mb-4">
                    Deesa potato cold storage facilities report steady terminal market arrivals with firm modal prices around <strong>₹1,420 / Qtl</strong>. Farmers are advised to maintain sorting and grade separation before yard transit to capture maximum auction premiums.
                </p>
                <a href="{{ route('distributors.index', ['district' => 'Banaskantha']) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 hover:text-emerald-950">
                    Locate Deesa Storage & Distribution Hubs →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
