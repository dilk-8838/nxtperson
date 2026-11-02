@extends('layouts.app')

@section('title', 'Gate Exits & Outgoing Dispatch Register — FPO Operations — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header & Sub-nav --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('fpo.gate-entries.index') }}" class="hover:text-emerald-400 transition">&larr; Gate Operations</a>
                    <span>/</span>
                    <span class="text-slate-300">Outgoing Movement Register</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Gate Exits & Dispatches</h1>
                    @if($organization)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            {{ $organization->name }}
                        </span>
                    @endif
                </div>
                <p class="text-slate-400 text-sm mt-1">
                    Commercial order dispatch authorization, return movements, transfer passes, and verified buyer handovers.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('fpo.gate-entries.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    &larr; Gate Entries
                </a>
                <a href="{{ route('fpo.gate-exits.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-600 text-white hover:bg-amber-500 shadow-lg shadow-amber-900/30 transition">
                    + Authorize Gate Exit &rarr;
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

        {{-- Metrics Overview --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Total Passes</span>
                <span class="text-2xl font-extrabold text-white mt-1 block">{{ number_format($stats['total_exits']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Exit movements logged</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-amber-400 font-bold block">Authorized Pending Exit</span>
                <span class="text-2xl font-extrabold text-amber-300 mt-1 block">{{ number_format($stats['authorized']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Awaiting physical gate pass</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-emerald-400 font-bold block">Exited Premises</span>
                <span class="text-2xl font-extrabold text-emerald-300 mt-1 block">{{ number_format($stats['exited']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Cleared and in-transit</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-sky-400 font-bold block">Volume Dispatched</span>
                <span class="text-2xl font-extrabold text-sky-300 mt-1 block">{{ number_format((float)$stats['dispatched_volume'], 1) }} kg</span>
                <span class="text-xs text-slate-400 mt-1 block">Fulfilled offtake volume</span>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
            <form method="GET" action="{{ route('fpo.gate-exits.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Exit Number, Vehicle Number, Driver..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <select name="movement_type" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                        <option value="">All Movement Types</option>
                        <option value="order_dispatch" {{ request('movement_type') === 'order_dispatch' ? 'selected' : '' }}>Buyer Order Dispatch</option>
                        <option value="transfer" {{ request('movement_type') === 'transfer' ? 'selected' : '' }}>Center Transfer</option>
                        <option value="return_rejected" {{ request('movement_type') === 'return_rejected' ? 'selected' : '' }}>Return of Rejected Produce</option>
                        <option value="empty_exit" {{ request('movement_type') === 'empty_exit' ? 'selected' : '' }}>Empty Vehicle Exit</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-3 rounded-xl text-xs transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'movement_type', 'status']))
                        <a href="{{ route('fpo.gate-exits.index') }}" class="bg-slate-700 hover:bg-slate-600 text-slate-300 py-2 px-3 rounded-xl text-xs transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Exits Table --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-700/70">
                        <tr>
                            <th class="py-3.5 px-4">Exit Pass #</th>
                            <th class="py-3.5 px-4">Date & Time</th>
                            <th class="py-3.5 px-4">Movement Type</th>
                            <th class="py-3.5 px-4">Vehicle & Driver</th>
                            <th class="py-3.5 px-4">Dispatched Volume</th>
                            <th class="py-3.5 px-4">Linked Reference</th>
                            <th class="py-3.5 px-4">Authorizer</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($exits as $exit)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-amber-400">{{ $exit->exit_number }}</td>
                                <td class="py-3.5 px-4 text-slate-300">
                                    <div>{{ $exit->exit_date_time->format('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $exit->exit_date_time->format('H:i A') }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-900 text-slate-300 border border-slate-700">
                                        {{ str_replace('_', ' ', $exit->movement_type) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-200">
                                    <div class="font-mono font-bold">{{ $exit->vehicle_number ?? 'Manual' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $exit->driver_name ?? 'Delivering Party' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-white">
                                    {{ number_format((float)$exit->dispatched_quantity, 1) }} {{ $exit->unit }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-300">
                                    @if($exit->purchaseOrder)
                                        <a href="{{ route('orders.show', $exit->purchaseOrder->id) }}" class="text-sky-400 hover:underline font-mono">
                                            #{{ $exit->purchaseOrder->po_number }}
                                        </a>
                                    @elseif($exit->gateEntry)
                                        <a href="{{ route('fpo.gate-entries.show', $exit->gateEntry->id) }}" class="text-emerald-400 hover:underline font-mono">
                                            #{{ $exit->gateEntry->entry_number }}
                                        </a>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-300">{{ $exit->authorizer_name }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $exit->getStatusBadgeClass() }}">
                                        {{ ucfirst($exit->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right space-y-1">
                                    @if($exit->status === \App\Models\FpoGateExit::STATUS_AUTHORIZED)
                                        <form method="POST" action="{{ route('fpo.gate-exits.confirm-exit', $exit->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500">
                                                Confirm Physical Exit &rarr;
                                            </button>
                                        </form>
                                    @elseif($exit->status === \App\Models\FpoGateExit::STATUS_EXITED && ! $exit->handover_confirmed_at && $exit->movement_type === 'order_dispatch')
                                        <form method="POST" action="{{ route('fpo.gate-exits.confirm-handover', $exit->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-700 text-white hover:bg-sky-600">
                                                Confirm Delivery Handover &rarr;
                                            </button>
                                        </form>
                                    @elseif($exit->handover_confirmed_at)
                                        <span class="text-[10px] text-emerald-400 font-semibold block">✓ Handover Verified</span>
                                    @else
                                        <span class="text-[10px] text-slate-500">Completed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400 text-xs">
                                    No gate exit passes recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($exits->hasPages())
                <div class="p-4 border-t border-slate-700/70">
                    {{ $exits->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
