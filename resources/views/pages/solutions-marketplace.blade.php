@extends('layouts.app')

@section('title', 'Integrated Digital Agricultural Marketplace - Nxtperson')
@section('meta_description', 'Nxtperson\'s integrated marketplace architecture uniting input manufacturers, distributors, FPOs, produce buyers, and logistics providers into a single trusted value chain.')

@section('content')
<div class="bg-stone-50 text-stone-900 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Top Breadcrumb & Architecture Header -->
    <header class="border-b border-stone-200/80 bg-white py-4">
        <div class="agri-container">
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <nav class="flex items-center gap-2 text-stone-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
                    <span class="text-stone-300">/</span>
                    <a href="{{ route('solutions.index') }}" class="hover:text-emerald-700 transition-colors">Solutions</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-emerald-800 font-semibold">Integrated Marketplace</span>
                </nav>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-900 border border-purple-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-purple-600"></span>
                    <span>Input Catalog Live • Produce Trading Planned</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 sm:py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-800 bg-purple-100/70 px-3 py-1 rounded-full border border-purple-200">
                        Strategic Solution Pillar 4
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-black text-stone-900 tracking-tight leading-tight">
                        Integrated Digital Agricultural Marketplace
                    </h1>
                    <p class="text-lg sm:text-xl font-bold text-emerald-900">
                        Connecting Supply, Demand, Inputs, and Logistics
                    </p>
                    <p class="text-stone-600 text-sm sm:text-base leading-relaxed">
                        Traditional agricultural supply chains are fractured across multiple layers of intermediaries, resulting in high input costs for farmers and fragmented sourcing for food processors. Nxtperson is building an open, integrated marketplace that connects all 10 value-chain stakeholders from certified input discovery to verified institutional crop procurement.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-3">
                        <a href="{{ route('marketplace.index') }}" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <span>🛒</span>
                            <span>Explore Marketplace Overview</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <span>📦</span>
                            <span>Browse Input Products (Demo)</span>
                        </a>
                        <a href="{{ route('distributors.index') }}" class="px-5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold text-sm transition-colors flex items-center gap-2">
                            <span>📍</span>
                            <span>Find Distributors by Pincode</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="p-6 rounded-3xl bg-stone-100 border border-stone-200 shadow-md space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-stone-200 text-xs">
                            <span class="font-bold text-stone-800">Two-Sided Marketplace Scope</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-bold">Value Chain</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-stone-900">Side A: Agricultural Inputs</span>
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold text-[10px]">Demo Live</span>
                                </div>
                                <p class="text-[11px] text-stone-500 mt-1">High-yield seeds, bio-fertilizers, crop protection, and irrigation equipment discoverable by crop and district.</p>
                            </div>

                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-stone-900">Side B: Produce Offtake & FPOs</span>
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-bold text-[10px]">Planned Roadmap</span>
                                </div>
                                <p class="text-[11px] text-stone-500 mt-1">Direct aggregation of harvest batches from FPOs and cooperatives matched to food processors, retail chains, and exporters.</p>
                            </div>

                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-stone-900">Side C: Value Chain Services</span>
                                    <span class="px-2 py-0.5 rounded bg-stone-100 text-stone-700 font-bold text-[10px]">Planned Roadmap</span>
                                </div>
                                <p class="text-[11px] text-stone-500 mt-1">Cold storage booking, freight transport dispatch, warehouse receipts, and trade credit facilitation.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Operational Inputs vs Planned Produce Matrix -->
    <section class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-800 bg-purple-100/70 px-3 py-1 rounded-full">
                    Transparency Matrix
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3 mb-3">
                    Available Demonstration vs Planned Marketplace
                </h2>
                <p class="text-sm sm:text-base text-stone-600">
                    Nxtperson clearly demarcates operational demonstration modules from roadmap commercial integrations.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Available in Demo -->
                <div class="bg-white p-7 rounded-3xl border border-emerald-200 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                        <h3 class="text-lg font-bold text-stone-900">Available in Current Demo</h3>
                    </div>
                    <ul class="space-y-3 text-xs sm:text-sm text-stone-700">
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-700 font-bold text-base">✓</span>
                            <div>
                                <strong class="text-stone-900">Agricultural Input Product Catalog:</strong>
                                <span class="text-stone-600 block">Search seeds, bio-fertilizers, micronutrients, and crop protection by category.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-700 font-bold text-base">✓</span>
                            <div>
                                <strong class="text-stone-900">Manufacturer Brand Directory:</strong>
                                <span class="text-stone-600 block">Explore participating brand partners across Gujarat.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-700 font-bold text-base">✓</span>
                            <div>
                                <strong class="text-stone-900">6-Digit Pincode Distributor Search:</strong>
                                <span class="text-stone-600 block">Locate local taluka dealers and authorized stockists.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="mt-6 pt-4 border-t border-stone-100">
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 flex items-center gap-1">
                            <span>Browse Product Directory</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- Planned Capabilities -->
                <div class="bg-white p-7 rounded-3xl border border-amber-200 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                        <h3 class="text-lg font-bold text-stone-900">Planned Roadmap Capabilities (2027–2030)</h3>
                    </div>
                    <ul class="space-y-3 text-xs sm:text-sm text-stone-700">
                        <li class="flex items-start gap-2.5">
                            <span class="text-amber-700 font-bold text-base">⏳</span>
                            <div>
                                <strong class="text-stone-900">Agricultural Produce Marketplace:</strong>
                                <span class="text-stone-600 block">Direct crop listing, quality-tested harvest lots, and dynamic price bidding.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-amber-700 font-bold text-base">⏳</span>
                            <div>
                                <strong class="text-stone-900">FPO Supply Aggregation:</strong>
                                <span class="text-stone-600 block">Bulk quantity pooling for institutional procurement contracts.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-amber-700 font-bold text-base">⏳</span>
                            <div>
                                <strong class="text-stone-900">Logistics, Storage & Trade Finance:</strong>
                                <span class="text-stone-600 block">Cold chain booking, warehouse receipt financing, and secure digital escrow.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="mt-6 pt-4 border-t border-stone-100">
                        <a href="{{ route('roadmap.index') }}" class="text-xs font-bold text-amber-800 hover:text-amber-950 flex items-center gap-1">
                            <span>View Planned Roadmap Timeline</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('solutions.index') }}" class="text-xs font-bold text-stone-600 hover:text-emerald-700">
                    ← Back to All 4 Solution Pillars
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
