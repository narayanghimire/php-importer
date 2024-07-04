<?php

namespace App\Logger\Processor;

use InvalidArgumentException;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use ReflectionClass;

class FacilityProcessor implements ProcessorInterface
{
    private static string $defaultFacility = 'Unknown';

    public function __invoke(LogRecord $record): LogRecord
    {
        $facility = $record->context['facility'] ?? self::$defaultFacility;
        if (!self::isValidFacility($facility)) {
            $message  = "Trying to log using unknown facility: '$facility'";
            $facility = self::$defaultFacility;
            trigger_error($message);
        }

        $record['extra']['facility'] = $facility;
        return $record;
    }

    /**
     * Checks if a given facility belongs to the group of valid facilities.
     */
    public static function isValidFacility(string $facility): bool
    {
        $reflection = new ReflectionClass('App\Logger\Logger');
        $validFacilities = array_filter(
            $reflection->getConstants(),
            fn($key) => str_starts_with($key, 'FACILITY_'),
            ARRAY_FILTER_USE_KEY
        );

        return in_array($facility, $validFacilities, true);
    }

    public static function setDefaultFacility(string $facility): void
    {
        if (!self::isValidFacility($facility)) {
            throw new InvalidArgumentException("Trying to set invalid default facility: '$facility'");
        }

        self::$defaultFacility = $facility;
    }
}
