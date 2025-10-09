-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: qlhd
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachments` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_group_id` bigint unsigned DEFAULT NULL,
  `course_group` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit` int DEFAULT NULL,
  `education_program_id` bigint unsigned NOT NULL,
  `department_id` bigint unsigned NOT NULL,
  `semester_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_course_code_unique` (`course_code`),
  KEY `courses_created_by_foreign` (`created_by`),
  KEY `courses_updated_by_foreign` (`updated_by`),
  KEY `courses_education_program_id_foreign` (`education_program_id`),
  KEY `courses_department_id_foreign` (`department_id`),
  KEY `courses_semester_id_foreign` (`semester_id`),
  KEY `courses_course_code_index` (`course_code`),
  CONSTRAINT `courses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `courses_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `courses_education_program_id_foreign` FOREIGN KEY (`education_program_id`) REFERENCES `education_programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `courses_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE SET NULL,
  CONSTRAINT `courses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `faculty_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `departments_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_programs`
--

DROP TABLE IF EXISTS `education_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `education_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `faculty_id` bigint unsigned NOT NULL,
  `education_system_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `education_system_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `education_programs_program_code_unique` (`program_code`),
  KEY `education_programs_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `education_programs_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_programs`
--

LOCK TABLES `education_programs` WRITE;
/*!40000 ALTER TABLE `education_programs` DISABLE KEYS */;
/*!40000 ALTER TABLE `education_programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_lectures`
--

DROP TABLE IF EXISTS `exam_lectures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_lectures` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_lectures`
--

LOCK TABLES `exam_lectures` WRITE;
/*!40000 ALTER TABLE `exam_lectures` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_lectures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_proctorings`
--

DROP TABLE IF EXISTS `exam_proctorings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_proctorings` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_proctorings`
--

LOCK TABLES `exam_proctorings` WRITE;
/*!40000 ALTER TABLE `exam_proctorings` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_proctorings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exams` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faculties`
--

DROP TABLE IF EXISTS `faculties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faculties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faculty_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `university_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faculties_university_id_foreign` (`university_id`),
  CONSTRAINT `faculties_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculties`
--

LOCK TABLES `faculties` WRITE;
/*!40000 ALTER TABLE `faculties` DISABLE KEYS */;
/*!40000 ALTER TABLE `faculties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lecture_roles`
--

DROP TABLE IF EXISTS `lecture_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lecture_roles` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lecture_roles`
--

LOCK TABLES `lecture_roles` WRITE;
/*!40000 ALTER TABLE `lecture_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `lecture_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lectures`
--

DROP TABLE IF EXISTS `lectures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lectures` (
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
  PRIMARY KEY (`id`),
  KEY `lectures_department_id_foreign` (`department_id`),
  KEY `lectures_university_id_foreign` (`university_id`),
  KEY `lectures_status_id_foreign` (`status_id`),
  CONSTRAINT `lectures_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lectures_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status_codes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lectures_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lectures`
--

LOCK TABLES `lectures` WRITE;
/*!40000 ALTER TABLE `lectures` DISABLE KEYS */;
/*!40000 ALTER TABLE `lectures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_participants`
--

DROP TABLE IF EXISTS `meeting_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_participants` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_participants`
--

LOCK TABLES `meeting_participants` WRITE;
/*!40000 ALTER TABLE `meeting_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meetings` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meetings`
--

LOCK TABLES `meetings` WRITE;
/*!40000 ALTER TABLE `meetings` DISABLE KEYS */;
/*!40000 ALTER TABLE `meetings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_08_24_083044_create_universities_table',1),(2,'2025_08_24_083055_create_status_codes_table',1),(3,'2025_08_24_083056_create_module_types_table',1),(4,'2025_08_24_083105_create_faculties_table',1),(5,'2025_08_24_083106_create_departments_table',1),(6,'2025_08_24_083111_create_academic_years_table',1),(7,'2025_08_24_083112_create_semesters_table',1),(8,'2025_08_24_083118_create_roles_table',1),(9,'2025_08_24_083119_create_lectures_table',1),(10,'2025_08_24_083141_create_users_table',1),(11,'2025_08_24_083143_create_lecture_roles_table',1),(12,'2025_08_24_083145_create_role_permissions_table',1),(13,'2025_08_24_083150_create_education_programs_table',1),(14,'2025_08_24_083154_create_courses_table',1),(15,'2025_08_24_083200_create_rooms_table',1),(16,'2025_08_24_083201_create_teaching_duties_table',1),(17,'2025_08_24_083207_create_exams_table',1),(18,'2025_08_24_083216_create_exam_proctorings_table',1),(19,'2025_08_24_083217_create_exam_lectures_table',1),(20,'2025_08_24_083222_create_meetings_table',1),(21,'2025_08_24_083223_create_meeting_participants_table',1),(22,'2025_08_24_083224_create_workloads_table',1),(23,'2025_08_24_083227_create_attachments_table',1),(24,'2025_08_24_083227_create_audit_logs_table',1),(25,'2025_08_24_093219_create_sessions_table',1),(26,'2025_08_24_124456_create_cache_table',1),(27,'2025_09_10_133116_create_system_notifications_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_types`
--

DROP TABLE IF EXISTS `module_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_order` int DEFAULT NULL,
  `description` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_types`
--

LOCK TABLES `module_types` WRITE;
/*!40000 ALTER TABLE `module_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `module_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_reads`
--

DROP TABLE IF EXISTS `notification_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_reads` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_reads`
--

LOCK TABLES `notification_reads` WRITE;
/*!40000 ALTER TABLE `notification_reads` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_reads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semesters`
--

DROP TABLE IF EXISTS `semesters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `semesters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `semester_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_number` int NOT NULL,
  `academic_year_id` bigint unsigned NOT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `semesters_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `semesters_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semesters`
--

LOCK TABLES `semesters` WRITE;
/*!40000 ALTER TABLE `semesters` DISABLE KEYS */;
/*!40000 ALTER TABLE `semesters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_codes`
--

DROP TABLE IF EXISTS `status_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_codes`
--

LOCK TABLES `status_codes` WRITE;
/*!40000 ALTER TABLE `status_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `status_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_notifications`
--

DROP TABLE IF EXISTS `system_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_notifications` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_notifications`
--

LOCK TABLES `system_notifications` WRITE;
/*!40000 ALTER TABLE `system_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teaching_duties`
--

DROP TABLE IF EXISTS `teaching_duties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teaching_duties` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teaching_duties`
--

LOCK TABLES `teaching_duties` WRITE;
/*!40000 ALTER TABLE `teaching_duties` DISABLE KEYS */;
/*!40000 ALTER TABLE `teaching_duties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universities`
--

DROP TABLE IF EXISTS `universities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universities` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universities`
--

LOCK TABLES `universities` WRITE;
/*!40000 ALTER TABLE `universities` DISABLE KEYS */;
/*!40000 ALTER TABLE `universities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_university_id_foreign` (`university_id`),
  KEY `users_lecture_id_foreign` (`lecture_id`),
  CONSTRAINT `users_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workloads`
--

DROP TABLE IF EXISTS `workloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workloads` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workloads`
--

LOCK TABLES `workloads` WRITE;
/*!40000 ALTER TABLE `workloads` DISABLE KEYS */;
/*!40000 ALTER TABLE `workloads` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-08  4:15:32
