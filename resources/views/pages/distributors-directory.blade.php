@extends('layouts.app')

@section('title', 'Gujarat Agro Distributors Directory & Pincode Locator - NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Ecosystem</span>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Distributor Directory</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Distributor records, license identifiers, and stock records are simulated demonstration data for Gujarat taluka clusters. Telephone numbers connect to demonstration routing lines.
            </div>
        </div>

        <!-- Search & Pincode Header Box -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                        <span>📍</span> Gujarat Fertilizer & Seed Channel Network
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                        Find Authorized Dealers by 6-Digit Pincode
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-500 mt-1">
                        Showing {{ $distributors->total() }} licensed input dealers across Rajkot, Anand, Banaskantha, and Gujarat agricultural talukas.
                    </p>
                </div>

                <!-- 6-digit Pincode / Keyword Search Form -->
                <form action="{{ route('distributors.index') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
                    <div class="relative flex-1">
                        <span class="absolute left-3.5 top-3 text-stone-400 text-xs">📮</span>
                        <input type="text" name="pincode" value="{{ request('pincode', (request('search') !== 'pincode' ? request('search') : '')) }}" placeholder="Enter 6-digit Pincode (e.g. 360001)..." maxlength="6" class="w-full rounded-xl border border-stone-300 py-2.5 pl-9 pr-8 text-xs sm:text-sm font-mono focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600 focus:outline-hidden">
                        @if(request('pincode') || request('search'))
                            <a href="{{ route('distributors.index') }}" class="absolute right-2.5 top-2.5 text-stone-400 hover:text-stone-600 text-sm">✕</a>
                        @endif
                    </div>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm transition-colors shrink-0">
                        Locate Dealers
                    </button>
                </form>
            </div>

            <!-- Fast Pincode Shortcut Chips -->
            <div class="flex flex-wrap items-center gap-2 pb-2 border-t border-stone-100 pt-4 text-xs">
                <span class="font-bold text-stone-600">Quick Pincode Matches:</span>
                <a href="{{ route('distributors.index', ['pincode' => '360001']) }}" class="px-2.5 py-1 rounded-lg border font-mono {{ request('pincode') === '360001' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 bg-stone-50 text-stone-700 hover:border-emerald-300' }}">
                    360001 (Rajkot)
                </a>
                <a href="{{ route('distributors.index', ['pincode' => '360311']) }}" class="px-2.5 py-1 rounded-lg border font-mono {{ request('pincode') === '360311' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 bg-stone-50 text-stone-700 hover:border-emerald-300' }}">
                    360311 (Gondal)
                </a>
                <a href="{{ route('distributors.index', ['pincode' => '388001']) }}" class="px-2.5 py-1 rounded-lg border font-mono {{ request('pincode') === '388001' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 bg-stone-50 text-stone-700 hover:border-emerald-300' }}">
                    388001 (Anand)
                </a>
                <a href="{{ route('distributors.index', ['pincode' => '385535']) }}" class="px-2.5 py-1 rounded-lg border font-mono {{ request('pincode') === '385535' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 bg-stone-50 text-stone-700 hover:border-emerald-300' }}">
                    385535 (Deesa)
                </a>
                <a href="{{ route('distributors.index', ['pincode' => '384170']) }}" class="px-2.5 py-1 rounded-lg border font-mono {{ request('pincode') === '384170' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 bg-stone-50 text-stone-700 hover:border-emerald-300' }}">
                    384170 (Unjha)
                </a>
                <a href="{{ route('distributors.index', ['pincode' => '395003']) }}" class="px-2.5 py-1 rounded-lg border font-mono {{ request('pincode') === '395003' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 bg-stone-50 text-stone-700 hover:border-emerald-300' }}">
                    395003 (Surat)
                </a>
                @if(request()->anyFilled(['pincode', 'search', 'district', 'category']))
                    <a href="{{ route('distributors.index') }}" class="text-red-600 font-bold hover:underline ml-auto">
                        Clear Search ✕
                    </a>
                @endif
            </div>

            <!-- District Filter Dropdown -->
            <div class="mt-4 pt-3 border-t border-stone-100 flex flex-wrap items-center gap-2 text-xs">
                <span class="font-bold text-stone-600">Filter by District:</span>
                <a href="{{ route('distributors.index', array_diff_key(request()->query(), ['district' => ''])) }}" class="px-2.5 py-1 rounded-lg border {{ !request('district') ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 text-stone-600' }}">
                    All Districts
                </a>
                @foreach($districts as $dst)
                    <a href="{{ route('distributors.index', array_merge(request()->query(), ['district' => $dst])) }}" class="px-2.5 py-1 rounded-lg border transition-colors {{ request('district') === $dst ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold' : 'border-stone-200 text-stone-600 hover:border-emerald-300' }}">
                        {{ $dst }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Distributors Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @forelse($distributors as $dist)
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs hover:border-emerald-400 hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <!-- Header with Pincode Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                📮 Pin: {{ $dist->pincode }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Verified Dealer
                            </span>
                        </div>

                        <!-- Business Name -->
                        <h2 class="text-base font-bold text-stone-900 leading-snug mb-0.5">
                            {{ $dist->business_name }}
                        </h2>
                        <div class="text-xs text-stone-500 mb-3">
                            Prop: <strong>{{ $dist->name }}</strong> • License: <span class="font-mono">{{ $dist->license_number }}</span>
                        </div>

                        <!-- Address -->
                        <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200/60 text-xs text-stone-600 mb-4 space-y-1">
                            <div>📍 {{ $dist->address }}</div>
                            <div class="font-semibold text-stone-800">Taluka: {{ $dist->taluka }} • {{ $dist->district }} District</div>
                        </div>

                        <!-- Authorized Categories -->
                        @if(!empty($dist->authorized_categories))
                            <div class="mb-4">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-1.5">Authorized Input Lines:</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($dist->authorized_categories as $cat)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-stone-100 text-stone-700 capitalize">
                                            {{ str_replace('-', ' ', $cat) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- In-Stock Products -->
                        @if($dist->products->isNotEmpty())
                            <div class="mb-4 border-t border-stone-100 pt-3">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-1.5">In-Stock at this Depot:</div>
                                <div class="space-y-1 text-xs">
                                    @foreach($dist->products->take(3) as $prod)
                                        <a href="{{ route('products.show', $prod->slug) }}" class="block font-medium text-emerald-800 hover:text-emerald-950 truncate">
                                            ✓ {{ $prod->name }}
                                        </a>
                                    @endforeach
                                    @if($dist->products->count() > 3)
                                        <span class="text-[10px] text-stone-400">+ {{ $dist->products->count() - 3 }} more products</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Contact & Communication Actions -->
                    <div class="pt-4 border-t border-stone-100 flex items-center gap-2">
                        <a href="tel:{{ $dist->phone }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5 shadow-xs">
                            <span>📞 Call Dealer</span>
                        </a>
                        <button type="button" onclick="alert('Simulated inquiry dispatched to {{ $dist->business_name }} (Pincode: {{ $dist->pincode }}). In live mode, this connects via Gujarat Kisan WhatsApp network.');" class="py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300 transition-colors">
                            💬 WhatsApp
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center bg-white rounded-3xl border border-stone-200">
                    <span class="text-4xl block mb-2">🔍</span>
                    <h3 class="text-base font-bold text-stone-900 mb-1">No distributors match this pincode or district</h3>
                    <p class="text-xs text-stone-500 mb-4">Try searching with a 2-digit district prefix (e.g. 36 for Rajkot, 38 for Anand/Ahmedabad) or clear your filter.</p>
                    <a href="{{ route('distributors.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 text-white font-bold text-xs">
                        View All Gujarat Distributors
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $distributors->links() }}
        </div>
    </div>
</div>
@endsection
