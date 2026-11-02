<?php

namespace App\Services\Advisory;

class SampleAiCropDiagnosticEngine implements CropAdvisoryEngineInterface
{
    /**
     * Diagnostic rule matrix for Gujarat key crops.
     */
    protected array $rules = [
        'cotton-bollworm' => [
            'crop' => 'Cotton',
            'title' => 'Pink Bollworm (Pectinophora gossypiella)',
            'description' => 'Larval penetration noted in squares and green bolls with characteristic rosette flowers.',
            'solution' => 'Install delta pheromone traps @ 8 traps/acre; apply Azadirachtin 10,000 ppm (1.5 ml/L). Consult extension officer for field-specific dosage.',
            'confidence' => 'Demonstration Symptom Match',
            'escalation' => 'Triage Level 1 • Demonstration Routine Monitoring',
        ],
        'cotton-curl' => [
            'crop' => 'Cotton',
            'title' => 'Cotton Leaf Curl Virus (CLCuD)',
            'description' => 'Upward vein-clearing, leaf cupping, and enations transmitted by Bemisia tabaci whiteflies.',
            'solution' => 'Deploy yellow sticky traps @ 12/acre; spray systemic bio-formulation (Beauveria bassiana).',
            'confidence' => 'Demonstration Symptom Match',
            'escalation' => 'Triage Level 2 • Escalated to Demo Agronomic Review',
        ],
        'banana-blight' => [
            'crop' => 'Banana',
            'title' => 'Black Sigatoka (Pseudocercospora fijiensis)',
            'description' => 'Spindle lesions with grey centers and yellow halos coalescing across lower fronds.',
            'solution' => 'Prune heavily infected lower leaves; apply mineral oil-based foliar bio-fungicide.',
            'confidence' => 'Demonstration Symptom Match',
            'escalation' => 'Triage Level 2 • Escalated to Demo Agronomist',
        ],
        'potato-blight' => [
            'crop' => 'Potato',
            'title' => 'Late Blight (Phytophthora infestans)',
            'description' => 'Dark water-soaked necrotic patches on leaves with white sporulating mycelium on undersides during high RH.',
            'solution' => 'Prophylactic application of bio-copper formulation or Mancozeb before night dew.',
            'confidence' => 'Demonstration Symptom Match',
            'escalation' => 'Priority Alert • Demo Specialist Queue',
        ],
        'tomato-curl' => [
            'crop' => 'Tomato',
            'title' => 'Tomato Leaf Curl Virus (ToLCV)',
            'description' => 'Stunted internodes, reduced leaflet size, pale marginal chlorosis, vector-borne.',
            'solution' => 'Use silver reflective mulch; apply bio-insecticide to suppress whitefly vector population.',
            'confidence' => 'Demonstration Symptom Match',
            'escalation' => 'Triage Level 2 • Demo Agronomist Assigned',
        ],
        'chilli-curl' => [
            'crop' => 'Chilli',
            'title' => 'Chilli Murda Complex (Thrips & Mites)',
            'description' => 'Boat-shaped downward curling of foliage with brittle leaves and shortened terminal nodes.',
            'solution' => 'Spray Neem seed kernel extract (NSKE 5%) or Spinosad; avoid excess nitrogen.',
            'confidence' => 'Demonstration Symptom Match',
            'escalation' => 'Triage Level 1 • Demonstration Routine Monitoring',
        ],
        'rice-blight' => [
            'crop' => 'Rice',
            'title' => 'Bacterial Leaf Blight (Xanthomonas oryzae)',
            'description' => 'Wavy chlorotic stripes extending along margins from leaf tip to base, turning straw-colored.',
            'solution' => 'Drain excess standing water temporarily; apply Pseudomonas fluorescens @ 5g/L.',
            'confidence' => 'Demonstration Symptom Match',
            'escalation' => 'Triage Level 1 • Demo Advisory Active',
        ],
        'grain-rust' => [
            'crop' => 'Wheat/Grain',
            'title' => 'Yellow / Stripe Rust (Puccinia striiformis)',
            'description' => 'Parallel rows of yellowish-orange uredinial pustules on leaf blades and sheaths.',
            'solution' => 'Apply bio-fungicide or Propiconazole @ 1 ml/L; inspect surrounding fields.',
            'confidence' => 'Demonstration Symptom Match',
            'escalation' => 'Priority Alert • Demo Field Specialist Queue',
        ],
    ];

    /**
     * Diagnose crop issue from symptom and crop slug.
     */
    public function diagnose(string $cropSlug, string $symptomKey, ?string $photoPath = null): array
    {
        $key = strtolower("{$cropSlug}-{$symptomKey}");

        $result = $this->rules[$key] ?? [
            'crop' => ucfirst($cropSlug),
            'title' => ucfirst($cropSlug).' General Agronomic Stress',
            'description' => "Visual symptoms matching '{$symptomKey}' analyzed against sample demonstration knowledge base.",
            'solution' => 'Foliar micronutrient spray, organic soil conditioning, and moisture check.',
            'confidence' => 'Demonstration Rule Match',
            'escalation' => 'Triage Level 1 • Demonstration Routine Monitoring',
        ];

        return array_merge($result, [
            'is_demonstration' => true,
            'model_name' => 'NxtAgri-Vision-v2.4 (Simulated Demonstration)',
            'disclaimer' => 'Demonstration Output: Simulated advisory for evaluation only. Ground inspection required before chemical use.',
            'photo_provided' => $photoPath !== null,
        ]);
    }

    /**
     * Get knowledge base rules.
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
