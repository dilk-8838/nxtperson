<?php

namespace App\Services\Mandi;

use App\Models\MandiPrice;
use Illuminate\Database\Eloquent\Collection;

class GujaratApmcSeededProvider implements MandiPriceServiceInterface
{
    /**
     * Get benchmark mandi prices for display or search.
     */
    public function getLatestPrices(?string $cropSlug = null, ?string $yardName = null): Collection
    {
        $query = MandiPrice::query();

        if ($cropSlug) {
            $query->where('crop_slug', $cropSlug);
        }

        if ($yardName) {
            $query->where('yard_name', 'like', "%{$yardName}%");
        }

        return $query->orderBy('modal_price', 'desc')->get();
    }

    /**
     * Search mandi price records across crops and market yards.
     */
    public function search(string $query): Collection
    {
        return MandiPrice::where('commodity_name', 'like', "%{$query}%")
            ->orWhere('yard_name', 'like', "%{$query}%")
            ->orWhere('district', 'like', "%{$query}%")
            ->orderBy('modal_price', 'desc')
            ->get();
    }
}
