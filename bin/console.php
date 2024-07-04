#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Command\ImportDataCommand;
use App\Logger\Logger;
use App\Logger\Processor\FacilityProcessor;
use Symfony\Component\Console\Application;
use App\config\ContainerConfig;

$container = new ContainerConfig();
$container->init();
FacilityProcessor::setDefaultFacility(Logger::FACILITY_IMPORTER);

$application = new Application();

$application->add($container->make(ImportDataCommand::class));

$application->run();