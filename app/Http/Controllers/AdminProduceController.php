<?php

namespace App\Http\Controllers;

use App\Events\ProduceInventoryPublished;
use App\Models\Crop;
use App\Models\ProduceGradingAssessment;
use App\Models\ProduceLot;
use App\Models\ProduceReservation;
use App\Models\User;
use App\Notifications\BuyerReservationStatusNotification;
use App\Notifications\FarmerLotStatusUpdatedNotification;
use App\Notifications\FarmerReservationStatusNotification;
use App\Services\Pricing\ProducePricingServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminProduceController extends Controller
{
    public function __construct(
        protected ProducePricingServiceInterface $pricingService
    ) {}

    /**
     * Admin Produce Procurement & Inventory Hub.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = ProduceLot::with(['crop', 'farmer', 'primaryImage', 'latestAssessment'])
            ->orderBy('id', 'desc');

        if ($status !== 'all' && $status) {
            $query->where('status', $status);
        }

        if ($request->filled('crop')) {
            $query->where('crop_slug', $request->input('crop'));
        }

        if ($request->filled('district')) {
            $query->where('district', $request->input('district'));
        }

        $lots = $query->paginate(15)->withQueryString();
        $crops = Crop::where('is_active', true)->get();

        $stats = [
            'pending_review_count' => ProduceLot::whereIn('status', [ProduceLot::STATUS_SUBMITTED, ProduceLot::STATUS_ASSESSED, ProduceLot::STATUS_UNDER_REVIEW])->count(),
            'approved_for_procurement_count' => ProduceLot::where('status', ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT)->count(),
            'purchase_confirmed_count' => ProduceLot::where('status', ProduceLot::STATUS_PURCHASE_CONFIRMED)->count(),
            'published_available_count' => ProduceLot::whereIn('status', [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])->count(),
            'total_stock_kg' => ProduceLot::whereIn('status', [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])->sum('available_quantity_kg'),
            'total_reservations' => ProduceReservation::count(),
        ];

        return view('pages.admin-produce-index', compact('lots', 'crops', 'stats', 'status'));
    }

    /**
     * Show comprehensive admin lot review, grading override, and price approval screen.
     */
    public function show(ProduceLot $lot)
    {
        $lot->load(['crop', 'farmer', 'images', 'assessments.assessor', 'reservations.buyer']);
        $pricing = $this->pricingService->calculateIndicativePrice($lot);

        return view('pages.admin-produce-detail', compact('lot', 'pricing'));
    }

    /**
     * Admin manual grading override or approval.
     */
    public function overrideGrade(Request $request, ProduceLot $lot)
    {
        $validated = $request->validate([
            'grade' => 'required|in:Good,Intermediate,Bad',
            'override_notes' => 'required|string|max:1000',
        ]);

        $admin = Auth::user();

        DB::transaction(function () use ($lot, $validated, $admin) {
            // Record audit assessment
            ProduceGradingAssessment::create([
                'produce_lot_id' => $lot->id,
                'grade' => $validated['grade'],
                'observed_defects' => ["Administrator Verified Grade: {$validated['grade']}"],
                'assessment_explanation' => "Manual Agronomist Override: {$validated['override_notes']}",
                'confidence_score' => '100% (Manual Expert Verification)',
                'manual_verification_required' => false,
                'grading_method' => 'Senior Agronomist Physical/Visual Audit Override',
                'engine_version' => 'Manual-Override-v1.0',
                'assessed_by' => $admin ? $admin->id : null,
                'is_admin_override' => true,
                'override_notes' => $validated['override_notes'],
                'is_demonstration' => false,
            ]);

            $lot->update([
                'approved_grade' => $validated['grade'],
                'status' => ProduceLot::STATUS_UNDER_REVIEW,
                'admin_notes' => $validated['override_notes'],
            ]);
        });

        // Notify owning farmer
        if ($lot->farmer) {
            $lot->farmer->notify(new FarmerLotStatusUpdatedNotification(
                $lot,
                'grade_overridden',
                "Quality grade verified as Grade {$validated['grade']}: {$validated['override_notes']}"
            ));
        }

        return redirect()->back()->with('success', "Quality grade updated to {$validated['grade']}. Assessment audit trail recorded.");
    }

    /**
     * Admin price approval & procurement authorization.
     */
    public function approveProcurement(Request $request, ProduceLot $lot)
    {
        $validated = $request->validate([
            'approved_procurement_price_per_kg' => 'required|numeric|min:0.5',
            'buyer_sale_price_per_kg' => 'required|numeric|min:0.5',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $lot->update([
            'approved_procurement_price_per_kg' => $validated['approved_procurement_price_per_kg'],
            'buyer_sale_price_per_kg' => $validated['buyer_sale_price_per_kg'],
            'status' => ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT,
            'admin_notes' => $validated['admin_notes'] ?? $lot->admin_notes,
        ]);

        // Notify owning farmer
        if ($lot->farmer) {
            $lot->farmer->notify(new FarmerLotStatusUpdatedNotification(
                $lot,
                'procurement_approved',
                "Procurement price authorized at ₹{$validated['approved_procurement_price_per_kg']}/kg. Ready for purchase confirmation."
            ));
        }

        return redirect()->back()->with('success', "Procurement price approved at ₹{$validated['approved_procurement_price_per_kg']}/kg. Ready for purchase confirmation.");
    }

    /**
     * Admin confirm company purchase with farmer.
     */
    public function confirmPurchase(Request $request, ProduceLot $lot)
    {
        $validated = $request->validate([
            'confirmed_quantity_kg' => 'required|numeric|min:0.1',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $confirmedKg = (float) $validated['confirmed_quantity_kg'];

        $lot->update([
            'confirmed_quantity_kg' => $confirmedKg,
            'status' => ProduceLot::STATUS_PURCHASE_CONFIRMED,
            'admin_notes' => $validated['admin_notes'] ?? $lot->admin_notes,
        ]);

        // Notify owning farmer
        if ($lot->farmer) {
            $lot->farmer->notify(new FarmerLotStatusUpdatedNotification(
                $lot,
                'purchase_confirmed',
                'Procurement purchase confirmed for '.number_format($confirmedKg, 1).' kg. Ready to publish to marketplace.'
            ));
        }

        return redirect()->back()->with('success', 'Procurement purchase confirmed for '.number_format($confirmedKg, 1).' kg. Ready to publish to marketplace.');
    }

    /**
     * Publish stock to buyers (INVENTORY_AVAILABLE).
     * Dispatches real-time event & notifications.
     */
    public function publishInventory(Request $request, ProduceLot $lot)
    {
        $validated = $request->validate([
            'buyer_sale_price_per_kg' => 'required|numeric|min:0.5',
        ]);

        // Guard: Lot must have confirmed procurement
        if (! in_array($lot->status, [ProduceLot::STATUS_PURCHASE_CONFIRMED, ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT])) {
            throw ValidationException::withMessages([
                'status' => 'Lot must have approved procurement and confirmed purchase before stock publication.',
            ]);
        }

        DB::transaction(function () use ($lot, $validated) {
            $confirmedKg = (float) ($lot->confirmed_quantity_kg ?: $lot->quantity_in_kg);

            $lot->update([
                'confirmed_quantity_kg' => $confirmedKg,
                'available_quantity_kg' => $confirmedKg,
                'reserved_quantity_kg' => 0,
                'sold_quantity_kg' => 0,
                'buyer_sale_price_per_kg' => $validated['buyer_sale_price_per_kg'],
                'status' => ProduceLot::STATUS_INVENTORY_AVAILABLE,
                'published_at' => now(),
            ]);
        });

        // Notify owning farmer that produce lot is live
        if ($lot->farmer) {
            $lot->farmer->notify(new FarmerLotStatusUpdatedNotification(
                $lot,
                'published',
                "Your produce lot #{$lot->lot_number} is now live on the Buyer Marketplace with ".number_format($lot->available_quantity_kg, 1)." kg at ₹{$lot->buyer_sale_price_per_kg}/kg."
            ));
        }

        // Dispatch inventory published event after commit to notify eligible buyers
        event(new ProduceInventoryPublished($lot->fresh(['crop', 'primaryImage'])));

        $buyerCount = User::where('role', 'buyer')->count();

        return redirect()->back()->with('success', "Stock published! Lot #{$lot->lot_number} is now live on the Buyer Marketplace with ".number_format($lot->available_quantity_kg, 1)." kg at ₹{$lot->buyer_sale_price_per_kg}/kg. Real-time notifications dispatched to {$buyerCount} registered buyer account(s).");
    }

    /**
     * Admin reject produce lot.
     */
    public function reject(Request $request, ProduceLot $lot)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $lot->update([
            'status' => ProduceLot::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Notify owning farmer about rejection and reason
        if ($lot->farmer) {
            $lot->farmer->notify(new FarmerLotStatusUpdatedNotification(
                $lot,
                'rejected',
                "Produce lot #{$lot->lot_number} was rejected by agronomist audit: {$validated['rejection_reason']}"
            ));
        }

        return redirect()->back()->with('warning', "Lot #{$lot->lot_number} rejected. Reason logged.");
    }

    /**
     * Buyer Reservations Management.
     */
    public function reservations()
    {
        $reservations = ProduceReservation::with(['lot.crop', 'lot.farmer', 'buyer'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('pages.admin-produce-reservations', compact('reservations'));
    }

    /**
     * Update reservation status (fulfill or cancel).
     */
    public function updateReservationStatus(Request $request, ProduceReservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:fulfilled,cancelled,confirmed',
        ]);

        $newStatus = $validated['status'];

        DB::transaction(function () use ($reservation, $newStatus) {
            $lot = ProduceLot::where('id', $reservation->produce_lot_id)->lockForUpdate()->firstOrFail();

            if ($reservation->status === 'confirmed' && $newStatus === 'cancelled') {
                // Restore reserved quantity to available
                $lot->reserved_quantity_kg = max(0, $lot->reserved_quantity_kg - $reservation->quantity_kg);
                $lot->available_quantity_kg += $reservation->quantity_kg;
                if ($lot->status === ProduceLot::STATUS_SOLD_OUT && $lot->available_quantity_kg > 0) {
                    $lot->status = ProduceLot::STATUS_PARTIALLY_SOLD;
                }
                $lot->save();
            } elseif ($reservation->status === 'confirmed' && $newStatus === 'fulfilled') {
                // Move from reserved to sold
                $lot->reserved_quantity_kg = max(0, $lot->reserved_quantity_kg - $reservation->quantity_kg);
                $lot->sold_quantity_kg += $reservation->quantity_kg;
                $lot->save();
            }

            $reservation->update(['status' => $newStatus]);
        });

        // Notify the buyer
        if ($reservation->buyer) {
            $reservation->buyer->notify(new BuyerReservationStatusNotification($reservation, $newStatus));
        }

        // Notify the farmer
        if ($reservation->lot && $reservation->lot->farmer) {
            $reservation->lot->farmer->notify(new FarmerReservationStatusNotification($reservation, $newStatus));
        }

        return redirect()->back()->with('success', "Reservation #{$reservation->reservation_number} status updated to {$newStatus}.");
    }

    /**
     * Buyer Notification History Log.
     */
    public function notificationHistory()
    {
        $notifications = DB::table('notifications')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.admin-produce-notifications', compact('notifications'));
    }
}
