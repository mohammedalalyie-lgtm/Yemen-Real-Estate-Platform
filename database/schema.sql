-- عقار اليمن (Yemen Real Estate Platform)
-- Database Schema
-- Character Set: utf8mb4, Engine: InnoDB

CREATE DATABASE IF NOT EXISTS `yemen_real_estate` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `yemen_real_estate`;

SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if re-installing
DROP TABLE IF EXISTS `chatbot_messages`;
DROP TABLE IF EXISTS `chatbot_conversations`;
DROP TABLE IF EXISTS `subscriptions`;
DROP TABLE IF EXISTS `subscription_plans`;
DROP TABLE IF EXISTS `verification_requests`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `property_status_history`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `office_members`;
DROP TABLE IF EXISTS `inquiries`;
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `property_features`;
DROP TABLE IF EXISTS `features`;
DROP TABLE IF EXISTS `property_images`;
DROP TABLE IF EXISTS `properties`;
DROP TABLE IF EXISTS `property_categories`;
DROP TABLE IF EXISTS `areas`;
DROP TABLE IF EXISTS `districts`;
DROP TABLE IF EXISTS `governorates`;
DROP TABLE IF EXISTS `offices`;
DROP TABLE IF EXISTS `user_roles`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `users`;

-- 1. Users table
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(30) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('super_admin', 'admin', 'office', 'broker', 'owner', 'customer') NOT NULL DEFAULT 'customer',
    `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `avatar` VARCHAR(255) NULL,
    `bio` TEXT NULL,
    `whatsapp` VARCHAR(30) NULL,
    `license_number` VARCHAR(100) NULL,
    `office_id` INT UNSIGNED NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Roles & Permissions (RBAC)
CREATE TABLE `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Geographic locations: Governorates -> Districts -> Areas
CREATE TABLE `governorates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_ar` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100) NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `image` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `districts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `governorate_id` INT UNSIGNED NOT NULL,
    `name_ar` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100) NULL,
    `slug` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`governorate_id`) REFERENCES `governorates`(`id`) ON DELETE CASCADE,
    INDEX `idx_district_gov` (`governorate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `areas` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `district_id` INT UNSIGNED NOT NULL,
    `name_ar` VARCHAR(150) NOT NULL,
    `name_en` VARCHAR(150) NULL,
    `slug` VARCHAR(150) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`district_id`) REFERENCES `districts`(`id`) ON DELETE CASCADE,
    INDEX `idx_area_district` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Property Categories
CREATE TABLE `property_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_ar` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100) NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `type_group` ENUM('residential', 'commercial', 'land') NOT NULL DEFAULT 'residential',
    `icon` VARCHAR(50) NULL DEFAULT 'home',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Real Estate Offices
CREATE TABLE `offices` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `owner_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `license_number` VARCHAR(100) NULL,
    `logo` VARCHAR(255) NULL,
    `cover_image` VARCHAR(255) NULL,
    `phone` VARCHAR(50) NOT NULL,
    `email` VARCHAR(150) NULL,
    `whatsapp` VARCHAR(50) NULL,
    `governorate_id` INT UNSIGNED NULL,
    `district_id` INT UNSIGNED NULL,
    `address` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`governorate_id`) REFERENCES `governorates`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`district_id`) REFERENCES `districts`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Update users FK for office_id
ALTER TABLE `users` ADD CONSTRAINT `fk_users_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE SET NULL;

-- 6. Office Members (Brokers linked to an Office)
CREATE TABLE `office_members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `office_id` INT UNSIGNED NOT NULL,
    `broker_id` INT UNSIGNED NOT NULL,
    `role_title` VARCHAR(100) DEFAULT 'وسيط عقاري',
    `status` ENUM('pending', 'active', 'rejected', 'removed') NOT NULL DEFAULT 'pending',
    `invited_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`broker_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_office_broker` (`office_id`, `broker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Properties Table
CREATE TABLE `properties` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `office_id` INT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` MEDIUMTEXT NOT NULL,
    `purpose` ENUM('sale', 'rent') NOT NULL DEFAULT 'sale',
    `category_id` INT UNSIGNED NOT NULL,
    `governorate_id` INT UNSIGNED NOT NULL,
    `district_id` INT UNSIGNED NOT NULL,
    `area_id` INT UNSIGNED NULL,
    `address_details` VARCHAR(255) NULL,
    `price` DECIMAL(14, 2) NOT NULL,
    `currency` ENUM('YER', 'USD', 'SAR') NOT NULL DEFAULT 'YER',
    `price_type` ENUM('total', 'per_month', 'per_year', 'per_labna', 'per_meter') NOT NULL DEFAULT 'total',
    `is_negotiable` TINYINT(1) NOT NULL DEFAULT 1,
    `area_size` DECIMAL(10, 2) NOT NULL,
    `area_unit` ENUM('sqm', 'labna', 'qasaba') NOT NULL DEFAULT 'sqm',
    `bedrooms` SMALLINT UNSIGNED NULL DEFAULT 0,
    `bathrooms` SMALLINT UNSIGNED NULL DEFAULT 0,
    `living_rooms` SMALLINT UNSIGNED NULL DEFAULT 0,
    `floors` SMALLINT UNSIGNED NULL DEFAULT 1,
    `floor_number` SMALLINT NULL DEFAULT 0,
    `building_year` SMALLINT UNSIGNED NULL,
    `direction` VARCHAR(50) NULL,
    `latitude` DECIMAL(10, 8) NULL,
    `longitude` DECIMAL(11, 8) NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'revision_required', 'reserved', 'sold', 'rented', 'suspended', 'archived') NOT NULL DEFAULT 'pending',
    `rejection_reason` TEXT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `inquiries_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `last_confirmed_at` TIMESTAMP NULL,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `property_categories`(`id`),
    FOREIGN KEY (`governorate_id`) REFERENCES `governorates`(`id`),
    FOREIGN KEY (`district_id`) REFERENCES `districts`(`id`),
    FOREIGN KEY (`area_id`) REFERENCES `areas`(`id`) ON DELETE SET NULL,
    INDEX `idx_prop_search` (`purpose`, `governorate_id`, `district_id`, `category_id`, `status`),
    INDEX `idx_prop_price` (`price`, `currency`),
    INDEX `idx_prop_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Property Images
