<?php

namespace Database\Seeder;

use Lib\Ticket;

class TicketSeeder
{
    public function run() {
        $ticket = new Ticket();
        $events = range(1, 10);
        $total = 1000;
        foreach ($events as $event) {
            $ticket->storeTicket($event, $total);
        }
    }
}
