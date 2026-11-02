@extends('layouts.app')

@section('title', 'Certified Agri Products & Inputs Directory - NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Ecosystem</span>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Agri Products</span>
        </nav>

        <!-- No Checkout Policy & Demonstration Notice -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">🤝</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">No Direct Checkout Policy:</span>
                NxtPerson is an input discovery platform. We do not sell chemical formulations or transact payments online. All product listings connect farmers directly with licensed, certified Gujarat distributors and taluka stockists.
            </div>
        </div>

        <!-- Catalog Header & Search Controls -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                        <span>📦</span> Input Catalogue & Stockist Network
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                        Certified Agricultural Inputs
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-500 mt-1">
                        Showing {{ $products->total() }} verified seeds, bio-fertilizers, and crop protection products available across Gujarat dealers.
                    </p>
                </div>

                <!-- Search Input Form -->
                <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('crop'))
                        <input type="hidden" name="crop" value="{{ request('crop') }}">
                    @endif
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product, active ingredient, brand..." class="w-full rounded-xl border border-stone-300 py-2.5 pl-3.5 pr-8 text-xs sm:text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600 focus:outline-hidden">
                        @if(request('search'))
                            <a href="{{ route('products.index', array_diff_key(request()->query(), ['search' => ''])) }}" class="absolute right-2.5 top-2.5 text-stone-400 hover:text-stone-600 text-sm">✕</a>
                        @endif
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm transition-colors shrink-0">
                        Search
                    </button>
                </form>
            </div>

            <!-- Category Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-2 border-t border-stone-100 pt-4">
                <a href="{{ route('products.index', array_diff_key(request()->query(), ['category' => ''])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors {{ !request('category') ? 'bg-emerald-800 text-white shadow-2xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                    All Categories
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', array_merge(request()->query(), ['category' => $cat->slug])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 {{ request('category') === $cat->slug ? 'bg-emerald-800 text-white shadow-2xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                        <span>{{ $cat->icon }}</span>
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Crop & Brand Filters Row -->
            <div class="mt-4 pt-3 border-t border-stone-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-bold text-stone-600">Filter by Target Crop:</span>
                    <a href="{{ route('products.index', array_diff_key(request()->query(), ['crop' => ''])) }}" class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold {{ !request('crop') ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 text-stone-600' }}">
                        Any Crop
                    </a>
                    @foreach($crops as $cr)
                        <a href="{{ route('products.index', array_merge(request()->query(), ['crop' => $cr->slug])) }}" class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-colors {{ request('crop') === $cr->slug ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 text-stone-600 hover:border-emerald-300' }}">
                            {{ $cr->en_name }}
                        </a>
                    @endforeach
                </div>

                @if(request()->anyFilled(['category', 'crop', 'search', 'manufacturer']))
                    <a href="{{ route('products.index') }}" class="text-xs font-bold text-red-600 hover:underline">
                        Reset All Filters ✕
                    </a>
                @endif
            </div>
        </div>

        <!-- Product Categories Photographic Showcase -->
        <div class="mb-8">
            <h2 class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-3 flex items-center gap-1.5">
                <span>📁</span> Certified Gujarat Category Sectors
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" 
                       class="relative rounded-2xl overflow-hidden group border border-stone-200/80 shadow-xs hover:border-emerald-500 transition-all aspect-4/3 flex flex-col justify-end p-3 {{ request('category') === $cat->slug ? 'ring-2 ring-emerald-600' : '' }}">
                        <img src="{{ asset('images/categories/' . $cat->slug . '.jpg') }}" 
                             alt="{{ $cat->name }}" 
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-950/80 via-stone-900/30 to-transparent"></div>
                        <div class="relative z-10 text-white">
                            <span class="text-base mb-0.5 block">{{ $cat->icon }}</span>
                            <div class="font-bold text-xs leading-tight">{{ $cat->name }}</div>
                            <span class="text-[10px] text-emerald-300 font-medium">Browse inputs →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
            @forelse($products as $prod)
                <div class="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs hover:border-emerald-400 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Category Photographic Header -->
                        <div class="h-28 w-full rounded-2xl overflow-hidden mb-3 bg-stone-100 relative">
                            <img src="{{ asset('images/categories/' . $prod->category_slug . '.jpg') }}" 
                                 alt="{{ $prod->name }}" 
                                 class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-stone-950/70 via-transparent to-transparent"></div>
                            <span class="absolute bottom-2 left-2 text-[10px] font-bold text-white bg-stone-900/80 backdrop-blur-xs px-2 py-0.5 rounded-md border border-white/20">
                                {{ $prod->category ? $prod->category->name : 'Agri Input' }}
                            </span>
                        </div>

                        <!-- Top Metadata -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ $prod->category ? $prod->category->name : 'Agri Input' }}
                            </span>
                            @if($prod->is_featured)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200">
                                    ⭐ Featured
                                </span>
                            @endif
                        </div>

                        <!-- Brand / Manufacturer -->
                        <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider mb-1 truncate">
                            {{ $prod->manufacturer ? $prod->manufacturer->name : 'Certified Brand' }}
                        </div>

                        <!-- Product Name -->
                        <h2 class="text-base font-bold text-stone-900 group-hover:text-emerald-800 transition-colors leading-snug mb-1">
                            <a href="{{ route('products.show', $prod->slug) }}">
                                {{ $prod->name }}
                            </a>
                        </h2>

                        <!-- Active Ingredient -->
                        @if($prod->active_ingredient)
                            <p class="text-[11px] text-stone-500 font-mono mb-2 truncate">
                                {{ $prod->active_ingredient }}
                            </p>
                        @endif

                        <!-- Short Description -->
                        <p class="text-xs text-stone-600 line-clamp-2 leading-relaxed mb-3">
                            {{ $prod->description }}
                        </p>

                        <!-- Target Crop Badges -->
                        @if(!empty($prod->target_crops))
                            <div class="flex flex-wrap items-center gap-1 mb-3">
                                <span class="text-[10px] font-bold text-stone-400">Crops:</span>
                                @foreach($prod->target_crops as $tc)
                                    <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded bg-stone-100 text-stone-700 capitalize">
                                        {{ $tc }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Bar: Price Guide & Stockist Discovery CTA -->
                    <div class="pt-4 border-t border-stone-100">
                        <div class="flex items-baseline justify-between mb-2">
                            <span class="text-[11px] text-stone-500 font-medium">Price Guide:</span>
                            <span class="text-xs font-extrabold text-emerald-800">{{ $prod->price_guide ?? 'Inquire at Dealer' }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1">
                            <span class="text-[11px] text-stone-500 flex items-center gap-1">
                                <span>📍</span> {{ $prod->distributors->count() }} Stockists in Guj
                            </span>
                            <a href="{{ route('products.show', $prod->slug) }}" class="font-bold text-emerald-800 group-hover:text-emerald-950 flex items-center gap-1">
                                <span>View Stockists</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center bg-white rounded-3xl border border-stone-200">
                    <span class="text-4xl block mb-2">🔍</span>
                    <h3 class="text-base font-bold text-stone-900 mb-1">No products match your selected filters</h3>
                    <p class="text-xs text-stone-500 mb-4">Try clearing your search query or selecting a different crop category.</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 text-white font-bold text-xs">
                        View All Products
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
