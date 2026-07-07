CREATE TABLE `subscriptions` (
     `id` varchar(64) NOT NULL,
     `customer_id` varchar(64) NOT NULL,
     `status` varchar(32) NOT NULL,
     `created` timestamp NOT NULL,
     `start_date` timestamp NULL DEFAULT NULL,
     `cancel_at` timestamp NULL DEFAULT NULL,
     `canceled_at` timestamp NULL DEFAULT NULL,
     `ended_at` timestamp NULL DEFAULT NULL,
     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;