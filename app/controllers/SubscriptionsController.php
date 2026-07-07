<?php
declare(strict_types=1);

/**
 * @property \Phalcon\Http\Request $request
 */
class SubscriptionsController extends ControllerBase
{
    public function getSubscriptionsAction(): array
    {
        /** @var SubscriptionsService $service */
        $service = $this->container->get('subscriptionsService');
        /** @var SubscriptionsGridService $gridService */
        $gridService = $this->container->get('subscriptionsGridService');

        $limit = $gridService->getLimit($this->request);
        $offset = $gridService->getOffset($this->request);
        $filter = $gridService->getFilter($this->request);

        $subscriptions = $service->getSubscriptions($limit, $offset, $filter);

        return [
            'data' => $subscriptions->toArray(),
        ];
    }

    public function syncSubscriptionsAction(): array
    {
        /** @var SubscriptionsService $service */
        $service = $this->container->get('subscriptionsService');

        $syncProcess = $service->syncSubscriptions();

        return [
            'data' => [
                'sync_process_id' => (int) $syncProcess->id,
                'status' => $syncProcess->status,
            ]
        ];
    }
}