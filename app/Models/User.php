<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * User Model (Laravel 12)
 *
 * Represents an employee or administrator account.
 *
 * Responsibilities:
 * - Authentication & authorization
 * - Employee profile data
 * - Movement aggregation & status derivation
 *
 * This model is intentionally logic-light and
 * delegates time-based status logic to Movement scopes
 * where possible.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $employee_id
 * @property string|null $department
 * @property string|null $position
 * @property string $role
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Movement> $movements
 * @property-read Movement|null $current_movement
 * @property-read bool $is_admin
 * @property-read bool $is_employee
 * @property-read string $profile_photo_url
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /*
     |======================================================================
     | Role Constants
     |======================================================================
     */

    public const ROLE_ADMIN = 'admin';
    public const ROLE_EMPLOYEE = 'employee';

    /*
     |======================================================================
     | Mass Assignment & Serialization
     |======================================================================
     */

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
     * Attribute casting configuration.
     *
     * - password is automatically hashed
     * - email_verified_at is treated as Carbon instance
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
     |======================================================================
     | Relationships
     |======================================================================
     */

    /**
     * A user may have multiple movements over time.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /*
     |======================================================================
     | Derived Attributes (Accessors)
     |======================================================================
     */

    /**
     * Current active movement (if any).
     *
     * A movement is considered active when:
     * - started_at <= now
     * - AND (ended_at IS NULL OR ended_at > now)
     *
     * Returns the most recent active movement.
     */
    protected function currentMovement(): Attribute
    {
        return Attribute::get(function () {
            $now = now();

            return $this->movements()
                ->active()
                ->latest('started_at')
                ->first();
        });
    }

    /** Determine whether the user is an administrator. */
    protected function isAdmin(): Attribute
    {
        return Attribute::get(fn () => $this->role === self::ROLE_ADMIN);
    }

    /** Determine whether the user is a standard employee. */
    protected function isEmployee(): Attribute
    {
        return Attribute::get(fn () => $this->role === self::ROLE_EMPLOYEE);
    }

    /**
     * Fully-qualified profile photo URL.
     * Falls back to a default avatar when none is provided.
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
     |======================================================================
     | Query Scopes
     |======================================================================
     */

    /**
     * Scope users who currently have at least one active movement.
     */
    public function scopeWithActiveMovement($query): void
    {
        $query->whereHas('movements', fn ($q) => $q->active());
    }

    /** Scope administrator accounts. */
    public function scopeAdmin($query): void
    {
        $query->where('role', self::ROLE_ADMIN);
    }

    /** Scope employee accounts. */
    public function scopeEmployee($query): void
    {
        $query->where('role', self::ROLE_EMPLOYEE);
    }

    /*
     |======================================================================
     | Helpers
     |======================================================================
     */

    /**
     * Generate uppercase initials from the user's name.
     *
     * Example: "John Doe" => "JD"
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
