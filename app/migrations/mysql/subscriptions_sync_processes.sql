CREATE TABLE `subscriptions_sync_processes` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `status` varchar(32) NOT NULL DEFAULT 'created',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;