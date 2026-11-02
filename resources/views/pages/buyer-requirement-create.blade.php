@extends('layouts.app')

@section('title', 'Post Bulk Procurement Requirement — NxtPerson Agritech')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-3xl">
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('buyer.requirements.index') }}" class="hover:text-emerald-800">Requirements</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Post Requirement</span>
        </nav>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-stone-200">
            <div class="pb-6 border-b border-stone-200 mb-6">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-[#0c365b] uppercase">
                    Procurement Tender Specification
                </span>
                <h1 class="text-2xl font-black text-[#0c365b] mt-1">
                    Post Bulk Procurement Requirement
                </h1>
                <p class="text-xs text-stone-500 mt-0.5">
                    Define commodity specifications and delivery terms to request competitive quotations from certified sellers.
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

            <form action="{{ route('buyer.requirements.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Requirement Type -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">
                        Requirement Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-stone-200 hover:border-emerald-500 cursor-pointer has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                            <input type="radio" name="type" value="produce" checked class="text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <strong class="text-xs font-bold text-stone-900 block">🌾 Farm Produce (Crops)</strong>
                                <span class="text-[11px] text-stone-500">Procure cotton, groundnut, grains, potatoes, etc.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-stone-200 hover:border-blue-500 cursor-pointer has-[:checked]:border-[#0c365b] has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="type" value="input" class="text-[#0c365b] focus:ring-[#0c365b]">
                            <div>
                                <strong class="text-xs font-bold text-stone-900 block">🏢 Agricultural Inputs</strong>
                                <span class="text-[11px] text-stone-500">Procure bulk seeds, fertilizers, or equipment</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Title & Item Name -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Requirement Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. 100 Tonnes Shankar-6 Raw Cotton"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Item / Commodity Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="item_name" value="{{ old('item_name') }}" required placeholder="e.g. Raw Seed Cotton (Bt-Cotton)"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Crop / Category Link -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Target Crop (For Farm Produce)
                        </label>
                        <select name="crop_slug" class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                            <option value="">-- Select Crop (Optional) --</option>
                            @foreach($crops as $c)
                                <option value="{{ $c->slug }}" {{ old('crop_slug') === $c->slug ? 'selected' : '' }}>
                                    {{ $c->en_name }} ({{ $c->gu_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Input Category (For Agricultural Inputs)
                        </label>
                        <select name="category_slug" class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                            <option value="">-- Select Category (Optional) --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ old('category_slug') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Quantity & Target Price -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Target Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.1" name="target_quantity" value="{{ old('target_quantity') }}" required placeholder="e.g. 50"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Measurement Unit <span class="text-red-500">*</span>
                        </label>
                        <select name="unit" required class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                            <option value="tonne">Tonnes (1,000 kg)</option>
                            <option value="quintal">Quintals (100 kg)</option>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="bag">Bags</option>
                            <option value="packet">Packets</option>
                            <option value="litre">Litres</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Target Price / Unit (₹)
                        </label>
                        <input type="number" step="0.1" name="target_price_per_unit" value="{{ old('target_price_per_unit') }}" placeholder="e.g. 7200"
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
                                <option value="{{ $d }}" {{ old('delivery_district', $buyer->district) === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Desired Fulfilment Date
                        </label>
                        <input type="date" name="desired_delivery_date" value="{{ old('desired_delivery_date') }}"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Quality Specs & Notes -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Required Quality / Grade Specifications
                    </label>
                    <textarea name="quality_specifications" rows="2" placeholder="e.g. Moisture <= 8%, Staple length >= 28mm, Trash content <= 3%..."
                              class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">{{ old('quality_specifications') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Additional Delivery Notes / Terms
                    </label>
                    <textarea name="notes" rows="2" placeholder="e.g. Unloading at GIDC processing mill, APMC weighment certificate required..."
                              class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-stone-200">
                    <a href="{{ route('buyer.requirements.index') }}" class="px-5 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all">
                        Register Requirement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
