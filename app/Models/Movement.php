<?php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\CarbonInterface;

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * NOTE: We removed 'public private(set) int $user_id' 
     * because it conflicts with Eloquent's dynamic attribute system.
     */

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
    | Property Hooks (Calculated Attributes)
    |--------------------------------------------------------------------------
    */

    /**
     * Virtual Hook: Provides the read-only 'user_id' behavior 
     * without breaking Eloquent's initialization.
     */
    public int $assigned_user_id {
        get => $this->user_id;
    }

    public bool $is_active {
        get => $this->started_at <= now() && 
               ($this->ended_at === null || $this->ended_at > now());
    }

    public bool $is_indefinite {
        get => $this->ended_at === null;
    }

    /**
     * PHP 8.4: Improved multi-line hook for complex logic
     */
    public string $duration_label {
        get {
            if ($this->is_indefinite) return 'In Progress...';

            return $this->started_at->diffForHumans($this->ended_at, [
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                'parts' => 1,
                'short' => true, 
            ]);
        }
    }

    /**
     * Total minutes for analytics/reporting
     */
    public int $total_minutes {
        get => (int) $this->started_at->diffInMinutes($this->ended_at ?? now());
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): void 
    {
        $query->where('started_at', '<=', now())
              ->where(fn(Builder $q) => $q->whereNull('ended_at')->orWhere('ended_at', '>', now()));
    }

    public function scopeOfType(Builder $query, MovementType $type): void
    {
        $query->where('type', $type);
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
    | Model Events
    |--------------------------------------------------------------------------
    */

    /**
     * Clear the public board cache whenever a movement is saved or deleted.
     */
    protected static function booted(): void
    {
        // Fires on create, update, and save
        static::saved(fn () => self::clearBoardCache());
        
        // Fires when a movement is deleted
        static::deleted(fn () => self::clearBoardCache());
    }

    private static function clearBoardCache(): void
    {
        // This clears the specific count and page caches used in PresenceBoard
        \Illuminate\Support\Facades\Cache::forget('pb_total_count');
        \Illuminate\Support\Facades\Cache::forget('pb_away_count');
        
        // Since we don't know which page the user is on, we flush all cache 
        // tags if using a tag-supported driver (like Redis), 
        // or just flush everything for simplicity on small-scale boards.
        \Illuminate\Support\Facades\Cache::flush();
    }
}
