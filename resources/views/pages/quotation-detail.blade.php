@extends('layouts.app')

@section('title', 'Quotation #' . $quotation->quotation_number . ' — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">

        {{-- Breadcrumb & Header --}}
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-400 mb-3">
                <a href="{{ route('rfqs.index') }}" class="hover:text-emerald-400 transition">RFQs</a>
                <span>&rarr;</span>
                <a href="{{ route('rfqs.show', $quotation->rfq_id) }}" class="hover:text-emerald-400 transition">RFQ #{{ $quotation->rfq->rfq_number }}</a>
                <span>&rarr;</span>
                <span class="text-slate-200">Quotation #{{ $quotation->quotation_number }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Quotation #{{ $quotation->quotation_number }}</h1>
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                'accepted' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                'rejected' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                'withdrawn' => 'bg-slate-600/20 text-slate-400 border-slate-600/30',
                                'expired' => 'bg-slate-700/30 text-slate-400 border-slate-600/30',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$quotation->status] ?? 'bg-slate-700 text-slate-300' }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                    <p class="text-slate-400 text-sm mt-1">
                        Quotation submitted for RFQ: <span class="text-white font-medium">{{ $quotation->rfq->title }}</span>
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 flex-wrap">
                    @if($quotation->purchaseOrder)
                        <a href="{{ route('orders.show', $quotation->purchaseOrder->id) }}" class="px-4 py-2 rounded-xl text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-md transition">
                            View Purchase Order &rarr;
                        </a>
                    @endif

                    @if($quotation->isAcceptable() && $user->isBuyer() && $quotation->rfq->buyer_id === $user->id)
                        <form action="{{ route('quotations.accept', $quotation->id) }}" method="POST" onsubmit="return confirm('Accept this quotation? This locks in stock and issues a Purchase Order.')">
                            @csrf
                            <button type="submit" class="px-5 py-2 rounded-xl text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition">
                                Accept Quotation & Issue PO &rarr;
                            </button>
                        </form>
                    @endif

                    @if($quotation->status === 'pending' && ($user->id === $quotation->seller_user_id || $user->isAdmin()))
                        <form action="{{ route('quotations.withdraw', $quotation->id) }}" method="POST" onsubmit="return confirm('Withdraw this quotation? You will no longer be considered for this RFQ.')">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-rose-900/40 text-rose-300 hover:bg-rose-900/70 border border-rose-700 transition">
                                Withdraw Offer
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('rfqs.show', $quotation->rfq_id) }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                        View RFQ
                    </a>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-900/40 border border-emerald-700 text-emerald-200 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 rounded-xl bg-sky-900/40 border border-sky-700 text-sky-200 text-sm">
                {{ session('info') }}
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

        {{-- Accepted PO Notification Banner --}}
        @if($quotation->purchaseOrder)
            <div class="p-5 rounded-2xl bg-emerald-950/60 border border-emerald-600/60 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎉</span>
                    <div>
                        <h3 class="font-bold text-white text-base">Quotation Accepted &amp; Purchase Order Active</h3>
                        <p class="text-xs text-emerald-300 mt-0.5">
                            Order <strong>#{{ $quotation->purchaseOrder->order_number }}</strong> was generated on {{ $quotation->purchaseOrder->created_at->format('d M Y, h:i A') }}.
                        </p>
                    </div>
                </div>
                <a href="{{ route('orders.show', $quotation->purchaseOrder->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 transition">
                    View PO #{{ $quotation->purchaseOrder->order_number }} &rarr;
                </a>
            </div>
        @endif

        {{-- Commercial Invoice-Style Breakdown --}}
        <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 overflow-hidden shadow-xl">
            <div class="px-6 py-4 bg-slate-850 border-b border-slate-700/70 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-white text-base">Commercial Proposal Summary</h2>
                    <span class="text-xs text-slate-400">Formal pricing schedule submitted by vendor</span>
                </div>
                <span class="text-xs text-slate-400">Date: {{ $quotation->created_at->format('d M Y') }}</span>
            </div>

            <div class="p-6 space-y-6">
                {{-- Vendor & Buyer Information Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm border-b border-slate-700/60 pb-6">
                    <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/50">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block mb-2">Seller / Vendor</span>
                        <div class="font-bold text-white text-base">{{ $quotation->getSellerDisplayName() }}</div>
                        <div class="text-xs text-slate-400 capitalize mt-1">Role: {{ $quotation->seller_type }}</div>
                        @if($quotation->sellerUser)
                            <div class="text-xs text-slate-400 mt-1">Contact: {{ $quotation->sellerUser->phone ?? '—' }} &bull; {{ $quotation->sellerUser->district ?? '' }}</div>
                        @endif
                    </div>

                    <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/50">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block mb-2">Buyer / Client</span>
                        <div class="font-bold text-white text-base">{{ $quotation->rfq->buyer->name }}</div>
                        <div class="text-xs text-slate-400 mt-1">Destination: {{ $quotation->rfq->delivery_district }}</div>
                        <div class="text-xs text-slate-400 mt-1">Target Date: {{ $quotation->rfq->target_delivery_date?->format('d M Y') ?? 'Flexible' }}</div>
                    </div>
                </div>

                {{-- Pricing Line Items Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-700 text-xs uppercase tracking-wider text-slate-400">
                                <th class="py-3 px-2">Line Item / Description</th>
                                <th class="py-3 px-4 text-center">Quoted Quantity</th>
                                <th class="py-3 px-4 text-right">Unit Price</th>
                                <th class="py-3 px-4 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-750">
                            <tr>
                                <td class="py-4 px-2">
                                    <div class="font-semibold text-white">{{ $quotation->rfq->title }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        @if($quotation->produceLot)
                                            Produce Lot #{{ $quotation->produceLot->lot_number }} (Grade: {{ $quotation->produceLot->approved_grade ?? 'Standard' }})
                                        @elseif($quotation->product)
                                            Product: {{ $quotation->product->name }} (Brand: {{ $quotation->product->brand_name ?? 'OEM' }})
                                        @elseif($quotation->collectiveListing)
                                            FPO Collective Pool #{{ $quotation->collectiveListing->listing_number }}
                                        @else
                                            General Agri Commodity Supply
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center font-medium text-slate-200">
                                    {{ number_format((float)$quotation->quoted_quantity, 1) }} {{ $quotation->unit }}
                                </td>
                                <td class="py-4 px-4 text-right font-medium text-white">
                                    ₹{{ number_format((float)$quotation->unit_price, 2) }}
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-white">
                                    ₹{{ number_format((float)($quotation->quoted_quantity * $quotation->unit_price), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Financial Calculation Subtotal & Total --}}
                <div class="flex justify-end pt-4 border-t border-slate-700/60">
                    <div class="w-full md:w-80 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal:</span>
                            <span class="text-white font-medium">₹{{ number_format((float)($quotation->quoted_quantity * $quotation->unit_price), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Freight &amp; Handling:</span>
                            <span class="text-white font-medium">₹{{ number_format((float)$quotation->additional_charges, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Taxes / GST:</span>
                            <span class="text-white font-medium">₹{{ number_format((float)$quotation->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-extrabold text-emerald-400 pt-2 border-t border-slate-700">
                            <span>Total Estimated Amount:</span>
                            <span class="text-xl">₹{{ number_format((float)$quotation->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Terms & Notes --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-700/60 text-sm">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Delivery &amp; Validity Schedule</h4>
                        <div class="space-y-1.5 text-slate-300">
                            <div><span class="text-slate-400">Estimated Delivery:</span> <strong class="text-white">{{ $quotation->estimated_delivery_date?->format('d M Y') ?? '—' }}</strong></div>
                            <div>
                                <span class="text-slate-400">Offer Valid Until:</span>
                                <strong class="{{ $quotation->valid_until && $quotation->valid_until->isPast() ? 'text-rose-400' : 'text-white' }}">
                                    {{ $quotation->valid_until?->format('d M Y') ?? '—' }}
                                    @if($quotation->valid_until && $quotation->valid_until->isPast())
                                        (Expired)
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Quality &amp; Fulfilment Terms</h4>
                        <p class="text-slate-300 italic text-xs">
                            {{ $quotation->quality_notes ?: 'Standard quality compliance as per APMC guidelines.' }}
                        </p>
                        @if($quotation->terms_and_conditions)
                            <div class="mt-2 text-xs text-slate-400">
                                <strong>Terms:</strong> {{ $quotation->terms_and_conditions }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
