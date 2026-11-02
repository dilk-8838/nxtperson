<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a filtered, searchable product catalog.
     */
    public function index(Request $request)
    {
        $query = Product::with(['manufacturer', 'category', 'distributors'])
            ->where('is_active', true);

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_slug', $request->input('category'));
        }

        // Filter by Target Crop
        if ($request->filled('crop')) {
            $cropSlug = $request->input('crop');
            $query->whereJsonContains('target_crops', $cropSlug);
        }

        // Filter by Manufacturer
        if ($request->filled('manufacturer')) {
            $query->whereHas('manufacturer', function ($q) use ($request) {
                $q->where('slug', $request->input('manufacturer'));
            });
        }

        // Keyword Search
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('active_ingredient', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('manufacturer', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $products = $query->orderBy('is_featured', 'desc')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = ProductCategory::all();
        $manufacturers = Manufacturer::where('is_verified', true)->orderBy('name')->get();
        $crops = Crop::where('is_active', true)->orderBy('en_name')->get();

        return view('pages.products-catalog', compact(
            'products',
            'categories',
            'manufacturers',
            'crops'
        ));
    }

    /**
     * Display a specific product detail page.
     */
    public function show(string $slug)
    {
        $product = Product::with(['manufacturer', 'category', 'distributors'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Relevant crops
        $targetCrops = Crop::whereIn('slug', $product->target_crops ?? [])->get();

        // Related inputs in same category
        $relatedProducts = Product::where('category_slug', $product->category_slug)
            ->where('id', '!=', $product->id)
            ->take(3)
            ->get();

        return view('pages.product-detail', compact('product', 'targetCrops', 'relatedProducts'));
    }
}
