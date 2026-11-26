<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'accepted_by',
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
        'image_path',
        'photo_paths',
    ];

    protected $casts = ['photo_paths' => 'array'];

    // Every photo path: the cover first, then the others
    public function allPhotoPaths(): array
    {
        return array_values(array_filter(array_merge([$this->image_path], $this->photo_paths ?? [])));
    }

    public function photoUrls(): array
    {
        $urls = [];
        foreach ($this->allPhotoPaths() as $path) {
            try { $urls[] = \Illuminate\Support\Facades\Storage::disk('s3')->url($path); } catch (\Throwable $e) {}
        }
        return $urls;
    }

    public function viewingRequests()
    {
        return $this->hasMany(ViewingRequest::class);
    }
}