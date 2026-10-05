-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: food_order
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Current Database: `food_order`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `food_order` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `food_order`;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('food-order-cache-0e2cfa6f6a7e145223ef55d12d18e591','i:1;',1791184768),('food-order-cache-0e2cfa6f6a7e145223ef55d12d18e591:timer','i:1791184768;',1791184768),('food-order-cache-1a3694d84946f8f5d71f604affe2ae5f','i:1;',1791185191),('food-order-cache-1a3694d84946f8f5d71f604affe2ae5f:timer','i:1791185191;',1791185191),('food-order-cache-5bfc56f49adc251aef50cf7cda1433ed','i:1;',1791184834),('food-order-cache-5bfc56f49adc251aef50cf7cda1433ed:timer','i:1791184834;',1791184834),('food-order-cache-6882485d5226cd3919ea28c256ff57b8','i:2;',1791185123),('food-order-cache-6882485d5226cd3919ea28c256ff57b8:timer','i:1791185123;',1791185123),('food-order-cache-a8d06573a452d6b79518da7ceb8f8492','i:1;',1791185184),('food-order-cache-a8d06573a452d6b79518da7ceb8f8492:timer','i:1791185184;',1791185184),('food-order-cache-eaba0cdd4e3835f7ccb5ab100dd5fb71','i:1;',1791185085),('food-order-cache-eaba0cdd4e3835f7ccb5ab100dd5fb71:timer','i:1791185085;',1791185085);
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Món Chính Đặc Sắc','mon-chinh','Các món ăn no ngon miệng, chuẩn vị đậm đà hấp dẫn','fa-utensils','2026-08-28 10:10:45','2026-08-28 10:10:45'),(2,'Khai Vị & Ăn Vặt','khai-vi-an-vat','Món ăn kèm giòn rụm, lai rai cực đã','fa-bowl-food','2026-08-28 10:10:45','2026-08-28 10:10:45'),(3,'Đồ Uống & Tráng Miệng','do-uong-trang-mieng','Thức uống giải khát mát lạnh và món ngọt thanh mát','fa-mug-hot','2026-08-28 10:10:45','2026-08-28 10:10:45'),(4,'Món Chay Thanh Tịnh','mon-chay','Món chay bổ dưỡng từ rau củ quả tươi tự nhiên','fa-leaf','2026-08-28 10:10:45','2026-08-28 10:10:45'),(5,'Xà bì chường','xa-bi-chuong',NULL,'fa-utensils','2026-08-28 11:27:13','2026-08-28 11:27:13');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_zones`
--

DROP TABLE IF EXISTS `delivery_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_zones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee` decimal(12,0) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_zones_name_unique` (`name`),
  KEY `delivery_zones_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_zones`
--

LOCK TABLES `delivery_zones` WRITE;
/*!40000 ALTER TABLE `delivery_zones` DISABLE KEYS */;
INSERT INTO `delivery_zones` VALUES (1,'Quận 1',15000,1,'2026-10-04 15:51:55','2026-10-04 15:51:55'),(2,'Quận 3',15000,1,'2026-10-04 15:51:55','2026-10-04 15:51:55'),(3,'Bình Thạnh',20000,1,'2026-10-04 15:51:55','2026-10-04 15:51:55');
/*!40000 ALTER TABLE `delivery_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `foods`
--

DROP TABLE IF EXISTS `foods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `foods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,0) NOT NULL,
  `image` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foods_category_id_is_available_index` (`category_id`,`is_available`),
  CONSTRAINT `foods_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `foods`
--

