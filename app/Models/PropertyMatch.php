<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMatch extends Model
{
    protected $fillable = ['rental_need_id', 'property_id', 'user_id', 'matched_by', 'status'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function need()
    {
        return $this->belongsTo(RentalNeed::class, 'rental_need_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
