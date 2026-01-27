<?php

namespace App\Enums;

/**
 * UserRole Enum
 * Defines authorization levels and UI presentation for users.
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';

    /**
     * Get the human-readable label.
     * Usage: $user->role->label()
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN    => 'System Administrator',
            self::EMPLOYEE => 'Employee',
        };
    }

    /**
     * Get the Tailwind CSS color base.
     * Usage: <span class="bg-{{ $user->role->color() }}-100 text-{{ $user->role->color() }}-700">
     */
    public function color(): string
    {
        return match($this) {
            self::ADMIN    => 'indigo',
            self::EMPLOYEE => 'slate',
        };
    }

    /**
     * Static helper for Livewire/Flux dropdowns.
     * Optimized for PHP 8.4 using native array functions.
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
