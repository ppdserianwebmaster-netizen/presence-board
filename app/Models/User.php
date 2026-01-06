<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\Movement;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_EMPLOYEE = 'employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'department',
        'position',
        'role',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        // 'two_factor_secret',
        // 'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    protected function currentMovement(): Attribute
    {
        return Attribute::get(function () {
            $now = now();
            return $this->movements()
                ->whereIn('status',[Movement::STATUS_PLANNED, Movement::STATUS_ACTIVE])
                ->where('start_datetime','<=',$now)
                ->where(function($q) use ($now) {
                    $q->whereNull('end_datetime')->orWhere('end_datetime','>=',$now);
                })
                ->first();
        });
    }

    protected function isAdmin(): Attribute
    {
        return Attribute::get(fn() => $this->role === self::ROLE_ADMIN);
    }

    public function scopeWithActiveMovement(Builder $query): void
    {
        $now = now();
        $query->whereHas('movements',fn($q)=>$q->whereIn('status',[Movement::STATUS_PLANNED,Movement::STATUS_ACTIVE])
            ->where('start_datetime','<=',$now)
            ->where(function($q2) use($now){$q2->whereNull('end_datetime')->orWhere('end_datetime','>=',$now);})
        );
    }

    public function scopeAdmin(Builder $query): void {$query->where('role',self::ROLE_ADMIN);}
    public function scopeEmployee(Builder $query): void {$query->where('role',self::ROLE_EMPLOYEE);}

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->profile_photo) {
                return Storage::url($this->profile_photo);
            }

            // Default avatar
            return asset('img/default-avatar.png');
        });
    }
}
