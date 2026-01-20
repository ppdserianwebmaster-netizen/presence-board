<?php
// app\Enums\UserRole.php

namespace App\Enums;

/**
 * UserRole Enum
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';

    /**
     * Get the human-readable label.
     * Usage in Blade: $user->role->label()
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN    => 'System Administrator',
            self::EMPLOYEE => 'Employee',
        };
    }

    /**
     * Get the CSS color.
     * Usage in Blade: $user->role->color()
     */
    public function color(): string
    {
        return match($this) {
            self::ADMIN    => 'indigo',
            self::EMPLOYEE => 'slate',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}
