<?php

namespace App\Services\Pricing;

use App\Models\MandiPrice;
use App\Models\ProduceLot;

class SampleProducePricingProvider implements ProducePricingServiceInterface
{
    /**
     * Compute indicative market pricing and lot valuation.
     */
    public function calculateIndicativePrice(ProduceLot $lot, ?string $grade = null): array
    {
        $resolvedGrade = $grade ?: ($lot->approved_grade ?: ($lot->latestAssessment ? $lot->latestAssessment->grade : 'Good'));

        // Query benchmark Mandi price for crop and district
        $benchmark = MandiPrice::where('crop_slug', $lot->crop_slug)
            ->where(function ($q) use ($lot) {
                if ($lot->district) {
                    $q->where('district', $lot->district);
                }
            })
            ->orderBy('price_date', 'desc')
            ->first();

        // Fallback to any recent mandi price for the crop
        if (! $benchmark) {
            $benchmark = MandiPrice::where('crop_slug', $lot->crop_slug)
                ->orderBy('price_date', 'desc')
                ->first();
        }

        // Default crop baseline per quintal if no mandi row exists
        $fallbackPrices = [
            'cotton' => 7150.00,
            'banana' => 1850.00,
            'potato' => 1420.00,
            'chilli' => 14200.00,
            'grain' => 2820.00,
            'rice' => 2340.00,
            'tomato' => 1650.00,
            'cauliflower' => 1980.00,
        ];

        $modalPriceQtl = $benchmark ? (float) $benchmark->modal_price : ($fallbackPrices[$lot->crop_slug] ?? 3000.00);
        $minPriceQtl = $benchmark ? (float) $benchmark->min_price : ($modalPriceQtl * 0.92);
        $maxPriceQtl = $benchmark ? (float) $benchmark->max_price : ($modalPriceQtl * 1.08);
        $yardName = $benchmark ? $benchmark->yard_name : 'Gujarat APMC Benchmark (Sample)';

        // 1 Quintal = 100 kg
        $basePricePerKg = round($modalPriceQtl / 100.0, 2);
        $baseMinPerKg = round($minPriceQtl / 100.0, 2);
        $baseMaxPerKg = round($maxPriceQtl / 100.0, 2);

        // Grade adjustment multipliers
        $multiplier = match ($resolvedGrade) {
            'Good' => 1.10,          // +10% premium for Grade A
            'Intermediate' => 1.00,  // Baseline mandi benchmark
            'Bad' => 0.72,           // -28% discount for sorting/cleaning
            default => 1.00,
        };

        $indicativePricePerKg = round($basePricePerKg * $multiplier, 2);
        $minIndicativePerKg = round($baseMinPerKg * $multiplier, 2);
        $maxIndicativePerKg = round($baseMaxPerKg * $multiplier, 2);

        // Lot valuation
        $quantityKg = (float) $lot->quantity_in_kg;
        if ($quantityKg <= 0) {
            $quantityKg = (float) $lot->quantity;
            if ($lot->unit === 'quintal') {
                $quantityKg *= 100;
            } elseif ($lot->unit === 'tonne') {
                $quantityKg *= 1000;
            }
        }

        $estimatedLotValuation = round($indicativePricePerKg * $quantityKg, 2);

        $gradeLabel = match ($resolvedGrade) {
            'Good' => 'Grade A (Good: +10% Quality Premium)',
            'Intermediate' => 'Grade B (Intermediate: Baseline Mandi Rate)',
            'Bad' => 'Grade C (Bad: -28% Deduction for Sorting & Cleaning)',
            default => 'Standard Rate',
        };

        $explanation = "Sample Benchmark derived from {$yardName} (₹".number_format($modalPriceQtl, 2).'/Quintal = ₹'.number_format($basePricePerKg, 2).'/kg). '.
            "Adjusted by {$gradeLabel}. ".
            'Estimated valuation across '.number_format($quantityKg, 1).' kg is ₹'.number_format($estimatedLotValuation, 2).'. '.
            'Note: This pricing is provisional demonstration guidance only and does not constitute a legally binding purchase contract until warehouse procurement confirmation.';

        return [
            'indicative_price_per_kg' => $indicativePricePerKg,
            'indicative_price_range' => '₹'.number_format($minIndicativePerKg, 2).' - ₹'.number_format($maxIndicativePerKg, 2).' / kg',
            'min_price_per_kg' => $minIndicativePerKg,
            'max_price_per_kg' => $maxIndicativePerKg,
            'estimated_lot_valuation' => $estimatedLotValuation,
            'benchmark_mandi_yard' => $yardName,
            'benchmark_modal_price_qtl' => $modalPriceQtl,
            'grade_multiplier' => $multiplier,
            'data_source' => "Demonstration Sample Benchmark ({$yardName})",
            'calculation_explanation' => $explanation,
            'is_provisional' => true,
        ];
    }
}
