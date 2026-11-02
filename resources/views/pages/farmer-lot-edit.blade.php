@extends('layouts.app')

@section('title', "Edit Produce Lot #{$lot->lot_number} — NxtPerson Agritech")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-3xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('farmer.lots.index') }}" class="hover:text-emerald-800">My Produce Lots</a>
            <span>/</span>
            <a href="{{ route('farmer.lots.show', $lot->id) }}" class="hover:text-emerald-800">{{ $lot->lot_number }}</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Edit</span>
        </nav>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-stone-200">
            <div class="flex items-center justify-between pb-6 border-b border-stone-200 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#0c365b]">
                        Edit Produce Listing
                    </h1>
                    <p class="text-xs text-stone-500 font-mono mt-0.5">
                        Lot Reference: <strong class="text-stone-800">{{ $lot->lot_number }}</strong> • Crop: {{ $lot->crop->en_name ?? ucfirst($lot->crop_slug) }}
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $lot->getStatusBadgeClass() }}">
                    {{ $lot->getStatusLabel() }}
                </span>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs">
                    <strong class="font-bold block mb-1">Please correct the following errors:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('farmer.lots.update', $lot->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Variety -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Crop Variety / Cultivar <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="variety" value="{{ old('variety', $lot->variety) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>

                <!-- Quantity & Unit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Available Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.1" name="quantity" value="{{ old('quantity', $lot->quantity) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                        @php
                            $committedKg = (float)$lot->reserved_quantity_kg + (float)$lot->sold_quantity_kg + (float)$lot->aggregated_quantity_kg;
                        @endphp
                        @if($committedKg > 0)
                            <p class="text-[11px] text-amber-700 mt-1">
                                Note: {{ number_format($committedKg, 1) }} kg is already committed or reserved and cannot be deducted.
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Measurement Unit <span class="text-red-500">*</span>
                        </label>
                        <select name="unit" required
                                class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                            <option value="kg" {{ old('unit', $lot->unit) === 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                            <option value="quintal" {{ old('unit', $lot->unit) === 'quintal' ? 'selected' : '' }}>Quintals (100 kg)</option>
                            <option value="tonne" {{ old('unit', $lot->unit) === 'tonne' ? 'selected' : '' }}>Tonnes (1,000 kg)</option>
                        </select>
                    </div>
                </div>

                <!-- Location -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            District <span class="text-red-500">*</span>
                        </label>
                        <select name="district" required
                                class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                            @foreach($districts as $d)
                                <option value="{{ $d }}" {{ old('district', $lot->district) === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Taluka / Tehsil
                        </label>
                        <input type="text" name="taluka" value="{{ old('taluka', $lot->taluka) }}"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Pincode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pincode" value="{{ old('pincode', $lot->pincode) }}" required maxlength="6"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Harvest Date & Price Expectation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Harvest / Picking Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="harvest_date" value="{{ old('harvest_date', $lot->harvest_date->format('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Farmer Expected Price (₹/kg)
                        </label>
                        <input type="number" step="0.5" name="expected_price_per_kg" value="{{ old('expected_price_per_kg', $lot->expected_price_per_kg) }}"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Farming Method -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Cultivation Method <span class="text-red-500">*</span>
                    </label>
                    <select name="farming_method" required
                            class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                        <option value="conventional" {{ old('farming_method', $lot->farming_method) === 'conventional' ? 'selected' : '' }}>Conventional Agriculture</option>
                        <option value="organic" {{ old('farming_method', $lot->farming_method) === 'organic' ? 'selected' : '' }}>Certified Organic (NPOP)</option>
                        <option value="natural" {{ old('farming_method', $lot->farming_method) === 'natural' ? 'selected' : '' }}>ZBNF / Natural Farming</option>
                        <option value="ipm" {{ old('farming_method', $lot->farming_method) === 'ipm' ? 'selected' : '' }}>Integrated Pest Management (IPM)</option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Produce Description & Quality Notes
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">{{ old('description', $lot->description) }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-stone-200">
                    <a href="{{ route('farmer.lots.show', $lot->id) }}" class="px-5 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
