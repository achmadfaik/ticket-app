<?php

namespace Database\Migration;
use Database\Connection\DB;

require_once('table_tickets_schema.php');

class Migrate
{
    private $DB = null;

    public function __construct() {
        $this->DB = new DB();
    }

    public function run() {
        foreach (STATEMENTS as $statement) {
            $this->DB->exec($statement);
        }
    }

}
