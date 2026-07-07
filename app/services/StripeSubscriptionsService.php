<?php
declare(strict_types=1);

use Stripe\StripeClient;
use Stripe\Subscription;

readonly class StripeSubscriptionsService
{
    public function __construct(
        private StripeClient $stripe,
        private SubscriptionsRepository $repository
    ) {}

    /**
     * @return Subscription[]
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function fetchPage(int $limit, ?string $startingAfter): array
    {
        $params = ['limit' => $limit, 'status' => 'all'];
        if ($startingAfter !== null && $startingAfter !== '') {
            $params['starting_after'] = $startingAfter;
        }

        return $this->stripe->subscriptions->all($params)->data;
    }

    /**
     * @param Subscription[] $subscriptions
     */
    public function upsertPage(array $subscriptions): void
    {
        if ($subscriptions === []) {
            return;
        }

        $rows = array_map([$this, 'mapSubscription'], $subscriptions);

        $this->repository->upsertSubscriptions($rows);
    }

    private function mapSubscription(Subscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'customer_id' => $subscription->customer,
            'status' => $subscription->status,
            'created' => $this->toDateTime($subscription->created),
            'start_date' => $this->toDateTime($subscription->start_date ?? null),
            'cancel_at' => $this->toDateTime($subscription->cancel_at ?? null),
            'canceled_at' => $this->toDateTime($subscription->canceled_at ?? null),
            'ended_at' => $this->toDateTime($subscription->ended_at ?? null),
        ];
    }

    private function toDateTime(?int $timestamp): ?string
    {
        if (empty($timestamp)) {
            return null;
        }

        return gmdate('Y-m-d H:i:s', $timestamp);
    }
}