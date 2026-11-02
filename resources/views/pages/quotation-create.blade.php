@extends('layouts.app')

@section('title', 'Submit Quotation for RFQ #' . $rfq->rfq_number . ' — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">

        {{-- Breadcrumb & Title --}}
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-400 mb-3">
                <a href="{{ route('rfqs.seller') }}" class="hover:text-emerald-400 transition">RFQ Inbox</a>
                <span>&rarr;</span>
                <a href="{{ route('rfqs.show', $rfq->id) }}" class="hover:text-emerald-400 transition">RFQ #{{ $rfq->rfq_number }}</a>
                <span>&rarr;</span>
                <span class="text-slate-200">Prepare Quotation</span>
            </nav>

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Prepare Formal Quotation</h1>
                    <p class="text-slate-400 text-sm mt-1">
                        Respond to buyer inquiry for <span class="text-white font-medium">{{ $rfq->title }}</span>
                    </p>
                </div>
                <a href="{{ route('rfqs.show', $rfq->id) }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    &larr; Cancel
                </a>
            </div>
        </div>

        {{-- Error Summary --}}
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-sm">
                <p class="font-bold mb-1">Please correct the following errors:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Buyer Request Summary Card --}}
        <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-6 space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-400">Buyer's RFQ Target Specifications</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Requested Quantity</span>
                    <span class="text-white font-bold">{{ number_format((float)$rfq->quantity, 1) }} {{ $rfq->unit }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Target Unit Price</span>
                    <span class="text-white font-bold">
                        {{ $rfq->target_price_per_unit ? '₹'.number_format((float)$rfq->target_price_per_unit, 2).' / '.$rfq->unit : 'Open / Unspecified' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Delivery Destination</span>
                    <span class="text-white font-semibold">{{ $rfq->delivery_district }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Required By Date</span>
                    <span class="text-white font-semibold">{{ $rfq->target_delivery_date?->format('d M Y') ?? 'Flexible' }}</span>
                </div>
            </div>

            @if($rfq->specifications)
                <div class="pt-3 border-t border-slate-700/60">
                    <span class="text-xs text-slate-400 block">Buyer Quality & Packaging Specifications:</span>
                    <p class="text-sm text-slate-200 mt-1 italic">{{ $rfq->specifications }}</p>
                </div>
            @endif
        </div>

        {{-- Quotation Submission Form --}}
        <form action="{{ route('quotations.store', $rfq->id) }}" method="POST" class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-6 space-y-6" id="quotationForm">
            @csrf

            <h2 class="text-base font-bold text-white border-b border-slate-700/60 pb-3">Your Commercial Proposal</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Quoted Quantity --}}
                <div>
                    <label for="quoted_quantity" class="block text-sm font-medium text-slate-300 mb-1">
                        Quoted Quantity <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="number" step="0.1" name="quoted_quantity" id="quoted_quantity" value="{{ old('quoted_quantity', $rfq->quantity) }}" required min="0.1" max="10000000"
                            class="flex-1 rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        <input type="text" name="unit" id="unit" value="{{ old('unit', $rfq->unit) }}" required
                            class="w-28 rounded-xl bg-slate-900 border border-slate-700 px-3 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm text-center">
                    </div>
                    <span class="text-xs text-slate-400 mt-1 block">Specify how much volume you are committing to supply.</span>
                </div>

                {{-- Unit Price --}}
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-slate-300 mb-1">
                        Offered Unit Price (₹) <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">₹</span>
                        <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price', $rfq->target_price_per_unit) }}" required min="0.01" max="1000000"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 pl-8 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="0.00">
                    </div>
                    <span class="text-xs text-slate-400 mt-1 block">Base price per unit excluding taxes & freight.</span>
                </div>

                {{-- Additional Charges --}}
                <div>
                    <label for="additional_charges" class="block text-sm font-medium text-slate-300 mb-1">
                        Freight, Handling & Packaging (₹)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">₹</span>
                        <input type="number" step="0.01" name="additional_charges" id="additional_charges" value="{{ old('additional_charges', 0) }}" min="0" max="1000000"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 pl-8 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="0.00">
                    </div>
                    <span class="text-xs text-slate-400 mt-1 block">Logistics, loading, or grading charges if applicable.</span>
                </div>

                {{-- Tax Amount --}}
                <div>
                    <label for="tax_amount" class="block text-sm font-medium text-slate-300 mb-1">
                        Applicable GST / Taxes (₹)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">₹</span>
                        <input type="number" step="0.01" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}" min="0" max="1000000"
                            class="w-full rounded-xl bg-slate-900 border border-slate-700 pl-8 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="0.00">
                    </div>
                    <span class="text-xs text-slate-400 mt-1 block">Applicable statutory taxes (if non-exempt produce).</span>
                </div>

                {{-- Estimated Delivery Date --}}
                <div>
                    <label for="estimated_delivery_date" class="block text-sm font-medium text-slate-300 mb-1">
                        Estimated Delivery Date <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="estimated_delivery_date" id="estimated_delivery_date" value="{{ old('estimated_delivery_date', $rfq->target_delivery_date?->format('Y-m-d') ?? date('Y-m-d', strtotime('+7 days'))) }}" required min="{{ date('Y-m-d') }}"
                        class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    <span class="text-xs text-slate-400 mt-1 block">When can the goods reach buyer's destination?</span>
                </div>

                {{-- Valid Until Date --}}
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-slate-300 mb-1">
                        Quotation Validity <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+14 days'))) }}" required min="{{ date('Y-m-d') }}"
                        class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    <span class="text-xs text-slate-400 mt-1 block">Date after which this pricing offer expires automatically.</span>
                </div>
            </div>

            {{-- Live Total Calculation Preview Box --}}
            <div class="p-5 rounded-xl bg-slate-900/80 border border-emerald-500/40 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase tracking-wider text-emerald-400 font-bold">Estimated Quotation Value</span>
                    <p class="text-xs text-slate-400 mt-0.5">Calculated as (Qty &times; Unit Price) + Freight + GST</p>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-emerald-300" id="liveTotalDisplay">₹0.00</span>
                    <span class="text-xs text-slate-400 block" id="liveSubtotalBreakdown">0 &times; ₹0.00</span>
                </div>
            </div>

            {{-- Quality Notes --}}
            <div>
                <label for="quality_notes" class="block text-sm font-medium text-slate-300 mb-1">
                    Quality, Moisture, Variety & Packaging Notes
                </label>
                <textarea name="quality_notes" id="quality_notes" rows="3"
                    class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                    placeholder="e.g. Moisture below 10%, graded uniform size, packed in 50kg new HDPE gunny bags...">{{ old('quality_notes') }}</textarea>
            </div>

            {{-- Terms & Conditions --}}
            <div>
                <label for="terms_and_conditions" class="block text-sm font-medium text-slate-300 mb-1">
                    Payment & Fulfilment Terms
                </label>
                <textarea name="terms_and_conditions" id="terms_and_conditions" rows="3"
                    class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                    placeholder="e.g. 50% advance on PO confirmation, balance upon APMC weighment inspection...">{{ old('terms_and_conditions') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ route('rfqs.show', $rfq->id) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition">
                    Submit Quotation &rarr;
                </button>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('quoted_quantity');
    const priceInput = document.getElementById('unit_price');
    const chargesInput = document.getElementById('additional_charges');
    const taxInput = document.getElementById('tax_amount');
    const totalDisplay = document.getElementById('liveTotalDisplay');
    const breakdownDisplay = document.getElementById('liveSubtotalBreakdown');

    function updateLiveTotal() {
        const qty = parseFloat(qtyInput?.value) || 0;
        const price = parseFloat(priceInput?.value) || 0;
        const charges = parseFloat(chargesInput?.value) || 0;
        const tax = parseFloat(taxInput?.value) || 0;

        const subtotal = qty * price;
        const total = subtotal + charges + tax;

        if (totalDisplay) {
            totalDisplay.textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        if (breakdownDisplay) {
            breakdownDisplay.textContent = `${qty} units @ ₹${price.toFixed(2)} + ₹${charges.toFixed(2)} extra + ₹${tax.toFixed(2)} tax`;
        }
    }

    [qtyInput, priceInput, chargesInput, taxInput].forEach(el => {
        el?.addEventListener('input', updateLiveTotal);
    });

    updateLiveTotal();
});
</script>
@endsection
