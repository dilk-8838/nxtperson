@extends('layouts.app')

@section('title', "RFQ #{$rfq->rfq_number} — {$rfq->title}")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-4xl">
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('rfqs.index') }}" class="hover:text-emerald-800">RFQs</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">{{ $rfq->rfq_number }}</span>
        </nav>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700">✕</button>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-bold flex items-center justify-between">
                <span>ℹ️ {{ session('info') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-blue-700">✕</button>
            </div>
        @endif

        <!-- RFQ Header Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-stone-200 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-200">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#0c365b]">
                            {{ ucfirst(str_replace('_', ' ', $rfq->item_type)) }} Tender
                        </span>
                        <span class="text-xs font-mono text-stone-500">Ref: {{ $rfq->rfq_number }}</span>
                    </div>
                    <h1 class="text-2xl font-black text-[#0c365b] mt-1">
                        {{ $rfq->title }}
                    </h1>
                    <p class="text-xs text-stone-500 mt-0.5">
                        Issued by <strong>{{ $rfq->buyer->name ?? 'Commercial Buyer' }}</strong> • Tendered on {{ $rfq->created_at->format('M d, Y') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $rfq->getStatusBadgeClass() }}">
                        {{ ucfirst(str_replace('_', ' ', $rfq->status)) }}
                    </span>
                    @if($user->id === $rfq->buyer_id || $user->isAdmin())
                        @if($rfq->quotations->count() > 0)
                            <a href="{{ route('rfqs.compare', $rfq->id) }}" class="px-5 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                                <span>⚖️</span> Compare {{ $rfq->quotations->count() }} Quotes →
                            </a>
                        @endif
                    @elseif($rfq->isOpen())
                        @if($existingQuotation)
                            <a href="{{ route('quotations.show', $existingQuotation->id) }}" class="px-5 py-2.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 font-bold text-xs">
                                View Submitted Quote (#{{ $existingQuotation->quotation_number }})
                            </a>
                        @else
                            <a href="{{ route('quotations.create', $rfq->id) }}" class="px-5 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all">
                                Submit Quotation Bid →
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Parameters Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-stone-100 text-xs">
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Target Quantity</span>
                    <strong class="text-base font-black text-stone-900 mt-1 block">
                        {{ number_format((float)$rfq->quantity, 1) }} {{ $rfq->unit }}
                    </strong>
                </div>
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Target Unit Budget</span>
                    <strong class="text-base font-black text-stone-900 mt-1 block">
                        {{ $rfq->target_price_per_unit ? '₹'.number_format((float)$rfq->target_price_per_unit, 2).' / '.$rfq->unit : 'Open to Quotes' }}
                    </strong>
                </div>
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Destination Location</span>
                    <strong class="text-base font-black text-stone-900 mt-1 block">
                        {{ $rfq->delivery_district }}
                    </strong>
                </div>
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Required Fulfilment Date</span>
                    <strong class="text-base font-black text-stone-900 mt-1 block">
                        {{ $rfq->target_delivery_date ? $rfq->target_delivery_date->format('M d, Y') : 'Immediate' }}
                    </strong>
                </div>
            </div>

            <!-- Technical Specifications -->
            <div class="pt-6 space-y-4 text-xs">
                @if($rfq->delivery_address)
                    <div>
                        <strong class="text-stone-800 font-bold block mb-1">Delivery Destination / Warehouse Bay:</strong>
                        <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 text-stone-700">
                            {{ $rfq->delivery_address }}
                        </div>
                    </div>
                @endif

                @if($rfq->specifications)
                    <div>
                        <strong class="text-stone-800 font-bold block mb-1">Quality Standards & Commodity Specifications:</strong>
                        <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 text-stone-700 leading-relaxed">
                            {{ $rfq->specifications }}
                        </div>
                    </div>
                @endif

                @if($rfq->notes)
                    <div>
                        <strong class="text-stone-800 font-bold block mb-1">Commercial & Unloading Terms:</strong>
                        <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 text-stone-700 leading-relaxed">
                            {{ $rfq->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Invited Sellers & Quotation Tally (Visible to Buyer / Admin) -->
        @if($user->id === $rfq->buyer_id || $user->isAdmin())
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200">
                <div class="flex items-center justify-between pb-4 border-b border-stone-200 mb-4">
                    <h3 class="text-base font-bold text-[#0c365b]">
                        Commercial Bids Received ({{ $rfq->quotations->count() }})
                    </h3>
                    @if($rfq->quotations->count() > 0)
                        <a href="{{ route('rfqs.compare', $rfq->id) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                            Open Side-by-Side Comparison Matrix →
                        </a>
                    @endif
                </div>

                @if($rfq->quotations->isEmpty())
                    <p class="text-xs text-stone-400 py-6 text-center">
                        No quotations submitted yet by invited sellers.
                    </p>
                @else
                    <div class="space-y-3">
                        @foreach($rfq->quotations as $quote)
                            <div class="p-4 rounded-2xl border border-stone-200 text-xs flex items-center justify-between hover:bg-stone-50 transition-colors">
                                <div>
                                    <div class="font-bold text-[#0c365b]">{{ $quote->getSellerDisplayName() }}</div>
                                    <div class="text-[11px] text-stone-500 font-mono">
                                        Quote #{{ $quote->quotation_number }} • Valid until {{ $quote->valid_until->format('M d, Y') }}
                                    </div>
                                </div>
                                <div class="text-right flex items-center gap-4">
                                    <div>
                                        <div class="font-black text-stone-900 text-sm">
                                            ₹{{ number_format((float)$quote->total_amount, 2) }}
                                        </div>
                                        <div class="text-[11px] text-stone-500">
                                            ₹{{ number_format((float)$quote->unit_price, 2) }} / {{ $quote->unit }}
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $quote->getStatusBadgeClass() }}">
                                        {{ ucfirst($quote->status) }}
                                    </span>
                                    <a href="{{ route('quotations.show', $quote->id) }}" class="font-bold text-emerald-700 hover:text-emerald-900">
                                        Inspect →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
