@extends('layouts.app')

@section('title', 'Stock Reconciliation & Audit Ledger — FPO Operations — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header & Sub-nav --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('fpo.gate-entries.index') }}" class="hover:text-emerald-400 transition">&larr; Gate Operations</a>
                    <span>/</span>
                    <span class="text-slate-300">Inventory Reconciliation</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Stock Reconciliation & Inventory Ledger</h1>
                    @if($org)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            {{ $org->name }}
                        </span>
                    @endif
                </div>
                <p class="text-slate-400 text-sm mt-1">
                    Comprehensive cross-verification: declared farmer volumes &bull; weighbridge net readings &bull; lab approvals &bull; posted stock.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('fpo.gate-entries.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    &larr; Gate Entries
                </a>
            </div>
        </div>

        {{-- Reconciliation Table --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-700/70 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>Delivery Lot Stock Reconciliation Matrix</span>
                </h2>
                <span class="text-xs text-slate-400">All figures calculated in Kilograms (kg)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-700/70">
                        <tr>
                            <th class="py-3 px-4">Gate Entry #</th>
                            <th class="py-3 px-4">Produce</th>
                            <th class="py-3 px-4">Declared Weight</th>
                            <th class="py-3 px-4">Gross Weight</th>
                            <th class="py-3 px-4">Tare Weight</th>
                            <th class="py-3 px-4">Measured Net</th>
                            <th class="py-3 px-4">Accepted Qty</th>
                            <th class="py-3 px-4">Rejected Qty</th>
                            <th class="py-3 px-4">Posted to Stock</th>
                            <th class="py-3 px-4">Source Lot Avail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($entries as $entry)
                            @php
                                $wm = $entry->acceptedWeightment;
                                $appr = $entry->latestApproval;
                                $postingsSum = $entry->inventoryPostings->where('status', 'posted')->sum('posted_quantity_kg');
                            @endphp
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-400">
                                    <a href="{{ route('fpo.gate-entries.show', $entry->id) }}" class="hover:underline">
                                        {{ $entry->entry_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-slate-200">
                                    <div class="font-semibold">{{ $entry->crop_name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $entry->delivering_party_name }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-300">
                                    {{ number_format((float)$entry->declared_quantity, 1) }} {{ $entry->declared_unit }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-400">
                                    {{ $wm ? number_format((float)$wm->gross_weight, 1).' '.$wm->unit : '—' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-400">
                                    {{ $wm ? number_format((float)$wm->tare_weight, 1).' '.$wm->unit : '—' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-sky-300">
                                    {{ $wm ? number_format((float)$wm->net_weight, 1).' '.$wm->unit : '—' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-400">
                                    {{ $appr ? number_format((float)$appr->accepted_quantity, 1).' kg' : '—' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-rose-400">
                                    {{ $appr ? number_format((float)$appr->rejected_quantity, 1).' kg' : '—' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-white">
                                    @if($postingsSum > 0)
                                        <span class="text-emerald-300">{{ number_format((float)$postingsSum, 1) }} kg</span>
                                    @else
                                        <span class="text-slate-500 italic">0.0 kg</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-300">
                                    @if($entry->sourceProduceLot)
                                        {{ number_format((float)$entry->sourceProduceLot->available_quantity_kg, 1) }} kg
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400 text-xs">
                                    No delivery entries found to reconcile.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($entries->hasPages())
                <div class="p-4 border-t border-slate-700/70">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
