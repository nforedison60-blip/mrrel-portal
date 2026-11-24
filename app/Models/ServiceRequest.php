<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'services_needed',
        'moving_from',
        'moving_to',
        'move_date',
        'date_flexibility',
        'item_size',
        'items_count',
        'budget',
        'access_pickup',
        'access_delivery',
        'details',
        'status',
        'pickup_lat',
        'pickup_lng',
    ];

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}