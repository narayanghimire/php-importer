<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Config\ContainerConfig;
use Dotenv\Dotenv;
use Illuminate\Container\Container;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (!function_exists('app')) {
    function app(): Container
    {
        return Container::getInstance();
    }
}

(new ContainerConfig(app()))->init();

