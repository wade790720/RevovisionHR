-- MySQL dump 10.13  Distrib 5.6.17, for Win64 (x86_64)
--
-- Host: localhost    Database: new_hr_qa
-- ------------------------------------------------------
-- Server version	5.6.17

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `rv_attendance`
--

DROP TABLE IF EXISTS `rv_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(6) DEFAULT '0' COMMENT '員工代號 - 系統内碼 rv_staff.id',
  `date` date DEFAULT NULL COMMENT '日期',
  `checkin_hours` time DEFAULT NULL COMMENT '上班',
  `checkout_hours` time DEFAULT NULL COMMENT '下班',
  `work_hours_total` float(5,2) NOT NULL DEFAULT '0.00' COMMENT '工時',
  `late` int(2) NOT NULL DEFAULT '0' COMMENT '考勤狀況 - 遲到',
  `early` int(2) NOT NULL DEFAULT '0' COMMENT '考勤狀況 - 早退',
  `nocard` int(2) NOT NULL DEFAULT '0' COMMENT '考勤狀況 - 忘卡',
  `remark` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '假日資料備註欄',
  `vocation_hours` float(5,2) NOT NULL DEFAULT '0.00' COMMENT '請假時數',
  `vocation_from` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '請假時數 - 開始時間',
  `vocation_to` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '請假時數 - 結束時間',
  `overtime_hours` float(5,2) NOT NULL DEFAULT '0.00' COMMENT '加班時數',
  `overtime_from` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '加班時數 - 開始時間',
  `overtime_to` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '加班時數 - 結束時間',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_attendance`
--

