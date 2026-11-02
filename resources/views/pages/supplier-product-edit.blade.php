@extends('layouts.app')

@section('title', "Edit Product — {$product->name}")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-3xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('supplier.products.index') }}" class="hover:text-emerald-800">Inventory</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">{{ $product->name }}</span>
        </nav>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-stone-200">
            <div class="pb-6 border-b border-stone-200 mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-[#0c365b]">
                        Edit Product & Stock Level
                    </h1>
                    <p class="text-xs text-stone-500 mt-0.5">
                        {{ $product->name }} • {{ $organization->name }}
                    </p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $product->isAvailable() ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-700' }}">
                    {{ $product->isAvailable() ? 'Active in Stock' : 'Inactive / Out of Stock' }}
                </span>
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

            <form action="{{ route('supplier.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Product Name & Brand -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Brand Name
                        </label>
                        <input type="text" name="brand_name" value="{{ old('brand_name', $product->brand_name) }}"
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Category & Unit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_slug" required class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ old('category_slug', $product->category_slug) === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Sales Unit <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Price & Stock -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Unit Price (₹) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Min Order Qty <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="1" name="min_order_quantity" value="{{ old('min_order_quantity', $product->min_order_quantity) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Current Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="1" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">
                    </div>
                </div>

                <!-- Description & Specs -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="3" required class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Specifications
                    </label>
                    <textarea name="specifications" rows="2" class="w-full px-4 py-3 rounded-xl border border-stone-300 text-sm focus:ring-1 focus:ring-emerald-600">{{ old('specifications', $product->specifications) }}</textarea>
                </div>

                <!-- Availability toggle -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_available" value="1" id="is_available" {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-stone-300">
                    <label for="is_available" class="text-xs font-bold text-stone-800">
                        Mark product as available for purchase and procurement requests
                    </label>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-stone-200">
                    <a href="{{ route('supplier.products.index') }}" class="px-5 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
