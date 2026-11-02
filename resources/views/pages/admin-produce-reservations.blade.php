@extends('layouts.app')

@section('title', 'Admin — Buyer Produce Reservations Management')

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
            <span class="text-stone-900 font-semibold">Buyer Reservations</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Buyer Produce Reservations & Allocations
                </h1>
                <p class="text-stone-600 text-sm mt-0.5">
                    Track all institutional buyer orders, allocate warehouse dispatches, and manage inventory holds.
                </p>
            </div>
            <a href="{{ route('admin.produce.index') }}" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-semibold">
                ← Back to Produce Hub
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2 font-medium">
                    <span>✓</span> {{ session('success') }}
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        <!-- Reservations Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-stone-200/80 overflow-hidden">
            <div class="p-5 border-b border-stone-200/80 bg-stone-50/50 flex justify-between items-center text-xs">
                <span class="font-bold text-stone-800">Active Allocations ({{ $reservations->total() }})</span>
                <span class="text-stone-500 font-mono">Concurrency-Safe Allocation Ledger</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-100/70 text-stone-600 uppercase tracking-wider text-[10px] font-bold border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-4">Reservation #</th>
                            <th class="py-3 px-4">Buyer Account</th>
                            <th class="py-3 px-4">Produce Lot</th>
                            <th class="py-3 px-4">Reserved Quantity</th>
                            <th class="py-3 px-4">Price & Total</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($reservations as $res)
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-stone-900">
                                    {{ $res->reservation_number }}
                                    <span class="text-[10px] text-stone-400 block font-normal">{{ $res->created_at->format('M d, H:i') }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <strong class="text-stone-900 block">{{ $res->buyer->name ?? 'Demo Buyer' }}</strong>
                                    <span class="text-stone-500">{{ $res->buyer->phone ?? '' }} • {{ $res->buyer->district ?? 'Gujarat' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('admin.produce.show', $res->produce_lot_id) }}" class="font-bold text-emerald-800 hover:underline">
                                        {{ $res->lot->variety }}
                                    </a>
                                    <span class="text-[11px] text-stone-500 block">Lot {{ $res->lot->lot_number }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-stone-900">
                                    {{ number_format((float)$res->quantity_kg, 1) }} kg
                                    <span class="text-[10px] text-stone-500 font-normal block">({{ number_format((float)$res->quantity_kg / 100, 1) }} Qtl)</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <strong class="text-emerald-800 font-black">₹{{ number_format((float)$res->total_price, 2) }}</strong>
                                    <span class="text-[10px] text-stone-500 block">@ ₹{{ number_format((float)$res->price_per_kg, 2) }}/kg</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $res->getStatusBadgeClass() }}">
                                        {{ ucfirst($res->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($res->status === 'confirmed')
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form action="{{ route('admin.produce.reservations.status', $res->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="fulfilled">
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-[11px] font-bold transition-colors">
                                                    Fulfill
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.produce.reservations.status', $res->id) }}" method="POST" onsubmit="return confirm('Cancel this reservation and restore inventory?');">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg text-[11px] font-semibold transition-colors">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-stone-400 text-[11px] italic">Completed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400 italic">
                                    No buyer reservations recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages())
                <div class="p-4 border-t border-stone-200">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
