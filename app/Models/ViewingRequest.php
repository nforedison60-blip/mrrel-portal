<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'customer_name',
        'status',
        'message',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}