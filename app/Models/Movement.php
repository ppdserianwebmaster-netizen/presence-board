<?php
// app\Models\Movement.php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\CarbonInterface;

/**
 * Movement Model - Tracking employee presence.
 * Refactored for Laravel 12 & PHP 8.4
 */
class Movement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'started_at', 'ended_at', 'type', 'remark',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
            'type'       => MovementType::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PHP 8.4 Property Hooks
    |--------------------------------------------------------------------------
    */

    /**
     * Replaces isActive() Attribute.
     * Usage: if ($movement->is_active) ...
     */
    public bool $is_active {
        get => $this->started_at <= now() && 
               ($this->ended_at === null || $this->ended_at > now());
    }

    /**
     * Determines if the movement has no set end time.
     */
    public bool $is_indefinite {
        get => $this->ended_at === null;
    }

    /**
     * Returns a human-readable duration (e.g., "2 hours").
     * Replaces the duration() helper method.
     */
    public string $duration_label {
        get {
            if ($this->is_indefinite) return 'Indefinite';

            return $this->started_at->diffForHumans($this->ended_at, [
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                'parts' => 1
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes (Standard Method Syntax)
    |--------------------------------------------------------------------------
    | Note: Methods must use { } even in PHP 8.4.
    | Only Property Hooks use the => syntax.
    */

    public function scopeActive(Builder $query): Builder 
    {
        return $query->where('started_at', '<=', now())
            ->where(fn(Builder $q) => 
                $q->whereNull('ended_at')->orWhere('ended_at', '>', now())
            );
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('ended_at')
                    ->where('ended_at', '<', now());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('started_at', '>', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
