<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'user_id',
        'customer_name',
        'total_price',
        'status',
        'tracking_status',
        'extras_status',
        'job_confirmed',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    // Only bookings whose quote was sent by this provider login
    public function scopeForProvider($query, $userId)
    {
        return $query->whereHas('quote', function ($q) use ($userId) {
            $q->where('provider_user_id', $userId);
        });
    }
}
