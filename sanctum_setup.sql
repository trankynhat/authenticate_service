-- sanctum_setup.sql

-- Create personal_access_tokens table
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `tokenable_id` bigint unsigned not null,
    `tokenable_type` varchar(255) not null,
    `name` varchar(255) not null,
    `token` varchar(64) not null UNIQUE,
    `abilities` text not null,
    `last_used_at` timestamp null,
    `expires_at` timestamp null,  -- Thêm cột expires_at
    `created_at` timestamp null,
    `updated_at` timestamp null,
    FOREIGN KEY (`tokenable_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `tokenable_type_constraint` CHECK (`tokenable_type` = 'App\\Models\\User') -- Optional: Check constraint for tokenable_type
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
