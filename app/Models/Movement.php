<?php

namespace App\Models;

use App\Enums\MovementType;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * PHP 8.4 Tip: Asymmetric Visibility could be used here for 
     * properties not managed by Eloquent's dynamic magic.
     */
    protected $fillable = [
        'user_id', 
        'started_at', 
        'ended_at', 
        'type', 
        'remark', 
        'logged_at',
    ];

    /**
     * Unified Casting (Laravel 11+ Style)
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
            'logged_at'  => 'datetime',
            'type'       => MovementType::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PHP 8.4 Property Hooks
    |--------------------------------------------------------------------------
    */

    /**
     * Read-only proxy for user_id to prevent accidental overwrites 
     * while maintaining Eloquent compatibility.
     */
    public int $assigned_user_id {
        get => $this->user_id;
    }

    public bool $is_active {
        get => $this->started_at?->isPast() && 
               ($this->ended_at === null || $this->ended_at->isFuture());
    }

    public bool $is_indefinite {
        get => $this->ended_at === null;
    }

    /**
     * Formatting logic utilizing PHP 8.4 multi-line get hooks.
     */
    public string $duration_label {
        get {
            if ($this->is_indefinite) {
                return 'In Progress...';
            }

            return $this->started_at->diffForHumans($this->ended_at, [
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                'parts' => 1,
                'short' => true, 
            ]);
        }
    }

    public int $total_minutes {
        get => (int) $this->started_at->diffInMinutes($this->ended_at ?? now());
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes & Relationships
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): void 
    {
        $query->where('started_at', '<=', now())
              ->where(fn(Builder $q) => $q->whereNull('ended_at')->orWhere('ended_at', '>', now()));
    }

    public function user(): BelongsTo
    {
        // Adding withTrashed() here ensures the relationship 
        // always loads the user even if they are soft-deleted.
        return $this->belongsTo(User::class)->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events & Cache Management
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        // Using static listeners for better performance
        static::saved(fn () => self::clearBoardCache());
        static::deleted(fn () => self::clearBoardCache());
    }

    /**
     * Refined Cache Clearing.
     * Instead of Cache::flush() (which kills sessions and other data), 
     * we target specific keys.
     */
    private static function clearBoardCache(): void
    {
        Cache::forget('pb_total_count');
        Cache::forget('pb_away_count');
        
        // Pro-tip: If using Livewire pagination, consider using a 'presence_board' 
        // cache tag if your driver supports it (Redis/Memcached).
    }
}
