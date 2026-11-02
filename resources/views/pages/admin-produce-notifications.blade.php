@extends('layouts.app')

@section('title', 'Admin — Buyer Notification Audit History')

@section('content')
<div class="bg-stone-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-6xl">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a>
            <span>/</span>
            <a href="{{ route('admin.index') }}" class="hover:text-emerald-800">Admin Portal</a>
            <span>/</span>
            <a href="{{ route('admin.produce.index') }}" class="hover:text-emerald-800">Produce Management</a>
            <span>/</span>
            <span class="text-stone-900 font-semibold">Notification Audit Log</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Buyer Notification Audit Ledger
                </h1>
                <p class="text-stone-600 text-sm mt-0.5">
                    Real-time and persistent notification delivery history triggered upon stock publication (<code class="bg-stone-100 text-emerald-800 px-1 py-0.5 rounded text-xs font-mono font-semibold">INVENTORY_AVAILABLE</code>).
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.produce.index') }}" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-semibold">
                    ← Back to Produce Hub
                </a>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm">
                <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wide">Total Dispatched</div>
                <div class="text-2xl font-extrabold text-stone-900 mt-1">{{ $notifications->total() }}</div>
                <div class="text-[11px] text-emerald-700 mt-0.5 font-medium">Database & Live Queue</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm">
                <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wide">Delivery Channel</div>
                <div class="text-2xl font-extrabold text-emerald-800 mt-1">Multi-Channel</div>
                <div class="text-[11px] text-stone-500 mt-0.5 font-medium">WebSocket & Live Polling</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm">
                <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wide">Privacy Standard</div>
                <div class="text-2xl font-extrabold text-stone-900 mt-1">Scrubbed</div>
                <div class="text-[11px] text-stone-500 mt-0.5 font-medium">Zero farmer PII exposed</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm">
                <div class="text-[11px] font-semibold text-stone-500 uppercase tracking-wide">Idempotency</div>
                <div class="text-2xl font-extrabold text-emerald-800 mt-1">Deduplicated</div>
                <div class="text-[11px] text-stone-500 mt-0.5 font-medium">Unique event per lot/buyer</div>
            </div>
        </div>

        <!-- Audit Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-stone-200/80 overflow-hidden">
            <div class="p-5 border-b border-stone-200/80 bg-stone-50/50 flex justify-between items-center text-xs">
                <span class="font-bold text-stone-800">Notification Records</span>
                <span class="text-stone-500 font-mono">App\Notifications\ProduceAvailableNotification</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-100/70 text-stone-600 uppercase tracking-wider text-[10px] font-bold border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-4">Timestamp</th>
                            <th class="py-3 px-4">Recipient Buyer</th>
                            <th class="py-3 px-4">Produce Details</th>
                            <th class="py-3 px-4">Grade</th>
                            <th class="py-3 px-4">Quantity & Price</th>
                            <th class="py-3 px-4">Read Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($notifications as $notif)
                            @php
                                $payload = is_string($notif->data) ? json_decode($notif->data, true) : (array) $notif->data;
                                $buyer = \App\Models\User::find($notif->notifiable_id);
                            @endphp
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-stone-500">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->format('M d, Y') }}
                                    <span class="text-[11px] text-stone-400 block">{{ \Carbon\Carbon::parse($notif->created_at)->format('H:i:s') }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <strong class="text-stone-900 block">{{ $buyer->name ?? 'User #' . $notif->notifiable_id }}</strong>
                                    <span class="text-stone-500 text-[11px]">{{ $buyer->email ?? 'buyer@nxtperson.com' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-stone-900">{{ $payload['crop_name'] ?? 'Produce' }}</span>
                                    <span class="text-stone-600">({{ $payload['variety'] ?? 'Variety' }})</span>
                                    <span class="text-[11px] text-stone-400 block font-mono">Lot #{{ $payload['lot_number'] ?? 'N/A' }} • {{ $payload['district'] ?? 'Gujarat' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $grade = $payload['quality_grade'] ?? 'Good';
                                        $gradeColor = match($grade) {
                                            'Good' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'Intermediate' => 'bg-amber-100 text-amber-800 border-amber-300',
                                            'Bad' => 'bg-rose-100 text-rose-800 border-rose-300',
                                            default => 'bg-stone-100 text-stone-700 border-stone-300'
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $gradeColor }}">
                                        Grade {{ $grade }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-stone-900">{{ number_format($payload['available_quantity_kg'] ?? 0, 1) }} kg</div>
                                    <div class="text-[11px] text-emerald-800 font-semibold">₹{{ number_format($payload['buyer_sale_price_per_kg'] ?? 0, 2) }}/kg</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($notif->read_at)
                                        <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-medium">
                                            <span>✓✓</span> Read {{ \Carbon\Carbon::parse($notif->read_at)->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                            ● Unread
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if(isset($payload['lot_id']))
                                        <a href="{{ route('admin.produce.show', $payload['lot_id']) }}" class="px-3 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-lg text-xs font-semibold">
                                            View Lot
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-500">
                                    <div class="text-3xl mb-2">🔔</div>
                                    <p class="font-semibold text-stone-700">No notifications dispatched yet</p>
                                    <p class="text-xs text-stone-500 mt-1">Notifications are generated automatically when administrator publishes stock with status <code class="font-mono text-emerald-800">INVENTORY_AVAILABLE</code>.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($notifications->hasPages())
                <div class="p-4 border-t border-stone-100">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
