<?php

/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */

use Dotenv\Dotenv;
use Phalcon\Config\Config;

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

return new Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => $_ENV['DATABASE_HOST']     ?? 'localhost',
        'username' => $_ENV['DATABASE_USER']     ?? 'root',
        'password' => $_ENV['DATABASE_PASSWORD'] ?? '',
        'dbname' => $_ENV['DATABASE_NAME']     ?? 'phalcon',
        'charset' => $_ENV['DATABASE_CHARSET']  ?? 'utf8mb4',
    ],
    'rabbitmq' => [
        'host' => $_ENV['RABBITMQ_HOST'] ?? 'localhost',
        'port' => $_ENV['RABBITMQ_PORT'] ?? 5672,
        'username' => $_ENV['RABBITMQ_USER'] ?? 'guest',
        'password' => $_ENV['RABBITMQ_PASSWORD'] ?? 'guest',
    ],
    'stripe' => [
        'secretKey' => $_ENV['STRIPE_SECRET_KEY']      ?? null,
        'publishableKey' => $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? null,
    ],
    'application' => [
        'appDir' => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir' => APP_PATH . '/models/',
        'enumsDir' => APP_PATH . '/enums/',
        'repositoriesDir' => APP_PATH . '/repositories/',
        'servicesDir' => APP_PATH . '/services/',
        'gridServicesDir' => APP_PATH . '/services/grid/',
        'tasksDir' => APP_PATH . '/tasks/',
        'migrationsDir' => APP_PATH . '/migrations/',
        'viewsDir' => APP_PATH . '/views/',
        'pluginsDir' => APP_PATH . '/plugins/',
        'libraryDir' => APP_PATH . '/library/',
        'cacheDir' => BASE_PATH . '/cache/',
        'baseUri' => '/',
    ]
]);
