<?php
// app\Models\Movement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    // Movement types
    public const TYPE_MEETING  = 'meeting';
    public const TYPE_COURSE   = 'course';
    public const TYPE_TRAINING = 'training';
    public const TYPE_TRAVEL   = 'travel';
    public const TYPE_LEAVE    = 'leave';
    public const TYPE_OTHER    = 'other';

    public const TYPES = [
        self::TYPE_MEETING  => 'Meeting',
        self::TYPE_COURSE   => 'Course',
        self::TYPE_TRAINING => 'Training',
        self::TYPE_TRAVEL   => 'Travel',
        self::TYPE_LEAVE    => 'Leave',
        self::TYPE_OTHER    => 'Other',
    ];

    // Movement status
    public const STATUS_PLANNED   = 'planned';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'movement_type',
        'status',
        'remark',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    /**
     * Relationship: Movement belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Movements that are active now (planned or active and ongoing).
     */
    public function scopeActiveNow($query): void
    {
        $now = now();
        $query->whereIn('status', [self::STATUS_PLANNED, self::STATUS_ACTIVE])
              ->where('started_at', '<=', $now)
              ->where(fn($q) => $q->whereNull('ended_at')->orWhere('ended_at', '>=', $now));
    }

    /**
     * Scope: Filter by movement type.
     */
    public function scopeOfType($query, string $type): void
    {
        $query->where('movement_type', $type);
    }

    /**
     * Scope: Filter by movement status.
     */
    public function scopeWhereStatus($query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope: Movements overlapping a given time range.
     */
    public function scopeOverlaps($query, $start, $end): void
    {
        $query->where(function ($q) use ($start, $end) {
            $q->where('started_at', '<', $end)
              ->where(fn($q2) => $q2->whereNull('ended_at')->orWhere('ended_at', '>', $start));
        });
    }

    /**
     * Scope: Upcoming movements (today onwards, planned or active).
     */
    public function scopeUpcoming($query): void
    {
        $query->where('ended_at', '>=', now()->startOfDay())
              ->whereIn('status', [self::STATUS_PLANNED, self::STATUS_ACTIVE]);
    }
}
