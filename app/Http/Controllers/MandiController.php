<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\MandiPrice;
use Illuminate\Http\Request;

class MandiController extends Controller
{
    /**
     * Display Gujarat APMC Mandi Price Discovery.
     */
    public function index(Request $request)
    {
        $query = MandiPrice::query();

        // Filter by APMC Yard
        if ($request->filled('yard')) {
            $query->where('yard_name', $request->input('yard'));
        }

        // Filter by Crop
        if ($request->filled('crop')) {
            $query->where('crop_slug', $request->input('crop'));
        }

        // Search Keyword
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('commodity_name', 'like', "%{$search}%")
                    ->orWhere('variety', 'like', "%{$search}%")
                    ->orWhere('yard_name', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $mandiRates = $query->orderBy('modal_price', 'desc')->paginate(12)->withQueryString();

        $yards = MandiPrice::select('yard_name')->distinct()->pluck('yard_name');
        $crops = Crop::where('is_active', true)->orderBy('en_name')->get();

        $stats = [
            'total_commodities' => MandiPrice::count(),
            'total_yards' => $yards->count(),
            'highest_rate' => MandiPrice::max('modal_price'),
        ];

        return view('pages.mandi-full', compact('mandiRates', 'yards', 'crops', 'stats'));
    }
}
