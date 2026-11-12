<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'services_needed', 'moving_from', 'moving_to', 'move_date',
        'date_flexibility', 'item_size', 'access_pickup', 'access_delivery', 'status',
    ];

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}