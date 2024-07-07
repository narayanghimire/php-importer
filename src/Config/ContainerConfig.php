<?php

declare(strict_types=1);

namespace App\Config;

use App\Command\ImportDataCommand;
use App\Database\DatabaseInterface;
use App\Constants\Constants;
use App\Factory\DatabaseFactory;
use App\Factory\DataReaderFactory;
use App\Logger\Logger;
use App\Logger\Processor\ExceptionProcessor;
use App\Logger\Processor\FacilityProcessor;
use App\Reader\DataReaderInterface;
use App\Reader\XMLDataReader;
use App\Repository\DataRepository;
use App\Repository\DataRepositoryInterface;
use App\Service\DataImportService;
use Closure;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Env;
use Illuminate\Container\Container;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Monolog\Logger as MonologLogger;

class ContainerConfig
{
    private Container $container;
    public const REQUIRED_ENV_VARS = [
        'MYSQL_HOST',
        'MYSQL_DATABASE',
        'MYSQL_USER',
        'MYSQL_PASSWORD',
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $abstract
     * @return Closure|mixed|object|null
     * @throws BindingResolutionException
     */
    public function make(string $abstract)
    {
        return $this->container->make($abstract);
    }

    /**
     * @throws Exception
     */
    public function init(): void
    {
        $this->checkEnvironment();
        $this->container
            ->when(ImportDataCommand::class)
            ->needs('$fileName')
            ->give(
                Env::get('XML_FILE_PATH',
                    getcwd(). Constants::DEFAULT_XML_FILE_PATH
                )
            );

        $this->container->singleton(LoggerInterface::class, function () {
            $monolog = new MonologLogger('php-importer');
            $monolog->pushHandler(new StreamHandler(__DIR__ . '/../../logs/error.log', MonologLogger::DEBUG));
            $monolog->pushProcessor(new FacilityProcessor());
            $monolog->pushProcessor(new ExceptionProcessor());

            return new Logger($monolog);
        });

        $this->container->singleton(DatabaseInterface::class, function () {
            $databaseType =  Env::get('DATABASE_TYPE', 'mysql') ?? Constants::MYSQL_DATABASE_TYPE;
            // @phpstan-ignore-next-line
            return DatabaseFactory::create($databaseType);
        });

        $this->container->singleton(DataRepository::class, function ($container) {
            return new DataRepository(
                $container->make(DatabaseInterface::class),
                $container->make(LoggerInterface::class)
            );
        });

        $this->container->singleton(DataReaderInterface::class, function () {
            $sourceType = getenv('DATA_SOURCE_TYPE') ?? 'xml';
            return DataReaderFactory::create($sourceType);
        });

        $this->container->bind(DataRepositoryInterface::class, DataRepository::class);


        $this->container->singleton(DataImportService::class, function ($container) {
            return new DataImportService(
                $container->make(XMLDataReader::class),
                $container->make(DataRepository::class)
            );
        });
    }

    protected function checkEnvironment(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $missing = [];
        foreach (static::REQUIRED_ENV_VARS as $key) {
            if (Env::get($key, null) === null) {
                $missing[] = $key;
            }
        }

        if (count($missing)) {
            throw new Exception(
                sprintf(
                    'Missing environment variable(s) for package configuration: %s',
                    implode(', ', $missing)
                )
            );
        }
    }
}