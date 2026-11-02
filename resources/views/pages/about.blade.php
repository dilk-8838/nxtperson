@extends('layouts.app')

@section('title', __('messages.nav.about_us') . ' - Nxtperson Private Limited')
@section('meta_description', 'About Nxtperson Private Limited: Building India\'s premier end-to-end digital agri-marketplace and value chain. Digitising Agriculture, Empowering Farmer.')

@section('content')
<div class="bg-white text-stone-800 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Top Editorial Header & Breadcrumb -->
    <header class="border-b border-stone-100 bg-stone-50/60 py-4">
        <div class="agri-container">
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <nav class="flex items-center gap-2 text-stone-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">{{ __('messages.nav.home') }}</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-emerald-800 font-semibold">{{ __('messages.about.breadcrumb') }}</span>
                </nav>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-medium">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ __('messages.about.badge') }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- SECTION 1: WHO WE ARE (Hero Story - Two-Column Editorial) -->
    <section class="py-12 sm:py-16 lg:py-20 bg-gradient-to-b from-stone-50/40 via-white to-white">
        <div class="agri-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                
                <!-- Left: Editorial Story & Introduction -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100/70 text-emerald-900 text-xs font-semibold tracking-wide uppercase">
                        <span>🌾</span> {{ __('messages.about.eyebrow_story') }}
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#0c365b] tracking-tight leading-[1.15]">
                        {{ __('messages.about.hero_title') }}
                    </h1>
                    <p class="text-lg sm:text-xl font-bold text-[#00875a] tracking-tight">
                        {{ __('messages.about.hero_subtitle') }}
                    </p>

                    <p class="text-base sm:text-lg text-slate-700 font-normal leading-relaxed">
                        {{ __('messages.about.hero_lead') }}
                    </p>

                    <div class="space-y-4 text-slate-600 text-sm sm:text-base leading-relaxed">
                        <p>
                            {{ __('messages.about.hero_p1') }}
                        </p>
                        <p>
                            {{ __('messages.about.hero_p2') }}
                        </p>
                    </div>

                    <!-- Trust Highlights Strip -->
                    <div class="pt-4 grid grid-cols-3 gap-4 border-t border-slate-200">
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight">{{ __('messages.about.stat_crops_num') }}</div>
                            <div class="text-xs text-slate-500 font-medium mt-0.5">{{ __('messages.about.stat_crops_label') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight">{{ __('messages.about.stat_languages_num') }}</div>
                            <div class="text-xs text-slate-500 font-medium mt-0.5">{{ __('messages.about.stat_languages_label') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight">{{ __('messages.about.stat_distributor_num') }}</div>
                            <div class="text-xs text-slate-500 font-medium mt-0.5">{{ __('messages.about.stat_distributor_label') }}</div>
                        </div>
                    </div>

                    <!-- Action Links -->
                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        <a href="{{ route('solutions.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm shadow-sm transition-all duration-200 hover:shadow">
                            <span>{{ __('messages.about.hero_cta_advisory') }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ route('roadmap.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold text-sm transition-colors">
                            <span>{{ __('messages.about.hero_cta_mandi') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Right: Warm, Human Farmer Story Photograph -->
                <div class="lg:col-span-5">
                    <div class="relative group">
                        <div class="absolute -inset-2 bg-gradient-to-tr from-emerald-100 via-amber-50 to-emerald-50 rounded-[2.2rem] filter blur-xl opacity-70 group-hover:opacity-90 transition-opacity"></div>
                        
                        <div class="relative rounded-3xl overflow-hidden border border-stone-200/90 bg-white shadow-xl">
                            <picture>
                                <source srcset="{{ asset('images/about/farmer_story.webp') }}" type="image/webp">
                                <img src="{{ asset('images/about/farmer_story.jpg') }}" 
                                     alt="{{ __('messages.about.hero_img_alt') }}" 
                                     class="w-full h-80 sm:h-96 lg:h-[460px] object-cover object-top group-hover:scale-[1.02] transition-transform duration-500 ease-out">
                            </picture>

                            <div class="p-5 bg-gradient-to-t from-stone-900/90 via-stone-900/60 to-transparent absolute inset-x-0 bottom-0 text-white">
                                <div class="inline-flex items-center gap-1.5 text-xs text-emerald-300 font-semibold mb-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                    <span>{{ __('messages.about.hero_img_tag') }}</span>
                                </div>
                                <p class="text-xs sm:text-sm text-stone-200 leading-snug">
                                    {{ __('messages.about.hero_img_caption') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION 2 & 3: VISION AND MISSION CALLOUT -->
    <section id="vision" class="py-14 bg-emerald-900 text-white">
        <div class="agri-container">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                <!-- Vision -->
                <div class="bg-emerald-800/70 p-8 rounded-3xl border border-emerald-700/80 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-300 block">Our Documented Vision</span>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Building India's Premier End-to-End Agri-Marketplace
                    </h2>
                    <p class="text-emerald-100 text-sm sm:text-base leading-relaxed">
                        To build India's premier end-to-end digital agri-marketplace and value chain, eliminating post-harvest waste and securing sustainable, data-driven food systems.
                    </p>
                </div>

                <!-- Mission -->
                <div class="bg-emerald-800/70 p-8 rounded-3xl border border-emerald-700/80 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-300 block">Our Documented Mission</span>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Productivity, Profitability & Prosperity for Every Farmer
                    </h2>
                    <p class="text-emerald-100 text-sm sm:text-base leading-relaxed">
                        To become India's most trusted agritech ecosystem driving agricultural productivity, profitability and prosperity for every farmer through connected intelligence and transparent markets.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: THE SIX AGRICULTURAL CHALLENGES WE ADDRESS (PDF Page 2) -->
    <section class="py-16 sm:py-20 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full">
                    {{ __('messages.about.challenges_eyebrow') }}
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3 mb-3">
                    {{ __('messages.about.challenges_title') }}
                </h2>
                <p class="text-sm sm:text-base text-stone-600">
                    {{ __('messages.about.challenges_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- C1 -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <span class="text-2xl mb-3 block">🧩</span>
                    <h3 class="font-bold text-stone-900 text-base mb-2">{{ __('messages.about.c1_title') }}</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">{{ __('messages.about.c1_desc') }}</p>
                </div>

                <!-- C2 -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <span class="text-2xl mb-3 block">🌦️</span>
                    <h3 class="font-bold text-stone-900 text-base mb-2">{{ __('messages.about.c2_title') }}</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">{{ __('messages.about.c2_desc') }}</p>
                </div>

                <!-- C3 -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <span class="text-2xl mb-3 block">💧</span>
                    <h3 class="font-bold text-stone-900 text-base mb-2">{{ __('messages.about.c3_title') }}</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">{{ __('messages.about.c3_desc') }}</p>
                </div>

                <!-- C4 -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <span class="text-2xl mb-3 block">📡</span>
                    <h3 class="font-bold text-stone-900 text-base mb-2">{{ __('messages.about.c4_title') }}</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">{{ __('messages.about.c4_desc') }}</p>
                </div>

                <!-- C5 -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <span class="text-2xl mb-3 block">⚖️</span>
                    <h3 class="font-bold text-stone-900 text-base mb-2">{{ __('messages.about.c5_title') }}</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">{{ __('messages.about.c5_desc') }}</p>
                </div>

                <!-- C6 -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs">
                    <span class="text-2xl mb-3 block">🔍</span>
                    <h3 class="font-bold text-stone-900 text-base mb-2">{{ __('messages.about.c6_title') }}</h3>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">{{ __('messages.about.c6_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 5: FOUR SOLUTION PILLARS SUMMARY -->
    <section class="py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full">
                    Strategic Architecture
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3 mb-2">
                    How Nxtperson Solves These Challenges
                </h2>
                <p class="text-sm sm:text-base text-stone-600">
                    Structured across four strategic solution pillars connecting the entire agricultural value chain.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Pillar 1</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-900">Roadmap</span>
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 mb-2">AI-Powered Quality Assurance & Farm-Gate Grading</h3>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                            Objective computer vision scanning reducing ₹90,000 Cr post-harvest losses with digital grading certificates and QR traceability.
                        </p>
                    </div>
                    <a href="{{ route('solutions.quality-grading') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950">
                        Explore Pillar 1 Blueprint →
                    </a>
                </div>

                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-sky-800 uppercase tracking-wider">Pillar 2</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Demo Live</span>
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 mb-2">Precision Agriculture with IoT & Smart Irrigation</h3>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                            Up to 30% water savings via Sentinel-2 NDVI canopy monitoring, 7-day spray windows, and planned IoT soil sensors.
                        </p>
                    </div>
                    <a href="{{ route('solutions.precision-agri') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950">
                        Explore Pillar 2 Blueprint →
                    </a>
                </div>

                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Pillar 3</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Demo Live</span>
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 mb-2">AI Advisory & Digital Farmer Platform</h3>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                            Multilingual crop advisory in Gujarati, Hindi, and English with visual leaf disease scanning and human agronomist escalation.
                        </p>
                    </div>
                    <a href="{{ route('solutions.digital-farmer') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950">
                        Explore Pillar 3 Blueprint →
                    </a>
                </div>

                <div class="p-6 rounded-3xl bg-stone-50 border border-stone-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-purple-800 uppercase tracking-wider">Pillar 4</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-900">Inputs Live</span>
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 mb-2">Integrated Digital Agricultural Marketplace</h3>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-4">
                            Two-sided marketplace connecting input manufacturers, dealers, FPOs, and institutional produce buyers into a single ecosystem.
                        </p>
                    </div>
                    <a href="{{ route('solutions.marketplace') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950">
                        Explore Pillar 4 Blueprint →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 6: LEADERSHIP & FOUNDING TEAM (PDF Document Verified) -->
    <section id="leadership" class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full">
                    {{ __('messages.about.leadership_eyebrow') }}
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3 mb-2">
                    {{ __('messages.about.leadership_title') }}
                </h2>
                <p class="text-sm sm:text-base text-stone-600">
                    {{ __('messages.about.leadership_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Snehal Talati -->
                <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="h-14 w-14 rounded-2xl bg-emerald-100 text-emerald-800 font-black text-xl flex items-center justify-center mb-4">
                            ST
                        </div>
                        <h3 class="text-lg font-bold text-stone-900">{{ __('messages.about.leader_1_name') }}</h3>
                        <p class="text-xs font-semibold text-emerald-800 mb-3">{{ __('messages.about.leader_1_role') }}</p>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                            {{ __('messages.about.leader_1_bio') }}
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-stone-100 text-[11px] text-stone-400">
                        Nxtperson Private Limited
                    </div>
                </div>

                <!-- Rajesh Talati -->
                <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="h-14 w-14 rounded-2xl bg-amber-100 text-amber-800 font-black text-xl flex items-center justify-center mb-4">
                            RT
                        </div>
                        <h3 class="text-lg font-bold text-stone-900">{{ __('messages.about.leader_2_name') }}</h3>
                        <p class="text-xs font-semibold text-amber-800 mb-3">{{ __('messages.about.leader_2_role') }}</p>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                            {{ __('messages.about.leader_2_bio') }}
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-stone-100 text-[11px] text-stone-400">
                        Nxtperson Private Limited
                    </div>
                </div>

                <!-- Karan Bhatt -->
                <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="h-14 w-14 rounded-2xl bg-purple-100 text-purple-800 font-black text-xl flex items-center justify-center mb-4">
                            KB
                        </div>
                        <h3 class="text-lg font-bold text-stone-900">{{ __('messages.about.leader_3_name') }}</h3>
                        <p class="text-xs font-semibold text-purple-800 mb-3">{{ __('messages.about.leader_3_role') }}</p>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                            {{ __('messages.about.leader_3_bio') }}
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-stone-100 text-[11px] text-stone-400">
                        Nxtperson Private Limited
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 7: PLANNED ROADMAP HIGHLIGHT -->
    <section class="py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full">
                        {{ __('messages.about.roadmap_eyebrow') }}
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-3">
                        {{ __('messages.about.roadmap_title') }}
                    </h2>
                    <p class="text-sm sm:text-base text-stone-600 mt-1">
                        {{ __('messages.about.roadmap_subtitle') }}
                    </p>
                </div>
                <a href="{{ route('roadmap.index') }}" class="px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs shadow-xs transition-colors whitespace-nowrap self-start md:self-auto">
                    View Full 2026–31 Roadmap →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <span class="font-extrabold text-emerald-900 block text-sm">2026–27</span>
                    <strong class="font-bold text-stone-900 block mt-1">Foundation & MVP</strong>
                    <span class="text-stone-600 block mt-1">Ahmedabad & North Gujarat pilots, core platform.</span>
                </div>

                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="font-extrabold text-stone-700 block text-sm">2028</span>
                    <strong class="font-bold text-stone-900 block mt-1">Commercial Launch</strong>
                    <span class="text-stone-600 block mt-1">All Gujarat agro zones, produce listing launch.</span>
                </div>

                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="font-extrabold text-stone-700 block text-sm">2029</span>
                    <strong class="font-bold text-stone-900 block mt-1">Regional Expansion</strong>
                    <span class="text-stone-600 block mt-1">Maharashtra, Rajasthan, MP corridors.</span>
                </div>

                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="font-extrabold text-stone-700 block text-sm">2030</span>
                    <strong class="font-bold text-stone-900 block mt-1">Pan-India Scale</strong>
                    <span class="text-stone-600 block mt-1">10,000+ FPOs, national marketplace scale.</span>
                </div>

                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="font-extrabold text-stone-700 block text-sm">2031</span>
                    <strong class="font-bold text-stone-900 block mt-1">Global Corridors</strong>
                    <span class="text-stone-600 block mt-1">Export linkages to Middle East & SE Asia.</span>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 8: COMMITMENT TO FARMERS & SUSTAINABILITY -->
    <section class="py-14 sm:py-20 bg-stone-50/60 border-t border-stone-200/70">
        <div class="agri-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                
                <!-- Left: Precision Agriculture Photograph -->
                <div class="lg:col-span-5 order-2 lg:order-1">
                    <div class="relative group">
                        <div class="rounded-3xl overflow-hidden border border-stone-200/90 bg-white shadow-xl">
                            <picture>
                                <source srcset="{{ asset('images/about/farm_vision.webp') }}" type="image/webp">
                                <img src="{{ asset('images/about/farm_vision.jpg') }}" 
                                     alt="{{ __('messages.about.vision_img_alt') }}" 
                                     class="w-full h-80 sm:h-96 lg:h-[420px] object-cover object-center group-hover:scale-[1.02] transition-transform duration-500 ease-out">
                            </picture>

                            <div class="p-5 bg-gradient-to-t from-stone-900/90 via-stone-900/50 to-transparent absolute inset-x-0 bottom-0 text-white">
                                <div class="text-xs text-emerald-300 font-semibold mb-0.5">
                                    {{ __('messages.about.vision_img_tag') }}
                                </div>
                                <p class="text-xs text-stone-200 leading-snug">
                                    {{ __('messages.about.vision_img_caption') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Vision Narrative -->
                <div class="lg:col-span-7 order-1 lg:order-2 space-y-6">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100/60 px-3 py-1 rounded-full">
                        {{ __('messages.about.commitment_eyebrow') }}
                    </span>

                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-stone-900 tracking-tight leading-tight">
                        {{ __('messages.about.commitment_title') }}
                    </h2>

                    <p class="text-stone-600 text-sm sm:text-base leading-relaxed">
                        {{ __('messages.about.commitment_p1') }}
                    </p>

                    <p class="text-stone-600 text-sm sm:text-base leading-relaxed">
                        {{ __('messages.about.commitment_p2') }}
                    </p>

                    <!-- Editorial Quote Block -->
                    <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-sm relative">
                        <span class="text-3xl text-emerald-400 font-serif leading-none absolute top-4 left-4 opacity-50">“</span>
                        <blockquote class="text-stone-800 text-sm sm:text-base font-medium italic pl-6 leading-relaxed">
                            {{ __('messages.about.vision_quote') }}
                        </blockquote>
                        <div class="mt-4 pt-3 border-t border-stone-100 pl-6 flex items-center justify-between text-xs text-stone-500">
                            <span class="font-bold text-stone-800">{{ __('messages.about.vision_author') }}</span>
                            <span class="text-emerald-700 font-medium">{{ __('messages.about.vision_location') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION 9: DEMONSTRATION PLATFORM TRANSPARENCY NOTICE -->
    <section class="py-12 sm:py-16 bg-white border-t border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-4xl mx-auto rounded-3xl bg-stone-50/90 border border-stone-200/90 p-8 sm:p-10 shadow-sm">
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-stone-200">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0">
                            🛡️
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-stone-900">{{ __('messages.about.demo_title') }}</h3>
                            <p class="text-xs text-stone-500">{{ __('messages.about.demo_subtitle') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                        {{ __('messages.simulated_tag') }}
                    </span>
                </div>

                <div class="pt-6 space-y-4 text-xs sm:text-sm text-stone-600 leading-relaxed">
                    <p>
                        {{ __('messages.about.demo_p1') }}
                    </p>
                    <p>
                        {{ __('messages.about.demo_p2') }}
                    </p>
                    <p class="text-stone-500 text-xs">
                        {{ __('messages.about.demo_p3') }}
                    </p>
                </div>

                <!-- Shortcuts to Explore Modules -->
                <div class="mt-6 pt-5 border-t border-stone-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="text-stone-500">
                        {{ __('messages.about.demo_explore') }}
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('advisory.index') }}" class="px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:border-emerald-500 text-stone-700 font-medium transition-colors">
                            {{ __('messages.about.demo_link_diagnostics') }}
                        </a>
                        <a href="{{ route('weather.index') }}" class="px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:border-emerald-500 text-stone-700 font-medium transition-colors">
                            {{ __('messages.about.demo_link_weather') }}
                        </a>
                        <a href="{{ route('mandi.index') }}" class="px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:border-emerald-500 text-stone-700 font-medium transition-colors">
                            {{ __('messages.about.demo_link_mandi') }}
                        </a>
                        <a href="{{ route('satellite.index') }}" class="px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:border-emerald-500 text-stone-700 font-medium transition-colors">
                            {{ __('messages.about.demo_link_satellite') }}
                        </a>
                        <a href="{{ route('distributors.index') }}" class="px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:border-emerald-500 text-stone-700 font-medium transition-colors">
                            {{ __('messages.about.demo_link_distributors') }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
@endsection
