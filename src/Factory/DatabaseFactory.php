<?php

declare(strict_types=1);

namespace App\Factory;

use App\constants\Constants;
use App\Database\DatabaseInterface;
use App\Database\MysqlDatabase;
use Illuminate\Support\Env;
use InvalidArgumentException;

class DatabaseFactory
{
    public static function create(string $databaseType): DatabaseInterface
    {
        return match ($databaseType) {
            Constants::MYSQL_DATABASE_TYPE => new MysqlDatabase(
                'mysql:host=' . Env::get('MYSQL_HOST', 'mysql') . ';dbname=' . Env::get('MYSQL_DATABASE'),
                Env::get('MYSQL_USER' ),
                Env::get('MYSQL_PASSWORD'),
                []
            ),
            default => throw new InvalidArgumentException("Unsupported database type: $databaseType"),
        };
    }

}