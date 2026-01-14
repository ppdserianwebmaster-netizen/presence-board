<?php
// app/Models/Movement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

/**
 * Movement Model (Laravel 12)
 *
 * Represents an employee's movement or absence from the office.
 *
 * DESIGN PRINCIPLES
 * ------------------------------------------------------------------
 * • No explicit status column
 * • "Active" state is derived from datetime comparison
 * • Business rules live in query scopes and helpers
 * • Optimized for real-time presence board queries
 *
 * ACTIVE MOVEMENT RULE
 * ------------------------------------------------------------------
 * started_at <= now() AND (ended_at IS NULL OR ended_at > now())
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property string $movement_type
 * @property string|null $remark
 *
 * @property-read bool $is_active
 * @property-read \App\Models\User $user
 */
class Movement extends Model
{
    use HasFactory, SoftDeletes;

    /* -----------------------------------------------------------------
     | Movement Type Constants
     |------------------------------------------------------------------*/

    public const TYPE_MEETING  = 'meeting';
    public const TYPE_COURSE   = 'course';
    public const TYPE_TRAINING = 'training';
    public const TYPE_TRAVEL   = 'travel';
    public const TYPE_LEAVE    = 'leave';
    public const TYPE_OTHER    = 'other';

    /**
     * Human-readable labels for movement types
     */
    public const TYPES = [
        self::TYPE_MEETING  => 'Meeting',
        self::TYPE_COURSE   => 'Course',
        self::TYPE_TRAINING => 'Training',
        self::TYPE_TRAVEL   => 'Travel',
        self::TYPE_LEAVE    => 'Leave',
        self::TYPE_OTHER    => 'Other',
    ];

    /* -----------------------------------------------------------------
     | Mass Assignment
     |------------------------------------------------------------------*/

    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'movement_type',
        'remark',
    ];

    /* -----------------------------------------------------------------
     | Attribute Casting (Laravel 12)
     |------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
        ];
    }

    /* -----------------------------------------------------------------
     | Relationships
     |------------------------------------------------------------------*/

    /**
     * A movement belongs to a user (employee).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* -----------------------------------------------------------------
     | Query Scopes (Core Business Logic)
     |------------------------------------------------------------------*/

    /**
     * Scope: Movements that are active right now.
     *
     * Centralized rule used by:
     * • User::currentMovement
     * • Presence board
     * • Active movement counts
     */
    public function scopeActive(Builder $query): void
    {
        $now = now();

        $query->where('started_at', '<=', $now)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('ended_at')
                  ->orWhere('ended_at', '>', $now);
            });
    }

    /**
     * Scope: Movements that overlap a given datetime range.
     */
    public function scopeOverlapping(Builder $query, $start, $end): void
    {
        $query->where('started_at', '<', $end)
            ->where(function (Builder $q) use ($start) {
                $q->whereNull('ended_at')
                  ->orWhere('ended_at', '>', $start);
            });
    }

    /**
     * Scope: Movements that have fully completed.
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->whereNotNull('ended_at')
              ->where('ended_at', '<', now());
    }

    /**
     * Scope: Movements by type.
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('movement_type', $type);
    }

    /* -----------------------------------------------------------------
     | Computed Attributes
     |------------------------------------------------------------------*/

    /**
     * Attribute: Determine if this movement is active.
     */
    protected function isActive(): Attribute
    {
        return Attribute::get(fn () => $this->started_at <= now()
            && ($this->ended_at === null || $this->ended_at > now()));
    }

    /* -----------------------------------------------------------------
     | Helper Methods
     |------------------------------------------------------------------*/

    /**
     * Check if the movement has ended.
     */
    public function hasEnded(): bool
    {
        return $this->ended_at !== null && $this->ended_at->isPast();
    }

    /**
     * Check if the movement is indefinite (no end date).
     */
    public function isIndefinite(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Get a human-readable duration string.
     */
    public function duration(): string
    {
        return $this->ended_at
            ? $this->started_at->diffForHumans($this->ended_at, ['parts' => 2])
            : 'Indefinite';
    }

    /**
     * Get a human-readable movement type label.
     */
    public function typeLabel(): string
    {
        return self::TYPES[$this->movement_type]
            ?? ucfirst(str_replace('_', ' ', $this->movement_type));
    }
}
