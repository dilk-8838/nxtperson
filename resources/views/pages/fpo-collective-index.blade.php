@extends('layouts.app')

@section('title', 'Collective Produce Listings — FPO Operations — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header & Sub-nav --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Collective Produce Listings</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        {{ $organization->name }}
                    </span>
                </div>
                <p class="text-slate-400 text-sm mt-1">
                    Pool smallholder member farm yields into verified bulk lots for high-value enterprise buyers.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('fpo.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    &larr; FPO Dashboard
                </a>
                <a href="{{ route('fpo.collective.create') }}" class="px-4 py-2 rounded-xl text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition">
                    + Aggregate Member Produce &rarr;
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-900/40 border border-emerald-700 text-emerald-200 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Metrics Overview --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Collective Lots</span>
                <span class="text-2xl font-extrabold text-white mt-1 block">{{ number_format($stats['total_listings']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Aggregated bulk listings</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-emerald-400 font-bold block">Total Volume Pooled</span>
                <span class="text-2xl font-extrabold text-emerald-300 mt-1 block">{{ number_format((float)$stats['total_volume_kg'], 1) }} kg</span>
                <span class="text-xs text-slate-400 mt-1 block">Accumulated member yield</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-sky-400 font-bold block">Available for Sale</span>
                <span class="text-2xl font-extrabold text-sky-300 mt-1 block">{{ number_format((float)$stats['available_volume_kg'], 1) }} kg</span>
                <span class="text-xs text-slate-400 mt-1 block">Ready for enterprise trade</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-amber-400 font-bold block">Traceable Member Lots</span>
                <span class="text-2xl font-extrabold text-amber-300 mt-1 block">{{ number_format($stats['total_aggregations']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Contributing farmer records</span>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-4">
            <form action="{{ route('fpo.collective.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <select name="crop" class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">All Crops</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->slug }}" {{ request('crop') === $crop->slug ? 'selected' : '' }}>
                                {{ $crop->en_name }} ({{ $crop->gu_name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-48">
                    <select name="status" class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published (Active)</option>
                        <option value="partially_sold" {{ request('status') === 'partially_sold' ? 'selected' : '' }}>Partially Committed</option>
                        <option value="sold_out" {{ request('status') === 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                        <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-700 text-white hover:bg-slate-600 transition">
                    Filter
                </button>
                @if(request()->hasAny(['crop', 'status']))
                    <a href="{{ route('fpo.collective.index') }}" class="text-xs text-slate-400 hover:text-white transition">
                        Reset Filters
                    </a>
                @endif
            </form>
        </div>

        {{-- Listings Table --}}
        @if($listings->isEmpty())
            <div class="bg-slate-800/40 rounded-2xl border border-slate-700 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-700/50 flex items-center justify-center text-slate-400 text-2xl font-bold">
                    🌾
                </div>
                <h3 class="text-lg font-bold text-white mb-1">No Collective Listings Created Yet</h3>
                <p class="text-sm text-slate-400 max-w-md mx-auto mb-6">
                    Aggregate verified member produce into consolidated bulk lots with full farmer traceability to attract wholesale commercial buyers.
                </p>
                <a href="{{ route('fpo.collective.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-500 transition">
                    Aggregate First Bulk Lot &rarr;
                </a>
            </div>
        @else
            <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-700">
                                <th class="py-4 px-6 font-semibold">Listing Number & Title</th>
                                <th class="py-4 px-4 font-semibold">Crop & Variety</th>
                                <th class="py-4 px-4 font-semibold">Quality Grade</th>
                                <th class="py-4 px-4 font-semibold">Pooled Yield</th>
                                <th class="py-4 px-4 font-semibold text-emerald-400">Available Qty</th>
                                <th class="py-4 px-4 font-semibold">Price / kg</th>
                                <th class="py-4 px-4 font-semibold">Status</th>
                                <th class="py-4 px-6 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-750">
                            @foreach($listings as $listing)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-4 px-6">
                                        <a href="{{ route('fpo.collective.show', $listing->id) }}" class="font-bold text-white hover:text-emerald-400 transition block">
                                            #{{ $listing->listing_number }}
                                        </a>
                                        <div class="text-xs text-slate-300 font-medium truncate max-w-xs mt-0.5">
                                            {{ $listing->title }}
                                        </div>
                                        <span class="text-[11px] text-slate-500">
                                            {{ $listing->memberAggregations->count() }} member {{ Str::plural('farmer', $listing->memberAggregations->count()) }} pooled
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-xs">
                                        <span class="font-semibold text-white block">{{ $listing->crop?->en_name ?? 'Agri Commodity' }}</span>
                                        <span class="text-slate-400">{{ $listing->variety }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-xs font-semibold text-slate-200">
                                        <span class="px-2 py-0.5 rounded bg-slate-700 text-slate-200 border border-slate-600">
                                            {{ $listing->approved_grade }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-slate-300">
                                        {{ number_format((float)$listing->total_aggregated_quantity_kg, 1) }} kg
                                    </td>
                                    <td class="py-4 px-4 font-bold text-emerald-300">
                                        {{ number_format((float)$listing->available_quantity_kg, 1) }} kg
                                    </td>
                                    <td class="py-4 px-4 font-semibold text-white">
                                        {{ $listing->asking_price_per_kg ? '₹'.number_format((float)$listing->asking_price_per_kg, 2) : 'Open / Mandi' }}
                                    </td>
                                    <td class="py-4 px-4">
                                        @php
                                            $badgeClasses = [
                                                'published' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                                'partially_sold' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                                'sold_out' => 'bg-slate-600/20 text-slate-400 border-slate-600/30',
                                                'withdrawn' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClasses[$listing->status] ?? 'bg-slate-700 text-slate-300' }}">
                                            {{ ucfirst(str_replace('_', ' ', $listing->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('fpo.collective.show', $listing->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-700 text-slate-200 hover:bg-slate-600 transition">
                                            Manage Pool &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($listings->hasPages())
                    <div class="px-6 py-4 bg-slate-850 border-t border-slate-700">
                        {{ $listings->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection
