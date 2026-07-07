<?php

use Phalcon\Http\Request;

class SubscriptionsGridService implements GridServiceInterface
{
    private const int DEFAULT_LIMIT = 100;

    public function getFilter(Request $request): array
    {
        $filter = [
            'id' => $request->getQuery('id', 'string'),
            'customer_id' => $request->getQuery('customer_id', 'string'),
            'status' => $request->getQuery('status', 'string'),
        ];

        return array_filter($filter, static fn ($value): bool => $value !== null && $value !== '');
    }

    public function getLimit(Request $request): int
    {
        $limit = $request->getQuery('limit', 'int');
        $limit = min($limit ?? self::DEFAULT_LIMIT, self::DEFAULT_LIMIT);

        return max(0, $limit);
    }

    public function getOffset(Request $request): int
    {
        $offset = $request->getQuery('offset', 'int');

        return max(0, $offset ?? 0);
    }
}