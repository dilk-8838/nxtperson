<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SupplierMember;
use App\Models\SupplierOrganization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupplierProductController extends Controller
{
    /**
     * Resolve active supplier user and their organization.
     */
    protected function getSupplierContext(): array
    {
        $user = Auth::user();
        if (! $user || ! $user->isSupplier()) {
            $user = User::firstOrCreate(
                ['email' => 'supplier.agritech@demo.nxtperson.farm'],
                [
                    'name' => 'AgriTech Inputs Gujarat (Demo Supplier)',
                    'phone' => '9876543220',
                    'role' => 'supplier',
                    'status' => 'active',
                    'district' => 'Anand',
                    'state' => 'Gujarat',
                    'pincode' => '388001',
                    'password' => bcrypt('demo1234'),
                ]
            );
        }

        $org = $user->primarySupplierOrganization();
        if (! $org) {
            $org = SupplierOrganization::firstOrCreate(
                ['slug' => 'agritech-inputs-gujarat'],
                [
                    'name' => 'AgriTech Inputs Gujarat Pvt Ltd',
                    'category' => 'multi_category',
                    'description' => 'Manufacturer and distributor of certified hybrid seeds, micronutrients, bio-fertilizers, and farm equipment.',
                    'gst_number' => '24AAACA1234A1Z5',
                    'license_number' => 'GUJ/AGRI/DIST/2026/889',
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone ?? '9876543220',
                    'operating_address' => 'GIDC Industrial Estate, Phase 2, Anand',
                    'district' => $user->district ?? 'Anand',
                    'state' => 'Gujarat',
                    'pincode' => $user->pincode ?? '388001',
                    'verification_status' => SupplierOrganization::STATUS_APPROVED,
                    'verified_at' => now(),
                ]
            );

            SupplierMember::firstOrCreate(
                ['supplier_organization_id' => $org->id, 'user_id' => $user->id],
                ['role_in_org' => 'owner', 'is_primary_contact' => true]
            );
        }

        return [$user, $org];
    }

    /**
     * Display supplier organization's product catalogue and inventory.
     */
    public function index(Request $request)
    {
        [$user, $organization] = $this->getSupplierContext();

        $query = Product::with(['category', 'manufacturer'])
            ->where('supplier_organization_id', $organization->id)
            ->orderBy('id', 'desc');

        if ($request->filled('category')) {
            $query->where('category_slug', $request->input('category'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'in_stock') {
                $query->where('is_available', true)->where('stock_quantity', '>', 0);
            } elseif ($request->input('status') === 'out_of_stock') {
                $query->where(function ($q) {
                    $q->where('stock_quantity', '<=', 0)->orWhere('is_available', false);
                });
            }
        }

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('brand_name', 'like', "%{$term}%")
                    ->orWhere('active_ingredient', 'like', "%{$term}%");
            });
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = ProductCategory::all();

        $stats = [
            'total_products' => Product::where('supplier_organization_id', $organization->id)->count(),
            'in_stock' => Product::where('supplier_organization_id', $organization->id)->where('is_available', true)->where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => Product::where('supplier_organization_id', $organization->id)->where(function ($q) {
                $q->where('stock_quantity', '<=', 0)->orWhere('is_available', false);
            })->count(),
            'total_stock_value' => Product::where('supplier_organization_id', $organization->id)
                ->selectRaw('SUM(stock_quantity * COALESCE(unit_price, 0)) as total')
                ->value('total') ?? 0,
        ];

        return view('pages.supplier-products', compact('products', 'categories', 'stats', 'organization', 'user'));
    }

    /**
     * Show form to create a new product.
     */
    public function create()
    {
        [$user, $organization] = $this->getSupplierContext();

        $categories = ProductCategory::all();
        $manufacturers = Manufacturer::where('is_verified', true)->orderBy('name')->get();

        return view('pages.supplier-product-create', compact('categories', 'manufacturers', 'organization', 'user'));
    }

    /**
     * Store new product listing in supplier inventory.
     */
    public function store(Request $request)
    {
        [$user, $organization] = $this->getSupplierContext();

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'brand_name' => 'nullable|string|max:100',
            'category_slug' => 'required|string|exists:product_categories,slug',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0.01|max:1000000',
            'min_order_quantity' => 'required|numeric|min:1|max:100000',
            'stock_quantity' => 'required|numeric|min:0|max:1000000',
            'active_ingredient' => 'nullable|string|max:200',
            'package_size' => 'nullable|string|max:100',
            'description' => 'required|string|max:3000',
            'specifications' => 'nullable|string|max:2000',
            'usage_instructions' => 'nullable|string|max:2000',
            'safety_guidelines' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $uploaded = $request->file('image');
            $filename = 'prod_'.time().'_'.Str::random(12).'.'.$uploaded->getClientOriginalExtension();
            $targetDir = public_path('uploads/products');
            if (! file_exists($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }
            $uploaded->move($targetDir, $filename);
            $imageUrl = '/uploads/products/'.$filename;
        }

        // Fallback default manufacturer if none selected
        $manufacturerId = $validated['manufacturer_id'] ?? Manufacturer::first()?->id ?? 1;

        $slug = Str::slug($validated['name']).'-'.Str::random(6);

        $product = Product::create([
            'supplier_organization_id' => $organization->id,
            'manufacturer_id' => $manufacturerId,
            'category_slug' => $validated['category_slug'],
            'name' => $validated['name'],
            'brand_name' => $validated['brand_name'] ?? $organization->name,
            'slug' => $slug,
            'active_ingredient' => $validated['active_ingredient'] ?? null,
            'package_size' => $validated['package_size'] ?? $validated['unit'],
            'unit' => $validated['unit'],
            'unit_price' => $validated['unit_price'],
            'min_order_quantity' => $validated['min_order_quantity'],
            'stock_quantity' => $validated['stock_quantity'],
            'description' => $validated['description'],
            'specifications' => $validated['specifications'] ?? null,
            'usage_instructions' => $validated['usage_instructions'] ?? null,
            'safety_guidelines' => $validated['safety_guidelines'] ?? null,
            'image_url' => $imageUrl ?? '/images/sample-product.jpg',
            'is_active' => true,
            'is_available' => $validated['stock_quantity'] > 0,
        ]);

        return redirect()->route('supplier.products.index')
            ->with('success', "Product '{$product->name}' successfully published to supplier inventory.");
    }

    /**
     * Show form to edit product listing.
     */
    public function edit(Product $product)
    {
        [$user, $organization] = $this->getSupplierContext();

        if ($product->supplier_organization_id !== $organization->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized access to modify another supplier\'s product.');
        }

        $categories = ProductCategory::all();
        $manufacturers = Manufacturer::where('is_verified', true)->orderBy('name')->get();

        return view('pages.supplier-product-edit', compact('product', 'categories', 'manufacturers', 'organization', 'user'));
    }

    /**
     * Update product listing and inventory levels.
     */
    public function update(Request $request, Product $product)
    {
        [$user, $organization] = $this->getSupplierContext();

        if ($product->supplier_organization_id !== $organization->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized access to modify another supplier\'s product.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'brand_name' => 'nullable|string|max:100',
            'category_slug' => 'required|string|exists:product_categories,slug',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0.01|max:1000000',
            'min_order_quantity' => 'required|numeric|min:1|max:100000',
            'stock_quantity' => 'required|numeric|min:0|max:1000000',
            'active_ingredient' => 'nullable|string|max:200',
            'package_size' => 'nullable|string|max:100',
            'description' => 'required|string|max:3000',
            'specifications' => 'nullable|string|max:2000',
            'usage_instructions' => 'nullable|string|max:2000',
            'safety_guidelines' => 'nullable|string|max:2000',
            'is_available' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $uploaded = $request->file('image');
            $filename = 'prod_'.time().'_'.Str::random(12).'.'.$uploaded->getClientOriginalExtension();
            $targetDir = public_path('uploads/products');
            if (! file_exists($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }
            $uploaded->move($targetDir, $filename);
            $product->image_url = '/uploads/products/'.$filename;
        }

        $product->name = $validated['name'];
        $product->brand_name = $validated['brand_name'] ?? $product->brand_name;
        $product->category_slug = $validated['category_slug'];
        $product->unit = $validated['unit'];
        $product->unit_price = $validated['unit_price'];
        $product->min_order_quantity = $validated['min_order_quantity'];
        $product->stock_quantity = $validated['stock_quantity'];
        $product->active_ingredient = $validated['active_ingredient'] ?? null;
        $product->package_size = $validated['package_size'] ?? $validated['unit'];
        $product->description = $validated['description'];
        $product->specifications = $validated['specifications'] ?? null;
        $product->usage_instructions = $validated['usage_instructions'] ?? null;
        $product->safety_guidelines = $validated['safety_guidelines'] ?? null;
        $product->is_available = $request->boolean('is_available', true) && ($validated['stock_quantity'] > 0);
        $product->save();

        return redirect()->route('supplier.products.index')
            ->with('success', "Product '{$product->name}' successfully updated.");
    }

    /**
     * Fast inline stock & availability update.
     */
    public function updateStock(Request $request, Product $product)
    {
        [$user, $organization] = $this->getSupplierContext();

        if ($product->supplier_organization_id !== $organization->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'stock_quantity' => 'required|numeric|min:0|max:1000000',
            'is_available' => 'required|boolean',
        ]);

        $product->stock_quantity = $validated['stock_quantity'];
        $product->is_available = $validated['is_available'] && ($validated['stock_quantity'] > 0);
        $product->save();

        return redirect()->back()->with('success', "Stock for '{$product->name}' updated to {$product->stock_quantity} {$product->unit}.");
    }

    /**
     * Remove / deactivate product listing.
     */
    public function destroy(Product $product)
    {
        [$user, $organization] = $this->getSupplierContext();

        if ($product->supplier_organization_id !== $organization->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $product->update([
            'is_active' => false,
            'is_available' => false,
            'stock_quantity' => 0,
        ]);

        return redirect()->route('supplier.products.index')
            ->with('success', "Product '{$product->name}' deactivated from catalogue.");
    }
}
