<?php
// app\Models\User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Movement;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    // Role constants
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EMPLOYEE = 'employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
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

    /**
     * The attributes hidden for serialization.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationship: User has many movements.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /**
     * Accessor: Get current movement for the user (planned or active).
     */
    protected function currentMovement(): Attribute
    {
        return Attribute::get(function () {
            $now = now();
            return $this->movements()
                ->whereIn('status', [Movement::STATUS_PLANNED, Movement::STATUS_ACTIVE])
                ->where('started_at', '<=', $now)
                ->where(function ($q) use ($now) {
                    $q->whereNull('ended_at')->orWhere('ended_at', '>=', $now);
                })
                ->first();
        });
    }

    /**
     * Accessor: Check if user is admin.
     */
    protected function isAdmin(): Attribute
    {
        return Attribute::get(fn () => $this->role === self::ROLE_ADMIN);
    }

    /**
     * Accessor: Check if user is employee.
     */
    protected function isEmployee(): Attribute
    {
        return Attribute::get(fn () => $this->role === self::ROLE_EMPLOYEE);
    }

    /**
     * Accessor: Get profile photo URL or default avatar.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->profile_photo_path
                ? Storage::url($this->profile_photo_path)
                : asset('img/default-avatar.png');
        });
    }

    /**
     * Scope: Users with active or planned movements.
     */
    public function scopeWithActiveMovement($query): void
    {
        $now = now();
        $query->whereHas('movements', fn($q) =>
            $q->whereIn('status', [Movement::STATUS_PLANNED, Movement::STATUS_ACTIVE])
              ->where('started_at', '<=', $now)
              ->where(fn($q2) => $q2->whereNull('ended_at')->orWhere('ended_at', '>=', $now))
        );
    }

    /**
     * Scope: Only admin users.
     */
    public function scopeAdmin($query): void
    {
        $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope: Only employee users.
     */
    public function scopeEmployee($query): void
    {
        $query->where('role', self::ROLE_EMPLOYEE);
    }

    /**
     * Get user initials (e.g., "John Doe" → "JD").
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
