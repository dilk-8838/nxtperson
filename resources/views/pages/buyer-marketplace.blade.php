@extends('layouts.app')

@section('title', 'Agricultural Marketplace — Farm Produce, Inputs & FPO Collective Exchange')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container space-y-8">

        {{-- Hero Header --}}
        <div class="rounded-3xl bg-gradient-to-r from-[#0c365b] via-[#071f37] to-[#051627] text-white p-6 sm:p-10 shadow-md relative overflow-hidden">
            <div class="relative z-10 max-w-4xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#00875a]/20 text-[#34d399] border border-[#00875a]/40 mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#34d399] animate-pulse"></span>
                    Verified B2B Agricultural Trading Exchange
                </span>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight">
                    NxtPerson Agricultural Marketplace
                </h1>
                <p class="text-slate-200 text-sm sm:text-base mt-2 leading-relaxed font-normal max-w-2xl">
                    Discover optical AI graded harvest lots, commercial agricultural inputs, and FPO collective bulk pools from verified sellers across Gujarat.
                </p>

                <div class="flex flex-wrap items-center gap-3 mt-6 text-xs text-slate-200">
                    <span class="flex items-center gap-1.5 bg-white/10 backdrop-blur-xs px-3.5 py-1.5 rounded-full border border-white/15 font-semibold">
                        ✓ <strong>{{ $stats['available_lots_count'] }}</strong> Farm Lots
                    </span>
                    <span class="flex items-center gap-1.5 bg-white/10 backdrop-blur-xs px-3.5 py-1.5 rounded-full border border-white/15 font-semibold">
                        ✓ <strong>{{ $stats['supplier_products_count'] }}</strong> Input Products
                    </span>
                    <span class="flex items-center gap-1.5 bg-white/10 backdrop-blur-xs px-3.5 py-1.5 rounded-full border border-white/15 font-semibold">
                        ✓ <strong>{{ $stats['fpo_bulk_lots_count'] }}</strong> FPO Collective Pools
                    </span>
                    <span class="flex items-center gap-1.5 bg-white/10 backdrop-blur-xs px-3.5 py-1.5 rounded-full border border-white/15 font-semibold">
                        ✓ Protected Concurrency &amp; Quality
                    </span>
                </div>

                {{-- Buyer Quick CTA buttons --}}
                <div class="flex flex-wrap items-center gap-3 mt-6 pt-4 border-t border-white/10">
                    <a href="{{ route('buyer.requirements.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md transition">
                        + Post Bulk Procurement Requirement &rarr;
                    </a>
                    <a href="{{ route('rfqs.create') }}" class="px-4 py-2 bg-white/15 hover:bg-white/25 text-white font-bold rounded-xl text-xs backdrop-blur-md transition">
                        Create Request for Quotation (RFQ)
                    </a>
                    <a href="{{ route('rfqs.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-slate-200 font-semibold rounded-xl text-xs transition">
                        My RFQs
                    </a>
                    <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-slate-200 font-semibold rounded-xl text-xs transition">
                        Purchase Orders
                    </a>
                </div>
            </div>

            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-[#00875a]/20 blur-3xl pointer-events-none"></div>
        </div>

        {{-- Marketplace Navigation Tabs --}}
        <div class="flex items-center gap-2 border-b border-stone-200 pb-3 overflow-x-auto">
            <a href="{{ route('buyer.marketplace', ['tab' => 'produce']) }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition {{ $currentTab === 'produce' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-stone-100 border border-stone-200' }}">
                <span>🌾 Farm Produce Lots</span>
                <span class="px-2 py-0.5 rounded-full text-xs {{ $currentTab === 'produce' ? 'bg-emerald-800 text-emerald-100' : 'bg-stone-100 text-stone-600' }}">
                    {{ $stats['available_lots_count'] }}
                </span>
            </a>

            <a href="{{ route('buyer.marketplace', ['tab' => 'inputs']) }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition {{ $currentTab === 'inputs' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-stone-100 border border-stone-200' }}">
                <span>🧪 Agricultural Inputs &amp; Supplies</span>
                <span class="px-2 py-0.5 rounded-full text-xs {{ $currentTab === 'inputs' ? 'bg-emerald-800 text-emerald-100' : 'bg-stone-100 text-stone-600' }}">
                    {{ $stats['supplier_products_count'] }}
                </span>
            </a>

            <a href="{{ route('buyer.marketplace', ['tab' => 'fpo']) }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition {{ $currentTab === 'fpo' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-stone-600 hover:bg-stone-100 border border-stone-200' }}">
                <span>🏢 FPO Collective Bulk Pools</span>
                <span class="px-2 py-0.5 rounded-full text-xs {{ $currentTab === 'fpo' ? 'bg-emerald-800 text-emerald-100' : 'bg-stone-100 text-stone-600' }}">
                    {{ $stats['fpo_bulk_lots_count'] }}
                </span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- Sidebar Filters --}}
            <aside class="lg:col-span-1 space-y-6">
                <form action="{{ route('buyer.marketplace') }}" method="GET" class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200/80 space-y-5">
                    <input type="hidden" name="tab" value="{{ $currentTab }}">

                    <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                        <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🔍</span> Search &amp; Filter
                        </h3>
                        <a href="{{ route('buyer.marketplace', ['tab' => $currentTab]) }}" class="text-xs text-stone-400 hover:text-emerald-800">Reset</a>
                    </div>

                    {{-- Search Input --}}
                    <div>
                        <label for="search" class="block text-xs font-bold text-stone-700 mb-1">Keywords</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, ref, variety, brand..." class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                    </div>

                    @if($currentTab === 'produce' || $currentTab === 'fpo')
                        {{-- Crop Filter --}}
                        <div>
                            <label for="crop" class="block text-xs font-bold text-stone-700 mb-1">Crop Commodity</label>
                            <select name="crop" id="crop" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white cursor-pointer">
                                <option value="">All Crops</option>
                                @foreach($crops as $c)
                                    <option value="{{ $c->slug }}" {{ request('crop') === $c->slug ? 'selected' : '' }}>
                                        {{ $c->icon }} {{ $c->en_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Quality Grade Filter (Produce only) --}}
                        @if($currentTab === 'produce')
                            <div>
                                <label for="grade" class="block text-xs font-bold text-stone-700 mb-1">Certified Grade</label>
                                <select name="grade" id="grade" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white cursor-pointer">
                                    <option value="">All Grades</option>
                                    <option value="Good" {{ request('grade') === 'Good' ? 'selected' : '' }}>Good (Grade A Premium)</option>
                                    <option value="Intermediate" {{ request('grade') === 'Intermediate' ? 'selected' : '' }}>Intermediate (Grade B Standard)</option>
                                </select>
                            </div>
                        @endif

                        {{-- District Filter --}}
                        <div>
                            <label for="district" class="block text-xs font-bold text-stone-700 mb-1">District</label>
                            <select name="district" id="district" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white cursor-pointer">
                                <option value="">All Gujarat Districts</option>
                                @foreach($districts as $d)
                                    <option value="{{ $d }}" {{ request('district') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($currentTab === 'inputs')
                        {{-- Product Category Filter --}}
                        <div>
                            <label for="category" class="block text-xs font-bold text-stone-700 mb-1">Category</label>
                            <select name="category" id="category" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white cursor-pointer">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <button type="submit" class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                        Apply Filters
                    </button>
                </form>

                {{-- Procurement Quick Links --}}
                <div class="bg-blue-50/70 border border-blue-200/80 rounded-3xl p-5 text-xs text-blue-900 space-y-2">
                    <span class="font-bold block">📋 Bulk Requirements</span>
                    <p class="text-blue-800 text-[11px] leading-relaxed">
                        Need large volumes or customized commercial specifications? Post your requirement once and solicit competing quotations.
                    </p>
                    <a href="{{ route('buyer.requirements.create') }}" class="inline-block font-bold text-blue-700 hover:underline">
                        + New Requirement &rarr;
                    </a>
                </div>
            </aside>

            {{-- Main Content Column --}}
            <main class="lg:col-span-3">

                {{-- TAB 1: Farm Produce Lots --}}
                @if($currentTab === 'produce')
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @forelse($lots as $lot)
                            <div class="bg-white rounded-3xl border border-stone-200/80 shadow-2xs hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                                <div class="relative aspect-4/3 bg-stone-100 overflow-hidden">
                                    @if($lot->primaryImage)
                                        <img src="{{ asset($lot->primaryImage->image_path) }}" alt="{{ $lot->variety }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-4xl">
                                            {{ $lot->crop->icon ?? '🌾' }}
                                        </div>
                                    @endif

                                    <div class="absolute top-3 left-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-black shadow-sm {{ $lot->getGradeBadgeClass() }}">
                                            Grade {{ $lot->approved_grade ?? 'Good' }}
                                        </span>
                                    </div>

                                    <div class="absolute top-3 right-3 bg-stone-900/80 backdrop-blur-md text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">
                                        📍 {{ $lot->district }}
                                    </div>

                                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-stone-950/90 to-transparent p-3 pt-6 text-white text-xs flex justify-between items-end">
                                        <div>
                                            <span class="text-[10px] text-stone-300 uppercase block font-semibold">Available Stock</span>
                                            <strong class="text-sm font-black">{{ number_format((float)$lot->available_quantity_kg, 1) }} kg</strong>
                                            <span class="text-[11px] text-stone-300">({{ number_format((float)$lot->available_quantity_kg / 100, 1) }} Qtl)</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-emerald-300 uppercase block font-bold">Buyer Rate</span>
                                            <strong class="text-base font-black text-white">₹{{ number_format((float)($lot->buyer_sale_price_per_kg ?: 0), 2) }}</strong>
                                            <span class="text-[10px] text-stone-300">/kg</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-1.5 text-xs text-stone-500 mb-1">
                                            <span>{{ $lot->crop->en_name ?? ucfirst($lot->crop_slug) }}</span>
                                            <span>&bull;</span>
                                            <span class="capitalize">{{ $lot->farming_method }}</span>
                                        </div>

                                        <h3 class="text-base font-extrabold text-stone-900 line-clamp-1 group-hover:text-emerald-800 transition-colors">
                                            {{ $lot->variety }}
                                        </h3>

                                        @if($lot->latestAssessment)
                                            <p class="text-xs text-stone-600 line-clamp-2 mt-2 leading-relaxed">
                                                {{ $lot->latestAssessment->assessment_explanation }}
                                            </p>
                                        @endif

                                        <div class="flex flex-wrap gap-1.5 mt-3 text-[11px]">
                                            <span class="px-2 py-0.5 rounded-md bg-stone-100 text-stone-700 font-medium">
                                                Harvest: {{ $lot->harvest_date?->format('M d') }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-stone-100 text-stone-700 font-medium">
                                                Pin: {{ $lot->pincode }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-mono text-stone-400">{{ $lot->lot_number }}</span>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('rfqs.create', ['produce_lot_id' => $lot->id]) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition" title="Request Custom Quote">
                                                RFQ
                                            </a>
                                            <a href="{{ route('buyer.lot.show', $lot->id) }}" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1">
                                                <span>Inspect &amp; Order</span>
                                                <span>&rarr;</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-white rounded-3xl p-12 text-center text-stone-500 border border-stone-200">
                                <span class="text-4xl mb-3 block">🛒</span>
                                <h4 class="text-base font-bold text-stone-800">No Produce Lots Found</h4>
                                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                                    No available produce matches these criteria. Try adjusting your filters.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if($lots->hasPages())
                        <div class="mt-8">
                            {{ $lots->links() }}
                        </div>
                    @endif

                {{-- TAB 2: Agricultural Inputs & Supplies --}}
                @elseif($currentTab === 'inputs')
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @forelse($products as $product)
                            <div class="bg-white rounded-3xl border border-stone-200/80 shadow-2xs hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                                <div class="relative aspect-4/3 bg-stone-100 overflow-hidden">
                                    @if($product->image_url)
                                        <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-4xl">
                                            🧪
                                        </div>
                                    @endif

                                    <div class="absolute top-3 left-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $product->category?->name ?? 'Agri Input' }}
                                        </span>
                                    </div>

                                    @if($product->brand_name)
                                        <div class="absolute top-3 right-3 bg-stone-900/80 backdrop-blur-md text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">
                                            {{ $product->brand_name }}
                                        </div>
                                    @endif

                                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-stone-950/90 to-transparent p-3 pt-6 text-white text-xs flex justify-between items-end">
                                        <div>
                                            <span class="text-[10px] text-stone-300 uppercase block font-semibold">Available Stock</span>
                                            <strong class="text-sm font-black">{{ number_format((float)$product->stock_quantity, 1) }} {{ $product->unit }}</strong>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-emerald-300 uppercase block font-bold">Unit Price</span>
                                            <strong class="text-base font-black text-white">₹{{ number_format((float)$product->unit_price, 2) }}</strong>
                                            <span class="text-[10px] text-stone-300">/{{ $product->unit }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <span class="text-xs text-stone-400 block mb-1">
                                            Supplier: <strong class="text-stone-700">{{ $product->supplierOrganization?->name ?? 'Verified Supplier' }}</strong>
                                        </span>
                                        <h3 class="text-base font-extrabold text-stone-900 line-clamp-1 group-hover:text-emerald-800 transition-colors">
                                            {{ $product->name }}
                                        </h3>
                                        <p class="text-xs text-stone-600 line-clamp-2 mt-2 leading-relaxed">
                                            {{ $product->description }}
                                        </p>

                                        <div class="mt-3 flex items-center gap-2 text-[11px] text-stone-500">
                                            <span class="bg-stone-100 px-2 py-0.5 rounded">Min Order: {{ number_format((float)$product->min_order_quantity) }} {{ $product->unit }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between gap-2">
                                        <a href="{{ route('rfqs.create', ['product_id' => $product->id]) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                            Request RFQ
                                        </a>
                                        <a href="{{ route('products.show', $product->id) }}" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1">
                                            <span>View Product</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-white rounded-3xl p-12 text-center text-stone-500 border border-stone-200">
                                <span class="text-4xl mb-3 block">🧪</span>
                                <h4 class="text-base font-bold text-stone-800">No Supplier Products Found</h4>
                                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                                    No available agricultural inputs matching your criteria.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if($products->hasPages())
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @endif

                {{-- TAB 3: FPO Collective Bulk Pools --}}
                @elseif($currentTab === 'fpo')
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @forelse($collectiveListings as $cl)
                            <div class="bg-white rounded-3xl border border-stone-200/80 shadow-2xs hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                                <div class="relative aspect-4/3 bg-emerald-950 overflow-hidden p-6 flex flex-col justify-between text-white">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/30 text-emerald-300 border border-emerald-400/40">
                                            FPO Collective Pool
                                        </span>
                                        <span class="text-[10px] bg-stone-900/80 px-2 py-0.5 rounded-full font-semibold">
                                            📍 {{ $cl->district }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="text-xs text-emerald-300 block font-semibold">{{ $cl->crop?->en_name }} ({{ $cl->variety }})</span>
                                        <h3 class="text-lg font-black text-white line-clamp-2 mt-1">{{ $cl->title }}</h3>
                                    </div>

                                    <div class="pt-3 border-t border-emerald-800/60 flex items-end justify-between">
                                        <div>
                                            <span class="text-[10px] text-emerald-300 uppercase block font-semibold">Bulk Volume</span>
                                            <strong class="text-base font-black text-white">{{ number_format((float)$cl->available_quantity_kg, 1) }} kg</strong>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-emerald-300 uppercase block font-bold">Grade</span>
                                            <strong class="text-sm font-bold text-white">{{ $cl->approved_grade }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <span class="text-xs text-stone-400 block mb-1">
                                            Aggregator: <strong class="text-stone-700">{{ $cl->fpoOrganization?->name }}</strong>
                                        </span>
                                        <p class="text-xs text-stone-600 line-clamp-2 mt-2 leading-relaxed">
                                            {{ $cl->description ?: 'Aggregated from verified smallholder member farmers with full individual source traceability.' }}
                                        </p>
                                        <div class="mt-3 flex items-center gap-2 text-[11px] text-stone-500">
                                            <span class="bg-stone-100 px-2 py-0.5 rounded">Harvest: {{ $cl->harvest_period ?: 'Current' }}</span>
                                            <span class="bg-stone-100 px-2 py-0.5 rounded">Pin: {{ $cl->pincode }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-mono text-stone-400">#{{ $cl->listing_number }}</span>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('rfqs.create', ['fpo_collective_listing_id' => $cl->id]) }}" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1">
                                                <span>Request Bulk RFQ</span>
                                                <span>&rarr;</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-white rounded-3xl p-12 text-center text-stone-500 border border-stone-200">
                                <span class="text-4xl mb-3 block">🏢</span>
                                <h4 class="text-base font-bold text-stone-800">No FPO Collective Lots Found</h4>
                                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                                    No collective bulk pools match your criteria.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if($collectiveListings->hasPages())
                        <div class="mt-8">
                            {{ $collectiveListings->links() }}
                        </div>
                    @endif
                @endif

            </main>
        </div>
    </div>
</div>
@endsection
