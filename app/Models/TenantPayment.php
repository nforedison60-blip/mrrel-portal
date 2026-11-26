<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantPayment extends Model
{
    public const MATCHING_FEE = 'matching_fee';
    public const ATTENDANCE = 'viewing_attendance';

    protected $fillable = [
        'user_id', 'type', 'amount', 'status', 'rental_need_id', 'viewing_request_id',
        'ozow_transaction_id', 'paid_at', 'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The reference sent to Ozow, for example TPY-0007
    public function reference(): string
    {
        return 'TPY-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function label(): string
    {
        return $this->type === self::MATCHING_FEE ? 'Matching fee' : 'Viewing attendance';
    }
}
