<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\ProcurementRequirement;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerRequirementController extends Controller
{
    /**
     * Resolve active buyer user.
     */
    protected function getBuyerUser(): User
    {
        $user = Auth::user();
        if ($user && $user->isBuyer()) {
            return $user;
        }

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
     * Display list of bulk procurement requirements.
     */
    public function index(Request $request)
    {
        $buyer = $this->getBuyerUser();

        $query = ProcurementRequirement::with(['buyer', 'crop', 'rfqs'])
            ->where('buyer_id', $buyer->id)
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $requirements = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => ProcurementRequirement::where('buyer_id', $buyer->id)->count(),
            'open' => ProcurementRequirement::where('buyer_id', $buyer->id)->where('status', ProcurementRequirement::STATUS_OPEN)->count(),
            'rfq_created' => ProcurementRequirement::where('buyer_id', $buyer->id)->where('status', ProcurementRequirement::STATUS_RFQ_CREATED)->count(),
            'fulfilled' => ProcurementRequirement::where('buyer_id', $buyer->id)->where('status', ProcurementRequirement::STATUS_FULFILLED)->count(),
        ];

        return view('pages.buyer-requirements-index', compact('requirements', 'stats', 'buyer'));
    }

    /**
     * Show form to create new procurement requirement.
     */
    public function create()
    {
        $buyer = $this->getBuyerUser();
        $crops = Crop::where('is_active', true)->orderBy('en_name')->get();
        $categories = ProductCategory::all();
        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad', 'Kheda', 'Vadodara', 'Amreli', 'Bhavnagar', 'Patan'];

        return view('pages.buyer-requirement-create', compact('crops', 'categories', 'districts', 'buyer'));
    }

    /**
     * Persist new procurement requirement.
     */
    public function store(Request $request)
    {
        $buyer = $this->getBuyerUser();

        $validated = $request->validate([
            'type' => 'required|in:produce,input',
            'title' => 'required|string|max:200',
            'item_name' => 'required|string|max:150',
            'crop_slug' => 'nullable|string|exists:crops,slug',
            'category_slug' => 'nullable|string|exists:product_categories,slug',
            'target_quantity' => 'required|numeric|min:1|max:10000000',
            'unit' => 'required|string|max:30',
            'target_price_per_unit' => 'nullable|numeric|min:0',
            'delivery_district' => 'required|string|max:100',
            'delivery_address' => 'nullable|string|max:300',
            'desired_delivery_date' => 'nullable|date|after_or_equal:today',
            'quality_specifications' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $reqNumber = 'REQ-'.date('Ymd').'-'.rand(1000, 9999);

        $requirement = ProcurementRequirement::create([
            'requirement_number' => $reqNumber,
            'buyer_id' => $buyer->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'item_name' => $validated['item_name'],
            'crop_slug' => $validated['crop_slug'] ?? null,
            'category_slug' => $validated['category_slug'] ?? null,
            'target_quantity' => $validated['target_quantity'],
            'unit' => $validated['unit'],
            'target_price_per_unit' => $validated['target_price_per_unit'] ?? null,
            'delivery_district' => $validated['delivery_district'],
            'delivery_address' => $validated['delivery_address'] ?? null,
            'desired_delivery_date' => $validated['desired_delivery_date'] ?? null,
            'quality_specifications' => $validated['quality_specifications'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => ProcurementRequirement::STATUS_OPEN,
        ]);

        return redirect()->route('buyer.requirements.show', $requirement->id)
            ->with('success', "Procurement Requirement #{$requirement->requirement_number} successfully registered.");
    }

    /**
     * View procurement requirement details.
     */
    public function show(ProcurementRequirement $requirement)
    {
        $buyer = $this->getBuyerUser();

        if ($requirement->buyer_id !== $buyer->id && ! $buyer->isAdmin()) {
            abort(403, 'Unauthorized access to another buyer\'s procurement requirement.');
        }

        $requirement->load(['crop', 'rfqs.quotations', 'rfqs.invitations']);

        return view('pages.buyer-requirement-detail', compact('requirement', 'buyer'));
    }

    /**
     * Cancel or close procurement requirement.
     */
    public function destroy(ProcurementRequirement $requirement)
    {
        $buyer = $this->getBuyerUser();

        if ($requirement->buyer_id !== $buyer->id && ! $buyer->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $requirement->update(['status' => ProcurementRequirement::STATUS_CANCELLED]);

        return redirect()->route('buyer.requirements.index')
            ->with('success', "Requirement #{$requirement->requirement_number} has been cancelled.");
    }
}
