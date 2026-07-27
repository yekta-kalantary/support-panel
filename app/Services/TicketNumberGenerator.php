<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TicketNumberGenerator
{
    public function next(): string
    {
        $year = (int) now()->format('Y');

        DB::table('ticket_sequences')->insertOrIgnore([
            'year' => $year,
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = DB::table('ticket_sequences')
            ->where('year', $year)
            ->lockForUpdate()
            ->first();

        if (! $sequence) {
            throw new \RuntimeException('Unable to initialize the ticket number sequence.');
        }

        $number = ((int) $sequence->last_number) + 1;

        DB::table('ticket_sequences')
            ->where('year', $year)
            ->update([
                'last_number' => $number,
                'updated_at' => now(),
            ]);

        return sprintf('TKT-%d-%06d', $year, $number);
    }
}
