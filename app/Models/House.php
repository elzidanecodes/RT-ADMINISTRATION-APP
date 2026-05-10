<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    protected $fillable = [
        'house_number',
        'block',
        'address',
        'ownership_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'ownership_type' => 'string',
        'status'         => 'string',
    ];

    // All residents ever assigned (full history)
    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'house_resident')
            ->withPivot(['start_date', 'end_date', 'is_active', 'notes'])
            ->withTimestamps()
            ->orderByPivot('start_date', 'desc');
    }

    // Only currently active resident
    public function activeResidents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'house_resident')
            ->withPivot(['start_date', 'end_date', 'is_active', 'notes'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    // Scope untuk filter status
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeVacant($query)
    {
        return $query->where('status', 'vacant');
    }
}
