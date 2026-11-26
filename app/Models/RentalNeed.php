<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalNeed extends Model
{
    protected $fillable = ['user_id', 'areas', 'budget_min', 'budget_max', 'bedrooms', 'move_in_date', 'notes', 'status'];

    protected $casts = ['move_in_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matches()
    {
        return $this->hasMany(PropertyMatch::class);
    }

    // "Studio" counts as 0 bedrooms and "5+" as 5
    public function minBedrooms(): int
    {
        return (int) $this->bedrooms;
    }
}
