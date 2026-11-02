@extends('layouts.app')

@section('title', 'Sentinel-2 Satellite NDVI Vegetative Monitoring - Gujarat • NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Services</span>
            <span>/</span>
            <span class="text-purple-800 font-semibold">Satellite Field Health & NDVI</span>
        </nav>

        <!-- Prominent Sample / Simulated Notice -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">🛰️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Sentinel-2 multispectral imagery, false-color vegetative indices (NDVI/NDWI), and pixel-level zone distributions are simulated demonstration outputs for agricultural evaluation in Gujarat.
            </div>
        </div>

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-bold mb-2">
                    <span>🛰️</span> Sentinel-2 10-Meter Multi-Spectral Radiometry
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Satellite Field Health & Biomass Mapping
                </h1>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">
                    Track canopy vigor, water stress hotspots, and nitrogen variability without setting foot in the field.
                </p>
            </div>

            <!-- Plot Switcher Dropdown / Buttons -->
            <div class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-xs font-bold text-stone-500 ml-1">Select Enrolled Plot:</span>
                <select id="plot-select" class="text-xs font-bold rounded-xl border border-stone-300 py-1.5 px-3 focus:ring-2 focus:ring-purple-600 bg-stone-50 text-stone-800">
                    <option value="plot1" selected>📍 Plot 1: Bt-Cotton (Rajkot, 4.2 Acres)</option>
                    <option value="plot2">📍 Plot 2: Grand Naine Banana (Anand, 2.5 Acres)</option>
                    <option value="plot3">📍 Plot 3: Kufri Jyoti Potato (Deesa, 3.0 Acres)</option>
                </select>
            </div>
        </div>

        <!-- Interactive Satellite Dashboard Workspace -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
            <!-- Left Controls & Layer Selection (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Spectral Layer Selection Card -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-stone-700 mb-3 flex items-center gap-1.5">
                        <span>🧪</span> Spectral Band Radiometry
                    </h2>
                    <div class="space-y-2" id="layer-selector">
                        <button type="button" data-layer="ndvi" class="w-full text-left p-3 rounded-2xl border border-emerald-600 bg-emerald-50/80 text-emerald-950 font-bold text-xs transition-all flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                                    <span>NDVI (Vegetative Health)</span>
                                </div>
                                <div class="text-[10px] text-emerald-700 font-normal mt-0.5">NIR vs Red chlorophyll absorption</div>
                            </div>
                            <span class="text-xs">✓</span>
                        </button>

                        <button type="button" data-layer="ndwi" class="w-full text-left p-3 rounded-2xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 font-bold text-xs transition-all flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                    <span>NDWI (Canopy Moisture)</span>
                                </div>
                                <div class="text-[10px] text-stone-500 font-normal mt-0.5">Short-Wave IR leaf water content</div>
                            </div>
                        </button>

                        <button type="button" data-layer="rgb" class="w-full text-left p-3 rounded-2xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 font-bold text-xs transition-all flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                                    <span>True-Color Natural RGB</span>
                                </div>
                                <div class="text-[10px] text-stone-500 font-normal mt-0.5">Visual drone-style high resolution</div>
                            </div>
                        </button>
                    </div>

                    <!-- Interactive Threshold Sensitivity Slider -->
                    <div class="mt-5 pt-4 border-t border-stone-100">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-bold text-stone-700">Canopy Sensitivity Cutoff:</span>
                            <span id="threshold-val" class="font-mono font-bold text-purple-700">0.55 NDVI</span>
                        </div>
                        <input type="range" id="threshold-slider" min="0.20" max="0.85" step="0.05" value="0.55" class="w-full accent-purple-700 cursor-pointer">
                        <div class="flex items-center justify-between text-[10px] text-stone-400 mt-1">
                            <span>0.20 (Barren)</span>
                            <span>0.55 (Threshold)</span>
                            <span>0.85 (Ultra Vigor)</span>
                        </div>
                    </div>
                </div>

                <!-- Satellite Pass Timeline -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-stone-700 mb-3 flex items-center gap-1.5">
                        <span>🛰️</span> Overpass Archive (Sentinel-2)
                    </h2>
                    <div class="space-y-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-purple-900">20 Oct 2026 (Active)</div>
                                <div class="text-[10px] text-purple-700">0% Cloud Cover • Optimal Quality</div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-200 text-purple-900">Live</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-stone-50 border border-stone-200 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-stone-800">15 Oct 2026</div>
                                <div class="text-[10px] text-stone-500">2% Cloud Cover • Standard Quality</div>
                            </div>
                            <span class="text-[10px] text-stone-400 font-mono">Archive</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-stone-50 border border-stone-200 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-stone-800">10 Oct 2026</div>
                                <div class="text-[10px] text-stone-500">5% Cloud Cover • Standard Quality</div>
                            </div>
                            <span class="text-[10px] text-stone-400 font-mono">Archive</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Visualizer Canvas & Field Analysis (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Visualizer Field Canvas Container -->
                <div class="bg-stone-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-stone-800">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-emerald-400 animate-pulse"></span>
                            <div>
                                <h3 id="plot-title" class="font-bold text-sm sm:text-base text-white">
                                    Suresh Patel — Parcel #GJ-RJK-042 (Bt-Cotton)
                                </h3>
                                <p class="text-[11px] text-stone-400">Coordinates: 22.3039° N, 70.8022° E • Saurashtra Zone</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-950 text-emerald-300 border border-emerald-700">
                            Mean NDVI: <span id="mean-ndvi">0.74</span>
                        </span>
                    </div>

                    <!-- Simulated Visual Field Boundary (Interactive Visual Canvas Simulation) -->
                    <div class="relative w-full aspect-16/9 bg-stone-950 rounded-2xl border border-stone-800 overflow-hidden flex items-center justify-center p-4">
                        <!-- Field Polygonal Area with Dynamic Color Zones -->
                        <div id="field-render" class="relative w-full h-full max-w-xl rounded-2xl overflow-hidden border-2 border-emerald-400/80 shadow-2xl flex flex-col justify-between p-4 transition-all duration-500" style="background: radial-gradient(circle at 65% 45%, #2d6a4f 0%, #1b4332 50%, #d97706 85%, #b91c1c 100%);">
                            <!-- Overlay Grid Lines (Simulating 10m pixels) -->
                            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff10_1px,transparent_1px),linear-gradient(to_bottom,#ffffff10_1px,transparent_1px)] bg-[size:32px_32px]"></div>

                            <!-- Zone Pin Markers -->
                            <div class="relative z-10 flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-black/60 backdrop-blur-xs text-white border border-white/20">
                                    North Border: 210m
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-emerald-950">
                                    Zone A (Dense Canopy)
                                </span>
                            </div>

                            <div class="relative z-10 text-center my-auto">
                                <div class="inline-block p-4 rounded-2xl bg-black/50 backdrop-blur-md border border-white/10 text-center">
                                    <div class="text-xs text-stone-300">Spectral Index Evaluated</div>
                                    <div id="active-layer-tag" class="text-xl font-black text-emerald-300 mt-0.5">NDVI Vegetative Index</div>
                                    <div class="text-[10px] text-stone-400 mt-1">Resolution: 10m Ground Sample Distance</div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between text-[10px] text-stone-300">
                                <span class="px-2 py-0.5 rounded-full bg-amber-500/80 text-black font-bold">
                                    Zone C: Mild Moisture Stress Detected
                                </span>
                                <span class="bg-black/60 px-2 py-1 rounded-md">Parcel Area: 4.2 Acres</span>
                            </div>
                        </div>
                    </div>

                    <!-- NDVI Legend Scale Bar -->
                    <div class="mt-6 pt-4 border-t border-stone-800">
                        <div class="flex items-center justify-between text-xs text-stone-400 mb-2">
                            <span>0.10 Water / Bare Soil</span>
                            <span>0.40 Moderate Biomass</span>
                            <span>0.70 Dense Crop Foliage</span>
                            <span>0.85+ Maximum Photosynthesis</span>
                        </div>
                        <div class="h-3 w-full rounded-full overflow-hidden flex" style="background: linear-gradient(to right, #b91c1c, #d97706, #eab308, #84cc16, #22c55e, #15803d);"></div>
                    </div>
                </div>

                <!-- Zone Breakdown Analytics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-emerald-900">Zone A: Prime Canopy</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        </div>
                        <div class="text-2xl font-black text-stone-900">2.8 Acres</div>
                        <div class="text-[10px] text-emerald-700 font-semibold mt-1">67% of plot • High Yield Expected</div>
                    </div>

                    <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-amber-900">Zone B: Moderate Vigor</span>
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        </div>
                        <div class="text-2xl font-black text-stone-900">1.0 Acre</div>
                        <div class="text-[10px] text-stone-500 font-medium mt-1">24% of plot • Normal Development</div>
                    </div>

                    <div class="bg-white rounded-2xl border border-rose-200 p-4 shadow-xs bg-rose-50/30">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-rose-900">Zone C: Water Stress</span>
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                        </div>
                        <div class="text-2xl font-black text-rose-700">0.4 Acre</div>
                        <div class="text-[10px] text-rose-800 font-semibold mt-1">9% of plot • Requires Targeted Drip</div>
                    </div>
                </div>

                <!-- Agronomic Action Directives -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-stone-900 text-sm">Actionable Precision Agronomy Protocol</h4>
                        <p class="text-xs text-stone-500 mt-1 max-w-lg">
                            Increase drip fertigation runtime by 20 minutes specifically in the south-west sector of the parcel (Zone C) to relieve root moisture deficit before square initiation.
                        </p>
                    </div>
                    <a href="{{ route('advisory.index') }}" class="px-4 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-xs transition-colors whitespace-nowrap">
                        Log Crop Advisory Ticket →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const plotSelect = document.getElementById('plot-select');
    const plotTitle = document.getElementById('plot-title');
    const meanNdvi = document.getElementById('mean-ndvi');
    const fieldRender = document.getElementById('field-render');
    const slider = document.getElementById('threshold-slider');
    const thresholdVal = document.getElementById('threshold-val');
    const layerButtons = document.querySelectorAll('#layer-selector button');
    const activeLayerTag = document.getElementById('active-layer-tag');

    const plots = {
        plot1: {
            title: 'Suresh Patel — Parcel #GJ-RJK-042 (Bt-Cotton)',
            mean: '0.74',
            bg: 'radial-gradient(circle at 65% 45%, #2d6a4f 0%, #1b4332 50%, #d97706 85%, #b91c1c 100%)'
        },
        plot2: {
            title: 'Suresh Patel — Parcel #GJ-AND-119 (Grand Naine Banana)',
            mean: '0.81',
            bg: 'radial-gradient(circle at 50% 50%, #1b4332 0%, #2d6a4f 60%, #52b788 85%, #d97706 100%)'
        },
        plot3: {
            title: 'Suresh Patel — Parcel #GJ-BK-304 (Kufri Jyoti Potato)',
            mean: '0.66',
            bg: 'radial-gradient(circle at 40% 60%, #52b788 0%, #d97706 50%, #b91c1c 80%, #7f1d1d 100%)'
        }
    };

    plotSelect.addEventListener('change', function() {
        const p = plots[this.value] || plots.plot1;
        plotTitle.textContent = p.title;
        meanNdvi.textContent = p.mean;
        fieldRender.style.background = p.bg;
    });

    slider.addEventListener('input', function() {
        thresholdVal.textContent = parseFloat(this.value).toFixed(2) + ' NDVI';
    });

    layerButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            layerButtons.forEach(b => {
                b.className = 'w-full text-left p-3 rounded-2xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 font-bold text-xs transition-all flex items-center justify-between';
                const check = b.querySelector('span:last-child');
                if (check && check.textContent === '✓') check.remove();
            });

            this.className = 'w-full text-left p-3 rounded-2xl border border-purple-600 bg-purple-50/80 text-purple-950 font-bold text-xs transition-all flex items-center justify-between';
            const check = document.createElement('span');
            check.className = 'text-xs text-purple-700';
            check.textContent = '✓';
            this.appendChild(check);

            const layer = this.getAttribute('data-layer');
            if (layer === 'ndwi') {
                activeLayerTag.textContent = 'NDWI Canopy Water Moisture';
                fieldRender.style.background = 'radial-gradient(circle at 55% 45%, #1d4ed8 0%, #3b82f6 50%, #93c5fd 80%, #dbeafe 100%)';
            } else if (layer === 'rgb') {
                activeLayerTag.textContent = 'True-Color Sentinel-2 High-Res RGB';
                fieldRender.style.background = 'radial-gradient(circle at 50% 50%, #15803d 0%, #166534 60%, #3f6212 90%, #854d0e 100%)';
            } else {
                activeLayerTag.textContent = 'NDVI Vegetative Index';
                fieldRender.style.background = plots[plotSelect.value].bg;
            }
        });
    });
});
</script>
@endsection
