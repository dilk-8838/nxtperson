@extends('layouts.app')

@section('title', "Product Catalogue & Inventory — {$organization->name}")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-[#0c365b]">
                        🏢 Supplier Organization Portal
                    </span>
                    <span class="text-xs text-stone-500 font-mono">{{ $organization->name }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#0c365b] tracking-tight">
                    Agricultural Inputs Catalogue & Inventory
                </h1>
                <p class="text-xs sm:text-sm text-stone-600 mt-1">
                    Manage certified hybrid seeds, micronutrients, fertilizers, crop protection, and agricultural machinery stock.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('supplier.dashboard') }}" class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100">
                    ← Supplier Dashboard
                </a>
                <a href="{{ route('supplier.products.create') }}" class="px-5 py-2.5 rounded-xl bg-[#00875a] hover:bg-[#00704a] text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                    <span>+</span> Add New Product
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700">✕</button>
            </div>
        @endif

        <!-- KPI Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Total SKUs</span>
                <div class="text-2xl font-black text-[#0c365b] mt-1">{{ number_format($stats['total_products']) }}</div>
                <span class="text-[11px] text-stone-500">Active catalog listings</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">In Stock</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['in_stock']) }}</div>
                <span class="text-[11px] text-emerald-600">Ready for procurement</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Low / Out of Stock</span>
                <div class="text-2xl font-black text-amber-700 mt-1">{{ number_format($stats['out_of_stock']) }}</div>
                <span class="text-[11px] text-amber-600">Requires stock replenishment</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs">
                <span class="text-stone-500 text-xs font-bold uppercase tracking-wider block">Total Inventory Value</span>
                <div class="text-2xl font-black text-[#0c365b] mt-1">₹{{ number_format((float)$stats['total_stock_value'], 2) }}</div>
                <span class="text-[11px] text-stone-500">Persisted valuation</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl p-4 border border-stone-200 mb-6">
            <form action="{{ route('supplier.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or brand..."
                           class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs focus:ring-1 focus:ring-emerald-600">
                </div>
                <div>
                    <select name="category" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs focus:ring-1 focus:ring-emerald-600">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs focus:ring-1 focus:ring-emerald-600">
                        <option value="">All Inventory Status</option>
                        <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock Only</option>
                        <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock / Inactive</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#0c365b] text-white font-bold text-xs rounded-xl hover:bg-[#08223a]">
                        Filter
                    </button>
                    <a href="{{ route('supplier.products.index') }}" class="px-3 py-2 bg-stone-100 text-stone-600 font-bold text-xs rounded-xl hover:bg-stone-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
            @if($products->isEmpty())
                <div class="py-16 text-center">
                    <span class="text-4xl block mb-2">📦</span>
                    <h3 class="text-sm font-bold text-stone-800">No products found in supplier inventory</h3>
                    <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                        Add hybrid seeds, fertilizers, chemicals, or equipment to make them visible to verified buyers.
                    </p>
                    <a href="{{ route('supplier.products.create') }}" class="inline-block mt-4 px-5 py-2.5 rounded-xl bg-[#00875a] text-white text-xs font-bold hover:bg-[#00704a]">
                        + Add First Product
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-stone-50 border-b border-stone-200 text-stone-600 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3.5 px-4">Product Details</th>
                                <th class="py-3.5 px-4">Category</th>
                                <th class="py-3.5 px-4">Unit Price</th>
                                <th class="py-3.5 px-4">Min Order</th>
                                <th class="py-3.5 px-4">Stock Level</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-800">
                            @foreach($products as $prod)
                                <tr class="hover:bg-stone-50/60 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-[#0c365b]">{{ $prod->name }}</div>
                                        <div class="text-[11px] text-stone-500 font-mono">
                                            {{ $prod->brand_name ?? $organization->name }} • Size: {{ $prod->package_size ?? $prod->unit }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-700">
                                            {{ $prod->category->name ?? ucfirst($prod->category_slug) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-black text-stone-900">
                                        ₹{{ number_format((float)($prod->unit_price ?? 0), 2) }} <span class="font-normal text-[10px] text-stone-500">/ {{ $prod->unit }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-stone-700">
                                        {{ number_format((float)($prod->min_order_quantity ?? 1), 1) }} {{ $prod->unit }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold {{ $prod->stock_quantity > 10 ? 'text-emerald-700' : 'text-amber-700 font-black' }}">
                                            {{ number_format((float)$prod->stock_quantity, 1) }} {{ $prod->unit }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($prod->isAvailable())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Active (In Stock)
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-200 text-stone-700">
                                                Unavailable
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right space-x-2">
                                        <a href="{{ route('supplier.products.edit', $prod->id) }}" class="font-bold text-emerald-700 hover:text-emerald-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('supplier.products.destroy', $prod->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to deactivate this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-bold text-red-600 hover:text-red-800">
                                                Deactivate
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-stone-200">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
