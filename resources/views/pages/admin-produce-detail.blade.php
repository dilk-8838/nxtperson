@extends('layouts.app')

@section('title', "Admin Audit: Lot #{$lot->lot_number} — Quality Override & Procurement")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-6xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('admin.index') }}" class="hover:text-emerald-800">Admin Portal</a>
            <span>/</span>
            <a href="{{ route('admin.produce.index') }}" class="hover:text-emerald-800">Produce Management</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">{{ $lot->lot_number }}</span>
        </nav>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>✓</span> {{ session('success') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>⚠️</span> {{ session('warning') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 font-bold">✕</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 text-sm">
                <p class="font-bold mb-1">Action could not be completed:</p>
                <ul class="list-disc pl-5 text-xs space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200/80 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-100">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="text-3xl p-2 rounded-xl bg-stone-100 border border-stone-200">
                            {{ $lot->crop->icon ?? '🌾' }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">
                                    {{ $lot->crop->en_name ?? ucfirst($lot->crop_slug) }} — {{ $lot->variety }}
                                </h1>
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $lot->getStatusBadgeClass() }}">
                                    {{ $lot->getStatusLabel() }}
                                </span>
                            </div>
                            <span class="text-xs text-stone-500 font-mono">Lot Reference: {{ $lot->lot_number }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1.5 rounded-xl font-mono text-xs bg-stone-100 border border-stone-200 text-stone-700">
                        Submitted: {{ $lot->created_at->format('M d, Y H:i') }}
                    </span>
                    <a href="{{ route('admin.produce.index') }}" class="px-4 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-semibold">
                        ← Back to List
                    </a>
                </div>
            </div>

            <!-- Farmer & Lot Metadata Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-6 text-xs">
                <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] text-stone-500 uppercase font-bold block">Origin Farmer</span>
                    <strong class="text-sm font-black text-stone-900 block mt-0.5">{{ $lot->farmer->name ?? 'Demo Farmer' }}</strong>
                    <span class="text-stone-500">{{ $lot->farmer->phone ?? 'N/A' }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] text-stone-500 uppercase font-bold block">Location & PIN</span>
                    <strong class="text-sm font-black text-stone-900 block mt-0.5">{{ $lot->district }}, Gujarat</strong>
                    <span class="text-stone-500">{{ $lot->taluka ?? 'District Yard' }} • PIN {{ $lot->pincode }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] text-stone-500 uppercase font-bold block">Declared Batch</span>
                    <strong class="text-sm font-black text-stone-900 block mt-0.5">{{ number_format((float)$lot->quantity, 1) }} {{ ucfirst($lot->unit) }}</strong>
                    <span class="text-stone-500">Standardized: {{ number_format((float)$lot->quantity_in_kg, 1) }} kg</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200">
                    <span class="text-[10px] text-stone-500 uppercase font-bold block">Harvest Telemetry</span>
                    <strong class="text-sm font-black text-stone-900 block mt-0.5">{{ $lot->harvest_date->format('M d, Y') }}</strong>
                    <span class="text-stone-500 capitalize">{{ $lot->farming_method }}</span>
                </div>
            </div>
        </div>

        <!-- Photographs Gallery -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200/80 mb-8">
            <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span>📸</span> Farmer Uploaded Inspection Photographs ({{ $lot->images->count() }})
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($lot->images as $img)
                    <div class="relative group rounded-2xl overflow-hidden border border-stone-200 bg-stone-100 aspect-square shadow-2xs">
                        <img src="{{ asset($img->image_path) }}" alt="Produce" class="w-full h-full object-cover">
                        <div class="absolute inset-x-0 bottom-0 p-2 bg-gradient-to-t from-black/80 to-transparent text-[10px] text-white">
                            {{ $img->original_name ?? 'Batch Photograph' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Left Col: Quality Grading Assessment & Manual Override -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-stone-200/80 space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-stone-900 pb-3 border-b border-stone-100 flex items-center gap-2">
                        <span>🔬</span> Quality Grading & Visual Defects
                    </h3>

                    @php $latest = $lot->latestAssessment; @endphp
                    @if($latest)
                        <div class="p-4 rounded-2xl my-4 {{ $latest->grade === 'Good' ? 'bg-emerald-50 border border-emerald-200' : ($latest->grade === 'Intermediate' ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200') }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase text-stone-700">System Assessed Grade</span>
                                <span class="text-xs font-black px-3 py-1 rounded-full text-white {{ $latest->grade === 'Good' ? 'bg-emerald-600' : ($latest->grade === 'Intermediate' ? 'bg-amber-600' : 'bg-red-600') }}">
                                    Grade {{ $latest->grade }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-700 mt-2 leading-relaxed">
                                {{ $latest->assessment_explanation }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <span class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1.5">Detected Visual Defect Cues</span>
                            <ul class="text-xs space-y-1 text-stone-700">
                                @foreach($latest->observed_defects ?? [] as $def)
                                    <li class="flex items-center gap-1.5">
                                        <span class="text-emerald-700">✓</span> {{ $def }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="text-[11px] text-stone-500 bg-stone-50 p-3 rounded-xl border border-stone-200 space-y-1">
                            <div>Confidence: <strong>{{ $latest->confidence_score }}</strong></div>
                            <div>Engine Version: <strong>{{ $latest->engine_version }}</strong></div>
                            <div>Physical Sorting Required: <strong>{{ $latest->manual_verification_required ? 'Yes' : 'No' }}</strong></div>
                        </div>
                    @endif
                </div>

                <!-- Manual Grade Override Form -->
                <div class="pt-5 border-t border-stone-200">
                    <h4 class="text-xs font-bold text-stone-900 uppercase tracking-wider mb-3">
                        Manual Agronomist Grade Override
                    </h4>
                    <form action="{{ route('admin.produce.override-grade', $lot->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label for="override_grade" class="block text-xs font-semibold text-stone-700 mb-1">Select Verified Grade</label>
                            <select name="grade" id="override_grade" required class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                                <option value="Good" {{ $lot->approved_grade === 'Good' ? 'selected' : '' }}>Good (Grade A Premium)</option>
                                <option value="Intermediate" {{ $lot->approved_grade === 'Intermediate' ? 'selected' : '' }}>Intermediate (Grade B Commercial)</option>
                                <option value="Bad" {{ $lot->approved_grade === 'Bad' ? 'selected' : '' }}>Bad (Grade C Substandard)</option>
                            </select>
                        </div>

                        <div>
                            <label for="override_notes" class="block text-xs font-semibold text-stone-700 mb-1">Agronomist Audit Notes <span class="text-red-500">*</span></label>
                            <textarea name="override_notes" id="override_notes" rows="2" required placeholder="State physical or high-res verification reasons for grade confirmation/override..." class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">{{ old('override_notes', $lot->admin_notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-stone-900 hover:bg-stone-800 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                            Submit Grade Override / Verification
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Col: Procurement Price Approval & Publication State Actions -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-stone-200/80 space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-stone-900 pb-3 border-b border-stone-100 flex items-center gap-2">
                        <span>💰</span> Procurement & Marketplace Publication
                    </h3>

                    <!-- Benchmark Guidance Box -->
                    <div class="p-4 rounded-2xl bg-stone-100 text-xs text-stone-700 my-4 space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-stone-500">Mandi Benchmark Yard:</span>
                            <strong>{{ $pricing['benchmark_mandi_yard'] }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Benchmark Rate:</span>
                            <strong>₹{{ number_format((float)$pricing['benchmark_modal_price_qtl'], 2) }} / Qtl (₹{{ number_format((float)$pricing['indicative_price_per_kg'], 2) }}/kg)</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Estimated Lot Valuation:</span>
                            <strong class="text-emerald-800 text-sm">₹{{ number_format((float)$pricing['estimated_lot_valuation'], 2) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- 1. Price Approval Form -->
                <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200">
                    <h4 class="text-xs font-bold text-blue-950 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span>1️⃣</span> Step 1: Approve Procurement & Sale Rates
                    </h4>
                    <form action="{{ route('admin.produce.approve-procurement', $lot->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="approved_procurement_price_per_kg" class="block text-[11px] font-bold text-stone-700 mb-1">
                                    Procurement Rate (₹/kg)
                                </label>
                                <input type="number" step="0.5" name="approved_procurement_price_per_kg" id="approved_procurement_price_per_kg" 
                                       value="{{ old('approved_procurement_price_per_kg', $lot->approved_procurement_price_per_kg ?? $pricing['indicative_price_per_kg']) }}" 
                                       required class="w-full bg-white border border-stone-300 rounded-xl py-2 px-3 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label for="buyer_sale_price_per_kg" class="block text-[11px] font-bold text-stone-700 mb-1">
                                    Buyer Sale Rate (₹/kg)
                                </label>
                                <input type="number" step="0.5" name="buyer_sale_price_per_kg" id="buyer_sale_price_per_kg" 
                                       value="{{ old('buyer_sale_price_per_kg', $lot->buyer_sale_price_per_kg ?? round($pricing['indicative_price_per_kg'] * 1.10, 2)) }}" 
                                       required class="w-full bg-white border border-stone-300 rounded-xl py-2 px-3 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-blue-600">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition-colors">
                            Approve Procurement Rates
                        </button>
                    </form>
                </div>

                <!-- 2. Purchase Confirmation Form -->
                <div class="p-4 rounded-2xl bg-teal-50/60 border border-teal-200">
                    <h4 class="text-xs font-bold text-teal-950 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span>2️⃣</span> Step 2: Confirm Purchase & Physical Quantity
                    </h4>
                    <form action="{{ route('admin.produce.confirm-purchase', $lot->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label for="confirmed_quantity_kg" class="block text-[11px] font-bold text-stone-700 mb-1">
                                Confirmed Intake Quantity (kg)
                            </label>
                            <input type="number" step="1" name="confirmed_quantity_kg" id="confirmed_quantity_kg" 
                                   value="{{ old('confirmed_quantity_kg', $lot->confirmed_quantity_kg ?: $lot->quantity_in_kg) }}" 
                                   required class="w-full bg-white border border-stone-300 rounded-xl py-2 px-3 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-teal-600">
                        </div>

                        <button type="submit" class="w-full py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-xl text-xs font-bold transition-colors">
                            Confirm Purchase Contract with Farmer
                        </button>
                    </form>
                </div>

                <!-- 3. Publish Stock to Marketplace -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300">
                    <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <span>3️⃣</span> Step 3: Publish Stock & Dispatch Buyer Alerts
                    </h4>
                    <p class="text-[11px] text-emerald-800 mb-3">
                        Transitions lot to <strong>INVENTORY_AVAILABLE</strong> and dispatches real-time notifications to all eligible registered buyers.
                    </p>

                    <form action="{{ route('admin.produce.publish', $lot->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label for="publish_sale_price" class="block text-[11px] font-bold text-stone-700 mb-1">
                                Final Buyer Marketplace Rate (₹/kg)
                            </label>
                            <input type="number" step="0.5" name="buyer_sale_price_per_kg" id="publish_sale_price" 
                                   value="{{ old('buyer_sale_price_per_kg', $lot->buyer_sale_price_per_kg ?? round(($lot->approved_procurement_price_per_kg ?? $pricing['indicative_price_per_kg']) * 1.10, 2)) }}" 
                                   required class="w-full bg-white border border-stone-300 rounded-xl py-2 px-3 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                        </div>

                        <button type="submit" class="w-full py-3 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-900/15 transition-all flex items-center justify-center gap-2 hover:shadow-lg">
                            <span>📡</span>
                            <span>Publish Stock & Dispatch Real-Time Alerts</span>
                            <span>→</span>
                        </button>
                    </form>
                </div>

                <!-- 4. Reject Lot -->
                <div class="pt-2">
                    <form action="{{ route('admin.produce.reject', $lot->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this produce lot?');" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="rejection_reason" placeholder="Rejection reason..." required class="flex-1 bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900">
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-colors shrink-0">
                            Reject Lot
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
