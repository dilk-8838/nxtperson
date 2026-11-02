<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Gate Entry Report — {{ $entry->entry_number }} — NxtPerson Agritech</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .sheet { border: none !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-6">
    <div class="max-w-4xl mx-auto space-y-4">

        {{-- Top Action Bar --}}
        <div class="no-print flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <a href="{{ route('fpo.reports.show', $entry->id) }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-600">
                &larr; Back to Entry Report
            </a>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg shadow transition">
                    🖨️ Print Report
                </button>
            </div>
        </div>

        {{-- Printable Sheet --}}
        <div class="sheet bg-white border border-slate-300 rounded-2xl p-8 sm:p-12 shadow-md space-y-6">
            {{-- Header --}}
            <div class="border-b-2 border-slate-900 pb-4 flex items-center justify-between">
                <div>
                    <div class="text-2xl font-black text-emerald-800 tracking-tight">🌱 NxtPerson Agritech</div>
                    <div class="text-sm font-bold text-slate-800">{{ $entry->fpoOrganization->name }}</div>
                    <div class="text-xs text-slate-600">{{ $entry->fpoOrganization->registered_address ?? 'APMC Compound' }}, {{ $entry->fpoOrganization->district }}, Gujarat</div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded bg-slate-100 border border-slate-300 font-mono font-bold text-xs text-slate-800">
                        GATE PROCUREMENT REPORT
                    </span>
                    <div class="font-mono font-bold text-base text-slate-900 mt-1">{{ $entry->entry_number }}</div>
                    <div class="text-xs text-slate-500">Report ID: {{ $reportNumber }}</div>
                </div>
            </div>

            {{-- Summary Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Entry Date & Time</span>
                    <span class="font-bold text-slate-900">{{ $entry->entry_date_time->format('d/m/Y h:i A') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Vehicle Number</span>
                    <span class="font-mono font-bold text-slate-900">{{ $entry->vehicle_number ?? 'Walk-in' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Delivering Party</span>
                    <span class="font-bold text-slate-900">{{ $entry->delivering_party_name }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Status</span>
                    <span class="font-bold text-emerald-800 uppercase">{{ $entry->getStatusLabel() }}</span>
                </div>
            </div>

            {{-- Produce Line Items --}}
            <div class="space-y-2">
                <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Produce Line Items</h3>
                <table class="w-full text-left text-xs border border-slate-200 rounded-lg overflow-hidden">
                    <thead class="bg-slate-100 text-slate-600 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="p-2 border-b">Produce</th>
                            <th class="p-2 border-b">Declared Weight</th>
                            <th class="p-2 border-b">Measured Net</th>
                            <th class="p-2 border-b">Accepted Qty</th>
                            <th class="p-2 border-b">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($entry->items as $item)
                            <tr>
                                <td class="p-2 font-bold">{{ $item->crop_name }} ({{ $item->variety ?? 'Standard' }})</td>
                                <td class="p-2 font-mono">{{ number_format((float)$item->declared_quantity, 1) }} {{ $item->declared_unit }}</td>
                                <td class="p-2 font-mono font-bold text-emerald-800">{{ $item->measured_net_quantity ? number_format((float)$item->measured_net_quantity, 1).' kg' : '—' }}</td>
                                <td class="p-2 font-mono font-bold text-emerald-800">{{ $item->accepted_quantity ? number_format((float)$item->accepted_quantity, 1).' kg' : '—' }}</td>
                                <td class="p-2">{{ $item->assigned_grade ?? 'Pending' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Weighbridge Readings --}}
            <div class="space-y-2">
                <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Weighbridge Measurements</h3>
                <table class="w-full text-left text-xs border border-slate-200 rounded-lg overflow-hidden">
                    <thead class="bg-slate-100 text-slate-600 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="p-2 border-b">Slip Ref</th>
                            <th class="p-2 border-b">Gross Weight</th>
                            <th class="p-2 border-b">Tare Weight</th>
                            <th class="p-2 border-b">Net Weight</th>
                            <th class="p-2 border-b">Method</th>
                            <th class="p-2 border-b">Discrepancy</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($entry->weightments as $wm)
                            <tr>
                                <td class="p-2 font-mono">{{ $wm->weightment_number }}</td>
                                <td class="p-2 font-mono">{{ number_format((float)$wm->gross_weight, 1) }} {{ $wm->unit }}</td>
                                <td class="p-2 font-mono">{{ number_format((float)$wm->tare_weight, 1) }} {{ $wm->unit }}</td>
                                <td class="p-2 font-mono font-bold text-emerald-800">{{ number_format((float)$wm->net_weight, 1) }} {{ $wm->unit }}</td>
                                <td class="p-2 capitalize">{{ str_replace('_', ' ', $wm->measurement_method) }}</td>
                                <td class="p-2">{{ $wm->discrepancy_flag ? $wm->discrepancy_percentage.'% variance' : 'In Tolerance' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-3 text-center text-slate-400">No weightment recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Quality Sign-off --}}
            @if($entry->latestApproval)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                    <div class="flex items-center justify-between font-bold">
                        <span>Quality Decision: {{ strtoupper($entry->latestApproval->decision) }} (Grade: {{ $entry->latestApproval->assigned_grade ?? 'Good' }})</span>
                        <span class="text-slate-500">Sign-off: {{ $entry->latestApproval->approver_name }} ({{ $entry->latestApproval->approver_role }})</span>
                    </div>
                    <div class="font-mono text-[11px] text-slate-600">{{ $entry->latestApproval->signature_attestation }}</div>
                </div>
            @endif

            {{-- Signatures --}}
            <div class="pt-8 grid grid-cols-2 gap-8 text-center text-xs">
                <div class="border-t border-slate-400 pt-2">
                    <span class="font-bold text-slate-900 block">{{ $entry->latestApproval->approver_name ?? 'Quality Officer' }}</span>
                    <span class="text-[10px] text-slate-500">FPO Quality Authority Signature</span>
                </div>
                <div class="border-t border-slate-400 pt-2">
                    <span class="font-bold text-slate-900 block">{{ $entry->delivering_party_name }}</span>
                    <span class="text-[10px] text-slate-500">Delivering Party Acknowledgment</span>
                </div>
            </div>

            <div class="text-[10px] text-slate-400 text-center border-t border-slate-100 pt-3">
                Generated via NxtPerson Agritech Operations &bull; Certified Record &bull; {{ $generatedAt->format('d M Y, h:i A') }}
            </div>
        </div>

    </div>
</body>
</html>
