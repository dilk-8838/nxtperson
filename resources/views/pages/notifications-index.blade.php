@extends('layouts.app')

@php
    $roleTitle = match($user->role) {
        'admin' => 'Administrative Notification Center',
        'buyer' => 'Buyer Procurement Notification Center',
        'distributor' => 'Distributor Notification Center',
        default => 'Farmer Notification Center',
    };
    $roleDescription = match($user->role) {
        'admin' => 'Operational triage alerts, incoming harvest lot verifications, and trade monitoring.',
        'buyer' => 'Live updates on certified produce stock publications and order reservation confirmations.',
        'distributor' => 'Agri-input logistics, regional order dispatches, and warehouse inventory notices.',
        default => 'Buyer purchase reservations, agronomist grading verifications, procurement approvals, and trade inquiries.',
    };
@endphp

@section('title', "{$roleTitle} — Nxtperson Agritech")

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-4xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            @if($user->role === 'admin')
                <a href="{{ route('admin.produce.index') }}" class="hover:text-emerald-800">Admin Hub</a>
            @elseif($user->role === 'buyer')
                <a href="{{ route('buyer.dashboard') }}" class="hover:text-emerald-800">Buyer Dashboard</a>
            @else
                <a href="{{ route('farmer.lots.index') }}" class="hover:text-emerald-800">Farmer Portal</a>
            @endif
            <span>/</span>
            <span class="text-stone-900 font-semibold">Notifications</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                        {{ $roleTitle }}
                    </h1>
                    @if($unreadCount > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#00875a] text-white">
                            {{ $unreadCount }} Unread
                        </span>
                    @endif
                </div>
                <p class="text-stone-600 text-sm mt-1">
                    {{ $roleDescription }}
                </p>
            </div>

            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-xl text-xs font-bold transition-colors shadow-xs cursor-pointer">
                        Mark All as Read ({{ $unreadCount }})
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>✓</span> {{ session('success') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 mb-6 border-b border-stone-200 pb-3">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
               class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filter !== 'unread' ? 'bg-[#0c365b] text-white shadow-xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                All ({{ $totalCount }})
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
               class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filter === 'unread' ? 'bg-[#0c365b] text-white shadow-xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                Unread ({{ $unreadCount }})
            </a>
        </div>

        <!-- Notifications Container -->
        <div class="bg-white rounded-3xl shadow-sm border border-stone-200/80 overflow-hidden divide-y divide-stone-100">
            @forelse($notifications as $notif)
                @php
                    $data = $notif->data;
                    $notifType = $data['type'] ?? 'notification';
                    $icon = match($notifType) {
                        'produce_stock_available' => '🌾',
                        'farmer_reservation_received' => '🛒',
                        'farmer_interest' => '💬',
                        'farmer_lot_status_updated' => '📋',
                        'farmer_reservation_status' => '📦',
                        'buyer_reservation_status' => '✅',
                        'admin_new_lot_submitted' => '🛡️',
                        'admin_reservation_alert' => '⚡',
                        default => '🔔',
                    };
                @endphp
                <div class="p-5 sm:p-6 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $notif->read_at ? 'bg-white' : 'bg-emerald-50/40 border-l-4 border-l-[#00875a]' }}">
                    <div class="flex items-start gap-4 min-w-0">
                        <!-- Icon Avatar -->
                        <div class="w-12 h-12 rounded-2xl bg-stone-100 border border-stone-200 flex items-center justify-center text-xl shrink-0">
                            {{ $icon }}
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm sm:text-base font-bold text-stone-900">
                                    {{ $data['title'] ?? 'Agritech Notification' }}
                                </h3>
                                @if(!$notif->read_at)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#00875a] text-white">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Unread
                                    </span>
                                @endif
                                @if(!empty($data['lot_number']))
                                    <span class="text-[10px] font-mono text-stone-500 bg-stone-100 px-1.5 py-0.5 rounded">
                                        {{ $data['lot_number'] }}
                                    </span>
                                @endif
                                @if(!empty($data['status_label']))
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                        {{ $data['status_label'] }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-stone-600 mt-1.5 leading-relaxed">
                                {{ $data['message'] ?? '' }}
                            </p>

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-stone-500 mt-2 font-medium">
                                @if(!empty($data['district']))
                                    <span>📍 {{ $data['district'] }}</span>
                                @endif
                                @if(!empty($data['available_quantity_kg']))
                                    <span>Stock: <strong>{{ number_format((float)$data['available_quantity_kg'], 1) }} kg</strong></span>
                                @endif
                                @if(!empty($data['buyer_sale_price_per_kg']))
                                    <span>Rate: <strong class="text-emerald-800">₹{{ number_format((float)$data['buyer_sale_price_per_kg'], 2) }}/kg</strong></span>
                                @endif
                                @if(!empty($data['total_price']))
                                    <span>Total: <strong class="text-emerald-800">₹{{ number_format((float)$data['total_price'], 2) }}</strong></span>
                                @endif
                                <span>•</span>
                                <span class="font-mono text-stone-400">{{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('M d, H:i') }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                        @if(!$notif->read_at)
                            <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-semibold transition-colors" title="Mark as read">
                                    Mark as read
                                </button>
                            </form>
                        @endif

                        @if(!empty($data['view_url']))
                            <a href="{{ $data['view_url'] }}" class="px-3.5 py-1.5 bg-[#0c365b] hover:bg-[#071f37] text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1">
                                <span>{{ $data['action_label'] ?? 'View' }}</span>
                                <span>→</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-stone-100 border border-stone-200 flex items-center justify-center text-3xl mx-auto mb-3">
                        🔔
                    </div>
                    <h3 class="text-base font-bold text-stone-800">
                        {{ $filter === 'unread' ? 'No unread notifications' : 'No notifications yet' }}
                    </h3>
                    <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                        {{ $filter === 'unread' ? 'You have read all your notifications. Switch to "All" to view past events.' : 'As market transactions, quality audits, and orders occur, your real-time alerts will appear here.' }}
                    </p>
                    @if($filter === 'unread')
                        <a href="{{ route('notifications.index', ['filter' => 'all']) }}" class="inline-block mt-4 px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-xl text-xs font-bold transition-colors">
                            View All Notifications
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
