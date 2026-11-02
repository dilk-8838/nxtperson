<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    /**
     * Display searchable distributor directory with 6-digit pincode matching.
     */
    public function index(Request $request)
    {
        $query = Distributor::with('products')->where('is_active', true);

        // 6-digit Pincode filter
        if ($request->filled('pincode')) {
            $pincode = trim($request->input('pincode'));
            $query->where('pincode', 'like', "{$pincode}%");
        }

        // Generic search parameter (can be pincode or taluka or name)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            if ($search !== 'pincode') {
                $query->where(function ($q) use ($search) {
                    $q->where('pincode', 'like', "{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('taluka', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }
        }

        // District filter
        if ($request->filled('district')) {
            $query->where('district', $request->input('district'));
        }

        // Category authorization filter
        if ($request->filled('category')) {
            $cat = $request->input('category');
            $query->whereJsonContains('authorized_categories', $cat);
        }

        $distributors = $query->orderBy('district')
            ->orderBy('business_name')
            ->paginate(12)
            ->withQueryString();

        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad'];
        $categories = ProductCategory::all();

        return view('pages.distributors-directory', compact('distributors', 'districts', 'categories'));
    }
}