CREATE TABLE `property_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    INDEX `idx_img_prop` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Features (Amenities)
CREATE TABLE `features` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_ar` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100) NULL,
    `icon` VARCHAR(50) DEFAULT 'check-circle',
    `category` VARCHAR(50) DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `property_features` (
    `property_id` INT UNSIGNED NOT NULL,
    `feature_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`property_id`, `feature_id`),
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`feature_id`) REFERENCES `features`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Favorites
CREATE TABLE `favorites` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `property_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_user_property_favorite` (`user_id`, `property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Inquiries & Messages
CREATE TABLE `inquiries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT UNSIGNED NOT NULL,
    `sender_id` INT UNSIGNED NULL,
    `sender_name` VARCHAR(150) NOT NULL,
    `sender_phone` VARCHAR(50) NOT NULL,
    `sender_email` VARCHAR(150) NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `reply` TEXT NULL,
    `replied_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_inq_property` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Notifications
CREATE TABLE `notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `link` VARCHAR(255) NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `type` VARCHAR(50) NOT NULL DEFAULT 'info',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_notif_user` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Reports (Anti-fraud & moderation)
CREATE TABLE `reports` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT UNSIGNED NOT NULL,
    `reporter_id` INT UNSIGNED NULL,
    `reporter_name` VARCHAR(150) NULL,
    `reporter_phone` VARCHAR(50) NULL,
    `reason` ENUM('incorrect_info', 'misleading_price', 'fake_images', 'duplicate_ad', 'invalid_phone', 'suspicious_ad', 'other') NOT NULL,
    `details` TEXT NULL,
    `status` ENUM('pending', 'reviewed', 'dismissed', 'action_taken') NOT NULL DEFAULT 'pending',
    `admin_note` TEXT NULL,
    `resolved_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reporter_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`resolved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Property Status History (Audit trail)
CREATE TABLE `property_status_history` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `previous_status` VARCHAR(50) NOT NULL,
    `new_status` VARCHAR(50) NOT NULL,
    `reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Activity Logs (Admin & user audit)
CREATE TABLE `activity_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `details` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_log_user` (`user_id`),
    INDEX `idx_log_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Verification Requests
CREATE TABLE `verification_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `id_card_image` VARCHAR(255) NULL,
    `commercial_record_image` VARCHAR(255) NULL,
    `license_number` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `admin_notes` TEXT NULL,
    `reviewed_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Subscription Plans & Subscriptions
CREATE TABLE `subscription_plans` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `currency` ENUM('YER', 'USD', 'SAR') NOT NULL DEFAULT 'YER',
    `duration_days` INT UNSIGNED NOT NULL DEFAULT 30,
    `max_listings` INT NOT NULL DEFAULT 5,
    `max_featured` INT NOT NULL DEFAULT 0,
    `allow_brokers` TINYINT(1) NOT NULL DEFAULT 0,
    `features_json` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `subscriptions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `plan_id` INT UNSIGNED NOT NULL,
    `starts_at` DATETIME NOT NULL,
    `ends_at` DATETIME NOT NULL,
    `status` ENUM('active', 'expired', 'canceled') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. Smart Assistant Chatbot Conversations & Messages
CREATE TABLE `chatbot_conversations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(100) NOT NULL,
    `user_id` INT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chatbot_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT UNSIGNED NOT NULL,
    `sender` ENUM('user', 'bot') NOT NULL,
    `message` TEXT NOT NULL,
    `matched_count` INT NOT NULL DEFAULT 0,
    `payload_json` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `chatbot_conversations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Site Settings
CREATE TABLE `site_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NULL,
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
