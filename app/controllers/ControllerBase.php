<?php
declare(strict_types=1);

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends Controller
{
    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
        $this->response->setContentType('application/json');
        $this->response->setHeader('Cache-Control', 'no-store');

        $data = $dispatcher->getReturnedValue();
        $dispatcher->setReturnedValue([]);

        if ($this->response->isSent() !== true) {
            $this->response->setJsonContent($data);

            return $this->response->send();
        }

        return null;
    }
}
