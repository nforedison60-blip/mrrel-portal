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
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}