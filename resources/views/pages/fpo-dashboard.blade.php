@extends('layouts.app')

@section('title', 'FPO Cooperative Management — ' . $organization->name)

@section('content')
<div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl mx-auto px-4 sm:px-6 space-y-8">

        <!-- Top Header & Status Bar -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-teal-100 text-teal-900 border border-teal-200">
                        🤝 {{ $organization->organization_type }}
                    </span>

                    @if($organization->isApproved())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Verified Producer Organization</span>
                        </span>
                    @elseif($organization->verification_status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Verification Pending Review</span>
                        </span>
                    @elseif($organization->verification_status === 'rejected')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span>Application Rejected</span>
                        </span>
                    @elseif($organization->verification_status === 'suspended')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Account Suspended</span>
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight">
                    {{ $organization->name }}
                </h1>
                <p class="text-sm text-slate-600 max-w-3xl">
                    {{ $organization->description ?? 'Farmer producer organization aggregating smallholder acreage for collective bargaining and quality produce trading.' }}
                </p>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-slate-500 pt-1">
                    <span>📍 {{ $organization->registered_address }}, {{ $organization->district }}, {{ $organization->state }}</span>
                    <span>📑 Reg/CIN: <strong class="text-slate-700">{{ $organization->registration_number }}</strong></span>
                    @if($organization->incorporation_year)
                        <span>📅 Est: <strong class="text-slate-700">{{ $organization->incorporation_year }}</strong></span>
                    @endif
                    @if($organization->bank_name)
                        <span>🏦 Bank: <strong class="text-slate-700">{{ $organization->bank_name }}</strong></span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('fpo.gate-entries.index') }}" 
                   class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-bold transition-all shadow-md shadow-emerald-900/30 flex items-center gap-1.5">
                    <span>🚛</span>
                    <span>Gate Operations & Weighment &rarr;</span>
                </a>
                <a href="{{ route('fpo.collective.index') }}" 
                   class="px-4 py-2.5 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <span>🌾</span>
                    <span>Collective Bulk Lots &rarr;</span>
                </a>
                <button type="button" 
                        onclick="document.getElementById('edit-fpo-modal').classList.remove('hidden')"
                        class="px-4 py-2.5 rounded-2xl border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700 transition-all shadow-xs cursor-pointer">
                    ✏️ Edit FPO Details
                </button>
                <button type="button" 
                        onclick="document.getElementById('add-farmer-modal').classList.remove('hidden')"
                        class="px-4 py-2.5 rounded-2xl bg-[#0c365b] hover:bg-[#071f37] text-white text-xs sm:text-sm font-bold transition-all shadow-xs cursor-pointer">
                    + Link Member Farmer
                </button>
            </div>
        </div>

        @if($organization->verification_status === 'pending')
            <div class="p-4 sm:p-5 rounded-3xl bg-amber-50/90 border border-amber-300/80 text-amber-900 flex items-start gap-3.5 shadow-xs">
                <span class="text-2xl shrink-0">⏳</span>
                <div>
                    <h3 class="text-sm font-bold">FPO Legal Verification Pending</h3>
                    <p class="text-xs sm:text-sm text-amber-800 mt-0.5">
                        Your cooperative / FPC legal credentials (CIN: {{ $organization->registration_number }}) have been submitted. An administrator will verify the registered society documentation. Member farmers can be linked immediately, while collective produce marketing will unlock upon approval.
                    </p>
                </div>
            </div>
        @elseif($organization->verification_status === 'rejected')
            <div class="p-4 sm:p-5 rounded-3xl bg-red-50 border border-red-300 text-red-900 flex items-start gap-3.5 shadow-xs">
                <span class="text-2xl shrink-0">⚠️</span>
                <div>
                    <h3 class="text-sm font-bold">Verification Application Rejected</h3>
                    <p class="text-xs sm:text-sm text-red-800 mt-0.5">
                        {{ $organization->rejection_reason ?? 'The organization registration number or society certificate could not be validated. Please update your legal credentials.' }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Aggregate Real Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Active Farmers</span>
                    <span class="text-xl">👨‍🌾</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#0c365b]">{{ $activeMemberCount }}</div>
                <p class="text-xs text-slate-500 mt-1">Registered shareholder farmers</p>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Aggregated Land</span>
                    <span class="text-xl">🗺️</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#00875a]">{{ number_format($totalAcreage, 1) }} <span class="text-sm font-semibold text-slate-500">Acres</span></div>
                <p class="text-xs text-slate-500 mt-1">Sum of verified member farm acreage</p>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Member Crops</span>
                    <span class="text-xl">🌱</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#0c365b]">{{ count($memberCrops) }}</div>
                <p class="text-xs text-slate-500 mt-1">
                    @if(count($memberCrops) > 0)
                        {{ implode(', ', array_slice($memberCrops, 0, 3)) }}
                    @else
                        No crops registered
                    @endif
                </p>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Total Share Capital</span>
                    <span class="text-xl">🪙</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                    {{ $memberships->sum('share_units') }} <span class="text-sm font-semibold text-slate-500">Units</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Cooperative share distribution</p>
            </div>
        </div>

        <!-- Farmer Members Directory Table -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#0c365b]">Farmer Shareholder Members</h2>
                    <p class="text-xs text-slate-500">Farmers linked to {{ $organization->name }} without duplicating personal records</p>
                </div>
                <button type="button" 
                        onclick="document.getElementById('add-farmer-modal').classList.remove('hidden')"
                        class="px-4 py-2 rounded-xl bg-emerald-50 text-[#00875a] border border-emerald-200 hover:bg-emerald-100 text-xs font-bold transition-colors cursor-pointer self-start sm:self-auto">
                    + Link Farmer by ID / Phone
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-3 px-4">Member / Farmer</th>
                            <th class="py-3 px-4">Member Code</th>
                            <th class="py-3 px-4">Village / District</th>
                            <th class="py-3 px-4">Holding Acreage</th>
                            <th class="py-3 px-4">Cultivated Crops</th>
                            <th class="py-3 px-4">Shares</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($memberships as $mem)
                            @php
                                $farmer = $mem->farmerUser;
                                $profile = $farmer?->farmerProfile;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-[#00875a] flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ substr($farmer->name ?? 'F', 0, 1) }}
                                        </div>
                                        <div>
                                            <div>{{ $farmer->name ?? 'Unknown Farmer' }}</div>
                                            <div class="text-[11px] text-slate-400 font-normal">{{ $farmer->phone ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-700">
                                    {{ $mem->member_code }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $profile->village ?? 'Village' }}, {{ $farmer->district ?? 'Gujarat' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-[#0c365b]">
                                    {{ $profile->total_acreage ?? 0 }} Acres
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    @if(!empty($profile->crops_cultivated))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($profile->crops_cultivated as $c)
                                                <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] capitalize">
                                                    {{ $c }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-700">
                                    {{ $mem->share_units }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($mem->status === 'active')
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] capitalize">{{ $mem->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <form action="{{ route('fpo.members.status', $mem->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $mem->status === 'active' ? 'suspended' : 'active' }}">
                                        <button type="submit" class="text-[11px] font-semibold {{ $mem->status === 'active' ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }} hover:underline cursor-pointer">
                                            {{ $mem->status === 'active' ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    No farmers linked to this FPO yet. Click "+ Link Member Farmer" to add registered farmers.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Physical Operations Banner (Batch 3 Scope Reminder) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#0c365b]">FPO Collective Operations Roadmap</h2>
                    <p class="text-xs text-slate-500">Physical yard & grading integrations scheduled for Batch 3</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200">
                    Batch 1 Foundation Active
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5 opacity-80">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-xs text-slate-900">🚛 Gate Entry & Weighbridge</span>
                        <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">Batch 3</span>
                    </div>
                    <p class="text-xs text-slate-500">Digital weighbridge slip logging and QR code gate entry slips for farmer deliveries.</p>
                </div>

                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5 opacity-80">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-xs text-slate-900">🧪 Quality Sample Lab Testing</span>
                        <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">Batch 3</span>
                    </div>
                    <p class="text-xs text-slate-500">Moisture, foreign matter, and oil content grading with instant farmer receipt generation.</p>
                </div>

                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5 opacity-80">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-xs text-slate-900">📊 Aggregated Lot Auctions</span>
                        <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">Batch 3</span>
                    </div>
                    <p class="text-xs text-slate-500">Pool smallholder harvests into 20MT commercial lots for direct bidding with verified buyers.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Link Member Farmer -->
<div id="add-farmer-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-[#0c365b]">Link Member Farmer</h3>
            <button type="button" onclick="document.getElementById('add-farmer-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form action="{{ route('fpo.members.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="farmer_identifier" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Farmer Phone, Email, or User ID <span class="text-red-500">*</span>
                </label>
                <input type="text" name="farmer_identifier" id="farmer_identifier" required placeholder="e.g. 9876543210 or suresh.patel@..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                <p class="text-[11px] text-slate-400 mt-1">Links an existing farmer's profile without duplicating their records.</p>
            </div>

            <div>
                <label for="member_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Member Society Code</label>
                <input type="text" name="member_code" id="member_code" placeholder="e.g. SKPC-MEM-002" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm uppercase">
            </div>

            <div>
                <label for="share_units" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Subscribed Share Units</label>
                <input type="number" name="share_units" id="share_units" value="10" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label for="membership_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Notes / Cluster Info</label>
                <textarea name="notes" id="membership_notes" rows="2" placeholder="e.g. Cotton grower cluster, Gondal village" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('add-farmer-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#0c365b] text-white text-xs font-bold">Link Farmer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit FPO Details -->
<div id="edit-fpo-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-[#0c365b]">Edit FPO Organization Profile</h3>
            <button type="button" onclick="document.getElementById('edit-fpo-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form action="{{ route('fpo.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="modal_fpo_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">FPO Name</label>
                <input type="text" name="name" id="modal_fpo_name" value="{{ $organization->name }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label for="modal_org_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Organization Type</label>
                <select name="organization_type" id="modal_org_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                    <option value="Farmer Producer Company (FPC)" {{ $organization->organization_type === 'Farmer Producer Company (FPC)' ? 'selected' : '' }}>Farmer Producer Company (FPC)</option>
                    <option value="Cooperative Society" {{ $organization->organization_type === 'Cooperative Society' ? 'selected' : '' }}>Cooperative Society</option>
                    <option value="Multi-State Cooperative" {{ $organization->organization_type === 'Multi-State Cooperative' ? 'selected' : '' }}>Multi-State Cooperative</option>
                </select>
            </div>

            <div>
                <label for="modal_reg_num" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">CIN / Registration Number</label>
                <input type="text" name="registration_number" id="modal_reg_num" value="{{ $organization->registration_number }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm uppercase">
            </div>

            <div>
                <label for="modal_registered_address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Registered Address</label>
                <textarea name="registered_address" id="modal_registered_address" rows="2" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">{{ $organization->registered_address }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="modal_village" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Village / Taluka</label>
                    <input type="text" name="village" id="modal_village" value="{{ $organization->village }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="modal_pincode" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pincode</label>
                    <input type="text" name="pincode" id="modal_pincode" value="{{ $organization->pincode }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="modal_bank" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bank Name</label>
                    <input type="text" name="bank_name" id="modal_bank" value="{{ $organization->bank_name }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="modal_ifsc" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">IFSC Code</label>
                    <input type="text" name="ifsc_code" id="modal_ifsc" value="{{ $organization->ifsc_code }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm uppercase">
                </div>
            </div>

            <div>
                <label for="modal_fpo_desc" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description & Crop Scope</label>
                <textarea name="description" id="modal_fpo_desc" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">{{ $organization->description }}</textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('edit-fpo-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#0c365b] text-white text-xs font-bold">Save FPO Details</button>
            </div>
        </form>
    </div>
</div>
@endsection
