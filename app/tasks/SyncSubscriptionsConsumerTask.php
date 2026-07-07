<?php
declare(strict_types=1);

use PhpAmqpLib\Message\AMQPMessage;

class SyncSubscriptionsConsumerTask extends ConsumerTask
{
    public function handleMessage(AMQPMessage $message): void
    {
        $payload = json_decode($message->getBody(), true) ?: [];
        $limit = (int) ($payload['limit'] ?? SubscriptionsService::STRIPE_PAGE_SIZE);
        $startingAfter = $payload['starting_after'] ?? null;
        $syncProcessId = $payload['sync_process_id'] ?? null;

        if ($syncProcessId === null) {
            $message->ack();
            return;
        }

        $syncProcess = SubscriptionsSyncProcess::findFirst($syncProcessId);

        if ($syncProcess === null) {
            $message->ack();
            return;
        }

        try {
            $this->markInProgress($syncProcess);

            /** @var StripeSubscriptionsService $stripeSubscriptionsService */
            $stripeSubscriptionsService = $this->container->get('stripeSubscriptionsService');

            $subscriptions = $stripeSubscriptionsService->fetchPage($limit, $startingAfter);
            $stripeSubscriptionsService->upsertPage($subscriptions);

            if (count($subscriptions) === $limit) {
                $last = $subscriptions[count($subscriptions) - 1];
                $this->enqueueNextPage($limit, $last->id, $syncProcessId);
            } else {
                $this->markCompleted($syncProcess);
            }
        } catch (\Throwable $exception) {
            $this->markError($syncProcess);
            error_log("[SyncSubscriptionsConsumerTask] {$exception->getMessage()}");
        } finally {
            $message->ack();
        }
    }

    private function enqueueNextPage(int $limit, string $startingAfter, int $syncProcessId): void
    {
        /** @var \Pmqelvis\QueueManagerFactory $queue */
        $queue = $this->container->get('queue');

        $queue->buildProducer(SubscriptionsService::QUEUE)->publish([
            'limit' => $limit,
            'starting_after' => $startingAfter,
            'sync_id' => $syncProcessId,
        ]);
    }
    private function markInProgress(?SubscriptionsSyncProcess $syncProcess): void
    {
        if ($syncProcess?->getStatus() !== SubscriptionsSyncProcessStatus::Created) {
            return;
        }

        $this->saveStatus($syncProcess, SubscriptionsSyncProcessStatus::InProgress);
    }

    private function markCompleted(?SubscriptionsSyncProcess $sync): void
    {
        $this->saveStatus($sync, SubscriptionsSyncProcessStatus::Completed);
    }

    private function markError(?SubscriptionsSyncProcess $syncProcess): void
    {
        $this->saveStatus($syncProcess, SubscriptionsSyncProcessStatus::Error);
    }

    private function saveStatus(?SubscriptionsSyncProcess $syncProcess, SubscriptionsSyncProcessStatus $status): void
    {
        if ($syncProcess === null) {
            return;
        }

        $syncProcess->setStatus($status);
        $syncProcess->save();
    }

    public function getQueueName(): string
    {
        return SubscriptionsService::QUEUE;
    }
}