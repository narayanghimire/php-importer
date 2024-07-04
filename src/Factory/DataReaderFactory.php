<?php

namespace App\Factory;

use App\Reader\DataReaderInterface;
use App\Reader\XMLDataReader;
use Illuminate\Container\Container;
use InvalidArgumentException;


class DataReaderFactory
{
    public static function create(string $sourceType): DataReaderInterface
    {
        switch ($sourceType) {
            case 'xml':
                return Container::getInstance()->make(XMLDataReader::class);
            // Add cases for other data source types (e.g., 'csv', 'amazon', 'json') as needed
            default:
                throw new InvalidArgumentException("Unsupported data source type: $sourceType");
        }
    }
}