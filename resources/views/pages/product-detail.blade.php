@extends('layouts.app')

@section('title', $product->name . ' - Gujarat Input Specification • NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('products.index') }}" class="hover:text-emerald-700 transition-colors">Agri Products</a>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">{{ $product->name }}</span>
        </nav>

        <!-- No Direct Checkout Policy Notice -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">🤝</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">No Direct Checkout Policy:</span>
                NxtPerson is an agritech intelligence and dealer discovery network. Products cannot be purchased directly through this website. Use the distributor directory below to locate stockists in your taluka.
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Left 2 Cols: Product Details & Usage Instructions -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Header Card -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                {{ $product->category ? $product->category->name : 'Agri Input' }}
                            </span>
                            @if($product->is_featured)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                    ⭐ Featured Input
                                </span>
                            @endif
                        </div>
                        <span class="text-xs text-stone-500 font-medium">
                            Pack: <strong class="text-stone-800">{{ $product->package_size ?? 'Standard Field Size' }}</strong>
                        </span>
                    </div>

                    <div class="text-xs font-bold uppercase tracking-wider text-stone-400 mb-1">
                        {{ $product->manufacturer ? $product->manufacturer->name : 'Agricultural Input' }}
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mb-2">
                        {{ $product->name }}
                    </h1>

                    @if($product->active_ingredient)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-stone-100 text-stone-700 text-xs font-mono mb-4">
                            <span>🧪 Active Composition:</span>
                            <span class="font-bold">{{ $product->active_ingredient }}</span>
                        </div>
                    @endif

                    <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200/70 text-xs sm:text-sm text-stone-700 leading-relaxed mb-6">
                        {{ $product->description }}
                    </div>

                    <!-- Price Guide & Package Size Box -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200/70 mb-6">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-emerald-800 tracking-wider block">Price Benchmark</span>
                            <span class="text-lg font-extrabold text-emerald-950">{{ $product->price_guide ?? 'Inquire with Dealer' }}</span>
                            <span class="text-[11px] text-emerald-700 block mt-0.5">Subject to local taluka taxes & transport</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-emerald-800 tracking-wider block">Demonstration Availability</span>
                            <span class="text-lg font-extrabold text-emerald-950">{{ $product->distributors->count() }} Stockists</span>
                            <span class="text-[11px] text-emerald-700 block mt-0.5">Gujarat dealer network directory</span>
                        </div>
                    </div>

                    <!-- Usage Instructions -->
                    @if($product->usage_instructions)
                        <div class="border-t border-stone-100 pt-5 mb-5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-stone-800 mb-2 flex items-center gap-1.5">
                                <span>📋</span> Agronomic Application & Dosage (Educational Reference):
                            </h3>
                            <div class="space-y-3">
                                <p class="text-xs sm:text-sm text-stone-600 bg-stone-50 p-4 rounded-xl border border-stone-200/60 leading-relaxed">
                                    {{ $product->usage_instructions }}
                                </p>
                                <div class="p-3 rounded-xl bg-amber-50/90 border border-amber-200 text-xs text-amber-900 leading-relaxed flex items-start gap-2">
                                    <span class="text-base shrink-0">⚠️</span>
                                    <span><strong>Agronomic Disclaimer:</strong> Specific dosage, dilution rates, and spraying intervals vary according to soil test results, crop growth stage, and local micro-climate. Always consult your local taluka agricultural extension officer or agronomist before application.</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Safety Guidelines -->
                    @if($product->safety_guidelines)
                        <div class="border-t border-stone-100 pt-5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 mb-2 flex items-center gap-1.5">
                                <span>🛡️</span> Safety & Eco-Precaution Guidelines:
                            </h3>
                            <p class="text-xs text-amber-950 bg-amber-50/60 p-4 rounded-xl border border-amber-200/70 leading-relaxed">
                                {{ $product->safety_guidelines }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Associated Gujarat Crops Section -->
                @if($targetCrops->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-bold text-stone-900 flex items-center gap-2">
                                <span>🌾</span> Relevant Crop Packages of Practice
                            </h2>
                            <span class="text-xs text-stone-500">Target agronomic regimes</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($targetCrops as $crop)
                                <a href="{{ route('crops.show', $crop->slug) }}" class="flex items-center gap-3 p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 hover:border-emerald-300 transition-all group">
                                    <span class="text-2xl">{{ $crop->icon }}</span>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-stone-900 group-hover:text-emerald-800 text-xs sm:text-sm">{{ $crop->en_name }}</div>
                                        <div class="text-[11px] text-stone-500 truncate">{{ $crop->season }} • APMC Rate: {{ $crop->mandi_price }}</div>
                                    </div>
                                    <span class="text-stone-400 group-hover:text-emerald-700">→</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar: Authorized Dealers Carrying This Product -->
            <div class="space-y-6">
                <!-- Available In-Stock Stockists -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📍</span>
                            <h2 class="text-base font-bold text-stone-900">Local Dealers (Demo)</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            In Stock
                        </span>
                    </div>

                    <p class="text-xs text-stone-500 mb-4">
                        Contact these local Gujarat stockists to check real-time batch availability:
                    </p>

                    <div class="space-y-3">
                        @forelse($product->distributors as $dist)
                            <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200/70 hover:border-emerald-300 transition-colors">
                                <div class="font-bold text-stone-900 text-xs sm:text-sm mb-0.5">
                                    {{ $dist->business_name }}
                                </div>
                                <div class="text-[11px] text-stone-500 mb-2">
                                    <span>📍 {{ $dist->address }}</span><br>
                                    <span>District: <strong>{{ $dist->district }}</strong> • Pin: <strong class="font-mono text-emerald-800">{{ $dist->pincode }}</strong></span>
                                </div>

                                <div class="flex items-center gap-2 pt-2 border-t border-stone-200/50">
                                    <a href="tel:{{ $dist->phone }}" class="flex-1 text-center py-1.5 px-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] transition-colors">
                                        📞 Call Dealer
                                    </a>
                                    <a href="{{ route('distributors.index', ['search' => $dist->pincode]) }}" class="py-1.5 px-2.5 rounded-lg bg-white border border-stone-300 hover:bg-stone-100 text-stone-700 font-semibold text-[11px] transition-colors">
                                        Details
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 rounded-2xl bg-stone-50 text-xs text-stone-500 text-center">
                                No registered stockists currently associated. Search by district below.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 pt-3 border-t border-stone-100">
                        <a href="{{ route('distributors.index') }}" class="block text-center w-full py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold transition-colors">
                            Search All Gujarat Distributors by Pincode →
                        </a>
                    </div>
                </div>

                <!-- Manufacturer Profile Card -->
                @if($product->manufacturer)
                    <div class="bg-stone-900 text-stone-200 rounded-3xl p-6 shadow-md">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Manufacturer Profile</span>
                        <h3 class="text-base font-bold text-white mt-1 mb-1">{{ $product->manufacturer->name }}</h3>
                        <p class="text-xs text-stone-400 mb-3">HQ: {{ $product->manufacturer->hq_location }}</p>
                        <p class="text-xs text-stone-300 leading-relaxed mb-4">
                            {{ $product->manufacturer->description }}
                        </p>
                        <a href="{{ route('manufacturers.index', ['type' => $product->manufacturer->category]) }}" class="text-xs font-bold text-emerald-300 hover:text-white">
                            View All {{ $product->manufacturer->name }} Inputs →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
