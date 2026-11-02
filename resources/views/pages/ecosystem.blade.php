@extends('layouts.app')

@section('title', 'Agricultural Stakeholder Ecosystem - Nxtperson Private Limited')
@section('meta_description', 'Explore Nxtperson\'s 10-stakeholder agricultural value chain network uniting farmers, FPOs, buyers, input manufacturers, advisors, logistics, and financial institutions.')

@section('content')
<div class="bg-stone-50 text-stone-900 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Top Breadcrumb & Corporate Header -->
    <header class="border-b border-stone-200/80 bg-white py-4">
        <div class="agri-container">
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <nav class="flex items-center gap-2 text-stone-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-emerald-800 font-semibold">Ecosystem</span>
                </nav>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    <span>10-Stakeholder Integrated Network</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 sm:py-16 bg-white border-b border-stone-200/80">
        <div class="agri-container">
            <div class="max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full border border-emerald-200">
                    Collaborative Value Chain
                </span>
                <h1 class="text-3xl sm:text-5xl font-black text-[#0c365b] tracking-tight mt-3 mb-4 leading-tight">
                    The Nxtperson Agricultural Ecosystem
                </h1>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Agriculture cannot be transformed in isolation. Nxtperson Private Limited positions itself at the center of the agricultural ecosystem, creating digital connective tissue between producers, input suppliers, advisory experts, logistics providers, and institutional buyers.
                </p>
                <div class="mt-4 p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 max-w-2xl">
                    <strong class="font-bold">Ecosystem Disclaimer:</strong>
                    This architecture illustrates Nxtperson's comprehensive stakeholder relationship model. It represents the planned value chain integration and does not claim exclusive contracts or formal government endorsements.
                </div>
            </div>
        </div>
    </section>

    <!-- Central Ecosystem Hub Visualization -->
    <section class="py-16 bg-stone-50 border-b border-stone-200/80">
        <div class="agri-container">
            <!-- Central Node Callout (Reference Central Hub Style) -->
            <div class="max-w-xl mx-auto text-center mb-12">
                <div class="inline-block p-7 rounded-3xl bg-[#0c365b] text-white shadow-xl border-4 border-[#00875a]/40">
                    <div class="h-16 w-16 mx-auto rounded-full bg-white flex items-center justify-center p-2.5 mb-3 shadow-md hover:scale-105 transition-transform">
                        <x-logo variant="icon-only" size="md" />
                    </div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#00875a] bg-emerald-950/40 px-3 py-1 rounded-full border border-emerald-500/30 inline-block mb-2">Central Ecosystem Hub</span>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Nxtperson Private Limited</h2>
                    <p class="text-xs text-slate-200 mt-2 max-w-md mx-auto">
                        Unified digital platform orchestrating data, AI quality grading, advisory intelligence, and marketplace transactions across all participants.
                    </p>
                </div>
            </div>

            <!-- 10 Stakeholder Groups Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                <!-- 1. Farmers -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl shrink-0">
                                🌾
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Smallholder Farmers</h3>
                                <span class="text-[10px] text-emerald-700 font-semibold uppercase tracking-wider">Primary Producer</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Access crop health AI diagnosis, hyper-local spray windows, transparent APMC rates, and certified inputs at competitive prices.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 self-start">
                        Interactive Demo Available
                    </span>
                </div>

                <!-- 2. FPOs & Cooperatives -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0">
                                🤝
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">FPOs & Cooperatives</h3>
                                <span class="text-[10px] text-amber-700 font-semibold uppercase tracking-wider">Aggregation Hub</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Consolidate member yields, manage collective input procurement, and access institutional buyers with verified quality certificates.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 self-start">
                        Roadmap Integration
                    </span>
                </div>

                <!-- 3. Input Manufacturers -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center text-xl shrink-0">
                                🏭
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Input Manufacturers</h3>
                                <span class="text-[10px] text-purple-700 font-semibold uppercase tracking-wider">Supply & R&D</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            List genuine seed genetics, bio-fertilizers, and micronutrients with direct demand visibility across Gujarat agro-zones.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 self-start">
                        Interactive Directory Live
                    </span>
                </div>

                <!-- 4. Produce Buyers & Industries -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center text-xl shrink-0">
                                🏢
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Produce Buyers & Industry</h3>
                                <span class="text-[10px] text-sky-700 font-semibold uppercase tracking-wider">Offtake & Processing</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Food processors, textile spinning mills, FMCG brands, and retail chains procuring graded, traceable raw commodities directly from source.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 self-start">
                        Roadmap Integration
                    </span>
                </div>

                <!-- 5. Agricultural Advisors -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center text-xl shrink-0">
                                👨‍🌾
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Agri Advisors & Experts</h3>
                                <span class="text-[10px] text-teal-700 font-semibold uppercase tracking-wider">Scientific Counsel</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Senior agronomists and soil scientists reviewing complex diagnostic escalations and tailoring regional crop protocols.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 self-start">
                        Interactive Demo Live
                    </span>
                </div>

                <!-- 6. Equipment & Tech Providers -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center text-xl shrink-0">
                                ⚙️
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Equipment Providers</h3>
                                <span class="text-[10px] text-rose-700 font-semibold uppercase tracking-wider">Hardware & IoT</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Suppliers of drip irrigation solenoids, soil telemetry probes, tractor attachments, and portable optical grading cameras.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 self-start">
                        Roadmap Integration
                    </span>
                </div>

                <!-- 7. Warehousing & Storage -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center text-xl shrink-0">
                                🏬
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Cold Storage & Warehouses</h3>
                                <span class="text-[10px] text-indigo-700 font-semibold uppercase tracking-wider">Post-Harvest Care</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Deesa cold belts for potatoes, controlled atmosphere chambers for bananas, and APMC godown integration.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-lg border border-stone-200 self-start">
                        Future Capability
                    </span>
                </div>

                <!-- 8. Logistics & Fleet -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0">
                                🚚
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Logistics & Transport</h3>
                                <span class="text-[10px] text-amber-700 font-semibold uppercase tracking-wider">Transit Routing</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Rural last-mile carriers, refrigerated freight trucks, and route optimization algorithms linking fields to buyers.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-lg border border-stone-200 self-start">
                        Future Capability
                    </span>
                </div>

                <!-- 9. Financial Institutions -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-green-100 text-green-800 flex items-center justify-center text-xl shrink-0">
                                💳
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Financial Institutions</h3>
                                <span class="text-[10px] text-green-700 font-semibold uppercase tracking-wider">Credit & Insurance</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Agri-credit, warehouse receipt financing (e-NWR), crop insurance underwriting, and digital payment escrow.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-lg border border-stone-200 self-start">
                        Future Capability
                    </span>
                </div>

                <!-- 10. Exporters & Global Trade -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-cyan-100 text-cyan-800 flex items-center justify-center text-xl shrink-0">
                                🚢
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Exporters & Global Corridors</h3>
                                <span class="text-[10px] text-cyan-700 font-semibold uppercase tracking-wider">Cross-Border</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Direct export of Grade-A Gujarat commodities (cotton, banana, cumin, chilli) to Middle East, Europe, and Southeast Asia.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-lg border border-stone-200 self-start">
                        Planned 2031 Vision
                    </span>
                </div>

                <!-- 11. APMC Market Yards -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-orange-100 text-orange-800 flex items-center justify-center text-xl shrink-0">
                                ⚖️
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">APMC Market Yards</h3>
                                <span class="text-[10px] text-orange-700 font-semibold uppercase tracking-wider">Mandi Benchmarks</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Transparent price discovery from Rajkot, Gondal, Unjha, Deesa, and Anand market yards ensuring open market liquidity.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 self-start">
                        Interactive Demo Live
                    </span>
                </div>

                <!-- 12. Regulators & Policy -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-stone-100 text-stone-800 flex items-center justify-center text-xl shrink-0">
                                🏛️
                            </div>
                            <div>
                                <h3 class="font-bold text-stone-900 text-base">Government & Regulators</h3>
                                <span class="text-[10px] text-stone-600 font-semibold uppercase tracking-wider">Standards & Subsidies</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed mb-4">
                            Alignment with AgriStack, PM-KISAN, NABARD FPO schemes, and national agricultural export promotion policies.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-stone-600 bg-stone-100 px-2.5 py-1 rounded-lg border border-stone-200 self-start">
                        Policy Alignment
                    </span>
                </div>

            </div>
        </div>
    </section>

</div>
@endsection
