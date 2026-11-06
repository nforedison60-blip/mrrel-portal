<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'property_type',
        'city',
        'suburb',
        'bedrooms',
        'bathrooms',
        'rent',
        'deposit',
        'status',
    ];

    public function viewingRequests()
    {
        return $this->hasMany(ViewingRequest::class);
    }
}