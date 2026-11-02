@extends('layouts.app')

@section('title', "Gate Entry Report #{$entry->entry_number} — NxtPerson Agritech")

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Top Navigation & Export Action Bar --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('fpo.gate-entries.show', $entry->id) }}" class="text-xs font-semibold text-slate-400 hover:text-emerald-400 transition flex items-center gap-1.5">
                &larr; Back to Gate Entry #{{ $entry->entry_number }}
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('fpo.reports.print', $entry->id) }}" target="_blank"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-800 text-slate-200 hover:bg-slate-700 border border-slate-700 transition">
                    🖨️ Print View
                </a>
                <a href="{{ route('fpo.reports.pdf', $entry->id) }}"
                    class="px-4 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow transition flex items-center gap-1.5">
                    <span>⬇️ Download Official PDF</span>
                </a>
            </div>
        </div>

        {{-- Formal Entry Report Sheet --}}
        <div class="bg-white text-slate-900 rounded-2xl p-8 sm:p-12 shadow-2xl border border-slate-300 space-y-8 font-sans">

            {{-- 1. Branding & FPO Details --}}
            <div class="border-b-2 border-slate-900 pb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="text-2xl font-black text-emerald-800 tracking-tight flex items-center gap-2">
                        <span>🌱 NxtPerson Agritech</span>
                    </div>
                    <div class="text-sm font-bold text-slate-800 mt-1">{{ $entry->fpoOrganization->name }}</div>
                    <div class="text-xs text-slate-600">{{ $entry->fpoOrganization->registered_address ?? 'APMC Compound' }}, {{ $entry->fpoOrganization->district }}, Gujarat</div>
                    <div class="text-[11px] text-slate-500">Registration # {{ $entry->fpoOrganization->registration_number ?? 'U01100GJ2023PTC140221' }} &bull; Phone: {{ $entry->fpoOrganization->contact_phone ?? 'N/A' }}</div>
                </div>
                <div class="sm:text-right">
                    <span class="inline-block px-3 py-1 rounded bg-slate-100 border border-slate-300 font-mono font-bold text-xs text-slate-800">
                        OFFICIAL GATE PROCUREMENT REPORT
                    </span>
                    <div class="font-mono font-bold text-base text-slate-900 mt-2">{{ $entry->entry_number }}</div>
                    <div class="text-xs text-slate-500">Report ID: {{ $reportNumber }}</div>
                    <div class="text-[11px] text-slate-400">Generated: {{ $generatedAt->format('d M Y, h:i A') }}</div>
                </div>
            </div>

            {{-- 2 & 3. Gate Entry, Date & Time, Gate Point --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Gate Entry Number</span>
                    <span class="font-mono font-bold text-slate-900 text-sm">{{ $entry->entry_number }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Entry Date & Time</span>
                    <span class="font-bold text-slate-900">{{ $entry->entry_date_time->format('d/m/Y h:i A') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Gate Point</span>
                    <span class="font-bold text-slate-900">{{ $entry->gate_point }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Lifecycle Status</span>
                    <span class="font-bold text-emerald-800 uppercase text-[11px]">{{ $entry->getStatusLabel() }}</span>
                </div>
            </div>

            {{-- 4 & 5. Delivery, Vehicle & Party Information --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs border-b border-slate-200 pb-6">
                <div class="space-y-2">
                    <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Delivering Party & Membership</h3>
                    <div class="space-y-1 text-slate-700">
                        <div>Name: <strong class="text-slate-900">{{ $entry->delivering_party_name }}</strong></div>
                        <div>Party Type: <strong class="capitalize text-slate-900">{{ str_replace('_', ' ', $entry->delivering_party_type) }}</strong></div>
                        @if($entry->farmer)
                            <div class="text-emerald-700">✓ Enrolled FPO Member Farmer (ID #{{ $entry->farmer->id }})</div>
                        @endif
                        <div>Contact Phone: <strong class="text-slate-900">{{ $entry->delivering_party_contact ?? 'N/A' }}</strong></div>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Vehicle & Driver Details</h3>
                    <div class="space-y-1 text-slate-700">
                        <div>Mode: <strong class="capitalize text-slate-900">{{ str_replace('_', ' ', $entry->entry_type) }}</strong></div>
                        <div>Vehicle Registration: <strong class="font-mono text-slate-900">{{ $entry->vehicle_number ?? 'Walk-in / Cart' }}</strong></div>
                        <div>Driver Name: <strong class="text-slate-900">{{ $entry->driver_name ?? 'N/A' }}</strong> ({{ $entry->driver_phone ?? 'N/A' }})</div>
                        <div>Initial Condition: <em class="text-slate-600">{{ $entry->initial_condition ?? 'Normal intake condition' }}</em></div>
                    </div>
                </div>
            </div>

            {{-- 6 & 7. Produce Line Items & Declared Quantities --}}
            <div class="space-y-3">
                <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Produce Line Items & Declared Quantities</h3>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Item #</th>
                                <th class="py-2.5 px-3">Crop / Produce</th>
                                <th class="py-2.5 px-3">Variety</th>
                                <th class="py-2.5 px-3">Declared Weight</th>
                                <th class="py-2.5 px-3">Measured Net</th>
                                <th class="py-2.5 px-3">Traceability Source</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800">
                            @foreach($entry->items as $item)
                                <tr>
                                    <td class="py-2.5 px-3 font-mono font-bold">{{ $item->item_number }}</td>
                                    <td class="py-2.5 px-3 font-bold">{{ $item->crop_name }}</td>
                                    <td class="py-2.5 px-3">{{ $item->variety ?? 'Standard' }}</td>
                                    <td class="py-2.5 px-3 font-mono">{{ number_format((float)$item->declared_quantity, 1) }} {{ $item->declared_unit }}</td>
                                    <td class="py-2.5 px-3 font-mono font-bold text-emerald-800">
                                        {{ $item->measured_net_quantity ? number_format((float)$item->measured_net_quantity, 1).' kg' : '—' }}
                                    </td>
                                    <td class="py-2.5 px-3 font-mono text-[11px] text-slate-600">
                                        @if($entry->sourceProduceLot)
                                            Lot #{{ $entry->sourceProduceLot->lot_number }}
                                        @elseif($entry->purchaseOrder)
                                            PO #{{ $entry->purchaseOrder->po_number }}
                                        @else
                                            Direct Gate Inbound
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 8, 9 & 10. Weighment Readings, Net Weight, and Discrepancies --}}
            <div class="space-y-3">
                <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Weightment Measurements & Reconciled Net</h3>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Slip #</th>
                                <th class="py-2.5 px-3">Gross Weight</th>
                                <th class="py-2.5 px-3">Tare Weight</th>
                                <th class="py-2.5 px-3">Net Weight</th>
                                <th class="py-2.5 px-3">Method / Scale</th>
                                <th class="py-2.5 px-3">Operator</th>
                                <th class="py-2.5 px-3">Discrepancy / Variance</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800">
                            @forelse($entry->weightments as $wm)
                                <tr class="{{ $wm->is_accepted ? 'bg-emerald-50/40' : 'opacity-60' }}">
                                    <td class="py-2.5 px-3 font-mono font-bold">{{ $wm->weightment_number }}</td>
                                    <td class="py-2.5 px-3 font-mono">{{ number_format((float)$wm->gross_weight, 1) }} {{ $wm->unit }}</td>
                                    <td class="py-2.5 px-3 font-mono">{{ number_format((float)$wm->tare_weight, 1) }} {{ $wm->unit }}</td>
                                    <td class="py-2.5 px-3 font-mono font-black text-emerald-800">{{ number_format((float)$wm->net_weight, 1) }} {{ $wm->unit }}</td>
                                    <td class="py-2.5 px-3 capitalize">{{ str_replace('_', ' ', $wm->measurement_method) }}</td>
                                    <td class="py-2.5 px-3">{{ $wm->operator_name }}</td>
                                    <td class="py-2.5 px-3">
                                        @if($wm->discrepancy_flag)
                                            <span class="text-rose-700 font-bold">⚠️ {{ $wm->discrepancy_percentage }}% variance</span>
                                            @if($wm->discrepancy_resolved)
                                                <span class="block text-[10px] text-emerald-700">Resolved: {{ $wm->discrepancy_resolution_notes }}</span>
                                            @endif
                                        @else
                                            <span class="text-emerald-700">✓ In Tolerance</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 font-bold text-[11px] {{ $wm->is_accepted ? 'text-emerald-800' : 'text-slate-400' }}">
                                        {{ $wm->is_accepted ? 'Accepted' : 'Superseded' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 text-center text-slate-400 italic">No weightment recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 11 & 12. Quality Inspection & Laboratory Test Parameters --}}
            <div class="space-y-3">
                <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Quality Inspection & Laboratory Test Parameters</h3>
                @php
                    $allTests = \App\Models\FpoQualityTest::whereHas('sample', fn($q) => $q->where('fpo_gate_entry_id', $entry->id))->get();
                @endphp
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Test Ref</th>
                                <th class="py-2.5 px-3">Parameter Name</th>
                                <th class="py-2.5 px-3">Measured Result</th>
                                <th class="py-2.5 px-3">Acceptable Range</th>
                                <th class="py-2.5 px-3">Outcome</th>
                                <th class="py-2.5 px-3">Tested By</th>
                                <th class="py-2.5 px-3">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800">
                            @forelse($allTests as $test)
                                <tr>
                                    <td class="py-2.5 px-3 font-mono font-bold">{{ $test->test_number }}</td>
                                    <td class="py-2.5 px-3 font-bold">{{ $test->parameter_name }}</td>
                                    <td class="py-2.5 px-3 font-mono font-black">{{ $test->measured_value }} {{ $test->unit }}</td>
                                    <td class="py-2.5 px-3 text-slate-500">
                                        {{ $test->max_acceptable_value ? 'Max '.$test->max_acceptable_value.' '.$test->unit : 'Standard' }}
                                    </td>
                                    <td class="py-2.5 px-3 font-bold uppercase text-[11px] {{ $test->outcome === 'pass' ? 'text-emerald-700' : ($test->outcome === 'fail' ? 'text-rose-700' : 'text-amber-700') }}">
                                        {{ $test->outcome }}
                                    </td>
                                    <td class="py-2.5 px-3">{{ $test->tester_name }}</td>
                                    <td class="py-2.5 px-3 text-slate-500">{{ $test->tested_at->format('d/m/Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-slate-400 italic">No quality tests recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 13, 14 & 15. Quality Decision, Approval Attestation, Accepted / Rejected Quantities --}}
            <div class="space-y-3">
                <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Quality Approval & Sign-Off Attestation</h3>
                @if($entry->latestApproval)
                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Decision:</span>
                                <span class="font-bold text-sm uppercase {{ $entry->latestApproval->decision === 'approved' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $entry->latestApproval->decision }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Assigned Commercial Grade:</span>
                                <span class="font-bold text-sm text-slate-900">{{ $entry->latestApproval->assigned_grade ?? 'Good' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Accepted Quantity:</span>
                                <span class="font-mono font-bold text-sm text-emerald-700">{{ number_format((float)$entry->latestApproval->accepted_quantity, 1) }} kg</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Rejected Quantity:</span>
                                <span class="font-mono font-bold text-sm text-rose-700">{{ number_format((float)$entry->latestApproval->rejected_quantity, 1) }} kg</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <span class="text-slate-500">Authorized Approver:</span>
                                <strong class="text-slate-900">{{ $entry->latestApproval->approver_name }}</strong>
                                <span class="text-slate-500">({{ $entry->latestApproval->approver_role }})</span>
                            </div>
                            <div class="text-slate-500">Timestamp: {{ $entry->latestApproval->decision_at->format('d M Y, h:i A') }}</div>
                        </div>

                        <div class="p-2.5 rounded bg-white border border-slate-200 font-mono text-[11px] text-slate-600">
                            <strong>Signature Attestation:</strong> {{ $entry->latestApproval->signature_attestation }}
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic">
                        Quality sign-off is pending authorized review.
                    </div>
                @endif
            </div>

            {{-- 16 & 17. Inventory Posting & Gate Exit Records --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <div class="space-y-2">
                    <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Inventory Posting Status</h3>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-slate-700">
                        @if($entry->inventoryPostings->where('status', 'posted')->count() > 0)
                            @php $p = $entry->inventoryPostings->where('status', 'posted')->first(); @endphp
                            <div class="text-emerald-700 font-bold text-sm">✓ Successfully Posted to Inventory</div>
                            <div>Posting Ref: <strong class="font-mono text-slate-900">#{{ $p->posting_number }}</strong></div>
                            <div>Quantity: <strong class="font-mono text-slate-900">{{ number_format((float)$p->posted_quantity_kg, 1) }} kg</strong></div>
                            <div>Posted at: {{ $p->posted_at->format('d M Y, h:i A') }} by {{ $p->poster->name ?? 'Staff' }}</div>
                        @else
                            <div class="text-slate-500 italic">Not yet posted into live marketplace stock.</div>
                        @endif
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Gate Exit & Dispatch Pass</h3>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-slate-700">
                        @if($entry->latestExit)
                            <div>Exit Pass: <strong class="font-mono text-slate-900">#{{ $entry->latestExit->exit_number }}</strong></div>
                            <div>Movement Type: <strong class="capitalize text-slate-900">{{ str_replace('_', ' ', $entry->latestExit->movement_type) }}</strong></div>
                            <div>Authorized by: {{ $entry->latestExit->authorizer_name }}</div>
                            <div>Actual Departure: {{ $entry->latestExit->actual_exit_at ? $entry->latestExit->actual_exit_at->format('d M Y, h:i A') : 'Vehicle On-Site' }}</div>
                        @else
                            <div class="text-slate-500 italic">No exit pass issued. Vehicle on-site or completed delivery.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 18 & 19. Exceptions & Audit Log Summary --}}
            <div class="space-y-3 pt-2">
                <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Audit Log & Exceptions Record</h3>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                    @forelse($entry->auditLogs as $log)
                        <div class="flex items-center justify-between text-[11px] text-slate-600 border-b border-slate-200 pb-1">
                            <span><strong class="text-slate-800 capitalize">{{ str_replace('_', ' ', $log->action) }}</strong>: {{ $log->reason }}</span>
                            <span class="text-slate-400 font-mono">{{ $log->actor_name }} &bull; {{ $log->created_at->format('d/m H:i') }}</span>
                        </div>
                    @empty
                        <span class="text-slate-400 italic">No audit records logged.</span>
                    @endforelse
                </div>
            </div>

            {{-- 20. Report Certification Footer --}}
            <div class="border-t-2 border-slate-900 pt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs text-slate-500">
                <div>
                    <div>NxtPerson Agritech Quality Procurement System</div>
                    <div class="text-[10px]">Tamper-evident system record &bull; Report ID: {{ $reportNumber }}</div>
                </div>
                <div class="text-right">
                    <div>Authorized Quality Signature Attested</div>
                    <div class="text-[10px]">Printed from NxtPerson Agritech Portal at {{ $generatedAt->toDayDateTimeString() }}</div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
