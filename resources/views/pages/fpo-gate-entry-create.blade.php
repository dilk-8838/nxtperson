@extends('layouts.app')

@section('title', 'Register Incoming Delivery — FPO Gate Operations — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Breadcrumb & Back --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('fpo.gate-entries.index') }}" class="text-xs font-semibold text-slate-400 hover:text-emerald-400 transition flex items-center gap-1.5">
                &larr; Back to Gate Operations Register
            </a>
            <span class="text-xs text-slate-400 font-mono">FPO: {{ $organization->name }}</span>
        </div>

        {{-- Main Card --}}
        <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-6 sm:p-8 shadow-xl">
            <div class="border-b border-slate-700/70 pb-5 mb-6">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-xl bg-emerald-500/20 text-emerald-400 text-xl border border-emerald-500/30">🚛</span>
                    <div>
                        <h1 class="text-2xl font-extrabold text-white tracking-tight">Register Incoming Produce Delivery</h1>
                        <p class="text-slate-400 text-xs mt-1">
                            Capture gate entry, vehicle details, delivering farmer/party, and declared produce weights.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Errors --}}
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

            <form method="POST" action="{{ route('fpo.gate-entries.store') }}" class="space-y-6" id="gateEntryForm">
                @csrf

                {{-- Section 1: Gate & Movement Type --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                            Gate / Entry Point <span class="text-rose-400">*</span>
                        </label>
                        <select name="gate_point" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                            <option value="Main Gate 1" {{ old('gate_point') === 'Main Gate 1' ? 'selected' : '' }}>Main Gate 1 (Primary Inbound)</option>
                            <option value="Weighbridge Gate 2" {{ old('gate_point') === 'Weighbridge Gate 2' ? 'selected' : '' }}>Weighbridge Gate 2</option>
                            <option value="North Delivery Bay" {{ old('gate_point') === 'North Delivery Bay' ? 'selected' : '' }}>North Delivery Bay</option>
                            <option value="Storage Warehouse Gate" {{ old('gate_point') === 'Storage Warehouse Gate' ? 'selected' : '' }}>Storage Warehouse Gate</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                            Delivery Movement Type <span class="text-rose-400">*</span>
                        </label>
                        <select name="entry_type" id="entryTypeSelect" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                            <option value="vehicle_delivery" {{ old('entry_type', 'vehicle_delivery') === 'vehicle_delivery' ? 'selected' : '' }}>Vehicle Delivery (Truck / Tractor / Mini-Van)</option>
                            <option value="walk_in" {{ old('entry_type') === 'walk_in' ? 'selected' : '' }}>Walk-in / Manual Delivery (Cart / Bullock / Headload)</option>
                            <option value="transfer" {{ old('entry_type') === 'transfer' ? 'selected' : '' }}>Internal FPO Center Transfer</option>
                            <option value="return" {{ old('entry_type') === 'return' ? 'selected' : '' }}>Customer Return / Rejection Intake</option>
                        </select>
                    </div>
                </div>

                {{-- Section 2: Vehicle & Driver Information --}}
                <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-700/50 space-y-4" id="vehicleFields">
                    <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider">Vehicle & Driver Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Vehicle Registration Number <span class="text-rose-400" id="vehicleReqStar">*</span>
                            </label>
                            <input type="text" name="vehicle_number" id="vehicleNumberInput" value="{{ old('vehicle_number') }}"
                                placeholder="e.g. GJ-03-BW-4821"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Driver Name</label>
                            <input type="text" name="driver_name" value="{{ old('driver_name') }}"
                                placeholder="e.g. Ramesh Patel"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Driver Mobile Phone</label>
                            <input type="tel" name="driver_phone" value="{{ old('driver_phone') }}"
                                placeholder="e.g. 9876543210"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>

                {{-- Section 3: Delivering Party & Membership Affiliation --}}
                <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-700/50 space-y-4">
                    <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider">Delivering Party & Membership</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Delivering Party Type <span class="text-rose-400">*</span>
                            </label>
                            <select name="delivering_party_type" id="partyTypeSelect" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                                <option value="farmer" {{ old('delivering_party_type', 'farmer') === 'farmer' ? 'selected' : '' }}>Enrolled FPO Member Farmer</option>
                                <option value="supplier" {{ old('delivering_party_type') === 'supplier' ? 'selected' : '' }}>Input Supplier / Vendor</option>
                                <option value="transporter" {{ old('delivering_party_type') === 'transporter' ? 'selected' : '' }}>Third-Party Transporter</option>
                                <option value="other" {{ old('delivering_party_type') === 'other' ? 'selected' : '' }}>Other External Delivering Party</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Enrolled Member Farmer (if applicable)
                            </label>
                            <select name="farmer_user_id" id="farmerSelect" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                                <option value="">Select Enrolled Farmer...</option>
                                @foreach($memberFarmers as $farmer)
                                    <option value="{{ $farmer->id }}" data-name="{{ $farmer->name }}" data-phone="{{ $farmer->phone }}" {{ old('farmer_user_id') == $farmer->id ? 'selected' : '' }}>
                                        {{ $farmer->name }} ({{ $farmer->phone ?? 'Member' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Delivering Party Name <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="delivering_party_name" id="partyNameInput" value="{{ old('delivering_party_name') }}"
                                required placeholder="e.g. Ramesh Patel"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>

                {{-- Section 4: Produce & Lot Linkage --}}
                <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-700/50 space-y-4">
                    <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider">Produce Details & Traceability Linkage</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Source Produce Lot (Optional Traceability Link)
                            </label>
                            <select name="source_produce_lot_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                                <option value="">Direct Entry (No Pre-existing Lot)</option>
                                @foreach($sourceLots as $lot)
                                    <option value="{{ $lot->id }}" {{ old('source_produce_lot_id') == $lot->id ? 'selected' : '' }}>
                                        #{{ $lot->lot_number }} — {{ $lot->crop->name ?? $lot->crop_slug }} ({{ $lot->farmer->name ?? 'Farmer' }}) [Avail: {{ number_format((float)$lot->available_quantity_kg, 1) }} kg]
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-slate-500 mt-1 block">Only displays member farmer lots belonging to this FPO.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Related Purchase Order (Optional)
                            </label>
                            <select name="purchase_order_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                                <option value="">No Purchase Order (Farmer Gate Offtake)</option>
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                        #{{ $po->po_number }} — {{ $po->item_description }} ({{ number_format((float)$po->quantity, 1) }} {{ $po->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Crop / Produce <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="crop_name" value="{{ old('crop_name') }}" required
                                placeholder="e.g. Cotton, Groundnut, Cumin"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Variety</label>
                            <input type="text" name="variety" value="{{ old('variety') }}"
                                placeholder="e.g. Shankar-6, GG-20"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Declared Quantity <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" step="0.1" name="declared_quantity" value="{{ old('declared_quantity') }}" required
                                placeholder="e.g. 2500"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                Declared Unit <span class="text-rose-400">*</span>
                            </label>
                            <select name="declared_unit" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-emerald-500">
                                <option value="kg" {{ old('declared_unit', 'kg') === 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                <option value="quintal" {{ old('declared_unit') === 'quintal' ? 'selected' : '' }}>Quintals (qtl)</option>
                                <option value="tonne" {{ old('declared_unit') === 'tonne' ? 'selected' : '' }}>Metric Tonnes (MT)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 5: Observations & Notes --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Initial Condition & Visual Observations</label>
                        <textarea name="initial_condition" rows="3" placeholder="e.g. Gunny bags intact, dry produce, no visible moisture leakage..."
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">{{ old('initial_condition') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Internal Gate Notes (Optional)</label>
                        <textarea name="notes" rows="3" placeholder="e.g. Instructed driver to proceed to Weighbridge Scale 01..."
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-4 border-t border-slate-700/70 flex items-center justify-between">
                    <a href="{{ route('fpo.gate-entries.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 transition">
                        Cancel
                    </a>

                    <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition flex items-center gap-2">
                        <span>Register Gate Entry & Vehicle Inbound &rarr;</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const entryTypeSelect = document.getElementById('entryTypeSelect');
        const vehicleReqStar = document.getElementById('vehicleReqStar');
        const vehicleNumberInput = document.getElementById('vehicleNumberInput');
        const farmerSelect = document.getElementById('farmerSelect');
        const partyNameInput = document.getElementById('partyNameInput');

        entryTypeSelect.addEventListener('change', function () {
            if (this.value === 'vehicle_delivery') {
                vehicleReqStar.style.display = 'inline';
                vehicleNumberInput.required = true;
            } else {
                vehicleReqStar.style.display = 'none';
                vehicleNumberInput.required = false;
            }
        });

        farmerSelect.addEventListener('change', function () {
            const selectedOpt = this.options[this.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.name) {
                partyNameInput.value = selectedOpt.dataset.name;
            }
        });
    });
</script>
@endsection
