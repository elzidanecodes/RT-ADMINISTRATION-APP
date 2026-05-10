<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    public const SECURITY_AMOUNT = 100000;
    public const CLEANING_AMOUNT = 15000;

    protected $fillable = [
        'house_id',
        'resident_id',
        'bill_type',
        'amount',
        'period_year',
        'period_month',
        'due_date',
        'status',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'period_year'  => 'integer',
        'period_month' => 'integer',
        'due_date'     => 'date',
        'status'       => 'string',
        'bill_type'    => 'string',
    ];

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeByPeriod($query, int $year, ?int $month = null)
    {
        return $query
            ->where('period_year', $year)
            ->when($month, fn ($q) => $q->where('period_month', $month));
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount_paid');
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->amount - $this->total_paid);
    }
}
