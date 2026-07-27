<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'باز',
            self::IN_PROGRESS => 'درحال بررسی',
            self::CLOSED => 'بسته',
        };
    }
}
