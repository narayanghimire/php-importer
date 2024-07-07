#php-importer Data Importer

## Introduction

Welcome to the documentation for the php-importer project! This project initially aimed to create a command-line 
application in PHP that processes a local XML file (feed.xml) and pushes its data to a database (e.g., MYSQL, SQLITE). 
However, as part of demonstrating advanced development skills and thorough planning, the project has been designed with
expandability and extensibility in mind. This documentation will detail not only the basic functionality but also 
potential enhancements and future-proofing considerations.

You might be wondering, `Why such a detailed design for a small project?` Well, I wanted to give you a good look at how
I work in software development, beyond just solving the immediate task. Let's keep in mind, we're all constantly 
learning! I'd really appreciate any thoughts or feedback on this project. It's a fantastic way to learn from each 
other and keep getting better! I know there is still something that needs to be done, but I think it is enough for now.

## Requirements

- Docker

## Installation & Running the Application

To run the application, make sure you have Docker installed to avoid any database connection issues. 
Then, follow these steps:

1. Clone the repository:
   ```bash
   git clone https://github.com/narayanghimire/php-importer.git
2. Navigate to the project directory:   
   ```bash
   cd php-importer
3. Set up environment variables: Create a .env file in the root directory with your database credentials. Example:
   ```bash
   MYSQL_ROOT_PASSWORD=admin
   MYSQL_DATABASE=mydatabase
   MYSQL_USER=root
   MYSQL_PASSWORD=root
   XML_FILE_NAME=/resources/feed.xml
   MYSQL_HOST=mysql
4. Build and run the Docker containers:
   ```bash
   docker-compose up --build -d

5. Run composer install:
   ```bash
   docker-compose run php-importer composer install
6. Run the importer command:
   ```bash
   docker-compose run php-importer php bin/console.php data:import

## Deployment Strategy
The project utilizes GitHub Actions workflows for automated testing and deployment. This ensures robustness and
consistency throughout the development lifecycle.

#### Feature Branches:

1. Each feature branch undergoes unit testing, PHPStan code analysis, and integration tests to validate changes.
2. Code reviews are conducted to maintain high code quality standards.

#### Cloud or server deployment(IN FUTURE):

1. After a feature branch is built and passes tests, a pull request can be merged to the main branch.
2. Once merged, a GitHub Action pipeline can be triggered to deploy to cloud platforms (e.g., Google Cloud, AWS) or to
 any specific php server.

## Application Structure and Design

The XML Data Importer application is structured to facilitate scalability and maintainability:

### Command-Line Interface (CLI):
1. Implements Symfony Console for intuitive command execution (`data:import`).

### Logging and Error Handling:
1. Errors are logged to ``logs/error.log`` for effective troubleshooting.
2. Monolog is used for detailed monitoring and debugging.

### Database Flexibility:
1. Supports MySQL database backend with configurable parameters via environment variables (`MYSQL_*`)
2. The implementation is designed to handle other database types by updating the .env file with ``DATABASE_TYPE=mysql``
or ``DATABASE_TYPE=sqlite``. In such cases, a new class must be extended:
```
<?php

declare(strict_types=1);

namespace App\Database;

use Exception;
use PDO;

class XXXDatabase extends AbstractDatabase
{
    /**
     * @throws Exception
     */
    public function connect(): void
    {
        if (!$this->isConnected()) {
            throw new Exception("Could not connect to the xxxx database.");
        }
    }
}
```
To use newly added database container config must be adjusted.
```
$this->container->singleton(DatabaseInterface::class, function () {
            $databaseType = (string) Env::get('DATABASE_TYPE', 'xxx') ?: Constants::xxx_DATABASE_TYPE;
            return DatabaseFactory::create($databaseType);
 });

```

### Importer Data Reader Flexibility

Currently, the application uses a default XML data reader. The default path for the XML file is `/resource/feed.xml`. The application is designed to be easily extendable for future data sources such as CSV, JSON, etc.

For example, if there is a new data source like CSV, the `DataReaderInterface` must be implemented. Here's an example implementation:

```php
<?php

declare(strict_types=1);

namespace App\Reader;

use Exception;

class CSVDataReader implements DataReaderInterface
{
    public function __construct(
        private readonly CSVReader $reader,
        private readonly CSVItemTransformer $transformer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function read(string $filePath): ItemCollection
    {
        // Implementation for reading CSV data
        ................
        ................
        ................
        ................
    }
}
```
Once this is implemented, a new ``.env`` key must be added:
``DATA_SOURCE_TYPE=CSV
``
Alternatively, this can be done by changing the container configuration:
```
$this->container->singleton(DataReaderInterface::class, function () {
    $sourceType = getenv('DATA_SOURCE_TYPE') ?? 'csv';
    return DataReaderFactory::create($sourceType);
});

```
### TypedCollection 

Enhances the standard Laravel collection by checking all items against
a given type before allowing them to be added to the collection.

##### Usage

Given that we want a collection that is only allowed to contain instances
of ExampleClass, we would have to make our own ExampleClassCollection that
inherits from TypedCollection:
```
use App\Model;

class ExampleClassCollection extends TypedCollection
{
    protected const ALLOWED_TYPE = ExampleClassCollection::class;
}
```
By overriding the constant ALLOWED_TYPE we define what is allowed to
go into the collection. Trying to add anything to the collection that is
not an instance of ExampleClass will now yield an InvalidArgumentException.

## Testing and Quality Assurance
The application includes comprehensive testing to ensure reliability and functionality:

### Unit Tests:
1. Command: ``docker-compose run php-importer composer unit-test``
2. Validates individual components and functionalities.
3. Automated testing on feature branches provided early detection of potential breaking changes

### Integration Tests:
1.  Command: ``docker-compose run php-importer composer Integration Tests``
2. Provided test for importing the xml document