<?php

declare(strict_types=1);

namespace App\Service;

use App\Logger\Logger;
use App\Reader\DataReaderInterface;
use App\Repository\DataRepositoryInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

readonly class DataImportService
{
    public function __construct(
        private DataReaderInterface     $reader,
        private DataRepositoryInterface $itemRepository
    ){}

    /**
     * @throws Exception
     */
    public function importData(string $file, OutputInterface $output): void
    {
        $itemCollection = $this->reader->read($file);
        $this->itemRepository->save($itemCollection, $output);
    }
}