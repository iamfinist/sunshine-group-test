<?php

class BatchUpsertQueryBuilder
{
    public function buildQuery(
        string $table,
        array $columns,
        array $rows,
        string $primaryKeyColumn = 'id',
    ): array
    {
        $columnList = '`' . implode('`, `', $columns) . '`';

        $placeholders = [];
        $bindings = [];

        foreach ($rows as $i => $row) {
            $rowPlaceholders = [];
            foreach ($columns as $column) {
                $param = "{$column}_{$i}";
                $rowPlaceholders[] = ":{$param}";
                $bindings[$param] = $row[$column] ?? null;
            }
            $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
        }

        $updates = [];
        foreach ($columns as $column) {
            if ($column === $primaryKeyColumn) {
                continue;
            }
            $updates[] = "`{$column}` = VALUES(`{$column}`)";
        }

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES %s ON DUPLICATE KEY UPDATE %s',
            $table,
            $columnList,
            implode(', ', $placeholders),
            implode(', ', $updates)
        );

        return [$sql, $bindings];
    }
}