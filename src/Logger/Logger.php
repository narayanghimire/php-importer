<?php

namespace App\Logger;

use Exception;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class Logger extends AbstractLogger
{
    public const FACILITY_UNKNOWN = 'Unknown';
    public const FACILITY_IMPORTER = 'Importer';

    private LoggerInterface $logHandler;

    public function __construct(LoggerInterface $logHandler)
    {
        $this->logHandler = $logHandler;
    }

    /**
     * @throws Exception
     */
    public function log($level, $message, array $context = []): void
    {
        $this->checkLevel($level);
        try {
            $this->logHandler->log($level, $message, $context);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function checkLevel(mixed $level): void
    {
        $validLevels = [
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::EMERGENCY,
            LogLevel::DEBUG,
            LogLevel::CRITICAL,
            LogLevel::ALERT,
        ];

        if (!in_array($level, $validLevels, true)) {
            $message = "Trying to log using invalid level: '$level'";
            throw new Exception($message);
        }
    }
}
