@extends('layouts.app')

@section('title', 'Supplier Business Dashboard — ' . $organization->name)

@section('content')
<div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl mx-auto px-4 sm:px-6 space-y-8">

        <!-- Top Header & Status Bar -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200">
                        🏢 Agricultural Input Supplier
                    </span>

                    @if($organization->isApproved())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Verified Business Partner</span>
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
                    {{ $organization->description ?? 'Certified agricultural input supplier and regional distributor.' }}
                </p>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-slate-500 pt-1">
                    <span>📍 {{ $organization->operating_address }}, {{ $organization->district }}, {{ $organization->state }}</span>
                    @if($organization->gst_number)
                        <span>📄 GSTIN: <strong class="text-slate-700">{{ $organization->gst_number }}</strong></span>
                    @endif
                    @if($organization->license_number)
                        <span>🛡️ License: <strong class="text-slate-700">{{ $organization->license_number }}</strong></span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <button type="button" 
                        onclick="document.getElementById('edit-profile-modal').classList.remove('hidden')"
                        class="px-4 py-2.5 rounded-2xl border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700 transition-all shadow-xs cursor-pointer">
                    ✏️ Edit Business Profile
                </button>
                <button type="button" 
                        onclick="document.getElementById('add-member-modal').classList.remove('hidden')"
                        class="px-4 py-2.5 rounded-2xl bg-[#0c365b] hover:bg-[#071f37] text-white text-xs sm:text-sm font-bold transition-all shadow-xs cursor-pointer">
                    + Add Team Member
                </button>
            </div>
        </div>

        @if($organization->verification_status === 'pending')
            <!-- Pending Notice Alert -->
            <div class="p-4 sm:p-5 rounded-3xl bg-amber-50/90 border border-amber-300/80 text-amber-900 flex items-start gap-3.5 shadow-xs">
                <span class="text-2xl shrink-0">⏳</span>
                <div>
                    <h3 class="text-sm font-bold">Organization Verification in Progress</h3>
                    <p class="text-xs sm:text-sm text-amber-800 mt-0.5">
                        Your business profile and dealer licensing credentials have been submitted for verification. While under review, your company profile is saved, but marketplace RFQ responses and live product catalog publishing remain restricted until approved by an administrator.
                    </p>
                </div>
            </div>
        @elseif($organization->verification_status === 'rejected')
            <div class="p-4 sm:p-5 rounded-3xl bg-red-50 border border-red-300 text-red-900 flex items-start gap-3.5 shadow-xs">
                <span class="text-2xl shrink-0">⚠️</span>
                <div>
                    <h3 class="text-sm font-bold">Verification Application Rejected</h3>
                    <p class="text-xs sm:text-sm text-red-800 mt-0.5">
                        {{ $organization->rejection_reason ?? 'Your application could not be verified with the submitted licenses. Please update your business details and valid state agricultural dealer licensing.' }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Real Persisted Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <a href="{{ route('supplier.products.index') }}" class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm hover:border-emerald-500 hover:shadow-md transition group block">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Catalog Products</span>
                    <span class="text-xl">📦</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#0c365b]">{{ $catalogCount }}</div>
                <span class="inline-block mt-1 text-xs font-bold text-emerald-700 group-hover:underline">
                    Manage Products &rarr;
                </span>
            </a>

            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Dealer Network</span>
                    <span class="text-xl">🏬</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#00875a]">{{ $distributorNetworkCount }}</div>
                <p class="text-xs text-slate-500 mt-1">Verified regional retail Kendra outlets</p>
            </div>

            <a href="{{ route('rfqs.seller') }}" class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm hover:border-amber-500 hover:shadow-md transition group block">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">RFQ Inquiries</span>
                    <span class="text-xl">📑</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-600">RFQ Inbox</div>
                <span class="inline-block mt-1 text-xs font-bold text-amber-700 group-hover:underline">
                    View &amp; Quote &rarr;
                </span>
            </a>

            <a href="{{ route('orders.index') }}" class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm hover:border-blue-500 hover:shadow-md transition group block">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Purchase Orders</span>
                    <span class="text-xl">🤝</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-blue-600">Orders</div>
                <span class="inline-block mt-1 text-xs font-bold text-blue-700 group-hover:underline">
                    Track Fulfilment &rarr;
                </span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Authorized Team Members & Organization Profile -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Team Members Section -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-[#0c365b]">Authorized Team Members</h2>
                            <p class="text-xs text-slate-500">Personnel authorized to act on behalf of {{ $organization->name }}</p>
                        </div>
                        <button type="button" 
                                onclick="document.getElementById('add-member-modal').classList.remove('hidden')"
                                class="text-xs font-bold text-[#00875a] hover:underline cursor-pointer">
                            + Add Representative
                        </button>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($teamMembers as $member)
                            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-[#0c365b] flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($member->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900">{{ $member->user->name ?? 'Representative' }}</span>
                                            @if($member->is_primary_contact)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                                    Primary Contact
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 flex items-center gap-3 mt-0.5">
                                            <span>{{ $member->user->email ?? 'No email' }}</span>
                                            <span>•</span>
                                            <span>{{ $member->user->phone ?? 'No phone' }}</span>
                                            <span>•</span>
                                            <span class="capitalize text-slate-600 font-semibold">{{ $member->role_in_org }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    @if(!$member->is_primary_contact && $teamMembers->count() > 1)
                                        <form action="{{ route('supplier.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Remove this team member from organization?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline cursor-pointer">
                                                Remove
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-500">
                                No additional members configured.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Commercial Modules (Batch 2 & 3 Foundations) -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-[#0c365b]">Supplier Operations Roadmap</h2>
                            <p class="text-xs text-slate-500">Commercial modules being unlocked in upcoming development phases</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                            Batch 1 Active
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5 opacity-80">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">📦 Product Catalog & Stocking</span>
                                <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">Batch 2</span>
                            </div>
                            <p class="text-xs text-slate-500">Publish seeds, fertilizers, and equipment SKUs directly to retail distributors and farmers.</p>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5 opacity-80">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">💬 Bulk Inquiries & Quotations</span>
                                <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">Batch 2</span>
                            </div>
                            <p class="text-xs text-slate-500">Receive and respond to bulk input inquiries from Farmer Producer Organizations (FPOs).</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Col: Organization Details & Recent Notifications -->
            <div class="space-y-8">
                <!-- Organization Credentials Summary -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-[#0c365b] uppercase tracking-wider">Business Information</h3>
                    <dl class="text-xs space-y-3">
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase">Category</dt>
                            <dd class="text-slate-800 font-bold capitalize mt-0.5">{{ str_replace('_', ' ', $organization->category) }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase">Authorized Representative</dt>
                            <dd class="text-slate-800 font-bold mt-0.5">{{ $organization->authorized_representative_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase">Direct Email</dt>
                            <dd class="text-slate-800 font-bold mt-0.5">{{ $organization->contact_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase">Direct Phone</dt>
                            <dd class="text-slate-800 font-bold mt-0.5">{{ $organization->contact_phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase">Registration Registered</dt>
                            <dd class="text-slate-800 font-bold mt-0.5">{{ $organization->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Notifications Panel -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-[#0c365b] uppercase tracking-wider">Recent Alerts</h3>
                        <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-[#00875a] hover:underline">View All</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($notifications as $noti)
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/70 text-xs space-y-1">
                                <div class="font-bold text-slate-800">{{ $noti->data['title'] ?? 'Notification' }}</div>
                                <p class="text-slate-600 text-[11px]">{{ $noti->data['message'] ?? '' }}</p>
                                <span class="text-[10px] text-slate-400 block pt-1">{{ $noti->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-slate-400">
                                🔔 No recent notifications.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Profile -->
<div id="edit-profile-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-[#0c365b]">Edit Supplier Business Profile</h3>
            <button type="button" onclick="document.getElementById('edit-profile-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form action="{{ route('supplier.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="modal_business_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Business Name</label>
                <input type="text" name="business_name" id="modal_business_name" value="{{ $organization->name }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label for="modal_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Input Category</label>
                <select name="business_category" id="modal_category" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                    <option value="seeds" {{ $organization->category === 'seeds' ? 'selected' : '' }}>Certified Hybrid & Organic Seeds</option>
                    <option value="fertilizers" {{ $organization->category === 'fertilizers' ? 'selected' : '' }}>Fertilizers & Soil Amendments</option>
                    <option value="crop_protection" {{ $organization->category === 'crop_protection' ? 'selected' : '' }}>Crop Protection & Bio-Defense</option>
                    <option value="equipment" {{ $organization->category === 'equipment' ? 'selected' : '' }}>Smart Farm Machinery & Equipment</option>
                    <option value="multi_category" {{ $organization->category === 'multi_category' ? 'selected' : '' }}>Multi-Category Agri Inputs</option>
                </select>
            </div>

            <div>
                <label for="modal_address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Operating Address</label>
                <textarea name="operating_address" id="modal_address" rows="2" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">{{ $organization->operating_address }}</textarea>
            </div>

            <div>
                <label for="modal_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Business Description</label>
                <textarea name="description" id="modal_description" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">{{ $organization->description }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="modal_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Contact Email</label>
                    <input type="email" name="contact_email" id="modal_email" value="{{ $organization->contact_email }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label for="modal_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" id="modal_phone" value="{{ $organization->contact_phone }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('edit-profile-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#0c365b] text-white text-xs font-bold">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Team Member -->
<div id="add-member-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-[#0c365b]">Add Authorized Team Member</h3>
            <button type="button" onclick="document.getElementById('add-member-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
        </div>

        <form action="{{ route('supplier.members.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="member_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                <input type="text" name="name" id="member_name" required placeholder="e.g. Priyesh Patel" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label for="member_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" name="email" id="member_email" required placeholder="e.g. priyesh@inputs.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label for="member_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number</label>
                <input type="tel" name="phone" id="member_phone" required placeholder="10 Digits" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label for="member_role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Role in Organization</label>
                <select name="role_in_org" id="member_role" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                    <option value="manager">Manager / Depot Head</option>
                    <option value="sales_rep">Field Sales Representative</option>
                    <option value="owner">Partner / Co-Owner</option>
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('add-member-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#0c365b] text-white text-xs font-bold">Add Member</button>
            </div>
        </form>
    </div>
</div>
@endsection
