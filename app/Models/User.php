<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * User Model
 * * Represents an employee or administrator in the system.
 * Optimized for Laravel 12 & PHP 8.2+
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'department',
        'position',
        'role',
        'profile_photo_path',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Modern Laravel 12 Casting
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
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all movements for the user.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /**
     * Relationship for the current active movement.
     * Used by the PresenceBoard for performance (Eager Loading).
     */
    public function currentMovementRel(): HasOne
    {
        return $this->hasOne(Movement::class)
            ->where('started_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ended_at')
                      ->orWhere('ended_at', '>', now());
            })
            ->latestOfMany('started_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (Derived Attributes)
    |--------------------------------------------------------------------------
    */

    /**
     * Accessor to get the active movement object directly.
     * Usage: $user->current_movement
     */
    protected function currentMovement(): Attribute
    {
        return Attribute::get(fn () => $this->currentMovementRel);
    }

    /**
     * Role Checks
     */
    protected function isAdmin(): Attribute
    {
        return Attribute::get(fn () => $this->role === UserRole::ADMIN);
    }

    protected function isEmployee(): Attribute
    {
        return Attribute::get(fn () => $this->role === UserRole::EMPLOYEE);
    }

    /**
     * Fix: Points to local default-avatar.png instead of external API.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn () =>
            $this->profile_photo_path
                ? Storage::url($this->profile_photo_path)
                : asset('img/default-avatar.png') 
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAdmin($query): void
    {
        $query->where('role', UserRole::ADMIN);
    }

    public function scopeEmployee($query): void
    {
        $query->where('role', UserRole::EMPLOYEE);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Generate initials from name (e.g., "John Doe" -> "JD").
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }
}
