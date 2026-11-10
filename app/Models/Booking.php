<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
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
}