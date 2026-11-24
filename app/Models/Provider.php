<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'user_id',
        'base_label',
        'base_lat',
        'base_lng',
        'service_radius_km',
        'company_name',
        'vehicle',
        'plate_number',
        'verification_status',
        'rating',
        'image_path',
        'vehicle_image_path',
        'id_image_path',
        'id_document',
        'vehicle_document',
        'bank_name',
        'bank_account_holder',
        'bank_account_number',
        'bank_account_type',
        'bank_branch_code',
    ];

    // The account number is encrypted in the database
    protected $casts = [
        'bank_account_number' => 'encrypted',
    ];

    public function hasBankDetails(): bool
    {
        return $this->bank_name && $this->bank_account_holder && $this->bank_account_number;
    }
}