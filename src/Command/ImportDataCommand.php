<?php

declare(strict_types=1);

namespace App\Command;

use App\config\ContainerConfig;
use App\Service\DataImportService;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDataCommand extends Command
{
    public function __construct(
        private DataImportService $dataImportService,
        private LoggerInterface $logger,
        private String $fileName
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('data:import');
        $this->setDescription('Imports data from XML to the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->dataImportService->importData($this->fileName, $output);
            $output->writeln('<info>Data import successful.</info>');
        } catch (Exception $e) {
            $this->logger->log(
                LogLevel::ERROR,
                "Error while importing file: {$this->fileName}",
                [
                    'exception' => $e
                ]
            );
            $output->writeln('<error>Data import failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
