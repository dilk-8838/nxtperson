@extends('layouts.app')

@section('title', 'Purchase Order #' . $order->po_number . ' — NxtPerson Agritech')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-8">

        {{-- Breadcrumb & Title --}}
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-400 mb-3">
                <a href="{{ route('orders.index') }}" class="hover:text-emerald-400 transition">Purchase Orders</a>
                <span>&rarr;</span>
                <span class="text-slate-200">#{{ $order->po_number }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Purchase Order #{{ $order->po_number }}</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $order->getStatusBadgeClass() }}">
                            {{ $order->getStatusLabel() }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $order->getPaymentStatusBadgeClass() }}">
                            Payment: {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                        </span>
                    </div>
                    <p class="text-slate-400 text-sm mt-1">
                        Contracted on {{ $order->created_at->format('d M Y, h:i A') }} &bull; Item: <span class="text-white font-medium">{{ $order->title ?? $order->item_description }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                        &larr; All Orders
                    </a>
                    @if($order->rfq)
                        <a href="{{ route('rfqs.show', $order->rfq_id) }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                            View RFQ
                        </a>
                    @endif
                </div>
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
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-rose-200 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Interactive Fulfilment Lifecycle Progress Bar --}}
        <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">Fulfilment Milestone Progress</h2>
            @php
                $stages = [
                    'pending_confirmation' => 'Order Placed',
                    'confirmed' => 'Confirmed',
                    'processing' => 'Processing',
                    'ready_for_dispatch' => 'Ready for Dispatch',
                    'dispatched' => 'Dispatched',
                    'delivered' => 'Delivered',
                ];
                $statusKeys = array_keys($stages);
                $currentIndex = array_search($order->status, $statusKeys);
                $isCancelled = $order->status === 'cancelled';
            @endphp

            @if($isCancelled)
                <div class="p-4 rounded-xl bg-rose-900/30 border border-rose-600/50 flex items-center gap-3 text-rose-200 text-sm">
                    <span class="text-xl">⚠️</span>
                    <div>
                        <strong>Order Cancelled:</strong> {{ $order->cancellation_reason ?: 'No specific reason recorded.' }}
                        <div class="text-xs text-rose-300/80 mt-0.5">Committed produce/stock has been returned safely to inventory.</div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                    @foreach($stages as $stageKey => $stageLabel)
                        @php
                            $stageIndex = array_search($stageKey, $statusKeys);
                            $isPassed = $currentIndex !== false && $stageIndex <= $currentIndex;
                            $isCurrent = $order->status === $stageKey;
                        @endphp
                        <div class="p-3 rounded-xl border text-center transition {{ $isCurrent ? 'bg-emerald-950/60 border-emerald-500 text-emerald-300 font-bold shadow-lg shadow-emerald-950/40' : ($isPassed ? 'bg-slate-850 border-emerald-700/60 text-slate-200' : 'bg-slate-900/40 border-slate-800 text-slate-500') }}">
                            <div class="w-6 h-6 mx-auto mb-1.5 rounded-full flex items-center justify-center text-xs font-bold {{ $isCurrent ? 'bg-emerald-500 text-slate-950' : ($isPassed ? 'bg-emerald-700 text-white' : 'bg-slate-800 text-slate-500') }}">
                                {{ $isPassed && !$isCurrent ? '✓' : ($stageIndex + 1) }}
                            </div>
                            <span class="text-xs block">{{ $stageLabel }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Status Transition Action Bar (Authorized Seller / Admin Workflow) --}}
        @php
            $isSellerParty = ($user->id === $order->seller_user_id)
                || ($order->supplier_organization_id && $user->supplierOrganizations()->where('supplier_organizations.id', $order->supplier_organization_id)->exists())
                || ($order->fpo_organization_id && $user->fpoOrganizations()->where('fpo_organizations.id', $order->fpo_organization_id)->exists());
            $canUpdateStatus = $isSellerParty || $user->isAdmin();
        @endphp

        @if($canUpdateStatus && !$isCancelled && $order->status !== 'delivered')
            <div class="bg-slate-850 rounded-2xl border border-emerald-500/40 p-6 shadow-xl">
                <h3 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Update Fulfilment Stage
                </h3>
                <p class="text-xs text-slate-400 mb-4">Advance the order through the verified operational pipeline.</p>

                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="space-y-4">
                    @csrf

                    @php
                        $nextStatusMap = [
                            'pending_confirmation' => ['status' => 'confirmed', 'label' => 'Confirm Order Acceptance', 'color' => 'bg-blue-600 hover:bg-blue-500'],
                            'confirmed' => ['status' => 'processing', 'label' => 'Start Processing & Packaging', 'color' => 'bg-indigo-600 hover:bg-indigo-500'],
                            'processing' => ['status' => 'ready_for_dispatch', 'label' => 'Mark Ready for Dispatch', 'color' => 'bg-teal-600 hover:bg-teal-500'],
                            'ready_for_dispatch' => ['status' => 'dispatched', 'label' => 'Mark as Dispatched / In Transit', 'color' => 'bg-cyan-600 hover:bg-cyan-500'],
                            'dispatched' => ['status' => 'delivered', 'label' => 'Confirm Final Delivery & Handover', 'color' => 'bg-emerald-600 hover:bg-emerald-500'],
                        ];
                        $nextStep = $nextStatusMap[$order->status] ?? null;
                    @endphp

                    @if($nextStep)
                        <input type="hidden" name="status" value="{{ $nextStep['status'] }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="notes" class="block text-xs font-semibold text-slate-300 mb-1">
                                    Status Update Notes / Vehicle &amp; Dispatch Number
                                </label>
                                <input type="text" name="notes" id="notes" placeholder="e.g. Dispatched via Truck GJ-03-AB-4412, driver contact: 9876543210"
                                    class="w-full rounded-xl bg-slate-900 border border-slate-700 px-4 py-2 text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>

                            <div>
                                <label for="payment_status" class="block text-xs font-semibold text-slate-300 mb-1">
                                    Payment Tracking Status
                                </label>
                                <select name="payment_status" id="payment_status" class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <option value="" disabled selected>— Leave unchanged (Current: {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}) —</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="payment_pending">Payment Pending Verification</option>
                                    <option value="escrow_held">Escrow Held</option>
                                    <option value="paid_offline">Paid Offline (Direct Mandi Cash / Bank Wire)</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-lg transition {{ $nextStep['color'] }}">
                                {{ $nextStep['label'] }} &rarr;
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        @endif

        {{-- Order Commercial Line Items & Calculation Card --}}
        <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 overflow-hidden shadow-xl">
            <div class="px-6 py-4 bg-slate-850 border-b border-slate-700/70 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-white text-base">Order Specifications &amp; Commercial Terms</h2>
                    <span class="text-xs text-slate-400">Formal agreed line item schedule</span>
                </div>
                <span class="text-xs text-slate-400 font-mono">PO Ref: {{ $order->po_number }}</span>
            </div>

            <div class="p-6 space-y-6">
                {{-- Parties Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm border-b border-slate-700/60 pb-6">
                    <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/50">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block mb-2">Seller / Supplier</span>
                        <div class="font-bold text-white text-base">{{ $order->getSellerDisplayName() }}</div>
                        <div class="text-xs text-slate-400 capitalize mt-1">Role: {{ $order->seller_type }}</div>
                        @if($order->sellerUser)
                            <div class="text-xs text-slate-400 mt-1">Contact: {{ $order->sellerUser->phone ?? '—' }} &bull; {{ $order->sellerUser->district ?? '' }}</div>
                        @endif
                    </div>

                    <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/50">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block mb-2">Buyer / Consignee</span>
                        <div class="font-bold text-white text-base">{{ $order->buyer?->name ?? 'Direct Buyer' }}</div>
                        <div class="text-xs text-slate-400 mt-1">Destination: {{ $order->delivery_district }}</div>
                        <div class="text-xs text-slate-400 mt-1">Delivery Address: {{ $order->delivery_address ?: 'As per registered buyer facility' }}</div>
                    </div>
                </div>

                {{-- Line Items Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-700 text-xs uppercase tracking-wider text-slate-400">
                                <th class="py-3 px-2">Item Description &amp; Source</th>
                                <th class="py-3 px-4 text-center">Ordered Quantity</th>
                                <th class="py-3 px-4 text-right">Unit Price</th>
                                <th class="py-3 px-4 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-750">
                            <tr>
                                <td class="py-4 px-2">
                                    <div class="font-bold text-white">{{ $order->title ?? $order->item_description }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        @if($order->produceLot)
                                            Farm Produce Lot: 
                                            <a href="{{ route('produce.show', $order->produceLot->id) }}" class="text-emerald-400 hover:underline">
                                                #{{ $order->produceLot->lot_number }} ({{ $order->produceLot->crop?->en_name }})
                                            </a>
                                        @elseif($order->product)
                                            Supplier Input: 
                                            <a href="{{ route('products.show', $order->product->id) }}" class="text-emerald-400 hover:underline">
                                                {{ $order->product->name }} ({{ $order->product->brand_name }})
                                            </a>
                                        @elseif($order->collectiveListing)
                                            FPO Collective Lot: 
                                            <a href="{{ route('fpo.collective.show', $order->collectiveListing->id) }}" class="text-emerald-400 hover:underline">
                                                #{{ $order->collectiveListing->listing_number }}
                                            </a>
                                        @else
                                            Agricultural Commodity Lot
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center font-medium text-slate-200">
                                    {{ number_format((float)$order->quantity, 1) }} {{ $order->unit }}
                                </td>
                                <td class="py-4 px-4 text-right font-medium text-white">
                                    ₹{{ number_format((float)$order->unit_price, 2) }}
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-white">
                                    ₹{{ number_format((float)$order->subtotal, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Financial Totals Breakdown --}}
                <div class="flex justify-end pt-4 border-t border-slate-700/60">
                    <div class="w-full md:w-80 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal:</span>
                            <span class="text-white font-medium">₹{{ number_format((float)$order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Freight &amp; Handling:</span>
                            <span class="text-white font-medium">₹{{ number_format((float)$order->additional_charges, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>GST / Statutory Taxes:</span>
                            <span class="text-white font-medium">₹{{ number_format((float)$order->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-extrabold text-emerald-400 pt-2 border-t border-slate-700">
                            <span>Total Contract Amount:</span>
                            <span class="text-xl">₹{{ number_format((float)$order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Dates and Fulfilment Notes --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-700/60 text-sm">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Delivery Schedule</h4>
                        <div class="space-y-1.5 text-slate-300">
                            <div><span class="text-slate-400">Target Delivery Date:</span> <strong class="text-white">{{ $order->target_delivery_date?->format('d M Y') ?? 'Flexible' }}</strong></div>
                            @if($order->actual_delivery_date)
                                <div><span class="text-slate-400">Actual Delivery Date:</span> <strong class="text-emerald-400">{{ $order->actual_delivery_date->format('d M Y') }}</strong></div>
                            @endif
                            <div><span class="text-slate-400">Payment Terms:</span> <strong class="text-white">{{ $order->payment_terms ?: 'Standard APMC Mandi settlement' }}</strong></div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Order Notes</h4>
                        <p class="text-slate-300 italic text-xs">
                            {{ $order->notes ?: 'No special instructions recorded.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Status History & Audit Log --}}
        <div class="bg-slate-800/60 rounded-2xl border border-slate-700/70 p-6 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>📜</span> Order Lifecycle Audit Trail
            </h3>
            <p class="text-xs text-slate-400">Immutable chronological status logs recorded with timestamps and acting parties.</p>

            @if($order->statusLogs->isEmpty())
                <p class="text-xs text-slate-400 italic">Order registered; no status changes logged yet.</p>
            @else
                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-700">
                    @foreach($order->statusLogs as $log)
                        <div class="relative text-xs">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-emerald-500 border-2 border-slate-900"></span>
                            <div class="flex items-center gap-2 font-semibold text-white">
                                <span class="capitalize">{{ str_replace('_', ' ', $log->to_status) }}</span>
                                <span class="text-slate-500 font-normal">&bull; {{ $log->created_at->format('d M Y, h:i A') }}</span>
                                @if($log->actor)
                                    <span class="text-slate-400 font-normal">by {{ $log->actor->name }}</span>
                                @endif
                            </div>
                            @if($log->notes)
                                <p class="text-slate-300 mt-1 italic">{{ $log->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Cancellation Section (Conditional) --}}
        @if($order->canBeCancelled() && ($user->id === $order->buyer_id || $user->id === $order->seller_user_id || $user->isAdmin()))
            <div class="bg-rose-950/20 border border-rose-800/40 rounded-2xl p-6">
                <details class="group">
                    <summary class="cursor-pointer text-sm font-bold text-rose-300 hover:text-rose-200 flex items-center justify-between">
                        <span>Cancel this Purchase Order</span>
                        <span class="text-xs text-slate-400 group-open:rotate-180 transition">&darr;</span>
                    </summary>
                    <div class="mt-4 pt-4 border-t border-rose-800/40 text-xs text-slate-300 space-y-3">
                        <p>
                            Orders in <strong>Pending Confirmation</strong> or <strong>Confirmed</strong> state can be cancelled. Cancelling automatically restores committed stock to available inventory.
                        </p>
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.')">
                            @csrf
                            <div>
                                <label for="cancellation_reason" class="block font-semibold text-slate-300 mb-1">Reason for Cancellation <span class="text-rose-400">*</span></label>
                                <textarea name="cancellation_reason" id="cancellation_reason" rows="2" required placeholder="Specify why the order is being cancelled..."
                                    class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-white text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 text-white hover:bg-rose-500 transition">
                                    Confirm Cancellation &amp; Restore Stock
                                </button>
                            </div>
                        </form>
                    </div>
                </details>
            </div>
        @endif

    </div>
</div>
@endsection
