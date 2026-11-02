<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduceGradingAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'produce_lot_id',
        'grade',
        'observed_defects',
        'assessment_explanation',
        'confidence_score',
        'manual_verification_required',
        'grading_method',
        'engine_version',
        'assessed_by',
        'is_admin_override',
        'override_notes',
        'is_demonstration',
    ];

    protected function casts(): array
    {
        return [
            'observed_defects' => 'array',
            'manual_verification_required' => 'boolean',
            'is_admin_override' => 'boolean',
            'is_demonstration' => 'boolean',
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(ProduceLot::class, 'produce_lot_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    /**
     * Human-readable label for quality classification.
     */
    public function getGradeTitle(): string
    {
        return match ($this->grade) {
            'Good' => 'Good (Premium Grade A)',
            'Intermediate' => 'Intermediate (Fair Grade B)',
            'Bad' => 'Bad (Substandard Grade C)',
            default => $this->grade,
        };
    }
}
