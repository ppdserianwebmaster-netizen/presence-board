<?php

namespace App\Enums;

/**
 * MovementType Enum
 * * Defines the classification of an employee's absence or location status.
 * Values are stored as strings in the 'movements' table.
 */
enum MovementType: string
{
    /** Employee is in a meeting (internal or external) */
    case MEETING = 'meeting';

    /** Employee is attending a course, seminar, or training session */
    case COURSE = 'course';

    /** Employee is on official travel or out-station duty */
    case TRAVEL = 'travel';

    /** Employee is on approved leave (Annual, Medical, etc.) */
    case LEAVE = 'leave';

    /** Any other reason for being away from the station */
    case OTHER = 'other';
}
