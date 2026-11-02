<?php

namespace App\Http\Controllers;

use App\Models\Advisory;
use App\Models\Crop;
use App\Models\Distributor;
use App\Models\FpoOrganization;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SupplierOrganization;
use App\Models\User;
use App\Notifications\FpoVerificationStatusNotification;
use App\Notifications\SupplierVerificationStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Admin Overview Dashboard.
     */
    public function index()
    {
        $stats = [
            'farmers_count' => User::where('role', 'farmer')->count(),
            'suppliers_count' => SupplierOrganization::count(),
            'pending_suppliers_count' => SupplierOrganization::where('verification_status', SupplierOrganization::STATUS_PENDING)->count(),
            'fpos_count' => FpoOrganization::count(),
            'pending_fpos_count' => FpoOrganization::where('verification_status', FpoOrganization::STATUS_PENDING)->count(),
            'buyers_count' => User::where('role', 'buyer')->count(),
            'advisories_count' => Advisory::count(),
            'escalated_advisories_count' => Advisory::where('status', 'escalated')->count(),
            'resolved_advisories_count' => Advisory::where('status', 'resolved')->count(),
            'products_count' => Product::count(),
            'distributors_count' => Distributor::count(),
        ];

        $pendingSuppliers = SupplierOrganization::where('verification_status', SupplierOrganization::STATUS_PENDING)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $pendingFpos = FpoOrganization::where('verification_status', FpoOrganization::STATUS_PENDING)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $recentAdvisories = Advisory::with('crop')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.admin-dashboard', compact('stats', 'pendingSuppliers', 'pendingFpos', 'recentAdvisories'));
    }

    /**
     * Update advisory review status and agronomist notes.
     */
    public function updateAdvisoryStatus(Request $request, Advisory $advisory)
    {
        $validated = $request->validate([
            'status' => 'required|in:ai_analyzed,escalated,resolved',
            'agronomist_notes' => 'nullable|string|max:2000',
        ]);

        $advisory->status = $validated['status'];
        if ($request->filled('agronomist_notes')) {
            $advisory->agronomist_notes = $validated['agronomist_notes'];
        }
        $advisory->save();

        return redirect()->back()->with('success', "Ticket {$advisory->ticket_number} status updated to {$advisory->status}.");
    }

    /**
     * Product Management Index.
     */
    public function products(Request $request)
    {
        $query = Product::with(['manufacturer', 'category'])->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('active_ingredient', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_slug', $request->input('category'));
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = ProductCategory::all();
        $manufacturers = Manufacturer::all();
        $crops = Crop::all();

        return view('pages.admin-products', compact('products', 'categories', 'manufacturers', 'crops'));
    }

    /**
     * Store new product.
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'category_slug' => 'required|string',
            'active_ingredient' => 'nullable|string|max:255',
            'package_size' => 'nullable|string|max:255',
            'price_guide' => 'nullable|string|max:255',
            'description' => 'required|string',
            'usage_instructions' => 'nullable|string',
            'safety_guidelines' => 'nullable|string',
            'target_crops' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
        ]);

        $slug = Str::slug($validated['name']);
        // Check uniqueness
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(5);
        }

        Product::create([
            'manufacturer_id' => $validated['manufacturer_id'],
            'category_slug' => $validated['category_slug'],
            'name' => $validated['name'],
            'slug' => $slug,
            'active_ingredient' => $validated['active_ingredient'] ?? null,
            'package_size' => $validated['package_size'] ?? null,
            'price_guide' => $validated['price_guide'] ?? null,
            'description' => $validated['description'],
            'usage_instructions' => $validated['usage_instructions'] ?? null,
            'safety_guidelines' => $validated['safety_guidelines'] ?? null,
            'target_crops' => $request->input('target_crops', []),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.products')->with('success', "Product '{$validated['name']}' successfully added to catalog.");
    }

    /**
     * Update existing product.
     */
    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'category_slug' => 'required|string',
            'active_ingredient' => 'nullable|string|max:255',
            'package_size' => 'nullable|string|max:255',
            'price_guide' => 'nullable|string|max:255',
            'description' => 'required|string',
            'usage_instructions' => 'nullable|string',
            'safety_guidelines' => 'nullable|string',
            'target_crops' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $product->update([
            'manufacturer_id' => $validated['manufacturer_id'],
            'category_slug' => $validated['category_slug'],
            'name' => $validated['name'],
            'active_ingredient' => $validated['active_ingredient'] ?? null,
            'package_size' => $validated['package_size'] ?? null,
            'price_guide' => $validated['price_guide'] ?? null,
            'description' => $validated['description'],
            'usage_instructions' => $validated['usage_instructions'] ?? null,
            'safety_guidelines' => $validated['safety_guidelines'] ?? null,
            'target_crops' => $request->input('target_crops', []),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.products')->with('success', "Product '{$product->name}' updated successfully.");
    }

    /**
     * Delete product.
     */
    public function destroyProduct(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products')->with('success', "Product '{$name}' deleted from catalog.");
    }

    /**
     * Distributor Management Index.
     */
    public function distributors(Request $request)
    {
        $query = Distributor::with('products')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where('business_name', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('pincode', 'like', "%{$search}%");
        }

        if ($request->filled('district')) {
            $query->where('district', $request->input('district'));
        }

        $distributors = $query->paginate(15)->withQueryString();
        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad', 'Kheda', 'Vadodara'];
        $categories = ProductCategory::all();

        return view('pages.admin-distributors', compact('distributors', 'districts', 'categories'));
    }

    /**
     * Store new distributor.
     */
    public function storeDistributor(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'taluka' => 'required|string|max:255',
            'address' => 'required|string',
            'pincode' => 'required|digits:6',
            'phone' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'authorized_categories' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        Distributor::create([
            'business_name' => $validated['business_name'],
            'name' => $validated['name'],
            'license_number' => $validated['license_number'],
            'district' => $validated['district'],
            'taluka' => $validated['taluka'],
            'address' => $validated['address'],
            'pincode' => $validated['pincode'],
            'phone' => $validated['phone'],
            'whatsapp' => $validated['whatsapp'] ?? $validated['phone'],
            'authorized_categories' => $request->input('authorized_categories', []),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.distributors')->with('success', "Distributor '{$validated['business_name']}' created successfully.");
    }

    /**
     * Update existing distributor.
     */
    public function updateDistributor(Request $request, Distributor $distributor)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'taluka' => 'required|string|max:255',
            'address' => 'required|string',
            'pincode' => 'required|digits:6',
            'phone' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'authorized_categories' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $distributor->update([
            'business_name' => $validated['business_name'],
            'name' => $validated['name'],
            'license_number' => $validated['license_number'],
            'district' => $validated['district'],
            'taluka' => $validated['taluka'],
            'address' => $validated['address'],
            'pincode' => $validated['pincode'],
            'phone' => $validated['phone'],
            'whatsapp' => $validated['whatsapp'] ?? $validated['phone'],
            'authorized_categories' => $request->input('authorized_categories', []),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.distributors')->with('success', "Distributor '{$distributor->business_name}' updated successfully.");
    }

    /**
     * Delete distributor.
     */
    public function destroyDistributor(Distributor $distributor)
    {
        $name = $distributor->business_name;
        $distributor->delete();

        return redirect()->route('admin.distributors')->with('success', "Distributor '{$name}' removed from directory.");
    }

    /**
     * User Account Management Index.
     */
    public function users(Request $request)
    {
        $query = User::with(['farmerProfile', 'supplierOrganizations', 'fpoOrganizations', 'buyerProfile'])
            ->orderBy('id', 'desc');

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $roleCounts = [
            'all' => User::count(),
            'farmer' => User::where('role', 'farmer')->count(),
            'supplier' => User::whereIn('role', ['supplier', 'distributor'])->count(),
            'buyer' => User::where('role', 'buyer')->count(),
            'fpo' => User::whereIn('role', ['fpo', 'cooperative'])->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('pages.admin-users', compact('users', 'roleCounts'));
    }

    /**
     * Update user account status (Activate / Suspend).
     */
    public function updateUserStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,suspended',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if ($user->isAdmin() && $user->id === Auth::id()) {
            return redirect()->back()->with('warning', 'You cannot suspend your own administrative account.');
        }

        $user->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        $statusLabel = ucfirst($validated['status']);

        return redirect()->back()->with('success', "User account '{$user->name}' status updated to {$statusLabel}.");
    }

    /**
     * Supplier Organizations & Verification Queue.
     */
    public function suppliers(Request $request)
    {
        $query = SupplierOrganization::with(['supplierMembers.user', 'verifiedBy'])
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('verification_status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('authorized_representative_name', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'all' => SupplierOrganization::count(),
            'pending' => SupplierOrganization::where('verification_status', SupplierOrganization::STATUS_PENDING)->count(),
            'approved' => SupplierOrganization::where('verification_status', SupplierOrganization::STATUS_APPROVED)->count(),
            'rejected' => SupplierOrganization::where('verification_status', SupplierOrganization::STATUS_REJECTED)->count(),
            'suspended' => SupplierOrganization::where('verification_status', SupplierOrganization::STATUS_SUSPENDED)->count(),
        ];

        return view('pages.admin-suppliers', compact('suppliers', 'statusCounts'));
    }

    /**
     * Review, approve, reject, or suspend supplier organization.
     */
    public function reviewSupplier(Request $request, SupplierOrganization $supplier)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,suspend',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $admin = Auth::user();
        $action = $validated['action'];
        $newStatus = match ($action) {
            'approve' => SupplierOrganization::STATUS_APPROVED,
            'reject' => SupplierOrganization::STATUS_REJECTED,
            'suspend' => SupplierOrganization::STATUS_SUSPENDED,
        };

        $supplier->update([
            'verification_status' => $newStatus,
            'verified_at' => now(),
            'verified_by' => $admin?->id,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        // Notify primary contact
        $primaryMember = $supplier->supplierMembers()->where('is_primary_contact', true)->first()
            ?? $supplier->supplierMembers()->first();

        if ($primaryMember && $primaryMember->user) {
            $primaryMember->user->notify(new SupplierVerificationStatusNotification(
                $supplier,
                $newStatus,
                $validated['rejection_reason'] ?? null
            ));
        }

        $actionLabel = ucfirst($action).'d';

        return redirect()->back()->with('success', "Supplier '{$supplier->name}' {$actionLabel} successfully.");
    }

    /**
     * FPO Cooperatives & Verification Queue.
     */
    public function fpos(Request $request)
    {
        $query = FpoOrganization::with(['fpoMembers.user', 'verifiedBy'])
            ->withCount('farmerMemberships')
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('verification_status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('authorized_representative_name', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $fpos = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'all' => FpoOrganization::count(),
            'pending' => FpoOrganization::where('verification_status', FpoOrganization::STATUS_PENDING)->count(),
            'approved' => FpoOrganization::where('verification_status', FpoOrganization::STATUS_APPROVED)->count(),
            'rejected' => FpoOrganization::where('verification_status', FpoOrganization::STATUS_REJECTED)->count(),
            'suspended' => FpoOrganization::where('verification_status', FpoOrganization::STATUS_SUSPENDED)->count(),
        ];

        return view('pages.admin-fpos', compact('fpos', 'statusCounts'));
    }

    /**
     * Review, approve, reject, or suspend FPO organization.
     */
    public function reviewFpo(Request $request, FpoOrganization $fpo)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,suspend',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $admin = Auth::user();
        $action = $validated['action'];
        $newStatus = match ($action) {
            'approve' => FpoOrganization::STATUS_APPROVED,
            'reject' => FpoOrganization::STATUS_REJECTED,
            'suspend' => FpoOrganization::STATUS_SUSPENDED,
        };

        $fpo->update([
            'verification_status' => $newStatus,
            'verified_at' => now(),
            'verified_by' => $admin?->id,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        // Notify primary contact
        $primaryMember = $fpo->fpoMembers()->where('is_primary', true)->first()
            ?? $fpo->fpoMembers()->first();

        if ($primaryMember && $primaryMember->user) {
            $primaryMember->user->notify(new FpoVerificationStatusNotification(
                $fpo,
                $newStatus,
                $validated['rejection_reason'] ?? null
            ));
        }

        $actionLabel = ucfirst($action).'d';

        return redirect()->back()->with('success', "FPO '{$fpo->name}' {$actionLabel} successfully.");
    }
}
