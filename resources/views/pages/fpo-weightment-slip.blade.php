<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weighbridge Slip — {{ $weightment->weightment_number }} — NxtPerson Agritech</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .slip-card { border: 1px solid #000 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-6">
    <div class="max-w-2xl mx-auto space-y-4">

        {{-- Print Action Bar --}}
        <div class="no-print flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <a href="{{ route('fpo.gate-entries.show', $entry->id) }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-600">
                &larr; Back to Gate Entry
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg shadow transition">
                🖨️ Print Weighment Slip
            </button>
        </div>

        {{-- Official Weighbridge Slip Card --}}
        <div class="slip-card bg-white border border-slate-300 rounded-2xl p-8 shadow-md space-y-6">
            {{-- Header --}}
            <div class="border-b-2 border-slate-800 pb-4 flex items-center justify-between">
                <div>
                    <div class="text-xl font-black text-emerald-800 tracking-tight">NxtPerson Agritech</div>
                    <div class="text-xs text-slate-600 font-medium">{{ $entry->fpoOrganization->name }}</div>
                    <div class="text-[11px] text-slate-500">{{ $entry->fpoOrganization->registered_address ?? 'Station Road, APMC Compound' }}, {{ $entry->fpoOrganization->district }}, Gujarat</div>
                    <div class="text-[10px] text-slate-400">Reg No: {{ $entry->fpoOrganization->registration_number ?? 'U01100GJ2023PTC140221' }}</div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded bg-slate-100 border border-slate-300 font-mono font-bold text-xs text-slate-800">
                        OFFICIAL WEIGH SLIP
                    </span>
                    <div class="font-mono font-bold text-sm text-slate-900 mt-1.5">{{ $weightment->weightment_number }}</div>
                    <div class="text-[11px] text-slate-500">{{ $weightment->measured_at->format('d/m/Y h:i A') }}</div>
                </div>
            </div>

            {{-- Gate & Vehicle Information --}}
            <div class="grid grid-cols-2 gap-4 text-xs border-b border-slate-200 pb-4">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Gate Entry Reference</span>
                    <span class="font-mono font-bold text-slate-900 text-sm">{{ $entry->entry_number }}</span>
                    <span class="block text-slate-600 mt-1">Gate: {{ $entry->gate_point }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Vehicle Registration</span>
                    <span class="font-mono font-bold text-slate-900 text-sm">
                        {{ $entry->vehicle_number ?? 'Walk-in / Manual' }}
                    </span>
                    <span class="block text-slate-600 mt-1">Driver: {{ $entry->driver_name ?? 'Delivering Party' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Delivering Party</span>
                    <span class="font-semibold text-slate-900">{{ $entry->delivering_party_name }}</span>
                    <span class="block text-[11px] text-slate-500 capitalize">({{ str_replace('_', ' ', $entry->delivering_party_type) }})</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Produce & Variety</span>
                    <span class="font-bold text-slate-900">{{ $entry->crop_name }}</span>
                    <span class="block text-[11px] text-slate-500">{{ $entry->variety ?? 'Standard' }}</span>
                </div>
            </div>

            {{-- Weight Readings (The Main Operational Verification Block) --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <span class="text-xs uppercase font-bold text-slate-700">Measurement Scale / Weighbridge:</span>
                    <span class="font-mono font-bold text-xs text-slate-900">{{ $weightment->weighbridge_reference ?? 'Scale 01' }}</span>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                        <span class="text-[10px] uppercase text-slate-500 font-bold block">Gross Weight</span>
                        <span class="font-mono text-base font-black text-slate-800 mt-1 block">
                            {{ number_format((float)$weightment->gross_weight, 1) }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-bold uppercase">{{ $weightment->unit }}</span>
                    </div>

                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                        <span class="text-[10px] uppercase text-slate-500 font-bold block">Tare Weight</span>
                        <span class="font-mono text-base font-black text-slate-600 mt-1 block">
                            {{ number_format((float)$weightment->tare_weight, 1) }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-bold uppercase">{{ $weightment->unit }}</span>
                    </div>

                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-300 shadow-sm">
                        <span class="text-[10px] uppercase text-emerald-800 font-black block">Net Measured</span>
                        <span class="font-mono text-lg font-black text-emerald-700 mt-1 block">
                            {{ number_format((float)$weightment->net_weight, 1) }}
                        </span>
                        <span class="text-[10px] text-emerald-800 font-bold uppercase">{{ $weightment->unit }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between text-xs pt-2 border-t border-slate-200 text-slate-600">
                    <div>Declared Quantity: <strong class="text-slate-900">{{ number_format((float)$entry->declared_quantity, 1) }} {{ $entry->declared_unit }}</strong></div>
                    <div>Measurement Method: <strong class="capitalize text-slate-900">{{ str_replace('_', ' ', $weightment->measurement_method) }}</strong></div>
                </div>
            </div>

            {{-- Discrepancy Notice --}}
            @if($weightment->discrepancy_flag)
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-300 text-xs text-amber-900">
                    <div class="font-bold flex items-center gap-1.5">
                        <span>⚠️ Weight Discrepancy Flagged:</span>
                        <span>{{ $weightment->discrepancy_percentage }}% variance from declared weight</span>
                    </div>
                    @if($weightment->discrepancy_resolved)
                        <div class="text-[11px] mt-1 text-emerald-800">
                            <strong>Override Approved:</strong> {{ $weightment->discrepancy_resolution_notes }}
                        </div>
                    @else
                        <div class="text-[11px] mt-1 text-amber-800">
                            Subject to authorized manager override before commercial sign-off.
                        </div>
                    @endif
                </div>
            @endif

            {{-- Operator & Authorized Signatures --}}
            <div class="pt-8 grid grid-cols-2 gap-8 text-center text-xs">
                <div class="border-t border-slate-400 pt-2">
                    <span class="font-bold text-slate-900 block">{{ $weightment->operator_name }}</span>
                    <span class="text-[10px] text-slate-500">Weighbridge Operator Signature</span>
                </div>
                <div class="border-t border-slate-400 pt-2">
                    <span class="font-bold text-slate-900 block">{{ $entry->delivering_party_name }}</span>
                    <span class="text-[10px] text-slate-500">Driver / Farmer Acknowledgment</span>
                </div>
            </div>

            <div class="text-[10px] text-slate-400 text-center border-t border-slate-100 pt-3">
                This is a tamper-evident system-generated weighbridge certificate generated by NxtPerson Agritech operations software.
            </div>
        </div>

    </div>
</body>
</html>
