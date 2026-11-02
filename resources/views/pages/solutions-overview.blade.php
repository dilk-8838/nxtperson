@extends('layouts.app')

@section('title', 'Four Core Solution Pillars - Nxtperson Private Limited')
@section('meta_description', 'Explore Nxtperson Private Limited\'s four strategic solution pillars: AI Quality Assurance, Precision Agriculture with IoT, Digital Farmer Platform, and Integrated Agricultural Marketplace.')

@section('content')
<div class="bg-stone-50 text-stone-900 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Top Breadcrumb & Corporate Header -->
    <header class="border-b border-stone-200/80 bg-white py-4">
        <div class="agri-container">
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <nav class="flex items-center gap-2 text-stone-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-emerald-800 font-semibold">Solutions</span>
                </nav>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    <span>Four Core Strategic Pillars</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 sm:py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider text-[#00875a] bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-200">
                    Nxtperson Architecture
                </span>
                <h1 class="text-3xl sm:text-5xl font-black text-[#0c365b] tracking-tight mt-3 mb-4 leading-tight">
                    Four Pillars of the Integrated Agri-Ecosystem
                </h1>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Nxtperson Private Limited is developing an end-to-end digital agricultural value chain designed to connect farmers directly with inputs, advisory, precision telemetry, farm-gate produce grading, and institutional buyers.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#pillars-grid" class="px-6 py-3 rounded-full bg-[#0c365b] hover:bg-[#071f37] text-white font-bold text-sm shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Explore the 4 Pillars ↓
                    </a>
                    <a href="#workflow" class="px-6 py-3 rounded-full bg-white hover:bg-slate-50 text-[#0c365b] border border-slate-300 hover:border-[#0c365b] font-bold text-sm shadow-xs transition-colors">
                        8-Stage Value Chain Flow →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Core Pillars Grid -->
    <section id="pillars-grid" class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                    The Four Solution Pillars
                </h2>
                <p class="text-sm sm:text-base text-stone-600 mt-2">
                    Every solution targets systemic bottlenecks in the agricultural supply chain from pre-sowing decisions to final market realization.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Pillar 1: AI Quality Assurance -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200/90 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center text-3xl">
                                🔬
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                Planned Roadmap Capability
                            </span>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Pillar 1</span>
                        <h3 class="text-xl sm:text-2xl font-black text-stone-900 mt-1 mb-3">
                            AI-Powered Quality Assurance & Farm-Gate Grading
                        </h3>
                        <p class="text-sm text-stone-600 leading-relaxed mb-4">
                            Targeting India's estimated ₹90,000 Cr annual post-harvest value leakage caused by subjective grading, poor sorting, and delayed assessments. Using computer vision and AI inspection at the farm gate, produce is categorized into objective quality tiers (A/B/C) with QR traceability for fair price realization.
                        </p>
                        <ul class="space-y-2 text-xs text-stone-700 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Computer vision defect, size, shape & color scanning
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Instant farm-gate digital grading certificates
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Transparent linkage to APMC and institutional buyer pricing
                            </li>
                        </ul>
                    </div>
                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                        <a href="{{ route('solutions.quality-grading') }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950 flex items-center gap-1.5">
                            <span>Read Grading Architecture</span>
                            <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                        <span class="text-xs text-stone-400">Phase 1 MVP Architecture</span>
                    </div>
                </div>

                <!-- Pillar 2: Precision Agriculture -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200/90 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-sky-50 text-sky-800 border border-sky-200 flex items-center justify-center text-3xl">
                                📡
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-200">
                                Interactive Demo Telemetry Live
                            </span>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider text-sky-800">Pillar 2</span>
                        <h3 class="text-xl sm:text-2xl font-black text-stone-900 mt-1 mb-3">
                            Precision Agriculture with IoT & Smart Irrigation
                        </h3>
                        <p class="text-sm text-stone-600 leading-relaxed mb-4">
                            Combines satellite remote sensing (NDVI canopy vigour) with hyper-local micro-weather models to reduce water consumption by up to 30% and eliminate input waste. The planned architecture pairs physical soil sensors with smart irrigation telemetry across Gujarat agro-climatic zones.
                        </p>
                        <ul class="space-y-2 text-xs text-stone-700 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Sentinel-2 NDVI spectral canopy stress detection
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Localized spray windows and weather alerts (Demo Live)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Planned IoT soil moisture, humidity & automated valve telemetry
                            </li>
                        </ul>
                    </div>
                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                        <a href="{{ route('solutions.precision-agri') }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950 flex items-center gap-1.5">
                            <span>Read Precision Agri Blueprint</span>
                            <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                        <span class="text-xs text-stone-400">Weather & NDVI Demo Ready</span>
                    </div>
                </div>

                <!-- Pillar 3: AI Advisory & Digital Farmer Platform -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200/90 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center text-3xl">
                                🌱
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-200">
                                Interactive Demo Workflows Live
                            </span>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Pillar 3</span>
                        <h3 class="text-xl sm:text-2xl font-black text-stone-900 mt-1 mb-3">
                            AI Advisory & Digital Farmer Platform
                        </h3>
                        <p class="text-sm text-stone-600 leading-relaxed mb-4">
                            Delivers actionable crop agronomics tailored for 8 key commodities across Gujarat in Gujarati, Hindi, and English. Features computer vision pest/disease leaf diagnosis, human expert escalation tickets, and personalized stage-by-stage crop protocols.
                        </p>
                        <ul class="space-y-2 text-xs text-stone-700 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Multilingual crop advisory form & ticket tracking (Demo Live)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Simulated AI leaf disease & pest diagnosis engine (Demo Live)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Senior agronomist escalation pathway (Demo Live)
                            </li>
                        </ul>
                    </div>
                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                        <a href="{{ route('solutions.digital-farmer') }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950 flex items-center gap-1.5">
                            <span>Read Advisory System</span>
                            <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                        <span class="text-xs text-stone-400">Advisory Tool Live</span>
                    </div>
                </div>

                <!-- Pillar 4: Integrated Agricultural Marketplace -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200/90 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="h-14 w-14 rounded-2xl bg-purple-50 text-purple-800 border border-purple-200 flex items-center justify-center text-3xl">
                                🛒
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                Inputs Live • Produce Trading Planned
                            </span>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-800">Pillar 4</span>
                        <h3 class="text-xl sm:text-2xl font-black text-stone-900 mt-1 mb-3">
                            Integrated Digital Agricultural Marketplace
                        </h3>
                        <p class="text-sm text-stone-600 leading-relaxed mb-4">
                            A two-sided agricultural ecosystem connecting farmers, FPOs, and cooperatives with verified input manufacturers on one end, and institutional produce buyers, food processors, and exporters on the other. Includes cold chain and fintech integration pathways.
                        </p>
                        <ul class="space-y-2 text-xs text-stone-700 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600">✓</span> Input catalog & 6-digit pincode distributor finder (Demo Live)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-amber-600">⏳</span> Produce listing & FPO supply aggregation (Planned Roadmap)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-amber-600">⏳</span> Institutional buyer discovery & contract escrow (Planned Roadmap)
                            </li>
                        </ul>
                    </div>
                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                        <a href="{{ route('solutions.marketplace') }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950 flex items-center gap-1.5">
                            <span>Read Marketplace Architecture</span>
                            <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                        <span class="text-xs text-stone-400">Input Directory Live</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8-Stage Value Chain Workflow Section -->
    <section id="workflow" class="py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full">
                    End-to-End Lifecycle
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3 mb-3">
                    How Nxtperson Works: The 8-Stage Agricultural Journey
                </h2>
                <p class="text-sm sm:text-base text-stone-600 leading-relaxed">
                    Based on Nxtperson's comprehensive business document, the platform organizes agricultural operations into three distinct phases across eight sequential value-creation stages.
                </p>
            </div>

            <div class="space-y-10">
                <!-- Phase 1: Registration & Assessment -->
                <div class="bg-stone-50 rounded-3xl p-6 sm:p-8 border border-stone-200">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-stone-200">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-700">Phase 1</span>
                            <h3 class="text-lg sm:text-xl font-black text-stone-900">Registration & Assessment</h3>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                            Onboarding & Profiling
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Stage 1 -->
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="h-8 w-8 rounded-xl bg-emerald-700 text-white font-black text-sm flex items-center justify-center">1</span>
                                <h4 class="font-bold text-stone-900">Farmer Registration</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-3">
                                Digital onboarding via mobile OTP, district/taluka profiling, crop selection, and plot boundary definition.
                            </p>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                <span>✓</span> Implemented in Demo (Farmer Dashboard)
                            </span>
                        </div>

                        <!-- Stage 2 -->
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="h-8 w-8 rounded-xl bg-emerald-700 text-white font-black text-sm flex items-center justify-center">2</span>
                                <h4 class="font-bold text-stone-900">Farm Assessment</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-3">
                                Historical soil profiling, baseline satellite vegetation index, and environmental risk evaluation.
                            </p>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-700">
                                <span>⚡</span> Demonstration Field Telemetry Available
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Phase 2: Precision Farming & Digital Services -->
                <div class="bg-stone-50 rounded-3xl p-6 sm:p-8 border border-stone-200">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-stone-200">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-sky-700">Phase 2</span>
                            <h3 class="text-lg sm:text-xl font-black text-stone-900">Precision Farming & Digital Services</h3>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-sky-100 text-sky-800">
                            Growing Season Operations
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Stage 3 -->
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="h-8 w-8 rounded-xl bg-sky-700 text-white font-black text-sm flex items-center justify-center">3</span>
                                <h4 class="font-bold text-stone-900">Precision Agriculture</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-3">
                                Micro-climate telemetry, 7-day spray guidance, satellite NDVI vegetative health, and water optimization.
                            </p>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                <span>✓</span> Implemented in Demo (Weather & NDVI Dashboards)
                            </span>
                        </div>

                        <!-- Stage 4 -->
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="h-8 w-8 rounded-xl bg-sky-700 text-white font-black text-sm flex items-center justify-center">4</span>
                                <h4 class="font-bold text-stone-900">Digital Advisory</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-3">
                                Multilingual disease/pest diagnosis from crop leaf photos with human agronomist escalation workflows.
                            </p>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                <span>✓</span> Implemented in Demo (Crop Advisory & Photo Upload)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Phase 3: Market & Finance Access -->
                <div class="bg-stone-50 rounded-3xl p-6 sm:p-8 border border-stone-200">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-stone-200">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-purple-700">Phase 3</span>
                            <h3 class="text-lg sm:text-xl font-black text-stone-900">Market & Finance Access</h3>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-100 text-purple-800">
                            Harvest, Trading & Settlement
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Stage 5 -->
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="h-7 w-7 rounded-lg bg-purple-700 text-white font-black text-xs flex items-center justify-center">5</span>
                                <h4 class="font-bold text-stone-900 text-sm">Harvest Quality Assessment</h4>
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed mb-3">
                                Farm-gate AI camera inspection for size, color, defect percentage, and grade assignment.
                            </p>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700">
                                ⏳ Planned Roadmap (MVP Phase)
                            </span>
                        </div>

                        <!-- Stage 6 -->
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="h-7 w-7 rounded-lg bg-purple-700 text-white font-black text-xs flex items-center justify-center">6</span>
                                <h4 class="font-bold text-stone-900 text-sm">Marketplace Access</h4>
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed mb-3">
                                Direct connection with agricultural input stockists and institutional produce buyers.
                            </p>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700">
                                ✓ Input Catalog Live • Produce Planned
                            </span>
                        </div>

                        <!-- Stage 7 -->
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="h-7 w-7 rounded-lg bg-purple-700 text-white font-black text-xs flex items-center justify-center">7</span>
                                <h4 class="font-bold text-stone-900 text-sm">Logistics & Finance</h4>
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed mb-3">
                                Integration with cold chain warehouses, freight logistics, and agri-credit providers.
                            </p>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-stone-500">
                                ⏳ Planned 2028-2029 Phase
                            </span>
                        </div>

                        <!-- Stage 8 -->
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="h-7 w-7 rounded-lg bg-purple-700 text-white font-black text-xs flex items-center justify-center">8</span>
                                <h4 class="font-bold text-stone-900 text-sm">Buyer & Export Corridors</h4>
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed mb-3">
                                Direct procurement contracts for processors, exporters, and retail chains.
                            </p>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-stone-500">
                                ⏳ Planned 2030-2031 Phase
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-12 bg-emerald-800 text-white">
        <div class="agri-container text-center max-w-2xl">
            <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-3">
                Experience the Demonstration Platform
            </h3>
            <p class="text-emerald-100 text-sm leading-relaxed mb-6">
                Test the operational farmer advisory, micro-weather advisories, APMC market yard rates, and agricultural input discovery.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('advisory.index') }}" class="px-6 py-3 rounded-xl bg-white text-emerald-900 font-bold text-sm shadow-md hover:bg-stone-100 transition-colors">
                    Try Crop Advisory Demo
                </a>
                <a href="{{ route('marketplace.index') }}" class="px-6 py-3 rounded-xl bg-emerald-900 hover:bg-emerald-950 text-white font-bold text-sm border border-emerald-700 transition-colors">
                    Explore Marketplace
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
