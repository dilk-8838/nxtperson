@extends('layouts.app')

@section('title', "Produce Lot #{$lot->lot_number} — Quality Assessment & Valuation")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-5xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('farmer.lots.index') }}" class="hover:text-emerald-800">Farmer Produce Lots</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">{{ $lot->lot_number }}</span>
        </nav>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>✓</span> {{ session('success') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        <!-- Lot Header Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200/80 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-100">
                <div class="flex items-start gap-3.5">
                    <span class="text-4xl p-2 rounded-2xl bg-stone-100 border border-stone-200 shrink-0">
                        {{ $lot->crop->icon ?? '🌾' }}
                    </span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">
                                {{ $lot->crop->en_name ?? ucfirst($lot->crop_slug) }} — {{ $lot->variety }}
                            </h1>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $lot->getStatusBadgeClass() }}">
                                {{ $lot->getStatusLabel() }}
                            </span>
                        </div>
                        <p class="text-xs text-stone-500 font-mono mt-1">
                            Lot Reference: <strong class="text-stone-800">{{ $lot->lot_number }}</strong> • Submitted {{ $lot->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:self-start">
                    @if(!in_array($lot->status, ['sold_out', 'cancelled']))
                        <a href="{{ route('farmer.lots.edit', $lot->id) }}" class="px-3.5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold rounded-xl text-xs transition-colors flex items-center gap-1">
                            <span>✏️</span> Edit Listing
                        </a>
                        <form action="{{ route('farmer.lots.withdraw', $lot->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to withdraw this produce listing from the marketplace?');">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-semibold rounded-xl text-xs transition-colors border border-red-200">
                                Withdraw
                            </button>
                        </form>
                    @endif
                    @if($lot->status === 'assessed')
                        <form action="{{ route('farmer.lots.submit', $lot->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-xs transition-colors flex items-center gap-1.5">
                                <span>📤</span>
                                <span>Submit for Admin Review</span>
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('farmer.sell.create') }}" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold rounded-xl text-xs transition-colors">
                        + Sell Another Lot
                    </a>
                </div>
            </div>

            <!-- Quick Specs Row -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 text-xs">
                <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200/70">
                    <span class="text-stone-500 block uppercase tracking-wider text-[10px] font-bold">Declared Batch</span>
                    <strong class="text-sm font-black text-stone-900 mt-0.5 block">
                        {{ number_format((float)$lot->quantity, 1) }} {{ ucfirst($lot->unit) }}
                    </strong>
                    <span class="text-[11px] text-stone-500">({{ number_format((float)$lot->quantity_in_kg, 1) }} kg)</span>
                </div>

                <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200/70">
                    <span class="text-stone-500 block uppercase tracking-wider text-[10px] font-bold">Location</span>
                    <strong class="text-sm font-black text-stone-900 mt-0.5 block truncate">
                        {{ $lot->district }}, Gujarat
                    </strong>
                    <span class="text-[11px] text-stone-500">PIN: {{ $lot->pincode }}</span>
                </div>

                <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200/70">
                    <span class="text-stone-500 block uppercase tracking-wider text-[10px] font-bold">Harvest Date</span>
                    <strong class="text-sm font-black text-stone-900 mt-0.5 block">
                        {{ $lot->harvest_date->format('M d, Y') }}
                    </strong>
                    <span class="text-[11px] text-stone-500">{{ ucfirst($lot->farming_method) }}</span>
                </div>

                <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200/70">
                    <span class="text-stone-500 block uppercase tracking-wider text-[10px] font-bold">Quality Grade</span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="px-2 py-0.5 rounded text-xs font-black {{ $lot->getGradeBadgeClass() }}">
                            Grade {{ $lot->approved_grade ?? 'Pending' }}
                        </span>
                    </div>
                    <span class="text-[11px] text-stone-500">Automated Assessment</span>
                </div>
            </div>
        </div>

        <!-- 2-Column Core: Grading Assessment & Indicative Valuation -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Quality Assessment Card -->
            @php $latestAssessment = $lot->latestAssessment; @endphp
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-stone-200/80 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                        <h3 class="text-base font-bold text-stone-900 flex items-center gap-2">
                            <span>🔬</span> AI Quality Grading Assessment
                        </h3>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                            {{ $latestAssessment->engine_version ?? 'v1.0.0-sample' }}
                        </span>
                    </div>

                    @if($latestAssessment)
                        <!-- Grade Classification Banner -->
                        <div class="p-4 rounded-2xl mb-4 {{ $latestAssessment->grade === 'Good' ? 'bg-emerald-50 border border-emerald-200' : ($latestAssessment->grade === 'Intermediate' ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200') }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider {{ $latestAssessment->grade === 'Good' ? 'text-emerald-800' : ($latestAssessment->grade === 'Intermediate' ? 'text-amber-800' : 'text-red-800') }}">
                                    Evaluated Classification
                                </span>
                                <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full {{ $latestAssessment->grade === 'Good' ? 'bg-emerald-600 text-white' : ($latestAssessment->grade === 'Intermediate' ? 'bg-amber-600 text-white' : 'bg-red-600 text-white') }}">
                                    {{ $latestAssessment->getGradeTitle() }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-700 mt-2 leading-relaxed">
                                {{ $latestAssessment->assessment_explanation }}
                            </p>
                        </div>

                        <!-- Defect Analysis -->
                        <div class="mb-4">
                            <span class="text-[11px] font-bold text-stone-600 uppercase tracking-wider block mb-2">Observed Defect & Quality Cues</span>
                            @if(!empty($latestAssessment->observed_defects))
                                <ul class="space-y-1.5 text-xs text-stone-700">
                                    @foreach($latestAssessment->observed_defects as $defect)
                                        <li class="flex items-start gap-2">
                                            <span class="text-emerald-700 shrink-0 mt-0.5">✓</span>
                                            <span>{{ $defect }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-stone-500 italic">No defects observed in visual sample analysis.</p>
                            @endif
                        </div>

                        <!-- Verification Metadata -->
                        <div class="p-3 rounded-xl bg-stone-50 border border-stone-200 text-[11px] text-stone-600 space-y-1">
                            <div class="flex justify-between">
                                <span class="text-stone-500">Confidence Metric:</span>
                                <span class="font-semibold text-stone-900">{{ $latestAssessment->confidence_score }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-stone-500">Grading Method:</span>
                                <span class="font-semibold text-stone-900">{{ $latestAssessment->grading_method }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-stone-500">Physical Sorting Needed:</span>
                                <span class="font-semibold text-amber-700">{{ $latestAssessment->manual_verification_required ? 'Mandatory at warehouse intake' : 'Verified' }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-stone-500 italic py-4">Assessment pending evaluation.</p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-stone-100 text-[10px] text-stone-400">
                    Note: AI assessment is provisional guidance. Admin may review or override grade prior to purchase confirmation.
                </div>
            </div>

            <!-- Indicative Pricing & Lot Valuation Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-stone-200/80 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                        <h3 class="text-base font-bold text-stone-900 flex items-center gap-2">
                            <span>📊</span> Indicative Benchmark Valuation
                        </h3>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                            APMC Linked
                        </span>
                    </div>

                    <!-- Primary Valuation Display -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-900 via-emerald-800 to-green-950 text-white mb-4 shadow-sm">
                        <span class="text-emerald-200 text-xs font-semibold uppercase tracking-wider block">Estimated Lot Valuation</span>
                        <div class="text-2xl sm:text-3xl font-black mt-1 tracking-tight">
                            ₹{{ number_format((float)$pricing['estimated_lot_valuation'], 2) }}
                        </div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-emerald-700/60 text-xs text-emerald-100">
                            <span>Indicative Rate: <strong>₹{{ number_format((float)$pricing['indicative_price_per_kg'], 2) }} / kg</strong></span>
                            <span>Range: {{ $pricing['indicative_price_range'] }}</span>
                        </div>
                    </div>

                    <!-- Breakdown Table -->
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-stone-100">
                            <span class="text-stone-500">Mandi Benchmark Yard:</span>
                            <strong class="text-stone-900">{{ $pricing['benchmark_mandi_yard'] }}</strong>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-stone-100">
                            <span class="text-stone-500">Benchmark Modal Rate:</span>
                            <span class="text-stone-900 font-semibold">₹{{ number_format((float)$pricing['benchmark_modal_price_qtl'], 2) }} / Qtl</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-stone-100">
                            <span class="text-stone-500">Quality Multiplier:</span>
                            <span class="font-bold {{ $pricing['grade_multiplier'] > 1 ? 'text-emerald-700' : 'text-stone-900' }}">
                                {{ $pricing['grade_multiplier'] }}x ({{ ($pricing['grade_multiplier'] - 1) * 100 > 0 ? '+' : '' }}{{ round(($pricing['grade_multiplier'] - 1) * 100) }}%)
                            </span>
                        </div>
                        @if($lot->approved_procurement_price_per_kg)
                            <div class="flex justify-between py-1.5 bg-emerald-50 px-2 rounded-lg">
                                <span class="font-bold text-emerald-900">Final Approved Procurement Rate:</span>
                                <strong class="text-emerald-900 text-sm">₹{{ number_format((float)$lot->approved_procurement_price_per_kg, 2) }} / kg</strong>
                            </div>
                        @endif
                    </div>

                    <!-- Calculation Explanation -->
                    <p class="text-[11px] text-stone-500 leading-relaxed mt-3 bg-stone-50 p-2.5 rounded-xl border border-stone-100">
                        {{ $pricing['calculation_explanation'] }}
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-stone-100 text-[10px] text-stone-400">
                    Source: {{ $pricing['data_source'] }}. Sample data for evaluation demonstration.
                </div>
            </div>
        </div>

        <!-- Uploaded Batch Photographs Gallery -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200/80 mb-8">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                <h3 class="text-base font-bold text-stone-900 flex items-center gap-2">
                    <span>📸</span> Uploaded Batch Photographs ({{ $lot->images->count() }})
                </h3>
                <span class="text-xs text-stone-500">
                    Randomized Secure Storage
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @forelse($lot->images as $img)
                    <div class="relative group rounded-2xl overflow-hidden border border-stone-200 bg-stone-100 aspect-square shadow-2xs">
                        <img src="{{ asset($img->image_path) }}" alt="Produce Photograph" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @if($img->is_primary)
                            <span class="absolute top-2 left-2 bg-emerald-700 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs">
                                Primary Angle
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-xs text-stone-400">
                        No batch photographs uploaded.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Stock Commitment & Buyer Activity Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Buyer Reservations & Orders -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200">
                <h3 class="text-sm font-bold text-[#0c365b] flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <span class="flex items-center gap-2">
                        <span>🛒</span> Buyer Reservations & Orders ({{ $lot->reservations->count() }})
                    </span>
                    <span class="text-xs font-normal text-stone-500">
                        Reserved: <strong>{{ number_format((float)$lot->reserved_quantity_kg, 1) }} kg</strong>
                    </span>
                </h3>

                @if($lot->reservations->isEmpty())
                    <p class="text-xs text-stone-400 py-6 text-center">
                        No direct buyer reservations placed yet on this lot.
                    </p>
                @else
                    <div class="space-y-3">
                        @foreach($lot->reservations as $res)
                            <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200 text-xs flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-stone-800">
                                        {{ $res->buyer->name ?? 'Commercial Buyer' }}
                                    </div>
                                    <div class="text-[11px] text-stone-500">
                                        Order #{{ $res->reservation_number }} • {{ $res->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-emerald-700">
                                        {{ number_format((float)$res->quantity_kg, 1) }} kg
                                    </div>
                                    <div class="text-[11px] font-semibold text-stone-600">
                                        ₹{{ number_format((float)$res->total_price, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- FPO Collective Aggregation Tracking -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200">
                <h3 class="text-sm font-bold text-[#0c365b] flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <span class="flex items-center gap-2">
                        <span>🤝</span> FPO Collective Sales Pooling ({{ $lot->memberAggregations->count() }})
                    </span>
                    <span class="text-xs font-normal text-stone-500">
                        Pooled: <strong>{{ number_format((float)$lot->aggregated_quantity_kg, 1) }} kg</strong>
                    </span>
                </h3>

                @if($lot->memberAggregations->isEmpty())
                    <p class="text-xs text-stone-400 py-6 text-center">
                        This lot has not been pooled into any FPO collective sale.
                    </p>
                @else
                    <div class="space-y-3">
                        @foreach($lot->memberAggregations as $agg)
                            <div class="p-3 rounded-2xl bg-emerald-50/50 border border-emerald-200 text-xs flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-[#0c365b]">
                                        {{ $agg->fpoOrganization->name ?? 'FPO Organization' }}
                                    </div>
                                    <div class="text-[11px] text-stone-500">
                                        Status: <span class="capitalize font-medium text-emerald-700">{{ $agg->status }}</span> • Pooled {{ $agg->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-emerald-800">
                                        {{ number_format((float)$agg->committed_quantity_kg, 1) }} kg
                                    </div>
                                    <span class="text-[10px] text-stone-500">Traceable Member Stock</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Procurement State Machine Timeline -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200/80">
            <h3 class="text-base font-bold text-stone-900 flex items-center gap-2 mb-4 pb-2 border-b border-stone-100">
                <span>🔄</span> Procurement & Marketplace Publication State Machine
            </h3>

            @php
                $stages = [
                    ['key' => 'submitted', 'label' => 'Submitted', 'desc' => 'Harvest declared with photos'],
                    ['key' => 'assessed', 'label' => 'Quality Assessed', 'desc' => 'AI grading & valuation generated'],
                    ['key' => 'under_review', 'label' => 'Under Review', 'desc' => 'Agronomist verification queue'],
                    ['key' => 'approved_for_procurement', 'label' => 'Procurement Approved', 'desc' => 'Rate approved by administrator'],
                    ['key' => 'purchase_confirmed', 'label' => 'Purchase Confirmed', 'desc' => 'Physical intake & quantity verified'],
                    ['key' => 'inventory_available', 'label' => 'Stock Published', 'desc' => 'Live on Buyer Marketplace'],
                ];

                $currentStatus = $lot->status;
                $statusOrder = ['draft', 'submitted', 'assessed', 'under_review', 'approved_for_procurement', 'purchase_confirmed', 'inventory_available', 'partially_sold', 'sold_out'];
                $currentIndex = array_search($currentStatus, $statusOrder);
                if ($currentIndex === false) $currentIndex = 0;
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-center">
                @foreach($stages as $idx => $stg)
                    @php
                        $stgIdx = array_search($stg['key'], $statusOrder);
                        $isCompleted = $currentIndex >= $stgIdx;
                        $isCurrent = $currentStatus === $stg['key'];
                    @endphp
                    <div class="p-3 rounded-2xl border text-xs {{ $isCurrent ? 'bg-emerald-50 border-emerald-500 font-bold text-emerald-950 shadow-xs' : ($isCompleted ? 'bg-stone-50 border-emerald-300 text-stone-800' : 'bg-stone-50/50 border-stone-200 text-stone-400') }}">
                        <div class="w-6 h-6 rounded-full mx-auto flex items-center justify-center text-xs font-bold mb-1.5 {{ $isCompleted ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-500' }}">
                            {{ $isCompleted ? '✓' : $idx + 1 }}
                        </div>
                        <div class="font-bold text-[11px] leading-tight">{{ $stg['label'] }}</div>
                        <div class="text-[10px] text-stone-500 mt-1 leading-snug hidden sm:block">{{ $stg['desc'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
