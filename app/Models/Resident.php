<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    protected $fillable = [
        'full_name',
        'phone_number',
        'ktp_photo_path',
        'resident_type',
        'is_married',
    ];

    protected $casts = [
        'is_married'    => 'boolean',
        'resident_type' => 'string',
    ];

    public function houses(): BelongsToMany
    {
        return $this->belongsToMany(House::class, 'house_resident')
            ->withPivot(['start_date', 'end_date', 'is_active', 'notes'])
            ->withTimestamps()
            ->orderByPivot('start_date', 'desc');
    }

    public function currentHouse(): BelongsToMany
    {
        return $this->belongsToMany(House::class, 'house_resident')
            ->withPivot(['start_date', 'end_date', 'is_active', 'notes'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
