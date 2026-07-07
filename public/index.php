<?php
declare(strict_types=1);

use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Application;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {
    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Handle routes
     */
    $di->set('router', function () {
        return include APP_PATH . "/config/router.php";
    });

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Handle the request
     */
    $application = new Application($di);
    $application->useImplicitView(false);

    $response = $application->handle($_SERVER['REQUEST_URI']);

    if ($response instanceof ResponseInterface && $response->isSent() === false) {
        $response->send();
    }
} catch (\Exception $exception) {
    error_log(sprintf(
        '%s in %s:%d',
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));

    $response = new Response();
    $response->setStatusCode(500, 'Internal Server Error');
    $response->setJsonContent([
        'error' => $exception->getMessage(),
        'code' => $exception->getCode(),
    ]);
    $response->send();
}
