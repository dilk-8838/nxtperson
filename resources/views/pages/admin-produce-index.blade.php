@extends('layouts.app')

@section('title', 'Admin Produce Procurement & Inventory — Nxtperson Private Limited')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('admin.index') }}" class="hover:text-emerald-800">Admin Portal</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Produce Procurement & Inventory</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#0c365b]/10 text-[#0c365b] border border-[#0c365b]/20">
                    <span class="w-2 h-2 rounded-full bg-[#0c365b] animate-pulse"></span>
                    Central Administration & Quality Verification
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight mt-2">
                    Produce Procurement & Inventory Hub
                </h1>
                <p class="text-slate-600 text-sm mt-0.5">
                    Review farmer harvest submissions, override/verify quality grades, confirm company purchases, and publish certified inventory to buyers.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.produce.reservations') }}" class="px-5 py-2.5 bg-white border border-slate-300 hover:border-[#0c365b] text-[#0c365b] font-bold rounded-full text-xs transition-colors shadow-2xs flex items-center gap-1.5">
                    <span>🛒</span>
                    <span>Buyer Reservations</span>
                </a>
                <a href="{{ route('admin.produce.notifications') }}" class="px-5 py-2.5 bg-white border border-slate-300 hover:border-[#0c365b] text-[#0c365b] font-bold rounded-full text-xs transition-colors shadow-2xs flex items-center gap-1.5">
                    <span>🔔</span>
                    <span>Notification Audit Log</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>✓</span> {{ session('success') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>⚠️</span> {{ session('warning') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 font-bold">✕</button>
            </div>
        @endif

        <!-- KPI Metrics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Pending Review Queue</span>
                <div class="text-2xl sm:text-3xl font-black text-amber-700 mt-1">
                    {{ $stats['pending_review_count'] }}
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">Requires agronomist audit</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Procurement Approved</span>
                <div class="text-2xl sm:text-3xl font-black text-blue-700 mt-1">
                    {{ $stats['approved_for_procurement_count'] }}
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">Price approved, ready for confirmation</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Confirmed Purchases</span>
                <div class="text-2xl sm:text-3xl font-black text-teal-700 mt-1">
                    {{ $stats['purchase_confirmed_count'] }}
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">Ready for stock publication</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Live Published Stock</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1">
                    {{ number_format((float)($stats['total_stock_kg'] / 1000), 1) }} <span class="text-base font-normal text-stone-500">Tonnes</span>
                </div>
                <span class="text-[11px] text-emerald-800 font-medium mt-1 block">Active on Buyer Marketplace</span>
            </div>
        </div>

        <!-- Lots Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-stone-200/80 overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-stone-200/80 bg-stone-50/50 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <a href="{{ route('admin.produce.index') }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'all' ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        All Lots
                    </a>
                    <a href="{{ route('admin.produce.index', ['status' => 'assessed']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'assessed' ? 'bg-amber-600 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        Assessed Queue
                    </a>
                    <a href="{{ route('admin.produce.index', ['status' => 'under_review']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'under_review' ? 'bg-indigo-600 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        Under Review
                    </a>
                    <a href="{{ route('admin.produce.index', ['status' => 'approved_for_procurement']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'approved_for_procurement' ? 'bg-blue-600 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        Rate Approved
                    </a>
                    <a href="{{ route('admin.produce.index', ['status' => 'purchase_confirmed']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'purchase_confirmed' ? 'bg-teal-600 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        Purchase Confirmed
                    </a>
                    <a href="{{ route('admin.produce.index', ['status' => 'inventory_available']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'inventory_available' ? 'bg-emerald-600 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' }}">
                        Live Stock
                    </a>
                </div>

                <div class="text-xs text-stone-500 font-mono">
                    Total: {{ $lots->total() }} Lot(s)
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-100/70 text-stone-600 uppercase tracking-wider text-[10px] font-bold border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-4">Lot & Crop</th>
                            <th class="py-3 px-4">Farmer Details</th>
                            <th class="py-3 px-4">Declared Quantity</th>
                            <th class="py-3 px-4">Grade</th>
                            <th class="py-3 px-4">Procurement Rate</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($lots as $lot)
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <!-- Lot & Crop -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-stone-100 border border-stone-200 shrink-0">
                                            @if($lot->primaryImage)
                                                <img src="{{ asset($lot->primaryImage->image_path) }}" alt="{{ $lot->variety }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-lg">🌾</div>
                                            @endif
                                        </div>
                                        <div>
                                            <strong class="font-extrabold text-stone-900 block text-xs">
                                                {{ $lot->variety }}
                                            </strong>
                                            <span class="text-[11px] text-stone-500 font-mono">{{ $lot->lot_number }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Farmer Details -->
                                <td class="py-3.5 px-4">
                                    <strong class="font-bold text-stone-900 block">{{ $lot->farmer->name ?? 'Demo Farmer' }}</strong>
                                    <span class="text-[11px] text-stone-500">{{ $lot->farmer->phone ?? 'N/A' }} • {{ $lot->district }}</span>
                                </td>

                                <!-- Declared Quantity -->
                                <td class="py-3.5 px-4 font-medium text-stone-800">
                                    <strong>{{ number_format((float)$lot->quantity, 1) }} {{ ucfirst($lot->unit) }}</strong>
                                    <span class="text-[11px] text-stone-500 block">({{ number_format((float)$lot->quantity_in_kg, 1) }} kg)</span>
                                </td>

                                <!-- Grade -->
                                <td class="py-3.5 px-4">
                                    @if($lot->approved_grade)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $lot->getGradeBadgeClass() }}">
                                            Grade {{ $lot->approved_grade }}
                                        </span>
                                    @else
                                        <span class="text-stone-400 italic text-[11px]">Pending</span>
                                    @endif
                                </td>

                                <!-- Procurement Rate -->
                                <td class="py-3.5 px-4">
                                    @if($lot->approved_procurement_price_per_kg)
                                        <strong class="text-emerald-800 font-black">₹{{ number_format((float)$lot->approved_procurement_price_per_kg, 2) }}/kg</strong>
                                        @if($lot->buyer_sale_price_per_kg)
                                            <span class="text-[10px] text-stone-500 block">Sale: ₹{{ number_format((float)$lot->buyer_sale_price_per_kg, 2) }}/kg</span>
                                        @endif
                                    @else
                                        <span class="text-stone-400 text-[11px]">Unapproved</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $lot->getStatusBadgeClass() }}">
                                        {{ $lot->getStatusLabel() }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.produce.show', $lot->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-stone-900 hover:bg-stone-800 text-white rounded-xl text-xs font-bold transition-colors">
                                        <span>Review & Act</span>
                                        <span>→</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400 italic">
                                    No produce lots found in this queue.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($lots->hasPages())
                <div class="p-4 border-t border-stone-200">
                    {{ $lots->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
