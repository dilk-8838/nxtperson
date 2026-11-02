<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqInvitation extends Model
{
    use HasFactory;

    public const STATUS_INVITED = 'invited';

    public const STATUS_VIEWED = 'viewed';

    public const STATUS_QUOTED = 'quoted';

    public const STATUS_DECLINED = 'declined';

    protected $fillable = [
        'rfq_id',
        'recipient_type',
        'recipient_user_id',
        'supplier_organization_id',
        'fpo_organization_id',
        'status',
    ];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class, 'rfq_id');
    }

    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function supplierOrganization(): BelongsTo
    {
        return $this->belongsTo(SupplierOrganization::class, 'supplier_organization_id');
    }

    public function fpoOrganization(): BelongsTo
    {
        return $this->belongsTo(FpoOrganization::class, 'fpo_organization_id');
    }
}
