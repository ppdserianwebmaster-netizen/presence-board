<?php

namespace App\Enums;

/**
 * UserRole Enum
 * Defines system access levels.
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';

    /**
     * Get a human-readable label for the role.
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'System Administrator',
            self::EMPLOYEE => 'Employee',
        };
    }
}
