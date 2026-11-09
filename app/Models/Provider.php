<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'vehicle',
        'plate_number',
        'verification_status',
        'rating',
        'image_path',
    ];
}