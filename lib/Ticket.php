<?php

namespace Lib;

use Database\Connection\DB;

class Ticket
{
    const PREFIX = "DTK";
    private $DB = null;

    public function __construct()
    {
        $this->DB = new DB();
    }

    public function runCommand(array $argv)
    {
        $this->validate($argv);

        $eventId = $argv[1];
        $totalTicket = $argv[2];

        $this->storeTicket($eventId, $totalTicket);

        $this->display("Ticket successfully generated!");
    }

    private function out($message)
    {
        echo $message;
    }

    private function newline()
    {
        $this->out("\n");
    }

    private function display($message)
    {
        $this->out($message);
        $this->newline();
        $this->newline();
    }

    private function generateTicket($length = 7) {
        $code = strtoupper(self::PREFIX.substr(md5(microtime()), 0, $length));

        $exist = $this->DB->table('tickets')->select('ticket_code')
            ->where('ticket_code', $code)
            ->get();

        if (count($exist) > 0) $code = $this->generateTicket();

        return $code;
    }

    public function storeTicket($eventId, $totalTicket) {
        foreach (range(1, $totalTicket) as $key => $value) {
            $code = $this->generateTicket();
            $result = $this->DB->table('tickets')->insert([
                'event_id' => $eventId,
                'ticket_code' => $code
            ]);
            if ($result) {
                $this->out("$value. $code");
                $this->newline();
            };
        }
    }

    private function validate($argv) {
        if (count($argv) != 3) {
            $this->display("Usage: php {$argv[0]} {event_id} {total_ticket}");
            exit;
        }

        if (!is_numeric($argv[2])) {
            $this->display("total_ticket: is invalid number");
            exit;
        }

        if (intval($argv[2]) < 1) {
            $this->display("total_ticket: is must be positive number");
            exit;
        }
    }
}
