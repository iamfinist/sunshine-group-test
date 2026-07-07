<?php
declare(strict_types=1);

use Phalcon\Mvc\Url as UrlResolver;
use Pmqelvis\QueueManagerFactory;
use Pmqelvis\RabbitMQAdapter;
use Stripe\StripeClient;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    return new $class($params);
});

$di->set('queue', function () {
    $config = $this->getConfig();

    $adapter = new RabbitMQAdapter(
        $config->rabbitmq->host,
        (int) $config->rabbitmq->port,
        $config->rabbitmq->username,
        $config->rabbitmq->password,
    );
    return new QueueManagerFactory($adapter);
});

$di->setShared('subscriptionsRepository', function () use ($di) {
    return new SubscriptionsRepository(
        $di->get('db'),
        $di->get('modelsManager'),
        new BatchUpsertQueryBuilder()
    );
});

$di->setShared('subscriptionsService', function () use ($di) {
    return new SubscriptionsService(
        $di->get('subscriptionsRepository'),
        $di->get('queue'),
    );
});

$di->setShared('stripeSubscriptionsService', function () use ($di) {
    return new StripeSubscriptionsService(
        $di->get('stripe'),
        $di->get('subscriptionsRepository'),
    );
});

$di->setShared('subscriptionsGridService', function () {
    return new SubscriptionsGridService();
});

$di->setShared('stripe', function () {
    $config = $this->getConfig();

    return new StripeClient($config->stripe->secretKey);
});
