@extends('layouts.app')

@section('title', "Gate Entry #{$entry->entry_number} — FPO Operations — NxtPerson Agritech")

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header & Quick Navigation --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('fpo.gate-entries.index') }}" class="hover:text-emerald-400 transition">&larr; Gate Register</a>
                    <span>/</span>
                    <span class="text-slate-300 font-mono">{{ $entry->entry_number }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight font-mono">{{ $entry->entry_number }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $entry->getStatusBadgeClass() }}">
                        {{ $entry->getStatusLabel() }}
                    </span>
                    @if($entry->isVehicleDelivery() && $entry->status !== \App\Models\FpoGateEntry::STATUS_EXITED)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                            Vehicle Inside
                        </span>
                    @endif
                </div>
                <p class="text-slate-400 text-xs mt-1">
                    Procurement Center: <span class="text-slate-200 font-medium">{{ $entry->fpoOrganization->name }}</span> ({{ $entry->gate_point }}) &bull; Registered at {{ $entry->entry_date_time->format('d M Y, h:i A') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('fpo.reports.show', $entry->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-200 hover:bg-slate-700 border border-slate-700 transition flex items-center gap-1.5 shadow">
                    <span>📄 Formal Report</span>
                </a>
                <a href="{{ route('fpo.reports.pdf', $entry->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition flex items-center gap-1.5">
                    <span>⬇️ Export PDF</span>
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
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-xs space-y-1">
                <p class="font-bold">Action could not be processed:</p>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Interactive Operational Lifecycle Timeline --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5 shadow-xl">
            <h2 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-4">Operational Lifecycle Workflow</h2>
            @php
                $stages = [
                    ['key' => 'entered', 'label' => 'Vehicle Entered', 'icon' => '🚛'],
                    ['key' => 'weighed', 'label' => 'Gross & Net Weighed', 'icon' => '⚖️'],
                    ['key' => 'testing', 'label' => 'Testing & Inspection', 'icon' => '🔬'],
                    ['key' => 'approval', 'label' => 'Quality Sign-Off', 'icon' => '✍️'],
                    ['key' => 'inventory', 'label' => 'Inventory Posted', 'icon' => '📦'],
                    ['key' => 'exited', 'label' => 'Exit & Handover', 'icon' => '🏁'],
                ];

                $currentStageIndex = match($entry->status) {
                    \App\Models\FpoGateEntry::STATUS_REGISTERED, \App\Models\FpoGateEntry::STATUS_ENTERED => 0,
                    \App\Models\FpoGateEntry::STATUS_WEIGHED => 1,
                    \App\Models\FpoGateEntry::STATUS_SAMPLING_PENDING, \App\Models\FpoGateEntry::STATUS_TESTING_IN_PROGRESS => 2,
                    \App\Models\FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL => 3,
                    \App\Models\FpoGateEntry::STATUS_APPROVED, \App\Models\FpoGateEntry::STATUS_PARTIALLY_ACCEPTED => 3,
                    \App\Models\FpoGateEntry::STATUS_INVENTORY_POSTED, \App\Models\FpoGateEntry::STATUS_EXIT_AUTHORIZED => 4,
                    \App\Models\FpoGateEntry::STATUS_EXITED => 5,
                    default => 1,
                };
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach($stages as $i => $stage)
                    @php
                        $isCompleted = $i < $currentStageIndex;
                        $isCurrent = $i === $currentStageIndex;
                    @endphp
                    <div class="p-3 rounded-xl border text-center transition {{ $isCurrent ? 'bg-emerald-950/60 border-emerald-500 shadow-md shadow-emerald-950' : ($isCompleted ? 'bg-slate-900/50 border-emerald-500/40 text-emerald-300' : 'bg-slate-900/30 border-slate-800 text-slate-500') }}">
                        <div class="text-xl mb-1">{{ $stage['icon'] }}</div>
                        <div class="text-xs font-bold {{ $isCurrent ? 'text-emerald-400' : ($isCompleted ? 'text-emerald-300' : 'text-slate-400') }}">
                            {{ $stage['label'] }}
                        </div>
                        <div class="text-[10px] mt-0.5 font-medium {{ $isCurrent ? 'text-emerald-300' : ($isCompleted ? 'text-emerald-400/80' : 'text-slate-600') }}">
                            {{ $isCurrent ? '● Active Stage' : ($isCompleted ? '✓ Completed' : 'Pending') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Delivery Details & Source Traceability Card --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Vehicle & Party Info --}}
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5 space-y-4">
                <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider border-b border-slate-700/70 pb-2">
                    Delivery & Vehicle Information
                </h3>
                <dl class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Delivering Party:</dt>
                        <dd class="text-slate-100 font-semibold">{{ $entry->delivering_party_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Party Type:</dt>
                        <dd class="text-slate-200 capitalize">{{ str_replace('_', ' ', $entry->delivering_party_type) }}</dd>
                    </div>
                    @if($entry->farmer)
                        <div class="flex justify-between">
                            <dt class="text-slate-400">Enrolled Member:</dt>
                            <dd class="text-emerald-400 font-semibold">✓ {{ $entry->farmer->name }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Movement Mode:</dt>
                        <dd class="text-slate-200 capitalize">{{ str_replace('_', ' ', $entry->entry_type) }}</dd>
                    </div>
                    @if($entry->vehicle_number)
                        <div class="flex justify-between items-center">
                            <dt class="text-slate-400">Vehicle No:</dt>
                            <dd class="font-mono px-2 py-0.5 rounded bg-slate-900 text-emerald-300 border border-slate-700 font-bold">
                                {{ $entry->vehicle_number }}
                            </dd>
                        </div>
                    @endif
                    @if($entry->driver_name)
                        <div class="flex justify-between">
                            <dt class="text-slate-400">Driver Name:</dt>
                            <dd class="text-slate-200">{{ $entry->driver_name }} ({{ $entry->driver_phone ?? 'No Phone' }})</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Gate Point:</dt>
                        <dd class="text-slate-300">{{ $entry->gate_point }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Registered By:</dt>
                        <dd class="text-slate-300">{{ $entry->creator->name ?? 'FPO Staff' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Produce & Weight Summary --}}
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5 space-y-4">
                <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider border-b border-slate-700/70 pb-2">
                    Produce & Declared Quantities
                </h3>
                <dl class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Produce / Crop:</dt>
                        <dd class="text-white font-bold text-sm">{{ $entry->crop_name }}</dd>
                    </div>
                    @if($entry->variety)
                        <div class="flex justify-between">
                            <dt class="text-slate-400">Variety:</dt>
                            <dd class="text-slate-300">{{ $entry->variety }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Declared Delivery Weight:</dt>
                        <dd class="font-mono text-white font-bold text-sm">{{ number_format((float)$entry->declared_quantity, 1) }} {{ $entry->declared_unit }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Measured Net Weight:</dt>
                        <dd class="font-mono font-bold text-sm {{ $entry->acceptedWeightment ? 'text-sky-300' : 'text-slate-500 italic' }}">
                            {{ $entry->acceptedWeightment ? number_format((float)$entry->acceptedWeightment->net_weight, 1).' '.$entry->acceptedWeightment->unit : 'Pending Weighment' }}
                        </dd>
                    </div>
                    @if($entry->acceptedWeightment && $entry->acceptedWeightment->discrepancy_flag)
                        <div class="p-2.5 rounded-xl bg-rose-950/60 border border-rose-700/60 text-rose-300 text-[11px] flex items-center justify-between">
                            <span>⚠️ Discrepancy: {{ $entry->acceptedWeightment->discrepancy_percentage }}% variance</span>
                            @if($entry->acceptedWeightment->discrepancy_resolved)
                                <span class="px-2 py-0.5 rounded bg-emerald-900 text-emerald-200 text-[10px] font-bold">Resolved</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-rose-900 text-rose-200 text-[10px] font-bold">Action Required</span>
                            @endif
                        </div>
                    @endif
                    @if($entry->initial_condition)
                        <div class="pt-2 border-t border-slate-700/60">
                            <dt class="text-slate-400 mb-1">Visual Observations:</dt>
                            <dd class="text-slate-300 italic text-[11px] bg-slate-900/50 p-2 rounded-lg border border-slate-800">
                                "{{ $entry->initial_condition }}"
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Commercial & Lot Traceability --}}
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5 space-y-4">
                <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider border-b border-slate-700/70 pb-2">
                    Commercial & Lot Traceability
                </h3>
                <dl class="space-y-3 text-xs">
                    @if($entry->sourceProduceLot)
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/60 space-y-1">
                            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Linked Farmer Produce Lot</span>
                            <div class="font-mono text-emerald-400 font-bold text-sm">#{{ $entry->sourceProduceLot->lot_number }}</div>
                            <div class="text-[11px] text-slate-300">Farmer: {{ $entry->sourceProduceLot->farmer->name ?? 'Enrolled Member' }}</div>
                            <div class="text-[11px] text-slate-400">Total Yield: {{ number_format((float)$entry->sourceProduceLot->quantity_in_kg, 1) }} kg &bull; Avail: {{ number_format((float)$entry->sourceProduceLot->available_quantity_kg, 1) }} kg</div>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-slate-900/40 border border-slate-800 text-slate-400 italic">
                            Direct Gate Inbound (No pre-existing marketplace lot linked).
                        </div>
                    @endif

                    @if($entry->purchaseOrder)
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/60 space-y-1">
                            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Related Purchase Order</span>
                            <div class="font-mono text-sky-400 font-bold text-sm">
                                <a href="{{ route('orders.show', $entry->purchaseOrder->id) }}" class="hover:underline">
                                    #{{ $entry->purchaseOrder->po_number }}
                                </a>
                            </div>
                            <div class="text-[11px] text-slate-300">Buyer: {{ $entry->purchaseOrder->buyer->name ?? 'Institutional Buyer' }}</div>
                            <div class="text-[11px] text-slate-400">Order Qty: {{ number_format((float)$entry->purchaseOrder->quantity, 1) }} {{ $entry->purchaseOrder->unit }} &bull; Status: {{ $entry->purchaseOrder->getStatusLabel() }}</div>
                        </div>
                    @endif

                    @if($entry->collectiveListing)
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/60 space-y-1">
                            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">FPO Collective Bulk Pool</span>
                            <div class="font-mono text-teal-400 font-bold text-sm">
                                <a href="{{ route('fpo.collective.show', $entry->collectiveListing->id) }}" class="hover:underline">
                                    #{{ $entry->collectiveListing->listing_number }}
                                </a>
                            </div>
                            <div class="text-[11px] text-slate-400">Pool Volume: {{ number_format((float)$entry->collectiveListing->total_aggregated_quantity_kg, 1) }} kg</div>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Produce Line Items Table (Multi-Item Delivery Traceability) --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5 space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-700/70 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>Produce Line Items</span>
                    <span class="text-xs font-normal text-slate-400">({{ $entry->items->count() }} items in delivery)</span>
                </h3>
                <span class="text-xs text-slate-400">Traceable lot outcome & grading</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-700/70">
                        <tr>
                            <th class="py-3 px-4">Item Ref</th>
                            <th class="py-3 px-4">Produce & Variety</th>
                            <th class="py-3 px-4">Declared Qty</th>
                            <th class="py-3 px-4">Measured Net</th>
                            <th class="py-3 px-4">Accepted Qty</th>
                            <th class="py-3 px-4">Rejected Qty</th>
                            <th class="py-3 px-4">Assigned Grade</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @foreach($entry->items as $item)
                            <tr>
                                <td class="py-3 px-4 font-mono font-bold text-emerald-400">{{ $item->item_number }}</td>
                                <td class="py-3 px-4 text-slate-200">
                                    <div class="font-semibold">{{ $item->crop_name }}</div>
                                    @if($item->variety)
                                        <div class="text-[10px] text-slate-400">{{ $item->variety }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-300">{{ number_format((float)$item->declared_quantity, 1) }} {{ $item->declared_unit }}</td>
                                <td class="py-3 px-4 font-mono text-sky-300">
                                    {{ $item->measured_net_quantity ? number_format((float)$item->measured_net_quantity, 1).' kg' : '—' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-emerald-400 font-bold">
                                    {{ $item->accepted_quantity !== null ? number_format((float)$item->accepted_quantity, 1).' kg' : '—' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-rose-400">
                                    {{ $item->rejected_quantity !== null ? number_format((float)$item->rejected_quantity, 1).' kg' : '—' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($item->assigned_grade)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-900/60 text-emerald-200 border border-emerald-700">
                                            {{ $item->assigned_grade }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 italic">Pending</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-900 text-slate-300 border border-slate-700">
                                        {{ str_replace('_', ' ', $item->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SECTION 1: Weightment & Weighbridge Records --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 space-y-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-700/70 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>⚖️ Weighment & Weighbridge Records</span>
                        <span class="text-xs font-normal text-slate-400">({{ $entry->weightments->count() }} measurements logged)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Gross, tare, and net weights with reweighment traceability and tolerance discrepancy protection.
                    </p>
                </div>
                @if($entry->acceptedWeightment)
                    <a href="{{ route('fpo.weightments.slip', [$entry->id, $entry->acceptedWeightment->id]) }}" target="_blank"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-sky-600/20 text-sky-300 hover:bg-sky-600/30 border border-sky-500/30 transition flex items-center gap-1.5 self-start">
                        <span>🖨️ Print Weighbridge Slip</span>
                    </a>
                @endif
            </div>

            {{-- Weightment History Table --}}
            @if($entry->weightments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-700/70">
                            <tr>
                                <th class="py-2.5 px-4">Seq</th>
                                <th class="py-2.5 px-4">Slip / Reference</th>
                                <th class="py-2.5 px-4">Gross Weight</th>
                                <th class="py-2.5 px-4">Tare Weight</th>
                                <th class="py-2.5 px-4">Net Weight</th>
                                <th class="py-2.5 px-4">Method / Scale</th>
                                <th class="py-2.5 px-4">Operator</th>
                                <th class="py-2.5 px-4">Discrepancy</th>
                                <th class="py-2.5 px-4">Accepted?</th>
                                <th class="py-2.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @foreach($entry->weightments as $wm)
                                <tr class="{{ $wm->is_accepted ? 'bg-slate-800/40' : 'bg-slate-900/20 opacity-60' }}">
                                    <td class="py-3 px-4 font-bold text-slate-300">#{{ $wm->sequence_number }}</td>
                                    <td class="py-3 px-4 font-mono text-emerald-400">{{ $wm->weightment_number }}</td>
                                    <td class="py-3 px-4 font-mono text-slate-200">{{ number_format((float)$wm->gross_weight, 1) }} {{ $wm->unit }}</td>
                                    <td class="py-3 px-4 font-mono text-slate-400">{{ number_format((float)$wm->tare_weight, 1) }} {{ $wm->unit }}</td>
                                    <td class="py-3 px-4 font-mono font-bold text-sky-300">{{ number_format((float)$wm->net_weight, 1) }} {{ $wm->unit }}</td>
                                    <td class="py-3 px-4">
                                        <span class="text-slate-300">{{ $wm->weighbridge_reference }}</span>
                                        <span class="block text-[10px] text-slate-500 capitalize">({{ str_replace('_', ' ', $wm->measurement_method) }})</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-300">{{ $wm->operator_name }}</td>
                                    <td class="py-3 px-4">
                                        @if($wm->discrepancy_flag)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-950 text-rose-300 border border-rose-800">
                                                {{ $wm->discrepancy_percentage }}% variance
                                            </span>
                                            @if($wm->discrepancy_resolved)
                                                <span class="block text-[10px] text-emerald-400 mt-0.5">Resolved by {{ $wm->resolver->name ?? 'Officer' }}</span>
                                            @endif
                                        @else
                                            <span class="text-emerald-400 font-semibold">✓ In tolerance</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($wm->is_accepted)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-900/60 text-emerald-200 border border-emerald-700">
                                                Active / Accepted
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-800 text-slate-400">
                                                Superseded
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('fpo.weightments.slip', [$entry->id, $wm->id]) }}" target="_blank"
                                            class="text-xs text-sky-400 hover:text-sky-300 font-medium">
                                            Slip &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Weightment Entry / Reweighment Forms Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-700/60">
                {{-- Form 1: Record New Weightment --}}
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-700/50 space-y-4">
                    <h4 class="text-xs uppercase font-bold text-slate-300 tracking-wider">
                        {{ $entry->weightments->count() === 0 ? '+ Record Initial Weighbridge Measurement' : '+ Record Additional Weightment' }}
                    </h4>
                    <form method="POST" action="{{ route('fpo.weightments.store', $entry->id) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Gross Weight <span class="text-rose-400">*</span></label>
                                <input type="number" step="0.1" name="gross_weight" required placeholder="e.g. 5200"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 font-mono focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Tare (Empty Vehicle) <span class="text-rose-400">*</span></label>
                                <input type="number" step="0.1" name="tare_weight" required placeholder="e.g. 2600"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 font-mono focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Measurement Unit <span class="text-rose-400">*</span></label>
                                <select name="unit" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:border-emerald-500">
                                    <option value="kg">Kilograms (kg)</option>
                                    <option value="quintal">Quintals (qtl)</option>
                                    <option value="tonne">Metric Tonnes (MT)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Measurement Method <span class="text-rose-400">*</span></label>
                                <select name="measurement_method" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:border-emerald-500">
                                    <option value="manual">Authorized Manual Entry / Scale</option>
                                    <option value="automated_weighbridge">Automated Electronic Weighbridge</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Operator Name <span class="text-rose-400">*</span></label>
                                <input type="text" name="operator_name" value="{{ $user->name }}" required
                                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Scale / Weighbridge ID</label>
                                <input type="text" name="weighbridge_reference" placeholder="e.g. WB-Gondal-01"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:border-emerald-500">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-3 rounded-lg text-xs transition">
                            Save Weightment Record & Compute Net
                        </button>
                    </form>
                </div>

                {{-- Form 2: Reweighment or Discrepancy Resolution --}}
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-700/50 space-y-4">
                    @if($entry->acceptedWeightment && $entry->acceptedWeightment->discrepancy_flag && ! $entry->acceptedWeightment->discrepancy_resolved)
                        {{-- Discrepancy Override Form --}}
                        <div class="p-3 bg-rose-950/40 rounded-xl border border-rose-800 space-y-3">
                            <h4 class="text-xs uppercase font-bold text-rose-300 tracking-wider">
                                ⚠️ Authorize Weight Discrepancy Override
                            </h4>
                            <p class="text-[11px] text-slate-300">
                                Measured weight diverges from declared volume by {{ $entry->acceptedWeightment->discrepancy_percentage }}%. Enter authorized explanation to proceed to testing and approval.
                            </p>
                            <form method="POST" action="{{ route('fpo.weightments.resolve', [$entry->id, $entry->acceptedWeightment->id]) }}" class="space-y-3">
                                @csrf
                                <textarea name="resolution_reason" required rows="2" placeholder="e.g. Verified moisture shrinkage of 6.2% during transit from Taluka collection center. Approved by Center Manager..."
                                    class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-slate-100 focus:border-rose-500"></textarea>
                                <button type="submit" class="w-full bg-rose-700 hover:bg-rose-600 text-white font-bold py-2 px-3 rounded-lg text-xs transition">
                                    Sign & Authorize Discrepancy Override
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($entry->acceptedWeightment)
                        <h4 class="text-xs uppercase font-bold text-slate-300 tracking-wider">
                            🔄 Request Reweighment (Preserves Audit History)
                        </h4>
                        <form method="POST" action="{{ route('fpo.weightments.reweigh', $entry->id) }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="original_weightment_id" value="{{ $entry->acceptedWeightment->id }}">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">New Gross Weight <span class="text-rose-400">*</span></label>
                                    <input type="number" step="0.1" name="gross_weight" required placeholder="e.g. 5180"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">New Tare Weight <span class="text-rose-400">*</span></label>
                                    <input type="number" step="0.1" name="tare_weight" required placeholder="e.g. 2600"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 font-mono focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Unit</label>
                                    <select name="unit" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100">
                                        <option value="kg">kg</option>
                                        <option value="quintal">quintal</option>
                                        <option value="tonne">tonne</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Method</label>
                                    <select name="measurement_method" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100">
                                        <option value="automated_weighbridge">Automated Weighbridge</option>
                                        <option value="manual">Manual Entry</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Reweighment Reason <span class="text-rose-400">*</span></label>
                                <input type="text" name="reweighment_reason" required placeholder="e.g. Driver requested second scale verification..."
                                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:border-emerald-500">
                            </div>
                            <input type="hidden" name="operator_name" value="{{ $user->name }}">
                            <button type="submit" class="w-full bg-slate-700 hover:bg-slate-600 text-slate-200 font-bold py-2 px-3 rounded-lg text-xs transition">
                                Execute Reweighment
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- SECTION 2: Sample Collection & Quality Inspection Laboratory --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 space-y-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-700/70 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>🔬 Quality Testing Laboratory & Samples</span>
                        <span class="text-xs font-normal text-slate-400">({{ $entry->samples->count() }} samples collected)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Moisture, foreign impurities, grain defects, and visual inspection parameters.
                    </p>
                </div>
                @if($entry->samples->count() > 0 && in_array($entry->status, [\App\Models\FpoGateEntry::STATUS_WEIGHED, \App\Models\FpoGateEntry::STATUS_TESTING_IN_PROGRESS]))
                    <form method="POST" action="{{ route('fpo.gate-entries.submit-approval', $entry->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-yellow-500 text-slate-950 hover:bg-yellow-400 shadow-lg shadow-yellow-900/30 transition">
                            Submit for Quality Approval &rarr;
                        </button>
                    </form>
                @endif
            </div>

            {{-- Quality Samples & Tests Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Samples List & Sample Logging Form --}}
                <div class="space-y-4">
                    <h4 class="text-xs uppercase font-bold text-slate-300 tracking-wider">Collected Samples</h4>
                    @forelse($entry->samples as $sample)
                        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-emerald-400 text-xs">{{ $sample->sample_number }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $sample->status }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-300">
                                <div><span class="text-slate-400">Sampling Method:</span> {{ $sample->sampling_method }}</div>
                                <div><span class="text-slate-400">Quantity:</span> {{ $sample->sample_quantity }} {{ $sample->sample_unit }} &bull; <span class="text-slate-400">Seal:</span> {{ $sample->container_seal_number ?? 'N/A' }}</div>
                                <div><span class="text-slate-400">Collected by:</span> {{ $sample->collector_name }} at {{ $sample->sample_date_time->format('d M, h:i A') }}</div>
                            </div>

                            {{-- Test Results under this sample --}}
                            @if($sample->qualityTests->count() > 0)
                                <div class="mt-3 pt-3 border-t border-slate-700/60">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Laboratory Test Parameters</span>
                                    <div class="space-y-1.5">
                                        @foreach($sample->qualityTests as $test)
                                            <div class="flex items-center justify-between text-xs p-1.5 rounded bg-slate-800/60 border border-slate-700/40">
                                                <span class="text-slate-200">{{ $test->parameter_name }}: <span class="font-bold text-white">{{ $test->measured_value }} {{ $test->unit }}</span></span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $test->getOutcomeBadgeClass() }}">
                                                    {{ strtoupper($test->outcome) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 rounded-xl bg-slate-900/40 border border-slate-800 text-center text-slate-400 text-xs">
                            No physical sample recorded yet. Collect sample from truck bags below.
                        </div>
                    @endforelse

                    {{-- Form: Collect New Sample --}}
                    <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-700/50 space-y-3">
                        <h5 class="text-xs uppercase font-bold text-slate-300">+ Collect Physical Sample</h5>
                        <form method="POST" action="{{ route('fpo.samples.store', $entry->id) }}" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Sample Quantity <span class="text-rose-400">*</span></label>
                                    <input type="number" step="0.05" name="sample_quantity" value="1.0" required
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Unit</label>
                                    <select name="sample_unit" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100">
                                        <option value="kg">kg</option>
                                        <option value="gm">grams</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Sampling Method <span class="text-rose-400">*</span></label>
                                    <input type="text" name="sampling_method" value="Manual Composite Sampling (IS 4333)" required
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Container Seal #</label>
                                    <input type="text" name="container_seal_number" placeholder="e.g. SEAL-8492"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 font-mono">
                                </div>
                            </div>
                            <input type="hidden" name="destination" value="On-site FPO Quality Testing Lab">
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-3 rounded-lg text-xs transition">
                                Register Sample & Seal Container
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Record Quality Test Parameters Form --}}
                <div class="space-y-4">
                    <h4 class="text-xs uppercase font-bold text-slate-300 tracking-wider">Record Quality Test Parameters</h4>
                    @if($entry->samples->count() > 0)
                        <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-700/50 space-y-3">
                            <form method="POST" action="{{ route('fpo.tests.store', [$entry->id, $entry->samples->last()->id]) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Quality Test Type <span class="text-rose-400">*</span></label>
                                    <select name="test_type" id="testTypeSelect" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100">
                                        <option value="moisture">Moisture Content Test</option>
                                        <option value="foreign_matter">Foreign Matter / Inert Impurities</option>
                                        <option value="damage_spoilage">Damaged / Discoloured Grain</option>
                                        <option value="grain_size">Grain Size / Staple Length</option>
                                        <option value="visual_condition">Visual Grade & Color Uniformity</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Parameter Name <span class="text-rose-400">*</span></label>
                                        <input type="text" name="parameter_name" id="paramNameInput" value="Moisture Content" required
                                            class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Measured Result <span class="text-rose-400">*</span></label>
                                        <input type="number" step="0.1" name="measured_value" required placeholder="e.g. 10.5"
                                            class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100 font-mono">
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-400 mb-1">Unit</label>
                                        <input type="text" name="unit" value="%" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-100">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-400 mb-1">Max Threshold</label>
                                        <input type="number" step="0.1" name="max_acceptable_value" value="12.0" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-100 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-400 mb-1">Outcome <span class="text-rose-400">*</span></label>
                                        <select name="outcome" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-slate-100">
                                            <option value="pass">Pass</option>
                                            <option value="fail">Fail</option>
                                            <option value="conditional">Conditional</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Lab Inspector Remarks</label>
                                    <input type="text" name="notes" placeholder="e.g. Digital moisture meter calibration verified..."
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-100">
                                </div>

                                <button type="submit" class="w-full bg-sky-600 hover:bg-sky-500 text-white font-bold py-2 px-3 rounded-lg text-xs transition">
                                    Record Laboratory Test Result
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-6 rounded-xl bg-slate-900/40 border border-slate-800 text-center text-slate-400 text-xs">
                            Please collect at least one sample first to record test parameters.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- SECTION 3: Quality Approval & Authorized Sign-Off --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 space-y-6 shadow-xl">
            <div class="border-b border-slate-700/70 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>✍️ Quality Decision & Authorized Sign-Off</span>
                    @if($entry->latestApproval)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $entry->latestApproval->getDecisionBadgeClass() }}">
                            {{ ucfirst(str_replace('_', ' ', $entry->latestApproval->decision)) }}
                        </span>
                    @endif
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Formal electronic sign-off by authorized FPO Quality Officer or Center Manager.
                </p>
            </div>

            @if($entry->latestApproval)
                {{-- Persisted Approval Record Display --}}
                <div class="p-5 rounded-xl bg-slate-900/60 border border-emerald-500/40 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="font-mono font-bold text-emerald-400 text-sm">#{{ $entry->latestApproval->approval_number }}</span>
                        <span class="text-xs text-slate-400">Attested on {{ $entry->latestApproval->decision_at->format('d M Y, h:i A') }}</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Approver Name:</span>
                            <span class="font-bold text-white">{{ $entry->latestApproval->approver_name }}</span>
                            <span class="block text-[10px] text-slate-400">{{ $entry->latestApproval->approver_role }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Assigned Grade:</span>
                            <span class="font-bold text-emerald-300 text-sm">{{ $entry->latestApproval->assigned_grade ?? 'Grade A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Accepted Quantity:</span>
                            <span class="font-mono font-bold text-emerald-400 text-sm">{{ number_format((float)$entry->latestApproval->accepted_quantity, 1) }} kg</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Rejected Quantity:</span>
                            <span class="font-mono font-bold text-rose-400 text-sm">{{ number_format((float)$entry->latestApproval->rejected_quantity, 1) }} kg</span>
                        </div>
                    </div>

                    @if($entry->latestApproval->rejection_reason)
                        <div class="p-2.5 rounded bg-rose-950/40 border border-rose-800 text-xs text-rose-200">
                            <strong>Rejection Reason:</strong> {{ $entry->latestApproval->rejection_reason }}
                        </div>
                    @endif

                    <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800 text-[11px] font-mono text-slate-300">
                        <div class="text-[10px] uppercase font-bold text-slate-500 mb-0.5">Authenticated Digital Signature Record:</div>
                        {{ $entry->latestApproval->signature_attestation }}
                    </div>
                </div>
            @endif

            {{-- Approval Sign-Off Action Form (Active if not finalized or pending review) --}}
            @if(in_array($entry->status, [\App\Models\FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL, \App\Models\FpoGateEntry::STATUS_TESTING_IN_PROGRESS, \App\Models\FpoGateEntry::STATUS_WEIGHED]))
                <div class="p-5 rounded-xl bg-slate-900/50 border border-slate-700/50 space-y-4">
                    <h4 class="text-xs uppercase font-bold text-slate-300 tracking-wider">
                        Execute Authorized Quality Sign-off
                    </h4>
                    <form method="POST" action="{{ route('fpo.approvals.store', $entry->id) }}" class="space-y-4" id="approvalForm">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">
                                    Quality Decision <span class="text-rose-400">*</span>
                                </label>
                                <select name="decision" id="decisionSelect" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:border-emerald-500">
                                    <option value="approved" selected>Approve Full Delivery for Inventory Posting</option>
                                    <option value="partially_accepted">Partially Accept (Separate Pass / Reject lots)</option>
                                    <option value="rejected">Reject Complete Delivery (Quality Non-Compliant)</option>
                                    <option value="quarantined">Hold / Quarantine Pending Expert Review</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">
                                    Assigned Commercial Grade <span class="text-rose-400">*</span>
                                </label>
                                <select name="assigned_grade" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:border-emerald-500">
                                    <option value="Good" selected>Good (Premium Grade 1)</option>
                                    <option value="Intermediate">Intermediate (Fair Average Quality - FAQ)</option>
                                    <option value="Bad">Bad (Substandard / Defective)</option>
                                    <option value="Grade A">Grade A (Certified Export)</option>
                                    <option value="Commercial Grade">Commercial Milling Grade</option>
                                </select>
                            </div>
                        </div>

                        {{-- Partial Acceptance Quantity Fields (Conditional) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden" id="partialQtyFields">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Accepted Quantity (kg) <span class="text-rose-400">*</span></label>
                                <input type="number" step="0.1" name="accepted_quantity" placeholder="e.g. 2000"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 font-mono focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Rejected Quantity (kg) <span class="text-rose-400">*</span></label>
                                <input type="number" step="0.1" name="rejected_quantity" placeholder="e.g. 500"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 font-mono focus:border-emerald-500">
                            </div>
                        </div>

                        <div id="rejectionReasonField" class="hidden">
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Formal Rejection Reason <span class="text-rose-400">*</span></label>
                            <input type="text" name="rejection_reason" placeholder="e.g. Excessive moisture above APMC threshold (16.2%), high insect damage..."
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:border-rose-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Quality Inspection Notes / Attestation Summary</label>
                            <textarea name="notes" rows="2" placeholder="e.g. Inspected 25 bags. Color, moisture, and grain length certified..."
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-slate-100 focus:border-emerald-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Electronic Attestation Record</label>
                            <input type="text" name="signature_attestation" value="Digitally attested and authorized by {{ $user->name }} ({{ $user->role }}) on {{ now()->toDayDateTimeString() }}"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-emerald-400">
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition shadow-lg shadow-emerald-900/30">
                            Sign Off & Record Authorized Quality Decision &rarr;
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- SECTION 4: Inventory Posting & Stock Reconciliation --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 space-y-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-700/70 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>📦 Inventory Posting & Stock Reconciliation</span>
                        @if($entry->inventoryPostings->where('status', 'posted')->count() > 0)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-600 text-white">
                                Active In Inventory
                            </span>
                        @endif
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Post accepted delivery into traceable marketplace inventory. Strictly protected by idempotency keys.
                    </p>
                </div>
            </div>

            @if($entry->inventoryPostings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-900/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-700/70">
                            <tr>
                                <th class="py-2.5 px-4">Posting Ref</th>
                                <th class="py-2.5 px-4">Quantity Posted</th>
                                <th class="py-2.5 px-4">Target Stock</th>
                                <th class="py-2.5 px-4">Stock Before &rarr; After</th>
                                <th class="py-2.5 px-4">Posted By</th>
                                <th class="py-2.5 px-4">Timestamp</th>
                                <th class="py-2.5 px-4">Status</th>
                                <th class="py-2.5 px-4 text-right">Correction</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @foreach($entry->inventoryPostings as $posting)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-bold text-emerald-400">{{ $posting->posting_number }}</td>
                                    <td class="py-3 px-4 font-mono font-bold text-white">{{ number_format((float)$posting->posted_quantity_kg, 1) }} kg</td>
                                    <td class="py-3 px-4 text-slate-200">
                                        @if($posting->produceLot)
                                            Lot #{{ $posting->produceLot->lot_number }}
                                        @elseif($posting->collectiveListing)
                                            Bulk Pool #{{ $posting->collectiveListing->listing_number }}
                                        @else
                                            FPO Central Stock
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-300">
                                        {{ number_format((float)$posting->source_stock_before_kg, 1) }} kg &rarr; {{ number_format((float)$posting->source_stock_after_kg, 1) }} kg
                                    </td>
                                    <td class="py-3 px-4 text-slate-300">{{ $posting->poster->name ?? 'FPO Staff' }}</td>
                                    <td class="py-3 px-4 text-slate-400">{{ $posting->posted_at->format('d M Y, h:i A') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $posting->status === 'posted' ? 'bg-emerald-900 text-emerald-200 border border-emerald-700' : 'bg-slate-800 text-rose-300 border border-rose-800' }}">
                                            {{ $posting->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if($posting->status === 'posted')
                                            <button type="button" onclick="document.getElementById('reversalModal-{{ $posting->id }}').classList.toggle('hidden')"
                                                class="text-[11px] text-rose-400 hover:text-rose-300 font-semibold underline">
                                                Revert &rarr;
                                            </button>

                                            {{-- Reversal Modal --}}
                                            <div id="reversalModal-{{ $posting->id }}" class="hidden fixed inset-0 z-50 bg-slate-950/80 flex items-center justify-center p-4">
                                                <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full space-y-4 text-left">
                                                    <h4 class="text-sm font-bold text-white">Revert Inventory Posting #{{ $posting->posting_number }}</h4>
                                                    <p class="text-xs text-slate-400">
                                                        This will safely deduct {{ $posting->posted_quantity_kg }} kg from marketplace stock. All actions are logged.
                                                    </p>
                                                    <form method="POST" action="{{ route('fpo.inventory.reversal', [$entry->id, $posting->id]) }}" class="space-y-3">
                                                        @csrf
                                                        <input type="text" name="reversal_reason" required placeholder="Reason for inventory correction..."
                                                            class="w-full bg-slate-950 border border-slate-700 rounded-xl p-2.5 text-xs text-slate-100">
                                                        <div class="flex justify-end gap-2">
                                                            <button type="button" onclick="document.getElementById('reversalModal-{{ $posting->id }}').classList.add('hidden')"
                                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 text-slate-300">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-700 text-white hover:bg-rose-600">
                                                                Confirm Reversal
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Post to Inventory Action (Available when Approved and not yet posted) --}}
            @if(in_array($entry->status, [\App\Models\FpoGateEntry::STATUS_APPROVED, \App\Models\FpoGateEntry::STATUS_PARTIALLY_ACCEPTED]))
                <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-emerald-300">Ready to Post {{ number_format((float)$entry->latestApproval?->accepted_quantity, 1) }} kg into Available Marketplace Stock</h4>
                        <p class="text-xs text-slate-300 mt-0.5">
                            Quality sign-off is complete. Once posted, stock becomes available for buyers and RFQ fulfilment.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('fpo.inventory.post', $entry->id) }}">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition whitespace-nowrap">
                            Post to Inventory Now &rarr;
                        </button>
                    </form>
                </div>
            @elseif($entry->status === \App\Models\FpoGateEntry::STATUS_INVENTORY_POSTED)
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700/60 text-xs text-emerald-300 flex items-center gap-2">
                    <span>✓</span>
                    <span>Accepted produce has been posted into live inventory. Proceed to Gate Pass Exit authorization below when dispatching.</span>
                </div>
            @endif
        </div>

        {{-- SECTION 5: Gate Exit & Outgoing Dispatch Authorization --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 space-y-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-700/70 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>🚛 Gate Pass & Exit Authorization</span>
                        @if($entry->latestExit)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $entry->latestExit->getStatusBadgeClass() }}">
                                {{ ucfirst($entry->latestExit->status) }}
                            </span>
                        @endif
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Authorize vehicle egress from premises, verify empty vehicle or dispatch handover.
                    </p>
                </div>
                <a href="{{ route('fpo.gate-exits.create', ['gate_entry_id' => $entry->id]) }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-600 text-white hover:bg-amber-500 shadow-lg shadow-amber-900/30 transition self-start">
                    + Authorize Gate Exit & Pass &rarr;
                </a>
            </div>

            @if($entry->gateExits->count() > 0)
                <div class="space-y-3">
                    @foreach($entry->gateExits as $exit)
                        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-amber-400">{{ $exit->exit_number }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ str_replace('_', ' ', $exit->movement_type) }}
                                    </span>
                                </div>
                                <div class="text-slate-300 mt-1">
                                    Vehicle: <span class="font-mono text-white">{{ $exit->vehicle_number ?? 'Manual' }}</span> &bull; Driver: {{ $exit->driver_name ?? 'N/A' }} &bull; Authorized by: {{ $exit->authorizer_name }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($exit->status === \App\Models\FpoGateExit::STATUS_AUTHORIZED)
                                    <form method="POST" action="{{ route('fpo.gate-exits.confirm-exit', $exit->id) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500">
                                            Confirm Actual Vehicle Exit
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 text-[11px]">Exited at {{ $exit->actual_exit_at?->format('d M, h:i A') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 italic">No exit pass issued yet for this delivery vehicle.</p>
            @endif
        </div>

        {{-- SECTION 6: Operational Exceptions & Discrepancies --}}
        @if($entry->exceptions->count() > 0)
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>⚠️ Operational Exceptions on Record</span>
                    <span class="text-xs font-normal text-slate-400">({{ $entry->exceptions->count() }})</span>
                </h3>

                <div class="space-y-3">
                    @foreach($entry->exceptions as $exc)
                        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700/60 text-xs space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-rose-400">{{ $exc->exception_number }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $exc->getSeverityBadgeClass() }}">
                                        {{ strtoupper($exc->severity) }}
                                    </span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $exc->getStatusBadgeClass() }}">
                                    {{ ucfirst($exc->status) }}
                                </span>
                            </div>
                            <div class="font-semibold text-slate-200">{{ $exc->title }}</div>
                            <div class="text-slate-400 text-[11px]">{{ $exc->description }}</div>
                            @if($exc->status === 'resolved')
                                <div class="mt-2 pt-2 border-t border-slate-800 text-[11px] text-emerald-400">
                                    <strong>Resolved:</strong> {{ $exc->resolution_action }} &bull; {{ $exc->resolution_reason }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- SECTION 7: Tamper-Evident Append-Only Audit History --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 space-y-4 shadow-xl">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span>🛡️ Tamper-Evident Operational Audit Trail</span>
                <span class="text-xs font-normal text-slate-400">({{ $entry->auditLogs->count() }} events logged)</span>
            </h3>

            <div class="space-y-2.5">
                @foreach($entry->auditLogs as $log)
                    <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-700/40 text-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-emerald-400 capitalize">{{ str_replace('_', ' ', $log->action) }}</span>
                                <span class="text-slate-500 font-mono text-[10px]">[{{ $log->entity_type }} #{{ $log->entity_id }}]</span>
                            </div>
                            <p class="text-slate-300 text-[11px]">{{ $log->reason }}</p>
                        </div>
                        <div class="text-right whitespace-nowrap text-[11px] text-slate-400">
                            <div>{{ $log->actor_name }} ({{ $log->actor_role }})</div>
                            <div class="text-[10px] text-slate-500">{{ $log->created_at->format('d M Y, h:i:s A') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const decisionSelect = document.getElementById('decisionSelect');
        const partialQtyFields = document.getElementById('partialQtyFields');
        const rejectionReasonField = document.getElementById('rejectionReasonField');

        if (decisionSelect) {
            decisionSelect.addEventListener('change', function () {
                if (this.value === 'partially_accepted') {
                    partialQtyFields.classList.remove('hidden');
                } else {
                    partialQtyFields.classList.add('hidden');
                }

                if (this.value === 'rejected') {
                    rejectionReasonField.classList.remove('hidden');
                } else {
                    rejectionReasonField.classList.add('hidden');
                }
            });
        }

        const testTypeSelect = document.getElementById('testTypeSelect');
        const paramNameInput = document.getElementById('paramNameInput');
        if (testTypeSelect && paramNameInput) {
            testTypeSelect.addEventListener('change', function () {
                const map = {
                    'moisture': 'Moisture Content',
                    'foreign_matter': 'Foreign Matter / Inert Dust',
                    'damage_spoilage': 'Damaged / Insect Borer Grain',
                    'grain_size': 'Grain Staple Length / Size',
                    'visual_condition': 'Visual Uniformity & Luster'
                };
                paramNameInput.value = map[this.value] || 'Quality Parameter';
            });
        }
    });
</script>
@endsection
