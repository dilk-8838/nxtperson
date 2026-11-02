@extends('layouts.app')

@section('title', "Procurement Requirement #{$requirement->requirement_number} — NxtPerson Agritech")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-4xl">
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('buyer.requirements.index') }}" class="hover:text-emerald-800">Requirements</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">{{ $requirement->requirement_number }}</span>
        </nav>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-stone-200 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-200">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $requirement->type === 'produce' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $requirement->type === 'produce' ? '🌾 Farm Produce' : '🏢 Agricultural Input' }}
                        </span>
                        <span class="text-xs font-mono text-stone-500">Ref: {{ $requirement->requirement_number }}</span>
                    </div>
                    <h1 class="text-2xl font-black text-[#0c365b] mt-1">
                        {{ $requirement->title }}
                    </h1>
                    <p class="text-xs text-stone-500 mt-0.5">
                        Commodity: <strong>{{ $requirement->item_name }}</strong> • Created {{ $requirement->created_at->format('M d, Y') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $requirement->getStatusBadgeClass() }}">
                        {{ ucfirst(str_replace('_', ' ', $requirement->status)) }}
                    </span>
                    @if($requirement->status === 'open')
                        <a href="{{ route('rfqs.create', ['requirement_id' => $requirement->id]) }}" class="px-5 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all">
                            Tender RFQ to Sellers →
                        </a>
                    @endif
                </div>
            </div>

            <!-- Specs Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-stone-100 text-xs">
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Target Volume</span>
                    <strong class="text-base font-black text-stone-900 mt-1 block">
                        {{ number_format((float)$requirement->target_quantity, 1) }} {{ $requirement->unit }}
                    </strong>
                </div>
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Target Price</span>
                    <strong class="text-base font-black text-stone-900 mt-1 block">
                        {{ $requirement->target_price_per_unit ? '₹'.number_format((float)$requirement->target_price_per_unit, 2).' / '.$requirement->unit : 'Negotiable' }}
                    </strong>
                </div>
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Delivery Location</span>
                    <strong class="text-base font-black text-stone-900 mt-1 block">
                        {{ $requirement->delivery_district }}
                    </strong>
                </div>
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Desired Delivery Date</span>
                    <strong class="text-base font-black text-stone-900 mt-1 block">
                        {{ $requirement->desired_delivery_date ? $requirement->desired_delivery_date->format('M d, Y') : 'Immediate' }}
                    </strong>
                </div>
            </div>

            <!-- Details -->
            <div class="pt-6 space-y-4 text-xs">
                @if($requirement->quality_specifications)
                    <div>
                        <strong class="text-stone-800 font-bold block mb-1">Quality Specifications:</strong>
                        <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 text-stone-700 leading-relaxed">
                            {{ $requirement->quality_specifications }}
                        </div>
                    </div>
                @endif

                @if($requirement->notes)
                    <div>
                        <strong class="text-stone-800 font-bold block mb-1">Commercial / Logistics Notes:</strong>
                        <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 text-stone-700 leading-relaxed">
                            {{ $requirement->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Linked RFQs -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200">
            <div class="flex items-center justify-between pb-4 border-b border-stone-200 mb-4">
                <h3 class="text-base font-bold text-[#0c365b]">
                    Tendered RFQs ({{ $requirement->rfqs->count() }})
                </h3>
                @if($requirement->status === 'open')
                    <a href="{{ route('rfqs.create', ['requirement_id' => $requirement->id]) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                        + Issue Another RFQ
                    </a>
                @endif
            </div>

            @if($requirement->rfqs->isEmpty())
                <div class="py-8 text-center text-xs text-stone-400">
                    No RFQs have been issued for this requirement yet. Click "Tender RFQ to Sellers" above to solicit quotations.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($requirement->rfqs as $rfq)
                        <div class="p-4 rounded-2xl border border-stone-200 text-xs flex items-center justify-between hover:bg-stone-50 transition-colors">
                            <div>
                                <div class="font-bold text-[#0c365b]">{{ $rfq->title }}</div>
                                <div class="text-[11px] text-stone-500 font-mono">
                                    #{{ $rfq->rfq_number }} • Tendered {{ $rfq->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $rfq->getStatusBadgeClass() }}">
                                    {{ ucfirst(str_replace('_', ' ', $rfq->status)) }}
                                </span>
                                <span class="text-stone-500">
                                    <strong>{{ $rfq->quotations->count() }}</strong> Quotes
                                </span>
                                <a href="{{ route('rfqs.show', $rfq->id) }}" class="font-bold text-emerald-700 hover:text-emerald-900">
                                    View RFQ →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
