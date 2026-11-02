<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advisory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'crop_slug',
        'farmer_name',
        'farmer_phone',
        'farmer_district',
        'issue_description',
        'image_path',
        'symptom_key',
        'ai_diagnosis_title',
        'ai_diagnosis_description',
        'ai_recommended_solution',
        'ai_confidence',
        'status',
        'assigned_agronomist',
        'agronomist_notes',
        'is_demonstration',
    ];

    protected function casts(): array
    {
        return [
            'is_demonstration' => 'boolean',
        ];
    }

    public function crop()
    {
        return $this->belongsTo(Crop::class, 'crop_slug', 'slug');
    }
}
