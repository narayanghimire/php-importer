<?php

declare(strict_types=1);

namespace App\Database;

use Exception;
use PDO;

abstract class AbstractDatabase implements DatabaseInterface
{
    protected PDO $pdo;

    /**
     * @throws Exception
     */
    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        array $options = []
    ) {
        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (Exception $e) {
            throw new Exception("Could not connect to the database: " . $e->getMessage());
        }

        $this->connect();
    }

    abstract public function connect(): void;

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    protected function isConnected(): bool
    {
        return (bool) $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    }
}