@extends('layouts.app')

@section('title', 'Admin Gate Operations Oversight — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header & Sub-nav --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('admin.index') }}" class="hover:text-emerald-400 transition">&larr; Admin Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-300">Cross-Organization Oversight</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">FPO Gate Operations Oversight</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        Platform Administration
                    </span>
                </div>
                <p class="text-slate-400 text-sm mt-1">
                    Multi-FPO gate entry surveillance, weighbridge discrepancies, lab testing queues, and audit log telemetry.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    &larr; Admin Main
                </a>
            </div>
        </div>

        {{-- Cross-Org Operational KPI Metrics --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Active FPOs</span>
                <span class="text-2xl font-extrabold text-white mt-1 block">{{ number_format($stats['total_fpos']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Cooperative centers</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-emerald-400 font-bold block">Total Gate Entries</span>
                <span class="text-2xl font-extrabold text-emerald-300 mt-1 block">{{ number_format($stats['total_gate_entries']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $stats['entries_today'] }} registered today</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-rose-400 font-bold block">Open Discrepancies</span>
                <span class="text-2xl font-extrabold text-rose-300 mt-1 block">{{ number_format($stats['open_weight_discrepancies']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Weighbridge variance alerts</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-yellow-400 font-bold block">Pending Approvals</span>
                <span class="text-2xl font-extrabold text-yellow-300 mt-1 block">{{ number_format($stats['pending_approvals']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $stats['pending_samples'] }} awaiting testing</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-sky-400 font-bold block">Posted Postings</span>
                <span class="text-2xl font-extrabold text-sky-300 mt-1 block">{{ number_format($stats['posted_inventory_count']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $stats['reversed_postings_count'] }} reversed corrections</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-purple-400 font-bold block">Open Exceptions</span>
                <span class="text-2xl font-extrabold text-purple-300 mt-1 block">{{ number_format($stats['total_open_exceptions']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $stats['rejected_deliveries'] }} rejected lots</span>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
            <form method="GET" action="{{ route('admin.gate-operations.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Entry #, Vehicle #, Delivering Party..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <select name="fpo_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                        <option value="">All FPO Organizations</option>
                        @foreach($fpos as $fpo)
                            <option value="{{ $fpo->id }}" {{ request('fpo_id') == $fpo->id ? 'selected' : '' }}>
                                {{ $fpo->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-3 rounded-xl text-xs transition">
                        Filter Oversight
                    </button>
                    @if(request()->hasAny(['search', 'fpo_id', 'status']))
                        <a href="{{ route('admin.gate-operations.index') }}" class="bg-slate-700 hover:bg-slate-600 text-slate-300 py-2 px-3 rounded-xl text-xs transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Multi-Org Gate Entries Table --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-700/70 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>Cross-FPO Gate Delivery Feed</span>
                    <span class="text-xs font-normal text-slate-400">({{ $entries->total() }} total entries)</span>
                </h2>
                <span class="text-xs text-slate-400">Platform administrator oversight</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-700/70">
                        <tr>
                            <th class="py-3 px-4">Entry Ref</th>
                            <th class="py-3 px-4">FPO Organization</th>
                            <th class="py-3 px-4">Date / Time</th>
                            <th class="py-3 px-4">Delivering Party</th>
                            <th class="py-3 px-4">Vehicle #</th>
                            <th class="py-3 px-4">Produce</th>
                            <th class="py-3 px-4">Declared Weight</th>
                            <th class="py-3 px-4">Net Measured</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-3 px-4 font-mono font-bold text-emerald-400">
                                    {{ $entry->entry_number }}
                                </td>
                                <td class="py-3 px-4 text-slate-200">
                                    <div class="font-semibold">{{ $entry->fpoOrganization->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $entry->gate_point }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-300">
                                    {{ $entry->entry_date_time->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-slate-300">
                                    {{ $entry->delivering_party_name }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-300">
                                    {{ $entry->vehicle_number ?? 'Manual' }}
                                </td>
                                <td class="py-3 px-4 text-slate-200 font-semibold">
                                    {{ $entry->crop_name }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-300">
                                    {{ number_format((float)$entry->declared_quantity, 1) }} {{ $entry->declared_unit }}
                                </td>
                                <td class="py-3 px-4 font-mono text-sky-300 font-bold">
                                    {{ $entry->acceptedWeightment ? number_format((float)$entry->acceptedWeightment->net_weight, 1).' '.$entry->acceptedWeightment->unit : 'Pending' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $entry->getStatusBadgeClass() }}">
                                        {{ $entry->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right space-x-1.5">
                                    <a href="{{ route('fpo.gate-entries.show', $entry->id) }}" class="px-2.5 py-1 rounded bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-[11px]">
                                        Inspect
                                    </a>
                                    <a href="{{ route('fpo.reports.show', $entry->id) }}" class="px-2.5 py-1 rounded bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/30 font-semibold text-[11px]">
                                        Report
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400 text-xs">
                                    No gate entries registered in the platform yet.
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

        {{-- Recent Audit Logs Stream --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 space-y-4 shadow-xl">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>🛡️ Recent Cross-Organization Operational Audit Activity</span>
                <span class="text-xs font-normal text-slate-400">(Latest 10 actions)</span>
            </h3>

            <div class="space-y-2">
                @foreach($recentAuditLogs as $log)
                    <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-700/40 text-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-emerald-400">{{ $log->fpoOrganization->name }}:</span>
                                <span class="capitalize font-bold text-white">{{ str_replace('_', ' ', $log->action) }}</span>
                                <span class="text-slate-500 font-mono text-[10px]">[{{ $log->entity_type }} #{{ $log->entity_id }}]</span>
                            </div>
                            <div class="text-[11px] text-slate-300 mt-0.5">{{ $log->reason }}</div>
                        </div>
                        <div class="text-right text-[11px] text-slate-400 whitespace-nowrap">
                            <div>{{ $log->actor_name }} ({{ $log->actor_role }})</div>
                            <div class="text-[10px] text-slate-500">{{ $log->created_at->format('d M Y, H:i:s') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
