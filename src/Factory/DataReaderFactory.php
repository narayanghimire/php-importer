<?php

declare(strict_types=1);

namespace App\Factory;

use App\Reader\DataReaderInterface;
use App\Reader\XMLDataReader;
use Illuminate\Container\Container;
use InvalidArgumentException;


class DataReaderFactory
{
    public static function create(string $sourceType): DataReaderInterface
    {
        return match ($sourceType) {
            'xml' => Container::getInstance()->make(XMLDataReader::class),
            default => throw new InvalidArgumentException("Unsupported data source type: $sourceType"),
        };
    }
}