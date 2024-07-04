#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Command\ImportDataCommand;
use App\Logger\Logger;
use App\Logger\Processor\FacilityProcessor;
use Illuminate\Container\Container;
use Symfony\Component\Console\Application;
use App\config\ContainerConfig;

$containerConfig = new ContainerConfig(
        new Container()
);
$containerConfig->init();
FacilityProcessor::setDefaultFacility(Logger::FACILITY_IMPORTER);

$application = new Application();

$application->add($containerConfig->make(ImportDataCommand::class));

$application->run();