@extends('layouts.app')

@section('title', 'Precision Agriculture with IoT & Smart Irrigation - Nxtperson')
@section('meta_description', 'Nxtperson\'s precision agriculture vision combining satellite remote sensing, micro-weather analytics, and planned IoT smart irrigation sensors to optimize water usage and field health.')

@section('content')
<div class="bg-stone-50 text-stone-900 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Top Breadcrumb & Architecture Header -->
    <header class="border-b border-stone-200/80 bg-white py-4">
        <div class="agri-container">
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <nav class="flex items-center gap-2 text-stone-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
                    <span class="text-stone-300">/</span>
                    <a href="{{ route('solutions.index') }}" class="hover:text-emerald-700 transition-colors">Solutions</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-emerald-800 font-semibold">Precision Agriculture</span>
                </nav>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-900 border border-sky-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                    <span>Interactive Telemetry Demo Live • Hardware IoT Planned</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 sm:py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-sky-800 bg-sky-100/70 px-3 py-1 rounded-full border border-sky-200">
                        Strategic Solution Pillar 2
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-black text-stone-900 tracking-tight leading-tight">
                        Precision Agriculture & Smart Irrigation
                    </h1>
                    <p class="text-lg sm:text-xl font-bold text-emerald-900">
                        Up to 30% Water Savings & Real-Time Micro-Climate Telemetry
                    </p>
                    <p class="text-stone-600 text-sm sm:text-base leading-relaxed">
                        Water scarcity, declining water tables in North Gujarat and Saurashtra, and erratic climate patterns make resource optimization essential for sustainable yields. Nxtperson's precision agriculture architecture unites satellite vegetation spectral indexes with hyper-local weather alerts and planned IoT in-ground sensors.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-3">
                        <a href="{{ route('weather.index') }}" class="px-5 py-2.5 rounded-xl bg-sky-700 hover:bg-sky-800 text-white font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <span>🌦️</span>
                            <span>Explore Live Weather Demo</span>
                        </a>
                        <a href="{{ route('satellite.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <span>🛰️</span>
                            <span>Explore Satellite NDVI Demo</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="p-6 rounded-3xl bg-stone-100 border border-stone-200 shadow-md space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-stone-200 text-xs">
                            <span class="font-bold text-stone-800">Precision Architecture Components</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">Status Matrix</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="p-3 rounded-xl bg-white border border-stone-200 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-stone-900 block">Sentinel-2 NDVI Canopy Analysis</span>
                                    <span class="text-[10px] text-stone-500">Vegetation stress & vigour zoning</span>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold text-[10px]">Demo Available</span>
                            </div>

                            <div class="p-3 rounded-xl bg-white border border-stone-200 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-stone-900 block">7-Day Spray Window Forecasting</span>
                                    <span class="text-[10px] text-stone-500">Wind, rain risk, humidity telemetry</span>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold text-[10px]">Demo Available</span>
                            </div>

                            <div class="p-3 rounded-xl bg-white border border-stone-200 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-stone-900 block">IoT Soil Moisture & Salinity Probes</span>
                                    <span class="text-[10px] text-stone-500">Subsurface root-zone telemetry</span>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-bold text-[10px]">Planned Hardware</span>
                            </div>

                            <div class="p-3 rounded-xl bg-white border border-stone-200 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-stone-900 block">Automated Drip Solenoid Valves</span>
                                    <span class="text-[10px] text-stone-500">Threshold-driven smart fertigation</span>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-bold text-[10px]">Planned Hardware</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-stone-500 italic text-center pt-2">
                            * Note: The live demonstration presents simulated satellite telemetry and seeded micro-weather; physical on-ground sensor installations are planned for 2026-27 pilots.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Key Modules in Precision Agriculture -->
    <section class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-800 bg-sky-100/70 px-3 py-1 rounded-full">
                    Technical Capabilities
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3 mb-3">
                    Precision Telemetry & Resource Optimization
                </h2>
                <p class="text-sm sm:text-base text-stone-600">
                    How data-driven sensing protects soil health, conserves groundwater, and prevents pesticide overspray.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center text-2xl mb-4">
                        🛰️
                    </div>
                    <h3 class="font-bold text-stone-900 text-base mb-2">Satellite Field Vigour</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Multispectral Sentinel-2 imagery computes NDVI indices across plots, pinpointing water stress and poor emergence before visual yellowing occurs.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl mb-4">
                        💧
                    </div>
                    <h3 class="font-bold text-stone-900 text-base mb-2">Smart Water Management</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Dynamic irrigation calculations based on evapotranspiration (ET), crop growth stage, and soil moisture prevent over-watering and root rot.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-2xl mb-4">
                        🌦️
                    </div>
                    <h3 class="font-bold text-stone-900 text-base mb-2">Micro-Climate Forecasting</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Taluka-level temperature, wind drift, and rainfall probability protect expensive biological spray applications from wash-off.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center text-2xl mb-4">
                        🧪
                    </div>
                    <h3 class="font-bold text-stone-900 text-base mb-2">Soil Health & NPK</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Planned sensor integration will benchmark nitrogen, phosphorus, and potassium levels against crop demand curves to optimize input spend.
                    </p>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('solutions.index') }}" class="text-xs font-bold text-stone-600 hover:text-emerald-700">
                    ← Back to All 4 Solution Pillars
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
