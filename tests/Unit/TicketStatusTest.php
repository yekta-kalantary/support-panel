<?php

namespace Tests\Unit;

use App\Enums\TicketStatus;
use PHPUnit\Framework\TestCase;

class TicketStatusTest extends TestCase
{
    public function test_status_labels_are_available_in_persian(): void
    {
        $this->assertSame('باز', TicketStatus::OPEN->label());
        $this->assertSame('درحال بررسی', TicketStatus::IN_PROGRESS->label());
        $this->assertSame('بسته', TicketStatus::CLOSED->label());
    }
}
