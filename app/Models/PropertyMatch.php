<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMatch extends Model
{
    protected $fillable = ['rental_need_id', 'property_id', 'user_id', 'matched_by', 'status'];

    protected $casts = [
        'landlord_decided_at' => 'datetime',
        'intro_paid_at' => 'datetime',
        'intro_amount' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function need()
    {
        return $this->belongsTo(RentalNeed::class, 'rental_need_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The reference sent to Ozow, for example LIN-0007
    public function introReference(): string
    {
        return 'LIN-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function introPaid(): bool
    {
        return $this->intro_status === 'Paid';
    }
}
