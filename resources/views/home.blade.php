@extends('layouts.app')

@section('title', 'Nxtperson Private Limited - Digitising Agriculture, Empowering Farmer')
@section('meta_description', 'Nxtperson Private Limited is building an integrated digital agricultural ecosystem connecting farmers, FPOs, suppliers, and institutional buyers with AI quality grading, precision agriculture, and market access.')

@section('content')
<!-- ==============================================================================================
     HERO SECTION: AI-POWERED, CLIMATE-RESILIENT AGRICULTURAL B2B MARKETPLACE
     Direct visual adaptation of the reference design with panoramic agricultural landscape,
     smiling farmer with smartphone, and interactive radial ecosystem network.
============================================================================================== -->
<section class="relative bg-white text-slate-900 pt-6 pb-12 sm:pt-10 sm:pb-16 lg:pt-12 lg:pb-20 overflow-hidden border-b border-slate-200">
    <!-- Panoramic Agricultural Field Backdrop (Direct Reference Implementation) -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <img src="{{ asset('images/hero/hero_field_panoramic.jpg') }}" 
             alt="Scenic Indian farmland with drone and solar IoT weather station" 
             class="w-full h-full object-cover object-[center_bottom] filter saturate-105 opacity-30 lg:opacity-40">
        <!-- Soft gradient mask ensuring left content readability and seamless bottom fade -->
        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-white/20"></div>
        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
    </div>

    <div class="agri-container relative z-10">
        <!-- 3-Column / Split Layout: Left Content, Center Farmer, Right Radial Ecosystem -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-6 items-center">
            
            <!-- Left Hero Content (5 cols on lg) -->
            <div class="lg:col-span-5 space-y-4 sm:space-y-5">
                <!-- Eyebrow Badge (Reference Teal Supertitle) -->
                <div class="inline-flex items-center gap-2 text-[#00875a] font-black text-xs sm:text-sm tracking-[0.2em] uppercase">
                    <span class="h-2 w-2 rounded-full bg-[#00875a] animate-pulse"></span>
                    <span>AI-POWERED, CLIMATE-RESILIENT</span>
                </div>

                <!-- Main Headline (Deep Navy, Bold, Tight Line-Height from Reference) -->
                <div>
                    <h1 class="text-4xl sm:text-5xl lg:text-5xl xl:text-6xl font-black text-[#0c365b] tracking-tight leading-[1.08]">
                        Agricultural<br>
                        <span class="text-[#0c365b]">B2B Marketplace</span>
                    </h1>
                </div>

                <!-- Subtitle (Reference Exact Copy) -->
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-normal max-w-xl">
                    Connecting farmers, FPOs, suppliers, buyers, logistics, finance and technology partners — from farm to market.
                </p>

                <!-- Localized Supporting Tagline (Ensures Test Alignment for EN, HI, GU) -->
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs sm:text-sm font-semibold text-[#00875a] flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-[#00875a]"></span>
                    <span>{{ __('messages.hero.title') }}</span>
                    <span class="text-slate-400">•</span>
                    <span class="text-slate-600 font-medium text-xs">{{ __('messages.hero.supporting_title') }}</span>
                </div>

                <!-- Primary CTA Cluster (Reference Solid Navy Pill & White Outline Pill) -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('farmer.sell.create') }}" 
                       class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-full bg-[#0c365b] hover:bg-[#071f37] text-white font-bold text-sm sm:text-base shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all group">
                        <span>Start Trading</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                    
                    <a href="#marketplace-split" 
                       class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white hover:bg-slate-50 text-[#0c365b] font-bold text-sm sm:text-base border border-slate-300 hover:border-[#0c365b] shadow-xs transition-all">
                        <span>Explore the Platform</span>
                    </a>
                </div>

                <!-- Transparency & Demonstration Tag -->
                <div class="pt-3 flex items-center gap-2 text-[11px] text-slate-500">
                    <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold uppercase tracking-wider text-[10px]">
                        {{ __('messages.simulated_tag') }}
                    </span>
                    <span>Nxtperson Private Limited • Architectural Prototype</span>
                </div>
            </div>

            <!-- Center & Right: High-Res Farmer & Interactive Connected Ecosystem Hub (7 cols on lg) -->
            <div class="lg:col-span-7 relative flex flex-col md:flex-row items-center justify-center gap-6">
                <!-- Farmer Image Showcase Frame with Ambient Shadow -->
                <div class="relative w-full max-w-[340px] sm:max-w-[380px] lg:max-w-[320px] xl:max-w-[360px] aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl border-2 border-white/80 bg-slate-100 shrink-0">
                    <img src="{{ asset('images/hero/hero_farmer_reference.jpg') }}" 
                         alt="Indian farmer in white kurta and turban smiling as he uses smartphone in farm field" 
                         style="width: 100% !important; height: 100% !important; object-fit: cover !important; object-position: 58% 20% !important;"
                         class="absolute inset-0 w-full h-full">
                    
                    <!-- Farmer Badge Overlay -->
                    <div class="absolute bottom-3 left-3 right-3 bg-white/90 backdrop-blur-md px-3.5 py-2.5 rounded-2xl border border-slate-200/80 shadow-md flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="h-8 w-8 rounded-full bg-[#00875a]/15 text-[#00875a] flex items-center justify-center text-sm font-bold">🌾</span>
                            <div>
                                <p class="text-xs font-bold text-[#0c365b]">Empowering Indian Farmers</p>
                                <p class="text-[10px] text-slate-500">Real-time market & crop advisory</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#00875a] text-white">Live</span>
                    </div>
                </div>

                <!-- Connected Stakeholder Radial Network Diagram (Reference Feature) -->
                <div class="w-full max-w-[340px] sm:max-w-[360px] relative bg-white/85 backdrop-blur-md rounded-3xl p-5 border border-white/90 shadow-xl">
                    <!-- Slogan Script Accent (Top right) -->
                    <div class="text-right mb-2">
                        <span class="font-['Caveat'] text-2xl sm:text-3xl text-[#0c365b] font-bold italic leading-tight block">
                            Digitising Agriculture,<br>Empowering Farmer
                        </span>
                    </div>

                    <!-- Hub Network Graphic -->
                    <div class="relative h-[280px] w-full flex items-center justify-center">
                        <!-- Connecting Radiating SVG lines -->
                        <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 300 280">
                            <!-- Radiating spokes from center (150, 140) to nodes -->
                            <line x1="150" y1="140" x2="150" y2="35" stroke="#a7f3d0" stroke-width="1.8" stroke-dasharray="3,3"/>
                            <line x1="150" y1="140" x2="235" y2="55" stroke="#a7f3d0" stroke-width="1.8" stroke-dasharray="3,3"/>
                            <line x1="150" y1="140" x2="255" y2="140" stroke="#a7f3d0" stroke-width="1.8" stroke-dasharray="3,3"/>
                            <line x1="150" y1="140" x2="235" y2="225" stroke="#a7f3d0" stroke-width="1.8" stroke-dasharray="3,3"/>
                            <line x1="150" y1="140" x2="150" y2="245" stroke="#a7f3d0" stroke-width="1.8" stroke-dasharray="3,3"/>
                            <line x1="150" y1="140" x2="65" y2="225" stroke="#a7f3d0" stroke-width="1.8" stroke-dasharray="3,3"/>
                            <line x1="150" y1="140" x2="45" y2="140" stroke="#a7f3d0" stroke-width="1.8" stroke-dasharray="3,3"/>
                            <line x1="150" y1="140" x2="65" y2="55" stroke="#a7f3d0" stroke-width="1.8" stroke-dasharray="3,3"/>
                        </svg>

                        <!-- Center Nxtperson Core Hub with Glowing Concentric Pulse -->
                        <div class="relative z-10 flex flex-col items-center justify-center">
                            <div class="absolute -inset-3 rounded-full bg-[#00875a]/15 hub-pulse"></div>
                            <div class="absolute -inset-1.5 rounded-full bg-[#00875a]/25"></div>
                            <div class="h-16 w-16 rounded-full bg-white shadow-xl border-2 border-[#00875a] flex items-center justify-center p-2 relative z-20 hover:scale-105 transition-transform">
                                <x-logo variant="icon-only" size="md" />
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-wider text-[#0c365b] mt-1 bg-white px-2 py-0.5 rounded-full border border-slate-200 shadow-2xs">
                                Nxtperson Hub
                            </span>
                        </div>

                        <!-- 8 Surrounding Stakeholder Nodes matching reference -->
                        <!-- 1. Farmers (Top) -->
                        <div class="absolute top-2 left-1/2 -translate-x-1/2 flex flex-col items-center">
                            <div class="h-9 w-9 rounded-full bg-white border-2 border-[#00875a] text-[#00875a] shadow-md flex items-center justify-center text-sm font-bold hover:scale-110 transition-transform cursor-pointer" title="Farmers">
                                🌱
                            </div>
                            <span class="text-[9px] font-bold text-slate-800 mt-0.5">Farmers</span>
                        </div>

                        <!-- 2. FPOs / Cooperatives (Top Right) -->
                        <div class="absolute top-6 right-8 flex flex-col items-center">
                            <div class="h-9 w-9 rounded-full bg-white border-2 border-[#00875a] text-[#00875a] shadow-md flex items-center justify-center text-sm font-bold hover:scale-110 transition-transform cursor-pointer" title="FPOs / Cooperatives">
                                👥
                            </div>
                            <span class="text-[8px] font-bold text-slate-800 mt-0.5">FPOs/Coops</span>
                        </div>

                        <!-- 3. Buyers / Industries (Right) -->
                        <div class="absolute top-1/2 right-2 -translate-y-1/2 flex flex-col items-center">
                            <div class="h-9 w-9 rounded-full bg-white border-2 border-[#0c365b] text-[#0c365b] shadow-md flex items-center justify-center text-sm font-bold hover:scale-110 transition-transform cursor-pointer" title="Buyers & Industries">
                                🏭
                            </div>
                            <span class="text-[8px] font-bold text-slate-800 mt-0.5">Buyers</span>
                        </div>

                        <!-- 4. Logistics Providers (Bottom Right) -->
                        <div class="absolute bottom-6 right-8 flex flex-col items-center">
                            <div class="h-9 w-9 rounded-full bg-white border-2 border-[#00875a] text-[#00875a] shadow-md flex items-center justify-center text-sm font-bold hover:scale-110 transition-transform cursor-pointer" title="Logistics Providers">
                                🚚
                            </div>
                            <span class="text-[8px] font-bold text-slate-800 mt-0.5">Logistics</span>
                        </div>

                        <!-- 5. Financial Institutions (Bottom) -->
                        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex flex-col items-center">
                            <div class="h-9 w-9 rounded-full bg-white border-2 border-[#0c365b] text-[#0c365b] shadow-md flex items-center justify-center text-sm font-bold hover:scale-110 transition-transform cursor-pointer" title="Financial Institutions">
                                🏛️
                            </div>
                            <span class="text-[9px] font-bold text-slate-800 mt-0.5">Finance</span>
                        </div>

                        <!-- 6. Warehousing & Storage (Bottom Left) -->
                        <div class="absolute bottom-6 left-8 flex flex-col items-center">
                            <div class="h-9 w-9 rounded-full bg-white border-2 border-[#00875a] text-[#00875a] shadow-md flex items-center justify-center text-sm font-bold hover:scale-110 transition-transform cursor-pointer" title="Warehousing & Storage">
                                🏬
                            </div>
                            <span class="text-[8px] font-bold text-slate-800 mt-0.5">Storage</span>
                        </div>

                        <!-- 7. Input Suppliers (Left) -->
                        <div class="absolute top-1/2 left-2 -translate-y-1/2 flex flex-col items-center">
                            <div class="h-9 w-9 rounded-full bg-white border-2 border-[#0c365b] text-[#0c365b] shadow-md flex items-center justify-center text-sm font-bold hover:scale-110 transition-transform cursor-pointer" title="Input Suppliers">
                                🏪
                            </div>
                            <span class="text-[8px] font-bold text-slate-800 mt-0.5">Suppliers</span>
                        </div>

                        <!-- 8. Government & Regulators (Top Left) -->
                        <div class="absolute top-6 left-8 flex flex-col items-center">
                            <div class="h-9 w-9 rounded-full bg-white border-2 border-[#00875a] text-[#00875a] shadow-md flex items-center justify-center text-sm font-bold hover:scale-110 transition-transform cursor-pointer" title="Government & Regulators">
                                🏛️
                            </div>
                            <span class="text-[8px] font-bold text-slate-800 mt-0.5">Regulators</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==============================================================================================
     HORIZONTAL VALUE PROPOSITION BAR (5 Core Value Pillars from Reference)
============================================================================================== -->
<section class="bg-white border-b border-slate-200/80 shadow-xs py-4 sm:py-6">
    <div class="agri-container">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 lg:gap-6 items-center">
            
            <!-- 1. Better Market Access -->
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-lg font-bold shrink-0">
                    🌱
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Better Market Access</h4>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Directly connect with verified buyers and suppliers.</p>
                </div>
            </div>

            <!-- 2. Transparent Trade -->
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-lg font-bold shrink-0">
                    🛡️
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Transparent Trade</h4>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Quality, quantity and pricing with complete transparency.</p>
                </div>
            </div>

            <!-- 3. Higher Productivity -->
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-lg font-bold shrink-0">
                    📈
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Higher Productivity</h4>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Access to inputs, services and expert advisory.</p>
                </div>
            </div>

            <!-- 4. Climate Resilience -->
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-lg font-bold shrink-0">
                    🍃
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Climate Resilience</h4>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Data-driven insights for sustainable farming.</p>
                </div>
            </div>

            <!-- 5. Stronger Value Chain -->
            <div class="flex items-center gap-3 col-span-2 md:col-span-1">
                <div class="h-11 w-11 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-lg font-bold shrink-0">
                    🤝
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Stronger Value Chain</h4>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">End-to-end support from farm to market.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==============================================================================================
     THE MARKETPLACE: TWO-PHASE ARCHITECTURE SHOWCASE (Direct Reference Section 3)
     Left: Option A Complete B2B Marketplace (Phase 1) with 6 Participant Cards
     Center: High-Fidelity Tablet and Smartphone Web & Mobile Application Mockups
     Right: Option D AI-Powered Integrated Marketplace (Phase 2) with 5 AI Intelligence Features
============================================================================================== -->
<section id="marketplace-split" class="py-14 sm:py-20 bg-[#f4f7fa] border-b border-slate-200">
    <div class="agri-container">
        <!-- 3-Column Split Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-6 items-start">
            
            <!-- LEFT COLUMN: Option A Complete Agricultural B2B Marketplace (Phase 1) (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <div class="space-y-2">
                    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-[#065f46] text-white">
                        THE MARKETPLACE
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight">
                        Option A: Complete Agricultural B2B Marketplace
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600">
                        Launch the core marketplace with all key participants and trading functions.
                    </p>
                    <span class="inline-block px-3 py-0.5 rounded-full text-xs font-bold bg-[#065f46] text-white">
                        Phase 1
                    </span>
                </div>

                <!-- 6 Participant Cards Grid (2 cols x 3 rows) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-3 pt-2">
                    <!-- 1. Agri-Input Marketplace -->
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-emerald-300 transition-all group">
                        <div class="h-9 w-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                            🌱
                        </div>
                        <h3 class="text-xs font-bold text-[#0c365b]">Agri-Input Marketplace</h3>
                        <p class="text-[10px] text-slate-500 leading-tight mt-1">Seeds, fertilizers, pesticides, bio-inputs & more</p>
                    </div>

                    <!-- 2. Agri-Produce Marketplace -->
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-amber-300 transition-all group">
                        <div class="h-9 w-9 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                            🌾
                        </div>
                        <h3 class="text-xs font-bold text-[#0c365b]">Agri-Produce Marketplace</h3>
                        <p class="text-[10px] text-slate-500 leading-tight mt-1">Crops, commodities, bulk trading & purchase orders</p>
                    </div>

                    <!-- 3. FPO / Cooperative Support -->
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-blue-300 transition-all group">
                        <div class="h-9 w-9 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                            👥
                        </div>
                        <h3 class="text-xs font-bold text-[#0c365b]">FPO / Cooperative Support</h3>
                        <p class="text-[10px] text-slate-500 leading-tight mt-1">Aggregate produce, manage listings, connect buyers</p>
                    </div>

                    <!-- 4. Supplier & Buyer Management -->
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-purple-300 transition-all group">
                        <div class="h-9 w-9 rounded-full bg-purple-100 text-purple-800 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                            🤝
                        </div>
                        <h3 class="text-xs font-bold text-[#0c365b]">Supplier & Buyer Management</h3>
                        <p class="text-[10px] text-slate-500 leading-tight mt-1">Verified profiles, quotations & deals</p>
                    </div>

                    <!-- 5. Logistics & Warehousing -->
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-teal-300 transition-all group">
                        <div class="h-9 w-9 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                            🚚
                        </div>
                        <h3 class="text-xs font-bold text-[#0c365b]">Logistics & Warehousing</h3>
                        <p class="text-[10px] text-slate-500 leading-tight mt-1">Transport, storage, shipment tracking</p>
                    </div>

                    <!-- 6. Financial Services -->
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-rose-300 transition-all group">
                        <div class="h-9 w-9 rounded-full bg-rose-100 text-rose-800 flex items-center justify-center text-base mb-2 group-hover:scale-105 transition-transform">
                            🏛️
                        </div>
                        <h3 class="text-xs font-bold text-[#0c365b]">Financial Services</h3>
                        <p class="text-[10px] text-slate-500 leading-tight mt-1">Payments, credit, insurance & more</p>
                    </div>
                </div>
            </div>

            <!-- CENTER COLUMN: Responsive Application Device Mockups (4 cols on lg) -->
            <div class="lg:col-span-4 flex flex-col items-center justify-center relative">
                <!-- Tablet Web App Frame Mockup -->
                <div class="mockup-tablet w-full max-w-[420px]">
                    <div class="mockup-tablet-inner p-3 bg-slate-50">
                        <!-- Top Web Mockup Search Bar -->
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-200 gap-2">
                            <x-logo variant="default" size="sm" />
                            <div class="flex-1 bg-white rounded-lg border border-slate-200 px-2 py-1 text-[9px] text-slate-400 flex items-center justify-between">
                                <span>Search products, suppliers...</span>
                                <span class="font-bold text-[#00875a]">Gujarat ▾</span>
                            </div>
                        </div>

                        <!-- Mockup Hero Banner -->
                        <div class="rounded-xl bg-gradient-to-r from-[#0c365b] to-[#00875a] text-white p-3 mb-2 shadow-xs">
                            <span class="text-[8px] font-bold uppercase tracking-wider text-emerald-200 block">Quality Inputs</span>
                            <h4 class="text-xs font-extrabold leading-tight">Better Harvests</h4>
                            <p class="text-[8px] text-emerald-100 mt-0.5">Trusted suppliers for your farming needs</p>
                        </div>

                        <!-- Mockup Category Pills -->
                        <div class="flex items-center justify-between gap-1 text-[8px] text-center font-bold text-slate-700 mb-2">
                            <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 flex-1">Seeds</span>
                            <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 flex-1">Fertilizer</span>
                            <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 flex-1">Crop Care</span>
                            <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 flex-1">Equipment</span>
                        </div>

                        <!-- Mockup Featured Products Grid -->
                        <div class="grid grid-cols-2 gap-1.5 text-[8px]">
                            <!-- Product 1 -->
                            <div class="bg-white p-1.5 rounded-lg border border-slate-200 shadow-2xs">
                                <span class="font-bold text-slate-900 block truncate">Hybrid Maize Seeds</span>
                                <span class="text-[#00875a] font-extrabold block">₹2,500 / Qtl</span>
                                <span class="text-[7px] text-slate-400 block truncate">Supplier: AgriSeeds Ltd</span>
                                <button type="button" class="mt-1 w-full py-0.5 rounded bg-[#00875a] text-white font-bold text-[7px]">Add to Cart</button>
                            </div>
                            <!-- Product 2 -->
                            <div class="bg-white p-1.5 rounded-lg border border-slate-200 shadow-2xs">
                                <span class="font-bold text-slate-900 block truncate">DAP Fertilizer 50kg</span>
                                <span class="text-[#00875a] font-extrabold block">₹1,350 / Bag</span>
                                <span class="text-[7px] text-slate-400 block truncate">Supplier: GreenFert</span>
                                <button type="button" class="mt-1 w-full py-0.5 rounded bg-[#00875a] text-white font-bold text-[7px]">Add to Cart</button>
                            </div>
                            <!-- Product 3 -->
                            <div class="bg-white p-1.5 rounded-lg border border-slate-200 shadow-2xs">
                                <span class="font-bold text-slate-900 block truncate">Bio-Pesticide 1L</span>
                                <span class="text-[#00875a] font-extrabold block">₹650 / Ltr</span>
                                <span class="text-[7px] text-slate-400 block truncate">Supplier: AgroChem</span>
                                <button type="button" class="mt-1 w-full py-0.5 rounded bg-[#00875a] text-white font-bold text-[7px]">Add to Cart</button>
                            </div>
                            <!-- Product 4 -->
                            <div class="bg-white p-1.5 rounded-lg border border-slate-200 shadow-2xs">
                                <span class="font-bold text-slate-900 block truncate">Tractor (On Rent)</span>
                                <span class="text-[#00875a] font-extrabold block">₹1,200 / Hr</span>
                                <span class="text-[7px] text-slate-400 block truncate">Supplier: FarmEquip</span>
                                <button type="button" class="mt-1 w-full py-0.5 rounded bg-[#00875a] text-white font-bold text-[7px]">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Realistic Modern Smartphone Mobile Application Mockup -->
                <div class="mockup-smartphone w-[210px] sm:w-[225px] md:w-[235px] mt-4 sm:mt-5 shadow-2xl relative">
                    <div class="mockup-smartphone-inner bg-slate-50">
                        <!-- Modern Phone Status Bar with Dynamic Island -->
                        <div class="pt-2 px-3 pb-1 bg-white flex items-center justify-between text-[8px] font-bold text-slate-800 shrink-0 select-none">
                            <span>9:41</span>
                            <!-- Dynamic Island Camera Pill -->
                            <div class="w-13 h-3 bg-black rounded-full flex items-center justify-end pr-1 gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#1e293b] ring-1 ring-slate-800 inline-block"></span>
                            </div>
                            <div class="flex items-center gap-1 text-[7px]">
                                <span>5G</span>
                                <span class="w-3.5 h-2 rounded-[2px] border border-slate-700 p-[1px] flex items-center">
                                    <span class="w-full h-full bg-[#00875a] rounded-[1px] block"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Mobile App Nav Header -->
                        <div class="px-2.5 py-1 bg-white border-b border-slate-100 flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-1">
                                <x-logo variant="default" size="xs" />
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[7px] font-bold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-full flex items-center gap-0.5">
                                    <span>📍</span> Gujarat
                                </span>
                                <span class="relative text-[10px] text-slate-700 p-0.5">
                                    🛒
                                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-600 text-white rounded-full text-[6px] font-bold flex items-center justify-center">2</span>
                                </span>
                            </div>
                        </div>

                        <!-- Mobile Search Bar -->
                        <div class="px-2.5 py-1.5 bg-white shrink-0">
                            <div class="bg-slate-100 rounded-lg px-2 py-1 flex items-center justify-between text-[8px] text-slate-400">
                                <span class="flex items-center gap-1">
                                    <span>🔍</span> Search seeds, fertilizer...
                                </span>
                                <span>🎙️</span>
                            </div>
                        </div>

                        <!-- Mobile Promo Hero Banner (matches tablet banner) -->
                        <div class="px-2.5 pb-1.5 shrink-0">
                            <div class="rounded-xl bg-gradient-to-r from-[#0c365b] via-[#082946] to-[#00875a] text-white p-2 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[7px] font-bold uppercase tracking-wider text-emerald-300">Quality Inputs</span>
                                    <span class="text-[6px] bg-emerald-500/30 text-emerald-200 px-1.5 py-0.2 rounded-full font-semibold">Direct FPO</span>
                                </div>
                                <h5 class="text-[10px] font-extrabold leading-tight mt-0.5">Better Harvests 🌾</h5>
                                <p class="text-[7px] text-emerald-100/90 leading-tight">Certified Agri-Inputs at Farm-Gate</p>
                            </div>
                        </div>

                        <!-- Mobile Category Pills (matches tablet categories) -->
                        <div class="px-2.5 pb-1.5 flex items-center gap-1 text-[7px] font-bold text-slate-700 shrink-0">
                            <span class="px-2 py-0.5 rounded-full bg-[#00875a] text-white shadow-2xs">🌱 Seeds</span>
                            <span class="px-1.5 py-0.5 rounded-full bg-white border border-slate-200">🧪 Fertilizer</span>
                            <span class="px-1.5 py-0.5 rounded-full bg-white border border-slate-200">🛡️ Care</span>
                            <span class="px-1.5 py-0.5 rounded-full bg-white border border-slate-200">🚜 Equip</span>
                        </div>

                        <!-- Mobile Products List (Adapted from tablet items with mobile layout) -->
                        <div class="px-2.5 space-y-1.5 flex-1 pb-1">
                            <!-- Product 1: Hybrid Maize Seeds -->
                            <div class="bg-white p-1.5 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-1.5">
                                <div class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs shrink-0 font-bold">
                                    🌱
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h6 class="text-[8px] font-bold text-slate-900 truncate">Hybrid Maize Seeds</h6>
                                        <span class="text-[6px] text-amber-600 font-bold">★ 4.9</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[7px] mt-0.5">
                                        <span class="text-slate-400 truncate">AgriSeeds Ltd</span>
                                        <strong class="text-[#00875a] font-black">₹2,500<span class="text-[6px] font-normal text-slate-500">/Qtl</span></strong>
                                    </div>
                                </div>
                                <button type="button" class="px-1.5 py-0.5 rounded-md bg-[#00875a] text-white font-bold text-[7px] shrink-0 shadow-2xs">
                                    + Add
                                </button>
                            </div>

                            <!-- Product 2: DAP Fertilizer 50kg -->
                            <div class="bg-white p-1.5 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-1.5">
                                <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-xs shrink-0 font-bold">
                                    🧪
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h6 class="text-[8px] font-bold text-slate-900 truncate">DAP Fertilizer 50kg</h6>
                                        <span class="text-[6px] text-emerald-700 font-bold">Verified</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[7px] mt-0.5">
                                        <span class="text-slate-400 truncate">GreenFert</span>
                                        <strong class="text-[#00875a] font-black">₹1,350<span class="text-[6px] font-normal text-slate-500">/Bag</span></strong>
                                    </div>
                                </div>
                                <button type="button" class="px-1.5 py-0.5 rounded-md bg-[#00875a] text-white font-bold text-[7px] shrink-0 shadow-2xs">
                                    + Add
                                </button>
                            </div>

                            <!-- Product 3: Bio-Pesticide 1L -->
                            <div class="bg-white p-1.5 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-1.5">
                                <div class="w-6 h-6 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center text-xs shrink-0 font-bold">
                                    🛡️
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h6 class="text-[8px] font-bold text-slate-900 truncate">Bio-Pesticide 1L</h6>
                                        <span class="text-[6px] text-teal-700 font-bold">Organic</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[7px] mt-0.5">
                                        <span class="text-slate-400 truncate">AgroChem</span>
                                        <strong class="text-[#00875a] font-black">₹650<span class="text-[6px] font-normal text-slate-500">/Ltr</span></strong>
                                    </div>
                                </div>
                                <button type="button" class="px-1.5 py-0.5 rounded-md bg-[#00875a] text-white font-bold text-[7px] shrink-0 shadow-2xs">
                                    + Add
                                </button>
                            </div>
                        </div>

                        <!-- Mobile App Bottom Navigation Bar -->
                        <div class="bg-white border-t border-slate-100 py-1 px-3 flex justify-around items-center text-[7px] font-bold text-slate-500 shrink-0">
                            <span class="text-[#00875a] flex flex-col items-center gap-0.5">
                                <span class="text-[8px]">🏠</span>
                                <span>Home</span>
                            </span>
                            <span class="flex flex-col items-center gap-0.5 hover:text-slate-700">
                                <span class="text-[8px]">🛒</span>
                                <span>Market</span>
                            </span>
                            <span class="flex flex-col items-center gap-0.5 hover:text-slate-700">
                                <span class="text-[8px]">🔬</span>
                                <span>Advisory</span>
                            </span>
                            <span class="flex flex-col items-center gap-0.5 hover:text-slate-700">
                                <span class="text-[8px]">👤</span>
                                <span>Profile</span>
                            </span>
                        </div>

                        <!-- Home Swipe Indicator Bar -->
                        <div class="pt-0.5 pb-1 bg-white shrink-0">
                            <div class="w-16 h-1 bg-slate-300 rounded-full mx-auto"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Option D AI-Powered Integrated Marketplace (Phase 2) (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <div class="space-y-2">
                    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-[#0c365b] text-white">
                        Followed by Option D
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight">
                        AI-Powered Integrated Marketplace
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600">
                        Scale with advanced AI, climate intelligence and full ecosystem services.
                    </p>
                    <span class="inline-block px-3 py-0.5 rounded-full text-xs font-bold bg-[#00875a] text-white">
                        Phase 2
                    </span>
                </div>

                <!-- 5 AI Intelligence Features List (Matching Reference) -->
                <div class="space-y-3 pt-2">
                    <!-- 1. AI Quality Grading & Traceability -->
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-teal-300 transition-all">
                        <div class="h-9 w-9 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-base shrink-0 mt-0.5">
                            ⚙️
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">AI Quality Grading & Traceability</h3>
                            <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Computer vision for quality assessment</p>
                        </div>
                    </div>

                    <!-- 2. Climate & Weather Intelligence -->
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-teal-300 transition-all">
                        <div class="h-9 w-9 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-base shrink-0 mt-0.5">
                            🌤️
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Climate & Weather Intelligence</h3>
                            <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Alerts, risk insights & crop advisory</p>
                        </div>
                    </div>

                    <!-- 3. Demand Forecasting & Price Intelligence -->
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-teal-300 transition-all">
                        <div class="h-9 w-9 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-base shrink-0 mt-0.5">
                            📈
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Demand Forecasting & Price Intelligence</h3>
                            <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Market insights & smart recommendations</p>
                        </div>
                    </div>

                    <!-- 4. Precision Farming & Digital Advisory -->
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-teal-300 transition-all">
                        <div class="h-9 w-9 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-base shrink-0 mt-0.5">
                            🌾
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Precision Farming & Digital Advisory</h3>
                            <p class="text-[11px] text-slate-500 leading-tight mt-0.5">IoT, sensors & expert guidance</p>
                        </div>
                    </div>

                    <!-- 5. Integrated Services -->
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-white border border-slate-200/70 shadow-xs hover:shadow-md hover:border-teal-300 transition-all">
                        <div class="h-9 w-9 rounded-full bg-[#e6f4ea] text-[#00875a] flex items-center justify-center text-base shrink-0 mt-0.5">
                            🤝
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-[#0c365b] leading-tight">Integrated Services</h3>
                            <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Logistics, warehousing, finance, equipment rental and more</p>
                        </div>
                    </div>
                </div>

                <!-- Phase 2 Value CTA Button -->
                <div class="pt-2">
                    <a href="{{ route('solutions.index') }}" 
                       class="inline-flex items-center justify-between w-full px-5 py-3.5 rounded-full bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-sm shadow-md hover:shadow-lg transition-all group">
                        <span>More Value. A Smarter Future.</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==============================================================================================
     BOTTOM NAVY SLOGAN BAR (Direct Reference Section 4)
============================================================================================== -->
<section class="bg-[#071f37] text-white py-4 border-t border-[#0c365b]">
    <div class="agri-container flex items-center justify-center gap-3 text-center text-xs sm:text-sm md:text-base font-bold tracking-wide">
        <span class="text-[#34d399]">🍃</span>
        <span>One Platform. Every Stakeholder. A Stronger Agricultural Ecosystem.</span>
        <span class="text-[#34d399]">🍃</span>
    </div>
</section>

<!-- ==============================================================================================
     SECTION: FOUR CORE SOLUTION PILLARS (Retained Functional Blueprint)
============================================================================================== -->
<section id="solutions-section" class="py-16 bg-white border-b border-slate-200">
    <div class="agri-container">
        <div class="max-w-3xl mb-12">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-50 text-[#00875a] border border-emerald-200 text-xs font-bold mb-2">
                🏛️ Core Value Proposition
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-[#0c365b] tracking-tight">
                Our Four Solution Pillars
            </h2>
            <p class="text-sm sm:text-base text-slate-600 mt-2">
                Nxtperson connects every link of the agricultural value chain through four strategic, interconnected solution pillars.
            </p>
        </div>

        <!-- 4-Card Prominent Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Card 1: AI Quality Assurance & Farm-Gate Grading -->
            <div class="agri-card p-7 flex flex-col justify-between group hover:border-[#0c365b]">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            🔬
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                            Planned Roadmap Capability
                        </span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Pillar 1</span>
                    <h3 class="text-xl font-bold text-[#0c365b] mt-1 mb-2">
                        AI Quality Assurance & Farm-Gate Grading
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                        Eliminates the estimated ₹90,000 Cr annual post-harvest value leakage through computer-vision inspection, instant quality grading certificates (A/B/C), and QR-based traceability directly at the farm gate.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('solutions.quality-grading') }}" class="text-xs font-bold text-[#0c365b] hover:text-[#00875a] flex items-center gap-1">
                        <span>Read Grading Architecture</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                    <span class="text-[11px] text-slate-400">Phase 1 Blueprint</span>
                </div>
            </div>

            <!-- Card 2: Precision Agriculture & Smart Irrigation -->
            <div class="agri-card p-7 flex flex-col justify-between group hover:border-[#0c365b]">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="h-12 w-12 rounded-2xl bg-sky-100 text-sky-800 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            📡
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-[#00875a] border border-emerald-200">
                            Interactive Demo Live
                        </span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-sky-800">Pillar 2</span>
                    <h3 class="text-xl font-bold text-[#0c365b] mt-1 mb-2">
                        Precision Agriculture & Smart Irrigation
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                        Unites satellite remote sensing (Sentinel-2 NDVI canopy vigour) with hyper-local micro-weather models to save up to 30% water and eliminate spray waste across Gujarat agro-zones.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('solutions.precision-agri') }}" class="text-xs font-bold text-[#0c365b] hover:text-[#00875a] flex items-center gap-1">
                        <span>Explore Precision Agri</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                    <span class="text-[11px] text-slate-400">Weather & NDVI Live</span>
                </div>
            </div>

            <!-- Card 3: AI Advisory & Digital Farmer Platform -->
            <div class="agri-card p-7 flex flex-col justify-between group hover:border-[#0c365b]">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-[#00875a] flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            🌱
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-[#00875a] border border-emerald-200">
                            Interactive Demo Live
                        </span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Pillar 3</span>
                    <h3 class="text-xl font-bold text-[#0c365b] mt-1 mb-2">
                        AI Advisory & Digital Farmer Platform
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                        Delivers regional-language agronomics (Gujarati, Hindi, English) for 8 Gujarat commodities, featuring computer-vision leaf diagnosis, senior agronomist escalation, and localized crop packages.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('solutions.digital-farmer') }}" class="text-xs font-bold text-[#0c365b] hover:text-[#00875a] flex items-center gap-1">
                        <span>Try Advisory Tools</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                    <span class="text-[11px] text-slate-400">Form & Scanner Live</span>
                </div>
            </div>

            <!-- Card 4: Integrated Agricultural Marketplace -->
            <div class="agri-card p-7 flex flex-col justify-between group hover:border-[#0c365b]">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="h-12 w-12 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            🛒
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                            Inputs Live • Produce Planned
                        </span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-800">Pillar 4</span>
                    <h3 class="text-xl font-bold text-[#0c365b] mt-1 mb-2">
                        Integrated Digital Agricultural Marketplace
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                        Connects input manufacturers, dealers, and farmers on one end, with institutional food processors, textile mills, FPOs, and export corridors on the other for fair, transparent agricultural commerce.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('solutions.marketplace') }}" class="text-xs font-bold text-[#0c365b] hover:text-[#00875a] flex items-center gap-1">
                        <span>View Marketplace Scope</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                    <span class="text-[11px] text-slate-400">Input Catalog Live</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================================================================
     SECTION: HOW NXTPERSON WORKS (8 Stages across 3 Phases)
============================================================================================== -->
<section id="how-it-works" class="py-16 bg-slate-50 border-b border-slate-200">
    <div class="agri-container">
        <div class="max-w-3xl mb-12">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                🛤️ Operational Journey
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-[#0c365b] tracking-tight">
                How Nxtperson Works
            </h2>
            <p class="text-sm sm:text-base text-slate-600 mt-2">
                The agricultural journey structured across eight sequential stages within three core phases, clearly distinguishing operational demonstration tools from planned roadmap capabilities.
            </p>
        </div>

        <div class="space-y-8">
            <!-- Phase 1 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-5 border-b border-slate-100">
                    <span class="text-xs font-black uppercase tracking-wider text-[#00875a]">Phase 1: Registration & Assessment</span>
                    <span class="text-xs text-slate-400">Pre-Sowing & Baseline</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 text-sm">Stage 1: Farmer Registration</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Demo Live</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mobile OTP login, district and taluka profiling, crop selection, and plot boundary registration.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 text-sm">Stage 2: Farm Assessment</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-sky-100 text-sky-800">Demo Telemetry</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Baseline soil health indicators, historical vegetative vigours, and micro-climate risk profiles.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Phase 2 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-5 border-b border-slate-100">
                    <span class="text-xs font-black uppercase tracking-wider text-[#0c365b]">Phase 2: Precision Farming & Digital Services</span>
                    <span class="text-xs text-slate-400">Growing Season Management</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 text-sm">Stage 3: Precision Agriculture</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Demo Live</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Hyper-local weather forecasts, optimal spray windows, and satellite NDVI vegetative monitoring.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 text-sm">Stage 4: Digital Advisory</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Demo Live</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Leaf photo pest scanning, bio-fertilizer dosage recommendations, and senior agronomist consultations.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Phase 3 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-5 border-b border-slate-100">
                    <span class="text-xs font-black uppercase tracking-wider text-purple-700">Phase 3: Market & Finance Access</span>
                    <span class="text-xs text-slate-400">Harvest, Sourcing & Trade</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 text-xs">Stage 5: Quality Assessment</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-900">Planned</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Farm-gate optical produce inspection and QR grade certificate issuance.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 text-xs">Stage 6: Marketplace Access</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">Inputs Live</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Input discovery live; produce listing and FPO bulk sales planned for commercial rollout.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 text-xs">Stage 7: Logistics & Finance</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">Planned</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Cold storage booking, freight dispatch, and digital trade escrow settlement.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 text-xs">Stage 8: Institutional Offtake</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">Planned</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Direct buyer contracts for food processors, spinning mills, and export corridors.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================================================================
     SECTION: STAKEHOLDER ECOSYSTEM (Visual Hub)
============================================================================================== -->
<section id="ecosystem-section" class="py-16 bg-white border-b border-slate-200">
    <div class="agri-container">
        <div class="max-w-3xl mb-12">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                🌐 Connected Value Chain
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-[#0c365b] tracking-tight">
                The Agricultural Value Chain Ecosystem
            </h2>
            <p class="text-sm sm:text-base text-slate-600 mt-2">
                Nxtperson Private Limited positions itself at the center of the agricultural ecosystem, uniting multiple stakeholders into a cohesive digital workflow.
            </p>
        </div>

        <!-- Visual Grid of 10 Stakeholders around Nxtperson Central Hub -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">🌾</span>
                <strong class="text-xs text-slate-900 block font-bold">Farmers</strong>
                <span class="text-[10px] text-slate-500">Producers & Growers</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">🤝</span>
                <strong class="text-xs text-slate-900 block font-bold">FPOs & Cooperatives</strong>
                <span class="text-[10px] text-slate-500">Yield Aggregators</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">🏭</span>
                <strong class="text-xs text-slate-900 block font-bold">Input Manufacturers</strong>
                <span class="text-[10px] text-slate-500">Seeds & Fertilizers</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">🏢</span>
                <strong class="text-xs text-slate-900 block font-bold">Produce Buyers</strong>
                <span class="text-[10px] text-slate-500">Processors & Retail</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">👨‍🌾</span>
                <strong class="text-xs text-slate-900 block font-bold">Agri Advisors</strong>
                <span class="text-[10px] text-slate-500">Agronomists & Labs</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">⚙️</span>
                <strong class="text-xs text-slate-900 block font-bold">Equipment Providers</strong>
                <span class="text-[10px] text-slate-500">Irrigation & Hardware</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">🏬</span>
                <strong class="text-xs text-slate-900 block font-bold">Cold Storage</strong>
                <span class="text-[10px] text-slate-500">Post-Harvest Hubs</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">🚚</span>
                <strong class="text-xs text-slate-900 block font-bold">Logistics Partners</strong>
                <span class="text-[10px] text-slate-500">Rural Freight & Fleet</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">💳</span>
                <strong class="text-xs text-slate-900 block font-bold">Financial Institutions</strong>
                <span class="text-[10px] text-slate-500">Credit & Insurance</span>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center hover:border-emerald-400 hover:shadow-xs transition-all">
                <span class="text-2xl block mb-1">🏛️</span>
                <strong class="text-xs text-slate-900 block font-bold">APMCs & Regulators</strong>
                <span class="text-[10px] text-slate-500">Mandi Benchmarks</span>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs">
            <span class="text-slate-500 italic">
                * Note: Demonstrates Nxtperson's planned stakeholder network model; no commercial contracts or exclusive partnerships implied.
            </span>
            <a href="{{ route('ecosystem.index') }}" class="font-bold text-[#0c365b] hover:text-[#00875a] flex items-center gap-1">
                <span>View Complete Ecosystem Architecture</span>
                <span>→</span>
            </a>
        </div>
    </div>
</section>

<!-- ==============================================================================================
     OPERATIONAL FARMER SERVICES (Retained Working Functionality)
============================================================================================== -->
<div id="farmer-services-suite">
    <!-- Weather Snapshot Bar -->
    <div class="bg-slate-50 py-8 border-b border-slate-200">
        <div class="agri-container">
            <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6 items-center">
                    <!-- Left: Current Conditions (5 cols) -->
                    <div class="lg:col-span-5 flex flex-wrap sm:flex-nowrap items-center justify-between gap-4 border-b lg:border-b-0 lg:border-r border-slate-200 pb-4 lg:pb-0 lg:pr-6">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-2xl shrink-0">
                                🌦️
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-[#00875a] flex items-center gap-1.5">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#00875a]"></span>
                                        {{ __('messages.weather.title') }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                        {{ __('messages.simulated_tag') }}
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 mt-0.5">{{ $weatherSummary['district'] }}</h3>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-[#0c365b]">{{ $weatherSummary['temperature'] }}°C</span>
                            <span class="text-xs font-medium text-[#00875a] bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                Spray: {{ $weatherSummary['spray_window'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Middle: Parameters (3 cols) -->
                    <div class="lg:col-span-3 grid grid-cols-3 gap-2 text-center text-xs border-b lg:border-b-0 lg:border-r border-slate-200 pb-4 lg:pb-0 lg:pr-4">
                        <div class="bg-slate-50 py-1.5 px-2 rounded-lg">
                            <span class="text-slate-500 text-[10px] block">Humidity</span>
                            <span class="font-bold text-slate-800">{{ $weatherSummary['humidity'] }}%</span>
                        </div>
                        <div class="bg-slate-50 py-1.5 px-2 rounded-lg">
                            <span class="text-slate-500 text-[10px] block">Wind NW</span>
                            <span class="font-bold text-slate-800">{{ $weatherSummary['wind_speed'] }} km/h</span>
                        </div>
                        <div class="bg-slate-50 py-1.5 px-2 rounded-lg">
                            <span class="text-slate-500 text-[10px] block">Rain Chance</span>
                            <span class="font-bold text-slate-800">{{ $weatherSummary['rainfall_prob'] }}%</span>
                        </div>
                    </div>

                    <!-- Right: Mini 4-day forecast chips & link (4 cols) -->
                    <div class="lg:col-span-4 flex items-center justify-between gap-3">
                        <div class="grid grid-cols-4 gap-1.5 flex-1 text-center">
                            @foreach($weatherSummary['next_forecast'] as $fc)
                                <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-200/80">
                                    <span class="text-[9px] text-slate-500 block leading-tight">{{ $fc['day'] }}</span>
                                    <span class="text-xs block my-0.5">{{ $fc['icon'] }}</span>
                                    <span class="text-[10px] font-bold text-slate-800 block">{{ $fc['temp'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('weather.index') }}" class="shrink-0 p-2 text-xs font-bold text-[#00875a] hover:text-[#0c365b] hover:bg-emerald-50 rounded-lg transition-colors" title="View Full Weather Advisory">
                            Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Simulated AI Crop Diagnostic Teaser -->
    <section id="ai-diagnostic-demo" class="py-14 bg-white border-b border-slate-200">
        <div class="agri-container">
            <div class="max-w-3xl mx-auto text-center mb-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold mb-3">
                    <span>🔬</span> Part of Digital Farmer Platform • Interactive Demonstration
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-[#0c365b] tracking-tight">
                    {{ __('messages.modules.advisory_title') }}
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-2">
                    {{ __('messages.modules.advisory_desc') }}
                </p>
            </div>

            <!-- Interactive Diagnostic Box -->
            <div class="max-w-4xl mx-auto bg-slate-50 rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-[#00875a]"></span>
                        <span class="font-bold text-sm sm:text-base text-slate-900">Try Interactive Simulated Disease Diagnosis</span>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-medium border border-amber-200">
                        {{ __('messages.simulated_tag') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    <!-- Controls (5 cols) -->
                    <div class="md:col-span-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Select Gujarat Crop:
                            </label>
                            <select id="diagnostic-crop-select" class="w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-[#00875a] focus:outline-hidden">
                                <option value="cotton">Cotton (કપાસ / कपास) - Rajkot/Surendranagar</option>
                                <option value="banana">Banana (કેળા / केला) - Anand/Bharuch</option>
                                <option value="potato">Potato (બટાકા / आलू) - Deesa/Banaskantha</option>
                                <option value="tomato">Tomato (ટામેટા / टमाटर) - Surat/Navsari</option>
                                <option value="chilli">Chilli (મરચા / मिर्च) - Gondal/Amreli</option>
                                <option value="rice">Rice (ડાંગર / चावल) - Kheda/Anand</option>
                                <option value="grain">Wheat (ઘઉં / गेहूं) - Bhal/Ahmedabad</option>
                                <option value="cauliflower">Cauliflower (ફૂલકોબી / फूलगोभी) - Vadodara</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Visual Symptom Observed:
                            </label>
                            <select id="diagnostic-symptom-select" class="w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-[#00875a] focus:outline-hidden">
                                <option value="bollworm">Holes in bolls / dropping squares</option>
                                <option value="curl">Upward leaf curling & stunted growth</option>
                                <option value="blight">Water-soaked brown necrotic lesions</option>
                                <option value="wilt">Yellowing foliage & vascular browning</option>
                                <option value="rust">Rust-colored powdery pustules</option>
                            </select>
                        </div>

                        <button type="button" id="run-ai-scan-btn" class="w-full py-3 px-4 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-sm shadow-sm transition-all flex items-center justify-center gap-2">
                            <span>⚡</span>
                            <span>Run Demonstration AI Scan</span>
                        </button>
                        <p class="text-[11px] text-slate-500 leading-tight">
                            * In accordance with demo rules, AI recommendations are simulated for evaluation and include expert escalation pathways.
                        </p>
                    </div>

                    <!-- Diagnostic Output Screen (7 cols) -->
                    <div class="md:col-span-7 bg-white rounded-xl border border-slate-200 p-5 shadow-inner">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wide">Diagnosis Output</span>
                            <span id="ai-status-badge" class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                Sample Demonstration Match
                            </span>
                        </div>

                        <div id="ai-result-content" class="space-y-3 text-sm">
                            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                                <span class="text-xs text-emerald-700 font-semibold block">Detected Primary Pest/Condition:</span>
                                <h4 id="ai-detected-title" class="text-base font-bold text-emerald-950 mt-0.5">
                                    Pink Bollworm (Pectinophora gossypiella)
                                </h4>
                                <p id="ai-detected-desc" class="text-xs text-emerald-800 mt-1">
                                    Early larval penetration in squares and developing bolls. Threshold exceeded (>10% rosette flowers).
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                                    <span class="text-slate-500 font-semibold block text-[10px]">Recommended Bio-Intervention:</span>
                                    <span id="ai-solution" class="font-bold text-slate-900 block mt-0.5">Pheromone traps (8/acre) + Neem Azadirachtin 10,000 ppm</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                                    <span class="text-slate-500 font-semibold block text-[10px]">Agronomist Escalation Status:</span>
                                    <span id="ai-escalation" class="font-bold text-emerald-700 block mt-0.5">Triage Level 1 • Routine Monitoring</span>
                                </div>
                            </div>

                            <div class="pt-2 flex items-center justify-between text-xs">
                                <a href="{{ route('advisory.index') }}" class="font-semibold text-[#00875a] hover:text-[#0c365b] flex items-center gap-1">
                                    Open Full Advisory Ticket & Photo Upload →
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">Model: NxtAgri-Vision-v2.4 (Simulated)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8 Key Gujarat Crops Section -->
    <section id="crops-section" class="py-16 bg-slate-50 border-b border-slate-200">
        <div class="agri-container">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold mb-2">
                        🌾 Tailored Agronomy
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-[#0c365b] tracking-tight">
                        8 Essential Gujarat Crops
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600 mt-1 max-w-xl">
                        Dedicated monitoring, market discovery, and protection protocols tailored for Gujarat's premier agricultural commodities.
                    </p>
                </div>
                <div class="text-xs text-slate-500">
                    <span class="font-semibold text-slate-800">Language view:</span>
                    @if($currentLocale === 'gu') ગુજરાતી આવૃત્તિ
                    @elseif($currentLocale === 'hi') हिंदी संस्करण
                    @else English Edition
                    @endif
                </div>
            </div>

            <!-- Crops 8-Card Grid (Responsive 1-col -> 2-col -> 4-col) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($crops as $crop)
                    <x-crop-card :crop="$crop" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- APMC Mandi Price Intelligence Grid -->
    <section id="mandi-section" class="py-16 bg-white border-b border-slate-200">
        <div class="agri-container">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold mb-2">
                        📈 Market Discovery
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-[#0c365b] tracking-tight">
                        {{ __('messages.modules.mandi_title') }}
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600 mt-1 max-w-xl">
                        Seeded live price discovery from prominent Gujarat APMC market yards. Updated daily for transparent farmer realization.
                    </p>
                </div>
                <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs max-w-xs">
                    <strong>Sample Gujarat Data:</strong> APMC rates shown are seeded demonstration records for October 2026.
                </div>
            </div>

            <!-- Mandi Table (Responsive with mobile overflow scroll) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden max-w-full">
                <div class="overflow-x-auto w-full max-w-full">
                    <table class="w-full text-left border-collapse text-sm min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6">Commodity & Variety</th>
                                <th class="py-3.5 px-4">Market Yard</th>
                                <th class="py-3.5 px-4">Arrivals</th>
                                <th class="py-3.5 px-4">Min Price</th>
                                <th class="py-3.5 px-4 font-bold text-slate-900">Modal Price</th>
                                <th class="py-3.5 px-4">Max Price</th>
                                <th class="py-3.5 px-4 text-right pr-6">Trend</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($mandiRates as $mandi)
                                @php
                                    $commodity = $mandi->commodity_name ?? $mandi['crop'] ?? 'Cotton';
                                    $yard = $mandi->yard_name ?? $mandi['yard'] ?? 'APMC Rajkot';
                                    $arrival = $mandi->arrival_quantity ?? $mandi['arrival'] ?? 'N/A';
                                    $min = $mandi->min_price ?? $mandi['min'] ?? 0;
                                    $modal = $mandi->modal_price ?? $mandi['modal'] ?? 0;
                                    $max = $mandi->max_price ?? $mandi['max'] ?? 0;
                                    $trend = $mandi->trend_percentage ?? $mandi['trend'] ?? '+0.0%';
                                @endphp
                                <tr class="hover:bg-emerald-50/40 transition-colors">
                                    <td class="py-3.5 px-4 sm:px-6 font-bold text-slate-900 flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-[#00875a]"></span>
                                        {{ $commodity }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700 font-medium">{{ $yard }}</td>
                                    <td class="py-3.5 px-4 text-slate-500 text-xs">{{ $arrival }}</td>
                                    <td class="py-3.5 px-4 text-slate-600">₹{{ number_format((float) $min) }}</td>
                                    <td class="py-3.5 px-4 font-extrabold text-[#0c365b]">₹{{ number_format((float) $modal) }} / Qtl</td>
                                    <td class="py-3.5 px-4 text-slate-600">₹{{ number_format((float) $max) }}</td>
                                    <td class="py-3.5 px-4 text-right pr-6">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ str_contains($trend, '+') ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $trend }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="py-3 px-6 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between text-xs text-slate-500">
                    <span>Displaying 6 Gujarat APMC benchmarks (Rajkot, Gondal, Unjha, Deesa, Anand)</span>
                    <a href="{{ route('mandi.index') }}" class="font-semibold text-[#00875a] hover:text-[#0c365b]">
                        Open Advanced Mandi Price Trends & History →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Satellite Field Health / NDVI Section -->
    <section id="satellite-section" class="py-16 bg-white border-b border-slate-200">
        <div class="agri-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Left Info (6 cols) -->
                <div class="lg:col-span-6 space-y-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                        <span>🛰️</span> {{ __('messages.simulated_tag') }} • Demonstration Telemetry
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-[#0c365b]">
                        {{ __('messages.modules.satellite_title') }}
                    </h2>
                    <p class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-[#00875a]">
                        Vegetative Canopy Vigour & Remote Sensing (NDVI Analysis)
                    </p>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        {{ __('messages.modules.satellite_desc') }}
                    </p>

                    <!-- Supporting Elevated Monitoring Photograph -->
                    <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-xs aspect-[16/9] relative bg-slate-100">
                        <picture>
                            <source srcset="{{ asset('images/services/satellite_monitoring.webp') }}" type="image/webp">
                            <img src="{{ asset('images/services/satellite_monitoring.jpg') }}" 
                                 alt="Elevated aerial field monitoring of crop canopy and irrigation layout" 
                                 width="700" 
                                 height="400" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover">
                        </picture>
                        <div class="absolute bottom-2.5 left-3 bg-slate-900/85 backdrop-blur-xs text-white text-[10px] px-2.5 py-1 rounded-md font-medium">
                            Canopy Vigour & Soil Moisture Monitoring
                        </div>
                    </div>

                    <!-- Plot Health Breakdown -->
                    <div class="grid grid-cols-3 gap-3 text-center text-xs">
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                            <span class="text-emerald-700 font-black text-xl">78%</span>
                            <span class="text-slate-700 block text-[11px] font-semibold mt-0.5">High Vigour (Zone A)</span>
                            <span class="text-slate-500 text-[10px] block">Dense Green Canopy</span>
                        </div>
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                            <span class="text-amber-700 font-black text-xl">14%</span>
                            <span class="text-slate-700 block text-[11px] font-semibold mt-0.5">Mild Stress (Zone B)</span>
                            <span class="text-slate-500 text-[10px] block">Nutrient Foliar Need</span>
                        </div>
                        <div class="p-3 rounded-xl bg-orange-50 border border-orange-200">
                            <span class="text-orange-700 font-black text-xl">8%</span>
                            <span class="text-slate-700 block text-[11px] font-semibold mt-0.5">Water Deficit (Zone C)</span>
                            <span class="text-slate-500 text-[10px] block">Irrigation Required</span>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('satellite.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-[#0c365b] hover:bg-[#071f37] text-white font-bold text-sm shadow-sm transition-colors">
                            <span>Explore Satellite Field Health Dashboard →</span>
                        </a>
                    </div>
                </div>

                <!-- Right Visual Light-Themed NDVI Card (6 cols) -->
                <div class="lg:col-span-6">
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 shadow-sm relative overflow-hidden">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#00875a]"></span>
                                <span class="font-bold text-slate-900">Plot #GJ-RKT-402 (Cotton 4.5 Ha)</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                Simulated Demonstration Data
                            </span>
                        </div>

                        <!-- NDVI Scale Legend Bar (Light) -->
                        <div class="my-4 p-3.5 rounded-xl bg-white border border-slate-200 space-y-1.5">
                            <div class="flex justify-between text-[11px] font-semibold text-slate-600">
                                <span>Bare Soil (0.15)</span>
                                <span>Moderate (0.50)</span>
                                <span>Dense Vigour (0.85)</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gradient-to-r from-orange-400 via-amber-300 to-emerald-600 shadow-inner"></div>
                            <div class="flex justify-between text-[10px] text-slate-500 pt-0.5">
                                <span class="text-orange-700 font-medium">Zone C (Stress)</span>
                                <span class="text-amber-700 font-medium">Zone B (Normal)</span>
                                <span class="text-emerald-700 font-medium">Zone A (Optimum)</span>
                            </div>
                        </div>

                        <!-- Simulated Visual Field Multi-Zone Grid Surface (Light-Themed) -->
                        <div class="my-4 aspect-video rounded-xl bg-emerald-50/60 border border-emerald-200/80 relative flex items-center justify-center p-4 overflow-hidden">
                            <!-- Plot Graphic Boundaries -->
                            <div class="w-full h-full border-2 border-dashed border-emerald-600/40 rounded-lg relative flex flex-col justify-between p-3 bg-gradient-to-br from-emerald-100/70 via-emerald-50/50 to-amber-50/60">
                                <div class="flex justify-between items-start">
                                    <span class="text-[11px] px-2 py-0.5 rounded-md bg-white text-emerald-800 font-bold border border-emerald-300 shadow-2xs">
                                        Zone A: NDVI 0.74 (Optimum)
                                    </span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-md bg-white text-amber-800 font-bold border border-amber-300 shadow-2xs">
                                        Zone B: NDVI 0.48 (Watch)
                                    </span>
                                </div>

                                <div class="text-center">
                                    <span class="text-xs font-bold text-slate-800 bg-white/90 px-3.5 py-1 rounded-full border border-slate-300 shadow-xs">
                                        Simulated Spectral Vegetation Layer
                                    </span>
                                </div>

                                <div class="flex justify-between items-end">
                                    <span class="text-[11px] text-slate-700 font-semibold bg-white/80 px-2 py-0.5 rounded border border-slate-200">
                                        Soil Moisture Index: 62%
                                    </span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-md bg-white text-orange-800 font-bold border border-orange-300 shadow-2xs">
                                        Zone C: NDVI 0.28 (Deficit)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Disclaimers & Notes -->
                        <p class="text-[11px] text-slate-500 text-center italic mt-2">
                            * Explicit Demonstration Notice: Satellite spectral reflectance and zonal polygons are simulated demonstration models and do not represent a real-time satellite acquisition.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Inputs & Local Distributor Directory Banner -->
    <section class="py-16 bg-slate-50 border-b border-slate-200">
        <div class="agri-container">
            <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-md">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                    <!-- Left: Content & CTAs (7 cols) -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">
                            <span>📍</span> Demonstration Dealer Directory • Gujarat Talukas
                        </div>
                        <h3 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-[#0c365b]">
                            {{ __('messages.modules.distributors_title') }}
                        </h3>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl">
                            Looking for bio-fertilizers, hybrid crop seeds, or micro-irrigation equipment? Discover local agricultural stockists and dealers across Saurashtra, Central, and North Gujarat by pincode.
                        </p>

                        <div class="pt-2 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('distributors.index') }}" class="py-3.5 px-6 rounded-full bg-[#0c365b] hover:bg-[#071f37] text-white font-bold text-sm text-center shadow-sm hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                                <span>📍</span>
                                <span>Find Distributors by Pincode</span>
                            </a>
                            <a href="{{ route('products.index') }}" class="py-3.5 px-6 rounded-full bg-white hover:bg-slate-50 text-[#0c365b] font-bold text-sm text-center border-2 border-[#0c365b] transition-colors flex items-center justify-center gap-2">
                                <span>📦</span>
                                <span>Browse Agricultural Products</span>
                            </a>
                        </div>

                        <p class="text-xs text-slate-500 italic pt-2">
                            * Demonstration Directory: Dealer listings and product availability are sample records provided for evaluation; no direct payment or commercial transaction is processed.
                        </p>
                    </div>

                    <!-- Right: Agricultural Store & Retail Photography (5 cols) -->
                    <div class="lg:col-span-5">
                        <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-md aspect-[4/3] bg-slate-100">
                            <picture>
                                <source srcset="{{ asset('images/distributors/agri_store.webp') }}" type="image/webp">
                                <img src="{{ asset('images/distributors/agri_store.jpg') }}" 
                                     alt="Agricultural input store with seeds, fertilizers and crop protection products" 
                                     width="600" 
                                     height="450" 
                                     loading="lazy" 
                                     class="w-full h-full object-cover">
                            </picture>
                            <div class="absolute bottom-3 left-3 right-3 bg-white/95 backdrop-blur-xs px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 shadow-xs flex items-center gap-2">
                                <span class="text-[#00875a]">🏪</span>
                                <span>Connect with Agricultural Input Stockists</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Vanilla JS for the Interactive Diagnostic Scanner Widget -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cropSelect = document.getElementById('diagnostic-crop-select');
        const symptomSelect = document.getElementById('diagnostic-symptom-select');
        const scanBtn = document.getElementById('run-ai-scan-btn');
        const detectedTitle = document.getElementById('ai-detected-title');
        const detectedDesc = document.getElementById('ai-detected-desc');
        const solution = document.getElementById('ai-solution');
        const escalation = document.getElementById('ai-escalation');
        const badge = document.getElementById('ai-status-badge');

        const diagnosisRules = @json($diagnosticRules);

        if (scanBtn) {
            scanBtn.addEventListener('click', function () {
                scanBtn.disabled = true;
                scanBtn.innerHTML = '<span>⏳</span> Analyzing leaf symptoms...';

                setTimeout(function () {
                    const crop = cropSelect.value;
                    const symptom = symptomSelect.value;
                    const key = `${crop}-${symptom}`;

                    const data = diagnosisRules[key] || {
                        title: `${crop.toUpperCase()} Pest/Pathogen Stress`,
                        desc: `Symptom "${symptom}" matched against Gujarat agricultural knowledge base.`,
                        solution: 'Organic bio-fertilizer foliar application & soil moisture optimization.',
                        escalation: 'Triage Level 1 • Demo Advisory Verified',
                        confidence: 'Demonstration Rule Match'
                    };

                    detectedTitle.textContent = data.title;
                    detectedDesc.textContent = data.desc;
                    solution.textContent = data.solution;
                    escalation.textContent = data.escalation;
                    badge.textContent = data.confidence;

                    scanBtn.disabled = false;
                    scanBtn.innerHTML = '<span>⚡</span> Run Demonstration AI Scan';
                }, 600);
            });
        }
    });
</script>
@endsection
