@extends('layouts.app')

@section('title', 'Issue Request for Quotation (RFQ) — NxtPerson Agritech')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-3xl">
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('rfqs.index') }}" class="hover:text-emerald-800">RFQs</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Issue RFQ</span>
        </nav>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-stone-200">
            <div class="pb-6 border-b border-stone-200 mb-6">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#0c365b] uppercase">
                    Procurement Tender Creation
                </span>
                <h1 class="text-2xl font-black text-[#0c365b] mt-1">
                    Issue Request for Quotation (RFQ)
                </h1>
                <p class="text-xs text-stone-500 mt-0.5">
                    Specify delivery dates, volume requirements, and invite certified sellers to submit commercial bids.
                </p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs">
                    <strong class="font-bold block mb-1">Please correct the following errors:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('rfqs.store') }}" method="POST" class="space-y-6">
                @csrf

                @if($requirement)
                    <input type="hidden" name="procurement_requirement_id" value="{{ $requirement->id }}">
                    <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 text-xs">
                        <strong class="text-[#0c365b] block font-bold">Linked to Requirement: #{{ $requirement->requirement_number }}</strong>
                        <span class="text-stone-600">{{ $requirement->title }} ({{ $requirement->target_quantity }} {{ $requirement->unit }})</span>
                    </div>
                @endif

                @if($produceLot)
                    <input type="hidden" name="produce_lot_id" value="{{ $produceLot->id }}">
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-xs">
                        <strong class="text-emerald-900 block font-bold">RFQ for Produce Lot: #{{ $produceLot->lot_number }}</strong>
                        <span class="text-stone-600">{{ $produceLot->variety }} ({{ $produceLot->crop->en_name ?? 'Produce' }}) • Available: {{ $produceLot->available_quantity_kg }} kg</span>
                    </div>
                @endif

                @if($product)
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 text-xs">
                        <strong class="text-[#0c365b] block font-bold">RFQ for Product: {{ $product->name }}</strong>
                        <span class="text-stone-600">Stock available: {{ $product->stock_quantity }} {{ $product->unit }}</span>
                    </div>
                @endif

                @if($collectiveListing)
                    <input type="hidden" name="fpo_collective_listing_id" value="{{ $collectiveListing->id }}">
                    <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs">
                        <strong class="text-amber-900 block font-bold">RFQ for FPO Bulk Lot: #{{ $collectiveListing->listing_number }}</strong>
                        <span class="text-stone-600">{{ $collectiveListing->title }} • Available: {{ $collectiveListing->available_quantity_kg }} kg</span>
                    </div>
                @endif

                <!-- Title & Item Type -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            RFQ Tender Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $requirement?->title ?? $produceLot?->variety ?? $product?->name ?? '') }}" required placeholder="e.g. Procurement of 500 Quintal Organic Groundnut"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Commodity Category <span class="text-red-500">*</span>
                        </label>
                        <select name="item_type" required class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                            <option value="produce" {{ old('item_type', $produceLot ? 'produce' : ($requirement && $requirement->type === 'produce' ? 'produce' : '')) === 'produce' ? 'selected' : '' }}>Farm Produce</option>
                            <option value="supplier_product" {{ old('item_type', $product ? 'supplier_product' : ($requirement && $requirement->type === 'input' ? 'supplier_product' : '')) === 'supplier_product' ? 'selected' : '' }}>Agricultural Input</option>
                            <option value="collective_produce" {{ old('item_type', $collectiveListing ? 'collective_produce' : '') === 'collective_produce' ? 'selected' : '' }}>FPO Collective Bulk</option>
                        </select>
                    </div>
                </div>

                <!-- Quantity & Target Price -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Required Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.1" name="quantity" value="{{ old('quantity', $requirement?->target_quantity ?? $produceLot?->available_quantity_kg ?? $collectiveListing?->available_quantity_kg ?? 10) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Unit <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="unit" value="{{ old('unit', $requirement?->unit ?? $produceLot?->unit ?? $product?->unit ?? 'kg') }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Target Budget / Unit (₹)
                        </label>
                        <input type="number" step="0.1" name="target_price_per_unit" value="{{ old('target_price_per_unit', $requirement?->target_price_per_unit ?? '') }}" placeholder="Optional"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Delivery Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Destination District <span class="text-red-500">*</span>
                        </label>
                        <select name="delivery_district" required class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                            @foreach($districts as $d)
                                <option value="{{ $d }}" {{ old('delivery_district', $requirement?->delivery_district ?? $user->district) === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Delivery Required By <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="target_delivery_date" value="{{ old('target_delivery_date', $requirement?->desired_delivery_date?->format('Y-m-d') ?? now()->addDays(14)->format('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Delivery Address / Warehouse Unloading Bay
                    </label>
                    <input type="text" name="delivery_address" value="{{ old('delivery_address', $requirement?->delivery_address) }}" placeholder="e.g. Plot 42, GIDC Processing Zone, Anand"
                           class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                </div>

                <!-- Specifications & Quality Notes -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Quality / Commodity Specifications
                    </label>
                    <textarea name="specifications" rows="2" placeholder="Detail moisture limit, impurity tolerance, packaging requirements, or grading standards..."
                              class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">{{ old('specifications', $requirement?->quality_specifications) }}</textarea>
                </div>

                <!-- Invite Eligible Sellers (Multi-Select) -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Invite Selected Certified Sellers (Optional)
                    </label>
                    <p class="text-[11px] text-stone-500 mb-2">
                        Invited sellers receive automated notifications and immediate access to submit bids.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 rounded-2xl border border-stone-200 bg-stone-50/50 text-xs">
                        @foreach($availableFpos as $fpo)
                            <label class="flex items-center gap-2 p-2 rounded-xl hover:bg-white cursor-pointer">
                                <input type="checkbox" name="invited_sellers[]" value="fpo:{{ $fpo->id }}" class="rounded text-[#0c365b] focus:ring-[#0c365b]">
                                <div>
                                    <strong class="text-stone-900 block font-bold">🤝 {{ $fpo->name }}</strong>
                                    <span class="text-[10px] text-stone-500">{{ $fpo->district }} • FPO Collective</span>
                                </div>
                            </label>
                        @endforeach

                        @foreach($availableSuppliers as $sup)
                            <label class="flex items-center gap-2 p-2 rounded-xl hover:bg-white cursor-pointer">
                                <input type="checkbox" name="invited_sellers[]" value="supplier:{{ $sup->id }}" class="rounded text-[#0c365b] focus:ring-[#0c365b]">
                                <div>
                                    <strong class="text-stone-900 block font-bold">🏢 {{ $sup->name }}</strong>
                                    <span class="text-[10px] text-stone-500">{{ $sup->district }} • Input Supplier</span>
                                </div>
                            </label>
                        @endforeach

                        @foreach($availableFarmers as $far)
                            <label class="flex items-center gap-2 p-2 rounded-xl hover:bg-white cursor-pointer">
                                <input type="checkbox" name="invited_sellers[]" value="farmer:{{ $far->id }}" class="rounded text-[#0c365b] focus:ring-[#0c365b]">
                                <div>
                                    <strong class="text-stone-900 block font-bold">🌾 {{ $far->name }}</strong>
                                    <span class="text-[10px] text-stone-500">{{ $far->district }} • Certified Producer</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-stone-200">
                    <a href="{{ route('rfqs.index') }}" class="px-5 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all">
                        Publish RFQ Tender & Dispatch Invitations
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
