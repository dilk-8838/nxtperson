<?php

namespace App\Services\Grading;

use App\Models\ProduceLot;

class SampleProduceGradingProvider implements ProduceGradingServiceInterface
{
    protected string $engineVersion = 'v1.0.0-sample';

    protected string $gradingMethod = 'Demonstration Rule-Engine (Sample Vision Heuristic)';

    /**
     * Perform quality grading assessment on a produce lot.
     */
    public function assess(ProduceLot $lot, array $imagePaths = []): array
    {
        $standards = $this->getCropStandards($lot->crop_slug);
        $variety = strtolower($lot->variety ?? '');
        $description = strtolower($lot->description ?? '');
        $farmingMethod = strtolower($lot->farming_method ?? 'conventional');
        $photoCount = count($imagePaths);

        // Evaluate sample heuristics based on crop-specific defect triggers
        $detectedDefects = [];
        $grade = 'Good';
        $explanation = '';

        // Check for negative descriptors in farmer notes or simulated edge cases
        if (str_contains($description, 'stain') || str_contains($description, 'worm') || str_contains($description, 'rot') || str_contains($description, 'blemish')) {
            $grade = 'Bad';
            $detectedDefects = $standards['bad_defects'];
            $explanation = $standards['bad_explanation'];
        } elseif (str_contains($description, 'rain') || str_contains($description, 'moisture') || str_contains($description, 'small') || str_contains($description, 'mixed')) {
            $grade = 'Intermediate';
            $detectedDefects = $standards['intermediate_defects'];
            $explanation = $standards['intermediate_explanation'];
        } else {
            // Default demo baseline based on crop
            // If organic or good variety, award Good grade
            $grade = 'Good';
            $detectedDefects = $standards['good_defects'];
            $explanation = $standards['good_explanation'];
        }

        $photoNotice = $photoCount > 0
            ? "Assessed across {$photoCount} lot sample photograph(s). Notice: Visual grading reflects photographed lot surfaces only and requires physical warehouse moisture and sorting verification."
            : 'Assessed using declared harvest agronomic parameters. Physical photos recommended before warehouse dispatch.';

        return [
            'grade' => $grade,
            'observed_defects' => $detectedDefects,
            'assessment_explanation' => $explanation.' '.$photoNotice,
            'confidence_score' => '91% (Demonstration Heuristic)',
            'manual_verification_required' => true,
            'grading_method' => $this->gradingMethod,
            'engine_version' => $this->engineVersion,
            'assessment_date' => now()->toDateTimeString(),
            'is_demonstration' => true,
        ];
    }

