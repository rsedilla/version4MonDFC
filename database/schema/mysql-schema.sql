/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cell_group_id` bigint unsigned NOT NULL,
  `attendee_id` bigint unsigned NOT NULL,
  `attendee_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  `month` tinyint unsigned NOT NULL,
  `week_number` tinyint unsigned NOT NULL,
  `present` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_records_cell_group_id_foreign` (`cell_group_id`),
  CONSTRAINT `attendance_records_cell_group_id_foreign` FOREIGN KEY (`cell_group_id`) REFERENCES `cell_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attenders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attenders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `leader_id` bigint unsigned DEFAULT NULL,
  `leader_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `suyln_lesson_1` date DEFAULT NULL,
  `suyln_lesson_2` date DEFAULT NULL,
  `suyln_lesson_3` date DEFAULT NULL,
  `suyln_lesson_4` date DEFAULT NULL,
  `suyln_lesson_5` date DEFAULT NULL,
  `suyln_lesson_6` date DEFAULT NULL,
  `suyln_lesson_7` date DEFAULT NULL,
  `suyln_lesson_8` date DEFAULT NULL,
  `suyln_lesson_9` date DEFAULT NULL,
  `suyln_lesson_10` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attenders_member_id_foreign` (`member_id`),
  CONSTRAINT `attenders_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cell_group_attendees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cell_group_attendees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cell_group_id` bigint unsigned NOT NULL,
  `attendee_id` bigint unsigned NOT NULL,
  `attendee_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cell_group_attendees_cell_group_id_foreign` (`cell_group_id`),
  CONSTRAINT `cell_group_attendees_cell_group_id_foreign` FOREIGN KEY (`cell_group_id`) REFERENCES `cell_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cell_group_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cell_group_infos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cell_group_id` bigint unsigned NOT NULL,
  `cell_group_idnum` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `day` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cell_group_infos_cell_group_idnum_unique` (`cell_group_idnum`),
  KEY `cell_group_infos_cell_group_id_foreign` (`cell_group_id`),
  CONSTRAINT `cell_group_infos_cell_group_id_foreign` FOREIGN KEY (`cell_group_id`) REFERENCES `cell_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cell_group_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cell_group_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cell_group_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cell_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cell_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cell_group_type_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leader_id` bigint unsigned NOT NULL,
  `leader_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cell_groups_cell_group_type_id_foreign` (`cell_group_type_id`),
  CONSTRAINT `cell_groups_cell_group_type_id_foreign` FOREIGN KEY (`cell_group_type_id`) REFERENCES `cell_group_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cell_leaders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cell_leaders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `leader_id` bigint unsigned DEFAULT NULL,
  `leader_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cell_leaders_member_id_foreign` (`member_id`),
  KEY `cell_leaders_user_id_foreign` (`user_id`),
  CONSTRAINT `cell_leaders_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cell_leaders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cell_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cell_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attender_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `member_id` bigint unsigned NOT NULL,
  `cell_group_id` bigint unsigned DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `leader_id` bigint unsigned DEFAULT NULL,
  `leader_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cell_members_attender_id_foreign` (`attender_id`),
  KEY `cell_members_member_id_foreign` (`member_id`),
  KEY `cell_members_cell_group_id_foreign` (`cell_group_id`),
  CONSTRAINT `cell_members_attender_id_foreign` FOREIGN KEY (`attender_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cell_members_cell_group_id_foreign` FOREIGN KEY (`cell_group_id`) REFERENCES `cell_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cell_members_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cg_attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cg_attendance_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cell_group_id` bigint unsigned NOT NULL,
  `attendee_id` bigint unsigned NOT NULL,
  `attendee_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  `month` tinyint unsigned NOT NULL,
  `week_number` tinyint unsigned NOT NULL,
  `present` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_attendance_records_cell_group_id_foreign` (`cell_group_id`),
  CONSTRAINT `cg_attendance_records_cell_group_id_foreign` FOREIGN KEY (`cell_group_id`) REFERENCES `cell_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `civil_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `civil_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `civil_statuses_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emerging_leaders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emerging_leaders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `leadership_area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `identified_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emerging_leaders_member_id_foreign` (`member_id`),
  CONSTRAINT `emerging_leaders_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `g12_leaders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `g12_leaders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `leader_id` bigint unsigned DEFAULT NULL,
  `leader_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `g12_leaders_member_id_foreign` (`member_id`),
  KEY `g12_leaders_user_id_foreign` (`user_id`),
  CONSTRAINT `g12_leaders_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `g12_leaders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `member_training_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_training_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `training_type_id` bigint unsigned NOT NULL,
  `training_status_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_member_training_type` (`member_id`,`training_type_id`),
  KEY `member_training_type_member_id_foreign` (`member_id`),
  KEY `member_training_type_training_type_id_foreign` (`training_type_id`),
  KEY `member_training_type_training_status_id_foreign` (`training_status_id`),
  CONSTRAINT `member_training_type_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_training_type_training_status_id_foreign` FOREIGN KEY (`training_status_id`) REFERENCES `training_statuses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_training_type_training_type_id_foreign` FOREIGN KEY (`training_type_id`) REFERENCES `training_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `leader_id` bigint unsigned DEFAULT NULL,
  `leader_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_leader_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `civil_status_id` bigint unsigned DEFAULT NULL,
  `sex_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `members_first_name_last_name_unique` (`first_name`,`last_name`),
  UNIQUE KEY `attenders_email_unique` (`email`),
  KEY `members_civil_status_id_foreign` (`civil_status_id`),
  KEY `members_sex_id_foreign` (`sex_id`),
  CONSTRAINT `members_civil_status_id_foreign` FOREIGN KEY (`civil_status_id`) REFERENCES `civil_statuses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `members_sex_id_foreign` FOREIGN KEY (`sex_id`) REFERENCES `sexes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `network_leaders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `network_leaders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `leader_id` bigint unsigned DEFAULT NULL,
  `leader_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `network_leaders_member_id_foreign` (`member_id`),
  KEY `network_leaders_user_id_foreign` (`user_id`),
  CONSTRAINT `network_leaders_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `network_leaders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `senior_pastors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `senior_pastors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `senior_pastors_member_id_foreign` (`member_id`),
  KEY `senior_pastors_user_id_foreign` (`user_id`),
  CONSTRAINT `senior_pastors_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `senior_pastors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sexes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sexes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sexes_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sunday_service_attendees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sunday_service_attendees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned DEFAULT NULL,
  `attendee_id` bigint unsigned NOT NULL,
  `attendee_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_date` date NOT NULL,
  `present` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `training_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `training_statuses_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `training_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `training_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_09_04_000000_create_attenders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_09_04_033003_create_cell_members_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_09_04_033552_create_training_types_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_09_04_033844_rename_attenders_to_members_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_09_04_034210_create_member_training_type_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_09_04_034336_create_cell_leaders_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_09_04_034339_create_g12_leaders_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_09_04_034343_create_network_leaders_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_09_04_034347_create_senior_pastors_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_09_04_035406_make_email_nullable_in_members_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_09_04_035852_create_civil_statuses_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_09_04_035917_add_civil_status_id_to_members_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_09_04_040212_create_sexes_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_09_04_040349_add_sex_id_to_members_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_09_04_041836_add_leader_to_members_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_09_04_043130_create_attenders_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_09_04_043441_add_leader_to_attenders_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_09_04_044601_add_user_id_to_leader_tables',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_09_04_045047_add_is_admin_to_users_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_09_04_045951_add_unique_constraint_to_members_names',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_09_04_054235_create_cell_groups_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_09_04_054615_create_cell_group_attendees_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_09_04_000000_create_attendance_records_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_09_04_000001_create_cg_attendance_records_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_09_04_000002_create_sunday_service_attendees_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_09_04_000003_create_cell_group_types_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_09_04_000004_create_cell_group_infos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_09_04_051839_create_cell_groups_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_09_04_051848_create_cell_group_attendees_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_09_04_051858_create_cell_group_types_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_09_05_073426_create_cell_members_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_09_05_141049_add_missing_columns_to_cell_members_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_09_05_143830_make_attender_id_nullable_in_cell_members',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_09_06_101913_add_leader_fields_to_cell_members_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_09_06_101931_add_leader_fields_to_cell_leaders_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_09_06_101948_add_leader_fields_to_g12_leaders_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_09_06_102003_add_leader_fields_to_network_leaders_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_09_06_103454_add_member_leader_type_to_members_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_09_08_074429_add_columns_to_cell_group_infos_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_09_08_080257_add_status_to_member_training_type_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_09_08_082715_create_training_statuses_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_09_08_082752_update_member_training_type_add_status_id',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_09_08_092936_remove_duplicate_member_training_assignments',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_09_08_083318_fix_member_training_type_status_migration',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_09_08_092815_add_unique_constraint_to_member_training_type_table',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_09_09_000001_create_emerging_leaders_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_09_09_081624_add_suyln_lessons_to_attenders_table',35);
