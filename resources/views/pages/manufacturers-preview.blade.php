@extends('layouts.app')

@php
    $type = request('type', 'all');
    $typeTitles = [
        'all' => 'All Certified Agri Manufacturers',
        'fertilizer' => 'Certified Fertilizer & Bio-Nutrient Companies',
        'seeds' => 'Certified Seed Breeders & Hybrids',
        'crop-protection' => 'Crop Protection & Bio-Control Formulators',
    ];
@endphp

@section('title', ($typeTitles[$type] ?? 'Manufacturers') . ' - Gujarat Ecosystem • NxtPerson AgriTech')

@section('content')
<div class="py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Ecosystem</span>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">{{ $typeTitles[$type] ?? 'Manufacturers' }}</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Brand listings, dealer authorizations, and product catalogs are simulated for demo evaluation. NxtPerson does not sell chemical formulations directly and facilitates local dealer discovery only.
            </div>
        </div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                    <span>🏭</span> Agri Input Manufacturers Network
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    {{ $typeTitles[$type] ?? 'Manufacturers Directory' }}
                </h1>
                <p class="text-stone-600 text-xs sm:text-sm mt-1">
                    Connect with certified input producers authorized by the Gujarat Department of Agriculture.
                </p>
            </div>

            <!-- Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
                <a href="{{ route('manufacturers.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap {{ $type === 'all' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-stone-700 border border-stone-200 hover:bg-stone-50' }}">
                    All Brands
                </a>
                <a href="{{ route('manufacturers.index', ['type' => 'fertilizer']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap {{ $type === 'fertilizer' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-stone-700 border border-stone-200 hover:bg-stone-50' }}">
                    🧪 Fertilizers
                </a>
                <a href="{{ route('manufacturers.index', ['type' => 'seeds']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap {{ $type === 'seeds' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-stone-700 border border-stone-200 hover:bg-stone-50' }}">
                    🌱 Seeds
                </a>
                <a href="{{ route('manufacturers.index', ['type' => 'crop-protection']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap {{ $type === 'crop-protection' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-stone-700 border border-stone-200 hover:bg-stone-50' }}">
                    🛡️ Crop Protection
                </a>
            </div>
        </div>

        <!-- Manufacturers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @php
                $brands = [
                    ['name' => 'Gujarat State Fertilizers & Chemicals (GSFC)', 'type' => 'fertilizer', 'type_label' => 'Fertilizers', 'hq' => 'Vadodara, Gujarat', 'products' => 'Sardar Urea, DAP, Ammonium Sulphate, Bio-fertilizers', 'icon' => '🧪', 'dealers' => '1,420+ Authorized Dealers'],
                    ['name' => 'Gujarat Narmada Valley Fertilizers (GNFC)', 'type' => 'fertilizer', 'type_label' => 'Fertilizers', 'hq' => 'Bharuch, Gujarat', 'products' => 'Narmada Urea, Neem-Coated Formulations', 'icon' => '⚗️', 'dealers' => '980+ Authorized Dealers'],
                    ['name' => 'Gujarat State Seed Corporation (GSSC - Gurjari)', 'type' => 'seeds', 'type_label' => 'Seeds', 'hq' => 'Gandhinagar, Gujarat', 'products' => 'Certified Shankar-6 Cotton, GW-496 Wheat, Paddy Seeds', 'icon' => '🌱', 'dealers' => '2,100+ Distribution Outlets'],
                    ['name' => 'Charotar Agro Bio-Tech Labs', 'type' => 'seeds', 'type_label' => 'Seeds', 'hq' => 'Anand, Gujarat', 'products' => 'Tissue Culture Grand Naine Banana Plantlets', 'icon' => '🍌', 'dealers' => '340+ Distribution Outlets'],
                    ['name' => 'Saurashtra Bio-Formulations & Pests Ltd', 'type' => 'crop-protection', 'type_label' => 'Crop Protection', 'hq' => 'Rajkot, Gujarat', 'products' => 'NeemAzal Bio-Pesticides, Trichoderma, Pheromone Lures', 'icon' => '🛡️', 'dealers' => '620+ Crop Clinics'],
                    ['name' => 'Deesa Agro Coldchain & Protection Corp', 'type' => 'crop-protection', 'type_label' => 'Crop Protection', 'hq' => 'Banaskantha, Gujarat', 'products' => 'Anti-Fungal Mancozeb Formulations, Storage Inhibitors', 'icon' => '🥔', 'dealers' => '450+ Stockists'],
                ];

                if ($type !== 'all') {
                    $brands = array_filter($brands, fn($b) => $b['type'] === $type);
                }
            @endphp

            @foreach($brands as $b)
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs hover:border-emerald-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-12 w-12 rounded-2xl bg-stone-100 flex items-center justify-center text-2xl">
                                {{ $b['icon'] }}
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ $b['type_label'] }}
                            </span>
                        </div>

                        <h2 class="text-base font-bold text-stone-900 mb-1">
                            {{ $b['name'] }}
                        </h2>
                        <div class="text-xs text-stone-500 mb-3 flex items-center gap-1">
                            <span>📍</span> HQ: {{ $b['hq'] }}
                        </div>
                        <p class="text-xs text-stone-600 line-clamp-2 mb-4">
                            <strong class="text-stone-800">Featured Inputs:</strong> {{ $b['products'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                        <span class="text-[11px] font-medium text-emerald-700">{{ $b['dealers'] }}</span>
                        <a href="{{ route('distributors.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-stone-900 hover:text-emerald-700">
                            Find Dealers →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-stone-900 text-stone-200 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-bold text-white mb-1">Looking for Local Taluka Stockists?</h2>
                <p class="text-xs sm:text-sm text-stone-400 max-w-xl">
                    Search over 450+ verified Gujarat agricultural input dealers and distributors by entering your 6-digit postal code.
                </p>
            </div>
            <a href="{{ route('distributors.index', ['search' => 'pincode']) }}" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm whitespace-nowrap shadow-xs transition-colors">
                🔍 Find Nearest Distributor by Pincode
            </a>
        </div>
    </div>
</div>
@endsection
