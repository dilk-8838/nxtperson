@extends('layouts.app')

@section('title', 'FPO Gate Operations & Procurement Centre — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header & Sub-nav --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">FPO Gate Operations</h1>
                    @if($organization)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            {{ $organization->name }}
                        </span>
                    @endif
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        Procurement Centre
                    </span>
                </div>
                <p class="text-slate-400 text-sm mt-1">
                    Live gate registration, gross/tare weightment, quality testing lab, sign-off approvals & stock posting.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('fpo.dashboard') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    &larr; FPO Portal
                </a>
                <a href="{{ route('fpo.reconciliation') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-sky-300 hover:bg-slate-700 border border-slate-700 transition">
                    📊 Stock Reconciliation
                </a>
                <a href="{{ route('fpo.exceptions.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-rose-300 hover:bg-slate-700 border border-slate-700 transition">
                    ⚠️ Exceptions ({{ $stats['open_exceptions'] }})
                </a>
                <a href="{{ route('fpo.gate-exits.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-amber-300 hover:bg-slate-700 border border-slate-700 transition">
                    🚛 Gate Exits
                </a>
                <a href="{{ route('fpo.gate-entries.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition">
                    + Register Delivery &rarr;
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-900/40 border border-emerald-700 text-emerald-200 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 rounded-xl bg-blue-900/40 border border-blue-700 text-blue-200 text-sm">
                {{ session('info') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Persisted Operational Metric Counters --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Entries Today</span>
                <span class="text-2xl font-extrabold text-white mt-1 block">{{ number_format($stats['entries_today']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Total: {{ number_format($stats['total_entries']) }}</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-sky-400 font-bold block">Vehicles Inside</span>
                <span class="text-2xl font-extrabold text-sky-300 mt-1 block">{{ number_format($stats['vehicles_inside']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Active on premises</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-amber-400 font-bold block">Needs Weightment</span>
                <span class="text-2xl font-extrabold text-amber-300 mt-1 block">{{ number_format($stats['awaiting_weightment']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">At weighbridge</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-yellow-400 font-bold block">Needs Approval</span>
                <span class="text-2xl font-extrabold text-yellow-300 mt-1 block">{{ number_format($stats['awaiting_approval']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Lab tests complete</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-emerald-400 font-bold block">Accepted / Posted</span>
                <span class="text-2xl font-extrabold text-emerald-300 mt-1 block">{{ number_format($stats['accepted_deliveries']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Rejected: {{ number_format($stats['rejected_deliveries']) }}</span>
            </div>

            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-4">
                <span class="text-[11px] uppercase tracking-wider text-cyan-400 font-bold block">Awaiting Exit</span>
                <span class="text-2xl font-extrabold text-cyan-300 mt-1 block">{{ number_format($stats['awaiting_exit']) }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Pending gate pass</span>
            </div>
        </div>

        {{-- Search and Filter Controls --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
            <form method="GET" action="{{ route('fpo.gate-entries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Search Gate Entry / Vehicle / Farmer</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. GE-20261027, GJ-03-AB, Patel..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status</label>
                    <select name="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                        <option value="">All Operational States</option>
                        <option value="entered" {{ request('status') === 'entered' ? 'selected' : '' }}>Vehicle Entered</option>
                        <option value="weighed" {{ request('status') === 'weighed' ? 'selected' : '' }}>Weighed</option>
                        <option value="testing_in_progress" {{ request('status') === 'testing_in_progress' ? 'selected' : '' }}>Testing in Progress</option>
                        <option value="quality_pending_approval" {{ request('status') === 'quality_pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="inventory_posted" {{ request('status') === 'inventory_posted' ? 'selected' : '' }}>Inventory Posted</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="exit_authorized" {{ request('status') === 'exit_authorized' ? 'selected' : '' }}>Exit Authorized</option>
                        <option value="exited" {{ request('status') === 'exited' ? 'selected' : '' }}>Gate Exited</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Crop</label>
                    <select name="crop" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                        <option value="">All Crops</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->name }}" {{ request('crop') === $crop->name ? 'selected' : '' }}>{{ $crop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-3 rounded-xl text-xs transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'crop', 'date_from', 'date_to', 'delivering_party_type']))
                        <a href="{{ route('fpo.gate-entries.index') }}" class="bg-slate-700 hover:bg-slate-600 text-slate-300 py-2 px-3 rounded-xl text-xs transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Gate Entries Data Register Table --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-700/70 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>Gate Entries Register</span>
                    <span class="text-xs font-normal text-slate-400">({{ $entries->total() }} recorded deliveries)</span>
                </h2>
                <span class="text-xs text-slate-400">Sorted by newest first</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-700/70">
                        <tr>
                            <th class="py-3.5 px-4">Entry Reference</th>
                            <th class="py-3.5 px-4">Date & Time</th>
                            <th class="py-3.5 px-4">Delivering Party</th>
                            <th class="py-3.5 px-4">Vehicle / Mode</th>
                            <th class="py-3.5 px-4">Produce Details</th>
                            <th class="py-3.5 px-4">Declared Weight</th>
                            <th class="py-3.5 px-4">Measured Net</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-400">
                                    <a href="{{ route('fpo.gate-entries.show', $entry->id) }}" class="hover:underline">
                                        {{ $entry->entry_number }}
                                    </a>
                                    <span class="block text-[10px] font-sans text-slate-500">{{ $entry->gate_point }}</span>
                                </td>

                                <td class="py-3.5 px-4 text-slate-300">
                                    <div>{{ $entry->entry_date_time->format('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $entry->entry_date_time->format('H:i A') }}</div>
                                </td>

                                <td class="py-3.5 px-4 text-slate-200">
                                    <div class="font-semibold">{{ $entry->delivering_party_name }}</div>
                                    <div class="text-[10px] text-slate-400 capitalize">{{ str_replace('_', ' ', $entry->delivering_party_type) }}</div>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($entry->vehicle_number)
                                        <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 font-mono text-[11px] border border-slate-700">
                                            {{ $entry->vehicle_number }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Walk-in / Manual</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-slate-200 font-medium">
                                    <div>{{ $entry->crop_name }}</div>
                                    @if($entry->variety)
                                        <div class="text-[10px] text-slate-400">{{ $entry->variety }}</div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 font-mono text-slate-300">
                                    {{ number_format((float)$entry->declared_quantity, 1) }} {{ $entry->declared_unit }}
                                </td>

                                <td class="py-3.5 px-4 font-mono">
                                    @if($entry->acceptedWeightment)
                                        <span class="text-sky-300 font-semibold">{{ number_format((float)$entry->acceptedWeightment->net_weight, 1) }} {{ $entry->acceptedWeightment->unit }}</span>
                                        @if($entry->acceptedWeightment->discrepancy_flag)
                                            <span class="block text-[10px] text-rose-400 font-sans font-bold">⚠️ Discrepancy</span>
                                        @endif
                                    @else
                                        <span class="text-slate-500 italic">Pending</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $entry->getStatusBadgeClass() }}">
                                        {{ $entry->getStatusLabel() }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <a href="{{ route('fpo.gate-entries.show', $entry->id) }}"
                                        class="px-2.5 py-1 rounded-lg bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/30 border border-emerald-500/30 font-semibold transition">
                                        Manage &rarr;
                                    </a>
                                    <a href="{{ route('fpo.reports.show', $entry->id) }}"
                                        class="px-2.5 py-1 rounded-lg bg-slate-700 text-slate-300 hover:bg-slate-600 font-medium transition" title="Formal Report">
                                        📄 Report
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">🚛</div>
                                    <p class="font-semibold text-slate-300">No gate entry records found.</p>
                                    <p class="text-xs text-slate-500 mt-1">Register incoming farmer delivery or adjust filter criteria.</p>
                                    <a href="{{ route('fpo.gate-entries.create') }}" class="inline-block mt-4 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500">
                                        + Register First Delivery
                                    </a>
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
