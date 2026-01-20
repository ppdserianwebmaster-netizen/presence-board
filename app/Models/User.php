<?php
// app\Models\User.php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * User Model - Employee Management System
 * Refactored for Laravel 12 & PHP 8.4
 */
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
            'role' => UserRole::class, // Enum Casting
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PHP 8.4 Property Hooks (Replacing Attribute::make)
    |--------------------------------------------------------------------------
    */

    /**
     * Accessor for the active movement object.
     * Usage: $user->current_movement
     */
    public ?Movement $current_movement {
        get => $this->currentMovementRel;
    }

    /**
     * PHP 8.4 Property Hook for Profile Photo
     */
    public string $profile_photo_url {
        get => $this->profile_photo_path 
            ? asset('storage/' . $this->profile_photo_path) 
            : asset('img/default-avatar.png'); // Ensure this matches your folder name!
    }

    /**
     * Role Boolean Checks
     */
    public bool $is_admin {
        get => $this->role === UserRole::ADMIN;
    }

    public bool $is_employee {
        get => $this->role === UserRole::EMPLOYEE;
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

    public function currentMovementRel(): HasOne
    {
        return $this->hasOne(Movement::class)
            ->where('started_at', '<=', now())
            ->where(fn($query) => 
                $query->whereNull('ended_at')->orWhere('ended_at', '>', now())
            )
            ->latestOfMany('started_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Filter users by Admin role.
     */
    public function scopeAdmin($query): void
    {
        $query->where('role', UserRole::ADMIN);
    }

    /**
     * Filter users by Employee role.
     */
    public function scopeEmployee($query): void
    {
        $query->where('role', UserRole::EMPLOYEE);
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }
}
