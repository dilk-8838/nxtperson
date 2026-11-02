@extends('layouts.app')

@section('title', 'Bulk Procurement Requirements — NxtPerson Agritech')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-[#0c365b]">
                    🛒 Buyer Procurement Portal
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight mt-1">
                    Bulk Procurement Requirements
                </h1>
                <p class="text-xs sm:text-sm text-stone-600 mt-0.5">
                    Define volume requirements, target quality parameters, and invite farmers, FPOs, and suppliers to quote.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('buyer.dashboard') }}" class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                    ← Buyer Dashboard
                </a>
                <a href="{{ route('buyer.requirements.create') }}" class="px-5 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                    <span>+</span> New Requirement
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
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Total Requirements</span>
                <div class="text-2xl font-black text-[#0c365b] mt-1">{{ number_format($stats['total']) }}</div>
                <span class="text-[11px] text-stone-500">Persisted demands</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Open Requirements</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['open']) }}</div>
                <span class="text-[11px] text-emerald-600">Awaiting RFQ generation</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">RFQs In Market</span>
                <div class="text-2xl font-black text-blue-700 mt-1">{{ number_format($stats['rfq_created']) }}</div>
                <span class="text-[11px] text-blue-600">Active quotation tenders</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Fulfilled</span>
                <div class="text-2xl font-black text-purple-700 mt-1">{{ number_format($stats['fulfilled']) }}</div>
                <span class="text-[11px] text-purple-600">Awarded & procured</span>
            </div>
        </div>

        <!-- Requirements List -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
            @if($requirements->isEmpty())
                <div class="py-16 text-center">
                    <span class="text-4xl block mb-2">📋</span>
                    <h3 class="text-sm font-bold text-stone-800">No procurement requirements created</h3>
                    <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                        Create structured bulk procurement requirements to solicit quotations from qualified farmers, suppliers, and FPOs.
                    </p>
                    <a href="{{ route('buyer.requirements.create') }}" class="inline-block mt-4 px-5 py-2.5 rounded-xl bg-[#00875a] text-white text-xs font-bold hover:bg-[#00704a]">
                        + Create First Requirement
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3.5 px-4">Requirement</th>
                                <th class="py-3.5 px-4">Type</th>
                                <th class="py-3.5 px-4">Target Volume</th>
                                <th class="py-3.5 px-4">Target Price</th>
                                <th class="py-3.5 px-4">Delivery District</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-800">
                            @foreach($requirements as $req)
                                <tr class="hover:bg-stone-50/60 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-[#0c365b]">{{ $req->title }}</div>
                                        <div class="text-[11px] text-stone-500 font-mono">
                                            #{{ $req->requirement_number }} • {{ $req->item_name }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $req->type === 'produce' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $req->type === 'produce' ? '🌾 Farm Produce' : '🏢 Agri Input' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-black text-stone-900">
                                        {{ number_format((float)$req->target_quantity, 1) }} {{ $req->unit }}
                                    </td>
                                    <td class="py-3 px-4 text-stone-700">
                                        @if($req->target_price_per_unit)
                                            ₹{{ number_format((float)$req->target_price_per_unit, 2) }} / {{ $req->unit }}
                                        @else
                                            <span class="text-stone-400">Negotiable</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-stone-700">
                                        {{ $req->delivery_district }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $req->getStatusBadgeClass() }}">
                                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right space-x-2">
                                        <a href="{{ route('buyer.requirements.show', $req->id) }}" class="font-bold text-emerald-700 hover:text-emerald-900">
                                            View Details
                                        </a>
                                        @if($req->status === 'open')
                                            <a href="{{ route('rfqs.create', ['requirement_id' => $req->id]) }}" class="font-bold text-[#0c365b] hover:underline">
                                                Create RFQ →
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-stone-200">
                    {{ $requirements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
