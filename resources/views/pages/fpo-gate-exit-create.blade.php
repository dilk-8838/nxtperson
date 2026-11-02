@extends('layouts.app')

@section('title', 'Authorize Gate Exit Pass — FPO Operations — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <a href="{{ route('fpo.gate-exits.index') }}" class="text-xs font-semibold text-slate-400 hover:text-emerald-400 transition flex items-center gap-1.5">
                &larr; Back to Gate Exits Register
            </a>
            <span class="text-xs text-slate-400 font-mono">{{ $organization->name }}</span>
        </div>

        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 sm:p-8 shadow-xl">
            <div class="border-b border-slate-700/70 pb-5 mb-6">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-xl bg-amber-500/20 text-amber-400 text-xl border border-amber-500/30">🚛</span>
                    <div>
                        <h1 class="text-2xl font-extrabold text-white tracking-tight">Authorize Gate Exit Pass</h1>
                        <p class="text-slate-400 text-xs mt-1">
                            Grant formal departure authorization for commercial dispatches, returns, or empty delivery vehicles.
                        </p>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-xs mb-6 space-y-1">
                    <p class="font-bold">Please correct the following errors:</p>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('fpo.gate-exits.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Outgoing Movement Type <span class="text-rose-400">*</span>
                    </label>
                    <select name="movement_type" id="movementTypeSelect" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                        <option value="empty_exit" {{ (old('movement_type') === 'empty_exit' || ($gateEntry && $gateEntry->status === 'inventory_posted')) ? 'selected' : '' }}>
                            Empty Vehicle Departure (Inbound delivery unloaded and completed)
                        </option>
                        <option value="order_dispatch" {{ old('movement_type') === 'order_dispatch' ? 'selected' : '' }}>
                            Buyer Purchase Order Offtake Dispatch (Commercial sale)
                        </option>
                        <option value="return_rejected" {{ (old('movement_type') === 'return_rejected' || ($gateEntry && $gateEntry->status === 'rejected')) ? 'selected' : '' }}>
                            Return of Rejected Produce (Quality non-compliant stock sent back)
                        </option>
                        <option value="transfer" {{ old('movement_type') === 'transfer' ? 'selected' : '' }}>
                            Internal Transfer to Another Center / Storage Silo
                        </option>
                    </select>
                </div>

                {{-- Linked Inbound Gate Entry (If vehicle was already inside) --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Linked Inbound Delivery Vehicle (Optional)
                    </label>
                    <select name="fpo_gate_entry_id" id="gateEntrySelect" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                        <option value="">No Inbound Entry (Standalone outgoing vehicle)</option>
                        @foreach($vehiclesInside as $insideEntry)
                            <option value="{{ $insideEntry->id }}" data-vehicle="{{ $insideEntry->vehicle_number }}" data-driver="{{ $insideEntry->driver_name }}" data-phone="{{ $insideEntry->driver_phone }}"
                                {{ (old('fpo_gate_entry_id') == $insideEntry->id || ($gateEntry && $gateEntry->id === $insideEntry->id)) ? 'selected' : '' }}>
                                Entry #{{ $insideEntry->entry_number }} — {{ $insideEntry->vehicle_number ?? 'Manual' }} ({{ $insideEntry->crop_name }} &bull; {{ $insideEntry->getStatusLabel() }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Buyer Purchase Order Selector (Conditional for order_dispatch) --}}
                <div id="poSelectContainer">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Target Buyer Purchase Order <span class="text-rose-400">*</span>
                    </label>
                    <select name="purchase_order_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                        <option value="">Select Confirmed Purchase Order...</option>
                        @foreach($eligibleOrders as $order)
                            <option value="{{ $order->id }}" {{ old('purchase_order_id') == $order->id ? 'selected' : '' }}>
                                #{{ $order->po_number }} &bull; {{ $order->item_description }} (Total Qty: {{ number_format((float)$order->quantity, 1) }} {{ $order->unit }} &bull; {{ $order->buyer->name ?? 'Buyer' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Vehicle & Driver Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Vehicle Registration #</label>
                        <input type="text" name="vehicle_number" id="vehicleNumber" value="{{ old('vehicle_number', $gateEntry?->vehicle_number) }}"
                            placeholder="e.g. GJ-03-BW-4821"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-slate-100 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Driver Name</label>
                        <input type="text" name="driver_name" id="driverName" value="{{ old('driver_name', $gateEntry?->driver_name) }}"
                            placeholder="e.g. Ramesh Patel"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Driver Phone</label>
                        <input type="tel" name="driver_phone" id="driverPhone" value="{{ old('driver_phone', $gateEntry?->driver_phone) }}"
                            placeholder="e.g. 9876543210"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:border-emerald-500">
                    </div>
                </div>

                {{-- Destination & Quantity Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Destination Facility / Delivery Address</label>
                        <input type="text" name="destination" value="{{ old('destination') }}"
                            placeholder="e.g. Adani Agri Logistics Warehouse, Gandhidham Port"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Dispatched Quantity</label>
                        <div class="flex gap-2">
                            <input type="number" step="0.1" name="dispatched_quantity" value="{{ old('dispatched_quantity', 0) }}" required
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-slate-100 focus:border-emerald-500">
                            <select name="unit" class="bg-slate-900 border border-slate-700 rounded-xl px-2 text-xs text-slate-100">
                                <option value="kg">kg</option>
                                <option value="quintal">qtl</option>
                                <option value="tonne">MT</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Authorization Instructions / Gate Pass Notes</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Authorized to depart. Verified empty tare container..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-slate-100 focus:border-emerald-500">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-700/70 flex items-center justify-between">
                    <a href="{{ route('fpo.gate-exits.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-amber-600 text-white hover:bg-amber-500 shadow-lg shadow-amber-900/30 transition">
                        Sign & Authorize Gate Exit Pass &rarr;
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const movementSelect = document.getElementById('movementTypeSelect');
        const poContainer = document.getElementById('poSelectContainer');
        const gateEntrySelect = document.getElementById('gateEntrySelect');

        function togglePO() {
            if (movementSelect.value === 'order_dispatch') {
                poContainer.style.display = 'block';
            } else {
                poContainer.style.display = 'none';
            }
        }

        movementSelect.addEventListener('change', togglePO);
        togglePO();

        gateEntrySelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.vehicle) {
                document.getElementById('vehicleNumber').value = opt.dataset.vehicle;
                document.getElementById('driverName').value = opt.dataset.driver || '';
                document.getElementById('driverPhone').value = opt.dataset.phone || '';
            }
        });
    });
</script>
@endsection
