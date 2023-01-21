<?php
if (php_sapi_name() !== 'cli') {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use Database\Migration\Migrate;
use Database\Seeder\TicketSeeder;

$migration = new Migrate();
$migration->run();

$ticketSeeder = new TicketSeeder();
$ticketSeeder->run();
