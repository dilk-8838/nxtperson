@extends('layouts.app')

@section('title', 'Admin — Supplier Organizations & Verification Queue')

@section('content')
<div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('admin.index') }}" class="hover:text-[#0c365b] font-medium">Admin Portal</a>
                    <span>/</span>
                    <span class="text-slate-800 font-bold">Supplier Organizations</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight">
                    Supplier Verification & Organizations
                </h1>
            </div>
            <a href="{{ route('admin.index') }}" class="text-xs font-bold text-[#0c365b] hover:underline self-start sm:self-auto">
                ← Return to Admin Overview
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Status Filter Tabs -->
        <div class="bg-white rounded-2xl p-2 border border-slate-200/90 shadow-xs flex flex-wrap gap-2">
            <a href="{{ route('admin.suppliers') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-[#0c365b] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                All Suppliers ({{ $statusCounts['all'] ?? 0 }})
            </a>
            <a href="{{ route('admin.suppliers', ['status' => 'pending']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'text-amber-700 bg-amber-50 hover:bg-amber-100' }}">
                <span>⏳ Pending Review</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-200 text-amber-900' }}">
                    {{ $statusCounts['pending'] ?? 0 }}
                </span>
            </a>
            <a href="{{ route('admin.suppliers', ['status' => 'approved']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                Approved ({{ $statusCounts['approved'] ?? 0 }})
            </a>
            <a href="{{ route('admin.suppliers', ['status' => 'rejected']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                Rejected ({{ $statusCounts['rejected'] ?? 0 }})
            </a>
            <a href="{{ route('admin.suppliers', ['status' => 'suspended']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'suspended' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                Suspended ({{ $statusCounts['suspended'] ?? 0 }})
            </a>
        </div>

        <!-- Supplier Directory Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form action="{{ route('admin.suppliers') }}" method="GET" class="w-full sm:w-80">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, license, representative..." 
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#0c365b] outline-none">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
                    </div>
                </form>
                <div class="text-xs text-slate-500 font-semibold self-end sm:self-auto">
                    Displaying {{ $suppliers->total() }} supplier records
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-3 px-4">Organization Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Representative / Phone</th>
                            <th class="py-3 px-4">District</th>
                            <th class="py-3 px-4">License / GST</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Submitted</th>
                            <th class="py-3 px-4 text-right">Review Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($suppliers as $supplier)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <div class="text-sm text-[#0c365b]">{{ $supplier->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-normal line-clamp-1">{{ $supplier->operating_address }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold capitalize text-slate-700">
                                    {{ str_replace('_', ' ', $supplier->category) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <div class="font-bold">{{ $supplier->authorized_representative_name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $supplier->contact_phone }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $supplier->district }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-700">
                                    <div>{{ $supplier->license_number ?? '—' }}</div>
                                    <div class="text-slate-400">{{ $supplier->gst_number ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($supplier->verification_status === 'approved')
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Approved</span>
                                    @elseif($supplier->verification_status === 'pending')
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px] animate-pulse">Pending Review</span>
                                    @elseif($supplier->verification_status === 'rejected')
                                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 font-bold text-[10px]">Rejected</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px] capitalize">{{ $supplier->verification_status }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                    {{ $supplier->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($supplier->verification_status !== 'approved')
                                            <form action="{{ route('admin.suppliers.review', $supplier->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] cursor-pointer">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if($supplier->verification_status === 'pending')
                                            <form action="{{ route('admin.suppliers.review', $supplier->id) }}" method="POST" onsubmit="return confirm('Reject this supplier application?');">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 font-bold text-[11px] cursor-pointer">
                                                    Reject
                                                </button>
                                            </form>
                                        @elseif($supplier->verification_status === 'approved')
                                            <form action="{{ route('admin.suppliers.review', $supplier->id) }}" method="POST" onsubmit="return confirm('Suspend this approved supplier?');">
                                                @csrf
                                                <input type="hidden" name="action" value="suspend">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold text-[11px] cursor-pointer">
                                                    Suspend
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    No supplier organizations found matching the criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($suppliers->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
