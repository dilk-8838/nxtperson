@extends('layouts.app')

@section('title', 'Admin Command Center - Agronomic Triage & Catalog Oversight • NxtPerson')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Operations</span>
            <span>/</span>
            <span class="text-purple-800 font-semibold">Admin Command Center</span>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-600 text-lg">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">✕</button>
            </div>
        @endif

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">🛡️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                You are currently viewing the administrative triage console. You have full access to review farmer advisories, update expert escalation statuses, and manage the live product and distributor databases.
            </div>
        </div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0c365b]/10 text-[#0c365b] text-xs font-bold mb-2">
                    <span>👑</span> Administrator Authority • Gujarat State Region
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight">
                    Agronomic Operations & Platform Oversight
                </h1>
                <p class="text-slate-600 text-xs sm:text-sm mt-1">
                    Manage farmer diagnostic queues, university agronomist escalations, input catalogues, and taluka stockists.
                </p>
            </div>

            <!-- Admin Sub-Navigation Buttons (Reference Pill Style) -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.index') }}" class="px-4 py-2 rounded-full bg-[#0c365b] text-white font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>📊</span> Overview
                </a>
                <a href="{{ route('admin.suppliers') }}" class="px-4 py-2 rounded-full bg-white border border-slate-300 hover:border-[#0c365b] text-slate-700 hover:text-[#0c365b] font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>🏢</span> Suppliers
                    @if(($stats['pending_suppliers_count'] ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-900 text-[10px] font-bold">{{ $stats['pending_suppliers_count'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.fpos') }}" class="px-4 py-2 rounded-full bg-white border border-slate-300 hover:border-[#0c365b] text-slate-700 hover:text-[#0c365b] font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>🤝</span> FPOs
                    @if(($stats['pending_fpos_count'] ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-900 text-[10px] font-bold">{{ $stats['pending_fpos_count'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-full bg-white border border-slate-300 hover:border-[#0c365b] text-slate-700 hover:text-[#0c365b] font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>👥</span> Users
                </a>
                <a href="{{ route('admin.produce.index') }}" class="px-4 py-2 rounded-full bg-white border border-slate-300 hover:border-[#0c365b] text-slate-700 hover:text-[#0c365b] font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>🌾</span> Quality & Lots
                </a>
                <a href="{{ route('admin.products') }}" class="px-4 py-2 rounded-full bg-white border border-slate-300 hover:border-[#0c365b] text-slate-700 hover:text-[#0c365b] font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>📦</span> Catalog
                </a>
                <a href="{{ route('admin.gate-operations.index') }}" class="px-4 py-2 rounded-full bg-emerald-50 border border-emerald-300 hover:border-emerald-600 text-emerald-800 hover:text-emerald-950 font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>🚛</span> Gate Operations
                </a>
            </div>
        </div>

        <!-- 6 Metrics KPI Grid for All Roles -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Enrolled Farmers</div>
                <div class="text-2xl font-black text-stone-900">{{ $stats['farmers_count'] }}</div>
                <div class="text-[10px] text-emerald-700 mt-1 font-semibold">Active Profiles</div>
            </div>

            <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Suppliers</div>
                <div class="text-2xl font-black text-stone-900">{{ $stats['suppliers_count'] ?? 0 }}</div>
                <div class="text-[10px] text-amber-700 mt-1 font-semibold">{{ $stats['pending_suppliers_count'] ?? 0 }} Pending Review</div>
            </div>

            <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">FPOs & Coops</div>
                <div class="text-2xl font-black text-stone-900">{{ $stats['fpos_count'] ?? 0 }}</div>
                <div class="text-[10px] text-teal-700 mt-1 font-semibold">{{ $stats['pending_fpos_count'] ?? 0 }} Pending Review</div>
            </div>

            <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Buyer Enterprises</div>
                <div class="text-2xl font-black text-stone-900">{{ $stats['buyers_count'] ?? 0 }}</div>
                <div class="text-[10px] text-blue-700 mt-1 font-semibold">Verified Corporate</div>
            </div>

            <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Total Advisories</div>
                <div class="text-2xl font-black text-stone-900">{{ $stats['advisories_count'] }}</div>
                <div class="text-[10px] text-amber-800 mt-1 font-semibold">{{ $stats['escalated_advisories_count'] }} Escalated</div>
            </div>

            <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Input Catalog</div>
                <div class="text-2xl font-black text-stone-900">{{ $stats['products_count'] }}</div>
                <div class="text-[10px] text-stone-500 mt-1 font-medium">{{ $stats['distributors_count'] }} Dealerships</div>
            </div>
        </div>

        @if(($stats['pending_suppliers_count'] ?? 0) > 0 || ($stats['pending_fpos_count'] ?? 0) > 0)
            <!-- Pending Organization Approvals Direct Triage Banner -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                @if(($stats['pending_suppliers_count'] ?? 0) > 0)
                    <div class="bg-amber-50/80 border border-amber-300 rounded-3xl p-5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">🏢</span>
                            <div>
                                <h3 class="font-bold text-sm text-amber-950">Supplier Verification Queue</h3>
                                <p class="text-xs text-amber-800 mt-0.5">{{ $stats['pending_suppliers_count'] }} new supplier application(s) awaiting dealer license review.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.suppliers', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shrink-0 shadow-xs">
                            Review Suppliers →
                        </a>
                    </div>
                @endif

                @if(($stats['pending_fpos_count'] ?? 0) > 0)
                    <div class="bg-teal-50/80 border border-teal-300 rounded-3xl p-5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">🤝</span>
                            <div>
                                <h3 class="font-bold text-sm text-teal-950">FPO Society Verification Queue</h3>
                                <p class="text-xs text-teal-800 mt-0.5">{{ $stats['pending_fpos_count'] }} new FPO/cooperative application(s) awaiting CIN verification.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.fpos', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs shrink-0 shadow-xs">
                            Review FPOs →
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Advisory Review & Escalation Triage Queue -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-bold text-stone-900 flex items-center gap-2">
                        <span>🔬</span> Farmer Crop Advisory Triage Queue
                    </h2>
                    <p class="text-xs text-stone-500 mt-0.5">
                        Review AI diagnostic evaluations, assign agronomist recommendations, and resolve escalation tickets.
                    </p>
                </div>
                <span class="text-xs font-semibold text-stone-500">
                    Showing latest {{ $recentAdvisories->count() }} submissions
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 text-stone-700 font-bold uppercase text-[10px] tracking-wider border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-3">Ticket #</th>
                            <th class="py-3 px-3">Farmer & Location</th>
                            <th class="py-3 px-3">Crop & Issue</th>
                            <th class="py-3 px-3">AI Diagnosis</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Triage Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($recentAdvisories as $adv)
                            <tr class="hover:bg-stone-50/80 transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-stone-900 whitespace-nowrap">
                                    <a href="{{ route('advisory.show', $adv->ticket_number) }}" class="text-purple-700 hover:underline">
                                        {{ $adv->ticket_number }}
                                    </a>
                                    <div class="text-[10px] text-stone-400 font-normal">
                                        {{ $adv->created_at->format('d M, H:i') }}
                                    </div>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="font-bold text-stone-900">{{ $adv->farmer_name }}</div>
                                    <div class="text-[10px] text-stone-500">📍 {{ $adv->farmer_district }} ({{ $adv->farmer_phone }})</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-stone-800">{{ $adv->crop->en_name ?? 'Field Crop' }}</div>
                                    <div class="text-[11px] text-stone-500 max-w-xs truncate" title="{{ $adv->issue_description }}">
                                        {{ $adv->issue_description }}
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    @if($adv->ai_diagnosis_title)
                                        <div class="font-bold text-emerald-900">{{ $adv->ai_diagnosis_title }}</div>
                                        <div class="text-[10px] text-emerald-700 font-mono">{{ $adv->ai_confidence }}% AI confidence</div>
                                    @else
                                        <span class="text-stone-400 italic">Processing</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    @if($adv->status === 'escalated')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                            ⚠️ Escalated
                                        </span>
                                    @elseif($adv->status === 'resolved')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ✓ Resolved
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            AI Analyzed
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap">
                                    <!-- In-line Quick Triage Modal / Form -->
                                    <button type="button" onclick="document.getElementById('triage-modal-{{ $adv->id }}').classList.remove('hidden')" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-purple-100 hover:text-purple-900 text-stone-700 font-bold text-xs transition-colors">
                                        Update Status ✍️
                                    </button>

                                    <!-- Modal -->
                                    <div id="triage-modal-{{ $adv->id }}" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4 text-left">
                                        <div class="bg-white rounded-3xl border border-stone-200 p-6 max-w-lg w-full shadow-2xl space-y-4">
                                            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                                                <h3 class="font-bold text-stone-900 text-sm">
                                                    Triage Ticket: <span class="font-mono text-purple-700">{{ $adv->ticket_number }}</span>
                                                </h3>
                                                <button type="button" onclick="document.getElementById('triage-modal-{{ $adv->id }}').classList.add('hidden')" class="text-stone-400 hover:text-stone-700">✕</button>
                                            </div>

                                            <div class="text-xs text-stone-600 bg-stone-50 p-3 rounded-xl border border-stone-200/60">
                                                <p><strong class="text-stone-800">Farmer:</strong> {{ $adv->farmer_name }} ({{ $adv->farmer_district }})</p>
                                                <p><strong class="text-stone-800">Crop:</strong> {{ $adv->crop->en_name ?? 'Crop' }}</p>
                                                <p><strong class="text-stone-800">Diagnosed Issue:</strong> {{ $adv->ai_diagnosis_title ?? 'Under Review' }}</p>
                                            </div>

                                            <form action="{{ route('admin.advisories.status', $adv->id) }}" method="POST" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-bold text-stone-700 mb-1">Update Status</label>
                                                    <select name="status" class="w-full text-xs rounded-xl border border-stone-300 p-2 focus:ring-2 focus:ring-purple-600">
                                                        <option value="ai_analyzed" {{ $adv->status === 'ai_analyzed' ? 'selected' : '' }}>AI Analyzed</option>
                                                        <option value="escalated" {{ $adv->status === 'escalated' ? 'selected' : '' }}>Escalated to University Agronomist</option>
                                                        <option value="resolved" {{ $adv->status === 'resolved' ? 'selected' : '' }}>Resolved (Remedy Dispatched)</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-stone-700 mb-1">Agronomist Recommendation / Response Notes</label>
                                                    <textarea name="agronomist_notes" rows="3" placeholder="Enter expert guidance, chemical dosages, or irrigation instructions to provide to farmer..." class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">{{ $adv->agronomist_notes }}</textarea>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-2">
                                                    <button type="button" onclick="document.getElementById('triage-modal-{{ $adv->id }}').classList.add('hidden')" class="px-3 py-1.5 rounded-xl border border-stone-300 text-stone-700 text-xs font-semibold hover:bg-stone-100">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-xs">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400">
                                    No advisory tickets found in database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-stone-100">
                {{ $recentAdvisories->links() }}
            </div>
        </div>

        <!-- Management Quick Shortcuts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">📦</span>
                        <h3 class="text-base font-bold text-stone-900">Certified Products Catalog Management</h3>
                    </div>
                    <p class="text-xs text-stone-500 mb-4">
                        Add, edit, or archive certified inputs, bio-nutrients, seed cultivars, and pest control treatments.
                    </p>
                </div>
                <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                    <span class="text-xs text-stone-600 font-medium">{{ $stats['products_count'] }} Formulations Active</span>
                    <a href="{{ route('admin.products') }}" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors">
                        Manage Catalog →
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">🏢</span>
                        <h3 class="text-base font-bold text-stone-900">Gujarat Distributor Network Oversight</h3>
                    </div>
                    <p class="text-xs text-stone-500 mb-4">
                        Register new taluka stockists, update 6-digit postal codes, and maintain authorized category credentials.
                    </p>
                </div>
                <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                    <span class="text-xs text-stone-600 font-medium">{{ $stats['distributors_count'] }} Verified Centers</span>
                    <a href="{{ route('admin.distributors') }}" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors">
                        Manage Stockists →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