LOCK TABLES `foods` WRITE;
/*!40000 ALTER TABLE `foods` DISABLE KEYS */;
INSERT INTO `foods` VALUES (1,1,'Phở Bò Tái Lăn Hà Nội','Bò tươi xào lăn thơm phức tỏi gừng, nước dùng hầm xương đậm đà 24h ăn kèm quẩy giòn.',65000,'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(2,1,'Cơm Tấm Sườn Bì Chả Trứng','Sườn nướng mật ong than hoa thơm lừng, bì dai giòn, chả trứng béo ngậy kèm mỡ hành tóp mỡ.',60000,'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(3,1,'Bún Chả Nướng Than Hoa','Chả viên và chả miếng nướng xém cạnh, nước mắm đu đủ cà rốt chua ngọt chuẩn vị phố cổ.',55000,'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(4,1,'Bánh Mì Kẹp Thịt Nướng Đặc Biệt','Vỏ bánh giòn rụm, pate béo ngậy tự làm, thịt xá xíu nướng mè và đồ chua sốt cay đặc biệt.',35000,'https://images.unsplash.com/photo-1626804475297-41608ea09aeb?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(5,1,'Mì Quảng Tôm Thịt Trứng Cút','Sợi mì vàng óng dai mềm, tôm rim đậm đà, thịt ba chỉ béo ngậy, rắc đậu phộng rang giòn.',50000,'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(6,2,'Gà Rán Giòn Sốt Phô Mai Cay','Đùi gà giòn tan rụm phủ lớp sốt cay Hàn Quốc và phô mai kéo sợi thơm phức béo ngậy.',59000,'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(7,2,'Nem Rán Giòn Hà Nội (6 chiếc)','Nhân thịt băm mộc nhĩ nấm hương bọc bánh đa nem chiên vàng ruộm giòn tan.',45000,'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(8,2,'Khoai Tây Chiên Lắc Phô Mai','Khoai tây cắt lát chiên vàng giòn rụm, lắc đều bột phô mai béo mặn ngọt hài hòa.',30000,'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(9,3,'Trà Sữa Trân Châu Đường Đen','Trà sữa ô long thơm nồng kết hợp trân châu thủ công dẻo dai đun sốt đường đen đậm vị.',38000,'https://images.unsplash.com/photo-1558857563-b371033873b8?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(10,3,'Trà Trái Cây Nhiệt Đới Tươi Mát','Trà lài ủ lạnh kết hợp chanh leo, dâu tây, cam vàng tươi mát giải nhiệt tức thì.',35000,'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(11,3,'Bánh Flan Trứng Sữa Cà Phê','Flan mềm mịn tan chảy trong miệng hòa quyện cùng nước cốt cà phê thơm lừng và đá bào.',25000,'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(12,4,'Cơm Chiên Hạt Sen Nấm Hương Chay','Cơm rang tơi xốp với hạt sen bùi béo, nấm hương tươi, cà rốt và đậu Hà Lan thanh nhẹ.',48000,'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(13,4,'Salad Bơ Trái Cây Sốt Mè Rang','Bơ sáp béo ngậy, cà chua bi, xà lách giòn và sốt mè rang Nhật Bản thanh mát tốt cho sức khỏe.',45000,'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=600&q=80',1,'2026-08-28 10:10:45','2026-08-28 10:10:45'),(14,1,'sườn bì chả',NULL,100,NULL,1,'2026-08-28 11:27:44','2026-08-28 11:27:44');
/*!40000 ALTER TABLE `foods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_20_120609_create_categories_table',1),(5,'2026_08_20_120939_create_foods_table',1),(6,'2026_08_20_121012_create_orders_table',1),(7,'2026_08_20_121050_create_order_items_table',1),(8,'2026_09_22_000001_create_order_status_histories_table',2),(9,'2026_09_22_000002_create_vouchers_table',2),(10,'2026_09_22_000003_add_voucher_fields_to_orders_table',2),(11,'2026_09_22_000004_create_store_settings_table',2),(12,'2026_09_22_000005_create_notifications_table',2),(13,'2026_09_22_000006_create_delivery_zones_table',2),(14,'2026_09_22_000007_add_delivery_zone_snapshot_to_orders_table',2),(15,'2026_10_05_000001_add_unlocked_at_to_users_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `notifications_notifiable_type_notifiable_id_read_at_index` (`notifiable_type`,`notifiable_id`,`read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('031d76c0-5e27-4793-99fc-ccc87bd502a3','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',5,'{\"order_id\":8,\"order_code\":\"FO-20261005-RA7FBT\",\"from_status\":\"confirmed\",\"to_status\":\"delivering\",\"status_label\":\"\\u0110ang giao h\\u00e0ng\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-RA7FBT \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110ang giao h\\u00e0ng.\",\"reason\":null}',NULL,'2026-10-05 07:22:03','2026-10-05 07:22:03'),('034bb8a4-db4d-44c7-afcd-445ff53170b4','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":9,\"order_code\":\"FO-20261005-PX5QL8\",\"from_status\":\"confirmed\",\"to_status\":\"delivering\",\"status_label\":\"\\u0110ang giao h\\u00e0ng\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-PX5QL8 \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110ang giao h\\u00e0ng.\",\"reason\":null}',NULL,'2026-10-05 07:24:54','2026-10-05 07:24:54'),('04729341-8ad6-4c9a-9ec8-beaad8ce1f66','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":6,\"order_code\":\"FO-20261005-ZKUNWW\",\"from_status\":\"delivering\",\"to_status\":\"cancelled\",\"status_label\":\"\\u0110\\u00e3 h\\u1ee7y\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-ZKUNWW \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 h\\u1ee7y.\",\"reason\":\"Kh\\u00e1ch kh\\u00f4ng nghe m\\u00e1y (\\u0111\\u00e3 g\\u1ecdi 3 l\\u1ea7n)\"}',NULL,'2026-10-05 07:23:12','2026-10-05 07:23:12'),('310473e1-5c5c-4fbb-bb37-28399c7c9144','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":4,\"order_code\":\"FO-20261005-MVSO5T\",\"from_status\":\"delivering\",\"to_status\":\"completed\",\"status_label\":\"Ho\\u00e0n t\\u1ea5t\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-MVSO5T \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i Ho\\u00e0n t\\u1ea5t.\",\"reason\":null}',NULL,'2026-10-05 07:19:05','2026-10-05 07:19:05'),('3eb46ecb-64ea-45c6-825d-48660df3a937','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":4,\"order_code\":\"FO-20261005-MVSO5T\",\"from_status\":\"preparing\",\"to_status\":\"delivering\",\"status_label\":\"\\u0110ang giao h\\u00e0ng\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-MVSO5T \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110ang giao h\\u00e0ng.\",\"reason\":null}',NULL,'2026-10-05 06:33:09','2026-10-05 06:33:09'),('5910d987-29ce-437e-ae15-960818c4f93e','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',5,'{\"order_id\":8,\"order_code\":\"FO-20261005-RA7FBT\",\"from_status\":\"delivering\",\"to_status\":\"completed\",\"status_label\":\"Ho\\u00e0n t\\u1ea5t\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-RA7FBT \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i Ho\\u00e0n t\\u1ea5t.\",\"reason\":null}',NULL,'2026-10-05 07:22:52','2026-10-05 07:22:52'),('7bf67755-4fce-4ae0-ae75-6ee9f6eeb726','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":7,\"order_code\":\"FO-20261005-AYM6WG\",\"from_status\":\"delivering\",\"to_status\":\"cancelled\",\"status_label\":\"\\u0110\\u00e3 h\\u1ee7y\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-AYM6WG \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 h\\u1ee7y.\",\"reason\":\"Kh\\u00e1ch kh\\u00f4ng nghe m\\u00e1y (\\u0111\\u00e3 g\\u1ecdi 3 l\\u1ea7n)\"}',NULL,'2026-10-05 07:23:01','2026-10-05 07:23:01'),('86bb7b5a-ef29-4ca4-935c-60c0febc5bc5','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":9,\"order_code\":\"FO-20261005-PX5QL8\",\"from_status\":\"pending\",\"to_status\":\"confirmed\",\"status_label\":\"\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-PX5QL8 \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 x\\u00e1c nh\\u1eadn.\",\"reason\":null}',NULL,'2026-10-05 07:24:08','2026-10-05 07:24:08'),('92dddd80-2f62-4ce5-a995-5143361580a5','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":6,\"order_code\":\"FO-20261005-ZKUNWW\",\"from_status\":\"confirmed\",\"to_status\":\"delivering\",\"status_label\":\"\\u0110ang giao h\\u00e0ng\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-ZKUNWW \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110ang giao h\\u00e0ng.\",\"reason\":null}',NULL,'2026-10-05 07:22:00','2026-10-05 07:22:00'),('96ab8773-e7c7-4212-9f2c-f2dea7c37d6d','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',1,'{\"order_id\":5,\"order_code\":\"FO-20261005-YCAGML\",\"from_status\":\"pending\",\"to_status\":\"cancelled\",\"status_label\":\"\\u0110\\u00e3 h\\u1ee7y\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-YCAGML \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 h\\u1ee7y.\",\"reason\":\"Kh\\u00e1ch h\\u00e0ng h\\u1ee7y t\\u1eeb trang web\"}',NULL,'2026-10-05 06:54:35','2026-10-05 06:54:35'),('a064907a-28ec-4ebd-aac8-502564033c80','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":7,\"order_code\":\"FO-20261005-AYM6WG\",\"from_status\":\"confirmed\",\"to_status\":\"delivering\",\"status_label\":\"\\u0110ang giao h\\u00e0ng\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-AYM6WG \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110ang giao h\\u00e0ng.\",\"reason\":null}',NULL,'2026-10-05 07:22:02','2026-10-05 07:22:02'),('a613647f-78ce-4b2a-a161-a4fe8bff8cd7','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":7,\"order_code\":\"FO-20261005-AYM6WG\",\"from_status\":\"pending\",\"to_status\":\"confirmed\",\"status_label\":\"\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-AYM6WG \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 x\\u00e1c nh\\u1eadn.\",\"reason\":null}',NULL,'2026-10-05 07:21:50','2026-10-05 07:21:50'),('acdb975d-63c5-4142-86ba-9bc989b41f4b','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',5,'{\"order_id\":8,\"order_code\":\"FO-20261005-RA7FBT\",\"from_status\":\"pending\",\"to_status\":\"confirmed\",\"status_label\":\"\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-RA7FBT \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 x\\u00e1c nh\\u1eadn.\",\"reason\":null}',NULL,'2026-10-05 07:21:48','2026-10-05 07:21:48'),('b0373bd5-c58b-4ed3-a55f-8f82c2ee216d','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":9,\"order_code\":\"FO-20261005-PX5QL8\",\"from_status\":\"delivering\",\"to_status\":\"cancelled\",\"status_label\":\"\\u0110\\u00e3 h\\u1ee7y\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-PX5QL8 \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 h\\u1ee7y.\",\"reason\":\"Kh\\u00e1ch t\\u1eeb ch\\u1ed1i nh\\u1eadn m\\u00f3n \\/ bom h\\u00e0ng\"}',NULL,'2026-10-05 07:25:14','2026-10-05 07:25:14'),('d63ada2a-d056-4c96-bd3e-861cfb495a26','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":4,\"order_code\":\"FO-20261005-MVSO5T\",\"from_status\":\"confirmed\",\"to_status\":\"preparing\",\"status_label\":\"\\u0110ang ch\\u1ebf bi\\u1ebfn\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-MVSO5T \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110ang ch\\u1ebf bi\\u1ebfn.\",\"reason\":null}',NULL,'2026-10-05 06:33:03','2026-10-05 06:33:03'),('e47f6e64-79a0-4cb9-b343-77e057615491','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":6,\"order_code\":\"FO-20261005-ZKUNWW\",\"from_status\":\"pending\",\"to_status\":\"confirmed\",\"status_label\":\"\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-ZKUNWW \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 x\\u00e1c nh\\u1eadn.\",\"reason\":null}',NULL,'2026-10-05 07:21:52','2026-10-05 07:21:52'),('ee00f96a-fb67-4de2-b1f0-e3352f2121b7','App\\Notifications\\OrderStatusUpdatedNotification','App\\Models\\User',4,'{\"order_id\":4,\"order_code\":\"FO-20261005-MVSO5T\",\"from_status\":\"pending\",\"to_status\":\"confirmed\",\"status_label\":\"\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng FO-20261005-MVSO5T \\u0111\\u00e3 chuy\\u1ec3n sang tr\\u1ea1ng th\\u00e1i \\u0110\\u00e3 x\\u00e1c nh\\u1eadn.\",\"reason\":null}','2026-10-05 06:32:37','2026-10-05 06:31:16','2026-10-05 06:32:37');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `food_id` bigint unsigned DEFAULT NULL,
  `food_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(12,0) NOT NULL,
  `quantity` smallint unsigned NOT NULL,
  `line_total` decimal(12,0) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_food_id_foreign` (`food_id`),
  CONSTRAINT `order_items_food_id_foreign` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (2,2,2,'Cơm Tấm Sườn Bì Chả Trứng',60000,1,60000,NULL,'2026-08-28 11:28:38','2026-08-28 11:28:38'),(3,3,12,'Cơm Chiên Hạt Sen Nấm Hương Chay',48000,1,48000,NULL,'2026-09-21 08:55:48','2026-09-21 08:55:48'),(4,4,14,'sườn bì chả',100,2,200,NULL,'2026-10-05 06:30:17','2026-10-05 06:30:17'),(5,4,1,'Phở Bò Tái Lăn Hà Nội',65000,1,65000,NULL,'2026-10-05 06:30:17','2026-10-05 06:30:17'),(6,4,2,'Cơm Tấm Sườn Bì Chả Trứng',60000,1,60000,NULL,'2026-10-05 06:30:17','2026-10-05 06:30:17'),(7,4,10,'Trà Trái Cây Nhiệt Đới Tươi Mát',35000,1,35000,NULL,'2026-10-05 06:30:17','2026-10-05 06:30:17'),(8,5,1,'Phở Bò Tái Lăn Hà Nội',65000,1,65000,NULL,'2026-10-05 06:54:25','2026-10-05 06:54:25'),(9,5,2,'Cơm Tấm Sườn Bì Chả Trứng',60000,1,60000,NULL,'2026-10-05 06:54:25','2026-10-05 06:54:25'),(10,6,1,'Phở Bò Tái Lăn Hà Nội',65000,1,65000,NULL,'2026-10-05 06:55:42','2026-10-05 06:55:42'),(11,6,2,'Cơm Tấm Sườn Bì Chả Trứng',60000,1,60000,NULL,'2026-10-05 06:55:42','2026-10-05 06:55:42'),(12,7,14,'sườn bì chả',100,1,100,NULL,'2026-10-05 07:05:22','2026-10-05 07:05:22'),(13,7,1,'Phở Bò Tái Lăn Hà Nội',65000,1,65000,NULL,'2026-10-05 07:05:22','2026-10-05 07:05:22'),(14,7,2,'Cơm Tấm Sườn Bì Chả Trứng',60000,1,60000,NULL,'2026-10-05 07:05:22','2026-10-05 07:05:22'),(15,8,11,'Bánh Flan Trứng Sữa Cà Phê',25000,1,25000,NULL,'2026-10-05 07:19:34','2026-10-05 07:19:34'),(16,9,1,'Phở Bò Tái Lăn Hà Nội',65000,1,65000,NULL,'2026-10-05 07:23:45','2026-10-05 07:23:45');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_histories`
--

DROP TABLE IF EXISTS `order_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `actor_id` bigint unsigned DEFAULT NULL,
  `from_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_status_histories_actor_id_foreign` (`actor_id`),
  KEY `order_status_histories_order_id_created_at_index` (`order_id`,`created_at`),
  CONSTRAINT `order_status_histories_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_status_histories_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_histories`
--

LOCK TABLES `order_status_histories` WRITE;
/*!40000 ALTER TABLE `order_status_histories` DISABLE KEYS */;
INSERT INTO `order_status_histories` VALUES (1,4,4,NULL,'pending','Khách hàng tạo đơn','2026-10-05 06:30:17','2026-10-05 06:30:17'),(2,4,1,'pending','confirmed',NULL,'2026-10-05 06:31:14','2026-10-05 06:31:14'),(3,4,1,'confirmed','preparing',NULL,'2026-10-05 06:33:03','2026-10-05 06:33:03'),(4,4,1,'preparing','delivering',NULL,'2026-10-05 06:33:09','2026-10-05 06:33:09'),(5,5,1,NULL,'pending','Khách hàng tạo đơn','2026-10-05 06:54:25','2026-10-05 06:54:25'),(6,5,1,'pending','cancelled','Khách hàng hủy từ trang web','2026-10-05 06:54:35','2026-10-05 06:54:35'),(7,6,4,NULL,'pending','Khách hàng tạo đơn','2026-10-05 06:55:42','2026-10-05 06:55:42'),(8,7,4,NULL,'pending','Khách hàng tạo đơn','2026-10-05 07:05:22','2026-10-05 07:05:22'),(9,4,5,'delivering','completed',NULL,'2026-10-05 07:19:05','2026-10-05 07:19:05'),(10,8,5,NULL,'pending','Khách hàng tạo đơn','2026-10-05 07:19:34','2026-10-05 07:19:34'),(11,8,1,'pending','confirmed',NULL,'2026-10-05 07:21:48','2026-10-05 07:21:48'),(12,7,1,'pending','confirmed',NULL,'2026-10-05 07:21:50','2026-10-05 07:21:50'),(13,6,1,'pending','confirmed',NULL,'2026-10-05 07:21:52','2026-10-05 07:21:52'),(14,6,1,'confirmed','delivering',NULL,'2026-10-05 07:22:00','2026-10-05 07:22:00'),(15,7,1,'confirmed','delivering',NULL,'2026-10-05 07:22:02','2026-10-05 07:22:02'),(16,8,1,'confirmed','delivering',NULL,'2026-10-05 07:22:03','2026-10-05 07:22:03'),(17,8,5,'delivering','completed',NULL,'2026-10-05 07:22:52','2026-10-05 07:22:52'),(18,7,5,'delivering','cancelled','Khách không nghe máy (đã gọi 3 lần)','2026-10-05 07:23:01','2026-10-05 07:23:01'),(19,6,5,'delivering','cancelled','Khách không nghe máy (đã gọi 3 lần)','2026-10-05 07:23:12','2026-10-05 07:23:12'),(20,9,4,NULL,'pending','Khách hàng tạo đơn','2026-10-05 07:23:45','2026-10-05 07:23:45'),(21,9,1,'pending','confirmed',NULL,'2026-10-05 07:24:07','2026-10-05 07:24:07'),(22,9,1,'confirmed','delivering',NULL,'2026-10-05 07:24:54','2026-10-05 07:24:54'),(23,9,5,'delivering','cancelled','Khách từ chối nhận món / bom hàng','2026-10-05 07:25:14','2026-10-05 07:25:14');
/*!40000 ALTER TABLE `order_status_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `delivery_zone_id` bigint unsigned DEFAULT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_zone_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(12,0) NOT NULL,
  `discount_amount` decimal(12,0) NOT NULL DEFAULT '0',
  `shipping_fee` decimal(12,0) NOT NULL DEFAULT '0',
  `total_price` decimal(12,0) NOT NULL,
  `payment_method` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cod',
  `payment_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  KEY `orders_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `orders_status_created_at_index` (`status`,`created_at`),
  KEY `orders_voucher_id_index` (`voucher_id`),
  KEY `orders_delivery_zone_id_foreign` (`delivery_zone_id`),
  CONSTRAINT `orders_delivery_zone_id_foreign` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `orders_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (2,'FO-20260828-YT2902',1,NULL,NULL,'Admin Food Order','0','nhà',NULL,'ko',60000,0,0,60000,'cod','paid','completed',NULL,NULL,'2026-08-28 11:29:06','2026-08-28 11:28:38','2026-08-28 11:29:06'),(3,'FO-20260921-G1DM1V',4,NULL,NULL,'Khách văn Hàng','0369686432','Qua lối nhỏ vào nhà em , muốn ghé vào thăm mà ba má e đuổi về',NULL,NULL,48000,0,0,48000,'cod','paid','completed',NULL,NULL,'2026-09-21 08:57:44','2026-09-21 08:55:48','2026-09-21 08:57:44'),(4,'FO-20261005-MVSO5T',4,3,NULL,'Khách văn Hàng','0369686432','Qua lối nhỏ vào nhà em , muốn ghé vào thăm mà ba má e đuổi về','Bình Thạnh',NULL,160200,0,20000,180200,'cod','paid','completed',NULL,NULL,'2026-10-05 07:19:05','2026-10-05 06:30:17','2026-10-05 07:19:05'),(5,'FO-20261005-YCAGML',1,1,1,'Admin Food Order','0369686432','Qua lối nhỏ vào nhà em , muốn ghé vào thăm mà ba má e đuổi về','Quận 1',NULL,125000,100,15000,139900,'cod','unpaid','cancelled','Khách hàng hủy từ trang web','2026-10-05 06:54:35',NULL,'2026-10-05 06:54:25','2026-10-05 06:54:35'),(6,'FO-20261005-ZKUNWW',4,3,1,'Khách văn Hàng','0369686432','Qua lối nhỏ vào nhà em , muốn ghé vào thăm mà ba má e đuổi về','Bình Thạnh',NULL,125000,100,20000,144900,'cod','unpaid','cancelled','Khách không nghe máy (đã gọi 3 lần)','2026-10-05 07:23:12',NULL,'2026-10-05 06:55:42','2026-10-05 07:23:12'),(7,'FO-20261005-AYM6WG',4,3,1,'Khách văn Hàng','0369686432','Qua lối nhỏ vào nhà em , muốn ghé vào thăm mà ba má e đuổi về','Bình Thạnh',NULL,125100,125100,20000,20000,'cod','unpaid','cancelled','Khách không nghe máy (đã gọi 3 lần)','2026-10-05 07:23:01',NULL,'2026-10-05 07:05:22','2026-10-05 07:23:01'),(8,'FO-20261005-RA7FBT',5,1,NULL,'Nguyễn Văn Ship (Shipper)','0901234567','Đội Giao Hàng FoodOrder Quận 1','Quận 1',NULL,25000,0,15000,40000,'cod','paid','completed',NULL,NULL,'2026-10-05 07:22:52','2026-10-05 07:19:34','2026-10-05 07:22:52'),(9,'FO-20261005-PX5QL8',4,1,NULL,'Khách văn Hàng','0369686432','Qua lối nhỏ vào nhà em , muốn ghé vào thăm mà ba má e đuổi về','Quận 1',NULL,65000,0,15000,80000,'cod','unpaid','cancelled','Khách từ chối nhận món / bom hàng','2026-10-05 07:25:14',NULL,'2026-10-05 07:23:45','2026-10-05 07:25:14');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('2BpJHCy3g7njDMxusO5f4tr6Z8iSA7GWBbKoUQT1',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJNVTZncE1LWmt4MlZ6ZnBIR0p4dGNoZnBBMnppNDcyaDNwOXg4RjF1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1791182376),('BLxd56cu44KW9vRdKgWkaI6apM67a93N1UrqZj3e',NULL,'127.0.0.1','','eyJfdG9rZW4iOiJod01ramZxQkQ3S2dUUm5jWFJIdjg5MHoxUjB2a2o2cE04SGxkeVdCIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1791184527),('De59NW3bZv1uoNrMd7uxpu8GsqzegnPtlr5Hbhfe',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJOQ1htRUFva01NY05BT3g1OWtzSTdHdWNSTVVBcTNmc1FtakR0ZGIzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1791181576),('OZuPTeEhUZPloWMzA1k5mOBBIWrb7vMbuwms2tQL',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJzMUF2TXBlbVVCaGtxUEw1TDRDZFdnR3dzb3g0Yml2UzhyRXo3eDQ2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6WyJzdGF0dXMiXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJzdGF0dXMiOiJcdTAxMTBcdTAxMDNuZyBuaFx1MWVhZHAgdGhcdTAwZTBuaCBjXHUwMGY0bmchIn0=',1791181534),('vUDbk1AW9HxjULL57DqA3mXq90PjGmdwZd5lNPa0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJncDl5Q1FQSm1rd2dYZU9KbUQydEgzOWpaV3hESktkWllXcnJsZms1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1791181407),('waUuwHmgGMEsAx2vPZvT9IUhcE9T1PwzKzxxMUD0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI3cGVzd09sVXl5WDY2ajUxdVNyZEUyREdCMDVUQkdZZWg4bmw0b0ZiIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImhvbWUifX0=',1791185154);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_settings`
--

DROP TABLE IF EXISTS `store_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FoodOrder',
  `is_open` tinyint(1) NOT NULL DEFAULT '1',
  `opens_at` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '00:00',
  `closes_at` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '23:59',
  `min_order_value` decimal(12,0) NOT NULL DEFAULT '0',
  `shipping_fee` decimal(12,0) NOT NULL DEFAULT '0',
  `estimated_delivery_minutes` smallint unsigned NOT NULL DEFAULT '30',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_settings`
--

LOCK TABLES `store_settings` WRITE;
/*!40000 ALTER TABLE `store_settings` DISABLE KEYS */;
INSERT INTO `store_settings` VALUES (1,'FoodOrder',1,'00:00','23:59',0,0,30,'2026-10-04 15:51:55','2026-10-04 15:51:55');
/*!40000 ALTER TABLE `store_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `unlocked_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_is_active_index` (`role`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Food Order','admin@foodorder.test',NULL,'$2y$12$/MHob0dL4FSe8s.sMuIwmO39uRqqnpAKW.sLssrqXaMnYE4LPNVVm',NULL,NULL,'admin',1,NULL,'wSglhhqBkqHHO2184LL491QvdOCVBSWS2c3jJJyyU2zGghYpBjSl6Tnpmn1J','2026-08-28 10:10:45','2026-10-04 15:51:55'),(3,'Tèo','nguyentrongthuy2005@gmail.com',NULL,'$2y$12$2xIolPKhB3gWRrlyXHPAweeaKA1uEPi1UH5yqj36dAP6ODd5/Ccbq','0369686432','Hàn Nội','user',1,NULL,NULL,'2026-08-28 11:30:33','2026-08-28 11:30:33'),(4,'Khách văn Hàng','Khachhang@gmail.com',NULL,'$2y$12$LZ4Ql7PJVC7dT4PVHihV9.yBpZ2XkOIRj6SJ2qSk1QVstaqK6WHaW','0369686432','Qua lối nhỏ vào nhà em , muốn ghé vào thăm mà ba má e đuổi về','user',1,'2026-10-05 07:25:44',NULL,'2026-09-21 08:55:40','2026-10-05 07:25:44'),(5,'Nguyễn Văn Ship (Shipper)','shipper@foodorder.test','2026-10-05 07:14:16','$2y$12$9OQXwKHu9qYhy7lVgVXSquY0HnHUzB3UpPSEV1Fg90zlxUbARDhbW','0901234567','Đội Giao Hàng FoodOrder Quận 1','shipper',1,NULL,NULL,'2026-10-05 07:14:16','2026-10-05 07:14:16');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `discount_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(12,0) NOT NULL,
  `min_order_value` decimal(12,0) NOT NULL DEFAULT '0',
  `max_discount_amount` decimal(12,0) DEFAULT NULL,
  `usage_limit` int unsigned DEFAULT NULL,
  `used_count` int unsigned NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`),
  KEY `vouchers_is_active_starts_at_ends_at_index` (`is_active`,`starts_at`,`ends_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
INSERT INTO `vouchers` VALUES (1,'ADMIN','Voucher Của ADmin','percent',100,0,NULL,99,0,'2026-10-05 06:53:00','2028-06-05 06:53:00',1,'2026-10-05 06:53:29','2026-10-05 07:23:12');
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'food_order'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-05 14:32:38
