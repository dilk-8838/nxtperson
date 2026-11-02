<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduceReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_number',
        'produce_lot_id',
        'buyer_id',
        'quantity_kg',
        'price_per_kg',
        'total_price',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_kg' => 'decimal:2',
            'price_per_kg' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(ProduceLot::class, 'produce_lot_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'requested' => 'bg-amber-100 text-amber-800 border-amber-300',
            'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'fulfilled' => 'bg-blue-100 text-blue-800 border-blue-300',
            'cancelled' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-stone-100 text-stone-800 border-stone-300',
        };
    }
}
