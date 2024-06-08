SET GLOBAL host_cache_size=0;

CREATE DATABASE  IF NOT EXISTS `integration_server` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `integration_server`;
-- MySQL dump 10.13  Distrib 8.0.35, for Linux (x86_64)
--
-- Host: localhost    Database: integration_server
-- ------------------------------------------------------
-- Server version	8.0.35-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `onboarding`
--

DROP TABLE IF EXISTS `onboarding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `onboarding` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `atfinity_case_id` int unsigned NOT NULL,
  `atfinity_instance_id` int unsigned NOT NULL,
  `atfinity_instance_fields_id` int unsigned NOT NULL DEFAULT '0',
  `odoo_id` int unsigned NOT NULL,
  `language` char(2) NOT NULL,
  `document_type` varchar(25) NOT NULL,
  `library_document_id` varchar(36) DEFAULT NULL,
  `xpressid_validation_id` varchar(32) DEFAULT NULL,
  `state` tinyint DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `webhook_timestamp` int DEFAULT NULL,
  `hash` varchar(65) NOT NULL,
  `data_error` text,
  `count_error` tinyint DEFAULT '0',
  `boidas_to_atfinity` tinyint(1) DEFAULT '0',
  `retargeting` tinyint(1) DEFAULT '0',
  `prospects` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`,`hash`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `xpressid_validation_id_idx` (`xpressid_validation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1025 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `retargeting`
--

DROP TABLE IF EXISTS `retargeting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `retargeting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `onboarding_id` int unsigned NOT NULL,
  `atfinity_case_id` int unsigned NOT NULL,
  `counter` tinyint NOT NULL DEFAULT '0',
  `date` datetime DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `next_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3847 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-12-03 12:06:21