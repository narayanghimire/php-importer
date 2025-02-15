<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

interface DatabaseInterface
{
    public function connect(): void;
    public function getPdo(): PDO;
}