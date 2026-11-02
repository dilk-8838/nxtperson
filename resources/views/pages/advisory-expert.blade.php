@extends('layouts.app')

@section('title', 'Talk to Agri Expert - Agronomist Consultation • NxtPerson AgriTech')

@section('content')
<div class="py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('advisory.index') }}" class="hover:text-emerald-700 transition-colors">Farmer Services</a>
            <span>/</span>
            <span class="text-emerald-800 font-semibold">Talk to Agri Expert</span>
        </nav>

        <!-- Demonstration Notice Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 shadow-xs flex items-start gap-3">
            <span class="text-2xl shrink-0">⚠️</span>
            <div class="text-xs sm:text-sm text-amber-900">
                <span class="font-bold">{{ __('messages.simulated_tag') }}:</span>
                Agronomist escalation and live tele-consultation are simulated for demo evaluation. No outbound call or chargeable helpline connection is executed.
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Consultation Panel -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-3">
                    <span>👨‍🌾</span> Gujarat Agricultural Expert Desk
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mb-3">
                    Connect with Senior Agronomists
                </h1>
                <p class="text-stone-600 text-sm leading-relaxed mb-6">
                    Have complex pest infestations, unseasonal rainfall damage, or soil nutrient questions? Consult Gujarat state university certified agronomists and plant pathologists in your preferred language (Gujarati, Hindi, English).
                </p>

                <!-- Consultation Form Preview -->
                <div class="bg-stone-50 rounded-2xl border border-stone-200 p-6 mb-6">
                    <h2 class="text-base font-bold text-stone-900 mb-4 flex items-center gap-2">
                        <span>📝</span> Schedule Simulated Consultation Request
                    </h2>

                    <form onsubmit="event.preventDefault(); alert('Consultation request simulated successfully! In this demo environment, your ticket is logged for agronomist review.');" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Target Crop</label>
                                <select class="w-full text-sm rounded-xl border-stone-300 bg-white py-2.5 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option>Cotton (કપાસ) - Pink Bollworm</option>
                                    <option>Potato (બટાકા) - Late Blight</option>
                                    <option>Banana (કેળા) - Sigatoka Leaf Spot</option>
                                    <option>Chilli (મરચા) - Leaf Curl Virus</option>
                                    <option>Rice (ડાંગર) - Blast Disease</option>
                                    <option>Wheat (ઘઉં) - Rust & Termite</option>
                                    <option>Tomato (ટામેટા) - Fruit Borer</option>
                                    <option>Cauliflower (ફૂલકોબી) - Diamondback Moth</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Preferred Language</label>
                                <select class="w-full text-sm rounded-xl border-stone-300 bg-white py-2.5 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option>ગુજરાતી (Gujarati)</option>
                                    <option>हिंदी (Hindi)</option>
                                    <option>English</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Field Observation / Query</label>
                            <textarea rows="3" class="w-full text-sm rounded-xl border-stone-300 bg-white py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Describe symptoms: leaf yellowing, stem holes, drooping, or pesticide application queries..."></textarea>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xs text-stone-500 flex items-center gap-1">
                                <span>🔒</span> Free Demo Session • SLA: &lt; 15 mins
                            </span>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold text-sm hover:bg-emerald-800 shadow-xs transition-colors">
                                Submit Consultation Ticket →
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Agronomist Roster Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs">
                    <h2 class="text-base font-bold text-stone-900 mb-4 flex items-center gap-2">
                        <span>🎖️</span> Demonstration Agronomist Panel
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3 p-3 rounded-2xl bg-stone-50 border border-stone-200/60">
                            <div class="h-10 w-10 rounded-full bg-emerald-200 text-emerald-900 flex items-center justify-center font-bold text-sm shrink-0">
                                AG
                            </div>
                            <div>
                                <div class="font-bold text-stone-900 text-sm">Demonstration Agronomic Consultant</div>
                                <div class="text-xs text-stone-500">Cotton & Field Crops Specialist</div>
                                <div class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-semibold mt-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Available for Demo (Gujarati/English)
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-2xl bg-stone-50 border border-stone-200/60">
                            <div class="h-10 w-10 rounded-full bg-blue-200 text-blue-900 flex items-center justify-center font-bold text-sm shrink-0">
                                PP
                            </div>
                            <div>
                                <div class="font-bold text-stone-900 text-sm">Demonstration Plant Pathologist</div>
                                <div class="text-xs text-stone-500">Horticulture & Vegetable Diseases Desk</div>
                                <div class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-semibold mt-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Available for Demo (Hindi/Gujarati)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-900 text-white rounded-3xl p-6 shadow-md relative overflow-hidden">
                    <h2 class="text-base font-bold mb-2">Instant AI Diagnostic Option</h2>
                    <p class="text-xs text-emerald-200 leading-relaxed mb-4">
                        Don't want to wait for an agronomist? Upload a photo of the affected leaf to get instant diagnostic results from our neural vision model.
                    </p>
                    <a href="{{ route('advisory.upload') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-emerald-900 font-bold text-xs hover:bg-emerald-50 transition-colors">
                        📸 Upload Leaf Photo →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
