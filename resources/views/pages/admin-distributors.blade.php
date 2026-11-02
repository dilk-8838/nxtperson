@extends('layouts.app')

@section('title', 'Distributor Network Management - Admin • NxtPerson AgriTech')

@section('content')
<div class="py-8 sm:py-10 bg-stone-50 min-h-screen">
    <div class="agri-container">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('admin.index') }}" class="hover:text-purple-700 transition-colors">Admin Operations</a>
            <span>/</span>
            <span class="text-purple-800 font-semibold">Distributor Network Management</span>
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
                    <span>🏢</span> Gujarat Dealer & Stockist Network CRUD
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Authorized Taluka Input Distributors
                </h1>
                <p class="text-stone-500 text-xs sm:text-sm mt-1">
                    Register certified Gujarat agronomical stockists with verified 6-digit postal codes and pesticide retailer licenses.
                </p>
            </div>

            <!-- Action CTA -->
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('add-distributor-modal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5">
                    <span>➕</span> Register Distributor
                </button>
                <a href="{{ route('admin.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 font-bold text-xs sm:text-sm shadow-xs transition-colors">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs mb-8">
            <form action="{{ route('admin.distributors') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by business name, proprietor, or 6-digit pincode..." class="w-full text-xs sm:text-sm rounded-xl border border-stone-300 py-2.5 pl-3.5 pr-8 focus:ring-2 focus:ring-purple-600 focus:outline-hidden">
                    @if(request('search'))
                        <a href="{{ route('admin.distributors', array_diff_key(request()->query(), ['search' => ''])) }}" class="absolute right-2.5 top-2.5 text-stone-400 hover:text-stone-600 text-sm">✕</a>
                    @endif
                </div>

                <div class="w-full sm:w-48">
                    <select name="district" onchange="this.form.submit()" class="w-full text-xs sm:text-sm rounded-xl border border-stone-300 py-2.5 px-3 focus:ring-2 focus:ring-purple-600">
                        <option value="">All Districts</option>
                        @foreach($districts as $d)
                            <option value="{{ $d }}" {{ request('district') === $d ? 'selected' : '' }}>
                                {{ $d }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-4 py-2.5 rounded-xl bg-stone-900 text-white text-xs sm:text-sm font-bold hover:bg-stone-800 transition-colors shrink-0 w-full sm:w-auto">
                    Apply Filter
                </button>
            </form>
        </div>

        <!-- Distributors Table -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 text-stone-700 font-bold uppercase text-[10px] tracking-wider border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-3">Business & License</th>
                            <th class="py-3 px-3">Location & Pincode</th>
                            <th class="py-3 px-3">Proprietor Contact</th>
                            <th class="py-3 px-3">Authorized Inputs</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($distributors as $dist)
                            <tr class="hover:bg-stone-50/80 transition-colors">
                                <td class="py-3 px-3">
                                    <div class="font-bold text-stone-900 text-xs sm:text-sm">{{ $dist->business_name }}</div>
                                    <div class="text-[11px] font-mono text-stone-500">{{ $dist->license_number }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-stone-800">{{ $dist->district }} ({{ $dist->taluka }})</div>
                                    <div class="text-[11px] text-stone-500 flex items-center gap-1">
                                        <span>📮</span>
                                        <span class="font-mono font-bold text-emerald-800">{{ $dist->pincode }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="font-medium text-stone-800">{{ $dist->name }}</div>
                                    <div class="text-[11px] text-stone-500 font-mono">{{ $dist->phone }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($dist->authorized_categories ?? [] as $ac)
                                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-stone-100 text-stone-700 capitalize">{{ str_replace('-', ' ', $ac) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    @if($dist->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-500">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap space-x-1">
                                    <button type="button" onclick="document.getElementById('edit-dist-modal-{{ $dist->id }}').classList.remove('hidden')" class="px-2 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs">
                                        Edit ✏️
                                    </button>

                                    <form action="{{ route('admin.distributors.destroy', $dist->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this distributor?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs">
                                            Delete 🗑️
                                        </button>
                                    </form>

                                    <!-- Edit Modal -->
                                    <div id="edit-dist-modal-{{ $dist->id }}" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4 text-left overflow-y-auto">
                                        <div class="bg-white rounded-3xl border border-stone-200 p-6 max-w-xl w-full shadow-2xl my-8">
                                            <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                                                <h3 class="font-bold text-stone-900 text-base">Edit Distributor: {{ $dist->business_name }}</h3>
                                                <button type="button" onclick="document.getElementById('edit-dist-modal-{{ $dist->id }}').classList.add('hidden')" class="text-stone-400 hover:text-stone-700">✕</button>
                                            </div>

                                            <form action="{{ route('admin.distributors.update', $dist->id) }}" method="POST" class="space-y-4">
                                                @csrf
                                                @method('PUT')
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Business Name *</label>
                                                        <input type="text" name="business_name" value="{{ $dist->business_name }}" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Proprietor Name *</label>
                                                        <input type="text" name="name" value="{{ $dist->name }}" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">License Number *</label>
                                                        <input type="text" name="license_number" value="{{ $dist->license_number }}" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">6-Digit Gujarat Pincode *</label>
                                                        <input type="text" name="pincode" value="{{ $dist->pincode }}" required pattern="[0-9]{6}" maxlength="6" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 font-mono focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">District *</label>
                                                        <select name="district" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                            @foreach($districts as $d)
                                                                <option value="{{ $d }}" {{ $dist->district === $d ? 'selected' : '' }}>{{ $d }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Taluka *</label>
                                                        <input type="text" name="taluka" value="{{ $dist->taluka }}" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">Phone Number *</label>
                                                        <input type="text" name="phone" value="{{ $dist->phone }}" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-stone-700 mb-1">WhatsApp</label>
                                                        <input type="text" name="whatsapp" value="{{ $dist->whatsapp }}" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-stone-700 mb-1">Full Physical Street Address *</label>
                                                    <textarea name="address" rows="2" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">{{ $dist->address }}</textarea>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-stone-700 mb-1">Authorized Product Categories</label>
                                                    <div class="flex flex-wrap gap-3 p-3 bg-stone-50 rounded-xl border border-stone-200">
                                                        @foreach($categories as $cat)
                                                            <label class="inline-flex items-center gap-1.5 text-xs text-stone-700 cursor-pointer">
                                                                <input type="checkbox" name="authorized_categories[]" value="{{ $cat->slug }}" {{ in_array($cat->slug, $dist->authorized_categories ?? []) ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                                <span>{{ $cat->name }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="inline-flex items-center gap-2 text-xs font-bold text-stone-700 cursor-pointer">
                                                        <input type="checkbox" name="is_active" value="1" {{ $dist->is_active ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                        <span>Active in Search Directory</span>
                                                    </label>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                                                    <button type="button" onclick="document.getElementById('edit-dist-modal-{{ $dist->id }}').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border border-stone-300 text-stone-700 text-xs font-semibold hover:bg-stone-100">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-xs">
                                                        Save Changes
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
                                    No distributors matching your filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-stone-100">
                {{ $distributors->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add New Distributor Modal -->
<div id="add-distributor-modal" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4 text-left overflow-y-auto">
    <div class="bg-white rounded-3xl border border-stone-200 p-6 max-w-xl w-full shadow-2xl my-8">
        <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
            <h3 class="font-bold text-stone-900 text-base">Register Authorized Gujarat Distributor</h3>
            <button type="button" onclick="document.getElementById('add-distributor-modal').classList.add('hidden')" class="text-stone-400 hover:text-stone-700">✕</button>
        </div>

        <form action="{{ route('admin.distributors.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Business / Depot Name *</label>
                    <input type="text" name="business_name" required placeholder="e.g. Navsari Kisan Seva Kendra" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Proprietor Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Bharatbhai Desai" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">License Number *</label>
                    <input type="text" name="license_number" required placeholder="e.g. GJ-NVS-AGR-2024-0899" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">6-Digit Gujarat Pincode *</label>
                    <input type="text" name="pincode" required pattern="[0-9]{6}" maxlength="6" placeholder="e.g. 396445" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 font-mono focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">District *</label>
                    <select name="district" required class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                        <option value="">Select District...</option>
                        @foreach($districts as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Taluka *</label>
                    <input type="text" name="taluka" required placeholder="e.g. Jalalpore" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Phone Number *</label>
                    <input type="text" name="phone" required placeholder="e.g. +91 9876543230" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">WhatsApp</label>
                    <input type="text" name="whatsapp" placeholder="e.g. +91 9876543230" class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Full Physical Street Address *</label>
                <textarea name="address" rows="2" required placeholder="Shop No, Market Yard, Road..." class="w-full text-xs rounded-xl border border-stone-300 p-2.5 focus:ring-2 focus:ring-purple-600"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Authorized Categories</label>
                <div class="flex flex-wrap gap-3 p-3 bg-stone-50 rounded-xl border border-stone-200">
                    @foreach($categories as $cat)
                        <label class="inline-flex items-center gap-1.5 text-xs text-stone-700 cursor-pointer">
                            <input type="checkbox" name="authorized_categories[]" value="{{ $cat->slug }}" class="rounded text-purple-600 focus:ring-purple-500">
                            <span>{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('add-distributor-modal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border border-stone-300 text-stone-700 text-xs font-semibold hover:bg-stone-100">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs">
                    Register Stockist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
