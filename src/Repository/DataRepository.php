<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\DatabaseInterface;
use App\Model\Item;
use App\Model\ItemCollection;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Exception;
use Symfony\Component\Console\Output\OutputInterface;

class DataRepository implements DataRepositoryInterface
{
    private DatabaseInterface $database;
    private LoggerInterface $logger;
    private int $insertCount = 0;
    private int $updateCount = 0;

    public function __construct(DatabaseInterface $database, LoggerInterface $logger)
    {
        $this->database = $database;
        $this->logger   = $logger;
    }

    /**
     * TODO: This method can be expanded further to create separate log files for each data import.
     *       Using separate log files allows for easier comparison and troubleshooting in case of database issues.
     *       This helps in maintaining data integrity and making it easier to identify and resolve any discrepancies.
     */

    public function save(ItemCollection $itemCollection, OutputInterface $output): void
    {
        $pdo = $this->database->getPdo();
        $pdo->beginTransaction();

        try {
            foreach ($itemCollection as $item) {
                if ($item instanceof Item) {
                    try {
                        $this->upsertItem($pdo, $item, $output);
                    } catch (PDOException $exception) {
                        // Log the detailed exception
                        $this->logger->log(
                            LogLevel::ERROR,
                            "Error while saving data in the database",
                            [
                                'item' => $item,
                                'exception' => $exception
                            ]
                        );

                        // Rollback the transaction and rethrow the exception
                        $pdo->rollBack();
                        throw new Exception("Import failed: " . $exception->getMessage(), 0, $exception);
                    }
                }
            }
            $pdo->commit();
            $output->writeln(sprintf('<info>Imported total %d items successfully.</info>', $this->insertCount));
            $output->writeln(sprintf('<info>updated total %d items successfully.</info>', $this->updateCount));
        } catch (PDOException $e) {
            $pdo->rollBack();
            // Log the exception for the transaction failure
            $this->logger->log(
                LogLevel::ERROR,
                "Transaction failed",
                [
                    'exception' => $e
                ]
            );
            throw new Exception("Transaction failed: " . $e->getMessage(), 0, $e);
        }
    }

    private function upsertItem(PDO $pdo, Item $item, OutputInterface $output): void
    {
        $sql = "INSERT INTO items (
                   entityId, 
                   categoryName, 
                   sku, 
                   name, 
                   description, 
                   shortDesc, 
                   price, 
                   link, 
                   image, 
                   brand, 
                   rating, 
                   caffeineType, 
                   count, 
                   flavored, 
                   seasonal, 
                   inStock, 
                   facebook, 
                   isKCup
                ) VALUES (
                        :entityId, 
                        :categoryName, 
                        :sku, 
                        :name, 
                        :description,
                        :shortDesc,
                        :price,
                        :link, 
                        :image, 
                        :brand,
                        :rating, 
                        :caffeineType,
                        :count,
                        :flavored,
                        :seasonal, 
                        :inStock,
                        :facebook,
                        :isKCup
                )
                ON DUPLICATE KEY UPDATE 
                categoryName = VALUES(categoryName), 
                sku = VALUES(sku), 
                name = VALUES(name), 
                description = VALUES(description), 
                shortDesc = VALUES(shortDesc), 
                price = VALUES(price), 
                link = VALUES(link), 
                image = VALUES(image), 
                brand = VALUES(brand), 
                rating = VALUES(rating), 
                caffeineType = VALUES(caffeineType), 
                count = VALUES(count), 
                flavored = VALUES(flavored), 
                seasonal = VALUES(seasonal), 
                inStock = VALUES(inStock), 
                facebook = VALUES(facebook), 
                isKCup = VALUES(isKCup)
            ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':entityId', $item->getEntityId(), PDO::PARAM_INT);
        $stmt->bindValue(':categoryName', $item->getCategoryName(), PDO::PARAM_STR);
        $stmt->bindValue(':sku', $item->getSku(), PDO::PARAM_STR);
        $stmt->bindValue(':name', $item->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $item->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':shortDesc', $item->getShortdesc(), PDO::PARAM_STR);
        $stmt->bindValue(':price', $item->getPrice(), PDO::PARAM_STR);
        $stmt->bindValue(':link', $item->getLink(), PDO::PARAM_STR);
        $stmt->bindValue(':image', $item->getImage(), PDO::PARAM_STR);
        $stmt->bindValue(':brand', $item->getBrand(), PDO::PARAM_STR);
        $stmt->bindValue(':rating', $item->getRating(), PDO::PARAM_INT);
        $stmt->bindValue(':caffeineType', $item->getCaffeineType(), PDO::PARAM_STR);
        $stmt->bindValue(':count', $item->getCount(), PDO::PARAM_INT);
        $stmt->bindValue(':flavored', $item->isFlavored() ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':seasonal', $item->isSeasonal() ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':inStock', $item->isInStock() ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':facebook', $item->getFacebook(), PDO::PARAM_INT);
        $stmt->bindValue(':isKCup', $item->isKCup() ? 1 : 0, PDO::PARAM_INT);

        $stmt->execute();

        // Count as insert or update
        if ($stmt->rowCount() > 0) {
            $this->insertCount++;
            $output->writeln(sprintf('Imported %d items successfully.', $item->getEntityId()));
        } else {
            $this->updateCount++;
            $output->writeln(sprintf('updated %d items successfully.', $item->getEntityId()));
        }
    }

    /**
     * @throws Exception
     */
    public function delete(int $entityId): bool
    {
        $pdo = $this->database->getPdo();

        try {
            $sql = "DELETE FROM items WHERE entityId = :entityId";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':entityId', $entityId, PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (PDOException $exception) {
            // Log the detailed exception
            $this->logger->log(
                LogLevel::ERROR,
                "Error while deleting item from the database",
                [
                    'entityId' => $entityId,
                    'exception' => $exception
                ]
            );
            throw new Exception("Deletion failed: " . $exception->getMessage(), 0, $exception);
        }
    }
}
