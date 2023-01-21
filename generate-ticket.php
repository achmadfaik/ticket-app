<?php
if (php_sapi_name() !== 'cli') {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use Lib\Ticket;

$app = new Ticket();
$app->runCommand($argv);
