<?php

namespace App\Services\Mandi;

use Illuminate\Database\Eloquent\Collection;

interface MandiPriceServiceInterface
{
    /**
     * Get benchmark mandi prices for display or search.
     */
    public function getLatestPrices(?string $cropSlug = null, ?string $yardName = null): Collection;

    /**
     * Search mandi price records.
     */
    public function search(string $query): Collection;
}
