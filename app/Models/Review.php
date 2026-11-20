<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'provider_user_id',
        'user_id',
        'customer_name',
        'rating',
        'comment',
    ];
}