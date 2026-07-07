<?php

use Phalcon\Cli\Task;
use PhpAmqpLib\Message\AMQPMessage;

abstract class ConsumerTask extends Task
{
    public function consumeAction(): void
    {
        /** @var \Pmqelvis\QueueManagerFactory $queue */
        $queue = $this->container->get('queue');

        $queue->buildConsumer(SubscriptionsService::QUEUE)
            ->consume([$this, 'handleMessage']);
    }

    abstract public function getQueueName(): string;

    abstract public function handleMessage(AMQPMessage $message): void;
}