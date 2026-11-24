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
        'photo_paths',
    ];

    protected $casts = [
        'photo_paths' => 'array',
    ];

    // Short-lived links to the photos the customer attached (empty list when none)
    public function photoUrls(): array
    {
        $urls = [];
        foreach ((array) $this->photo_paths as $path) {
            try {
                $urls[] = \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(30));
            } catch (\Throwable $e) {
                try {
                    $urls[] = \Illuminate\Support\Facades\Storage::disk('s3')->url($path);
                } catch (\Throwable $e2) {
                    // photo link not available: skip it
                }
            }
        }
        return $urls;
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}