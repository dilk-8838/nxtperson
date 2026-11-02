<?php

namespace App\Services\Advisory;

interface CropAdvisoryEngineInterface
{
    /**
     * Diagnose crop issue from symptom, crop slug, and optional photo.
     */
    public function diagnose(string $cropSlug, string $symptomKey, ?string $photoPath = null): array;

    /**
     * Get knowledge base rules for target crops.
     */
    public function getRules(): array;
}
