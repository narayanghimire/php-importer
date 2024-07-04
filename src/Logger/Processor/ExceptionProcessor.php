<?php

namespace App\Logger\Processor;

use Monolog\Processor\ProcessorInterface;
use Monolog\LogRecord;
use Throwable;

class ExceptionProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        if (isset($record->context['exception']) && $record->context['exception'] instanceof Throwable) {
            $exception = $record->context['exception'];

            $record->extra['exception'] = [
                'level' => $record->level->getName(),
                'type' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace()
            ];
        }

        return $record;
    }
}