LOCK TABLES `rv_attendance` WRITE;
/*!40000 ALTER TABLE `rv_attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_config_cyclical`
--

DROP TABLE IF EXISTS `rv_config_cyclical`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_config_cyclical` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL,
  `month` int(4) NOT NULL,
  `day_start` int(2) NOT NULL DEFAULT '21',
  `day_end` int(2) NOT NULL DEFAULT '20',
  `day_cut_addition` int(2) NOT NULL DEFAULT '2' COMMENT '按開始審核階段的幾日後是結算日',
  `cut_off_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '截止日期',
  `monthly_launched` int(2) DEFAULT '0' COMMENT '月績效開關 啟動=1,關閉=0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_config_cyclical`
--

LOCK TABLES `rv_config_cyclical` WRITE;
/*!40000 ALTER TABLE `rv_config_cyclical` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_config_cyclical` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_department`
--

DROP TABLE IF EXISTS `rv_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lv` int(11) NOT NULL DEFAULT '0',
  `unit_id` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `supervisor_staff_id` int(11) NOT NULL DEFAULT '0',
  `manager_staff_id` int(11) NOT NULL DEFAULT '0',
  `duty_shift` int(11) DEFAULT NULL,
  `upper_id` int(11) NOT NULL DEFAULT '1',
  `enable` int(2) DEFAULT '1' COMMENT '啟用=1,關閉=0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_department`
--

LOCK TABLES `rv_department` WRITE;
/*!40000 ALTER TABLE `rv_department` DISABLE KEYS */;
INSERT INTO `rv_department` VALUES (1,1,'A00','運維中心',1,1,0,0,1,'2017-04-06 03:49:52'),(2,2,'B00','架構發展事業部',1,2,0,1,1,'0000-00-00 00:00:00'),(3,2,'F00','稽核部',1,0,0,1,1,'2017-04-06 04:11:54'),(4,2,'G00','風險管理部',1,0,0,1,1,'0000-00-00 00:00:00'),(5,2,'D00','營運系統部',1,0,0,1,1,'0000-00-00 00:00:00'),(6,2,'C00','客戶服務部',1,5,0,1,1,'2017-03-29 14:22:02'),(7,3,'B20','總務行政處',2,116,0,2,1,'2017-04-06 03:57:07'),(8,3,'D10','系統管理處',1,0,0,5,1,'0000-00-00 00:00:00'),(9,3,'D50','開發處',1,0,0,5,1,'2017-03-29 14:41:08'),(10,3,'G10','風險管理處',1,0,0,4,1,'0000-00-00 00:00:00'),(11,3,'C10','客戶服務處',5,0,0,6,1,'0000-00-00 00:00:00'),(12,3,'D31','資料庫管理處',1,0,0,5,1,'2017-04-07 06:43:53'),(13,3,'B10','人力資源處',2,0,0,2,1,'0000-00-00 00:00:00'),(14,3,'D20','技術支援處',1,0,0,5,1,'0000-00-00 00:00:00'),(15,3,'F10','稽查訓練處',1,85,0,3,1,'0000-00-00 00:00:00'),(16,4,'D31','資料管理組',1,71,0,12,1,'2017-04-07 06:53:54'),(17,4,'C12','值班客服組',5,15,0,11,1,'0000-00-00 00:00:00'),(18,4,'D26','值班技術四組',1,70,0,14,1,'0000-00-00 00:00:00'),(19,4,'D23','值班技術一組',1,54,0,14,1,'0000-00-00 00:00:00'),(20,4,'C11','專屬客服組',5,7,0,11,1,'0000-00-00 00:00:00'),(21,4,'D11','系統管理組',1,0,0,8,1,'0000-00-00 00:00:00'),(22,4,'D24','值班技術二組',1,66,0,14,1,'2017-03-29 14:39:25'),(23,4,'D51','開發組',1,80,0,9,1,'2017-03-29 14:40:48'),(24,4,'C13','聊天管理組',5,33,0,11,1,'0000-00-00 00:00:00'),(25,4,'D25','值班技術三組',1,68,0,14,1,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `rv_department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_monthly_processing`
--

DROP TABLE IF EXISTS `rv_monthly_processing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_monthly_processing` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status_code` int(6) DEFAULT '1',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '月表類型 1=主管, 2=一般',
  `commited` int(2) DEFAULT '0',
  `created_staff_id` int(6) NOT NULL,
  `created_department_id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `month` int(4) NOT NULL,
  `owner_staff_id` int(11) NOT NULL DEFAULT '0' COMMENT '目前報告所有權 - 報告在誰手裏',
  `owner_department_id` int(6) DEFAULT '1',
  `path_staff_id` varchar(63) NOT NULL DEFAULT '[1]' COMMENT '單子的送審路程[]',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_monthly_processing`
--

LOCK TABLES `rv_monthly_processing` WRITE;
/*!40000 ALTER TABLE `rv_monthly_processing` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_monthly_processing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_monthly_report`
--

DROP TABLE IF EXISTS `rv_monthly_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_monthly_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(6) NOT NULL DEFAULT '0' COMMENT '員工代號 - 系統内碼 rv_staff.id',
  `year` int(4) NOT NULL DEFAULT '2016' COMMENT '評核年度',
  `month` int(2) NOT NULL COMMENT '評核月份',
  `quality` int(5) NOT NULL DEFAULT '5' COMMENT '工作品質',
  `completeness` int(5) NOT NULL DEFAULT '5' COMMENT '工作績效',
  `responsibility` int(5) NOT NULL DEFAULT '5' COMMENT '責任感',
  `cooperation` int(5) NOT NULL DEFAULT '5' COMMENT '配合度',
  `attendance` int(5) NOT NULL DEFAULT '5' COMMENT '時間觀念出席率',
  `addedValue` int(5) NOT NULL DEFAULT '0' COMMENT '特殊貢獻-依照貢獻度額外加分',
  `mistake` int(5) NOT NULL DEFAULT '0' COMMENT '重大缺失-若有重大疏失依照情節予以扣分或獎金不予發放',
  `total` int(5) NOT NULL COMMENT '總分 - 應該是不需要，因爲是計算出來的',
  `comment_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '對應評論的ID',
  `status` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT '是否已提交',
  `releaseFlag` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT '已審批與否 - 可否送交給總部',
  `bonus` int(2) DEFAULT '1' COMMENT '當月是否發放獎金 ■是=1,□否=0',
  `processing_id` int(11) DEFAULT '0',
  `owner_staff_id` int(11) NOT NULL COMMENT '目前報告所有權 - 報告在誰手裏',
  `owner_department_id` int(6) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `staff` (`staff_id`) USING BTREE,
  KEY `report_pid` (`processing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_monthly_report`
--

LOCK TABLES `rv_monthly_report` WRITE;
/*!40000 ALTER TABLE `rv_monthly_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_monthly_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_monthly_report_leader`
--

DROP TABLE IF EXISTS `rv_monthly_report_leader`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_monthly_report_leader` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(6) NOT NULL DEFAULT '0' COMMENT '員工代號 - 系統内碼 rv_staff.id',
  `year` int(4) NOT NULL DEFAULT '2016' COMMENT '評核年度',
  `month` int(2) NOT NULL COMMENT '評核月份',
  `target` int(5) NOT NULL DEFAULT '5' COMMENT '目標達成率',
  `quality` int(5) NOT NULL DEFAULT '5' COMMENT '工作品質',
  `method` int(5) NOT NULL DEFAULT '5' COMMENT '工作方法',
  `error` int(5) NOT NULL DEFAULT '5' COMMENT '出錯率',
  `backtrack` int(5) NOT NULL DEFAULT '5' COMMENT '進度追蹤/回報',
  `planning` int(5) NOT NULL DEFAULT '5' COMMENT '企劃能力',
  `execute` int(5) NOT NULL DEFAULT '5' COMMENT '執行力',
  `decision` int(5) NOT NULL DEFAULT '5' COMMENT '判斷力',
  `resilience` int(5) NOT NULL DEFAULT '5' COMMENT '應變能力',
  `attendance` int(5) NOT NULL DEFAULT '5' COMMENT '出缺勤率',
  `attendance_members` int(5) NOT NULL DEFAULT '5' COMMENT '組員出缺勤率',
  `addedValue` int(5) NOT NULL DEFAULT '0' COMMENT '特殊貢獻-依照貢獻度額外加分',
  `mistake` int(5) NOT NULL DEFAULT '0' COMMENT '重大缺失-若有重大疏失依照情節予以扣分或獎金不予發放',
  `total` int(5) NOT NULL COMMENT '總分 - 應該是不需要，因爲是計算出來的',
  `comment_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '對應評論的ID',
  `status` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT '是否已提交',
  `releaseFlag` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT '已審批與否 - 可否往下一關',
  `bonus` int(2) DEFAULT '1' COMMENT '當月是否發放獎金 ■是=1,□否=0',
  `processing_id` int(11) DEFAULT '0',
  `owner_staff_id` int(11) NOT NULL DEFAULT '0' COMMENT '目前報告所有權 - 報告在誰手裏',
  `owner_department_id` int(6) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `staff` (`staff_id`) USING BTREE,
  KEY `report_leader_pid` (`processing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_monthly_report_leader`
--

LOCK TABLES `rv_monthly_report_leader` WRITE;
/*!40000 ALTER TABLE `rv_monthly_report_leader` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_monthly_report_leader` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_record_monthly_processing`
--

DROP TABLE IF EXISTS `rv_record_monthly_processing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_record_monthly_processing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operating_staff_id` int(11) NOT NULL DEFAULT '0',
  `target_staff_id` int(11) NOT NULL,
  `processing_id` int(11) NOT NULL,
  `action` enum('launch','commit','return','done','cancel','other') COLLATE utf8_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `changed_json` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `record_monthly_update` (`update_date`),
  KEY `processing_id` (`processing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_record_monthly_processing`
--

LOCK TABLES `rv_record_monthly_processing` WRITE;
/*!40000 ALTER TABLE `rv_record_monthly_processing` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_record_monthly_processing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_record_monthly_report`
--

DROP TABLE IF EXISTS `rv_record_monthly_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_record_monthly_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operating_staff_id` int(11) NOT NULL DEFAULT '0',
  `report_id` int(11) NOT NULL,
  `report_type` int(2) NOT NULL DEFAULT '1' COMMENT '月表類型 1=主管, 2=一般',
  `changed_json` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `record_monthly_update` (`update_date`),
  KEY `report_id` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_record_monthly_report`
--

LOCK TABLES `rv_record_monthly_report` WRITE;
/*!40000 ALTER TABLE `rv_record_monthly_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_record_monthly_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_record_personal_comment`
--

DROP TABLE IF EXISTS `rv_record_personal_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_record_personal_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_staff_id` int(11) NOT NULL,
  `target_staff_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL COMMENT '對應哪一個月報表',
  `report_type` int(2) NOT NULL DEFAULT '1' COMMENT '對應主管或組員',
  `content` varchar(255) DEFAULT '',
  `status` int(2) DEFAULT '1' COMMENT '記錄狀態 1=正常 0=關閉',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `create_time` (`create_time`),
  KEY `report_id` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_record_personal_comment`
--

LOCK TABLES `rv_record_personal_comment` WRITE;
/*!40000 ALTER TABLE `rv_record_personal_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_record_personal_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_record_personal_comment_changed`
--

DROP TABLE IF EXISTS `rv_record_personal_comment_changed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_record_personal_comment_changed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL COMMENT '對應哪一個評論',
  `create_staff_id` int(11) NOT NULL,
  `target_staff_id` int(11) NOT NULL,
  `content` varchar(255) DEFAULT '',
  `change_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_record_personal_comment_changed`
--

LOCK TABLES `rv_record_personal_comment_changed` WRITE;
/*!40000 ALTER TABLE `rv_record_personal_comment_changed` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_record_personal_comment_changed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_record_staff`
--

DROP TABLE IF EXISTS `rv_record_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_record_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operating_staff_id` int(11) NOT NULL DEFAULT '0',
  `staff_id` int(11) NOT NULL,
  `changed_json` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `record_monthly_update` (`update_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_record_staff`
--

LOCK TABLES `rv_record_staff` WRITE;
/*!40000 ALTER TABLE `rv_record_staff` DISABLE KEYS */;
/*!40000 ALTER TABLE `rv_record_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_staff`
--

DROP TABLE IF EXISTS `rv_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_no` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT '員工工號',
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '職類',
  `title_id` int(2) DEFAULT '1',
  `post` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '職務',
  `post_id` int(2) DEFAULT '1',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '中文性名',
  `name_en` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '英文性名',
  `account` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '登入帳號',
  `passwd` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '12121212' COMMENT '登入密碼',
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '聯絡用郵件地址',
  `lv` int(2) NOT NULL COMMENT '組織階層 米奇開始為1，Eric 為2.....依此類推',
  `first_day` date NOT NULL DEFAULT '0000-00-00' COMMENT '到職日',
  `last_day` date DEFAULT '0000-00-00' COMMENT '離職日',
  `update_date` date DEFAULT '0000-00-00' COMMENT '最後更新日期',
  `status` enum('約聘','試用','正式','離職') COLLATE utf8_unicode_ci NOT NULL DEFAULT '試用' COMMENT '在職狀態',
  `status_id` int(2) DEFAULT '1',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `is_leader` int(2) NOT NULL DEFAULT '0' COMMENT '是否為單位長',
  `is_admin` int(2) NOT NULL DEFAULT '0' COMMENT '是否為管理者',
  `rank` int(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_no` (`staff_no`)
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_staff`
--

LOCK TABLES `rv_staff` WRITE;
/*!40000 ALTER TABLE `rv_staff` DISABLE KEYS */;
INSERT INTO `rv_staff` VALUES (1,'R001','決策人員',1,'經理',1,'侯統揚','Mickey','mickey.hou','24962821','mickey.hou@rv88.tw',1,'2012-11-01','0000-00-00','2017-04-06','正式',1,1,1,0,0),(2,'R019','部級主管',2,'經理',1,'蘇穎珊','Susanss','susan.su','24962821','susan.su@rv88.tw',2,'2013-12-02','0000-00-00','0000-00-00','正式',1,2,1,1,0),(3,'R050','一般人員(行政/專技)',5,'資深人事專員',3,'吳美君','Mavis','mavis.wu','24962821','mavis.wu@rv88.tw',5,'2015-04-01','0000-00-00','0000-00-00','正式',1,13,0,1,0),(4,'R115','處級主管',3,'行政專員',5,'鄧幼華','Liz','liz.teng','12121212','liz.teng@rv88.tw',3,'2016-03-17','0000-00-00','2017-04-07','正式',1,13,0,1,0),(5,'R031','部級主管',2,'經理',1,'林子雲','Eric','eric.lin','24962821','eric.lin@rv88.tw',2,'2014-12-01','0000-00-00','0000-00-00','正式',1,6,1,0,0),(6,'R010','一般人員(行政/專技)',5,'客服專員',11,'王姸茹','Zoe','zoe.wang','12121212','zoe.wang@rv88.tw',5,'2013-04-15','0000-00-00','0000-00-00','正式',1,20,0,0,0),(7,'R020','組長',4,'客服專員',11,'李威揚','Vincent','vincent.lee','24962821','vincent.lee@rv88.tw',4,'2014-04-09','0000-00-00','0000-00-00','正式',1,20,1,0,0),(8,'R027','一般人員(行政/專技)',5,'客服專員',11,'林欣馨','Shin','shin.lin','12121212','shin.lin@rv88.tw',5,'2014-10-01','0000-00-00','0000-00-00','正式',1,20,0,0,0),(9,'R033','一般人員(行政/專技)',5,'客服專員',11,'朱書賢','Kevin','kevin.chu','12121212','kevin.chu@rv88.tw',5,'2014-12-02','0000-00-00','0000-00-00','正式',1,20,0,0,0),(10,'R116','一般人員(行政/專技)',5,'客服專員',11,'李孝龍','Bruce','bruce.lee','12121212','bruce.lee@rv88.tw',5,'2016-03-21','0000-00-00','0000-00-00','正式',1,20,0,0,0),(11,'R015','一般人員(行政/專技)',5,'客服專員',11,'甘博仁','Ken','ken.kan','12121212','ken.kan@rv88.tw',5,'2013-10-02','0000-00-00','0000-00-00','正式',1,17,0,0,0),(12,'R017','一般人員(行政/專技)',5,'客服專員',11,'黃忠信','Jeff','jeff.huang','12121212','jeff.huang@rv88.tw',5,'2013-11-12','0000-00-00','0000-00-00','正式',1,17,0,0,0),(13,'R022','一般人員(行政/專技)',5,'客服專員',11,'林世創','Strong','strong.lin','987454','strong.lin@rv88.tw',5,'2014-08-04','0000-00-00','0000-00-00','正式',1,17,0,0,0),(14,'R023','一般人員(行政/專技)',5,'客服專員',11,'楊麗萍','Crystal','crystal.yang','12121212','crystal.yang@rv88.tw',5,'2014-08-04','0000-00-00','0000-00-00','正式',1,17,0,0,0),(15,'R024','組長',4,'客服專員',11,'許雅玲','Rita','rita.hsu','12121212','rita.hsu@rv88.tw',4,'2014-08-04','0000-00-00','2017-01-01','正式',1,17,1,0,0),(16,'R025','一般人員(行政/專技)',5,'客服專員',11,'林碩人','David','david.lin','12121212','david.lin@rv88.tw',5,'2014-08-04','0000-00-00','0000-00-00','正式',1,17,0,0,0),(17,'R032','一般人員(行政/專技)',5,'客服專員',11,'高君龢','Herman','herman.gao','12121212','herman.gao@rv88.tw',5,'2014-12-02','0000-00-00','0000-00-00','正式',1,17,0,0,0),(18,'R041','一般人員(行政/專技)',5,'客服專員',11,'廖逸楷','Kai','kai.liao','12121212','kai.liao@rv88.tw',5,'2015-01-07','0000-00-00','0000-00-00','正式',1,17,0,0,0),(19,'R042','一般人員(行政/專技)',5,'客服專員',11,'蔣雄貴','Kuei','kuei.jiang','12121212','kuei.jiang@rv88.tw',5,'2015-01-07','0000-00-00','0000-00-00','正式',1,17,0,0,0),(20,'R043','一般人員(行政/專技)',5,'客服專員',11,'鄒宜君','Candy','candy.tsou','12121212','candy.tsou@rv88.tw',5,'2015-01-07','0000-00-00','0000-00-00','正式',1,17,0,0,0),(21,'R044','一般人員(行政/專技)',5,'客服專員',11,'余佩霖','Peilin','peilin.yu','12121212','peilin.yu@rv88.tw',5,'2015-01-07','0000-00-00','0000-00-00','正式',1,17,0,0,0),(22,'R046','一般人員(行政/專技)',5,'客服專員',11,'林育賢','Matt','matt.lin','12121212','matt.lin@rv88.tw',5,'2015-01-19','0000-00-00','0000-00-00','正式',1,17,0,0,0),(23,'R047','一般人員(行政/專技)',5,'客服專員',11,'鍾瀅','Saya','saya.zhun','','zhun.saya@rv88.tw',5,'2015-03-04','0000-00-00','0000-00-00','正式',1,17,0,0,0),(24,'R055','一般人員(行政/專技)',5,'客服專員',11,'譚奇勝','Jacob','jacob.tan','12121212','jacob.tan@rv88.tw',5,'2015-04-07','0000-00-00','0000-00-00','正式',1,17,0,0,0),(25,'R056','一般人員(行政/專技)',5,'客服專員',11,'高薇雯','Evelyn','evelyn.kao','12121212','evelyn.kao@rv88.tw',5,'2015-04-07','0000-00-00','0000-00-00','正式',1,17,0,0,0),(27,'R075','一般人員(行政/專技)',5,'客服專員',11,'洪廷霖','Lin','lin.hong','12121212','lin.hong@rv88.tw',5,'2015-07-14','0000-00-00','0000-00-00','正式',1,17,0,0,0),(28,'R082','一般人員(行政/專技)',5,'客服專員',11,'林宛諭','Alison','alison.lin','12121212','alison.lin@rv88.tw',5,'2015-08-03','0000-00-00','0000-00-00','正式',1,17,0,0,0),(29,'R100','一般人員(行政/專技)',5,'客服專員',11,'葉庭瑋','Willie','willie.yeh','12121212','willie.yeh@rv88.tw',5,'2015-10-05','0000-00-00','0000-00-00','正式',1,17,0,0,0),(30,'R104','一般人員(行政/專技)',5,'客服專員',11,'黃志衡','George','george.huang','1212w1212','george.huang@rv88.tw',5,'2015-11-02','0000-00-00','0000-00-00','正式',1,17,0,0,0),(31,'R125','一般人員(行政/專技)',5,'客服專員',11,'徐澄','Raven','raven.hsu','12121212','raven.hsu@rv88.tw',5,'2016-06-01','0000-00-00','0000-00-00','正式',1,17,0,0,0),(32,'R128','一般人員(行政/專技)',5,'稽核專員',9,'丁于希','Yusi','yusi.ting','12121212','yusi.ting@rv88.tw',5,'2016-06-13','0000-00-00','0000-00-00','正式',1,15,0,0,0),(33,'R013','組長',4,'客服專員',11,'焦小紅','Jessie','jessie.chiao','24962821','jessie.chiao@rv88.tw',4,'2013-08-01','0000-00-00','0000-00-00','正式',1,24,1,0,0),(34,'R072','其他職員',7,'助理',12,'陳星羽','Annie','annie.chen','12121212','annie.chen@rv88.tw',6,'2015-07-13','0000-00-00','0000-00-00','正式',1,24,0,0,0),(35,'R080','其他職員',7,'助理',12,'張奇勳','Tristan','tristan.chang','12121212','tristan.chang@rv88.tw',6,'2015-07-23','0000-00-00','0000-00-00','正式',1,24,0,0,0),(39,'R109','一般人員(行政/專技)',5,'客服專員',11,'李洪彰','Moore','moore.lee','qqqq123','moore.lee@rv88.tw',5,'2015-11-11','0000-00-00','0000-00-00','正式',1,17,0,0,0),(41,'R111','其他職員',7,'助理',12,'袁書豪','White','white.yuan','12121212','white.yuan@rv88.tw',6,'2015-12-14','0000-00-00','0000-00-00','正式',1,24,0,0,0),(43,'R117','約聘人員',6,'工讀生',13,'風善醴','Marcel','marcel.fong','12121212','marcel.fong@rv88.tw',6,'2016-03-23','2016-10-10','0000-00-00','離職',4,24,0,0,0),(44,'R124','約聘人員',6,'工讀生',13,'林宇彬','Rare','rare.lin','12121212','rare.lin@rv88.tw',6,'2016-05-23','0000-00-00','0000-00-00','離職',4,24,0,0,0),(49,'R029','一般人員(行政/專技)',5,'系統工程師',8,'黃瑞龍','Ray','ray.huang','12121212','ray.huang@rv88.tw',5,'2014-11-04','0000-00-00','0000-00-00','正式',1,21,0,0,0),(50,'R106','一般人員(行政/專技)',5,'系統工程師',8,'陳禹豪','David','david.chen','12121212','david.chen@rv88.tw',5,'2015-11-02','0000-00-00','0000-00-00','正式',1,21,0,0,0),(52,'R006','一般人員(行政/專技)',5,'技術支援工程師',10,'全士芃','Peter','peter.chuang22','12121212','peter.chuang@rv88.tw',5,'2013-03-04','0000-00-00','0000-00-00','正式',1,19,0,0,0),(53,'R007','一般人員(行政/專技)',5,'技術支援工程師',10,'林子翔','Pokiqq','poki.lin','12121212','poki.lin@rv88.tw',5,'2013-04-01','0000-00-00','0000-00-00','正式',1,19,0,0,0),(54,'R008','組長',4,'技術支援工程師',10,'黃海威','Turtle','turtle.huang','24962821','turtle.huang@rv88.tw',4,'2013-04-01','0000-00-00','0000-00-00','正式',1,19,1,0,0),(55,'R011','一般人員(行政/專技)',5,'技術支援工程師',10,'何俊達','Dada','dada.ho','12121212','dadazax.ho@rv88.tw',5,'2013-05-02','0000-00-00','0000-00-00','正式',1,19,0,0,0),(56,'R026','一般人員(行政/專技)',5,'技術支援工程師',10,'趙祐晟','Johnson','johnson.chao','12121212','johnson.chao@rv88.tw',5,'2014-10-01','0000-00-00','0000-00-00','正式',1,19,0,0,0),(57,'R059','一般人員(行政/專技)',5,'技術支援工程師',10,'陳昱丞','Yuchen','yuchen.chen','12121212','yuchen.chen@rv88.tw',5,'2015-04-15','0000-00-00','0000-00-00','正式',1,19,0,0,0),(58,'R063','其他職員',7,'助理',12,'黃心潔','Lulu','lulu.huang','12121212','lulu.huang@rv88.tw',6,'2015-05-11','0000-00-00','0000-00-00','離職',4,15,0,0,0),(59,'R094','一般人員(行政/專技)',5,'技術支援工程師',10,'王智威','Peter','peter.wang','12121212','peter.wang@rv88.tw',5,'2015-09-14','0000-00-00','0000-00-00','正式',1,19,0,0,0),(60,'R113','一般人員(行政/專技)',5,'技術支援工程師',10,'張士駿','Grey','grey.chang','12121212','grey.chang@rv88.tw',5,'2016-03-01','0000-00-00','0000-00-00','正式',1,19,0,0,0),(62,'R120','一般人員(行政/專技)',5,'技術支援工程師',10,'郭庭維','Duke','duke.kuo','12121212','duke.kuo@rv88.tw',5,'2016-05-03','0000-00-00','0000-00-00','正式',1,18,0,0,0),(65,'R123','一般人員(行政/專技)',5,'技術支援工程師',10,'賴佳昌','Exia','exia.lai','12121212','exia.lai@rv88.tw',5,'2016-05-16','0000-00-00','0000-00-00','正式',1,19,0,0,0),(66,'R049','組長',4,'技術支援工程師',10,'宋長洲','Ryan','ryan.sung','24962821','ryan.sung@rv88.tw',4,'2015-03-09','0000-00-00','0000-00-00','正式',1,22,1,0,0),(67,'R069','一般人員(行政/專技)',5,'技術支援工程師',10,'許嘉維','Vic','vic.hsu','12121212','vic.hsu@rv88.tw',5,'2015-07-13','0000-00-00','0000-00-00','正式',1,22,0,0,0),(68,'R021','組長',4,'資深技術支援工程師',2,'楊伯麟','Luke','luke.yang','24962821','luke.yang@rv88.tw',4,'2014-06-18','0000-00-00','0000-00-00','正式',1,25,1,0,0),(69,'R052','一般人員(行政/專技)',5,'技術支援工程師',10,'林永鋒','Dennis','dennis.lin','12121212','dennis.lin@rv88.tw',5,'2015-04-01','0000-00-00','0000-00-00','正式',1,25,0,0,0),(70,'R018','組長',4,'技術支援工程師',10,'邱柏洋','Quake','quake.chiu','24962821','quake.chiu@rv88.tw',4,'2013-12-02','0000-00-00','0000-00-00','正式',1,18,1,0,0),(71,'R002','組長',4,'資料庫管理工程師',4,'張裕','Richard','richard.chang','24962821','richard.chang@rv88.tw',4,'2012-11-01','0000-00-00','2017-04-07','正式',1,16,1,0,0),(72,'R053','一般人員(行政/專技)',5,'資料庫操作員',14,'徐銘鴻','Leo','leo.hsu','12121212','leo.hsu@rv88.tw',5,'2015-04-07','0000-00-00','0000-00-00','正式',1,16,0,0,0),(73,'R067','一般人員(行政/專技)',5,'資料庫管理工程師',4,'王嘉偉','Falcon','falcon.wang','12121212','falcon.wang@rv88.tw',5,'2015-07-01','0000-00-00','0000-00-00','正式',1,16,0,0,0),(76,'R034','一般人員(行政/專技)',5,'網頁設計師',6,'吳敏絹','Joan','joan.wu','12121212','joan.wu@rv88.tw',5,'2014-12-02','0000-00-00','0000-00-00','正式',1,23,0,0,0),(77,'R036','組長',4,'網頁程式設計師',7,'賴政男','Jemmy','jemmy.lai','24962821','jemmy.lai@rv88.tw',4,'2014-12-15','0000-00-00','0000-00-00','正式',1,23,0,0,0),(78,'R039','一般人員(行政/專技)',5,'網頁程式設計師',7,'張景翔','Chris','chris.chang','12121212','chris.chang@rv88.tw',5,'2014-12-16','0000-00-00','0000-00-00','正式',1,23,0,0,0),(79,'R040','一般人員(行政/專技)',5,'網頁程式設計師',7,'陳家德','Jader','jader.chen','24962821','jader.chen@rv88.tw',5,'2014-12-22','0000-00-00','0000-00-00','正式',1,23,0,0,0),(80,'R058','一般人員(行政/專技)',5,'網頁程式設計師',7,'莊格維','Snow','snow.jhung','12121212','snow.jhung@rv88.tw',4,'2015-04-13','0000-00-00','0000-00-00','正式',1,23,1,0,0),(81,'R064','一般人員(行政/專技)',5,'網頁程式設計師',7,'程晉鴻','Castle','castle.cheng','12121212','castle.cheng@rv88.tw',5,'2015-06-01','0000-00-00','0000-00-00','正式',1,23,0,0,0),(82,'R066','一般人員(行政/專技)',5,'網頁程式設計師',7,'戴妙妃','Sophia','sophia.tai','12121212','sophia.tai@rv88.tw',5,'2015-06-22','0000-00-00','0000-00-00','正式',1,23,0,0,0),(83,'R096','一般人員(行政/專技)',5,'網頁程式設計師',7,'朱德溎','Wade','wade.zhu','12121212','wade.zhu@rv88.tw',5,'2015-09-21','0000-00-00','0000-00-00','正式',1,23,0,0,0),(84,'R101','一般人員(行政/專技)',5,'網頁程式設計師',7,'詹明儒','James','james.chan','12121212','james.chan@rv88.tw',5,'2015-10-19','2016-11-01','0000-00-00','離職',4,23,0,0,0),(85,'R009','處級主管',3,'稽核專員',9,'楊劭儀','Hako','hako.yang','24962821','hako.yang@rv88.tw',3,'2013-04-15','0000-00-00','2017-04-07','正式',1,15,1,0,5),(86,'R035','一般人員(行政/專技)',5,'稽核專員',9,'陳孟成','Roy','roy.chen','12121212','roy.chen@rv88.tw',5,'2014-12-02','0000-00-00','0000-00-00','正式',1,15,0,0,0),(87,'R037','一般人員(行政/專技)',5,'稽核專員',9,'黃子寧','Ruiza','ruiza.huang','12121212','ruiza.huang@rv88.tw',5,'2014-12-15','0000-00-00','0000-00-00','正式',1,15,0,0,0),(89,'R139','一般人員(行政/專技)',5,'網頁設計師',6,'林威逸','Bryan','bryan.lin','12121212','bryan.lin@rv88.tw',5,'2016-08-03','0000-00-00','0000-00-00','正式',1,23,0,0,0),(100,'R133','一般人員(行政/專技)',5,'客服專員',11,'李政庭','Tim','tim.lee','12121212','tim.lee@rv88.tw',5,'2016-07-04','0000-00-00','0000-00-00','試用',3,17,0,0,0),(103,'R136','一般人員(行政/專技)',5,'客服專員',11,'陳葭','Jasmine','jasmine.chen','12121212','jasmine.chen@rv88.tw',5,'2016-07-18','0000-00-00','0000-00-00','試用',3,17,0,0,0),(105,'R140','一般人員(行政/專技)',5,'系統工程師',8,'蘇鼎棋','Dennis','dennis.su','12121212','dennis.su@rv88.tw',5,'2016-08-15','2016-10-04','0000-00-00','離職',4,21,0,0,0),(106,'R141','約聘人員',6,'工讀生',13,'鄭嘉宏','Red','red.cheng','12121212','red.cheng@rv88.tw',6,'2016-08-29','0000-00-00','0000-00-00','約聘',2,24,0,0,0),(107,'R143','約聘人員',6,'工讀生',13,'游淮','Huai','huai.yu','12121212','huai.yu@rv88.tw',6,'2016-08-30','0000-00-00','0000-00-00','離職',4,24,0,0,0),(108,'R144','約聘人員',6,'工讀生',13,'周晉平','Leo','leo.chou','12121212','leo.chou@rv88.tw',6,'2016-08-31','0000-00-00','0000-00-00','約聘',2,24,0,0,0),(109,'R145','約聘人員',6,'工讀生',13,'孫維謙','David','david.sun','12121212','david.sun@rv88.tw',6,'2016-08-31','2016-09-26','0000-00-00','離職',4,24,0,0,0),(110,'R146','一般人員(行政/專技)',5,'客服專員',11,'張佩芬','Fanny','fanny.chang','12121212','fanny.chang@rv88.tw',5,'2016-09-01','2016-10-06','0000-00-00','離職',4,17,0,0,0),(111,'R147','一般人員(行政/專技)',5,'技術支援工程師',10,'蔡偉豪','Andy','andy.tsai','12121212','andy.tsai@rv88.tw',5,'2016-09-05','0000-00-00','0000-00-00','正式',1,19,0,0,0),(116,'R148','處級主管',3,'行政專員',5,'吳雅雯','Lucy','lucy.wu','12121212','lucy.wu@rv88.tw',3,'2014-07-14','0000-00-00','2017-04-07','正式',1,7,1,0,0),(117,'R151','一般人員(行政/專技)',5,'客服專員',11,'趙伯霖','Poe','poe.chao','12121212','poe.chao@rv88.tw',5,'2016-10-11','0000-00-00','0000-00-00','離職',4,17,0,0,0),(119,'R149','約聘人員',6,'工讀生',13,'謝易桓','Allen','allen.hsieh','12121212','allen.hsieh@rv88.tw',6,'2016-09-22','0000-00-00','0000-00-00','離職',4,24,0,0,0),(120,'R150','約聘人員',6,'工讀生',13,'林榆傑','Jay','jay.lin','12121212','jay.lin@rv88.tw',6,'2016-10-03','0000-00-00','0000-00-00','約聘',2,24,0,0,0),(121,'R152','一般人員(行政/專技)',5,'技術支援工程師',10,'馬瑞成','Ricky','ricky.ma','12121212','ricky.ma@rv88.tw',5,'2016-10-11','0000-00-00','0000-00-00','試用',3,19,0,0,0),(122,'R153','一般人員(行政/專技)',5,'技術支援工程師',10,'張庭維','Jeffrey','jeffrey.chang','12121212','jeffrey.chang@rv88.tw',5,'2016-10-17','0000-00-00','0000-00-00','離職',4,19,0,0,0),(123,'R134','約聘人員',6,'工讀生',13,'林念誼','Evalin','eva.lin','12121212','eva.lin@rv88.tw',6,'2016-07-04','0000-00-00','0000-00-00','正式',1,2,0,0,0),(124,'R045','一般人員(行政/專技)',5,'風控專員',15,'張振華','Walter','walter.chang','12121212','walter.chang@rv88.tw',5,'2015-01-07','0000-00-00','2016-12-01','正式',1,10,0,0,0),(147,'R154','一般人員(行政/專技)',5,'系統工程師',8,'洪辰錐','CJ Hungww','cjhung','12121212','cj.hung@rv88.tw',5,'2016-10-24','0000-00-00','0000-00-00','試用',3,21,0,0,0),(148,'R155','約聘人員',6,'工讀生',13,'楊東諺','DonYan Yang','donyang.yang','12121212','donyang.yang@rv88.tw',6,'2016-10-24','0000-00-00','0000-00-00','約聘',2,24,0,0,0),(149,'R156','一般人員(行政/專技)',5,'技術支援工程師',10,'楊程筑','Eric Yang','eric.yang','12121212','eric.yang@rv88.tw',5,'2016-11-01','0000-00-00','0000-00-00','試用',3,18,0,0,0),(150,'R157','一般人員(行政/專技)',5,'技術支援工程師',10,'李承哲','Fred Lee','fred.lee','12121212','fred.lee@rv88.tw',5,'2016-11-01','0000-00-00','0000-00-00','試用',3,18,0,0,0),(199,'R158','約聘人員',6,'工讀生',13,'林鼎鈞','Jun','jun.lin','12121212','jun.lin@rv88.tw',6,'2016-11-21','0000-00-00','0000-00-00','約聘',2,24,0,0,0),(200,'R159','約聘人員',6,'工讀生',13,'臧婕琴','Justine','justine.tsang','12121212','justine.tsang@rv88.tw',6,'2016-11-25','0000-00-00','0000-00-00','離職',4,24,0,0,0),(201,'R160','約聘人員',6,'工讀生',13,'林哲賢','Jack','jack.lin','12121212','jack.lin@rv88.tw',6,'2016-11-25','0000-00-00','0000-00-00','約聘',2,24,0,0,0),(202,'R161','一般人員(行政/專技)',5,'技術支援工程師',10,'林佳穎','Joseph','joseph.lin','12121212','joseph.lin@rv88.tw',5,'2016-12-01','0000-00-00','0000-00-00','試用',3,25,0,0,0),(203,'R162','一般人員(行政/專技)',5,'風控專員',15,'陳時仲',' Aries','aries.chen','12121212','aries.chen@rv88.tw',5,'2016-12-01','0000-00-00','0000-00-00','試用',3,10,0,0,0),(204,'R163','約聘人員',6,'工讀生',13,'袁書慧','Piggy','piggy.yuan','12121212','piggy.yuan@rv88.tw',6,'2016-12-20','0000-00-00','0000-00-00','約聘',2,24,0,0,0),(205,'R164','一般人員(行政/專技)',5,'技術支援工程師',10,'謝文中','Donem','','121212','donem.hsieh@rv88.tw',5,'2017-01-09','0000-00-00','0000-00-00','試用',3,19,0,0,0);
/*!40000 ALTER TABLE `rv_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_staff_post`
--

DROP TABLE IF EXISTS `rv_staff_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_staff_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '職務',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_staff_post`
--

LOCK TABLES `rv_staff_post` WRITE;
/*!40000 ALTER TABLE `rv_staff_post` DISABLE KEYS */;
INSERT INTO `rv_staff_post` VALUES (1,'經理'),(2,'資深技術支援工程師'),(3,'資深人事專員'),(4,'資料庫管理工程師'),(5,'行政專員'),(6,'網頁設計師'),(7,'網頁程式設計師'),(8,'系統工程師'),(9,'稽核專員'),(10,'技術支援工程師'),(11,'客服專員'),(12,'助理'),(13,'工讀生'),(14,'資料庫操作員'),(15,'風控專員');
/*!40000 ALTER TABLE `rv_staff_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_staff_status`
--

DROP TABLE IF EXISTS `rv_staff_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_staff_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_staff_status`
--

LOCK TABLES `rv_staff_status` WRITE;
/*!40000 ALTER TABLE `rv_staff_status` DISABLE KEYS */;
INSERT INTO `rv_staff_status` VALUES (1,'正式'),(2,'約聘'),(3,'試用'),(4,'離職');
/*!40000 ALTER TABLE `rv_staff_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rv_staff_title_lv`
--

DROP TABLE IF EXISTS `rv_staff_title_lv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rv_staff_title_lv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '職類',
  `lv` int(2) DEFAULT '5',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rv_staff_title_lv`
--

LOCK TABLES `rv_staff_title_lv` WRITE;
/*!40000 ALTER TABLE `rv_staff_title_lv` DISABLE KEYS */;
INSERT INTO `rv_staff_title_lv` VALUES (1,'決策人員',1),(2,'部級主管',2),(3,'處級主管',3),(4,'組長',4),(5,'一般人員(行政/專技)',5),(6,'約聘人員',6),(7,'其他職員',6),(8,'無',99);
/*!40000 ALTER TABLE `rv_staff_title_lv` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-07 14:55:05