    /**
     * Get crop-specific grading standards and defect classification criteria.
     */
    public function getCropStandards(string $cropSlug): array
    {
        $standards = [
            'cotton' => [
                'name' => 'Cotton (કપાસ / कपास)',
                'good_defects' => ['Minimal trash (<2%)', 'Lustrous creamy-white staple', 'No pink bollworm staining'],
                'good_explanation' => 'Premium Grade A: High micronaire value, staple length >29mm, minimal seed coat fragments.',
                'intermediate_defects' => ['Leaf trash content (3-5%)', 'Slight dullness from late picking', 'Minor ginning fly'],
                'intermediate_explanation' => 'Standard Grade B: Commercial grade meeting spinning mill benchmark. Light trash content.',
                'bad_defects' => ['Severe bollworm staining', 'Moisture >12%', 'High trash (>6%)', 'Yellow discoloration'],
                'bad_explanation' => 'Substandard Grade C: Excessive vegetative matter and insect damage. Demands heavy cleaning discount.',
            ],
            'banana' => [
                'name' => 'Banana (કેળા / केला)',
                'good_defects' => ['Uniform finger calibration (7-8 inches)', 'Zero thrip scars', 'Intact peduncle crown'],
                'good_explanation' => 'Premium Grade A: Export-grade Grand Naine bunches with clean green peel and uniform finger curvature.',
                'intermediate_defects' => ['Minor skin freckle', 'Slight mechanical rubbing on outer fingers', 'Grade 2 length (6-7 inches)'],
                'intermediate_explanation' => 'Standard Grade B: Domestic retail table standard with minor surface blemishes not affecting pulp quality.',
                'bad_defects' => ['Premature yellowing in transit', 'Finger drop', 'Anthracnose crown rot', 'Deep abrasions'],
                'bad_explanation' => 'Substandard Grade C: Prematurely ripened bunches with crown fungal lesions. Unsuitable for long-distance transit.',
            ],
            'potato' => [
                'name' => 'Potato (બટાકા / आलू)',
                'good_defects' => ['Uniform 45-60mm tuber size', 'Firm smooth skin', 'Zero solanine greening', 'No common scab'],
                'good_explanation' => 'Premium Grade A: Deesa cold-storage standard. High dry matter (>20%), clean eyes, optimal for chipping or seed table.',
                'intermediate_defects' => ['Slight size variance (35-45mm)', 'Minor mechanical skinning', 'Superficial net scab <5%'],
                'intermediate_explanation' => 'Standard Grade B: General table potato suitable for local wholesale APMC trade.',
                'bad_defects' => ['Solanine greening (>10%)', 'Soft rot / Pythium leak', 'Hollow heart', 'Tuber moth tunnels'],
                'bad_explanation' => 'Substandard Grade C: Solar exposure greening and tuber moth infestation. Rejected for human table consumption.',
            ],
            'chilli' => [
                'name' => 'Dry Red Chilli (મરચા / मिर्च)',
                'good_defects' => ['Uniform deep crimson color', 'Moisture <10%', 'Zero stalk-less broken pods', 'High capsaicin retention'],
                'good_explanation' => 'Premium Grade A: Gondal APMC Resham Patti benchmark. Glossy calyx, intact pods, high ASTA color value.',
                'intermediate_defects' => ['Partial bleaching from sun-drying', 'Broken pods (4-7%)', 'Slight dust contamination'],
                'intermediate_explanation' => 'Standard Grade B: Commercial spice extraction and powder grinding grade with moderate color uniformity.',
                'bad_defects' => ['Severe pod fungus / Aspergillus', 'Moisture >14%', 'Extensive discolored white pods', 'Seed detachment'],
                'bad_explanation' => 'Substandard Grade C: Fungal deterioration from wet yard storage. Unsafe for direct human food processing.',
            ],
            'grain' => [
                'name' => 'Wheat & Grain (ઘઉં / गेहूं)',
                'good_defects' => ['Golden amber translucency', 'Moisture <11%', 'Zero Karnal bunt', 'Foreign matter <0.5%'],
                'good_explanation' => 'Premium Grade A: Bhalia wheat GI benchmark. High gluten strength, lustrous bold grains with 100% sound berries.',
                'intermediate_defects' => ['Chaff content (1-2%)', 'Shriveled grains (2-3%)', 'Minor luster loss from rain near harvest'],
                'intermediate_explanation' => 'Standard Grade B: Standard milling flour grade with acceptable test weight and minimal broken kernels.',
                'bad_defects' => ['Weevil-bored grains (>3%)', 'Black point / smuts', 'High moisture (>13%)', 'Musty odor'],
                'bad_explanation' => 'Substandard Grade C: Storage pest damage and moisture damage. Requires mandatory fumigation and animal feed re-routing.',
            ],
            'tomato' => [
                'name' => 'Tomato (ટામેટા / टमाटर)',
                'good_defects' => ['Firm breaker/turning stage', 'Zero radial cracks', 'Deep red skin pigmentation', 'Clean calyx'],
                'good_explanation' => 'Premium Grade A: Ideal firmness for interstate freight. Uniform spherical shape with high brix content.',
                'intermediate_defects' => ['Minor sunscald', 'Slight zipper scar', 'Mixed maturity stages in crate'],
                'intermediate_explanation' => 'Standard Grade B: Local city distribution grade for immediate 24-hour retail consumption.',
                'bad_defects' => ['Blossom-end rot', 'Fruit borer larval holes', 'Crushed/leaking fruit', 'Viral mottling'],
                'bad_explanation' => 'Substandard Grade C: Fruit wall collapse and rot lesions. Immediate discard candidate.',
            ],
        ];

        return $standards[$cropSlug] ?? [
            'name' => ucfirst($cropSlug),
            'good_defects' => ['Uniform morphology', 'Optimal harvest maturity', 'Zero fungal or insect scars'],
            'good_explanation' => 'Premium Grade A: High market presentation quality and optimal firmness.',
            'intermediate_defects' => ['Minor superficial marks', 'Slight size variation', 'Commercial acceptable standard'],
            'intermediate_explanation' => 'Standard Grade B: Satisfactory agricultural quality suitable for local mandi trading.',
            'bad_defects' => ['Insect infestation', 'High foreign matter', 'Rot / bacterial lesions'],
            'bad_explanation' => 'Substandard Grade C: Substantial degradation detected requiring significant sorting or rejection.',
        ];
    }
}
