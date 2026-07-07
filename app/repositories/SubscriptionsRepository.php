<?php
declare(strict_types=1);

use Phalcon\Contracts\Db\Adapter\Adapter;
use Phalcon\Mvc\Model\ManagerInterface;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

readonly class SubscriptionsRepository
{
    private const array COLUMNS = [
        'id',
        'customer_id',
        'status',
        'created',
        'start_date',
        'cancel_at',
        'canceled_at',
        'ended_at',
    ];

    /**
     * @var class-string<Subscriptions>
     */
    private const string MODEL_CLASS = Subscriptions::class;

    public function __construct(
        private Adapter $db,
        private ManagerInterface $modelsManager,
        private BatchUpsertQueryBuilder $upsertBuilder
    ) {}

    public function getSubscriptions(int $limit, int $offset, array $filters = []): Resultset
    {
        $builder = $this->modelsManager->createBuilder()->from(self::MODEL_CLASS);

        $bind = [];
        foreach ($filters as $field => $value) {
            $builder->andWhere("[$field] = :$field:");
            $bind[$field] = $value;
        }

        $builder->orderBy('[created] DESC');
        $builder->limit($limit, $offset);

        return $builder->getQuery()->execute($bind);
    }

    public function upsertSubscriptions(array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $modelClass = self::MODEL_CLASS;
        $table = new $modelClass()->getSource();

        [$sql, $bindings] = $this->upsertBuilder->buildQuery($table, self::COLUMNS, $rows);

        $this->db->execute($sql, $bindings);
    }
}