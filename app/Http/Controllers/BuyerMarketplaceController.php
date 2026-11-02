<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\FpoCollectiveListing;
use App\Models\ProduceLot;
use App\Models\ProduceReservation;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Notifications\AdminReservationAlertNotification;
use App\Notifications\FarmerProduceInterestNotification;
use App\Notifications\FarmerReservationReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BuyerMarketplaceController extends Controller
{
    /**
     * Resolve active buyer user (with demo fallback).
     */
    protected function getBuyerUser(): User
    {
        $user = Auth::user();
        if ($user && $user->role === 'buyer') {
            return $user;
        }

        // Demo buyer fallback
        return User::firstOrCreate(
            ['email' => 'buyer.procurement@demo.nxtperson.farm'],
            [
                'name' => 'Adani Agri Fresh / Saurashtra Agro (Demo Buyer)',
                'phone' => '9876543240',
                'role' => 'buyer',
                'district' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380001',
                'password' => bcrypt('demo1234'),
            ]
        );
    }

    /**
     * Buyer Produce Marketplace (Browse certified available produce, supplier inputs, and FPO bulk lots).
     */
    public function index(Request $request)
    {
        $currentTab = $request->input('tab', 'produce');

        // 1. Farm Produce Lots
        $produceQuery = ProduceLot::with(['crop', 'primaryImage', 'latestAssessment', 'farmer'])
            ->whereIn('status', [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])
            ->where('available_quantity_kg', '>', 0)
            ->orderBy('published_at', 'desc');

        if ($request->filled('crop')) {
            $produceQuery->where('crop_slug', $request->input('crop'));
        }
        if ($request->filled('district')) {
            $produceQuery->where('district', $request->input('district'));
        }
        if ($request->filled('grade')) {
            $produceQuery->where('approved_grade', $request->input('grade'));
        }
        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $produceQuery->where(function ($q) use ($term) {
                $q->where('variety', 'like', "%{$term}%")
                    ->orWhere('lot_number', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }
        $lots = $produceQuery->paginate(12)->withQueryString();

        // 2. Supplier Input Products
        $productQuery = Product::with(['category', 'supplierOrganization', 'manufacturer'])
            ->where('is_active', true)
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('id', 'desc');

        if ($request->filled('category')) {
            $productQuery->where('category_slug', $request->input('category'));
        }
        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $productQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('brand_name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }
        $products = $productQuery->paginate(12, ['*'], 'products_page')->withQueryString();

        // 3. FPO Collective Bulk Listings
        $fpoQuery = FpoCollectiveListing::with(['crop', 'fpoOrganization'])
            ->whereIn('status', [FpoCollectiveListing::STATUS_PUBLISHED, FpoCollectiveListing::STATUS_PARTIALLY_SOLD])
            ->where('available_quantity_kg', '>', 0)
            ->orderBy('published_at', 'desc');

        if ($request->filled('crop')) {
            $fpoQuery->where('crop_slug', $request->input('crop'));
        }
        if ($request->filled('district')) {
            $fpoQuery->where('district', $request->input('district'));
        }
        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $fpoQuery->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('listing_number', 'like', "%{$term}%")
                    ->orWhere('variety', 'like', "%{$term}%");
            });
        }
        $collectiveListings = $fpoQuery->paginate(12, ['*'], 'fpo_page')->withQueryString();

        $crops = Crop::where('is_active', true)->get();
        $categories = ProductCategory::all();
        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad', 'Kheda', 'Vadodara', 'Amreli', 'Bhavnagar', 'Patan'];

        $stats = [
            'available_lots_count' => ProduceLot::whereIn('status', [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])
                ->where('available_quantity_kg', '>', 0)
                ->count(),
            'total_available_tonnes' => round(ProduceLot::whereIn('status', [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])
                ->sum('available_quantity_kg') / 1000, 1),
            'verified_crops_count' => ProduceLot::whereIn('status', [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])
                ->distinct('crop_slug')
                ->count('crop_slug'),
            'supplier_products_count' => Product::where('is_active', true)->where('is_available', true)->where('stock_quantity', '>', 0)->count(),
            'fpo_bulk_lots_count' => FpoCollectiveListing::whereIn('status', [FpoCollectiveListing::STATUS_PUBLISHED, FpoCollectiveListing::STATUS_PARTIALLY_SOLD])->count(),
        ];

        return view('pages.buyer-marketplace', compact('lots', 'products', 'collectiveListings', 'crops', 'categories', 'districts', 'stats', 'currentTab'));
    }

    /**
     * Show approved produce lot details to prospective buyers.
     * Guaranteed NO confidential farmer identity exposed!
     */
    public function show(ProduceLot $lot)
    {
        // Security check: Buyers can ONLY view lots in inventory_available or partially_sold
        if (! in_array($lot->status, [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD, ProduceLot::STATUS_SOLD_OUT])) {
            $user = Auth::user();
            if (! $user || (! $user->isAdmin() && $user->id !== $lot->farmer_id)) {
                abort(404, 'Produce lot is not published or currently unavailable.');
            }
        }

        $lot->load(['crop', 'images', 'latestAssessment']);

        return view('pages.buyer-lot-detail', compact('lot'));
    }

    /**
     * Concurrency-safe Produce Lot Reservation.
     */
    public function reserve(Request $request, ProduceLot $lot)
    {
        $buyer = $this->getBuyerUser();

        $validated = $request->validate([
            'quantity_kg' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $requestedKg = (float) $validated['quantity_kg'];

        $reservation = DB::transaction(function () use ($lot, $buyer, $requestedKg, $validated) {
            // Lock lot row for concurrency safety
            $lockedLot = ProduceLot::where('id', $lot->id)->lockForUpdate()->firstOrFail();

            if (! in_array($lockedLot->status, [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])) {
                throw ValidationException::withMessages([
                    'quantity_kg' => 'This produce lot is no longer open for buyer orders.',
                ]);
            }

            if ($lockedLot->available_quantity_kg < $requestedKg) {
                throw ValidationException::withMessages([
                    'quantity_kg' => 'Requested quantity ('.number_format($requestedKg, 1).' kg) exceeds currently available stock ('.number_format($lockedLot->available_quantity_kg, 1).' kg).',
                ]);
            }

            // Price per kg
            $pricePerKg = (float) ($lockedLot->buyer_sale_price_per_kg ?: 0);
            $totalPrice = round($requestedKg * $pricePerKg, 2);

            // Deduct available, increment reserved
            $lockedLot->available_quantity_kg -= $requestedKg;
            $lockedLot->reserved_quantity_kg += $requestedKg;

            if ($lockedLot->available_quantity_kg <= 0.001) {
                $lockedLot->available_quantity_kg = 0;
                $lockedLot->status = ProduceLot::STATUS_SOLD_OUT;
            } else {
                $lockedLot->status = ProduceLot::STATUS_PARTIALLY_SOLD;
            }

            $lockedLot->save();

            $resNumber = 'RES-'.strtoupper(substr($lockedLot->crop_slug, 0, 3)).'-'.date('Ymd').'-'.rand(1000, 9999);

            $reservation = ProduceReservation::create([
                'reservation_number' => $resNumber,
                'produce_lot_id' => $lockedLot->id,
                'buyer_id' => $buyer->id,
                'quantity_kg' => $requestedKg,
                'price_per_kg' => $pricePerKg,
                'total_price' => $totalPrice,
                'status' => 'confirmed',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Persist unified PurchaseOrder linked to this reservation
            $poNumber = 'PO-'.date('Ymd').'-'.rand(1000, 9999);
            PurchaseOrder::create([
                'po_number' => $poNumber,
                'produce_reservation_id' => $reservation->id,
                'buyer_id' => $buyer->id,
                'seller_user_id' => $lockedLot->farmer_id,
                'seller_type' => 'farmer',
                'item_type' => 'produce',
                'produce_lot_id' => $lockedLot->id,
                'item_description' => "{$lockedLot->variety} (Produce Lot #{$lockedLot->lot_number})",
                'quantity' => $requestedKg,
                'unit' => 'kg',
                'unit_price' => $pricePerKg,
                'subtotal' => $totalPrice,
                'additional_charges' => 0,
                'tax_amount' => 0,
                'total_amount' => $totalPrice,
                'delivery_address' => "{$lockedLot->district}, Gujarat",
                'delivery_district' => $lockedLot->district,
                'status' => PurchaseOrder::STATUS_CONFIRMED,
                'payment_status' => PurchaseOrder::PAYMENT_UNPAID,
                'payment_terms' => 'APMC Mandi Direct Settlement (Due upon verified delivery)',
                'notes' => $validated['notes'] ?? null,
            ]);

            return $reservation;
        });

        // Notify owning farmer about reservation / purchase request
        if ($lot->farmer) {
            $lot->farmer->notify(new FarmerReservationReceivedNotification($reservation, $buyer));
        }

        // Notify administrators of reservation for operational triage
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new AdminReservationAlertNotification($reservation, $lot->fresh()));
        }

        return redirect()->route('buyer.dashboard')
            ->with('success', "Order #{$reservation->reservation_number} confirmed! Reserved ".number_format($requestedKg, 1)." kg of {$lot->variety} for ₹".number_format($reservation->total_price, 2).'.');
    }

    /**
     * Buyer expresses interest or submits an inquiry on a produce listing.
     */
    public function expressInterest(Request $request, ProduceLot $lot)
    {
        $buyer = $this->getBuyerUser();

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        if ($lot->farmer) {
            $lot->farmer->notify(new FarmerProduceInterestNotification(
                $lot,
                $buyer,
                $validated['notes'] ?? null
            ));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Interest recorded! The farmer has been notified of your interest.',
            ]);
        }

        return redirect()->back()->with('success', 'Interest recorded! Farmer has been notified of your purchase interest.');
    }

    /**
     * Buyer Dashboard.
     */
    public function dashboard()
    {
        $buyer = $this->getBuyerUser();

        $reservations = ProduceReservation::with(['lot.crop', 'lot.primaryImage'])
            ->where('buyer_id', $buyer->id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        $notifications = $buyer->notifications()->take(10)->get();
        $unreadCount = $buyer->unreadNotifications()->count();

        $stats = [
            'active_reservations_count' => ProduceReservation::where('buyer_id', $buyer->id)->whereIn('status', ['requested', 'confirmed'])->count(),
            'total_procured_kg' => ProduceReservation::where('buyer_id', $buyer->id)->whereIn('status', ['confirmed', 'fulfilled'])->sum('quantity_kg'),
            'total_spend' => ProduceReservation::where('buyer_id', $buyer->id)->whereIn('status', ['confirmed', 'fulfilled'])->sum('total_price'),
            'unread_notifications' => $unreadCount,
        ];

        return view('pages.buyer-dashboard', compact('buyer', 'reservations', 'notifications', 'unreadCount', 'stats'));
    }

    /**
     * Buyer Notification Center.
     */
    public function notifications()
    {
        $buyer = $this->getBuyerUser();
        $notifications = $buyer->notifications()->paginate(15);
        $unreadCount = $buyer->unreadNotifications()->count();

        return view('pages.buyer-notifications', compact('buyer', 'notifications', 'unreadCount'));
    }

    /**
     * Live Polling Endpoint for Real-time Notification Popups without page reload.
     * Returns newly arrived notifications since client's last poll timestamp.
     */
    public function pollNotifications(Request $request)
    {
        $buyer = $this->getBuyerUser();
        $since = $request->input('since');

        $query = $buyer->unreadNotifications()->orderBy('created_at', 'desc');

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $newNotifications = $query->take(5)->get();
        $totalUnread = $buyer->unreadNotifications()->count();

        $items = $newNotifications->map(function ($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->data['title'] ?? 'New Produce Available',
                'message' => $notif->data['message'] ?? '',
                'crop_name' => $notif->data['crop_name'] ?? '',
                'variety' => $notif->data['variety'] ?? '',
                'grade' => $notif->data['grade'] ?? 'Good',
                'available_quantity_kg' => $notif->data['available_quantity_kg'] ?? 0,
                'district' => $notif->data['district'] ?? '',
                'price_per_kg' => $notif->data['buyer_sale_price_per_kg'] ?? 0,
                'image_url' => $notif->data['image_url'] ?? '',
                'view_url' => $notif->data['view_url'] ?? route('buyer.marketplace'),
                'created_at' => $notif->created_at->toIso8601String(),
                'time_ago' => $notif->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'unread_count' => $totalUnread,
            'new_count' => $items->count(),
            'has_new' => $items->isNotEmpty(),
            'notifications' => $items,
            'polled_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead(string $id)
    {
        $buyer = $this->getBuyerUser();
        $notification = $buyer->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'unread_count' => $buyer->unreadNotifications()->count()]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead()
    {
        $buyer = $this->getBuyerUser();
        $buyer->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'unread_count' => 0]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
