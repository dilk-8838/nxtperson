@extends('layouts.app')

@section('title', 'AI Advisory & Digital Farmer Platform - Nxtperson')
@section('meta_description', 'Nxtperson\'s Digital Farmer Platform empowering smallholders with localized crop advisories, AI visual leaf diagnostics, expert human escalation, and APMC mandi benchmarks.')

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
                    <span class="text-emerald-800 font-semibold">Digital Farmer Platform</span>
                </nav>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-900 border border-emerald-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    <span>Interactive Demonstration Tools Live</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 sm:py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full border border-emerald-200">
                        Strategic Solution Pillar 3
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-black text-stone-900 tracking-tight leading-tight">
                        AI Advisory & Digital Farmer Platform
                    </h1>
                    <p class="text-lg sm:text-xl font-bold text-emerald-900">
                        Farmer-First Agronomic Intelligence in Regional Languages
                    </p>
                    <p class="text-stone-600 text-sm sm:text-base leading-relaxed">
                        Indian agriculture is predominantly driven by small and marginal landholdings where access to timely, scientific crop advice is constrained by distance and language barriers. Nxtperson's Digital Farmer Platform bridges this gap with regional-language crop intelligence, computer-vision diagnostic scans, and direct pathways to certified agronomists.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-3">
                        <a href="{{ route('advisory.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <span>🌱</span>
                            <span>Open Crop Advisory Demo</span>
                        </a>
                        <a href="{{ route('advisory.upload') }}" class="px-5 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-300 font-bold text-sm shadow-xs transition-colors flex items-center gap-2">
                            <span>📸</span>
                            <span>Upload Crop Disease Photo</span>
                        </a>
                        <a href="{{ route('advisory.expert') }}" class="px-5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold text-sm transition-colors flex items-center gap-2">
                            <span>👨‍🌾</span>
                            <span>Talk to Agri Expert</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="p-6 rounded-3xl bg-stone-100 border border-stone-200 shadow-md space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-stone-200 text-xs">
                            <span class="font-bold text-stone-800">Platform Features In This Demo</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">Operational</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <span class="font-bold text-stone-900 block">Multilingual Support</span>
                                <span class="text-stone-500">Instant toggle across English, Hindi, and Gujarati</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <span class="font-bold text-stone-900 block">8 Gujarat Crop Advisory Protocols</span>
                                <span class="text-stone-500">Cotton, Banana, Potato, Rice, Wheat, Tomato, Cauliflower, Chilli</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <span class="font-bold text-stone-900 block">Interactive Disease Diagnosis Engine</span>
                                <span class="text-stone-500">Pest identification, bio-intervention guidance, confidence rating</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-stone-200">
                                <span class="font-bold text-stone-900 block">Agronomist Escalation Workflow</span>
                                <span class="text-stone-500">Simulated ticket escalation for complex field infestations</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Modules -->
    <section class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-2xl mx-auto text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full">
                    Digital Capabilities
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3 mb-3">
                    Empowering Every Farmer with Decision Support
                </h2>
                <p class="text-sm sm:text-base text-stone-600">
                    Accessible on low-bandwidth mobile networks, designed with high contrast and intuitive iconography.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl mb-4">
                        📸
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">Visual AI Disease Scanning</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                        Farmers capture leaf or stem photos directly in the field. The model cross-references symptoms against disease libraries (e.g. Pink Bollworm, Late Blight, Leaf Curl).
                    </p>
                    <a href="{{ route('advisory.upload') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                        Try Photo Upload Demo →
                    </a>
                </div>

                <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-sky-100 text-sky-800 flex items-center justify-center text-2xl mb-4">
                        👨‍🌾
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">Senior Agronomist Advisory</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                        When AI diagnostic confidence is below critical threshold or multiple stressors co-occur, queries are routed to senior agronomists for verified prescription.
                    </p>
                    <a href="{{ route('advisory.expert') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                        View Expert Consultation Desk →
                    </a>
                </div>

                <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-2xs">
                    <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-2xl mb-4">
                        📊
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">APMC Price Discovery</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                        Daily modal and arrival data from prominent Gujarat market yards (Rajkot, Gondal, Unjha, Deesa, Anand) so farmers choose the optimal day and market yard to sell.
                    </p>
                    <a href="{{ route('mandi.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                        View APMC Price Yard →
                    </a>
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
