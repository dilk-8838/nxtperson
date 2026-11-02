@extends('layouts.app')

@section('title', 'Product Catalog Management - Admin • NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('admin.index') }}" class="hover:text-purple-700 transition-colors">Admin Operations</a>
            <span>/</span>
            <span class="text-purple-800 font-semibold">Product Catalog Management</span>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-600 text-lg">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">✕</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm">
                <span class="font-bold">Please correct the following errors:</span>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-bold mb-2">
                    <span>📦</span> Product Catalog CRUD Operations
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Agri Input Formulations Directory
                </h1>
                <p class="text-stone-500 text-xs sm:text-sm mt-1">
                    Add certified bio-fertilizers, pesticides, hybrid seeds, and link them to crop packages.
                </p>
            </div>

            <!-- Action CTA -->
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('add-product-modal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>➕</span> Add New Product
                </button>
                <a href="{{ route('admin.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 font-bold text-xs sm:text-sm shadow-xs transition-colors">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs mb-8">
            <form action="{{ route('admin.products') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name, active ingredient..." class="w-full text-xs sm:text-sm rounded-xl border border-stone-300 py-2.5 pl-3.5 pr-8 focus:ring-2 focus:ring-purple-600 focus:outline-hidden">
                    @if(request('search'))
                        <a href="{{ route('admin.products', array_diff_key(request()->query(), ['search' => ''])) }}" class="absolute right-2.5 top-2.5 text-stone-400 hover:text-stone-600 text-sm">✕</a>
                    @endif
                </div>

                <div class="w-full sm:w-48">
                    <select name="category" onchange="this.form.submit()" class="w-full text-xs sm:text-sm rounded-xl border border-stone-300 py-2.5 px-3 focus:ring-2 focus:ring-purple-600">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-4 py-2.5 rounded-xl bg-stone-900 text-white text-xs sm:text-sm font-bold hover:bg-stone-800 transition-colors shrink-0 w-full sm:w-auto">
                    Apply Filter
                </button>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 text-stone-700 font-bold uppercase text-[10px] tracking-wider border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-3">Product Name & Formulation</th>
                            <th class="py-3 px-3">Category</th>
                            <th class="py-3 px-3">Manufacturer</th>
                            <th class="py-3 px-3">Price Guide</th>
                            <th class="py-3 px-3">Target Crops</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($products as $prod)
                            <tr class="hover:bg-stone-50/80 transition-colors">
                                <td class="py-3 px-3">
                                    <div class="font-bold text-stone-900 text-xs sm:text-sm">
                                        {{ $prod->name }}
                                        @if($prod->is_featured)
                                            <span class="ml-1 px-1.5 py-0.2 rounded text-[10px] bg-amber-100 text-amber-800 font-bold">Featured</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-stone-500 font-mono">{{ $prod->active_ingredient ?? 'Formulation' }}</div>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-800 capitalize">
                                        {{ str_replace('-', ' ', $prod->category_slug) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="font-medium text-stone-800">{{ $prod->manufacturer->name ?? 'Manufacturer' }}</span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap font-mono text-emerald-800 font-bold">
                                    {{ $prod->price_guide ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($prod->target_crops ?? [] as $tc)
                                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-stone-100 text-stone-600 capitalize">{{ $tc }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap space-x-1">
                                    <button type="button" onclick="document.getElementById('edit-product-modal-{{ $prod->id }}').classList.remove('hidden')" class="px-2 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs">
                                        Edit ✏️
                                    </button>

                                    <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs">
                                            Delete 🗑️
                                        </button>
                                    </form>

                                    <!-- Edit Modal -->
                                    <div id="edit-product-modal-{{ $prod->id }}" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4 text-left overflow-y-auto">
                                        <div class="bg-white rounded-3xl border border-stone-200 p-6 max-w-2xl w-full shadow-2xl my-8">
                                            <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                                                <h3 class="font-bold text-stone-900 text-base">Edit Product: {{ $prod->name }}</h3>
                                                <button type="button" onclick="document.getElementById('edit-product-modal-{{ $prod->id }}').classList.add('hidden')" class="text-stone-400 hover:text-stone-700">✕</button>
                                            </div>

                                            <form action="{{ route('admin.products.update', $prod->id) }}" method="POST" class="space-y-4">
                                                @csrf
                                                @method('PUT')
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Product Name *</label>
                                                        <input type="text" name="name" value="{{ $prod->name }}" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Manufacturer *</label>
                                                        <select name="manufacturer_id" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                            @foreach($manufacturers as $mfg)
                                                                <option value="{{ $mfg->id }}" {{ $prod->manufacturer_id == $mfg->id ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Category *</label>
                                                        <select name="category_slug" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                            @foreach($categories as $cat)
                                                                <option value="{{ $cat->slug }}" {{ $prod->category_slug == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Active Ingredient</label>
                                                        <input type="text" name="active_ingredient" value="{{ $prod->active_ingredient }}" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Package Size</label>
                                                        <input type="text" name="package_size" value="{{ $prod->package_size }}" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Price Guide</label>
                                                        <input type="text" name="price_guide" value="{{ $prod->price_guide }}" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-stone-700 mb-1">Description *</label>
                                                    <textarea name="description" rows="3" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">{{ $prod->description }}</textarea>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Usage Instructions</label>
                                                        <textarea name="usage_instructions" rows="2" class="w-full text-xs rounded-xl border border-stone-300 p-2 focus:ring-2 focus:ring-purple-600">{{ $prod->usage_instructions }}</textarea>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Safety Guidelines</label>
                                                        <textarea name="safety_guidelines" rows="2" class="w-full text-xs rounded-xl border border-stone-300 p-2 focus:ring-2 focus:ring-purple-600">{{ $prod->safety_guidelines }}</textarea>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-stone-700 mb-1">Target Crops</label>
                                                    <div class="flex flex-wrap gap-3 p-3 bg-stone-50 rounded-xl border border-stone-200">
                                                        @foreach($crops as $crop)
                                                            <label class="inline-flex items-center gap-1.5 text-xs text-stone-700 cursor-pointer">
                                                                <input type="checkbox" name="target_crops[]" value="{{ $crop->slug }}" {{ in_array($crop->slug, $prod->target_crops ?? []) ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                                <span>{{ $crop->en_name }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-4">
                                                    <label class="inline-flex items-center gap-2 text-xs font-bold text-stone-700 cursor-pointer">
                                                        <input type="checkbox" name="is_featured" value="1" {{ $prod->is_featured ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                        <span>Feature in Highlights</span>
                                                    </label>
                                                    <label class="inline-flex items-center gap-2 text-xs font-bold text-stone-700 cursor-pointer">
                                                        <input type="checkbox" name="is_active" value="1" {{ $prod->is_active ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                        <span>Active in Catalog</span>
                                                    </label>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                                                    <button type="button" onclick="document.getElementById('edit-product-modal-{{ $prod->id }}').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border border-stone-300 text-stone-700 text-xs font-semibold hover:bg-stone-100">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-xs">
                                                        Update Product
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400">
                                    No products matching your search filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-stone-100">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add New Product Modal -->
<div id="add-product-modal" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4 text-left overflow-y-auto">
    <div class="bg-white rounded-3xl border border-stone-200 p-6 max-w-2xl w-full shadow-2xl my-8">
        <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
            <h3 class="font-bold text-stone-900 text-base">Add New Agricultural Input to Catalog</h3>
            <button type="button" onclick="document.getElementById('add-product-modal').classList.add('hidden')" class="text-stone-400 hover:text-stone-700">✕</button>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Product Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Sardar Zinc-Fortified Urea" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Manufacturer *</label>
                    <select name="manufacturer_id" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                        <option value="">Select Manufacturer...</option>
                        @foreach($manufacturers as $mfg)
                            <option value="{{ $mfg->id }}">{{ $mfg->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Category *</label>
                    <select name="category_slug" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Active Ingredient</label>
                    <input type="text" name="active_ingredient" placeholder="e.g. 46% N + 0.5% Zn" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Package Size</label>
                    <input type="text" name="package_size" placeholder="e.g. 45 kg Sealed Bag" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Price Guide</label>
                    <input type="text" name="price_guide" placeholder="e.g. MRP ₹320 / Bag" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Description *</label>
                <textarea name="description" rows="3" required placeholder="Agronomic performance, formulation benefits, soil conditioning attributes..." class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Usage Instructions</label>
                    <textarea name="usage_instructions" rows="2" placeholder="Basal, top dressing, fertigation dosages..." class="w-full text-xs rounded-xl border border-stone-300 p-2 focus:ring-2 focus:ring-purple-600"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Safety Guidelines</label>
                    <textarea name="safety_guidelines" rows="2" placeholder="Storage temperature, PPE gear, re-entry interval..." class="w-full text-xs rounded-xl border border-stone-300 p-2 focus:ring-2 focus:ring-purple-600"></textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Target Crops</label>
                <div class="flex flex-wrap gap-3 p-3 bg-stone-50 rounded-xl border border-stone-200">
                    @foreach($crops as $crop)
                        <label class="inline-flex items-center gap-1.5 text-xs text-stone-700 cursor-pointer">
                            <input type="checkbox" name="target_crops[]" value="{{ $crop->slug }}" class="rounded text-purple-600 focus:ring-purple-500">
                            <span>{{ $crop->en_name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-4">
                <label class="inline-flex items-center gap-2 text-xs font-bold text-stone-700 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-purple-600 focus:ring-purple-500">
                    <span>Feature in Top Highlights</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('add-product-modal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border border-stone-300 text-stone-700 text-xs font-semibold hover:bg-stone-100">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
