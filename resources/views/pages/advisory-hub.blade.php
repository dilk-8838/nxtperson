@extends('layouts.app')

@section('title', 'AI Crop Advisory Hub & Leaf Diagnostics - NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Farmer Services</span>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Crop Advisory Hub</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Diagnostic analyses, biological remedy recommendations, and agronomist triage escalations are simulated demonstration outputs for Gujarat agro-ecological zones. No outbound carrier SMS is dispatched.
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <span class="text-emerald-700 font-bold">✓ Ticket Active</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Left 2 Cols: Main Interactive Diagnostic Form -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-3">
                    <span>🔬</span> Neural Agronomic Diagnostic Engine
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mb-2">
                    Submit Crop Symptoms for AI Diagnosis
                </h1>
                <p class="text-stone-600 text-xs sm:text-sm leading-relaxed mb-6">
                    Select your crop, describe observed leaf discoloration or insect damage, and upload an optional photo. Our engine will generate biological remedies and provide agronomist escalation.
                </p>

                <!-- Advisory Form -->
                <form action="{{ route('advisory.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Crop Selection -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">
                            Select Target Gujarat Crop <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach($crops as $c)
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-stone-200 bg-stone-50/50 hover:bg-emerald-50/60 cursor-pointer transition-colors has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-900">
                                    <input type="radio" name="crop_slug" value="{{ $c->slug }}" {{ ($loop->first || old('crop_slug') === $c->slug) ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs font-bold truncate">{{ $c->en_name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('crop_slug')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Symptom Pattern Quick-Select -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">
                            Primary Symptom Pattern (Optional)
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-stone-200 text-xs cursor-pointer hover:bg-stone-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="symptom_key" value="bollworm" class="text-emerald-600">
                                <span>🐛 Bollworm / Larval holes</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-stone-200 text-xs cursor-pointer hover:bg-stone-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="symptom_key" value="curl" class="text-emerald-600">
                                <span>🍂 Leaf curling / Yellow veins</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-stone-200 text-xs cursor-pointer hover:bg-stone-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="symptom_key" value="blight" class="text-emerald-600">
                                <span>🌧️ Water-soaked blight spots</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Description -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Detailed Symptom Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="issue_description" id="issue_description" rows="3" class="w-full rounded-2xl border border-stone-300 bg-white p-3.5 text-sm text-stone-900 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600 focus:outline-hidden" placeholder="Describe symptoms, e.g.: Leaves are curling upward with sticky honeydew on underside; observed whiteflies on cotton squares...">{{ old('issue_description', 'Observed green bolls dropping with tiny entry holes and pinkish larvae inside flowers.') }}</textarea>
                        @error('issue_description')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Photo Upload -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Upload Leaf or Plant Photo (JPEG, PNG, WEBP - Max 10MB)
                        </label>
                        <div class="border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-2xl p-4 sm:p-6 text-center bg-stone-50/50 hover:bg-emerald-50/30 transition-all cursor-pointer" onclick="document.getElementById('leaf-file-input').click()">
                            <input type="file" name="image" id="leaf-file-input" class="hidden" accept="image/*" onchange="previewUpload(this)">
                            <div class="text-2xl mb-1">📸</div>
                            <div class="text-xs font-bold text-stone-800" id="upload-label">
                                Tap here to attach field photo from your phone or device
                            </div>
                            <div class="text-[10px] text-stone-400 mt-0.5">
                                High-resolution daylight photo recommended for automated visual feature extraction
                            </div>
                        </div>
                        @error('image')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Farmer Location / Contact -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Farmer Name</label>
                            <input type="text" name="farmer_name" value="{{ Auth::user() ? Auth::user()->name : 'Suresh Patel' }}" class="w-full rounded-xl border border-stone-300 py-2 px-3 text-xs bg-stone-50">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Mobile Number</label>
                            <input type="text" name="farmer_phone" value="{{ Auth::user() ? Auth::user()->phone : '9876543210' }}" class="w-full rounded-xl border border-stone-300 py-2 px-3 text-xs bg-stone-50 font-mono">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Gujarat District</label>
                            <select name="farmer_district" class="w-full rounded-xl border border-stone-300 py-2 px-3 text-xs bg-stone-50">
                                <option value="Rajkot" selected>Rajkot (Saurashtra)</option>
                                <option value="Anand">Anand (Charotar)</option>
                                <option value="Banaskantha">Banaskantha (Deesa)</option>
                                <option value="Surat">Surat (South Gujarat)</option>
                                <option value="Mehsana">Mehsana (North Gujarat)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3 flex items-center justify-between">
                        <span class="text-xs text-stone-500">
                            🔒 Simulated AI Inference Engine • SLA: &lt; 5s
                        </span>
                        <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md transition-all flex items-center gap-2">
                            <span>🌱 Generate AI Diagnosis →</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Sidebar: Fast Pre-Loaded Samples & Expert Desk -->
            <div class="space-y-6">
                <!-- Fast Demonstration Presets -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                    <h2 class="text-base font-bold text-stone-900 mb-3 flex items-center gap-2">
                        <span>⚡</span> Fast Demo Test Cases
                    </h2>
                    <p class="text-xs text-stone-500 mb-4">
                        Click any sample scenario below to automatically fill the advisory form for testing:
                    </p>
                    <div class="space-y-2.5">
                        <button type="button" onclick="setPreset('cotton', 'bollworm', 'Observed green bolls dropping with tiny entry holes and pinkish larvae inside flowers in Gondal taluka.')" class="w-full text-left p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 transition-colors text-xs">
                            <strong class="font-bold text-stone-900 block">☁️ Cotton Pink Bollworm</strong>
                            <span class="text-[11px] text-stone-500">Saurashtra cotton belt case</span>
                        </button>
                        <button type="button" onclick="setPreset('potato', 'blight', 'Water-soaked brown necrotic patches spreading on lower potato foliage after heavy morning fog in Deesa.')" class="w-full text-left p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 transition-colors text-xs">
                            <strong class="font-bold text-stone-900 block">🥔 Potato Late Blight</strong>
                            <span class="text-[11px] text-stone-500">Banaskantha cold-fog case</span>
                        </button>
                        <button type="button" onclick="setPreset('banana', 'blight', 'Sigatoka leaf streaks with yellow halo turning necrotic across Grand Naine banana lower fronds.')" class="w-full text-left p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 transition-colors text-xs">
                            <strong class="font-bold text-stone-900 block">🍌 Banana Black Sigatoka</strong>
                            <span class="text-[11px] text-stone-500">Anand Charotar basin case</span>
                        </button>
                    </div>
                </div>

                <!-- Expert Escalation Desk Promo -->
                <div class="bg-gradient-to-br from-emerald-900 via-emerald-950 to-stone-900 text-white rounded-3xl p-6 shadow-md">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Human Agronomist Review</div>
                    <h3 class="text-base font-bold text-white mb-2">Need Agronomic Guidance?</h3>
                    <p class="text-xs text-emerald-100/90 leading-relaxed mb-4">
                        Every AI ticket can be escalated to our panel of demonstration agronomists and crop protection specialists for verification.
                    </p>
                    <a href="{{ route('advisory.expert') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white text-emerald-900 text-xs font-bold hover:bg-emerald-50 transition-colors">
                        👨‍🌾 View Agronomist Panel →
                    </a>
                </div>

                <!-- History shortcut -->
                <div class="p-4 rounded-2xl bg-stone-100 border border-stone-200 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-stone-900 text-xs">Advisory History</div>
                        <div class="text-[11px] text-stone-500">Browse all past tickets & resolutions</div>
                    </div>
                    <a href="{{ route('advisory.history') }}" class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-bold text-xs">
                        View Records →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewUpload(input) {
        if (input.files && input.files[0]) {
            document.getElementById('upload-label').innerText = '✓ Photo attached: ' + input.files[0].name;
        }
    }

    function setPreset(crop, symptom, desc) {
        // select radio
        const cropRadio = document.querySelector(`input[name="crop_slug"][value="${crop}"]`);
        if (cropRadio) cropRadio.checked = true;

        const symRadio = document.querySelector(`input[name="symptom_key"][value="${symptom}"]`);
        if (symRadio) symRadio.checked = true;

        document.getElementById('issue_description').value = desc;
        window.scrollTo({ top: 300, behavior: 'smooth' });
    }
</script>
@endsection
