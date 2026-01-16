<?php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

/**
 * Movement Model
 * * Manages employee movement records (Meeting, Travel, etc.)
 * Strictly typed for PHP 8.2+ and Laravel 12.
 */
class Movement extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'type',
        'remark',
    ];

    /**
     * Automatic Type Casting
     */
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
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Determine if a movement record is currently active based on system time.
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('started_at', '<=', $now)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('ended_at')
                  ->orWhere('ended_at', '>', $now);
            });
    }

    /**
     * Alias for scopeActive.
     */
    public function scopeActiveNow(Builder $query): Builder
    {
        return $this->active();
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
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if movement is currently active (Boolean).
     * Usage: $movement->is_active
     */
    protected function isActive(): Attribute
    {
        return Attribute::get(fn () => 
            $this->started_at <= now() && 
            ($this->ended_at === null || $this->ended_at > now())
        );
    }

    public function isIndefinite(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Returns a human-readable duration (e.g., "2 hours", "1 day").
     */
    public function duration(): string
    {
        if ($this->isIndefinite()) {
            return 'Indefinite';
        }

        return $this->started_at->diffForHumans($this->ended_at, [
            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
            'parts' => 1
        ]);
    }
}
