<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$di = new CliDI();

$config = include __DIR__ . '/config/config.php';
$di->set('config', $config);

include __DIR__ . '/config/loader.php';
include __DIR__ . '/config/services.php';

$console = new ConsoleApp();
$console->setDI($di);

$arguments = [];
foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    $console->handle($arguments);
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(255);
}