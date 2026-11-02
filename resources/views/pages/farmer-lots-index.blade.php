@extends('layouts.app')

@section('title', 'My Produce Lots & Grading Tracker — Nxtperson Private Limited')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-6xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('dashboard') }}" class="hover:text-emerald-800">Farmer Portal</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Produce Lots & Grading Tracker</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#00875a]/15 text-[#00875a] border border-[#00875a]/30">
                    <span class="w-2 h-2 rounded-full bg-[#00875a] animate-pulse"></span>
                    Farm-Gate Optical Quality Assurance
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight mt-2">
                    My Produce Lots
                </h1>
                <p class="text-slate-600 text-sm mt-0.5">
                    Track quality grading results, indicative APMC valuations, and sales status across your harvest submissions.
                </p>
            </div>
            <a href="{{ route('farmer.sell.create') }}" class="px-6 py-3 bg-[#0c365b] hover:bg-[#071f37] text-white font-bold rounded-full shadow-md shadow-[#0c365b]/20 transition-all text-sm flex items-center justify-center gap-2 hover:shadow-lg shrink-0">
                <span>📸</span>
                <span>Sell New Harvest Lot</span>
                <span>→</span>
            </a>
        </div>

        <!-- KPI Metrics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Declared Lots</span>
                <div class="text-2xl sm:text-3xl font-black text-stone-900 mt-1">
                    {{ $stats['total_lots'] }}
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">Registered in farmer account</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Active on Marketplace</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1">
                    {{ $stats['active_listings'] }}
                </div>
                <span class="text-[11px] text-emerald-800 font-medium mt-1 block">Open for buyer procurement</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Under Grading Review</span>
                <div class="text-2xl sm:text-3xl font-black text-amber-700 mt-1">
                    {{ $stats['under_review'] }}
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">In agronomist review queue</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Confirmed Intake</span>
                <div class="text-2xl sm:text-3xl font-black text-stone-900 mt-1">
                    {{ number_format((float)($stats['total_procured_kg'] / 1000), 1) }} <span class="text-base font-normal text-stone-500">Tonnes</span>
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">Physical intake confirmed</span>
            </div>
        </div>

        <!-- Lots Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-stone-200/80 overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-stone-200/80 bg-stone-50/50 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('farmer.lots.index') }}" class="px-3 py-1.5 rounded-xl font-bold {{ !request('status') ? 'bg-emerald-700 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        All Lots
                    </a>
                    <a href="{{ route('farmer.lots.index', ['status' => 'inventory_available']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ request('status') === 'inventory_available' ? 'bg-emerald-700 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        Active Stock
                    </a>
                    <a href="{{ route('farmer.lots.index', ['status' => 'under_review']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ request('status') === 'under_review' ? 'bg-emerald-700 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        In Review
                    </a>
                    <a href="{{ route('farmer.lots.index', ['status' => 'assessed']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ request('status') === 'assessed' ? 'bg-emerald-700 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        Assessed
                    </a>
                </div>

                <div class="text-xs text-stone-500 font-mono">
                    Showing {{ $lots->total() }} Lot(s)
                </div>
            </div>

            <!-- Lots Grid / Table -->
            <div class="divide-y divide-stone-100">
                @forelse($lots as $lot)
                    <div class="p-5 sm:p-6 hover:bg-stone-50/70 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <!-- Thumbnail -->
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-stone-100 border border-stone-200 shrink-0">
                                @if($lot->primaryImage)
                                    <img src="{{ asset($lot->primaryImage->image_path) }}" alt="{{ $lot->variety }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl">
                                        {{ $lot->crop->icon ?? '🌾' }}
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-extrabold text-stone-900">
                                        {{ $lot->crop->en_name ?? ucfirst($lot->crop_slug) }} — {{ $lot->variety }}
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $lot->getStatusBadgeClass() }}">
                                        {{ $lot->getStatusLabel() }}
                                    </span>
                                    @if($lot->approved_grade)
                                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $lot->getGradeBadgeClass() }}">
                                            Grade {{ $lot->approved_grade }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-stone-600 mt-1.5">
                                    <span>Ref: <strong class="font-mono text-stone-800">{{ $lot->lot_number }}</strong></span>
                                    <span>Batch: <strong>{{ number_format((float)$lot->quantity, 1) }} {{ ucfirst($lot->unit) }}</strong> ({{ number_format((float)$lot->quantity_in_kg, 1) }} kg)</span>
                                    <span>Harvest: {{ $lot->harvest_date->format('M d, Y') }}</span>
                                    <span>📍 {{ $lot->district }}, Gujarat</span>
                                </div>

                                @if($lot->latestAssessment)
                                    <p class="text-xs text-stone-500 mt-2 line-clamp-1 italic">
                                        "{{ $lot->latestAssessment->assessment_explanation }}"
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Right CTA -->
                        <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                            @if($lot->approved_procurement_price_per_kg)
                                <div class="text-right">
                                    <span class="text-[10px] text-stone-400 block uppercase font-bold">Approved Rate</span>
                                    <strong class="text-sm font-black text-emerald-800">₹{{ number_format((float)$lot->approved_procurement_price_per_kg, 2) }}/kg</strong>
                                </div>
                            @endif
                            <a href="{{ route('farmer.lots.show', $lot->id) }}" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                                <span>Inspect Report</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center text-stone-500">
                        <span class="text-4xl mb-3 block">🌾</span>
                        <h4 class="text-base font-bold text-stone-800">No Produce Lots Found</h4>
                        <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                            You haven't submitted any harvest lots for quality grading yet. Click below to register your first lot with photographs.
                        </p>
                        <a href="{{ route('farmer.sell.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-5 py-2.5 bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs">
                            + Sell Your First Produce Lot
                        </a>
                    </div>
                @endforelse
            </div>

            @if($lots->hasPages())
                <div class="p-4 border-t border-stone-200">
                    {{ $lots->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
