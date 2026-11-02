@extends('layouts.app')

@section('title', 'Purchase Orders — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header & Sub-nav --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Purchase Orders</h1>
                <p class="text-slate-400 text-sm mt-1">
                    @if($user->isBuyer())
                        Track your procurement contracts, shipments, and dispatch milestones.
                    @else
                        Manage confirmed purchase orders, fulfilment progress, and delivery logs.
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if($user->isBuyer())
                    <a href="{{ route('rfqs.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                        View RFQs
                    </a>
                    <a href="{{ route('buyer.marketplace') }}" class="px-4 py-2 rounded-xl text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition">
                        Browse Marketplace &rarr;
                    </a>
                @else
                    <a href="{{ route('rfqs.seller') }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                        RFQ Inbox
                    </a>
                @endif
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-900/40 border border-emerald-700 text-emerald-200 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Metrics Row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Total Orders</span>
                <span class="text-2xl font-extrabold text-white mt-1 block">{{ number_format($stats['total']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">All registered contracts</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-emerald-400 font-bold block">Active Fulfilment</span>
                <span class="text-2xl font-extrabold text-emerald-300 mt-1 block">{{ number_format($stats['active']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">In processing or in transit</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-sky-400 font-bold block">Completed / Delivered</span>
                <span class="text-2xl font-extrabold text-sky-300 mt-1 block">{{ number_format($stats['delivered']) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Successfully verified</span>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-700/70 p-5">
                <span class="text-xs uppercase tracking-wider text-amber-400 font-bold block">Total Trade Volume</span>
                <span class="text-2xl font-extrabold text-amber-300 mt-1 block">₹{{ number_format((float)$stats['total_volume_rupees'], 2) }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Contracted order value</span>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-800">
            @php $currentStatus = request('status'); @endphp
            <a href="{{ route('orders.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ !$currentStatus ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                All ({{ $stats['total'] }})
            </a>
            <a href="{{ route('orders.index', ['status' => 'pending_confirmation']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ $currentStatus === 'pending_confirmation' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                Pending Confirmation
            </a>
            <a href="{{ route('orders.index', ['status' => 'confirmed']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ $currentStatus === 'confirmed' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                Confirmed
            </a>
            <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ $currentStatus === 'processing' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                Processing
            </a>
            <a href="{{ route('orders.index', ['status' => 'dispatched']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ $currentStatus === 'dispatched' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                Dispatched
            </a>
            <a href="{{ route('orders.index', ['status' => 'delivered']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ $currentStatus === 'delivered' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                Delivered
            </a>
            <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ $currentStatus === 'cancelled' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                Cancelled
            </a>
        </div>

        {{-- Orders Table / List --}}
        @if($orders->isEmpty())
            <div class="bg-slate-800/40 rounded-2xl border border-slate-700 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-700/50 flex items-center justify-center text-slate-400 text-2xl font-bold">
                    📦
                </div>
                <h3 class="text-lg font-bold text-white mb-1">No Purchase Orders Found</h3>
                <p class="text-sm text-slate-400 max-w-md mx-auto mb-6">
                    @if($user->isBuyer())
                        Accept quotations from your RFQs or reserve produce from the marketplace to generate purchase orders.
                    @else
                        Once buyers accept your quotations or make reservations, orders will appear here.
                    @endif
                </p>
                @if($user->isBuyer())
                    <a href="{{ route('buyer.marketplace') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-500 transition">
                        Explore Marketplace &rarr;
                    </a>
                @endif
            </div>
        @else
            <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-700">
                                <th class="py-4 px-6 font-semibold">PO Number & Item</th>
                                <th class="py-4 px-4 font-semibold">Counterparty</th>
                                <th class="py-4 px-4 font-semibold">Quantity</th>
                                <th class="py-4 px-4 font-semibold text-emerald-400">Total Value</th>
                                <th class="py-4 px-4 font-semibold">Order Status</th>
                                <th class="py-4 px-4 font-semibold">Payment Status</th>
                                <th class="py-4 px-4 font-semibold">Est. Delivery</th>
                                <th class="py-4 px-6 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-750">
                            @foreach($orders as $order)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-4 px-6">
                                        <a href="{{ route('orders.show', $order->id) }}" class="font-bold text-white hover:text-emerald-400 transition block">
                                            #{{ $order->order_number }}
                                        </a>
                                        <div class="text-xs text-slate-300 font-medium truncate max-w-xs mt-0.5">
                                            {{ $order->title }}
                                        </div>
                                        <span class="text-[11px] text-slate-500">
                                            Ordered: {{ $order->created_at->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-xs">
                                        @if($user->isBuyer())
                                            <span class="font-semibold text-white block">{{ $order->getSellerDisplayName() }}</span>
                                            <span class="text-slate-400 capitalize">Seller ({{ $order->seller_type }})</span>
                                        @else
                                            <span class="font-semibold text-white block">{{ $order->buyer?->name ?? 'Buyer' }}</span>
                                            <span class="text-slate-400">{{ $order->buyer?->district ?? 'Client' }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-medium text-slate-200">
                                        {{ number_format((float)$order->quantity, 1) }} {{ $order->unit }}
                                    </td>
                                    <td class="py-4 px-4 font-bold text-emerald-300">
                                        ₹{{ number_format((float)$order->total_amount, 2) }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->getStatusBadgeClass() }}">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->getPaymentStatusBadgeClass() }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-xs text-slate-300">
                                        {{ $order->target_delivery_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-700 text-slate-200 hover:bg-slate-600 transition">
                                            Details &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="px-6 py-4 bg-slate-850 border-t border-slate-700">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection
