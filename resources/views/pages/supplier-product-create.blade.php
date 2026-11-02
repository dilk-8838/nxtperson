@extends('layouts.app')

@section('title', "Add Agricultural Input Product — {$organization->name}")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-3xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('supplier.products.index') }}" class="hover:text-emerald-800">Inventory</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">New Product</span>
        </nav>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-stone-200">
            <div class="pb-6 border-b border-stone-200 mb-6">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#0c365b] uppercase">
                    Supplier Inventory Registration
                </span>
                <h1 class="text-2xl font-black text-[#0c365b] mt-1">
                    Add Agricultural Input Product
                </h1>
                <p class="text-xs text-stone-500 mt-0.5">
                    Register seeds, bio-fertilizers, chemicals, or equipment into {{ $organization->name }}'s verified catalogue.
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

            <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Product Name & Brand -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Bio-NPK Granular Fertilizer"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Brand / Manufacturer Name
                        </label>
                        <input type="text" name="brand_name" value="{{ old('brand_name', $organization->name) }}" placeholder="e.g. Kisan Seva Bio"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Category & Unit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Product Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_slug" required class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ old('category_slug') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Packaging / Sales Unit <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="unit" value="{{ old('unit', 'bag') }}" required placeholder="e.g. 50kg bag, 1L bottle, packet"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Pricing, Min Order & Stock Quantity -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Unit Price (₹) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price') }}" required placeholder="e.g. 850.00"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Min Order Qty <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="1" name="min_order_quantity" value="{{ old('min_order_quantity', 1) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Initial Stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="1" name="stock_quantity" value="{{ old('stock_quantity', 50) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Technical Specs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Active Ingredients / Technical Content
                        </label>
                        <input type="text" name="active_ingredient" value="{{ old('active_ingredient') }}" placeholder="e.g. Azotobacter + PSB + KMB"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Pack Size / Dimensions
                        </label>
                        <input type="text" name="package_size" value="{{ old('package_size') }}" placeholder="e.g. 50 kg HDPE Bag"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Description & Specifications -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Product Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="3" required placeholder="Detailed product composition, crop benefits, and agronomic application..."
                              class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Specifications & Dosage Guide
                    </label>
                    <textarea name="specifications" rows="2" placeholder="Recommended dosage, water dilution, or application guidelines..."
                              class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">{{ old('specifications') }}</textarea>
                </div>

                <!-- Product Photo -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Product Packaging Photo
                    </label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2 rounded-xl border border-stone-300 text-xs">
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-stone-200">
                    <a href="{{ route('supplier.products.index') }}" class="px-5 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all">
                        Publish Product to Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
