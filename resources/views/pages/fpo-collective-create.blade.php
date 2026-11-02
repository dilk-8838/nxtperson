@extends('layouts.app')

@section('title', 'Aggregate Member Produce into Collective Lot — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">

        {{-- Header & Breadcrumb --}}
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-400 mb-3">
                <a href="{{ route('fpo.collective.index') }}" class="hover:text-emerald-400 transition">Collective Listings</a>
                <span>&rarr;</span>
                <span class="text-slate-200">New Aggregation Pool</span>
            </nav>

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Aggregate Member Produce</h1>
                    <p class="text-slate-400 text-sm mt-1">
                        Consolidate verified farm produce from active <strong class="text-white">{{ $organization->name }}</strong> member farmers.
                    </p>
                </div>
                <a href="{{ route('fpo.collective.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    &larr; Back to Catalog
                </a>
            </div>
        </div>

        {{-- Error Summary --}}
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-sm">
                <p class="font-bold mb-1">Please address the following validation issues:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Aggregation Form --}}
        <form action="{{ route('fpo.collective.store') }}" method="POST" class="space-y-8" id="aggregationForm">
            @csrf

            {{-- Bulk Listing Specification Card --}}
            <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-6 space-y-6">
                <h2 class="text-base font-bold text-white border-b border-slate-700/60 pb-3 flex items-center justify-between">
                    <span>1. Collective Bulk Lot Specifications</span>
                    <span class="text-xs font-normal text-slate-400">Enterprise Commercial Details</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Title --}}
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-slate-300 mb-1">
                            Listing Title <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            placeholder="e.g. Saurashtra FPO Shankar-6 Certified Long Staple Cotton (Bulk Pool 50 MT)">
                    </div>

                    {{-- Crop Selection --}}
                    <div>
                        <label for="crop_slug" class="block text-sm font-medium text-slate-300 mb-1">
                            Commodity Crop <span class="text-rose-400">*</span>
                        </label>
                        <select name="crop_slug" id="crop_slug" required
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            <option value="" disabled selected>— Select Crop —</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->slug }}" {{ old('crop_slug') === $crop->slug ? 'selected' : '' }}>
                                    {{ $crop->en_name }} ({{ $crop->gu_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Variety --}}
                    <div>
                        <label for="variety" class="block text-sm font-medium text-slate-300 mb-1">
                            Variety / Hybrid <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="variety" id="variety" value="{{ old('variety') }}" required
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            placeholder="e.g. Shankar-6, Gujarat-4, Cumin Bold">
                    </div>

                    {{-- Approved Quality Grade --}}
                    <div>
                        <label for="approved_grade" class="block text-sm font-medium text-slate-300 mb-1">
                            Consolidated Quality Grade <span class="text-rose-400">*</span>
                        </label>
                        <select name="approved_grade" id="approved_grade" required
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            <option value="Grade A (Export / Premium)">Grade A (Export / Premium)</option>
                            <option value="Grade B (APMC Standard FAQ)" selected>Grade B (APMC Standard FAQ)</option>
                            <option value="Grade C (Processing / Feed)">Grade C (Processing / Feed)</option>
                        </select>
                    </div>

                    {{-- Asking Price per kg --}}
                    <div>
                        <label for="asking_price_per_kg" class="block text-sm font-medium text-slate-300 mb-1">
                            Indicative Asking Price per kg (₹)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">₹</span>
                            <input type="number" step="0.01" name="asking_price_per_kg" id="asking_price_per_kg" value="{{ old('asking_price_per_kg') }}" min="0"
                                class="w-full rounded-xl bg-slate-900 border border-slate-700 pl-8 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="Leave blank for open competitive buyer bidding">
                        </div>
                    </div>

                    {{-- District --}}
                    <div>
                        <label for="district" class="block text-sm font-medium text-slate-300 mb-1">
                            Consolidation Warehouse District <span class="text-rose-400">*</span>
                        </label>
                        <select name="district" id="district" required
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            @foreach($districts as $district)
                                <option value="{{ $district }}" {{ old('district', $organization->district) === $district ? 'selected' : '' }}>
                                    {{ $district }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Taluka & Pincode --}}
                    <div>
                        <label for="taluka" class="block text-sm font-medium text-slate-300 mb-1">Taluka / Sub-district</label>
                        <input type="text" name="taluka" id="taluka" value="{{ old('taluka', $organization->taluka) }}"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    </div>

                    <div>
                        <label for="pincode" class="block text-sm font-medium text-slate-300 mb-1">
                            Warehouse Pincode <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="pincode" id="pincode" value="{{ old('pincode', $organization->pincode ?? '360001') }}" required maxlength="6" pattern="[0-9]{6}"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    </div>

                    {{-- Harvest Period --}}
                    <div>
                        <label for="harvest_period" class="block text-sm font-medium text-slate-300 mb-1">Harvest Period</label>
                        <input type="text" name="harvest_period" id="harvest_period" value="{{ old('harvest_period', 'Kharif '.date('Y')) }}"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-300 mb-1">Consolidation Notes &amp; Quality Parameters</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            placeholder="Uniform moisture content, machine cleaned, packed into 50kg bags at FPO collection center...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Member Produce Lots Selection Card --}}
            <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-700/60 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-white">2. Select Member Farmer Produce Lots</h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Only active registered members with available verified inventory appear here.
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-400 block uppercase tracking-wider font-semibold">Total Pooled Volume</span>
                        <span class="text-xl font-extrabold text-emerald-400" id="totalPooledDisplay">0.0 kg</span>
                    </div>
                </div>

                @if($eligibleLots->isEmpty())
                    <div class="p-8 rounded-xl bg-slate-900/60 border border-slate-800 text-center">
                        <p class="text-sm text-slate-400">
                            No member produce lots currently available for aggregation. Member farmers must list and have their produce assessed first.
                        </p>
                    </div>
                @else
                    <div class="space-y-3" id="lotsContainer">
                        @foreach($eligibleLots as $index => $lot)
                            <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-700/60 hover:border-emerald-500/50 transition lot-row flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" id="lot_check_{{ $lot->id }}" class="mt-1 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 bg-slate-800 border-slate-700 lot-checkbox" data-index="{{ $index }}">
                                    <div>
                                        <label for="lot_check_{{ $lot->id }}" class="font-bold text-white text-sm cursor-pointer hover:text-emerald-300">
                                            Lot #{{ $lot->lot_number }} &mdash; {{ $lot->crop?->en_name }} ({{ $lot->variety }})
                                        </label>
                                        <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-3 flex-wrap">
                                            <span>Farmer: <strong class="text-slate-200">{{ $lot->farmer?->name }}</strong></span>
                                            <span>Grade: <span class="px-1.5 py-0.2 rounded bg-slate-800 text-emerald-400 font-medium">{{ $lot->approved_grade ?? 'Standard' }}</span></span>
                                            <span>Available: <strong class="text-white">{{ number_format((float)$lot->available_quantity_kg, 1) }} kg</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <input type="hidden" name="contributions[{{ $index }}][produce_lot_id]" value="{{ $lot->id }}" disabled class="lot-id-input">
                                    <div>
                                        <label class="block text-[11px] text-slate-400 mb-0.5">Quantity to Aggregate (kg)</label>
                                        <div class="flex items-center gap-1.5">
                                            <input type="number" step="0.1" min="1" max="{{ (float)$lot->available_quantity_kg }}"
                                                name="contributions[{{ $index }}][quantity_kg]"
                                                value="{{ (float)$lot->available_quantity_kg }}"
                                                disabled
                                                class="w-32 rounded-lg bg-slate-800 border border-slate-700 px-3 py-1.5 text-xs text-white text-right focus:ring-emerald-500 lot-qty-input">
                                            <span class="text-xs text-slate-400">kg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Submit Row --}}
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-800">
                <a href="{{ route('fpo.collective.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition">
                    Publish Collective Bulk Lot &rarr;
                </button>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalDisplay = document.getElementById('totalPooledDisplay');
    const rows = document.querySelectorAll('.lot-row');

    function recalculateTotal() {
        let totalKg = 0;
        rows.forEach(row => {
            const checkbox = row.querySelector('.lot-checkbox');
            const qtyInput = row.querySelector('.lot-qty-input');
            const idInput = row.querySelector('.lot-id-input');

            if (checkbox && checkbox.checked) {
                if (qtyInput) qtyInput.disabled = false;
                if (idInput) idInput.disabled = false;
                totalKg += parseFloat(qtyInput?.value) || 0;
            } else {
                if (qtyInput) qtyInput.disabled = true;
                if (idInput) idInput.disabled = true;
            }
        });

        if (totalDisplay) {
            totalDisplay.textContent = totalKg.toLocaleString('en-IN', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' kg';
        }
    }

    rows.forEach(row => {
        const checkbox = row.querySelector('.lot-checkbox');
        const qtyInput = row.querySelector('.lot-qty-input');

        checkbox?.addEventListener('change', recalculateTotal);
        qtyInput?.addEventListener('input', recalculateTotal);
    });

    recalculateTotal();
});
</script>
@endsection
