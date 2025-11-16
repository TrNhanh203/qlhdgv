-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Phiên bản:           12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for qlhd
DROP DATABASE IF EXISTS `qlhd`;
CREATE DATABASE IF NOT EXISTS `qlhd` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `qlhd`;

-- Dumping structure for table qlhd.academic_years
DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE IF NOT EXISTS `academic_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `university_id` bigint unsigned NOT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_years_year_code_university_id_unique` (`year_code`,`university_id`),
  KEY `academic_years_university_id_foreign` (`university_id`),
  CONSTRAINT `academic_years_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.attachments
DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_type_id` bigint unsigned NOT NULL,
  `entity_id` bigint unsigned NOT NULL,
  `file_name` varchar(260) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `attachments_uploaded_by_foreign` (`uploaded_by`),
  KEY `attachments_module_type_id_entity_id_index` (`module_type_id`,`entity_id`),
  CONSTRAINT `attachments_module_type_id_foreign` FOREIGN KEY (`module_type_id`) REFERENCES `module_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.audit_logs
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `table_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` bigint unsigned DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `changes_json` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_logged_at_index` (`logged_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.courses
DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned NOT NULL,
  `course_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courses_department_fk` (`department_id`),
  CONSTRAINT `courses_department_fk` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.courses_bak_before_minimalize
DROP TABLE IF EXISTS `courses_bak_before_minimalize`;
CREATE TABLE IF NOT EXISTS `courses_bak_before_minimalize` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned NOT NULL,
  `education_program_id` bigint unsigned DEFAULT NULL,
  `course_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit` int DEFAULT '0',
  `has_outline` tinyint(1) DEFAULT '0',
  `outline_status` enum('none','draft','review','approved','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `last_outline_version_id` bigint unsigned DEFAULT NULL,
  `semester_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courses_department_fk` (`department_id`),
  KEY `courses_program_fk` (`education_program_id`),
  KEY `courses_outline_fk` (`last_outline_version_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.departments
DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `faculty_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `outline_manager_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `departments_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.education_programs
DROP TABLE IF EXISTS `education_programs`;
CREATE TABLE IF NOT EXISTS `education_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `faculty_id` bigint unsigned NOT NULL,
  `education_system_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `education_system_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `current_version_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `education_programs_program_code_unique` (`program_code`),
  KEY `education_programs_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `education_programs_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.exams
DROP TABLE IF EXISTS `exams`;
CREATE TABLE IF NOT EXISTS `exams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `semester_id` bigint unsigned NOT NULL,
  `exam_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exam_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exam_batch` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exam_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `exam_end` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `exam_form` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_id` bigint unsigned DEFAULT NULL,
  `expected_students` int DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `status_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exams_course_id_foreign` (`course_id`),
  KEY `exams_semester_id_foreign` (`semester_id`),
  KEY `exams_exam_start_index` (`exam_start`),
  KEY `exams_academic_year_id_semester_id_index` (`academic_year_id`,`semester_id`),
  KEY `exams_room_id_index` (`room_id`),
  KEY `exams_status_id_index` (`status_id`),
  CONSTRAINT `exams_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `exams_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status_codes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.exam_lectures
DROP TABLE IF EXISTS `exam_lectures`;
CREATE TABLE IF NOT EXISTS `exam_lectures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_proctoring_id` bigint unsigned NOT NULL,
  `lecture_id` bigint unsigned NOT NULL,
  `assignment_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_lectures_unique` (`exam_proctoring_id`,`lecture_id`,`assignment_type`),
  KEY `exam_lectures_lecture_id_foreign` (`lecture_id`),
  CONSTRAINT `exam_lectures_exam_proctoring_id_foreign` FOREIGN KEY (`exam_proctoring_id`) REFERENCES `exam_proctorings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_lectures_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.exam_proctorings
DROP TABLE IF EXISTS `exam_proctorings`;
CREATE TABLE IF NOT EXISTS `exam_proctorings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` bigint unsigned NOT NULL,
  `lecture_id` bigint unsigned NOT NULL,
  `assignment_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proctor_order` int DEFAULT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_proctorings_lecture_id_foreign` (`lecture_id`),
  KEY `exam_proctorings_status_id_foreign` (`status_id`),
  KEY `exam_proctorings_exam_id_index` (`exam_id`),
  CONSTRAINT `exam_proctorings_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_proctorings_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_proctorings_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status_codes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.faculties
DROP TABLE IF EXISTS `faculties`;
CREATE TABLE IF NOT EXISTS `faculties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faculty_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `university_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `default_outline_template_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faculties_university_id_foreign` (`university_id`),
  CONSTRAINT `faculties_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.lectures
DROP TABLE IF EXISTS `lectures`;
CREATE TABLE IF NOT EXISTS `lectures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lecturer_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `degree` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_id` bigint unsigned NOT NULL,
  `university_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `academic_rank` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lectures_department_id_foreign` (`department_id`),
  KEY `lectures_university_id_foreign` (`university_id`),
  KEY `lectures_status_id_foreign` (`status_id`),
  CONSTRAINT `lectures_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lectures_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status_codes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lectures_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.lecture_roles
DROP TABLE IF EXISTS `lecture_roles`;
CREATE TABLE IF NOT EXISTS `lecture_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lecture_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `faculty_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lecture_roles_lecture_id_role_id_faculty_id_department_id_unique` (`lecture_id`,`role_id`,`faculty_id`,`department_id`),
  KEY `lecture_roles_role_id_foreign` (`role_id`),
  KEY `lecture_roles_faculty_id_foreign` (`faculty_id`),
  KEY `lecture_roles_department_id_foreign` (`department_id`),
  CONSTRAINT `lecture_roles_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lecture_roles_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lecture_roles_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lecture_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.meetings
DROP TABLE IF EXISTS `meetings`;
CREATE TABLE IF NOT EXISTS `meetings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `semester_id` bigint unsigned NOT NULL,
  `meeting_date` timestamp NOT NULL,
  `title` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_summary` longtext COLLATE utf8mb4_unicode_ci,
  `participants` longtext COLLATE utf8mb4_unicode_ci,
  `minutes_file` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meetings_department_id_foreign` (`department_id`),
  KEY `meetings_academic_year_id_foreign` (`academic_year_id`),
  KEY `meetings_semester_id_foreign` (`semester_id`),
  KEY `meetings_status_id_foreign` (`status_id`),
  CONSTRAINT `meetings_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `meetings_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `meetings_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `meetings_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status_codes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.meeting_participants
DROP TABLE IF EXISTS `meeting_participants`;
CREATE TABLE IF NOT EXISTS `meeting_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` bigint unsigned NOT NULL,
  `lecture_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meeting_participants_meeting_id_lecture_id_unique` (`meeting_id`,`lecture_id`),
  KEY `meeting_participants_lecture_id_foreign` (`lecture_id`),
  CONSTRAINT `meeting_participants_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `meeting_participants_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.module_types
DROP TABLE IF EXISTS `module_types`;
CREATE TABLE IF NOT EXISTS `module_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_order` int DEFAULT NULL,
  `description` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.notification_reads
DROP TABLE IF EXISTS `notification_reads`;
CREATE TABLE IF NOT EXISTS `notification_reads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_reads_notification_id_user_id_unique` (`notification_id`,`user_id`),
  KEY `notification_reads_user_id_foreign` (`user_id`),
  CONSTRAINT `notification_reads_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `system_notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_clos
DROP TABLE IF EXISTS `outline_clos`;
CREATE TABLE IF NOT EXISTS `outline_clos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_version_id` bigint unsigned NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `bloom_level` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outline_clos_course_version_id_foreign` (`course_version_id`),
  CONSTRAINT `outline_clos_course_version_id_foreign` FOREIGN KEY (`course_version_id`) REFERENCES `outline_course_versions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_clo_pi_maps
DROP TABLE IF EXISTS `outline_clo_pi_maps`;
CREATE TABLE IF NOT EXISTS `outline_clo_pi_maps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clo_id` bigint unsigned NOT NULL,
  `pi_id` bigint unsigned NOT NULL,
  `level` enum('I','R','T','A') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'I',
  `weight` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outline_clo_pi_maps_clo_id_foreign` (`clo_id`),
  KEY `outline_clo_pi_maps_pi_id_foreign` (`pi_id`),
  CONSTRAINT `outline_clo_pi_maps_clo_id_foreign` FOREIGN KEY (`clo_id`) REFERENCES `outline_clos` (`id`),
  CONSTRAINT `outline_clo_pi_maps_pi_id_foreign` FOREIGN KEY (`pi_id`) REFERENCES `outline_pis` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_clo_plo_maps
DROP TABLE IF EXISTS `outline_clo_plo_maps`;
CREATE TABLE IF NOT EXISTS `outline_clo_plo_maps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clo_id` bigint unsigned NOT NULL,
  `plo_id` bigint unsigned NOT NULL,
  `level` enum('I','R','T','A') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'I',
  `weight` int NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `outline_clo_plo_maps_clo_id_foreign` (`clo_id`),
  KEY `outline_clo_plo_maps_plo_id_foreign` (`plo_id`),
  KEY `outline_clo_plo_maps_created_by_foreign` (`created_by`),
  CONSTRAINT `outline_clo_plo_maps_clo_id_foreign` FOREIGN KEY (`clo_id`) REFERENCES `outline_clos` (`id`),
  CONSTRAINT `outline_clo_plo_maps_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `outline_clo_plo_maps_plo_id_foreign` FOREIGN KEY (`plo_id`) REFERENCES `outline_plos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_course_assignments
DROP TABLE IF EXISTS `outline_course_assignments`;
CREATE TABLE IF NOT EXISTS `outline_course_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_course_id` bigint unsigned NOT NULL,
  `outline_course_version_id` bigint unsigned DEFAULT NULL,
  `lecture_id` bigint unsigned NOT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL COMMENT 'user_id người giao (thường là TBM/TK)',
  `role` enum('chu_bien','dong_bien','tham_gia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'chu_bien',
  `status` enum('assigned','in_progress','submitted','submitted_review','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `due_date` date DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oca_unique` (`program_course_id`,`lecture_id`,`role`),
  KEY `oca_ocv_fk` (`outline_course_version_id`),
  KEY `oca_lecture_fk` (`lecture_id`),
  KEY `oca_assigned_by_fk` (`assigned_by`),
  KEY `oca_program_course_idx` (`program_course_id`),
  CONSTRAINT `oca_assigned_by_fk` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `oca_lecture_fk` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oca_ocv_fk` FOREIGN KEY (`outline_course_version_id`) REFERENCES `outline_course_versions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oca_program_course_fk` FOREIGN KEY (`program_course_id`) REFERENCES `outline_program_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_course_versions
DROP TABLE IF EXISTS `outline_course_versions`;
CREATE TABLE IF NOT EXISTS `outline_course_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_course_id` bigint unsigned DEFAULT NULL,
  `version_no` int NOT NULL DEFAULT '1',
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `status` enum('draft','review','approved','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `change_log` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ocv_program_course_fk` (`program_course_id`),
  CONSTRAINT `ocv_program_course_fk` FOREIGN KEY (`program_course_id`) REFERENCES `outline_program_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_pis
DROP TABLE IF EXISTS `outline_pis`;
CREATE TABLE IF NOT EXISTS `outline_pis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plo_id` bigint unsigned NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outline_pis_plo_id_foreign` (`plo_id`),
  CONSTRAINT `outline_pis_plo_id_foreign` FOREIGN KEY (`plo_id`) REFERENCES `outline_plos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_plos
DROP TABLE IF EXISTS `outline_plos`;
CREATE TABLE IF NOT EXISTS `outline_plos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_version_id` bigint unsigned NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outline_plos_program_version_id_foreign` (`program_version_id`),
  CONSTRAINT `outline_plos_program_version_id_foreign` FOREIGN KEY (`program_version_id`) REFERENCES `outline_program_versions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_program_courses
DROP TABLE IF EXISTS `outline_program_courses`;
CREATE TABLE IF NOT EXISTS `outline_program_courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_version_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `knowledge_type` enum('kien_thuc_chung','kien_thuc_khoa_hoc_co_ban','kien_thuc_bo_tro','kien_thuc_co_so_nganh_lien_nganh','kien_thuc_chuyen_nganh','hoc_phan_nghe_nghiep','hoc_phan_thuc_tap_tot_nghiep','hoc_phan_tot_nghiep','khoi_kien_thuc_dieu_kien_tot_nghiep','khoi_kien_thuc_ky_su_dac_thu','khac') COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_compulsory` tinyint(1) DEFAULT '1',
  `semester_no` int DEFAULT NULL,
  `semester_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `academic_year_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_in_semester` int DEFAULT NULL,
  `credit_theory` int NOT NULL DEFAULT '0',
  `credit_practice` int NOT NULL DEFAULT '0',
  `elective_group` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_elective_credit` int DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `special_requirement` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `credit_total` int GENERATED ALWAYS AS ((`credit_theory` + `credit_practice`)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_program_course` (`program_version_id`,`course_id`),
  KEY `idx_program_version` (`program_version_id`),
  KEY `idx_course` (`course_id`),
  KEY `idx_semester` (`semester_no`),
  KEY `idx_knowledge_type` (`knowledge_type`),
  KEY `idx_group` (`course_group`),
  KEY `opc_semester_fk` (`semester_id`),
  KEY `opc_academic_year_fk` (`academic_year_id`),
  CONSTRAINT `opc_academic_year_fk` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `opc_course_fk` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `opc_program_version_fk` FOREIGN KEY (`program_version_id`) REFERENCES `outline_program_versions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `opc_semester_fk` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_program_versions
DROP TABLE IF EXISTS `outline_program_versions`;
CREATE TABLE IF NOT EXISTS `outline_program_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `education_program_id` bigint unsigned NOT NULL,
  `version_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `status` enum('draft','review','approved','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outline_program_versions_education_program_id_foreign` (`education_program_id`),
  CONSTRAINT `outline_program_versions_education_program_id_foreign` FOREIGN KEY (`education_program_id`) REFERENCES `education_programs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_report_snapshots
DROP TABLE IF EXISTS `outline_report_snapshots`;
CREATE TABLE IF NOT EXISTS `outline_report_snapshots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_version_id` bigint unsigned NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outline_report_snapshots_program_version_id_foreign` (`program_version_id`),
  CONSTRAINT `outline_report_snapshots_program_version_id_foreign` FOREIGN KEY (`program_version_id`) REFERENCES `outline_program_versions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_section_contents
DROP TABLE IF EXISTS `outline_section_contents`;
CREATE TABLE IF NOT EXISTS `outline_section_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_version_id` bigint unsigned NOT NULL,
  `section_template_id` bigint unsigned NOT NULL,
  `content_html` longtext COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outline_section_contents_course_version_id_foreign` (`course_version_id`),
  KEY `outline_section_contents_section_template_id_foreign` (`section_template_id`),
  KEY `outline_section_contents_created_by_foreign` (`created_by`),
  CONSTRAINT `outline_section_contents_course_version_id_foreign` FOREIGN KEY (`course_version_id`) REFERENCES `outline_course_versions` (`id`),
  CONSTRAINT `outline_section_contents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `outline_section_contents_section_template_id_foreign` FOREIGN KEY (`section_template_id`) REFERENCES `outline_section_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_section_templates
DROP TABLE IF EXISTS `outline_section_templates`;
CREATE TABLE IF NOT EXISTS `outline_section_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `outline_template_id` bigint unsigned NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_no` int NOT NULL DEFAULT '1',
  `default_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outline_section_templates_outline_template_id_foreign` (`outline_template_id`),
  CONSTRAINT `outline_section_templates_outline_template_id_foreign` FOREIGN KEY (`outline_template_id`) REFERENCES `outline_templates` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.outline_templates
DROP TABLE IF EXISTS `outline_templates`;
CREATE TABLE IF NOT EXISTS `outline_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faculty_id` bigint unsigned NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gov_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'UBND TP. HỒ CHÍ MINH',
  `university_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'TRƯỜNG ĐẠI HỌC THỦ DẦU MỘT',
  `national_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM',
  `national_motto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Độc lập - Tự do - Hạnh phúc',
  `major_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ngành hoặc khối ngành áp dụng cho mẫu đề cương',
  PRIMARY KEY (`id`),
  KEY `outline_templates_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `outline_templates_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.role_permissions
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_allowed` tinyint(1) NOT NULL DEFAULT '1',
  `notes` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_id_module_action_unique` (`role_id`,`module`,`action`),
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.rooms
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hoc',
  `capacity` int DEFAULT NULL,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `university_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rooms_university_id_foreign` (`university_id`),
  CONSTRAINT `rooms_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.semesters
DROP TABLE IF EXISTS `semesters`;
CREATE TABLE IF NOT EXISTS `semesters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `semester_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_number` int NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `semesters_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `semesters_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.status_codes
DROP TABLE IF EXISTS `status_codes`;
CREATE TABLE IF NOT EXISTS `status_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.system_notifications
DROP TABLE IF EXISTS `system_notifications`;
CREATE TABLE IF NOT EXISTS `system_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT '1',
  `university_id` bigint unsigned DEFAULT NULL,
  `start_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_notifications_created_by_foreign` (`created_by`),
  KEY `system_notifications_university_id_foreign` (`university_id`),
  CONSTRAINT `system_notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `system_notifications_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.teaching_duties
DROP TABLE IF EXISTS `teaching_duties`;
CREATE TABLE IF NOT EXISTS `teaching_duties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `duty_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lecture_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `semester_id` bigint unsigned NOT NULL,
  `hours` double DEFAULT NULL,
  `class_group` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `venue` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duty_date` timestamp NULL DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `status_id` bigint unsigned DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.universities
DROP TABLE IF EXISTS `universities`;
CREATE TABLE IF NOT EXISTS `universities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `university_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `university_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `logo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `founded_date` date DEFAULT NULL,
  `fanpage` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `university_id` bigint unsigned DEFAULT NULL,
  `lecture_id` bigint unsigned DEFAULT NULL,
  `role` enum('superadmin','admin','truongkhoa','truongbomon','giangvien') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `is_outline_reviewer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_university_id_foreign` (`university_id`),
  KEY `users_lecture_id_foreign` (`lecture_id`),
  CONSTRAINT `users_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table qlhd.workloads
DROP TABLE IF EXISTS `workloads`;
CREATE TABLE IF NOT EXISTS `workloads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lecture_id` bigint unsigned NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `semester_id` bigint unsigned NOT NULL,
  `teaching_hours` double NOT NULL DEFAULT '0',
  `exam_proctoring_hours` double NOT NULL DEFAULT '0',
  `other_duty_hours` double NOT NULL DEFAULT '0',
  `standard_hours` double NOT NULL DEFAULT '0',
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workloads_lecture_id_academic_year_id_semester_id_unique` (`lecture_id`,`academic_year_id`,`semester_id`),
  KEY `workloads_academic_year_id_foreign` (`academic_year_id`),
  KEY `workloads_semester_id_foreign` (`semester_id`),
  CONSTRAINT `workloads_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workloads_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workloads_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
