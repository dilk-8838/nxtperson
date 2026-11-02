@extends('layouts.app')

@section('title', 'Buyer Dashboard & Allocations — Nxtperson Private Limited')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-6xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('buyer.marketplace') }}" class="hover:text-emerald-800">Produce Marketplace</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Buyer Dashboard</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#0c365b]/10 text-[#0c365b] border border-[#0c365b]/20">
                    <span class="w-2 h-2 rounded-full bg-[#00875a] animate-pulse"></span>
                    Verified Institutional Buyer Perspective
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight mt-2">
                    Buyer Portal & Procurement Allocations
                </h1>
                <p class="text-slate-600 text-sm mt-0.5">
                    Logged in as <strong>{{ $buyer->name }}</strong> ({{ $buyer->district }}, Gujarat)
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('rfqs.index') }}" class="px-4 py-2 bg-white border border-slate-300 hover:border-[#0c365b] text-slate-800 font-bold rounded-full text-xs transition shadow-xs flex items-center gap-1.5">
                    <span>⚖️</span>
                    <span>My RFQs</span>
                </a>
                <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-white border border-slate-300 hover:border-[#0c365b] text-slate-800 font-bold rounded-full text-xs transition shadow-xs flex items-center gap-1.5">
                    <span>📦</span>
                    <span>Purchase Orders</span>
                </a>
                <a href="{{ route('buyer.requirements.index') }}" class="px-4 py-2 bg-white border border-slate-300 hover:border-[#0c365b] text-slate-800 font-bold rounded-full text-xs transition shadow-xs flex items-center gap-1.5">
                    <span>📋</span>
                    <span>Bulk Needs</span>
                </a>
                <a href="{{ route('buyer.notifications') }}" class="px-4 py-2 bg-white border border-slate-300 hover:border-[#0c365b] text-slate-800 font-bold rounded-full text-xs transition shadow-xs flex items-center gap-1.5">
                    <span>🔔</span>
                    <span>Alerts</span>
                    @if($unreadCount > 0)
                        <span class="bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('buyer.marketplace') }}" class="px-5 py-2 bg-[#0c365b] hover:bg-[#071f37] text-white font-bold rounded-full shadow-md transition text-xs flex items-center gap-1.5">
                    <span>🛒</span>
                    <span>Marketplace &rarr;</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>✓</span> {{ session('success') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        <!-- KPI Metrics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Active Reservations</span>
                <div class="text-2xl sm:text-3xl font-black text-stone-900 mt-1">
                    {{ $stats['active_reservations_count'] }}
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">Awaiting warehouse dispatch</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Quantity Reserved</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1">
                    {{ number_format((float)$stats['total_procured_kg'], 1) }} <span class="text-sm font-normal text-stone-500">kg</span>
                </div>
                <span class="text-[11px] text-emerald-800 font-medium mt-1 block">({{ number_format((float)$stats['total_procured_kg'] / 100, 1) }} Quintals)</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Committed Value</span>
                <div class="text-2xl sm:text-3xl font-black text-stone-900 mt-1">
                    ₹{{ number_format((float)$stats['total_spend'], 2) }}
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">Confirmed procurement value</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-stone-200/80 shadow-2xs">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Unread Alerts</span>
                <div class="text-2xl sm:text-3xl font-black text-blue-700 mt-1">
                    {{ $stats['unread_notifications'] }}
                </div>
                <span class="text-[11px] text-stone-500 mt-1 block">Live stock availability alerts</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Reservations Table -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-stone-200/80 overflow-hidden">
                    <div class="p-5 border-b border-stone-200/80 flex items-center justify-between">
                        <h3 class="text-base font-extrabold text-stone-900 flex items-center gap-2">
                            <span>📋</span> My Produce Orders & Reservations
                        </h3>
                        <span class="text-xs text-stone-500 font-mono">
                            {{ $reservations->total() }} Record(s)
                        </span>
                    </div>

                    <div class="divide-y divide-stone-100">
                        @forelse($reservations as $res)
                            <div class="p-5 hover:bg-stone-50/70 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-14 h-14 rounded-2xl overflow-hidden bg-stone-100 border border-stone-200 shrink-0">
                                        @if($res->lot->primaryImage)
                                            <img src="{{ asset($res->lot->primaryImage->image_path) }}" alt="{{ $res->lot->variety }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xl">
                                                {{ $res->lot->crop->icon ?? '🌾' }}
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-extrabold text-stone-900">
                                                {{ $res->lot->variety }} ({{ $res->lot->crop->en_name ?? ucfirst($res->lot->crop_slug) }})
                                            </h4>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $res->getStatusBadgeClass() }}">
                                                {{ ucfirst($res->status) }}
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-stone-600 mt-1">
                                            <span class="font-mono text-stone-500">#{{ $res->reservation_number }}</span>
                                            <span>Reserved: <strong>{{ number_format((float)$res->quantity_kg, 1) }} kg</strong></span>
                                            <span>Rate: ₹{{ number_format((float)$res->price_per_kg, 2) }}/kg</span>
                                            <span>Total: <strong class="text-emerald-800">₹{{ number_format((float)$res->total_price, 2) }}</strong></span>
                                        </div>

                                        <span class="text-[11px] text-stone-400 mt-1 block">
                                            Ordered {{ $res->created_at->diffForHumans() }} • Lot {{ $res->lot->lot_number }} ({{ $res->lot->district }})
                                        </span>
                                    </div>
                                </div>

                                <a href="{{ route('buyer.lot.show', $res->produce_lot_id) }}" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-xl text-xs font-semibold transition-colors shrink-0 self-end sm:self-center">
                                    View Lot →
                                </a>
                            </div>
                        @empty
                            <div class="py-12 text-center text-stone-500">
                                <span class="text-3xl mb-2 block">🛒</span>
                                <h4 class="text-sm font-bold text-stone-800">No Orders Yet</h4>
                                <p class="text-xs text-stone-500 mt-1">Explore available produce lots to place your first reservation request.</p>
                                <a href="{{ route('buyer.marketplace') }}" class="mt-3 inline-block px-4 py-2 bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs">
                                    Browse Marketplace
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($reservations->hasPages())
                        <div class="p-4 border-t border-stone-200">
                            {{ $reservations->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right 1 Col: Notification Stream Preview -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200/80">
                    <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                        <h3 class="text-sm font-bold text-stone-900 flex items-center gap-2">
                            <span>🔔</span> Recent In-App Alerts
                        </h3>
                        <a href="{{ route('buyer.notifications') }}" class="text-xs font-bold text-emerald-800 hover:underline">
                            View All ({{ $unreadCount }} New)
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($notifications->take(5) as $notif)
                            <div class="p-3 rounded-2xl border {{ $notif->read_at ? 'bg-stone-50/50 border-stone-200 text-stone-600' : 'bg-emerald-50/60 border-emerald-300 text-emerald-950 font-medium' }} text-xs">
                                <div class="flex items-start justify-between gap-1 mb-1">
                                    <strong class="font-bold text-stone-900 text-xs">
                                        {{ $notif->data['title'] ?? 'New Produce Stock' }}
                                    </strong>
                                    @if(!$notif->read_at)
                                        <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0 mt-1"></span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-stone-600 line-clamp-2 leading-relaxed">
                                    {{ $notif->data['message'] ?? '' }}
                                </p>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-stone-200/60 text-[10px] text-stone-400">
                                    <span>{{ $notif->created_at->diffForHumans() }}</span>
                                    <a href="{{ $notif->data['view_url'] ?? route('buyer.marketplace') }}" class="font-bold text-emerald-800 hover:underline">
                                        Inspect Lot →
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-stone-400 italic py-6 text-center">No alerts in your inbox.</p>
                        @endforelse
                    </div>
                </div>

                @php
                    $buyerProfile = $buyer->buyerProfile;
                @endphp
                @if($buyerProfile)
                    <!-- Buyer Organization Profile -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Enterprise Profile</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Verified Corporate</span>
                        </div>
                        <div class="text-sm font-extrabold text-[#0c365b]">{{ $buyerProfile->company_name ?? $buyer->name }}</div>
                        <div class="space-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-2">
                            <div><strong>Type:</strong> <span class="capitalize">{{ $buyerProfile->buyer_type }}</span></div>
                            @if($buyerProfile->gst_number)
                                <div><strong>GSTIN:</strong> <span class="font-mono">{{ $buyerProfile->gst_number }}</span></div>
                            @endif
                            @if($buyerProfile->trade_license)
                                <div><strong>FSSAI/License:</strong> <span class="font-mono">{{ $buyerProfile->trade_license }}</span></div>
                            @endif
                            @if($buyerProfile->annual_procurement_budget)
                                <div><strong>Procurement Scale:</strong> {{ $buyerProfile->annual_procurement_budget }}</div>
                            @endif
                            <div><strong>Depot:</strong> {{ $buyerProfile->operating_address ?? $buyer->district . ', Gujarat' }}</div>
                        </div>
                    </div>
                @endif

                <!-- Bulk RFQ & Future Procurement Engine (Batch 2 Scope Notice) -->
                <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900">📑 Enterprise RFQs & POs</span>
                        <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Batch 2 Engine</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Upcoming modules will enable direct RFQ broadcast to FPO clusters, multi-vendor quote comparison, and digital purchase orders.
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('buyer.marketplace') }}" class="w-full text-center py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-[#0c365b] text-xs font-bold border border-slate-200">
                            Search Spot Lots →
                        </a>
                    </div>
                </div>

                <!-- Buyer Procurement Policy -->
                <div class="p-5 rounded-3xl bg-stone-100 border border-stone-200 text-xs text-stone-600 space-y-2">
                    <strong class="font-bold text-stone-800 block">Nxtperson Quality Guarantee</strong>
                    <p class="text-[11px] leading-relaxed">
                        Every listed lot is harvested from verified Gujarat growers and certified by Nxtperson's computer-vision inspection engine. Moisture, grading, and weight certificates accompany all physical dispatches.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
