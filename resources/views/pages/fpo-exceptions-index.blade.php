@extends('layouts.app')

@section('title', 'Operational Exceptions & Audit Register — FPO Operations — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header & Sub-nav --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('fpo.gate-entries.index') }}" class="hover:text-emerald-400 transition">&larr; Gate Operations</a>
                    <span>/</span>
                    <span class="text-slate-300">Exception Management</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Operational Exceptions Register</h1>
                    @if($organization)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            {{ $organization->name }}
                        </span>
                    @endif
                </div>
                <p class="text-slate-400 text-sm mt-1">
                    Traceable resolution workflow for weight variances, quality inspection failures, and stock reconciliation alerts.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('fpo.gate-entries.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    &larr; Gate Entries
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-900/40 border border-emerald-700 text-emerald-200 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Metrics Overview --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Total Exceptions</span>
                <span class="text-2xl font-extrabold text-white mt-1 block">{{ number_format($stats['total']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Logged operational events</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-rose-400 font-bold block">Open Exceptions</span>
                <span class="text-2xl font-extrabold text-rose-300 mt-1 block">{{ number_format($stats['open']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Awaiting documented review</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-rose-500 font-bold block">Critical Severity</span>
                <span class="text-2xl font-extrabold text-rose-400 mt-1 block">{{ number_format($stats['critical']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Requires manager sign-off</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-emerald-400 font-bold block">Resolved & Closed</span>
                <span class="text-2xl font-extrabold text-emerald-300 mt-1 block">{{ number_format($stats['resolved']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Resolved with audit reason</span>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
            <form method="GET" action="{{ route('fpo.exceptions.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Exception Number, Title, Description..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <select name="severity" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                        <option value="">All Severities</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-3 rounded-xl text-xs transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'severity', 'status', 'type']))
                        <a href="{{ route('fpo.exceptions.index') }}" class="bg-slate-700 hover:bg-slate-600 text-slate-300 py-2 px-3 rounded-xl text-xs transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Exceptions List --}}
        <div class="space-y-4">
            @forelse($exceptions as $exc)
                <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 shadow-xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-700/70 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="font-mono font-bold text-rose-400 text-sm">#{{ $exc->exception_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $exc->getSeverityBadgeClass() }}">
                                {{ strtoupper($exc->severity) }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $exc->getStatusBadgeClass() }}">
                                {{ ucfirst($exc->status) }}
                            </span>
                            <span class="text-xs text-slate-400 font-medium">Type: {{ str_replace('_', ' ', $exc->exception_type) }}</span>
                        </div>
                        <div class="text-xs text-slate-400">
                            Logged on {{ $exc->created_at->format('d M Y, h:i A') }} by {{ $exc->creator->name ?? 'System' }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-white">{{ $exc->title }}</h3>
                        <p class="text-xs text-slate-300">{{ $exc->description }}</p>
                    </div>

                    @if($exc->gateEntry)
                        <div class="text-xs text-slate-400 flex items-center gap-2">
                            <span>Linked Gate Entry:</span>
                            <a href="{{ route('fpo.gate-entries.show', $exc->gateEntry->id) }}" class="text-emerald-400 hover:underline font-mono font-bold">
                                #{{ $exc->gateEntry->entry_number }} ({{ $exc->gateEntry->crop_name }})
                            </a>
                        </div>
                    @endif

                    {{-- Resolution Details if Resolved --}}
                    @if($exc->status === 'resolved')
                        <div class="p-3.5 rounded-xl bg-emerald-950/40 border border-emerald-700/60 text-xs text-emerald-200 space-y-1">
                            <div class="font-bold flex items-center justify-between">
                                <span>✓ Resolved by {{ $exc->resolver->name ?? 'Authorized Officer' }} on {{ $exc->resolved_at?->format('d M Y, h:i A') }}</span>
                            </div>
                            <div><strong>Action Taken:</strong> {{ $exc->resolution_action }}</div>
                            <div><strong>Resolution Reason:</strong> {{ $exc->resolution_reason }}</div>
                        </div>
                    @else
                        {{-- Resolution Form if Open --}}
                        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700/60 space-y-3">
                            <h4 class="text-xs uppercase font-bold text-slate-300 tracking-wider">Execute Documented Resolution</h4>
                            <form method="POST" action="{{ route('fpo.exceptions.resolve', $exc->id) }}" class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Action Taken <span class="text-rose-400">*</span></label>
                                        <input type="text" name="resolution_action" required placeholder="e.g. Approved tolerance variance after supervisor re-inspection..."
                                            class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:border-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Resolution Reason & Justification <span class="text-rose-400">*</span></label>
                                        <input type="text" name="resolution_reason" required placeholder="e.g. Moisture shrinkage within seasonal allowance..."
                                            class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:border-emerald-500">
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 transition shadow">
                                        Sign & Resolve Exception
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-12 text-center text-slate-400 text-xs">
                    <div class="text-3xl mb-2">🛡️</div>
                    <p class="font-semibold text-slate-300">No operational exceptions found.</p>
                    <p class="text-slate-500 mt-1">All gate entries, weightments, and dispatches are within standard operating thresholds.</p>
                </div>
            @endforelse

            @if($exceptions->hasPages())
                <div class="p-4 border-t border-slate-700/70">
                    {{ $exceptions->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
