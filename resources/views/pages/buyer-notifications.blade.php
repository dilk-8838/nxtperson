@extends('layouts.app')

@section('title', 'Buyer Notification Center — Nxtperson Private Limited')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-4xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('buyer.dashboard') }}" class="hover:text-emerald-800">Buyer Dashboard</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Notification Center</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Buyer Notification Center
                </h1>
                <p class="text-stone-600 text-sm mt-0.5">
                    Real-time alerts whenever approved quality produce becomes available on the marketplace.
                </p>
            </div>

            @if($unreadCount > 0)
                <form action="{{ route('buyer.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-xl text-xs font-semibold transition-colors">
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

        <!-- Notifications List -->
        <div class="bg-white rounded-3xl shadow-sm border border-stone-200/80 overflow-hidden divide-y divide-stone-100">
            @forelse($notifications as $notif)
                <div class="p-5 sm:p-6 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $notif->read_at ? 'bg-white' : 'bg-emerald-50/40' }}">
                    <div class="flex items-start gap-4">
                        <!-- Thumbnail -->
                        <div class="w-16 h-16 rounded-2xl overflow-hidden bg-stone-100 border border-stone-200 shrink-0">
                            @if(!empty($notif->data['image_url']))
                                <img src="{{ $notif->data['image_url'] }}" alt="Produce" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-2xl">🌾</div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm sm:text-base font-bold text-stone-900">
                                    {{ $notif->data['title'] ?? 'New Produce Published' }}
                                </h3>
                                @if(!$notif->read_at)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Unread
                                    </span>
                                @endif
                                @if(!empty($notif->data['grade']))
                                    <span class="text-[10px] px-2 py-0.5 rounded font-bold {{ $notif->data['grade'] === 'Good' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        Grade {{ $notif->data['grade'] }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-stone-600 mt-1 leading-relaxed">
                                {{ $notif->data['message'] ?? '' }}
                            </p>

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-stone-500 mt-2">
                                <span>📍 {{ $notif->data['district'] ?? 'Gujarat' }}</span>
                                @if(!empty($notif->data['available_quantity_kg']))
                                    <span>Stock: <strong>{{ number_format((float)$notif->data['available_quantity_kg'], 1) }} kg</strong></span>
                                @endif
                                @if(!empty($notif->data['buyer_sale_price_per_kg']))
                                    <span>Rate: <strong class="text-emerald-800">₹{{ number_format((float)$notif->data['buyer_sale_price_per_kg'], 2) }}/kg</strong></span>
                                @endif
                                <span>•</span>
                                <span class="font-mono">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                        @if(!$notif->read_at)
                            <form action="{{ route('buyer.notifications.read', $notif->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs text-stone-500 hover:text-stone-800 font-semibold border border-stone-200 rounded-xl hover:bg-stone-50 transition-colors">
                                    Mark as Read
                                </button>
                            </form>
                        @endif

                        <a href="{{ $notif->data['view_url'] ?? route('buyer.marketplace') }}" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1">
                            <span>Inspect Produce</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center text-stone-500">
                    <span class="text-4xl mb-3 block">🔔</span>
                    <h3 class="text-base font-bold text-stone-800">No Notifications</h3>
                    <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                        You have no alerts at this time. When administrators publish approved produce lots, you will be notified instantly without needing to refresh.
                    </p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
