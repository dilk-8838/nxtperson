@extends('layouts.app')

@section('title', 'AI-Powered Quality Assurance & Farm-Gate Grading - Nxtperson')
@section('meta_description', 'Nxtperson\'s planned AI computer-vision produce grading at the farm-gate, targeting India\'s ₹90,000 Cr annual post-harvest value leakage with objective quality assessment and QR traceability.')

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
                    <span class="text-emerald-800 font-semibold">Quality Assurance</span>
                </nav>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-900 border border-amber-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    <span>Planned Roadmap Capability • MVP Phase</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 sm:py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100/70 px-3 py-1 rounded-full border border-amber-200">
                        Strategic Solution Pillar 1
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-black text-stone-900 tracking-tight leading-tight">
                        AI-Powered Quality Assurance & Farm-Gate Grading
                    </h1>
                    <p class="text-lg sm:text-xl font-bold text-emerald-900">
                        Eliminating the ₹90,000 Cr Annual Post-Harvest Value Leakage
                    </p>
                    <p class="text-stone-600 text-sm sm:text-base leading-relaxed">
                        According to leading agricultural studies (including ICRIER and government value-chain audits), Indian farmers lose up to ₹90,000 Crore each year due to subjective manual grading, post-harvest mishandling, and lack of farm-gate quality certification. Nxtperson is architecting a computer vision grading model to bring transparent, objective grading directly to the farmer.
                    </p>
                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-900">
                        <strong class="font-bold flex items-center gap-1.5 mb-1">
                            <span>🛡️</span> Technical Transparency Notice:
                        </strong>
                        This module describes Nxtperson's planned farm-gate computer vision grading architecture. The current demonstration focuses on agronomic diagnostics, micro-weather, and input discovery; physical optical sorting and automated grading hardware are forward-looking roadmap capabilities.
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="rounded-3xl border border-stone-200 bg-stone-100 p-6 shadow-md relative overflow-hidden">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-stone-200">
                            <span class="text-xs font-bold text-stone-700 uppercase tracking-wider">Grading Conceptual Architecture</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-800">Planned Prototype</span>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <span class="text-stone-500 font-semibold block text-[10px]">Step 1: Farm-Gate Image Acquisition</span>
                                <span class="font-bold text-stone-900 block mt-0.5">High-resolution multi-angle camera scan of harvested lots</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <span class="text-stone-500 font-semibold block text-[10px]">Step 2: AI Neural Network Assessment</span>
                                <span class="font-bold text-stone-900 block mt-0.5">Size distribution, color uniformity, surface blemishes & moisture</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <span class="text-stone-500 font-semibold block text-[10px]">Step 3: Instant Grade Classification</span>
                                <span class="font-bold text-stone-900 block mt-0.5">Tier A (Premium Retail/Export), Tier B (APMC/Standard), Tier C (Processing)</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <span class="text-stone-500 font-semibold block text-[10px]">Step 4: QR-Code Traceability</span>
                                <span class="font-bold text-stone-900 block mt-0.5">Tamper-evident digital batch certificate linked to mandi pricing</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Deep-Dive Problem & Solution -->
    <section class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full">
                    Value Chain Impact
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3 mb-3">
                    Why Farm-Gate Grading Changes Everything
                </h2>
                <p class="text-sm sm:text-base text-stone-600">
                    Without objective quality verification at harvest, farmers are forced to accept price discounts at wholesale yards, regardless of true produce quality.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center text-2xl mb-4">
                        ⚠️
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">The Current Challenge</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Produce is visually inspected by middlemen with subjective criteria. Rejections and unilateral price cuts occur after the farmer has already incurred transport costs to the APMC yard.
                    </p>
                </div>

                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-2xl mb-4">
                        🔍
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">The Nxtperson Solution</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Computer vision models evaluate produce on-farm before dispatch. Lots are sorted and matched to appropriate buyers—premium export, supermarket retail, or industrial processing.
                    </p>
                </div>

                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl mb-4">
                        📈
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">The Planned Outcome</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Farmers capture 15% to 25% higher realization on Grade-A lots, buyers receive standardized batches with verified quality specs, and transit spoilage is dramatically reduced.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Technical Components (Planned Architecture) -->
    <section class="py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mb-8">
                Key Technical Components in the Planned Roadmap
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-2xl mb-3 block">📸</span>
                    <h4 class="font-bold text-stone-900 text-sm mb-1">Optical Inspection</h4>
                    <p class="text-xs text-stone-600 leading-relaxed">
                        Mobile app and portable camera integration analyzing color palettes, surface damage, and geometric dimensions.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-2xl mb-3 block">🏷️</span>
                    <h4 class="font-bold text-stone-900 text-sm mb-1">QR Batch Certification</h4>
                    <p class="text-xs text-stone-600 leading-relaxed">
                        Digital passports embedded on crates and bags carrying harvest date, farmer ID, moisture, and certified grade.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-2xl mb-3 block">⚖️</span>
                    <h4 class="font-bold text-stone-900 text-sm mb-1">Price Benchmarking</h4>
                    <p class="text-xs text-stone-600 leading-relaxed">
                        Dynamic linkage to APMC modal rates, ensuring grade premiums are reflected automatically in buyer offers.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-2xl mb-3 block">🚚</span>
                    <h4 class="font-bold text-stone-900 text-sm mb-1">Cold Chain Routing</h4>
                    <p class="text-xs text-stone-600 leading-relaxed">
                        Direct assignment to refrigerated logistics or local aggregation hubs based on post-harvest shelf-life projections.
                    </p>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-stone-200 flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('solutions.index') }}" class="text-xs font-bold text-stone-600 hover:text-emerald-700">
                    ← Back to All 4 Solutions
                </a>
                <a href="{{ route('roadmap.index') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950">
                    View 2026-2031 Deployment Roadmap →
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
