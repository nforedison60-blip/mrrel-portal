<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_name',
        'total_price',
        'price_type',
        'included',
        'not_included',
        'validity',
        'note',
        'service_request_id',
    ];
}