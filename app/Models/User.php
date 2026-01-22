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
    | Property Hooks (Modern PHP 8.4)
    |--------------------------------------------------------------------------
    */

    /**
     * Access the current movement via the relationship.
     */
    public ?Movement $current_movement {
        get => $this->currentMovementRel;
    }

    /**
     * Get URL for profile photo with fallback.
     */
    public string $profile_photo_url {
        get => $this->profile_photo_path 
            ? asset('storage/' . $this->profile_photo_path) 
            : asset('img/default-avatar.png');
    }

    /**
     * Boolean role checks using hooks.
     */
    public bool $is_admin { get => $this->role === UserRole::ADMIN; }
    public bool $is_employee { get => $this->role === UserRole::EMPLOYEE; }

    /**
     * Standard method for the Starter Kit's components.
     * This fixes the BadMethodCallException.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($segment) => (string) Str::upper(Str::substr($segment, 0, 1)))
            ->implode('');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes (Fixes the BadMethodCallException)
    |--------------------------------------------------------------------------
    */

    /**
     * Scope for PresenceBoard: User::employee()
     */
    public function scopeEmployee(Builder $query): void
    {
        $query->where('role', UserRole::EMPLOYEE);
    }

    /**
     * Scope: User::admin()
     */
    public function scopeAdmin(Builder $query): void
    {
        $query->where('role', UserRole::ADMIN);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /**
     * Fetches the movement that is currently active.
     */
    public function currentMovementRel(): HasOne
    {
        return $this->hasOne(Movement::class)
            ->where('started_at', '<=', now())
            ->where(fn($query) => 
                $query->whereNull('ended_at')
                      ->orWhere('ended_at', '>', now())
            )
            ->latestOfMany('started_at');
    }
}
