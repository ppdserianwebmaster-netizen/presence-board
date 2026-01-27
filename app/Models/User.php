<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'employee_id',
        'department', 'position', 'role', 'profile_photo_path',
    ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Unified Casting (Laravel 12 Style)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class, 
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PHP 8.4 Property Hooks & Asymmetric Visibility
    |--------------------------------------------------------------------------
    */

    /**
     * Profile Photo Hook. 
     * Using multi-line 'get' for readability.
     */
    public string $profile_photo_url {
        get {
            return $this->profile_photo_path 
                ? asset('storage/' . $this->profile_photo_path) 
                : asset('img/default-avatar.png');
        }
    }

    /**
     * Role Boolean Hooks.
     * Clean and lightning-fast for Blade: @if($user->is_admin)
     */
    public bool $is_admin { get => $this->role === UserRole::ADMIN; }
    public bool $is_employee { get => $this->role === UserRole::EMPLOYEE; }

    /**
     * Dynamic current movement access.
     * Note: PHP 8.4 allows us to call this like a property.
     */
    public ?Movement $current_movement {
        get => $this->currentMovementRel;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Generate initials for avatar fallbacks.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($segment) => Str::upper(Str::substr($segment, 0, 1)))
            ->implode('');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships & Scopes
    |--------------------------------------------------------------------------
    */

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /**
     * Uses 'latestOfMany' for high-performance current status lookups.
     * Optimized for the composite index we added in the migration.
     */
    public function currentMovementRel(): HasOne
    {
        return $this->hasOne(Movement::class)
            ->where('started_at', '<=', now())
            ->where(fn(Builder $query) => 
                $query->whereNull('ended_at')
                      ->orWhere('ended_at', '>', now())
            )
            ->latestOfMany('started_at');
    }

    /**
     * Filters for PresenceBoard.
     */
    public function scopeEmployee(Builder $query): void
    {
        $query->where('role', UserRole::EMPLOYEE);
    }

    public function scopeAdmin(Builder $query): void
    {
        $query->where('role', UserRole::ADMIN);
    }
}
