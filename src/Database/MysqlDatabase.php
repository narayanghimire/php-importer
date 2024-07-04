<?php

declare(strict_types=1);

namespace App\Database;

use Exception;
use PDO;

class MysqlDatabase extends AbstractDatabase
{
    /**
     * @throws Exception
     */
    public function connect(): void
    {
        if (!$this->isConnected()) {
            throw new Exception("Could not connect to the MySQL database.");
        }
    }
}