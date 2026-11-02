@extends('layouts.app')

@section('title', 'Agricultural Marketplace Overview - Nxtperson Private Limited')
@section('meta_description', 'Nxtperson\'s integrated agricultural marketplace vision spanning agricultural input discovery, local distributor search, and planned institutional produce trading and FPO aggregation.')

@section('content')
<div class="bg-stone-50 text-stone-900 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Top Breadcrumb & Corporate Header -->
    <header class="border-b border-stone-200/80 bg-white py-4">
        <div class="agri-container">
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <nav class="flex items-center gap-2 text-stone-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-emerald-800 font-semibold">Marketplace</span>
                </nav>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    <span>Integrated Value Chain Marketplace</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 sm:py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-800 bg-purple-100/70 px-3 py-1 rounded-full border border-purple-200">
                    Two-Sided Agricultural Exchange
                </span>
                <h1 class="text-3xl sm:text-5xl font-black text-stone-900 tracking-tight mt-3 mb-4 leading-tight">
                    Integrated Digital Agricultural Marketplace
                </h1>
                <p class="text-base sm:text-lg text-stone-600 leading-relaxed">
                    Nxtperson Private Limited is creating an open agricultural exchange uniting both sides of the agricultural economy: high-quality input discovery for farmers on one side, and transparent produce sourcing for institutional buyers and FPOs on the other.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                        <span>📦</span>
                        <span>Browse Agricultural Inputs (Demo Live)</span>
                    </a>
                    <a href="{{ route('distributors.index') }}" class="px-5 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-300 font-bold text-sm shadow-xs transition-colors flex items-center gap-2">
                        <span>📍</span>
                        <span>Find Local Distributors by Pincode</span>
                    </a>
                    <a href="#planned-produce" class="px-5 py-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-900 border border-purple-200 font-bold text-sm transition-colors flex items-center gap-2">
                        <span>🌾</span>
                        <span>Planned Produce Marketplace ↓</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section A: Working Agricultural Inputs Experience (Interactive Demo) -->
    <section class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                        <span>✓</span> Operational in Current Demonstration
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                        Agricultural Inputs Discovery
                    </h2>
                    <p class="text-sm sm:text-base text-stone-600 mt-1 max-w-xl">
                        Discover certified seeds, bio-fertilizers, micronutrients, crop protection formulations, and irrigation equipment with transparent manufacturer profiles and dealer stockists.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('products.index') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 flex items-center gap-1 bg-white px-3 py-2 rounded-xl border border-stone-200 shadow-2xs">
                        <span>View Full Catalog</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- 4 Input Category Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Seeds -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-sm transition-all">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl mb-4">
                        🌱
                    </div>
                    <h3 class="font-bold text-stone-900 text-base mb-1">High-Yield Seeds</h3>
                    <p class="text-xs text-stone-600 leading-relaxed mb-4">
                        Certified hybrid cotton, potato tubers, wheat grains, and horticulture varieties.
                    </p>
                    <a href="{{ route('products.index', ['category' => 'seeds']) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                        Explore Seeds (6 Products) →
                    </a>
                </div>

                <!-- Bio-Fertilizers -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-sm transition-all">
                    <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-2xl mb-4">
                        🌿
                    </div>
                    <h3 class="font-bold text-stone-900 text-base mb-1">Organic & Bio-Fertilizers</h3>
                    <p class="text-xs text-stone-600 leading-relaxed mb-4">
                        NPK bio-consortia, organic compost, and soil rejuvenation nutrients.
                    </p>
                    <a href="{{ route('products.index', ['category' => 'fertilizers']) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                        Explore Fertilizers →
                    </a>
                </div>

                <!-- Crop Protection -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-sm transition-all">
                    <div class="h-12 w-12 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center text-2xl mb-4">
                        🛡️
                    </div>
                    <h3 class="font-bold text-stone-900 text-base mb-1">Crop Protection</h3>
                    <p class="text-xs text-stone-600 leading-relaxed mb-4">
                        Eco-friendly bio-pesticides, neem azadirachtin sprays, and pheromone traps.
                    </p>
                    <a href="{{ route('products.index', ['category' => 'crop-protection']) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                        Explore Protection →
                    </a>
                </div>

                <!-- Irrigation & Tools -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-sm transition-all">
                    <div class="h-12 w-12 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center text-2xl mb-4">
                        💧
                    </div>
                    <h3 class="font-bold text-stone-900 text-base mb-1">Irrigation & Tools</h3>
                    <p class="text-xs text-stone-600 leading-relaxed mb-4">
                        Micro-drip drippers, inline filters, sprayers, and soil moisture sensors.
                    </p>
                    <a href="{{ route('products.index', ['category' => 'irrigation-tools']) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                        Explore Hardware →
                    </a>
                </div>
            </div>

            <!-- Distributor Discovery Banner -->
            <div class="mt-8 bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl shrink-0">
                        📍
                    </div>
                    <div>
                        <h4 class="font-bold text-stone-900 text-base">Pincode-Based Local Distributor Locator</h4>
                        <p class="text-xs sm:text-sm text-stone-600 mt-0.5">
                            Locate stockists across Rajkot, Anand, Surat, Mehsana, and Banaskantha using 6-digit postal codes.
                        </p>
                    </div>
                </div>
                <a href="{{ route('distributors.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm whitespace-nowrap shadow-xs transition-colors">
                    Search Distributors by Pincode →
                </a>
            </div>
        </div>
    </section>

    <!-- Section B: Planned Produce Marketplace & Institutional Offtake (Roadmap) -->
    <section id="planned-produce" class="py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold mb-2">
                    <span>⏳</span> Planned Roadmap Capabilities • 2027–2030 Deployment
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                    Agricultural Produce Marketplace & Institutional Sourcing
                </h2>
                <p class="text-sm sm:text-base text-stone-600 mt-2">
                    Extending beyond input sales into direct commodity offtake, aggregating FPO volumes and connecting farmers to food industries and international export corridors.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Produce Listing & Sourcing -->
                <div class="bg-stone-50 p-6 sm:p-7 rounded-3xl border border-stone-200 flex flex-col justify-between">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center text-2xl mb-4">
                            🌾
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 mb-2">Produce Listing & Lot Sourcing</h3>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                            Farmers and FPOs list harvested batches with verified tonnage, variety, moisture level, and farm-gate AI quality grade certifications.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 self-start">
                        Planned 2027 Commercial Launch
                    </span>
                </div>

                <!-- FPO Aggregation -->
                <div class="bg-stone-50 p-6 sm:p-7 rounded-3xl border border-stone-200 flex flex-col justify-between">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl mb-4">
                            🤝
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 mb-2">FPO & Cooperative Aggregation</h3>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                            Pools smallholder output into commercial truckload volumes, negotiating collective volume premiums and reducing freight costs.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 self-start">
                        Planned 2028 Expansion
                    </span>
                </div>

                <!-- Institutional Buyers -->
                <div class="bg-stone-50 p-6 sm:p-7 rounded-3xl border border-stone-200 flex flex-col justify-between">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-sky-100 text-sky-800 flex items-center justify-center text-2xl mb-4">
                            🏢
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 mb-2">Food Processors & Exporters</h3>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                            Direct supply contracts for textile mills (cotton), potato chips manufacturers (Deesa belt), flour millers, and Gulf export trading houses.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 self-start">
                        Planned 2029-2030 Corridors
                    </span>
                </div>
            </div>

            <!-- Value Chain Logistics & Financial Services Note -->
            <div class="mt-8 p-6 rounded-3xl bg-purple-50/60 border border-purple-200 text-xs sm:text-sm text-purple-950">
                <h4 class="font-bold text-sm text-purple-900 mb-1 flex items-center gap-2">
                    <span>🚚</span> Integrated Logistics, Warehousing & Trade Escrow (Roadmap Architecture)
                </h4>
                <p class="text-xs text-purple-900/80 leading-relaxed">
                    Nxtperson's long-term architecture incorporates warehouse receipts (e-NWR) integration, refrigerated container booking for perishables (banana and tomato), and transparent digital escrow settlements to ensure same-day farmer payment upon quality acceptance.
                </p>
            </div>
        </div>
    </section>

</div>
@endsection
