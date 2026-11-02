@extends('layouts.app')

@section('title', 'Tenders & RFQs Inbox — NxtPerson Agritech')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-[#00875a]">
                    📥 Seller RFQ Inbox
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight mt-1">
                    Procurement Tenders & Quotation Requests
                </h1>
                <p class="text-xs sm:text-sm text-stone-600 mt-0.5">
                    Commercial buyers are seeking agricultural commodities and inputs. Submit your quotation to win direct purchase orders.
                </p>
            </div>
            <div>
                <a href="{{ route('home') }}" class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                    ← Back to Portal
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700">✕</button>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
            @if($rfqs->isEmpty())
                <div class="py-16 text-center">
                    <span class="text-4xl block mb-2">📬</span>
                    <h3 class="text-sm font-bold text-stone-800">No active procurement tenders</h3>
                    <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                        When buyers publish procurement requirements or specifically invite your organization, they will appear here.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3.5 px-4">Tender Details</th>
                                <th class="py-3.5 px-4">Buyer Entity</th>
                                <th class="py-3.5 px-4">Requested Volume</th>
                                <th class="py-3.5 px-4">Target Destination</th>
                                <th class="py-3.5 px-4">Required Fulfilment</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-800">
                            @foreach($rfqs as $rfq)
                                @php
                                    $hasQuoted = $rfq->quotations->where('seller_user_id', $user->id)->first();
                                @endphp
                                <tr class="hover:bg-stone-50/60 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#0c365b]">{{ $rfq->title }}</div>
                                        <div class="text-[11px] text-stone-500 font-mono">
                                            #{{ $rfq->rfq_number }} • Published {{ $rfq->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-stone-800">
                                        {{ $rfq->buyer->name ?? 'Commercial Buyer' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-black text-stone-900">
                                        {{ number_format((float)$rfq->quantity, 1) }} {{ $rfq->unit }}
                                    </td>
                                    <td class="py-3.5 px-4 text-stone-700">
                                        {{ $rfq->delivery_district }}
                                    </td>
                                    <td class="py-3.5 px-4 text-stone-700 font-medium">
                                        {{ $rfq->target_delivery_date ? $rfq->target_delivery_date->format('M d, Y') : 'Open' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($hasQuoted)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                Quoted: {{ $hasQuoted->status }}
                                            </span>
                                        @elseif($rfq->isOpen())
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Open for Bidding
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-600">
                                                {{ ucfirst($rfq->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if($hasQuoted)
                                            <a href="{{ route('quotations.show', $hasQuoted->id) }}" class="font-bold text-blue-700 hover:text-blue-900">
                                                View My Quote →
                                            </a>
                                        @elseif($rfq->isOpen())
                                            <a href="{{ route('quotations.create', $rfq->id) }}" class="px-3.5 py-1.5 rounded-xl bg-[#00875a] text-white font-bold text-xs hover:bg-[#00704a] shadow-xs inline-block">
                                                Quote Now →
                                            </a>
                                        @else
                                            <a href="{{ route('rfqs.show', $rfq->id) }}" class="font-bold text-stone-600 hover:text-stone-900">
                                                View
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
