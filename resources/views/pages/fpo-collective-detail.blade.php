@extends('layouts.app')

@section('title', 'Collective Bulk Lot #' . $listing->listing_number . ' — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-8">

        {{-- Breadcrumb & Title --}}
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-400 mb-3">
                <a href="{{ route('fpo.collective.index') }}" class="hover:text-emerald-400 transition">Collective Listings</a>
                <span>&rarr;</span>
                <span class="text-slate-200">#{{ $listing->listing_number }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Bulk Lot #{{ $listing->listing_number }}</h1>
                        @php
                            $badgeClasses = [
                                'published' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                'partially_sold' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                'sold_out' => 'bg-slate-600/20 text-slate-400 border-slate-600/30',
                                'withdrawn' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClasses[$listing->status] ?? 'bg-slate-700 text-slate-300' }}">
                            {{ ucfirst(str_replace('_', ' ', $listing->status)) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $listing->fpoOrganization?->name }}
                        </span>
                    </div>
                    <p class="text-slate-400 text-sm mt-1">
                        {{ $listing->title }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('fpo.collective.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                        &larr; All Listings
                    </a>

                    @if(in_array($listing->status, ['published', 'partially_sold']))
                        <form action="{{ route('fpo.collective.withdraw', $listing->id) }}" method="POST" onsubmit="return confirm('Withdraw this collective listing? All uncommitted member produce will be released immediately back to member farmers.')">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-rose-900/40 text-rose-300 hover:bg-rose-900/70 border border-rose-700 transition">
                                Withdraw &amp; Release Stock
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-900/40 border border-emerald-700 text-emerald-200 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Metrics Row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Total Pooled</span>
                <span class="text-2xl font-extrabold text-white mt-1 block">{{ number_format((float)$listing->total_aggregated_quantity_kg, 1) }} kg</span>
                <span class="text-xs text-slate-400 mt-1 block">{{ $listing->memberAggregations->count() }} farmers pooled</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-emerald-400 font-bold block">Available Stock</span>
                <span class="text-2xl font-extrabold text-emerald-300 mt-1 block">{{ number_format((float)$listing->available_quantity_kg, 1) }} kg</span>
                <span class="text-xs text-slate-400 mt-1 block">Open for enterprise quotation</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-amber-400 font-bold block">Reserved / Committed</span>
                <span class="text-2xl font-extrabold text-amber-300 mt-1 block">{{ number_format((float)$listing->reserved_quantity_kg, 1) }} kg</span>
                <span class="text-xs text-slate-400 mt-1 block">In pending contracts</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-sky-400 font-bold block">Sold / Delivered</span>
                <span class="text-2xl font-extrabold text-sky-300 mt-1 block">{{ number_format((float)$listing->sold_quantity_kg, 1) }} kg</span>
                <span class="text-xs text-slate-400 mt-1 block">Completed trade volume</span>
            </div>
        </div>

        {{-- Bulk Lot Specifications --}}
        <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-6 space-y-6">
            <h2 class="text-base font-bold text-white border-b border-slate-700/60 pb-3">Commodity &amp; Warehouse Specifications</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Commodity</span>
                    <span class="font-bold text-white">{{ $listing->crop?->en_name ?? 'Agri Produce' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Variety / Hybrid</span>
                    <span class="font-bold text-white">{{ $listing->variety }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Approved Grade</span>
                    <span class="font-bold text-emerald-400">{{ $listing->approved_grade }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Asking Benchmark Price</span>
                    <span class="font-bold text-white">
                        {{ $listing->asking_price_per_kg ? '₹'.number_format((float)$listing->asking_price_per_kg, 2).' / kg' : 'Market Bidding' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Consolidation District</span>
                    <span class="font-semibold text-white">{{ $listing->district }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Warehouse Pincode</span>
                    <span class="font-semibold text-white">{{ $listing->pincode }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Harvest Period</span>
                    <span class="font-semibold text-white">{{ $listing->harvest_period ?: 'Current Season' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Creation Date</span>
                    <span class="font-semibold text-white">{{ $listing->created_at->format('d M Y') }}</span>
                </div>
            </div>

            @if($listing->description)
                <div class="pt-4 border-t border-slate-700/60">
                    <span class="text-xs text-slate-400 block">Consolidation &amp; Quality Notes:</span>
                    <p class="text-sm text-slate-300 mt-1 italic">{{ $listing->description }}</p>
                </div>
            @endif
        </div>

        {{-- Contributing Member Roster (Traceability Table) --}}
        <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 overflow-hidden shadow-xl">
            <div class="px-6 py-4 bg-slate-850 border-b border-slate-700/70 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-white text-base">Traceable Member Farmer Contributions</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Direct source traceability to individual smallholder farmers</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    {{ $listing->memberAggregations->count() }} Source Lots
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-700">
                            <th class="py-3 px-6">Member Farmer</th>
                            <th class="py-3 px-4">Contact & Location</th>
                            <th class="py-3 px-4">Source Produce Lot</th>
                            <th class="py-3 px-4 text-right">Committed Yield</th>
                            <th class="py-3 px-4 text-center">Pooling Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-750">
                        @foreach($listing->memberAggregations as $agg)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-4 px-6 font-semibold text-white">
                                    {{ $agg->farmer?->name ?? 'Member Farmer' }}
                                    <div class="text-xs text-slate-400 font-normal">Member ID: #FM-{{ $agg->farmer_user_id }}</div>
                                </td>
                                <td class="py-4 px-4 text-xs text-slate-300">
                                    {{ $agg->farmer?->phone ?? '—' }}
                                    <span class="text-slate-500 block">{{ $agg->farmer?->village ? $agg->farmer->village . ', ' : '' }}{{ $agg->farmer?->district ?? 'Gujarat' }}</span>
                                </td>
                                <td class="py-4 px-4 text-xs">
                                    @if($agg->produceLot)
                                        <a href="{{ route('produce.show', $agg->produceLot->id) }}" class="font-bold text-emerald-400 hover:underline">
                                            #{{ $agg->produceLot->lot_number }}
                                        </a>
                                        <span class="text-slate-400 block">Grade: {{ $agg->produceLot->approved_grade ?? 'Standard' }}</span>
                                    @else
                                        <span class="text-slate-500">Unspecified Lot</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-white">
                                    {{ number_format((float)$agg->committed_quantity_kg, 1) }} kg
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @php
                                        $aggStatusColors = [
                                            'pooled' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                            'sold' => 'bg-sky-500/20 text-sky-300 border-sky-500/30',
                                            'released' => 'bg-slate-700/40 text-slate-400 border-slate-600',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $aggStatusColors[$agg->status] ?? 'bg-slate-700 text-slate-300' }}">
                                        {{ ucfirst($agg->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Linked Purchase Orders / Enterprise Contracts --}}
        @if($listing->purchaseOrders && $listing->purchaseOrders->isNotEmpty())
            <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-6 space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span>📦</span> Associated Enterprise Purchase Orders
                </h3>
                <div class="space-y-3">
                    @foreach($listing->purchaseOrders as $po)
                        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700/50 flex items-center justify-between">
                            <div>
                                <a href="{{ route('orders.show', $po->id) }}" class="font-bold text-white hover:text-emerald-400">
                                    #{{ $po->po_number }}
                                </a>
                                <span class="text-xs text-slate-400 block mt-0.5">
                                    Buyer: {{ $po->buyer?->name }} &bull; Quantity: {{ number_format((float)$po->quantity, 1) }} {{ $po->unit }} &bull; Total: ₹{{ number_format((float)$po->total_amount, 2) }}
                                </span>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $po->getStatusBadgeClass() }}">
                                {{ $po->getStatusLabel() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
