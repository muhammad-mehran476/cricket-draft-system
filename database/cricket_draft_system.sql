-- ============================================================
-- Cricket Draft Ceremony Management System (CDCMS)
-- Database: cricket_draft_system
-- Compatible: MySQL 8.0+ / phpMyAdmin
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `cricket_draft_system`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `cricket_draft_system`;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(191) NOT NULL,
  `role` ENUM('admin','player','team_captain') NOT NULL DEFAULT 'player',
  `status` ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_role_index` (`role`),
  KEY `users_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: categories
-- ============================================================
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `slug` VARCHAR(191) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `max_players` INT NOT NULL DEFAULT 10,
  `draft_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: players
-- ============================================================
CREATE TABLE `players` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `profile_picture` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('batsman','bowler','all_rounder','wicket_keeper') NOT NULL,
  `skill_level` ENUM('good','better','best') NOT NULL DEFAULT 'good',
  `bowling_type` ENUM('fast','medium','spin','none') NOT NULL DEFAULT 'none',
  `batting_style` ENUM('right_hand','left_hand') NOT NULL DEFAULT 'right_hand',
  `category_id` BIGINT UNSIGNED DEFAULT NULL,
  `payment_slip` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('pending','approved','rejected','drafted') NOT NULL DEFAULT 'pending',
  `rejection_reason` TEXT DEFAULT NULL,
  `team_id` BIGINT UNSIGNED DEFAULT NULL,
  `rules_accepted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `players_user_id_foreign` (`user_id`),
  KEY `players_category_id_foreign` (`category_id`),
  KEY `players_team_id_foreign` (`team_id`),
  KEY `players_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: teams
-- ============================================================
CREATE TABLE `teams` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `team_name` VARCHAR(191) NOT NULL,
  `captain_name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `team_logo` VARCHAR(255) DEFAULT NULL,
  `captain_image` VARCHAR(255) DEFAULT NULL,
  `team_banner` VARCHAR(255) DEFAULT NULL,
  `payment_slip` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` TEXT DEFAULT NULL,
  `draft_order` INT DEFAULT NULL,
  `total_players_drafted` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_user_id_foreign` (`user_id`),
  KEY `teams_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: draft_sessions
-- ============================================================
CREATE TABLE `draft_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(191) NOT NULL,
  `status` ENUM('pending','active','paused','completed') NOT NULL DEFAULT 'pending',
  `current_category_id` BIGINT UNSIGNED DEFAULT NULL,
  `current_round` INT NOT NULL DEFAULT 1,
  `current_team_turn_id` BIGINT UNSIGNED DEFAULT NULL,
  `timer_seconds` INT NOT NULL DEFAULT 300,
  `timer_started_at` TIMESTAMP NULL DEFAULT NULL,
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `draft_sessions_current_category_id_foreign` (`current_category_id`),
  KEY `draft_sessions_current_team_turn_id_foreign` (`current_team_turn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: draft_rounds
-- ============================================================
CREATE TABLE `draft_rounds` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `draft_session_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `round_number` INT NOT NULL,
  `team_order` JSON NOT NULL COMMENT 'Array of team IDs in pick order',
  `status` ENUM('pending','active','completed') NOT NULL DEFAULT 'pending',
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `draft_rounds_draft_session_id_foreign` (`draft_session_id`),
  KEY `draft_rounds_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: draft_picks
-- ============================================================
CREATE TABLE `draft_picks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `draft_session_id` BIGINT UNSIGNED NOT NULL,
  `draft_round_id` BIGINT UNSIGNED NOT NULL,
  `team_id` BIGINT UNSIGNED NOT NULL,
  `player_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `pick_number` INT NOT NULL,
  `time_taken_seconds` INT DEFAULT NULL,
  `is_auto_pick` TINYINT(1) NOT NULL DEFAULT 0,
  `picked_at` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `draft_picks_player_id_unique` (`player_id`),
  KEY `draft_picks_team_id_foreign` (`team_id`),
  KEY `draft_picks_draft_session_id_foreign` (`draft_session_id`),
  KEY `draft_picks_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: draft_queue (team turn order per round)
-- ============================================================
CREATE TABLE `draft_queue` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `draft_session_id` BIGINT UNSIGNED NOT NULL,
  `draft_round_id` BIGINT UNSIGNED NOT NULL,
  `team_id` BIGINT UNSIGNED NOT NULL,
  `pick_position` INT NOT NULL,
  `status` ENUM('waiting','active','done','skipped') NOT NULL DEFAULT 'waiting',
  `timer_expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `draft_queue_draft_session_id_foreign` (`draft_session_id`),
  KEY `draft_queue_team_id_foreign` (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: matches
-- ============================================================
CREATE TABLE `matches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `home_team_id` BIGINT UNSIGNED NOT NULL,
  `away_team_id` BIGINT UNSIGNED DEFAULT NULL,
  `opponent_name` VARCHAR(191) DEFAULT NULL,
  `match_date` DATE NOT NULL,
  `venue` VARCHAR(191) DEFAULT NULL,
  `match_type` ENUM('league','knockout','friendly','final') NOT NULL DEFAULT 'league',
  `home_score` INT DEFAULT NULL,
  `away_score` INT DEFAULT NULL,
  `result` ENUM('win','loss','draw','no_result') DEFAULT NULL,
  `home_runs` INT DEFAULT NULL,
  `home_wickets` INT DEFAULT NULL,
  `home_overs` DECIMAL(4,1) DEFAULT NULL,
  `away_runs` INT DEFAULT NULL,
  `away_wickets` INT DEFAULT NULL,
  `away_overs` DECIMAL(4,1) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `matches_home_team_id_foreign` (`home_team_id`),
  KEY `matches_away_team_id_foreign` (`away_team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: player_stats
-- ============================================================
CREATE TABLE `player_stats` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `player_id` BIGINT UNSIGNED NOT NULL,
  `match_id` BIGINT UNSIGNED DEFAULT NULL,
  `runs_scored` INT NOT NULL DEFAULT 0,
  `balls_faced` INT NOT NULL DEFAULT 0,
  `fours` INT NOT NULL DEFAULT 0,
  `sixes` INT NOT NULL DEFAULT 0,
  `wickets_taken` INT NOT NULL DEFAULT 0,
  `overs_bowled` DECIMAL(4,1) NOT NULL DEFAULT 0.0,
  `runs_conceded` INT NOT NULL DEFAULT 0,
  `catches` INT NOT NULL DEFAULT 0,
  `run_outs` INT NOT NULL DEFAULT 0,
  `stumpings` INT NOT NULL DEFAULT 0,
  `is_not_out` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `player_stats_player_id_foreign` (`player_id`),
  KEY `player_stats_match_id_foreign` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: audit_logs
-- ============================================================
CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(191) NOT NULL,
  `model_type` VARCHAR(191) DEFAULT NULL,
  `model_id` BIGINT UNSIGNED DEFAULT NULL,
  `old_values` JSON DEFAULT NULL,
  `new_values` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: notifications
-- ============================================================
CREATE TABLE `notifications` (
  `id` CHAR(36) NOT NULL,
  `type` VARCHAR(191) NOT NULL,
  `notifiable_type` VARCHAR(191) NOT NULL,
  `notifiable_id` BIGINT UNSIGNED NOT NULL,
  `data` TEXT NOT NULL,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: password_reset_tokens
-- ============================================================
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(191) NOT NULL,
  `token` VARCHAR(191) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: sessions
-- ============================================================
CREATE TABLE `sessions` (
  `id` VARCHAR(191) NOT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: cache
-- ============================================================
CREATE TABLE `cache` (
  `key` VARCHAR(191) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: jobs
-- ============================================================
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(191) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FOREIGN KEY CONSTRAINTS
-- ============================================================
ALTER TABLE `players`
  ADD CONSTRAINT `players_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `players_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `players_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

ALTER TABLE `teams`
  ADD CONSTRAINT `teams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `draft_sessions`
  ADD CONSTRAINT `draft_sessions_current_category_id_foreign` FOREIGN KEY (`current_category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `draft_sessions_current_team_turn_id_foreign` FOREIGN KEY (`current_team_turn_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `draft_sessions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `draft_rounds`
  ADD CONSTRAINT `draft_rounds_draft_session_id_foreign` FOREIGN KEY (`draft_session_id`) REFERENCES `draft_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `draft_rounds_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

ALTER TABLE `draft_picks`
  ADD CONSTRAINT `draft_picks_draft_session_id_foreign` FOREIGN KEY (`draft_session_id`) REFERENCES `draft_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `draft_picks_draft_round_id_foreign` FOREIGN KEY (`draft_round_id`) REFERENCES `draft_rounds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `draft_picks_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`),
  ADD CONSTRAINT `draft_picks_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`),
  ADD CONSTRAINT `draft_picks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

ALTER TABLE `draft_queue`
  ADD CONSTRAINT `draft_queue_draft_session_id_foreign` FOREIGN KEY (`draft_session_id`) REFERENCES `draft_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `draft_queue_draft_round_id_foreign` FOREIGN KEY (`draft_round_id`) REFERENCES `draft_rounds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `draft_queue_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);

