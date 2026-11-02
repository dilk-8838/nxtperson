@extends('layouts.app')

@section('title', 'Agro Weather & 7-Day Spray Windows - Gujarat • NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-stone-400">Services</span>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Agro Weather Intelligence</span>
        </nav>

        <!-- Prominent Sample Data Notice -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">🌦️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Micro-climate telemetry, 7-day spray suitability windows, and soil moisture calculations are demonstration sample records for Gujarat agro-climatic zones.
            </div>
        </div>

        <!-- Header & District Switcher -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold mb-2">
                    <span>📡</span> Gujarat IMD & Automatic Weather Station (AWS) Grid
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Agro-Meteorological Advisory & Spray Forecast
                </h1>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">
                    Zone: <strong class="text-stone-800">{{ $weather->zone_name }}</strong>
                </p>
            </div>

            <!-- District Switcher Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 bg-white p-1.5 rounded-2xl border border-stone-200/80 shadow-xs">
                @foreach($districts as $d)
                    <a href="{{ route('weather.index', ['district' => $d]) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors {{ $selectedDistrict === $d ? 'bg-emerald-800 text-white shadow-2xs' : 'text-stone-600 hover:bg-stone-100' }}">
                        📍 {{ $d }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Telemetry Main Card -->
        <div class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-stone-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl pointer-events-none select-none">
                🌦️
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <!-- Current Condition Left (6 cols) -->
                <div class="lg:col-span-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-800 text-emerald-200 border border-emerald-600">
                            Current Telemetry
                        </span>
                        <span class="text-xs text-stone-300">{{ $weather->district }} Station • Updated 15 min ago</span>
                    </div>

                    <div class="flex items-baseline gap-4">
                        <span class="text-5xl sm:text-6xl font-black text-white tracking-tight">
                            {{ $weather->temperature }}°C
                        </span>
                        <div>
                            <div class="text-base font-bold text-emerald-200">{{ $weather->condition }}</div>
                            <div class="text-xs text-stone-300">Heat Index: {{ $weather->heat_index }}</div>
                        </div>
                    </div>

                    <!-- Spray Status Big Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 text-xs sm:text-sm font-bold">
                        <span>🎯 Chemical & Bio-Spray Window:</span>
                        <span class="text-white underline">{{ $weather->spray_window }}</span>
                    </div>
                </div>

                <!-- Sensor Measurements Grid (6 cols) -->
                <div class="lg:col-span-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="p-3.5 rounded-2xl bg-stone-950/50 border border-stone-800/80 backdrop-blur-xs">
                        <div class="text-[11px] text-stone-400 font-medium">Relative Humidity</div>
                        <div class="text-xl font-black text-white mt-0.5">{{ $weather->humidity }}%</div>
                        <div class="text-[10px] text-emerald-400 mt-1">Normal Transpiration</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-stone-950/50 border border-stone-800/80 backdrop-blur-xs">
                        <div class="text-[11px] text-stone-400 font-medium">Wind Vector</div>
                        <div class="text-xl font-black text-white mt-0.5">{{ $weather->wind_speed }} km/h</div>
                        <div class="text-[10px] text-stone-300 mt-1">Heading: {{ $weather->wind_direction }}</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-stone-950/50 border border-stone-800/80 backdrop-blur-xs">
                        <div class="text-[11px] text-stone-400 font-medium">Rain Probability</div>
                        <div class="text-xl font-black text-white mt-0.5">{{ $weather->rainfall_probability }}%</div>
                        <div class="text-[10px] text-emerald-400 mt-1">Low Washoff Risk</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-stone-950/50 border border-stone-800/80 backdrop-blur-xs">
                        <div class="text-[11px] text-stone-400 font-medium">Root Soil Moisture</div>
                        <div class="text-sm font-bold text-white mt-1">{{ $weather->soil_moisture }}</div>
                        <div class="text-[10px] text-stone-400 mt-0.5">Sensor depth: 15 cm</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-stone-950/50 border border-stone-800/80 backdrop-blur-xs">
                        <div class="text-[11px] text-stone-400 font-medium">Dew Point</div>
                        <div class="text-xl font-black text-white mt-0.5">18.2°C</div>
                        <div class="text-[10px] text-stone-300 mt-1">No Fogging</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-stone-950/50 border border-stone-800/80 backdrop-blur-xs">
                        <div class="text-[11px] text-stone-400 font-medium">UV Radiation</div>
                        <div class="text-xl font-black text-white mt-0.5">Index 6</div>
                        <div class="text-[10px] text-amber-300 mt-1">Moderate Intensity</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 12-Hour Hourly Spray Suitability Timeline -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-stone-900 flex items-center gap-2">
                        <span>⏱️</span> Hourly Field Spraying Suitability (Next 12 Hours)
                    </h2>
                    <p class="text-xs text-stone-500 mt-0.5">
                        Evaluated against droplet drift, thermal evaporation, and foliar absorption thresholds.
                    </p>
                </div>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                    Target: 6 to 10 km/h wind
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
                @foreach($hourlySpray as $hs)
                    <div class="p-3 rounded-2xl border text-center {{ $hs['color'] === 'emerald' ? 'bg-emerald-50/50 border-emerald-200 text-emerald-950' : ($hs['color'] === 'amber' ? 'bg-amber-50/50 border-amber-200 text-amber-950' : 'bg-rose-50/50 border-rose-200 text-rose-950') }}">
                        <div class="font-bold text-xs">{{ $hs['time'] }}</div>
                        <div class="text-base font-black my-1">{{ $hs['temp'] }}</div>
                        <div class="text-[10px] text-stone-500 mb-2">Wind: {{ $hs['wind'] }}</div>
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold {{ $hs['color'] === 'emerald' ? 'bg-emerald-200 text-emerald-900' : ($hs['color'] === 'amber' ? 'bg-amber-200 text-amber-900' : 'bg-rose-200 text-rose-900') }}">
                            {{ $hs['status'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 7-Day Extended Agro-Climatic Outlook Table -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-bold text-stone-900 flex items-center gap-2">
                        <span>📅</span> 7-Day Gujarat Agro Zone Outlook
                    </h2>
                    <p class="text-xs text-stone-500 mt-0.5">
                        Synchronized with regional agricultural university crop protection schedules.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 text-stone-700 font-bold uppercase text-[10px] tracking-wider border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-4">Forecast Day</th>
                            <th class="py-3 px-4">Weather Condition</th>
                            <th class="py-3 px-4">Temp Range</th>
                            <th class="py-3 px-4">Rain Probability</th>
                            <th class="py-3 px-4">Avg Wind</th>
                            <th class="py-3 px-4 text-right">Spray Advisory</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($weather->forecast_json ?? [] as $fc)
                            <tr class="hover:bg-stone-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-stone-900">
                                    {{ $fc['day'] }}
                                </td>
                                <td class="py-3.5 px-4 flex items-center gap-2">
                                    <span class="text-lg">{{ $fc['icon'] }}</span>
                                    <span class="font-medium text-stone-800">Clear Sunshine</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-stone-900">
                                    {{ $fc['temp'] }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-blue-700 font-semibold">{{ $fc['pop'] ?? '5%' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-stone-600">
                                    {{ $fc['wind'] ?? '10 km/h' }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ ($fc['spray'] ?? '') === 'Optimal' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-stone-100 text-stone-700' }}">
                                        {{ $fc['spray'] ?? 'Normal' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Agronomic Pathology Alerts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                <div class="flex items-center gap-2 text-rose-800 font-bold text-sm mb-2">
                    <span>⚠️</span> Micro-Climate Crop Pathology Alert
                </div>
                <p class="text-xs text-stone-600 leading-relaxed mb-4">
                    High relative humidity (>60%) combined with evening temperature drops creates favorable spores germination conditions for <strong>Early Blight in Potato</strong> and <strong>Sigatoka Leaf Spot in Banana</strong> in Central and North Gujarat.
                </p>
                <a href="{{ route('advisory.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 hover:text-emerald-950">
                    Submit Crop Photos for AI Diagnosis →
                </a>
            </div>

            <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                <div class="flex items-center gap-2 text-emerald-800 font-bold text-sm mb-2">
                    <span>💡</span> Irrigation Management Suggestion
                </div>
                <p class="text-xs text-stone-600 leading-relaxed mb-4">
                    Root soil moisture in {{ $weather->district }} is currently at {{ $weather->soil_moisture }}. With low rainfall probability ({{ $weather->rainfall_probability }}%), maintain scheduled drip intervals for cotton and horticulture crops.
                </p>
                <a href="{{ route('products.index', ['category' => 'irrigation-tools']) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 hover:text-emerald-950">
                    Find Certified Drip Irrigation Stockists →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
