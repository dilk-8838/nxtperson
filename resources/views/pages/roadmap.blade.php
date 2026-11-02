@extends('layouts.app')

@section('title', 'Company Planned Roadmap (2026–2031) - Nxtperson Private Limited')
@section('meta_description', 'Explore Nxtperson Private Limited\'s planned strategic roadmap from Ahmedabad & North Gujarat MVP foundation (2026-27) to pan-India scale (2030) and global trade corridors (2031).')

@section('content')
<div class="bg-stone-50 text-stone-900 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Top Breadcrumb & Corporate Header -->
    <header class="border-b border-stone-200/80 bg-white py-4">
        <div class="agri-container">
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <nav class="flex items-center gap-2 text-stone-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-emerald-800 font-semibold">Planned Roadmap</span>
                </nav>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    <span>Strategic Phased Development Plan</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 sm:py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full border border-emerald-200">
                    Nxtperson Private Limited
                </span>
                <h1 class="text-3xl sm:text-5xl font-black text-[#0c365b] tracking-tight mt-3 mb-4 leading-tight">
                    Planned Company Roadmap (2026–2031)
                </h1>
                <p class="text-base sm:text-lg text-stone-600 leading-relaxed">
                    A measured, phased trajectory designed to establish verified technology foundations in Gujarat before expanding regionally across Western India, scaling nationwide, and building international agricultural export corridors.
                </p>
                <div class="mt-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                    <strong class="font-bold flex items-center gap-1.5 mb-0.5">
                        <span>🛡️</span> Strategic Roadmap Labeling Notice:
                    </strong>
                    The milestones below represent Nxtperson Private Limited's official planned roadmap as set forth in the corporate business document. They represent projected development targets and strategic milestones, not achieved historical results.
                </div>
            </div>
        </div>
    </section>

    <!-- Phased Timeline Section (5 Key Phases) -->
    <section class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="space-y-8 max-w-4xl mx-auto">

                <!-- Phase 1: 2026-27 Foundation & MVP -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-emerald-600/60 shadow-sm relative overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-xl bg-emerald-800 text-white font-black text-sm tracking-wide">
                                2026–2027
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-stone-900">Phase 1: Foundation & MVP</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                            Current Active Phase
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm text-stone-600">
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-emerald-700">📍</span> Geographic Scope
                            </h4>
                            <p class="leading-relaxed">
                                Initial technology pilots and farmer onboarding focused primarily in <strong>Ahmedabad and North Gujarat</strong> (Mehsana, Sabarkantha, Banaskantha potato belt), extending into Saurashtra (Rajkot cotton cluster).
                            </p>
                        </div>
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-emerald-700">⚡</span> Core Deliverables
                            </h4>
                            <ul class="space-y-1.5 leading-relaxed">
                                <li>• Core mobile and web platform launch (English, Gujarati, Hindi)</li>
                                <li>• Prototype AI farm-gate grading algorithms for 3 primary crops</li>
                                <li>• IoT smart irrigation telemetry pilots with select progressive farmers</li>
                                <li>• Digital input directory with local distributor discovery by pincode</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Phase 2: 2028 Commercial Launch -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-xl bg-stone-800 text-white font-black text-sm tracking-wide">
                                2028
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-stone-900">Phase 2: Commercial Launch</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-stone-100 text-stone-700 border border-stone-200 text-xs font-bold">
                            Planned Target
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm text-stone-600">
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-stone-700">📍</span> Geographic Scope
                            </h4>
                            <p class="leading-relaxed">
                                Comprehensive rollout across all 4 agro-climatic zones of <strong>Gujarat</strong>: Saurashtra, North Gujarat, Central Gujarat (Charotar belt), and South Gujarat.
                            </p>
                        </div>
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-stone-700">⚡</span> Core Deliverables
                            </h4>
                            <ul class="space-y-1.5 leading-relaxed">
                                <li>• Commercial deployment of farm-gate AI grading certification</li>
                                <li>• Launch of produce listing marketplace for FPOs and bulk buyers</li>
                                <li>• Onboarding 50+ verified input manufacturers & brand partners</li>
                                <li>• Deep integration with major Gujarat APMC market yards</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Phase 3: 2029 Regional Expansion -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-xl bg-stone-800 text-white font-black text-sm tracking-wide">
                                2029
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-stone-900">Phase 3: Regional Expansion</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-stone-100 text-stone-700 border border-stone-200 text-xs font-bold">
                            Planned Target
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm text-stone-600">
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-stone-700">📍</span> Geographic Scope
                            </h4>
                            <p class="leading-relaxed">
                                Expansion into contiguous Western Indian agricultural corridors: <strong>Maharashtra, Rajasthan, and Madhya Pradesh</strong>.
                            </p>
                        </div>
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-stone-700">⚡</span> Core Deliverables
                            </h4>
                            <ul class="space-y-1.5 leading-relaxed">
                                <li>• Cold storage and refrigerated logistics partner network integration</li>
                                <li>• Multi-state FPO federations aggregation</li>
                                <li>• Trade credit and warehouse receipt financing partnerships</li>
                                <li>• Expansion of crop intelligence protocols to 20+ regional commodities</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Phase 4: 2030 Pan-India Scale -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-xl bg-stone-800 text-white font-black text-sm tracking-wide">
                                2030
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-stone-900">Phase 4: Pan-India Scale</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-stone-100 text-stone-700 border border-stone-200 text-xs font-bold">
                            Planned Target
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm text-stone-600">
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-stone-700">📍</span> Geographic Scope
                            </h4>
                            <p class="leading-relaxed">
                                National coverage across all premier agricultural states in Northern, Southern, and Eastern India.
                            </p>
                        </div>
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-stone-700">⚡</span> Core Deliverables
                            </h4>
                            <ul class="space-y-1.5 leading-relaxed">
                                <li>• Full-scale digital agricultural marketplace linking 10,000+ FPOs</li>
                                <li>• Automated contract escrow with instant settlement</li>
                                <li>• Agronomic AI models covering 50+ staples, cash crops, and spices</li>
                                <li>• Seamless integration with India's AgriStack and digital public infrastructure</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Phase 5: 2031 Global Expansion -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-xl bg-stone-800 text-white font-black text-sm tracking-wide">
                                2031
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-stone-900">Phase 5: Global Expansion</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-stone-100 text-stone-700 border border-stone-200 text-xs font-bold">
                            Planned Vision
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm text-stone-600">
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-stone-700">📍</span> Geographic Scope
                            </h4>
                            <p class="leading-relaxed">
                                Cross-border agricultural export corridors connecting Indian farmer collectives directly to <strong>Middle East, Southeast Asia, and European buyers</strong>.
                            </p>
                        </div>
                        <div>
                            <h4 class="font-bold text-stone-900 mb-2 text-sm flex items-center gap-1.5">
                                <span class="text-stone-700">⚡</span> Core Deliverables
                            </h4>
                            <ul class="space-y-1.5 leading-relaxed">
                                <li>• International phytosanitary and QR traceability compliance</li>
                                <li>• Direct containerized shipments for high-value Gujarat produce</li>
                                <li>• Global commodity benchmarking and multi-currency settlement</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
@endsection
