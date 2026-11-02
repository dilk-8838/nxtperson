@extends('layouts.app')

@section('title', "{$lot->variety} ({$lot->approved_grade} Grade) — Produce Exchange")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-5xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('buyer.marketplace') }}" class="hover:text-emerald-800">Produce Exchange</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">{{ $lot->crop->en_name ?? ucfirst($lot->crop_slug) }} ({{ $lot->lot_number }})</span>
        </nav>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>✓</span> {{ session('success') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 text-sm">
                <p class="font-bold mb-1">Reservation failed:</p>
                <ul class="list-disc pl-5 text-xs space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Images & Quality Inspection Certificate -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Image Preview Card -->
                <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-stone-200/80">
                    <div class="relative aspect-16/10 rounded-2xl overflow-hidden bg-stone-100 border border-stone-200">
                        @php $primary = $lot->primaryImage; @endphp
                        @if($primary)
                            <img id="main-lot-img" src="{{ asset($primary->image_path) }}" alt="{{ $lot->variety }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">
                                {{ $lot->crop->icon ?? '🌾' }}
                            </div>
                        @endif

                        <div class="absolute top-3 left-3">
                            <span class="px-3 py-1 rounded-full text-xs font-black shadow-sm {{ $lot->getGradeBadgeClass() }}">
                                Grade {{ $lot->approved_grade ?? 'Good' }}
                            </span>
                        </div>

                        <div class="absolute top-3 right-3 bg-stone-900/80 backdrop-blur-md text-white text-xs font-semibold px-2.5 py-1 rounded-full">
                            📍 {{ $lot->district }}, Gujarat (PIN {{ $lot->pincode }})
                        </div>
                    </div>

                    <!-- Thumbnails row -->
                    @if($lot->images->count() > 1)
                        <div class="flex items-center gap-2 mt-3 overflow-x-auto pb-1">
                            @foreach($lot->images as $img)
                                <button type="button" onclick="document.getElementById('main-lot-img').src='{{ asset($img->image_path) }}'" class="w-16 h-16 rounded-xl overflow-hidden border-2 border-stone-200 hover:border-emerald-600 focus:border-emerald-600 shrink-0 transition-colors cursor-pointer">
                                    <img src="{{ asset($img->image_path) }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Certified Quality Assessment Card -->
                @php $assessment = $lot->latestAssessment; @endphp
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-stone-200/80">
                    <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                        <h3 class="text-base font-bold text-stone-900 flex items-center gap-2">
                            <span>🛡️</span> Certified Quality Assessment Certificate
                        </h3>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Verified Quality
                        </span>
                    </div>

                    @if($assessment)
                        <div class="p-4 rounded-2xl mb-4 {{ $assessment->grade === 'Good' ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-stone-800">Assessed Quality Class</span>
                                <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full {{ $assessment->grade === 'Good' ? 'bg-emerald-700 text-white' : 'bg-amber-600 text-white' }}">
                                    {{ $assessment->getGradeTitle() }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-700 mt-2 leading-relaxed">
                                {{ $assessment->assessment_explanation }}
                            </p>
                        </div>

                        <!-- Defect Analysis -->
                        <div class="mb-4">
                            <span class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-2">Quality & Visual Assessment Cues</span>
                            @if(!empty($assessment->observed_defects))
                                <ul class="space-y-1.5 text-xs text-stone-700">
                                    @foreach($assessment->observed_defects as $defect)
                                        <li class="flex items-start gap-2">
                                            <span class="text-emerald-700 shrink-0 mt-0.5">✓</span>
                                            <span>{{ $defect }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- Grading Specifications -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs bg-stone-50 p-3 rounded-2xl border border-stone-200">
                            <div>
                                <span class="text-[10px] text-stone-500 uppercase block font-bold">Grading Engine</span>
                                <strong class="text-stone-900 text-[11px]">{{ $assessment->engine_version }}</strong>
                            </div>
                            <div>
                                <span class="text-[10px] text-stone-500 uppercase block font-bold">Farming Method</span>
                                <strong class="text-stone-900 text-[11px] capitalize">{{ $lot->farming_method }}</strong>
                            </div>
                            <div>
                                <span class="text-[10px] text-stone-500 uppercase block font-bold">Harvest Date</span>
                                <strong class="text-stone-900 text-[11px]">{{ $lot->harvest_date->format('M d, Y') }}</strong>
                            </div>
                        </div>
                    @endif

                    @if($lot->description)
                        <div class="mt-4 pt-3 border-t border-stone-100">
                            <span class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1">Lot Quality & Storage Notes</span>
                            <p class="text-xs text-stone-600 leading-relaxed italic bg-stone-50 p-3 rounded-xl border border-stone-100">
                                "{{ $lot->description }}"
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right 1 Col: Reservation & Procurement Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-stone-200/80 sticky top-24">
                    <div class="pb-4 border-b border-stone-100 mb-5">
                        <span class="text-[10px] font-mono text-stone-400 block">{{ $lot->lot_number }}</span>
                        <h2 class="text-xl font-extrabold text-stone-900 mt-0.5">
                            {{ $lot->variety }}
                        </h2>
                        <span class="text-xs text-stone-500">
                            {{ $lot->crop->en_name ?? ucfirst($lot->crop_slug) }} • {{ $lot->district }}, Gujarat
                        </span>
                    </div>

                    <!-- Price Block -->
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 mb-5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Verified Buyer Rate</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-3xl font-black text-emerald-950">₹{{ number_format((float)($lot->buyer_sale_price_per_kg ?: 0), 2) }}</span>
                            <span class="text-xs text-emerald-800 font-semibold">/ kg</span>
                        </div>
                        <span class="text-[11px] text-emerald-700 mt-1 block">
                            (₹{{ number_format((float)($lot->buyer_sale_price_per_kg ?: 0) * 100, 2) }} / Quintal)
                        </span>
                    </div>

                    <!-- Stock Availability Bar -->
                    <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 mb-6 text-xs">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-stone-500">Available Stock:</span>
                            <strong class="text-stone-900 font-black text-sm">{{ number_format((float)$lot->available_quantity_kg, 1) }} kg</strong>
                        </div>
                        <div class="flex justify-between items-center text-[11px] text-stone-500">
                            <span>Equivalent:</span>
                            <span>{{ number_format((float)$lot->available_quantity_kg / 100, 1) }} Quintals</span>
                        </div>
                    </div>

                    @if($lot->isAvailableForBuyers())
                        <!-- Reservation Form -->
                        <form action="{{ route('buyer.lot.reserve', $lot->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="quantity_kg" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                                    Reserve Quantity (kg) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="1" name="quantity_kg" id="quantity_kg" 
                                       value="{{ old('quantity_kg', min(500, (float)$lot->available_quantity_kg)) }}" 
                                       min="1" max="{{ (float)$lot->available_quantity_kg }}" required 
                                       class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2.5 px-3.5 text-sm font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white"
                                       oninput="calculateTotal(this.value, {{ (float)($lot->buyer_sale_price_per_kg ?: 0) }})">
                                <span class="text-[11px] text-stone-500 mt-1 block">Max available: {{ number_format((float)$lot->available_quantity_kg, 1) }} kg</span>
                            </div>

                            <!-- Live Total Estimation -->
                            <div class="p-3 rounded-xl bg-stone-100 flex justify-between items-center text-xs">
                                <span class="text-stone-600 font-semibold">Total Estimated Cost:</span>
                                <strong id="calculated-total-cost" class="text-base font-black text-stone-900">
                                    ₹{{ number_format(min(500, (float)$lot->available_quantity_kg) * (float)($lot->buyer_sale_price_per_kg ?: 0), 2) }}
                                </strong>
                            </div>

                            <div>
                                <label for="notes" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                                    Procurement / Delivery Notes <span class="text-stone-400 font-normal">(Optional)</span>
                                </label>
                                <textarea name="notes" id="notes" rows="2" placeholder="Destination depot, preferred dispatch date, or quality tolerances..." class="w-full bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">{{ old('notes') }}</textarea>
                            </div>

                            <button type="submit" class="w-full py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-2xl shadow-md shadow-emerald-900/15 transition-all text-sm flex items-center justify-center gap-2 hover:shadow-lg">
                                <span>🛒</span>
                                <span>Confirm Produce Reservation</span>
                            </button>

                            <p class="text-[10px] text-stone-400 text-center leading-snug">
                                Concurrency-protected transaction. Quantity is atomically reserved upon confirmation.
                            </p>
                        </form>

                        <!-- Express Interest / Direct Inquiry to Farmer -->
                        <div class="mt-4 pt-4 border-t border-stone-200/80">
                            <form action="{{ route('buyer.lot.interest', $lot->id) }}" method="POST" class="space-y-2">
                                @csrf
                                <label for="interest_notes" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                                    Express Trade Interest / Inquiry
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" name="notes" id="interest_notes" placeholder="Ask question or state procurement intent..." class="flex-1 bg-stone-50 border border-stone-300 rounded-xl py-2 px-3 text-xs text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:bg-white">
                                    <button type="submit" class="px-3.5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-xl text-xs transition-colors whitespace-nowrap shadow-xs cursor-pointer">
                                        Send Inquiry
                                    </button>
                                </div>
                                <span class="text-[10px] text-stone-400 block">Notifies the producer securely without disclosing private phone numbers.</span>
                            </form>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-stone-100 text-center text-stone-600 text-xs font-semibold">
                            This produce lot is currently fully reserved or sold out.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateTotal(qty, pricePerKg) {
        const costEl = document.getElementById('calculated-total-cost');
        const numQty = parseFloat(qty) || 0;
        const total = numQty * pricePerKg;
        costEl.textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
</script>
@endsection
