<?php

use Phalcon\Mvc\Router;

$router = new Router();

$router->addGet('/subscriptions', [
    'controller' => 'subscriptions',
    'action' => 'getSubscriptions',
]);

$router->addPost('/subscriptions/sync', [
    'controller' => 'subscriptions',
    'action' => 'syncSubscriptions',
]);

return $router;
