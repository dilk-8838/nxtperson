<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\ProduceGradingAssessment;
use App\Models\ProduceLot;
use App\Models\ProduceLotImage;
use App\Models\User;
use App\Notifications\AdminNewLotSubmittedNotification;
use App\Services\Grading\ProduceGradingServiceInterface;
use App\Services\Pricing\ProducePricingServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProduceLotController extends Controller
{
    public function __construct(
        protected ProduceGradingServiceInterface $gradingService,
        protected ProducePricingServiceInterface $pricingService
    ) {}

    /**
     * Get the active farmer user (handles demo fallback).
     */
    protected function getFarmerUser(): User
    {
        $user = Auth::user();
        if ($user) {
            return $user;
        }

        // In demo mode, resolve demo farmer
        return User::firstOrCreate(
            ['phone' => '9876543210'],
            [
                'name' => 'Suresh Patel (Demo Farmer)',
                'email' => 'suresh.patel@demo.nxtperson.farm',
                'role' => 'farmer',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360001',
                'password' => bcrypt('demo1234'),
            ]
        );
    }

    /**
     * Display farmer's produce lots history.
     */
    public function index(Request $request)
    {
        $farmer = $this->getFarmerUser();

        $query = ProduceLot::with(['crop', 'primaryImage', 'latestAssessment'])
            ->where('farmer_id', $farmer->id)
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('crop')) {
            $query->where('crop_slug', $request->input('crop'));
        }

        $lots = $query->paginate(10)->withQueryString();
        $crops = Crop::where('is_active', true)->get();

        $stats = [
            'total_lots' => ProduceLot::where('farmer_id', $farmer->id)->count(),
            'active_listings' => ProduceLot::where('farmer_id', $farmer->id)
                ->whereIn('status', [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])
                ->count(),
            'under_review' => ProduceLot::where('farmer_id', $farmer->id)
                ->whereIn('status', [ProduceLot::STATUS_SUBMITTED, ProduceLot::STATUS_ASSESSED, ProduceLot::STATUS_UNDER_REVIEW])
                ->count(),
            'total_procured_kg' => ProduceLot::where('farmer_id', $farmer->id)
                ->whereIn('status', [ProduceLot::STATUS_PURCHASE_CONFIRMED, ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD, ProduceLot::STATUS_SOLD_OUT])
                ->sum('confirmed_quantity_kg'),
        ];

        return view('pages.farmer-lots-index', compact('lots', 'crops', 'stats', 'farmer'));
    }

    /**
     * Show the Sell Your Produce form.
     */
    public function create()
    {
        $farmer = $this->getFarmerUser();
        $crops = Crop::where('is_active', true)->orderBy('en_name')->get();
        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad', 'Kheda', 'Vadodara', 'Amreli', 'Bhavnagar', 'Patan'];

        return view('pages.sell-produce', compact('crops', 'districts', 'farmer'));
    }

    /**
     * Store new produce lot, upload photographs, and run modular grading.
     */
    public function store(Request $request)
    {
        $farmer = $this->getFarmerUser();

        $validated = $request->validate([
            'crop_slug' => 'required|string|exists:crops,slug',
            'variety' => 'required|string|max:100',
            'quantity' => 'required|numeric|min:0.1|max:1000000',
            'unit' => 'required|in:kg,quintal,tonne',
            'district' => 'required|string|max:100',
            'taluka' => 'nullable|string|max:100',
            'pincode' => 'required|digits:6',
            'harvest_date' => 'required|date',
            'expected_price_per_kg' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'farming_method' => 'required|in:conventional,organic,natural,ipm',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'images.required' => 'Please upload at least one produce batch photograph for visual quality assessment.',
            'images.*.max' => 'Each image must not exceed 10MB in size.',
        ]);

        // Normalize quantity to kg
        $quantityInKg = match ($validated['unit']) {
            'tonne' => (float) $validated['quantity'] * 1000.0,
            'quintal' => (float) $validated['quantity'] * 100.0,
            default => (float) $validated['quantity'],
        };

        $lotNumber = 'LOT-'.strtoupper(substr($validated['crop_slug'], 0, 3)).'-'.date('Ymd').'-'.rand(1000, 9999);

        return DB::transaction(function () use ($farmer, $validated, $quantityInKg, $lotNumber, $request) {
            // 1. Create Produce Lot
            $lot = ProduceLot::create([
                'lot_number' => $lotNumber,
                'farmer_id' => $farmer->id,
                'crop_slug' => $validated['crop_slug'],
                'variety' => $validated['variety'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'quantity_in_kg' => $quantityInKg,
                'district' => $validated['district'],
                'taluka' => $validated['taluka'] ?? null,
                'pincode' => $validated['pincode'],
                'harvest_date' => $validated['harvest_date'],
                'expected_price_per_kg' => $validated['expected_price_per_kg'] ?? null,
                'description' => $validated['description'] ?? null,
                'farming_method' => $validated['farming_method'],
                'status' => ProduceLot::STATUS_ASSESSED,
                'confirmed_quantity_kg' => 0,
                'available_quantity_kg' => 0,
                'reserved_quantity_kg' => 0,
                'sold_quantity_kg' => 0,
            ]);

            // 2. Store Photographs with randomized protected names
            $storedImagePaths = [];
            $targetDir = public_path('uploads'.DIRECTORY_SEPARATOR.'produce_lots');
            if (! file_exists($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $uploadedFile) {
                    if ($uploadedFile->isValid()) {
                        $safeExt = in_array(strtolower($uploadedFile->extension()), ['jpg', 'jpeg', 'png', 'webp']) ? strtolower($uploadedFile->extension()) : 'jpg';
                        $filename = 'lot_'.time().'_'.Str::random(16).'.'.$safeExt;
                        $destinationPath = $targetDir.DIRECTORY_SEPARATOR.$filename;
                        $fileSize = (int) $uploadedFile->getSize();
                        $mimeType = $uploadedFile->getClientMimeType();
                        $originalName = $uploadedFile->getClientOriginalName();

                        try {
                            $uploadedFile->move($targetDir, $filename);
                        } catch (\Throwable $e) {
                            @copy($uploadedFile->getPathname(), $destinationPath);
                        }

                        $relativeImagePath = 'uploads/produce_lots/'.$filename;
                        $storedImagePaths[] = $relativeImagePath;

                        ProduceLotImage::create([
                            'produce_lot_id' => $lot->id,
                            'image_path' => $relativeImagePath,
                            'original_name' => $originalName,
                            'file_size' => $fileSize,
                            'mime_type' => $mimeType,
                            'is_primary' => $index === 0,
                            'is_approved_for_buyers' => true,
                        ]);
                    }
                }
            }

            // 3. Execute Modular AI Quality Assessment
            $assessmentResult = $this->gradingService->assess($lot, $storedImagePaths);

            ProduceGradingAssessment::create([
                'produce_lot_id' => $lot->id,
                'grade' => $assessmentResult['grade'],
                'observed_defects' => $assessmentResult['observed_defects'],
                'assessment_explanation' => $assessmentResult['assessment_explanation'],
                'confidence_score' => $assessmentResult['confidence_score'],
                'manual_verification_required' => $assessmentResult['manual_verification_required'],
                'grading_method' => $assessmentResult['grading_method'],
                'engine_version' => $assessmentResult['engine_version'],
                'assessed_by' => null,
                'is_admin_override' => false,
                'is_demonstration' => true,
            ]);

            // Update lot approved_grade suggested by engine
            $lot->update([
                'approved_grade' => $assessmentResult['grade'],
                'status' => ProduceLot::STATUS_ASSESSED,
            ]);

            // Notify administrators about new harvest lot requiring verification
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNewLotSubmittedNotification($lot));
            }

            return redirect()->route('farmer.lots.show', $lot->id)
                ->with('success', "Produce Lot #{$lot->lot_number} successfully registered. Simulated quality assessment completed ({$assessmentResult['grade']} Grade).");
        });
    }

    /**
     * Show produce lot details, grading assessment, and pricing calculation.
     */
    public function show(ProduceLot $lot)
    {
        $user = Auth::user();
        if (! $user && session('demo_role') === 'farmer') {
            $user = User::where('role', 'farmer')->first();
        }

        // Authorization check: Only lot owner or admin can view farmer private lot
        if (! $user || (! $user->isAdmin() && $user->id !== $lot->farmer_id)) {
            abort(403, 'Unauthorized access to another farmer\'s private produce submission.');
        }

        $lot->load([
            'crop',
            'images',
            'assessments',
            'farmer',
            'reservations.buyer',
            'memberAggregations.fpoOrganization',
            'rfqs',
            'purchaseOrders.buyer',
        ]);
        $pricing = $this->pricingService->calculateIndicativePrice($lot);

        return view('pages.farmer-lot-detail', compact('lot', 'pricing'));
    }

    /**
     * Show edit form for farmer produce lot.
     */
    public function edit(ProduceLot $lot)
    {
        $user = Auth::user() ?: $this->getFarmerUser();

        if (! $user->isAdmin() && $user->id !== $lot->farmer_id) {
            abort(403, 'Unauthorized access to modify this produce lot.');
        }

        if (in_array($lot->status, [ProduceLot::STATUS_SOLD_OUT, ProduceLot::STATUS_CANCELLED])) {
            return redirect()->route('farmer.lots.show', $lot->id)
                ->with('error', 'Completed or cancelled produce lots cannot be edited.');
        }

        $crops = Crop::where('is_active', true)->orderBy('en_name')->get();
        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad', 'Kheda', 'Vadodara', 'Amreli', 'Bhavnagar', 'Patan'];

        return view('pages.farmer-lot-edit', compact('lot', 'crops', 'districts'));
    }

    /**
     * Update farmer produce lot with stock validation.
     */
    public function update(Request $request, ProduceLot $lot)
    {
        $user = Auth::user() ?: $this->getFarmerUser();

        if (! $user->isAdmin() && $user->id !== $lot->farmer_id) {
            abort(403, 'Unauthorized access to modify this produce lot.');
        }

        if (in_array($lot->status, [ProduceLot::STATUS_SOLD_OUT, ProduceLot::STATUS_CANCELLED])) {
            return redirect()->route('farmer.lots.show', $lot->id)
                ->with('error', 'Completed or cancelled produce lots cannot be edited.');
        }

        $validated = $request->validate([
            'variety' => 'required|string|max:100',
            'quantity' => 'required|numeric|min:0.1|max:1000000',
            'unit' => 'required|in:kg,quintal,tonne',
            'district' => 'required|string|max:100',
            'taluka' => 'nullable|string|max:100',
            'pincode' => 'required|digits:6',
            'harvest_date' => 'required|date',
            'expected_price_per_kg' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'farming_method' => 'required|in:conventional,organic,natural,ipm',
        ]);

        $quantityInKg = match ($validated['unit']) {
            'tonne' => (float) $validated['quantity'] * 1000.0,
            'quintal' => (float) $validated['quantity'] * 100.0,
            default => (float) $validated['quantity'],
        };

        // Ensure new quantity is not less than committed stock
        $committedKg = (float) $lot->reserved_quantity_kg + (float) $lot->sold_quantity_kg + (float) $lot->aggregated_quantity_kg;
        if ($quantityInKg < $committedKg) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantity' => 'Cannot reduce total quantity below currently committed/reserved stock ('.number_format($committedKg, 1).' kg).']);
        }

        DB::transaction(function () use ($lot, $validated, $quantityInKg, $committedKg) {
            $lockedLot = ProduceLot::where('id', $lot->id)->lockForUpdate()->firstOrFail();

            $lockedLot->variety = $validated['variety'];
            $lockedLot->quantity = $validated['quantity'];
            $lockedLot->unit = $validated['unit'];
            $lockedLot->quantity_in_kg = $quantityInKg;
            $lockedLot->district = $validated['district'];
            $lockedLot->taluka = $validated['taluka'] ?? null;
            $lockedLot->pincode = $validated['pincode'];
            $lockedLot->harvest_date = $validated['harvest_date'];
            $lockedLot->expected_price_per_kg = $validated['expected_price_per_kg'] ?? null;
            $lockedLot->description = $validated['description'] ?? null;
            $lockedLot->farming_method = $validated['farming_method'];

            // Recalculate available quantity if already approved/published
            if (in_array($lockedLot->status, [ProduceLot::STATUS_INVENTORY_AVAILABLE, ProduceLot::STATUS_PARTIALLY_SOLD])) {
                $lockedLot->available_quantity_kg = max(0, $quantityInKg - $committedKg);
                $lockedLot->status = ($lockedLot->available_quantity_kg <= 0.001) ? ProduceLot::STATUS_SOLD_OUT : ProduceLot::STATUS_INVENTORY_AVAILABLE;
            }

            $lockedLot->save();
        });

        return redirect()->route('farmer.lots.show', $lot->id)
            ->with('success', "Produce Lot #{$lot->lot_number} successfully updated.");
    }

    /**
     * Withdraw/cancel produce listing.
     */
    public function withdraw(ProduceLot $lot)
    {
        $user = Auth::user() ?: $this->getFarmerUser();

        if (! $user->isAdmin() && $user->id !== $lot->farmer_id) {
            abort(403, 'Unauthorized.');
        }

        if (in_array($lot->status, [ProduceLot::STATUS_SOLD_OUT, ProduceLot::STATUS_CANCELLED])) {
            return redirect()->back()->with('error', 'This listing is already inactive.');
        }

        DB::transaction(function () use ($lot) {
            $lockedLot = ProduceLot::where('id', $lot->id)->lockForUpdate()->firstOrFail();
            $lockedLot->available_quantity_kg = 0;
            $lockedLot->status = ProduceLot::STATUS_CANCELLED;
            $lockedLot->save();
        });

        return redirect()->route('farmer.lots.show', $lot->id)
            ->with('success', "Produce Lot #{$lot->lot_number} has been withdrawn from the marketplace.");
    }

    /**
     * Submit lot for administrative review.
     */
    public function submitForReview(ProduceLot $lot)
    {
        $user = Auth::user();
        if (! $user && session('demo_role') === 'farmer') {
            $user = User::where('role', 'farmer')->first();
        }

        if (! $user || ($user->id !== $lot->farmer_id && ! $user->isAdmin())) {
            abort(403, 'Unauthorized.');
        }

        if ($lot->status === ProduceLot::STATUS_DRAFT || $lot->status === ProduceLot::STATUS_ASSESSED) {
            $lot->update(['status' => ProduceLot::STATUS_UNDER_REVIEW]);

            // Notify administrators if not already notified
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $exists = $admin->notifications()
                    ->where('data->lot_id', $lot->id)
                    ->where('data->type', 'admin_new_lot_submitted')
                    ->exists();

                if (! $exists) {
                    $admin->notify(new AdminNewLotSubmittedNotification($lot));
                }
            }
        }

        return redirect()->back()->with('success', "Produce Lot #{$lot->lot_number} submitted to administrator review queue.");
    }
}
