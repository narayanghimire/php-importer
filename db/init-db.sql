CREATE DATABASE IF NOT EXISTS mydatabase;

USE mydatabase;

CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entityId INT UNIQUE,
    categoryName VARCHAR(255),
    sku VARCHAR(255),
    name VARCHAR(255),
    description TEXT,
    shortDesc TEXT,
    price DECIMAL(10, 2),
    link VARCHAR(255),
    image VARCHAR(255),
    brand VARCHAR(255),
    rating INT,
    caffeineType VARCHAR(50),
    count INT,
    flavored TINYINT(1),
    seasonal TINYINT(1),
    inStock TINYINT(1),
    facebook VARCHAR(255),
    isKCup TINYINT(1),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
