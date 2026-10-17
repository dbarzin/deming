/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `action_control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_control` (
  `action_id` int(10) unsigned NOT NULL,
  `control_id` int(10) unsigned NOT NULL,
  KEY `action_measure_action_id_foreign` (`action_id`),
  KEY `action_measure_measure_id_foreign` (`control_id`),
  CONSTRAINT `action_measure_action_id_foreign` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`),
  CONSTRAINT `action_measure_measure_id_foreign` FOREIGN KEY (`control_id`) REFERENCES `controls` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `action_risk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_risk` (
  `risk_id` int(10) unsigned NOT NULL,
  `action_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`risk_id`,`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `action_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_user` (
  `action_id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  KEY `action_user_action_id_foreign` (`action_id`),
  KEY `action_user_user_id_foreign` (`user_id`),
  CONSTRAINT `action_user_action_id_foreign` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`),
  CONSTRAINT `action_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(32) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `progress` int(11) DEFAULT NULL,
  `criticity` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `scope` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `cause` text DEFAULT NULL,
  `remediation` text DEFAULT NULL,
  `measure_id` int(10) unsigned DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `close_date` date DEFAULT NULL,
  `justification` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actions_control_id_foreign` (`measure_id`),
  CONSTRAINT `actions_control_id_foreign` FOREIGN KEY (`measure_id`) REFERENCES `measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attributes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `values` varchar(4096) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `subject_id` int(10) unsigned DEFAULT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `properties` text DEFAULT NULL,
  `host` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `control_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `control_mappings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `source_control_id` int(10) unsigned NOT NULL,
  `target_control_id` int(10) unsigned NOT NULL,
  `mapping_type` enum('equivalent','covers','covered_by','partial','supports','related') NOT NULL,
  `coverage` enum('full','high','medium','low','none') DEFAULT NULL,
  `confidence` decimal(5,2) DEFAULT NULL,
  `rationale` text DEFAULT NULL,
  `source_reference` varchar(255) DEFAULT NULL,
  `source_url` varchar(2048) DEFAULT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT 0,
  `validated_by` bigint(20) unsigned DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `control_mappings_source_target_unique` (`source_control_id`,`target_control_id`),
  KEY `control_mappings_validated_by_foreign` (`validated_by`),
  KEY `control_mappings_target_control_index` (`target_control_id`),
  KEY `control_mappings_mapping_type_index` (`mapping_type`),
  KEY `control_mappings_coverage_index` (`coverage`),
  CONSTRAINT `control_mappings_source_control_id_foreign` FOREIGN KEY (`source_control_id`) REFERENCES `controls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `control_mappings_target_control_id_foreign` FOREIGN KEY (`target_control_id`) REFERENCES `controls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `control_mappings_validated_by_foreign` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `control_measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `control_measure` (
  `control_id` int(10) unsigned NOT NULL,
  `measure_id` int(10) unsigned NOT NULL,
  KEY `control_measure_control_id_foreign` (`control_id`),
  KEY `control_measure_measure_id_foreign` (`measure_id`),
  CONSTRAINT `control_measure_control_id_foreign` FOREIGN KEY (`control_id`) REFERENCES `controls` (`id`),
  CONSTRAINT `control_measure_measure_id_foreign` FOREIGN KEY (`measure_id`) REFERENCES `measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `control_risk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `control_risk` (
  `control_id` int(10) unsigned NOT NULL,
  `risk_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`control_id`,`risk_id`),
  KEY `control_risk_risk_id_foreign` (`risk_id`),
  CONSTRAINT `control_risk_control_id_foreign` FOREIGN KEY (`control_id`) REFERENCES `controls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `control_risk_risk_id_foreign` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `controls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `controls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `clause` varchar(255) NOT NULL,
  `objective` text DEFAULT NULL,
  `input` text DEFAULT NULL,
  `model` text DEFAULT NULL,
  `indicator` text DEFAULT NULL,
  `action_plan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attributes` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `measures_clause_unique` (`clause`),
  KEY `measures_domain_id_foreign` (`domain_id`),
  CONSTRAINT `measures_domain_id_foreign` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `measure_id` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mimetype` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_control_id_foreign` (`measure_id`),
  CONSTRAINT `documents_control_id_foreign` FOREIGN KEY (`measure_id`) REFERENCES `measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `framework` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `exceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exceptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `control_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `justification` text DEFAULT NULL,
  `compensating_controls` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `submitted_by` bigint(20) unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exceptions_measure_id_foreign` (`control_id`),
  KEY `exceptions_created_by_foreign` (`created_by`),
  KEY `exceptions_submitted_by_foreign` (`submitted_by`),
  KEY `exceptions_approved_by_foreign` (`approved_by`),
  CONSTRAINT `exceptions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `exceptions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `exceptions_measure_id_foreign` FOREIGN KEY (`control_id`) REFERENCES `controls` (`id`) ON DELETE SET NULL,
  CONSTRAINT `exceptions_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `frameworks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `frameworks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL DEFAULT '',
  `publisher` varchar(255) NOT NULL DEFAULT '',
  `jurisdiction` varchar(255) DEFAULT NULL,
  `source_url` varchar(2048) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `publication_date` date DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `frameworks_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `measure_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `measure_user` (
  `measure_id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  KEY `control_id_fk_5920381` (`measure_id`),
  KEY `user_id_fk_5837573` (`user_id`),
  CONSTRAINT `control_id_fk_49294573` FOREIGN KEY (`measure_id`) REFERENCES `measures` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `user_id_fk_304958543` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `measure_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `measure_user_group` (
  `measure_id` int(10) unsigned NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  KEY `control_user_group_control_id_foreign` (`measure_id`),
  KEY `control_user_group_user_group_id_foreign` (`user_group_id`),
  CONSTRAINT `control_user_group_control_id_foreign` FOREIGN KEY (`measure_id`) REFERENCES `measures` (`id`),
  CONSTRAINT `control_user_group_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `measures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `measures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `objective` text DEFAULT NULL,
  `input` text DEFAULT NULL,
  `model` text DEFAULT NULL,
  `indicator` text DEFAULT NULL,
  `action_plan` longtext DEFAULT NULL,
  `periodicity` int(11) DEFAULT NULL,
  `plan_date` date NOT NULL,
  `realisation_date` date DEFAULT NULL,
  `observations` longtext DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `note` decimal(9,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `next_id` int(10) unsigned DEFAULT NULL,
  `attributes` varchar(1024) DEFAULT NULL,
  `scope` varchar(32) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_controls_next_id` (`next_id`),
  CONSTRAINT `fk_controls_next_id` FOREIGN KEY (`next_id`) REFERENCES `measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_device_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_device_codes` (
  `id` char(80) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` char(36) NOT NULL,
  `user_code` char(8) NOT NULL,
  `scopes` text NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `user_approved_at` datetime DEFAULT NULL,
  `last_polled_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_device_codes_user_code_unique` (`user_code`),
  KEY `oauth_device_codes_user_id_index` (`user_id`),
  KEY `oauth_device_codes_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `risk_scoring_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `risk_scoring_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `formula` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `probability_levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`probability_levels`)),
  `impact_levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`impact_levels`)),
  `exposure_levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`exposure_levels`)),
  `vulnerability_levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vulnerability_levels`)),
  `risk_thresholds` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`risk_thresholds`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_scoring_configs_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `risks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `risks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `owner_id` int(10) unsigned DEFAULT NULL,
  `probability` tinyint(4) NOT NULL DEFAULT 1,
  `probability_comment` text DEFAULT NULL,
  `impact` tinyint(4) NOT NULL DEFAULT 1,
  `impact_comment` text DEFAULT NULL,
  `status` enum('not_evaluated','not_accepted','temporarily_accepted','accepted','mitigated','transferred','avoided') NOT NULL DEFAULT 'not_evaluated',
  `status_comment` text DEFAULT NULL,
  `review_frequency` smallint(5) unsigned NOT NULL DEFAULT 12,
  `next_review_at` date DEFAULT NULL,
  `exposure` tinyint(4) DEFAULT NULL COMMENT 'v2 - BSI 200-3',
  `vulnerability` tinyint(4) DEFAULT NULL COMMENT 'v2 - BSI 200-3',
  `residual_risk` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `risks_status_index` (`status`),
  KEY `risks_owner_id_index` (`owner_id`),
  KEY `risks_next_review_at_index` (`next_review_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'business',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_user_group` (
  `user_id` bigint(20) unsigned NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  KEY `user_user_group_user_id_foreign` (`user_id`),
  KEY `user_user_group_user_group_id_foreign` (`user_group_id`),
  CONSTRAINT `user_user_group_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`),
  CONSTRAINT `user_user_group_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `profile_image` int(11) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_unique` (`login`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*M!999999\- enable the sandbox mode */ 
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2019_07_28_175941_create_domains_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2019_08_09_084322_create_measures_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2019_08_09_105245_create_controls_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2020_04_12_073028_create_documents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2022_04_23_081110_add_next_control_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2022_05_15_030940_control_score_to_int',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2022_12_21_113730_add_user_language',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2023_01_29_114100_add_tags',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2023_01_30_180336_normalization',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2023_03_09_222639_alter_attributes_values',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2023_04_06_202034_alter_attribute_length',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2023_04_19_112145_change_clause_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2023_06_18_170340_owner',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2023_08_22_095642_add_scope',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2024_04_15_193546_attributes_values_text',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2024_04_20_192325_add_control_status',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2024_06_27_123923_add_control_measure_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2024_07_02_101657_add_framework_to_domains',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2024_07_05_174735_clause_unique',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2024_10_01_181052_remove_clause',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2016_06_01_000001_create_oauth_auth_codes_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2016_06_01_000002_create_oauth_access_tokens_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2016_06_01_000003_create_oauth_refresh_tokens_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2016_06_01_000004_create_oauth_clients_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2016_06_01_000005_create_oauth_personal_access_clients_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2024_11_06_123808_add_actions',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2025_02_04_064646_create_audit_logs_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2025_02_05_121035_cleanup',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2025_04_29_123908_add_user_group',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2025_05_27_152856_change_actions',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2025_07_31_090259_alter_note_on_controls_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2024_06_01_000001_create_oauth_device_codes_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2026_04_07_151247_create_risk_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2026_04_07_152854_create_risk_scoring_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2026_04_16_081633_change_note_precision_in_controls_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_04_23_160957_create_exceptions_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_05_21_000001_swap_measures_controls_tables',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_05_21_000002_fix_control_measure_foreign_keys',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_06_09_000001_replace_measure_risk_with_control_risk',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_06_11_000001_add_monarc_scoring_config',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_06_30_000001_rename_control_user_to_measure_user',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_07_29_000001_rename_action_measure_to_action_control',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_07_29_091936_add_residual_risk_to_risks_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2026_09_16_000001_create_frameworks_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2026_09_16_000002_create_control_mappings_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2026_10_16_000001_drop_standard_from_controls_and_measures',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2026_10_17_000001_add_type_to_user_groups',15);
