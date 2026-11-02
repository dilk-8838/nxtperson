<?php

namespace App\Services\Grading;

use App\Models\ProduceLot;

interface ProduceGradingServiceInterface
{
    /**
     * Perform quality grading assessment on a produce lot.
     *
     * @param  ProduceLot  $lot  The lot containing crop, variety, and metadata
     * @param  array  $imagePaths  List of image paths associated with this lot
     * @return array [
     *               'grade' => 'Good'|'Intermediate'|'Bad',
     *               'observed_defects' => string[],
     *               'assessment_explanation' => string,
     *               'confidence_score' => string,
     *               'manual_verification_required' => bool,
     *               'grading_method' => string,
     *               'engine_version' => string,
     *               'assessment_date' => string,
     *               'is_demonstration' => bool,
     *               ]
     */
    public function assess(ProduceLot $lot, array $imagePaths = []): array;

    /**
     * Get crop-specific grading standards and defect classification criteria.
     */
    public function getCropStandards(string $cropSlug): array;
}
