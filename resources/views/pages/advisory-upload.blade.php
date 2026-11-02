@extends('layouts.app')

@section('title', 'Upload Crop Disease Photo - AI Plant Pathology Scan • NxtPerson AgriTech')

@section('content')
<div class="py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('advisory.index') }}" class="hover:text-emerald-700 transition-colors">Farmer Services</a>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Upload Crop Disease Photo</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Computer vision plant diagnostic analysis is simulated for demo evaluation. The engine accepts JPEG/PNG leaf images and matches them against Gujarat university pest pathogen models.
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upload Drag-and-Drop Area -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-3">
                    <span>📸</span> Instant Disease Diagnostic Scanner
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mb-3">
                    Upload Crop Disease Photo
                </h1>
                <p class="text-stone-600 text-sm leading-relaxed mb-6">
                    Take a clear close-up photo of infected leaves, stems, or fruits. Our simulated AI diagnostic model will detect bacterial blight, powdery mildew, bollworm punctures, or nutrient deficiencies and suggest organic remedies.
                </p>

                <!-- Interactive Upload Dropzone -->
                <div id="dropzone" class="border-2 border-dashed border-emerald-300 hover:border-emerald-600 rounded-3xl p-8 sm:p-12 text-center bg-emerald-50/40 hover:bg-emerald-50 transition-all cursor-pointer mb-6 group">
                    <input type="file" id="file-input" class="hidden" accept="image/*">
                    <div class="h-16 w-16 mx-auto rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition-transform">
                        📸
                    </div>
                    <div class="text-base font-bold text-stone-900 mb-1">
                        Tap here to capture or select a photo
                    </div>
                    <p class="text-xs text-stone-500 mb-4">
                        Supports JPEG, PNG, WEBP (Max 10MB) • Clear daylight recommended
                    </p>
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 text-white text-xs font-bold shadow-xs">
                        Browse Files on Device
                    </span>
                </div>

                <!-- Simulation Quick Trigger buttons -->
                <div class="bg-stone-50 rounded-2xl border border-stone-200 p-5">
                    <div class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-3">
                        ⚡ Or Test with Pre-Loaded Gujarat Sample Disease Images:
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button type="button" onclick="triggerSimulatedScan('Cotton Pink Bollworm Damage', 'Larvae boring into boll; spray neem oil 1500ppm + pheromone traps', '94% Confidence')" class="p-3 text-left rounded-xl bg-white border border-stone-200 hover:border-emerald-500 hover:shadow-xs transition-all text-xs">
                            <span class="font-bold text-stone-900 block mb-0.5">☁️ Cotton Bollworm</span>
                            <span class="text-[11px] text-stone-500">Saurashtra cluster sample</span>
                        </button>
                        <button type="button" onclick="triggerSimulatedScan('Banana Sigatoka Leaf Spot', 'Yellowish streaks turning brown; apply carbendazim 50% WP', '91% Confidence')" class="p-3 text-left rounded-xl bg-white border border-stone-200 hover:border-emerald-500 hover:shadow-xs transition-all text-xs">
                            <span class="font-bold text-stone-900 block mb-0.5">🍌 Banana Sigatoka</span>
                            <span class="text-[11px] text-stone-500">Charotar basin sample</span>
                        </button>
                        <button type="button" onclick="triggerSimulatedScan('Potato Late Blight', 'Water-soaked spots on foliage; prophylactic Mancozeb spray recommended', '96% Confidence')" class="p-3 text-left rounded-xl bg-white border border-stone-200 hover:border-emerald-500 hover:shadow-xs transition-all text-xs">
                            <span class="font-bold text-stone-900 block mb-0.5">🥔 Potato Late Blight</span>
                            <span class="text-[11px] text-stone-500">Deesa cold belt sample</span>
                        </button>
                    </div>
                </div>

                <!-- Simulated Diagnostic Output (Hidden initially) -->
                <div id="scan-results" class="hidden mt-6 p-6 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-800 text-white text-[11px] font-bold">
                            <span>✅</span> Scan Complete
                        </div>
                        <span id="scan-confidence" class="text-xs font-bold text-emerald-800"></span>
                    </div>
                    <h3 id="scan-title" class="text-lg font-bold text-stone-900 mb-1"></h3>
                    <p id="scan-remedy" class="text-xs text-stone-700 leading-relaxed mb-4"></p>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('advisory.index') }}" class="px-4 py-2 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors">
                            View Full Treatment Protocol →
                        </a>
                        <a href="{{ route('advisory.expert') }}" class="px-4 py-2 rounded-xl bg-white text-emerald-800 border border-emerald-300 text-xs font-bold hover:bg-emerald-50 transition-colors">
                            Escalate to Agronomist
                        </a>
                    </div>
                </div>
            </div>

            <!-- Guidelines Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                    <h2 class="text-base font-bold text-stone-900 mb-3 flex items-center gap-2">
                        <span>💡</span> Photo Guidelines
                    </h2>
                    <ul class="space-y-2.5 text-xs text-stone-600">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Capture affected leaves in bright natural morning light.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Focus clearly on the boundary between healthy and infected tissue.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Avoid blurry shots or photos taken against strong sunlight.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function triggerSimulatedScan(title, remedy, confidence) {
        const box = document.getElementById('scan-results');
        document.getElementById('scan-title').innerText = title;
        document.getElementById('scan-remedy').innerText = remedy;
        document.getElementById('scan-confidence').innerText = confidence;
        box.classList.remove('hidden');
        box.scrollIntoView({ behavior: 'smooth' });
    }

    document.getElementById('dropzone').addEventListener('click', function() {
        triggerSimulatedScan(
            'Cotton Leaf Curl Virus (CLCuV) & Whitefly Infestation',
            'Upward leaf curling with thickened veins detected. Apply Yellow sticky traps (15/acre) and alternate with botanical neem oil spray.',
            '95% AI Confidence'
        );
    });
</script>
@endsection
