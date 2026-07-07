<?php
declare(strict_types=1);

use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use Pmqelvis\QueueManagerFactory;

readonly class SubscriptionsService
{
    public const string QUEUE = 'sync-subscriptions';
    public const int STRIPE_PAGE_SIZE = 100;

    public function __construct(
        private SubscriptionsRepository $repository,
        private QueueManagerFactory $queue
    )
    {}

    public function getSubscriptions(?int $limit, ?int $offset, array $filters): Resultset
    {
        return $this->repository->getSubscriptions($limit, $offset, $filters);
    }

    public function syncSubscriptions(): SubscriptionsSyncProcess
    {
        $sync = new SubscriptionsSyncProcess();
        $sync->setStatus(SubscriptionsSyncProcessStatus::Created);

        if ($sync->save() === false) {
            $messages = implode('; ', $sync->getMessages());
            throw new \RuntimeException("Unable to create sync record: {$messages}");
        }

        $this->queue->buildProducer(self::QUEUE)->publish([
            'limit' => self::STRIPE_PAGE_SIZE,
            'starting_after' => null,
            'sync_process_id' => $sync->id,
        ]);

        return $sync;
    }
}