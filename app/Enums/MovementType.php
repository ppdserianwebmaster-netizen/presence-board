<?php

namespace App\Enums;

/**
 * MovementType Enum
 * Centralized classification for employee status with UI metadata.
 */
enum MovementType: string
{
    case MEETING = 'meeting';
    case COURSE  = 'course';
    case TRAVEL  = 'travel';
    case LEAVE   = 'leave';
    case OTHER   = 'other';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            self::MEETING => 'Meeting',
            self::COURSE  => 'Training / Course',
            self::TRAVEL  => 'Official Travel',
            self::LEAVE   => 'On Leave',
            self::OTHER   => 'Other / Out of Office',
        };
    }

    /**
     * Tailwind CSS Color Palette.
     * Refactor: Return the full class or a specific intensity for consistency.
     */
    public function color(): string
    {
        return match($this) {
            self::MEETING => 'blue',
            self::COURSE  => 'indigo',
            self::TRAVEL  => 'orange',
            self::LEAVE   => 'rose',
            self::OTHER   => 'slate',
        };
    }

    /**
     * Heroicon names (compatible with Blade UI Kit or Flux).
     */
    public function icon(): string
    {
        return match($this) {
            self::MEETING => 'users',
            self::COURSE  => 'academic-cap',
            self::TRAVEL  => 'briefcase',
            self::LEAVE   => 'calendar-days',
            self::OTHER   => 'question-mark-circle',
        };
    }

    /**
     * Helper for Livewire Select Select options.
     * Refactor: Uses array_column for better performance over collect().
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
