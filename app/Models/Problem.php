<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'customer_name',
        'subject',
        'description',
        'status',
        'image_path',
    ];
}