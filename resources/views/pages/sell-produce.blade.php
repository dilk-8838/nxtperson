@extends('layouts.app')

@section('title', 'Sell Your Produce — Nxtperson Private Limited')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-4xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('farmer.lots.index') }}" class="hover:text-emerald-800">Farmer Produce</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Sell Your Produce</span>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#00875a]/15 text-[#00875a] border border-[#00875a]/30">
                    <span class="w-2 h-2 rounded-full bg-[#00875a] animate-pulse"></span>
                    AI Quality Grading & Suggested Pricing
                </span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-medium">
                    Demonstration Engine v1.0
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight mt-2">
                Sell Your Harvest Lot
            </h1>
            <p class="text-slate-600 text-sm mt-1 max-w-2xl">
                Submit harvest photographs and details for automated computer-vision quality grading, sample APMC mandi benchmark valuation, and direct marketplace procurement.
            </p>
        </div>

        <!-- Demonstration Notice Banner -->
        <div class="mb-8 p-4 rounded-2xl bg-amber-50/80 border border-amber-200/90 text-amber-900 text-xs sm:text-sm flex items-start gap-3">
            <span class="text-xl shrink-0">🔬</span>
            <div>
                <strong class="font-bold">Transparent Demonstration Advisory:</strong>
                <p class="text-amber-800 mt-0.5 text-xs leading-relaxed">
                    This evaluation utilizes the Nxtperson Demonstration Rule-Engine (Sample Vision Heuristic). Uploaded photographs represent visual surface cues for initial grading; final warehouse procurement includes physical sorting, moisture calibration, and quality confirmation by authorized agronomists.
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
                <p class="font-bold mb-1">Please correct the following errors:</p>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <form action="{{ route('farmer.sell.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200/80 space-y-8">
            @csrf

            <!-- Section 1: Produce Selection -->
            <div>
                <h3 class="text-base font-bold text-stone-900 flex items-center gap-2 mb-4 pb-2 border-b border-stone-100">
                    <span class="h-6 w-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">1</span>
                    Crop & Variety Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="crop_slug" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Select Crop / Produce Category <span class="text-red-500">*</span>
                        </label>
                        <select name="crop_slug" id="crop_slug" required class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white cursor-pointer">
                            <option value="">-- Choose Crop --</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->slug }}" {{ old('crop_slug') === $crop->slug ? 'selected' : '' }}>
                                    {{ $crop->icon }} {{ $crop->en_name }} ({{ $crop->gu_name }} / {{ $crop->hi_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="variety" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Crop Variety <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="variety" id="variety" value="{{ old('variety', 'Shankar-6 Bt') }}" required placeholder="e.g. Shankar-6, Grand Naine, Kufri Jyoti" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                        <span class="text-[11px] text-stone-500 mt-1 block">Specify commercial seed/variety name for accurate grading.</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Quantity & Harvest Telemetry -->
            <div>
                <h3 class="text-base font-bold text-stone-900 flex items-center gap-2 mb-4 pb-2 border-b border-stone-100">
                    <span class="h-6 w-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">2</span>
                    Harvest Quantity & Date
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="quantity" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.1" name="quantity" id="quantity" value="{{ old('quantity', '25') }}" required min="0.1" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                    </div>

                    <div>
                        <label for="unit" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Unit <span class="text-red-500">*</span>
                        </label>
                        <select name="unit" id="unit" required class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white cursor-pointer">
                            <option value="quintal" {{ old('unit', 'quintal') === 'quintal' ? 'selected' : '' }}>Quintal (100 kg)</option>
                            <option value="tonne" {{ old('unit') === 'tonne' ? 'selected' : '' }}>Tonne (1,000 kg)</option>
                            <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                        </select>
                    </div>

                    <div>
                        <label for="harvest_date" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Harvest Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="harvest_date" id="harvest_date" value="{{ old('harvest_date', now()->toDateString()) }}" required class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label for="expected_price_per_kg" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Expected Price (₹ / kg) <span class="text-stone-400 font-normal">(Optional)</span>
                        </label>
                        <input type="number" step="0.5" name="expected_price_per_kg" id="expected_price_per_kg" value="{{ old('expected_price_per_kg') }}" placeholder="e.g. 72.00" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                        <span class="text-[11px] text-stone-500 mt-1 block">Leave blank to use automated APMC Mandi benchmark guidance.</span>
                    </div>

                    <div>
                        <label for="farming_method" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Farming Method <span class="text-red-500">*</span>
                        </label>
                        <select name="farming_method" id="farming_method" required class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white cursor-pointer">
                            <option value="conventional" {{ old('farming_method') === 'conventional' ? 'selected' : '' }}>Conventional GAP (Good Agricultural Practices)</option>
                            <option value="organic" {{ old('farming_method') === 'organic' ? 'selected' : '' }}>Certified Organic</option>
                            <option value="natural" {{ old('farming_method') === 'natural' ? 'selected' : '' }}>Natural Farming (ZBNF / Prakritik Kheti)</option>
                            <option value="ipm" {{ old('farming_method') === 'ipm' ? 'selected' : '' }}>Integrated Pest Management (IPM)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Gujarat Location -->
            <div>
                <h3 class="text-base font-bold text-stone-900 flex items-center gap-2 mb-4 pb-2 border-b border-stone-100">
                    <span class="h-6 w-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">3</span>
                    Produce Location (Gujarat District & Pincode)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="district" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            District <span class="text-red-500">*</span>
                        </label>
                        <select name="district" id="district" required class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white cursor-pointer">
                            @foreach($districts as $dist)
                                <option value="{{ $dist }}" {{ old('district', $farmer->district ?? 'Rajkot') === $dist ? 'selected' : '' }}>{{ $dist }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="taluka" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Taluka / Village
                        </label>
                        <input type="text" name="taluka" id="taluka" value="{{ old('taluka', 'Gondal') }}" placeholder="e.g. Gondal, Deesa, Petlad" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                    </div>

                    <div>
                        <label for="pincode" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            6-Digit Pincode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pincode" id="pincode" value="{{ old('pincode', $farmer->pincode ?? '360001') }}" required pattern="[0-9]{6}" maxlength="6" class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                    </div>
                </div>
            </div>

            <!-- Section 4: Multi-Photograph Upload -->
            <div>
                <h3 class="text-base font-bold text-stone-900 flex items-center gap-2 mb-2 pb-2 border-b border-stone-100">
                    <span class="h-6 w-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">4</span>
                    Upload Produce Photographs (1 to 5 Images) <span class="text-red-500">*</span>
                </h3>
                <p class="text-xs text-stone-500 mb-4">
                    Upload clear photos of the produce batch under natural lighting (top surface, lateral angle, cross-section). Maximum 10MB per file (JPG, PNG, WEBP).
                </p>

                <div class="border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-2xl p-6 text-center bg-stone-50/60 hover:bg-emerald-50/30 transition-colors">
                    <div class="max-w-md mx-auto">
                        <span class="text-3xl mb-2 block">📸</span>
                        <label for="images" class="cursor-pointer">
                            <span class="text-sm font-bold text-emerald-800 hover:text-emerald-900 underline">Click to select photographs</span>
                            <span class="text-xs text-stone-500 block mt-1">or drag and drop multiple images here</span>
                        </label>
                        <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/jpg,image/webp" required class="hidden" onchange="previewImages(this)">
                    </div>

                    <!-- Image Preview Grid -->
                    <div id="image-preview-grid" class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 hidden"></div>
                </div>
            </div>

            <!-- Section 5: Harvest Notes -->
            <div>
                <label for="description" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                    Additional Harvest Notes & Quality Declarations
                </label>
                <textarea name="description" id="description" rows="3" placeholder="Describe harvest conditions, sorting, packaging, or storage status (e.g. clean hand-picked, rain-protected, stored on wooden pallets in dry barn)." class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">{{ old('description', 'Clean hand-picked produce, protected from rain, stored on clean tarpaulin with minimal moisture.') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-stone-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('farmer.lots.index') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-800">
                    ← Cancel & Back to Lots
                </a>
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-[#0c365b] hover:bg-[#071f37] text-white font-bold rounded-full shadow-md shadow-[#0c365b]/20 transition-all text-sm flex items-center justify-center gap-2 hover:shadow-lg">
                    <span>🔬</span>
                    <span>Assess Quality & Generate Valuation</span>
                    <span>→</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImages(input) {
        const grid = document.getElementById('image-preview-grid');
        grid.innerHTML = '';
        if (input.files && input.files.length > 0) {
            grid.classList.remove('hidden');
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const card = document.createElement('div');
                    card.className = 'relative rounded-xl overflow-hidden border border-stone-200 bg-white h-24';
                    card.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <span class="absolute bottom-1 right-1 bg-stone-900/80 text-white text-[9px] px-1.5 py-0.5 rounded font-mono">#${index + 1}</span>
                    `;
                    grid.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        } else {
            grid.classList.add('hidden');
        }
    }
</script>
@endsection
