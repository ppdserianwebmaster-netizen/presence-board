<?php
// app\Enums\MovementType.php

namespace App\Enums;

/**
 * MovementType Enum
 * Defines the classification of an employee's status.
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
     * Usage in Blade: {{ $movement->type->label() }}
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
     * Tailwind CSS color.
     * Usage in Blade: <span class="text-{{ $movement->type->color() }}-600">
     */
    public function color(): string
    {
        return match($this) {
            self::MEETING => 'blue',
            self::COURSE  => 'indigo',
            self::TRAVEL  => 'purple',
            self::LEAVE   => 'red',
            self::OTHER   => 'slate',
        };
    }

    /**
     * FontAwesome/Heroicon icon name.
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
     * Static helper for Livewire dropdowns.
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}
