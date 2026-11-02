@extends('layouts.app')

@section('title', 'Farmer Dashboard - NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Farmer Dashboard</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                You are viewing the interactive <strong>Farmer Persona Portal</strong>. Farm plot telemetry, NDVI vegetative scores, and advisory tickets reflect simulated evaluation data for Gujarat agro-ecological zones.
            </div>
        </div>

        @if(session('role_message'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm flex items-center justify-between">
                <span>{{ session('role_message') }}</span>
                <span class="text-emerald-700 font-bold">✓ Active Session</span>
            </div>
        @endif

        <!-- Farmer Profile Header -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-4 sm:gap-5">
                    <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl bg-gradient-to-br from-[#0c365b] to-[#00875a] text-white flex items-center justify-center text-3xl sm:text-4xl shadow-md shrink-0">
                        👨‍🌾
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#00875a]/15 text-[#00875a] border border-[#00875a]/30">
                                Verified Gujarat Farmer
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                📍 {{ $profile->village ?? 'Gondal' }}, {{ $district }} Hub
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-900">
                                KYC Verified
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight">
                            {{ $user ? $user->name : 'Suresh Patel' }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 flex flex-wrap items-center gap-3">
                            <span>Holding: <strong>{{ $profile->farm_name ?? 'Patel Agro Farms' }}</strong></span>
                            <span>•</span>
                            <span>🌾 Total Land: <strong>{{ $profile->total_acreage ?? 18.5 }} Acres</strong> ({{ $profile->cultivated_acreage ?? 17.0 }} Cultivated)</span>
                            <span>•</span>
                            <span>💧 Irrigation: <strong>{{ ucfirst($profile->irrigation_type ?? 'drip') }}</strong></span>
                            <span>•</span>
                            <span>🌱 Soil: <strong>{{ $profile->soil_type ?? 'Medium Black Cotton Soil' }}</strong></span>
                        </p>

                        <!-- Profile Completion Progress Bar -->
                        <div class="mt-3 flex items-center gap-3 max-w-md">
                            <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                <div class="bg-gradient-to-r from-[#0c365b] to-[#00875a] h-full rounded-full transition-all duration-500" style="width: {{ $profile->completionPercentage() }}%;"></div>
                            </div>
                            <span class="text-xs font-bold text-[#00875a] shrink-0">{{ $profile->completionPercentage() }}% Profile Completed</span>
                        </div>
                    </div>
                </div>

                <!-- Action CTAs -->
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <button type="button" 
                            onclick="document.getElementById('edit-farmer-modal').classList.remove('hidden')"
                            class="px-4 py-2.5 rounded-full bg-white hover:bg-slate-50 text-[#0c365b] border border-slate-300 font-bold text-xs sm:text-sm transition-colors shadow-xs cursor-pointer flex items-center gap-1.5">
                        <span>✏️ Edit Farm Profile</span>
                    </button>
                    <a href="{{ route('farmer.sell.create') }}" class="px-5 py-2.5 rounded-full bg-[#0c365b] text-white font-bold text-xs sm:text-sm hover:bg-[#071f37] shadow-xs transition-colors flex items-center gap-2">
                        <span>📸 Sell Produce</span>
                    </a>
                    <a href="{{ route('advisory.index') }}" class="px-5 py-2.5 rounded-full bg-[#00875a] text-white font-bold text-xs sm:text-sm hover:bg-[#00704a] shadow-xs transition-colors flex items-center gap-2">
                        <span>🌱 Crop Advisory</span>
                    </a>
                </div>
            </div>

            <!-- Quick Service Shortcuts Bar -->
            <div class="mt-6 pt-6 border-t border-stone-100 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                <a href="{{ route('farmer.lots.index') }}" class="p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 hover:border-emerald-300 transition-all text-left group">
                    <span class="text-xl block mb-1">📋</span>
                    <span class="text-xs font-bold text-stone-900 group-hover:text-emerald-800 block">My Produce Lots</span>
                    <span class="text-[10px] text-stone-500">Edit &amp; withdraw stock</span>
                </a>
                <a href="{{ route('rfqs.seller') }}" class="p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 hover:border-emerald-300 transition-all text-left group">
                    <span class="text-xl block mb-1">⚖️</span>
                    <span class="text-xs font-bold text-stone-900 group-hover:text-emerald-800 block">RFQ Quotes</span>
                    <span class="text-[10px] text-stone-500">Buyer quote requests</span>
                </a>
                <a href="{{ route('orders.index') }}" class="p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 hover:border-emerald-300 transition-all text-left group">
                    <span class="text-xl block mb-1">📦</span>
                    <span class="text-xs font-bold text-stone-900 group-hover:text-emerald-800 block">Purchase Orders</span>
                    <span class="text-[10px] text-stone-500">Dispatches &amp; payments</span>
                </a>
                <a href="{{ route('advisory.index') }}" class="p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 hover:border-emerald-300 transition-all text-left group">
                    <span class="text-xl block mb-1">🌱</span>
                    <span class="text-xs font-bold text-stone-900 group-hover:text-emerald-800 block">AI Diagnostics</span>
                    <span class="text-[10px] text-stone-500">Scan leaf symptoms</span>
                </a>
                <a href="{{ route('weather.index') }}" class="p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 hover:border-emerald-300 transition-all text-left group">
                    <span class="text-xl block mb-1">🌦️</span>
                    <span class="text-xs font-bold text-stone-900 group-hover:text-emerald-800 block">Spray Forecast</span>
                    <span class="text-[10px] text-stone-500">Hyperlocal window</span>
                </a>
                <a href="{{ route('mandi.index') }}" class="p-3 rounded-2xl bg-stone-50 hover:bg-emerald-50 border border-stone-200/60 hover:border-emerald-300 transition-all text-left group">
                    <span class="text-xl block mb-1">📈</span>
                    <span class="text-xs font-bold text-stone-900 group-hover:text-emerald-800 block">APMC Prices</span>
                    <span class="text-[10px] text-stone-500">Benchmark rates</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Left 2 Cols: Enrolled Farm Plots & Active Advisories -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Enrolled Farm Plots Section -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🚜</span>
                            <h2 class="text-lg font-bold text-stone-900">Enrolled Farm Plots (3 Active Plots)</h2>
                        </div>
                        <span class="text-xs text-stone-500">Telemetry Sync: Just now</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($plots as $p)
                            <div class="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs hover:border-emerald-300 transition-all flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-2xl">{{ $p['icon'] }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $p['health_badge'] }}">
                                            {{ $p['health'] }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-stone-900 text-sm mb-0.5">{{ $p['name'] }}</h3>
                                    <p class="text-xs text-emerald-800 font-semibold mb-2">{{ $p['crop'] }} • {{ $p['area'] }}</p>
                                    
                                    <div class="space-y-1 text-[11px] text-stone-600 border-t border-stone-100 pt-2 mb-3">
                                        <div><strong>Location:</strong> {{ $p['village'] }}</div>
                                        <div><strong>Soil:</strong> {{ $p['soil'] }}</div>
                                        <div><strong>Stage:</strong> {{ $p['stage'] }}</div>
                                    </div>
                                </div>

                                <a href="{{ route('crops.show', $p['crop_slug']) }}" class="inline-flex items-center justify-center gap-1 w-full py-2 rounded-xl bg-stone-50 hover:bg-emerald-50 text-emerald-800 text-xs font-bold transition-colors">
                                    <span>Agronomic Guide →</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Advisory Tickets Table / History -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📋</span>
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-stone-900">Crop Advisory & Diagnosis Records</h2>
                                <p class="text-xs text-stone-500">Live SQLite demonstration records with simulated AI output and escalation state</p>
                            </div>
                        </div>
                        <a href="{{ route('advisory.history') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                            View All History →
                        </a>
                    </div>

                    @if($advisories->isEmpty())
                        <div class="p-8 text-center bg-stone-50 rounded-2xl border border-dashed border-stone-200">
                            <span class="text-3xl block mb-2">🌱</span>
                            <div class="text-sm font-bold text-stone-800 mb-1">No advisory tickets filed yet</div>
                            <p class="text-xs text-stone-500 mb-4 max-w-sm mx-auto">Upload a photo or submit symptoms of your crop to receive instant AI recommendations.</p>
                            <a href="{{ route('advisory.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 text-white text-xs font-bold">
                                Create First Advisory Ticket →
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                                        <th class="pb-2.5">Ticket #</th>
                                        <th class="pb-2.5">Crop</th>
                                        <th class="pb-2.5">Observed Issue</th>
                                        <th class="pb-2.5">AI Diagnosis</th>
                                        <th class="pb-2.5">Status</th>
                                        <th class="pb-2.5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100">
                                    @foreach($advisories as $adv)
                                        <tr class="hover:bg-stone-50/80 transition-colors">
                                            <td class="py-3 font-mono font-bold text-stone-900">
                                                {{ $adv->ticket_number }}
                                            </td>
                                            <td class="py-3 font-bold text-stone-800 capitalize">
                                                {{ $adv->crop_slug }}
                                            </td>
                                            <td class="py-3 text-stone-600 max-w-[160px] truncate">
                                                {{ $adv->issue_description }}
                                            </td>
                                            <td class="py-3 text-emerald-800 font-semibold max-w-[180px] truncate">
                                                {{ $adv->ai_diagnosis_title }}
                                            </td>
                                            <td class="py-3">
                                                @if($adv->status === 'escalated')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                                        👨‍🌾 Escalated
                                                    </span>
                                                @elseif($adv->status === 'resolved')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-200">
                                                        ✓ Resolved
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200">
                                                        ⚡ AI Analyzed
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 text-right">
                                                <a href="{{ route('advisory.show', $adv->ticket_number) }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-900">
                                                    View →
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Sidebar: Localized Weather & Live APMC Benchmarks -->
            <div class="space-y-6">
                <!-- Localized Weather Widget -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🌦️</span>
                            <h2 class="text-base font-bold text-stone-900">Rajkot Agro-Climate</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            {{ $weather['spray_window'] ?? 'Optimal Spray' }}
                        </span>
                    </div>

                    <div class="flex items-baseline gap-2 mb-3">
                        <span class="text-4xl font-extrabold text-stone-900">{{ $weather['temperature'] ?? 28 }}°C</span>
                        <span class="text-xs text-stone-500 font-medium">{{ $weather['condition'] ?? 'Clear Horizons' }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-stone-50 p-3 rounded-2xl mb-4">
                        <div>
                            <span class="text-[10px] text-stone-400 uppercase font-bold block">Humidity</span>
                            <span class="font-bold text-stone-800">{{ $weather['humidity'] ?? 58 }}%</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-stone-400 uppercase font-bold block">Wind</span>
                            <span class="font-bold text-stone-800">{{ $weather['wind_speed'] ?? 11 }} km/h ({{ $weather['wind_direction'] ?? 'NW' }})</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-stone-400 uppercase font-bold block">Rain Chance</span>
                            <span class="font-bold text-stone-800">{{ $weather['rainfall_probability'] ?? 12 }}%</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-stone-400 uppercase font-bold block">Soil Temp</span>
                            <span class="font-bold text-stone-800">24.2°C</span>
                        </div>
                    </div>

                    <a href="{{ route('weather.index') }}" class="block text-center w-full py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold transition-colors">
                        View 7-Day Micro-Climate Guide →
                    </a>
                </div>

                <!-- Linked FPO / Cooperative Affiliations -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🤝</span>
                            <h2 class="text-base font-bold text-stone-900">FPO Affiliation</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                            Cooperative Member
                        </span>
                    </div>

                    @if(isset($fpos) && $fpos->count() > 0)
                        <div class="space-y-3">
                            @foreach($fpos as $fpo)
                                <div class="p-3.5 rounded-2xl bg-teal-50/50 border border-teal-200 text-xs space-y-1">
                                    <div class="font-bold text-[#0c365b]">{{ $fpo->name }}</div>
                                    <div class="text-[11px] text-teal-800">{{ $fpo->organization_type }} • {{ $fpo->district }}</div>
                                    <div class="text-[11px] text-slate-500 pt-1 flex items-center justify-between border-t border-teal-100 mt-2">
                                        <span>Code: <strong>{{ $fpo->pivot->member_code ?? 'SKPC-MEM-001' }}</strong></span>
                                        <span>Shares: <strong>{{ $fpo->pivot->share_units ?? 10 }} Units</strong></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-xs text-slate-500 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="font-bold text-slate-700">No FPO Linked Yet</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Join a local Farmer Producer Co. to pool harvests and access wholesale seed pricing.</p>
                            <a href="{{ route('solutions.digital-farmer') }}" class="inline-block mt-2 text-xs font-bold text-[#00875a] hover:underline">Explore FPOs →</a>
                        </div>
                    @endif
                </div>

                <!-- Relevant Mandi Price Benchmarks -->
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📊</span>
                            <h2 class="text-base font-bold text-stone-900">APMC Mandi Benchmarks</h2>
                        </div>
                        <a href="{{ route('mandi.index') }}" class="text-xs font-bold text-emerald-700">All Yards</a>
                    </div>

                    <div class="space-y-3">
                        @foreach($mandiPrices as $m)
                            <div class="flex items-center justify-between p-2.5 rounded-2xl bg-stone-50 border border-stone-200/60">
                                <div>
                                    <div class="font-bold text-stone-900 text-xs">{{ $m->commodity_name }}</div>
                                    <div class="text-[11px] text-stone-500">Yard: {{ $m->yard_name }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-extrabold text-emerald-800 text-sm">₹{{ number_format($m->modal_price) }}</div>
                                    <div class="text-[10px] text-emerald-600 font-bold">{{ $m->trend_percentage ?? '+2.1%' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Farmer Profile -->
<div id="edit-farmer-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-[#0c365b]">Edit Farm Land & Crop Profile</h3>
            <button type="button" onclick="document.getElementById('edit-farmer-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form action="{{ route('farmer.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="modal_farm_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Farm / Holding Name</label>
                <input type="text" name="farm_name" id="modal_farm_name" value="{{ $profile->farm_name ?? 'Patel Agro Farms' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="modal_farmer_village" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Village</label>
                    <input type="text" name="village" id="modal_farmer_village" value="{{ $profile->village ?? 'Gondal' }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="modal_farmer_district" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">District</label>
                    <input type="text" name="district" id="modal_farmer_district" value="{{ $profile->district ?? 'Rajkot' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="modal_total_acreage" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Total Acreage</label>
                    <input type="number" step="0.1" name="total_acreage" id="modal_total_acreage" value="{{ $profile->total_acreage ?? 18.5 }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="modal_cultivated_acreage" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cultivated Acreage</label>
                    <input type="number" step="0.1" name="cultivated_acreage" id="modal_cultivated_acreage" value="{{ $profile->cultivated_acreage ?? 17.0 }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="modal_soil_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Soil Type</label>
                    <input type="text" name="soil_type" id="modal_soil_type" value="{{ $profile->soil_type ?? 'Medium Black Cotton Soil' }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="modal_irrigation_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Irrigation Method</label>
                    <select name="irrigation_type" id="modal_irrigation_type" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                        <option value="drip" {{ ($profile->irrigation_type ?? 'drip') === 'drip' ? 'selected' : '' }}>Drip (Micro-irrigation)</option>
                        <option value="borewell" {{ ($profile->irrigation_type ?? '') === 'borewell' ? 'selected' : '' }}>Borewell Tube Well</option>
                        <option value="canal" {{ ($profile->irrigation_type ?? '') === 'canal' ? 'selected' : '' }}>Canal Flow</option>
                        <option value="sprinkler" {{ ($profile->irrigation_type ?? '') === 'sprinkler' ? 'selected' : '' }}>Sprinkler System</option>
                        <option value="rainfed" {{ ($profile->irrigation_type ?? '') === 'rainfed' ? 'selected' : '' }}>Rainfed (Barani)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="modal_harvest_quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Estimated Harvest</label>
                    <input type="number" step="0.1" name="estimated_harvest_quantity" id="modal_harvest_quantity" value="{{ $profile->estimated_harvest_quantity ?? 245.0 }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="modal_harvest_unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Unit</label>
                    <input type="text" name="estimated_harvest_unit" id="modal_harvest_unit" value="{{ $profile->estimated_harvest_unit ?? 'Quintal' }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div>
                <label for="modal_harvest_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Expected Harvest Date</label>
                <input type="date" name="expected_harvest_date" id="modal_harvest_date" value="{{ $profile->expected_harvest_date ? $profile->expected_harvest_date->format('Y-m-d') : '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('edit-farmer-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#0c365b] text-white text-xs font-bold">Save Farm Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection

