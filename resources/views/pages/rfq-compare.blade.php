@extends('layouts.app')

@section('title', 'Compare Quotations — RFQ #' . $rfq->rfq_number . ' — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Breadcrumb & Header --}}
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-400 mb-3">
                <a href="{{ route('rfqs.index') }}" class="hover:text-emerald-400 transition">RFQs</a>
                <span>&rarr;</span>
                <a href="{{ route('rfqs.show', $rfq->id) }}" class="hover:text-emerald-400 transition">RFQ #{{ $rfq->rfq_number }}</a>
                <span>&rarr;</span>
                <span class="text-slate-200">Quotation Comparison Matrix</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Quotation Comparison</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            {{ $quotations->count() }} {{ Str::plural('Quotation', $quotations->count()) }} Received
                        </span>
                    </div>
                    <p class="text-slate-400 text-sm mt-1">
                        Side-by-side vendor quotation analysis for RFQ: <span class="text-white font-medium">{{ $rfq->title }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('rfqs.show', $rfq->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                        &larr; Back to RFQ
                    </a>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-900/40 border border-emerald-700 text-emerald-200 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- RFQ Specs Summary Card --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-4">Target Requirement Specifications</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-slate-400 block">Requested Quantity</span>
                    <span class="text-base font-bold text-white">{{ number_format((float)$rfq->quantity, 1) }} {{ $rfq->unit }}</span>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-slate-400 block">Target Unit Price</span>
                    <span class="text-base font-bold text-white">
                        {{ $rfq->target_price_per_unit ? '₹'.number_format((float)$rfq->target_price_per_unit, 2).' / '.$rfq->unit : 'Open / Unspecified' }}
                    </span>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-slate-400 block">Delivery Location</span>
                    <span class="text-base font-semibold text-white">{{ $rfq->delivery_district }}</span>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-slate-400 block">Required By Date</span>
                    <span class="text-base font-semibold text-white">{{ $rfq->target_delivery_date?->format('d M Y') ?? 'Flexible' }}</span>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                    <span class="text-xs text-slate-400 block">RFQ Status</span>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        {{ ucfirst(str_replace('_', ' ', $rfq->status)) }}
                    </span>
                </div>
            </div>
        </div>

        @if($quotations->isEmpty())
            <div class="bg-slate-800/40 rounded-2xl border border-slate-700 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-700/50 flex items-center justify-center text-slate-400 text-2xl font-bold">
                    ⚖️
                </div>
                <h3 class="text-lg font-bold text-white mb-1">No Quotations to Compare Yet</h3>
                <p class="text-sm text-slate-400 max-w-md mx-auto mb-6">
                    Sellers invited to this RFQ have not yet submitted their offers. You will be notified as soon as quotes arrive.
                </p>
                <a href="{{ route('rfqs.show', $rfq->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-500 transition">
                    View RFQ Status
                </a>
            </div>
        @else
            {{-- Quotation Comparison Table --}}
            <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 overflow-hidden shadow-2xl">
                <div class="px-6 py-4 border-b border-slate-700/70 bg-slate-850 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-white text-base">Quotation Evaluation Matrix</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Ranked automatically from lowest total cost to highest</p>
                    </div>
                    <span class="text-xs text-slate-400">Strictly confidential to buyer</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-700">
                                <th class="py-4 px-6 font-semibold">Vendor / Seller</th>
                                <th class="py-4 px-4 font-semibold">Quoted Qty</th>
                                <th class="py-4 px-4 font-semibold">Unit Price</th>
                                <th class="py-4 px-4 font-semibold">Extra & Tax</th>
                                <th class="py-4 px-4 font-semibold text-emerald-400">Total Amount</th>
                                <th class="py-4 px-4 font-semibold">Est. Delivery</th>
                                <th class="py-4 px-4 font-semibold">Quote Validity</th>
                                <th class="py-4 px-4 font-semibold">Status</th>
                                <th class="py-4 px-6 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-750">
                            @foreach($quotations as $index => $quote)
                                <tr class="hover:bg-slate-800/40 transition {{ $quote->status === 'accepted' ? 'bg-emerald-950/20' : '' }}">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            @if($index === 0 && $quote->status === 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                    Best Value
                                                </span>
                                            @endif
                                            <div>
                                                <a href="{{ route('quotations.show', $quote->id) }}" class="font-bold text-white hover:text-emerald-400 transition block">
                                                    {{ $quote->getSellerDisplayName() }}
                                                </a>
                                                <span class="text-xs text-slate-400 capitalize">
                                                    {{ $quote->seller_type }} &bull; Ref: {{ $quote->quotation_number }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-slate-200">
                                        {{ number_format((float)$quote->quoted_quantity, 1) }} {{ $quote->unit }}
                                    </td>
                                    <td class="py-4 px-4 font-semibold text-white">
                                        ₹{{ number_format((float)$quote->unit_price, 2) }}
                                        <span class="text-xs text-slate-400 font-normal">/ {{ $quote->unit }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-xs text-slate-400">
                                        + ₹{{ number_format((float)($quote->additional_charges + $quote->tax_amount), 2) }}
                                    </td>
                                    <td class="py-4 px-4 font-extrabold text-emerald-300 text-base">
                                        ₹{{ number_format((float)$quote->total_amount, 2) }}
                                    </td>
                                    <td class="py-4 px-4 text-xs text-slate-300">
                                        {{ $quote->estimated_delivery_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="py-4 px-4 text-xs">
                                        @if($quote->valid_until && $quote->valid_until->isPast())
                                            <span class="text-rose-400 font-medium">Expired ({{ $quote->valid_until->format('d M') }})</span>
                                        @else
                                            <span class="text-slate-300">{{ $quote->valid_until?->format('d M Y') ?? '—' }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                                'accepted' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                                'rejected' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                                'withdrawn' => 'bg-slate-600/20 text-slate-400 border-slate-600/30',
                                                'expired' => 'bg-slate-700/30 text-slate-400 border-slate-600/30',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$quote->status] ?? 'bg-slate-700 text-slate-300 border-slate-600' }}">
                                            {{ ucfirst($quote->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-2">
                                        <a href="{{ route('quotations.show', $quote->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-700 text-slate-200 hover:bg-slate-600 transition">
                                            View Details
                                        </a>

                                        @if($quote->isAcceptable() && $rfq->isOpen() && $user->isBuyer() && $rfq->buyer_id === $user->id)
                                            <form action="{{ route('quotations.accept', $quote->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to ACCEPT this quotation? This will lock in stock and immediately generate Purchase Order #PO. Other bids will be marked closed.')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-sm transition">
                                                    Accept & Issue PO &rarr;
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Explanatory notice --}}
            <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/60 text-xs text-slate-400 flex items-start gap-3">
                <span class="text-base text-emerald-400">🛡️</span>
                <p>
                    <strong>Stock Integrity Assurance:</strong> Accepting an offer triggers a transactional lock to verify that the seller has not committed the stock elsewhere. Upon acceptance, the system automatically marks the RFQ as closed, marks alternative vendor bids as closed, and produces a traceable Purchase Order.
                </p>
            </div>
        @endif

    </div>
</div>
@endsection
