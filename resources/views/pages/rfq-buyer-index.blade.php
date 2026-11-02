@extends('layouts.app')

@section('title', 'My Request for Quotations (RFQs) — NxtPerson Agritech')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-[#0c365b]">
                    📄 Commercial Tendering
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight mt-1">
                    Request for Quotations (RFQs)
                </h1>
                <p class="text-xs sm:text-sm text-stone-600 mt-0.5">
                    Track issued procurement tenders, inspect quotations received from sellers, and compare offers.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('buyer.dashboard') }}" class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                    ← Buyer Dashboard
                </a>
                <a href="{{ route('rfqs.create') }}" class="px-5 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                    <span>+</span> Issue New RFQ
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700">✕</button>
            </div>
        @endif

        <!-- KPI Grid -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Total RFQs Issued</span>
                <div class="text-2xl font-black text-[#0c365b] mt-1">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Open for Bidding</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['open']) }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Awarded / Accepted</span>
                <div class="text-2xl font-black text-purple-700 mt-1">{{ number_format($stats['accepted']) }}</div>
            </div>
        </div>

        <!-- RFQs Table -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
            @if($rfqs->isEmpty())
                <div class="py-16 text-center">
                    <span class="text-4xl block mb-2">📄</span>
                    <h3 class="text-sm font-bold text-stone-800">No Request for Quotations issued</h3>
                    <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                        Issue RFQs to invite certified farmers, input suppliers, and FPOs to bid competitive rates.
                    </p>
                    <a href="{{ route('rfqs.create') }}" class="inline-block mt-4 px-5 py-2.5 rounded-xl bg-[#00875a] text-white text-xs font-bold hover:bg-[#00704a]">
                        + Issue First RFQ
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3.5 px-4">RFQ Number & Title</th>
                                <th class="py-3.5 px-4">Commodity / Item Type</th>
                                <th class="py-3.5 px-4">Required Volume</th>
                                <th class="py-3.5 px-4">Delivery By</th>
                                <th class="py-3.5 px-4">Quotes Received</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-800">
                            @foreach($rfqs as $rfq)
                                <tr class="hover:bg-stone-50/60 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#0c365b]">{{ $rfq->title }}</div>
                                        <div class="text-[11px] text-stone-500 font-mono">
                                            #{{ $rfq->rfq_number }} • Destination: {{ $rfq->delivery_district }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-700 capitalize">
                                            {{ str_replace('_', ' ', $rfq->item_type) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-black text-stone-900">
                                        {{ number_format((float)$rfq->quantity, 1) }} {{ $rfq->unit }}
                                    </td>
                                    <td class="py-3.5 px-4 text-stone-700 font-medium">
                                        {{ $rfq->target_delivery_date ? $rfq->target_delivery_date->format('M d, Y') : 'Open' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $rfq->quotations->count() > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-600' }}">
                                            {{ $rfq->quotations->count() }} Quotes
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $rfq->getStatusBadgeClass() }}">
                                            {{ ucfirst(str_replace('_', ' ', $rfq->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-2">
                                        <a href="{{ route('rfqs.show', $rfq->id) }}" class="font-bold text-stone-700 hover:text-stone-900">
                                            Details
                                        </a>
                                        @if($rfq->quotations->count() > 0)
                                            <a href="{{ route('rfqs.compare', $rfq->id) }}" class="font-bold text-emerald-700 hover:text-emerald-900">
                                                Compare ({{ $rfq->quotations->count() }}) →
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-stone-200">
                    {{ $rfqs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
