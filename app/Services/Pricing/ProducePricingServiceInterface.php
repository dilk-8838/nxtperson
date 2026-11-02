<?php

namespace App\Services\Pricing;

use App\Models\ProduceLot;

interface ProducePricingServiceInterface
{
    /**
     * Compute indicative market pricing and lot valuation.
     *
     * @param  string|null  $grade  If null, uses lot->approved_grade or latest assessment grade
     * @return array [
     *               'indicative_price_per_kg' => float,
     *               'indicative_price_range' => string, // e.g. "₹68.00 - ₹74.00 / kg"
     *               'min_price_per_kg' => float,
     *               'max_price_per_kg' => float,
     *               'estimated_lot_valuation' => float,
     *               'benchmark_mandi_yard' => string,
     *               'benchmark_modal_price_qtl' => float,
     *               'grade_multiplier' => float,
     *               'data_source' => string,
     *               'calculation_explanation' => string,
     *               'is_provisional' => bool,
     *               ]
     */
    public function calculateIndicativePrice(ProduceLot $lot, ?string $grade = null): array;
}
