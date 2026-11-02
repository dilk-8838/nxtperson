<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduceLotImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'produce_lot_id',
        'image_path',
        'original_name',
        'file_size',
        'mime_type',
        'is_primary',
        'is_approved_for_buyers',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_approved_for_buyers' => 'boolean',
            'file_size' => 'integer',
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(ProduceLot::class, 'produce_lot_id');
    }

    /**
     * Get accessible URL path for image.
     */
    public function getUrlAttribute(): string
    {
        return asset($this->image_path);
    }
}