ALTER TABLE `matches`
  ADD CONSTRAINT `matches_home_team_id_foreign` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_away_team_id_foreign` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `player_stats`
  ADD CONSTRAINT `player_stats_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `player_stats_match_id_foreign` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE SET NULL;

ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- ============================================================
-- SEED DATA: Admin User
-- password: Admin@123 (bcrypt)
-- ============================================================
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
('Super Admin', 'admin@cdcms.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW(), NOW());

-- ============================================================
-- SEED DATA: Categories
-- ============================================================
INSERT INTO `categories` (`name`, `slug`, `description`, `max_players`, `draft_order`, `is_active`, `created_at`, `updated_at`) VALUES
('Iconic Players', 'iconic-players', 'Top-tier legendary players', 5, 1, 1, NOW(), NOW()),
('Platinum Players', 'platinum-players', 'Elite players of the tournament', 8, 2, 1, NOW(), NOW()),
('Gold Batsmen', 'gold-batsmen', 'Skilled specialist batsmen', 10, 3, 1, NOW(), NOW()),
('Gold Bowlers', 'gold-bowlers', 'Skilled specialist bowlers', 10, 4, 1, NOW(), NOW()),
('All Rounders', 'all-rounders', 'Versatile players excelling in both', 8, 5, 1, NOW(), NOW()),
('Emerging Players', 'emerging-players', 'Promising young talent', 12, 6, 1, NOW(), NOW());

COMMIT;
