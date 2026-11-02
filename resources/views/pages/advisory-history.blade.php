@extends('layouts.app')

@section('title', 'Crop Advisory History & Case Log - NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('advisory.index') }}" class="hover:text-emerald-700 transition-colors">Crop Advisory Hub</a>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Advisory History</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Advisory records below are persistent SQLite demonstration tickets. Farmer identity and escalation statuses reflect Gujarat agronomic evaluation scenarios.
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                        <span>📋</span> Case Triage & Diagnosis Registry
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                        Advisory History & Diagnoses
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-500 mt-1">
                        Track diagnosis outcomes, biological treatments, and university expert escalation states.
                    </p>
                </div>

                <a href="{{ route('advisory.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors inline-flex items-center gap-2">
                    <span>🌱 + New Advisory Ticket</span>
                </a>
            </div>

            <!-- Table of Advisories -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold text-[11px]">
                            <th class="py-3 px-3">Ticket #</th>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Crop</th>
                            <th class="py-3 px-3">Farmer / District</th>
                            <th class="py-3 px-3">AI Diagnostic Summary</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($advisories as $adv)
                            <tr class="hover:bg-stone-50/80 transition-colors">
                                <td class="py-3.5 px-3 font-mono font-bold text-stone-900">
                                    {{ $adv->ticket_number }}
                                </td>
                                <td class="py-3.5 px-3 text-stone-500 text-xs whitespace-nowrap">
                                    {{ $adv->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-3 font-bold text-stone-800 capitalize">
                                    {{ $adv->crop_slug }}
                                </td>
                                <td class="py-3.5 px-3 text-xs">
                                    <div class="font-bold text-stone-900">{{ $adv->farmer_name }}</div>
                                    <div class="text-stone-500">{{ $adv->farmer_district }}, Gujarat</div>
                                </td>
                                <td class="py-3.5 px-3 max-w-xs">
                                    <div class="font-bold text-emerald-800 truncate">{{ $adv->ai_diagnosis_title }}</div>
                                    <div class="text-[11px] text-stone-500 truncate">{{ $adv->ai_recommended_solution }}</div>
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    @if($adv->status === 'escalated')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                            👨‍🌾 Escalated
                                        </span>
                                    @elseif($adv->status === 'resolved')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-200">
                                            ✓ Resolved
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200">
                                            ⚡ AI Analyzed
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                    <a href="{{ route('advisory.show', $adv->ticket_number) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition-colors">
                                        View Report →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-500 text-xs">
                                    No advisory tickets found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($advisories, 'links'))
                <div class="mt-6 pt-4 border-t border-stone-100">
                    {{ $advisories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
