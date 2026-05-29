-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: retail-lmx-db.cdk8wggounv9.ap-south-1.rds.amazonaws.com    Database: retail_dev
-- ------------------------------------------------------
-- Server version	8.4.7

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
-- Table structure for table `CustomerDetails`
--

DROP TABLE IF EXISTS `CustomerDetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `CustomerDetails` (
  `S No` int DEFAULT NULL,
  `S.No` int DEFAULT NULL,
  `NAME` varchar(46) DEFAULT NULL,
  `MOBILE NO` varchar(14) DEFAULT NULL,
  `GENDER` varchar(6) DEFAULT NULL,
  `ADDRESS` varchar(142) DEFAULT NULL,
  `AREA` varchar(17) DEFAULT NULL,
  `CITY` varchar(12) DEFAULT NULL,
  `DISTRICT` varchar(13) DEFAULT NULL,
  `Pincode` varchar(7) DEFAULT NULL,
  `STATE` varchar(14) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `access`
--

DROP TABLE IF EXISTS `access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `access` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_profile` int unsigned DEFAULT NULL,
  `id_menu` int unsigned DEFAULT NULL,
  `view` int unsigned DEFAULT NULL,
  `add` int unsigned DEFAULT NULL,
  `edit` int unsigned DEFAULT NULL,
  `delete` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `id_menu` (`id_menu`),
  KEY `id_profile` (`id_profile`),
  KEY `id_menu_2` (`id_menu`),
  KEY `id_profile_2` (`id_profile`),
  KEY `id_menu_3` (`id_menu`),
  KEY `id_profile_3` (`id_profile`)
) ENGINE=InnoDB AUTO_INCREMENT=20823 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `address` (
  `id_address` int unsigned NOT NULL AUTO_INCREMENT,
  `id_country` int unsigned DEFAULT NULL,
  `id_state` int unsigned DEFAULT NULL,
  `id_city` int unsigned DEFAULT NULL,
  `id_customer` int unsigned NOT NULL DEFAULT '0',
  `id_employee` int unsigned NOT NULL DEFAULT '0',
  `id_nominee` int unsigned NOT NULL DEFAULT '0',
  `id_agent` int unsigned DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `pincode` varchar(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `active` tinyint unsigned NOT NULL DEFAULT '1',
  `deleted` tinyint unsigned NOT NULL DEFAULT '0',
  `company_name` varchar(100) DEFAULT NULL,
  `added_by` int DEFAULT NULL COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5- Sync, 6-Import 7-Retail admin',
  PRIMARY KEY (`id_address`),
  UNIQUE KEY `AddIndex` (`id_address`,`id_customer`,`id_employee`),
  KEY `FK_address_employee` (`id_employee`),
  KEY `FK_address_customer` (`id_customer`),
  KEY `id_country` (`id_country`),
  KEY `id_state` (`id_state`),
  KEY `id_city` (`id_city`),
  KEY `id_agent` (`id_agent`),
  KEY `active` (`active`),
  KEY `id_customer` (`id_customer`),
  KEY `id_country_2` (`id_country`),
  KEY `id_state_2` (`id_state`),
  KEY `id_city_2` (`id_city`),
  KEY `id_customer_2` (`id_customer`),
  KEY `id_country_3` (`id_country`),
  KEY `id_state_3` (`id_state`),
  KEY `id_city_3` (`id_city`)
) ENGINE=InnoDB AUTO_INCREMENT=393 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agent`
--

DROP TABLE IF EXISTS `agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agent` (
  `id_agent` int unsigned NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL COMMENT 'cus register branch',
  `id_village` int DEFAULT NULL,
  `title` varchar(5) DEFAULT NULL,
  `initials` varchar(10) DEFAULT NULL,
  `firstname` varchar(32) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  `agent_code` varchar(45) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `cash_reward` decimal(10,2) NOT NULL DEFAULT '0.00',
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `active` tinyint unsigned NOT NULL DEFAULT '0',
  `date_of_birth` date DEFAULT NULL,
  `date_of_wed` date DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `id_address` int unsigned DEFAULT '0',
  `image` varchar(250) DEFAULT NULL,
  `bank_account_number` bigint DEFAULT NULL,
  `ifsc_code` varchar(15) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_acc_holder_name` varchar(50) DEFAULT NULL,
  `preferred_mode` int NOT NULL DEFAULT '1' COMMENT '1 - CASH , 2 - ONLINE',
  `bank_image` varchar(600) DEFAULT NULL COMMENT 'Statement of Account / Cancelled Cheque',
  `website_url` varchar(100) DEFAULT NULL,
  `facebook_url` varchar(100) DEFAULT NULL,
  `instagram_url` varchar(100) DEFAULT NULL,
  `twitter_url` varchar(100) DEFAULT NULL,
  `youtube_url` varchar(100) DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `notification` tinyint unsigned NOT NULL DEFAULT '0',
  `gst_number` varchar(50) NOT NULL,
  `religion` int DEFAULT NULL COMMENT 'religion of custmer',
  `kyc_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - KYC Completed',
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5- Sync',
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_agent`),
  UNIQUE KEY `mobile` (`mobile`),
  UNIQUE KEY `CusIndex` (`id_agent`,`firstname`,`lastname`,`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `appt_emp_allot`
--

DROP TABLE IF EXISTS `appt_emp_allot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appt_emp_allot` (
  `id_emp_allot` int NOT NULL AUTO_INCREMENT,
  `id_appt_request` int NOT NULL,
  `alloted_emp` int unsigned NOT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_emp_allot`),
  KEY `id_appt_request` (`id_appt_request`),
  KEY `id_emp` (`alloted_emp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `appt_request`
--

DROP TABLE IF EXISTS `appt_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appt_request` (
  `id_appt_request` int NOT NULL AUTO_INCREMENT,
  `booking_date_iso` datetime DEFAULT NULL,
  `booking_date` varchar(20) DEFAULT NULL,
  `booking_time` text,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(25) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `whats_app_no` varchar(25) DEFAULT NULL,
  `location` varchar(250) DEFAULT NULL,
  `pref_category` tinyint(1) DEFAULT NULL COMMENT '1-Gold, 2-Silver, 3-Platinum, 4-Diamond',
  `prefered_lang` text,
  `pref_item` varchar(150) DEFAULT NULL,
  `description` text,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Open, 1 - Allotted, 2 - Rejected, 3 - Completed, 4 - Closed, 5 - Missed',
  `remarks` text,
  `preferred_slot` int DEFAULT NULL,
  `alloted_slot` int DEFAULT NULL,
  `reject_reason` text,
  `customer_feedback` text,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_appt_request`),
  KEY `preferred_slot` (`preferred_slot`),
  KEY `alloted_slot` (`alloted_slot`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `appt_request_log`
--

DROP TABLE IF EXISTS `appt_request_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appt_request_log` (
  `id_appt_request_log` int unsigned NOT NULL AUTO_INCREMENT,
  `id_employee` int unsigned DEFAULT NULL,
  `id_appt_request` int DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `remark` text,
  PRIMARY KEY (`id_appt_request_log`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `appt_slots`
--

DROP TABLE IF EXISTS `appt_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appt_slots` (
  `id_appointment_slot` int NOT NULL AUTO_INCREMENT,
  `slot_no` int NOT NULL,
  `slot_date` date NOT NULL,
  `slot_time_from` time NOT NULL,
  `slot_time_to` time NOT NULL,
  `allowed_booking` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'No of appointments allowed per slot',
  `created_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_appointment_slot`),
  UNIQUE KEY `slot_date` (`slot_date`,`slot_time_from`,`slot_time_to`),
  UNIQUE KEY `slot_no` (`slot_no`,`slot_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auto_debit_subscription`
--

DROP TABLE IF EXISTS `auto_debit_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_debit_subscription` (
  `id_auto_debit_subscription` int NOT NULL AUTO_INCREMENT,
  `id_scheme_account` int unsigned NOT NULL,
  `subscription_id` varchar(100) NOT NULL,
  `plan_id` varchar(45) NOT NULL,
  `auth_link` text COMMENT 'Created by cashfree for authorization',
  `auth_status` tinyint(1) DEFAULT NULL COMMENT '1- INITIALIZED, 2- BANK_APPROVAL_PENDING, 3- ACTIVE, 4- ON_HOLD, 5- CANCELLED, 6- COMPLETED',
  `message` text NOT NULL,
  `sub_reference_id` varchar(100) DEFAULT NULL COMMENT 'Created by cashfree, used as reference id in payment response & webhook',
  `first_charge_delay` int DEFAULT NULL COMMENT 'Number of Days after which the first debit for subscription will occur.Applicable for periodic subscriptions only',
  `expires_on` datetime DEFAULT NULL COMMENT 'The last date till which the subscription stands valid. The status of subscription will be COMPLETED. Default value is 2 years from date of subscription creation',
  `status` tinyint(1) DEFAULT '1' COMMENT '1 - Active, 0 - Inactive',
  `created_on` datetime NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `added_by` tinyint(1) DEFAULT NULL COMMENT '0 - Web App, 2 - Mobile App',
  PRIMARY KEY (`id_auto_debit_subscription`),
  KEY `id_scheme_account` (`id_scheme_account`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank`
--

DROP TABLE IF EXISTS `bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank` (
  `id_bank` int unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(100) NOT NULL,
  `short_code` varchar(5) DEFAULT NULL,
  `acc_number` varchar(20) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `address` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_bank`),
  KEY `id_bank` (`id_bank`),
  KEY `id_bank_2` (`id_bank`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bill_no_format`
--

DROP TABLE IF EXISTS `bill_no_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bill_no_format` (
  `id_bill_no_format` int NOT NULL AUTO_INCREMENT,
  `bill_type` int NOT NULL,
  `bill_no_format` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id_bill_no_format`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch`
--

DROP TABLE IF EXISTS `branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branch` (
  `id_branch` int unsigned NOT NULL AUTO_INCREMENT,
  `show_to_all` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Own,1-All,2-All Cus Only,3-All Emp only',
  `id_company` int DEFAULT '1' COMMENT 'company wise branches',
  `name` varchar(45) NOT NULL DEFAULT '',
  `warehouse` varchar(45) DEFAULT NULL COMMENT 'Warehouse Code Or Branch Code',
  `expo_warehouse` varchar(45) DEFAULT NULL COMMENT 'associate warehouse',
  `active` tinyint unsigned NOT NULL DEFAULT '1',
  `short_name` varchar(10) NOT NULL DEFAULT '',
  `email` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `id_employee` tinyint unsigned NOT NULL DEFAULT '0',
  `address1` varchar(45) DEFAULT NULL,
  `address2` varchar(45) DEFAULT NULL,
  `id_country` int unsigned DEFAULT NULL,
  `id_state` int unsigned DEFAULT NULL,
  `id_city` int unsigned DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `cusromercare` varchar(30) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `id_village` int DEFAULT NULL,
  `metal_rate_type` int unsigned NOT NULL DEFAULT '0' COMMENT '0 - Manual , 1 - Automatic, 2 - Partial ',
  `partial_silverrate_diff` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'use if partial rate update option is selected',
  `partial_goldrate_diff` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'use if partial rate update option is selected',
  `logo` text COMMENT 'name of logo with extension',
  `map_url` text,
  `fb_link` text,
  `insta_link` text,
  `sort` int DEFAULT NULL COMMENT 'Sorting order',
  `otp_verif_mob` varchar(12) DEFAULT NULL,
  `day_close` datetime DEFAULT NULL COMMENT 'Daily Stock closing time',
  `enable_bin_loc` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  `otp_verif_mobileno` varchar(45) DEFAULT NULL COMMENT 'Mobile No. to send OTP for verification',
  `enable_gift_voucher` tinyint(1) NOT NULL DEFAULT '0',
  `branch_type` int DEFAULT NULL COMMENT '1 - Store,2-Customer service',
  `is_ho` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'There should be only one Head Office',
  `note` varchar(250) DEFAULT NULL COMMENT 'Any notes near branch name. Like "Customer Service"',
  `cin_number` text,
  `gst_number` text,
  `otp_reg_mobile` varchar(20) DEFAULT NULL,
  `brledgername` varchar(20) DEFAULT NULL,
  `opening_bal` decimal(12,2) DEFAULT '0.00' COMMENT 'Opening balance, used cash deposit entry',
  `is_ho_flag` tinyint GENERATED ALWAYS AS ((case when (`is_ho` = 1) then 1 end)) VIRTUAL,
  PRIMARY KEY (`id_branch`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `uniq_is_ho_true` (`is_ho_flag`),
  KEY `sort` (`sort`),
  KEY `show_to_all` (`show_to_all`),
  KEY `id_company` (`id_company`),
  KEY `active` (`active`),
  KEY `id_country` (`id_country`),
  KEY `id_state` (`id_state`),
  KEY `id_city` (`id_city`),
  KEY `branch_type` (`branch_type`),
  KEY `show_to_all_2` (`show_to_all`),
  KEY `id_branch` (`id_branch`),
  KEY `show_to_all_3` (`show_to_all`),
  KEY `id_branch_2` (`id_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_rate`
--

DROP TABLE IF EXISTS `branch_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branch_rate` (
  `id_branchrate` int unsigned NOT NULL AUTO_INCREMENT,
  `id_metalrate` int unsigned NOT NULL DEFAULT '0',
  `id_branch` int unsigned NOT NULL DEFAULT '0',
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_branchrate`),
  KEY `id_metalrate` (`id_metalrate`),
  KEY `id_branch` (`id_branch`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_tag_details`
--

DROP TABLE IF EXISTS `branch_tag_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branch_tag_details` (
  `Tag Number` varchar(11) DEFAULT NULL,
  `Branch` varchar(11) DEFAULT NULL,
  `branchid` int NOT NULL,
  `newtagid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `card_brand`
--

DROP TABLE IF EXISTS `card_brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `card_brand` (
  `id_card_brand` int unsigned NOT NULL AUTO_INCREMENT,
  `card_brand` varchar(15) NOT NULL,
  `short_code` varchar(10) DEFAULT NULL,
  `card_type` int NOT NULL DEFAULT '1' COMMENT '1 - cc , 2- dc',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_card_brand`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_pro_des_subdes`
--

DROP TABLE IF EXISTS `cat_pro_des_subdes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat_pro_des_subdes` (
  `Category` varchar(16) DEFAULT NULL,
  `Short Code` varchar(2) DEFAULT NULL,
  `Purity` decimal(3,1) DEFAULT NULL,
  `Product` varchar(22) DEFAULT NULL,
  `Product Short Code` varchar(3) DEFAULT NULL,
  `Categorylink` varchar(16) DEFAULT NULL,
  `Design` varchar(14) DEFAULT NULL,
  `Sub Design` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id_category` int unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int NOT NULL DEFAULT '0',
  `categoryname` varchar(125) DEFAULT NULL,
  `description` varchar(245) DEFAULT NULL,
  `catimage` varchar(120) DEFAULT NULL,
  `active` tinyint unsigned DEFAULT '1',
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charges`
--

DROP TABLE IF EXISTS `charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charges` (
  `id_charges` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_mode` varchar(25) NOT NULL,
  `code` varchar(8) DEFAULT NULL,
  `service_tax` decimal(10,2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_charges`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charges_range`
--

DROP TABLE IF EXISTS `charges_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charges_range` (
  `id_range` int unsigned NOT NULL AUTO_INCREMENT,
  `id_charges` int unsigned NOT NULL,
  `lower_limit` decimal(12,2) NOT NULL,
  `upper_limit` decimal(12,2) DEFAULT NULL,
  `charge_type` tinyint unsigned NOT NULL COMMENT '''0 -> prect, 1 -> amount''',
  `charges_value` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_range`) USING BTREE,
  KEY `FK_charges_range_1` (`id_charges`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chit_settings`
--

DROP TABLE IF EXISTS `chit_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chit_settings` (
  `id_chit_settings` int unsigned NOT NULL AUTO_INCREMENT,
  `allow_join_multiple` tinyint(1) NOT NULL COMMENT '0 -No, 1 - Yes,  Allow mulitple scheme account',
  `allow_join_unpaid` tinyint(1) NOT NULL COMMENT '0 - No, 1 - Yes, Allow join if any account exist without payment',
  `currency_name` varchar(10) DEFAULT NULL,
  `currency_symbol` varchar(4) DEFAULT NULL,
  `curr_symb_html` text,
  `currency_format` varchar(5) NOT NULL DEFAULT 'en-IN',
  `currency_decimal` int NOT NULL DEFAULT '2',
  `mob_code` varchar(5) NOT NULL,
  `mob_no_len` int DEFAULT NULL,
  `delete_unpaid` tinyint(1) DEFAULT NULL COMMENT '0',
  `rate_update` tinyint unsigned DEFAULT NULL COMMENT '0 - Manual, 1 -  API, 2 - API and Manual',
  `maintenance_mode` tinyint NOT NULL DEFAULT '0',
  `maintenance_text` text,
  `reg_existing` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-No ,1 -Yes',
  `show_closed_list` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-No,1-yes',
  `allow_notification` tinyint NOT NULL DEFAULT '0' COMMENT '0-No,1-yes',
  `receipt` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-Default(A4 size),1-Customized',
  `enable_closing_otp` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '1-allow notification ',
  `edit_addpay_page` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Allow editing in admin payment page',
  `edit_custom_entry_date` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'allow to edit the entry date',
  `custom_entry_date` date DEFAULT NULL,
  `scheme_wise_receipt` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Common,2 - branch wise, 3 - Scheme-wise,4 - Scheme-wise with branch wise 5- Financial year wise, 6-Financial Year with Scheme & Branch wise,7-Financial Year with Branch wise',
  `scheme_wise_acc_no` tinyint NOT NULL DEFAULT '0' COMMENT '0 - Common, 1 - Common with branch wise, 2 - Scheme-wise, 3 - Scheme-wise with branch wise,4-financial year wise,5-financial year with scheme wise,6-financial year with scheme and branch wise',
  `is_ratenoti_sent` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'To check whether mjdma rate notification sent. 1 -> Rate updated & notification sent  2 -> Rate updated & didn''t send notification ',
  `gst_setting` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-no,1-enable gst',
  `branch_settings` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'branch_settings type=0, without branch and branch_settings =1 with branch ',
  `schemeacc_no_set` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for account no generator , 1 for account not generator, 2- a/c no jila generate, 3 for account no auto generate',
  `receipt_no_set` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No, 2-Jila, 3-Auto',
  `wallet_account_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '''0 for wallet account no generate , 1 for account no not generated',
  `schrefbenifit_secadd` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 yes, same scheme 2nd time  join  means add refevalues  ,1 No same scheme 2nd time  join  means add refevalues ',
  `cusplan_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 yes, same scheme 2nd time  join  means add refevalues  ,1 No same scheme 2nd time  join  means add refevalues ',
  `cusbenefitscrt_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 yes, \r\n benifts amount credit single time ,1  benifts amount credit multiple time ',
  `empplan_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 yes,  benifts amount credit single time ,1  benifts amount credit multiple time ',
  `empbenefitscrt_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 yes,  benifts amount credit single time ,1  benifts amount credit multiple time ',
  `regExistingReqOtp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - reg without validation, 1 - (reg with validation) OTP verification required to register existing a/c',
  `integration_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Through tool,2 - Direct api call ',
  `date_upd` datetime DEFAULT NULL,
  `newSchjoinonline` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 for join newscheme or 0 for not  join newscheme',
  `getExisting_balance` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'get opening balnce for existing customer',
  `sch_limit` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'limits for individual schems',
  `has_lucky_draw` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `allow_savecard` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes ( for app)',
  `allow_referral` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0- Disable referral (show only app sharing ),1- Show referral ',
  `allow_wallet` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Disable, 1- enable',
  `enableGoldrateDisc` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No discount, 1 - Discount',
  `enableGoldrateDisc_18k` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No discount, 1 - Discount for 18k',
  `goldDiscAmt` decimal(10,2) DEFAULT NULL,
  `goldDiscAmt_18k` decimal(10,2) DEFAULT '0.00',
  `walletIntegration` tinyint NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes (Like SSS)',
  `branchWiseLogin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Branch wise login , 0 - No default 0',
  `is_branchwise_cus_reg` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' 1 - yes , 0 -> No default 0',
  `is_branchwise_rate` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' 1 - yes , 0 -> No default 0',
  `useWalletForChit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Use wallet for chit payment , 0 - No',
  `allow_catlog` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `allow_our_stores` tinyint NOT NULL DEFAULT '0',
  `wallet_balance_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Amount,1-Points',
  `wallet_amt_per_points` float NOT NULL DEFAULT '0' COMMENT 'amount equivalent for point (when wallet_balance_type is 0)',
  `wallet_points` int NOT NULL DEFAULT '0' COMMENT 'amount equivalent per how many points (when wallet_balance_type is 0)',
  `enableSilver_rateDisc` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No discount, 1 - Discount',
  `silverDiscAmt` decimal(10,2) DEFAULT '0.00',
  `isOTPReqToLogin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - yes',
  `isOTPRegForPayment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - yes ',
  `payOTP_exp` int DEFAULT NULL COMMENT 'in seconds',
  `loginOTP_exp` int DEFAULT NULL COMMENT 'in seconds ',
  `cost_center` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> Single, 2 -> Multi-Cost Center [Branch-wise Customer], 3 -> Multi-Cost Center [Single Customer Ac for multi branch] ',
  `branchwise_scheme` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'branchwise scheme code',
  `allow_chit_catlog` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `allow_prestashop_catlog` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `emp_ref_by` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-Emp code,1-Mobile',
  `msg91_authkey` text COMMENT 'auth key of msg 91',
  `req_otp_login` tinyint NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes (yet to complete work)',
  `is_kyc_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -> No, 1->Yes, common KYC & plan based,2->Yes, Based on Plan only',
  `kyc_approval` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -  Through Api , 2 - Manual Approval',
  `enable_dth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Enable , 1 - Disable',
  `appt_auto_assign` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `vs_send_sms_to` varchar(45) DEFAULT NULL,
  `vs_send_mail_to` text,
  `enable_coin_enq` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Enable , 0 - Disable',
  `gent_clientid` tinyint NOT NULL DEFAULT '0' COMMENT 'No - 0, Yes -1',
  `cusName_edit` tinyint DEFAULT '0' COMMENT 'No - 0, Yes -1',
  `pledge_calculator` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Enable, 0 - Disable',
  `estimation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Enable, 0 - Disable',
  `compare_plan_img` text COMMENT 'Compare purchase plan image',
  `metal_wgt_decimal` int NOT NULL DEFAULT '4' COMMENT 'No. of decimals for metal weight',
  `metal_wgt_roundoff` tinyint(1) DEFAULT '1' COMMENT '0 - No , 1 - Yes',
  `req_gift_issue_otp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1- Required OTP verification for gift issue',
  `req_prize_issue_otp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1- Required OTP verification for prize issue [Lucky draw]',
  `rate_history` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Enable, 0 - Disable',
  `vs_enable` tinyint NOT NULL DEFAULT '0' COMMENT 'No - 0, Yes -1',
  `is_multi_commodity` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 -Multi-Commodity schemes available',
  `enable_coin_book` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Enable , 0 - Disable',
  `auto_debit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Active, 0 - Inactive',
  `auto_debit_allow_app_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Block app payment, 1 - Allow app payment, 2- Allow app payment only when subscription status is not ACTIVE.',
  `rate_disc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Auto, 2 - Manual',
  `prof_wise_loginotp_exp` int DEFAULT '60' COMMENT 'in seconds',
  `login_branch` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Required branch while login',
  `firstPayamt_payable` decimal(10,3) DEFAULT NULL,
  `get_amt_in_schjoin` tinyint(1) DEFAULT NULL,
  `block_pay_mins` varchar(45) DEFAULT NULL COMMENT 'Block payment till entered minutes',
  `restrict_lastPayment_days` int NOT NULL DEFAULT '0' COMMENT 'To restrict online gateway payments for mentioned days specifically for last installment if already its not success in gateway',
  `show_video_shop` tinyint(1) DEFAULT '0',
  `vs_booking_time` varchar(15) DEFAULT NULL,
  `show_customer_order` tinyint(1) NOT NULL DEFAULT '0',
  `req_calling_code` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Yes, 0 - No',
  `is_agent_kyc_required` tinyint(1) NOT NULL DEFAULT '0',
  `company_settings` tinyint(1) NOT NULL DEFAULT '0' COMMENT '	0--> Single Company 1 --> Multiple Company',
  `enable_digi_gold` tinyint NOT NULL DEFAULT '0',
  `group_wise_receipt` int NOT NULL DEFAULT '0',
  `isOTPReqToGift` int NOT NULL DEFAULT '0' COMMENT '0-NO, 1-YES',
  `giftOTP_exp` int DEFAULT NULL COMMENT 'In seconds',
  `emp_wallet_account_type` tinyint(1) NOT NULL DEFAULT '0',
  `otp_scheme_join` tinyint(1) NOT NULL DEFAULT '0',
  `is_pin_required` tinyint(1) DEFAULT '0',
  `schemeaccNo_displayFrmt` int NOT NULL DEFAULT '0' COMMENT '0 - only number, 1 - based on settings,2 - customised',
  `receiptNo_displayFrmt` int NOT NULL DEFAULT '0' COMMENT '0 - only number, 1 - based on settings,2 - customised',
  `custom_AccDisplayFrmt` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `custom_ReceiptDisplayFrmt` varchar(250) DEFAULT NULL,
  `chitCollectionEmpCount` int NOT NULL DEFAULT '0' COMMENT 'no of employees allowed for chit collection',
  `pg_email` varchar(100) DEFAULT NULL,
  `showDirectpay` tinyint(1) DEFAULT '0',
  `block_kyc_by` int NOT NULL DEFAULT '1' COMMENT '1 ->Block Plan joining & Payment\r\n,2->Block Payment from 2nd installment\r\n,3->Allow Plan joining & Payment\r\n',
  `pan_required_by` int NOT NULL DEFAULT '0' COMMENT '0 ->Not applicable,1->Overall Plan paid amount,2->Plan installment amount',
  `pan_req_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'get PAN by amount',
  `show_kyc_optional` int NOT NULL DEFAULT '0' COMMENT '0 -> No, 1-> Yes',
  `lock_metal` int NOT NULL DEFAULT '0' COMMENT '0-Don''t Show, 1- Only app, 2- Only Web, 3- Show in All	',
  `enable_old_sch_acc_no` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'For admin to show the old sch acc no or not',
  PRIMARY KEY (`id_chit_settings`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_auth_log`
--

DROP TABLE IF EXISTS `cicd_auth_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_auth_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `event` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_clients`
--

DROP TABLE IF EXISTS `cicd_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `client_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `repo_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `folder` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `branch` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'main',
  `deploy_mode` enum('git_pull','symlink') COLLATE utf8mb4_general_ci DEFAULT 'git_pull',
  `url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `webhook_secret` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','pending','inactive') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_deploy` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_deployment_history`
--

DROP TABLE IF EXISTS `cicd_deployment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_deployment_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Client repository identifier',
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Human-readable client name',
  `environment` enum('staging','production','develop','qa','support') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staging',
  `status` enum('pending','in_progress','success','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `branch` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'main',
  `commit_sha` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Git commit SHA',
  `commit_message` text COLLATE utf8mb4_unicode_ci,
  `triggered_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'webhook' COMMENT 'User or system that triggered deployment',
  `trigger_type` enum('push','manual','rollback','webhook') COLLATE utf8mb4_unicode_ci DEFAULT 'webhook',
  `duration_seconds` int DEFAULT NULL COMMENT 'Total deployment duration',
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `server_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Raw server response data',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_environment` (`environment`),
  KEY `idx_status` (`status`),
  KEY `idx_started_at` (`started_at`),
  KEY `idx_client_env` (`client_id`,`environment`),
  KEY `idx_date_range` (`started_at`,`status`),
  CONSTRAINT `cicd_deployment_history_chk_1` CHECK (json_valid(`server_response`))
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_deployment_stats`
--

DROP TABLE IF EXISTS `cicd_deployment_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_deployment_stats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `environment` enum('staging','production','develop','qa','support') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_deployments` int NOT NULL DEFAULT '0',
  `successful_deployments` int NOT NULL DEFAULT '0',
  `failed_deployments` int NOT NULL DEFAULT '0',
  `avg_duration_seconds` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_date_env` (`date`,`environment`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_deployments`
--

DROP TABLE IF EXISTS `cicd_deployments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_deployments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `environment` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `branch` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `commit_hash` varchar(40) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `commit_message` text COLLATE utf8mb4_general_ci,
  `deployed_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('success','failed','rollback') COLLATE utf8mb4_general_ci DEFAULT 'success',
  `deploy_mode` enum('git_pull','symlink') COLLATE utf8mb4_general_ci DEFAULT 'git_pull',
  `duration_seconds` int DEFAULT '0',
  `release_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `previous_release` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_environment` (`environment`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_environments`
--

DROP TABLE IF EXISTS `cicd_environments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_environments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_settings`
--

DROP TABLE IF EXISTS `cicd_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_sync_history`
--

DROP TABLE IF EXISTS `cicd_sync_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_sync_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `client_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `source_branch` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `target_branch` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `commit_sha` varchar(40) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `triggered_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','syncing','success','failed','conflict') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `pr_number` int DEFAULT NULL,
  `pr_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_client` (`client_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cicd_users`
--

DROP TABLE IF EXISTS `cicd_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cicd_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'viewer',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `city` (
  `id_city` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `id_state` int NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_city`) USING BTREE,
  UNIQUE KEY `CtIndex` (`id_city`,`id_state`),
  KEY `id_state` (`id_state`),
  KEY `id_city` (`id_city`),
  KEY `name` (`name`),
  KEY `id_state_2` (`id_state`),
  KEY `id_city_2` (`id_city`),
  KEY `name_2` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=47579 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company` (
  `id_company` int unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) DEFAULT NULL,
  `short_code` varchar(5) NOT NULL,
  `comp_name_in_sms` varchar(45) NOT NULL,
  `address1` varchar(128) DEFAULT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `id_country` int unsigned DEFAULT NULL,
  `id_state` int unsigned DEFAULT NULL,
  `id_city` int unsigned DEFAULT NULL,
  `pincode` varchar(12) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `website` varchar(50) DEFAULT NULL,
  `bank_acc_number` varchar(45) DEFAULT NULL,
  `bank_name` varchar(120) DEFAULT NULL,
  `bank_acc_name` varchar(45) DEFAULT NULL,
  `bank_branch` varchar(100) DEFAULT NULL,
  `bank_ifsc` varchar(45) DEFAULT NULL,
  `mail_server` varchar(250) DEFAULT NULL,
  `mail_password` varchar(25) DEFAULT NULL,
  `server_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Shared hosting , 2 - Dedicated server (Shared and Reseller packages do not support remote smtp)',
  `smtp_host` text,
  `smtp_user` text,
  `smtp_pass` text,
  `send_through` tinyint unsigned DEFAULT '0' COMMENT '0 - phpmail, 1 - gmail',
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL COMMENT 'last modified on',
  `phone1` varchar(45) DEFAULT NULL,
  `mobile1` varchar(45) DEFAULT NULL,
  `gst_number` varchar(50) NOT NULL,
  `cin_number` text,
  `whatsapp_no` varchar(20) DEFAULT NULL,
  `whatsapp_no1` varchar(20) DEFAULT NULL COMMENT 'For alternate whatsapp number',
  `tollfree1` varchar(20) DEFAULT NULL,
  `map_url` varchar(250) DEFAULT NULL,
  `authtoken` text,
  `show_social_media` tinyint NOT NULL DEFAULT '0' COMMENT ' For app to show the social media option or not',
  `whatsapp_url` longtext,
  `insta_url` longtext,
  `fb_url` longtext,
  `youtube_url` longtext,
  PRIMARY KEY (`id_company`),
  KEY `id_country` (`id_country`),
  KEY `id_state` (`id_state`),
  KEY `id_city` (`id_city`),
  KEY `id_country_2` (`id_country`),
  KEY `id_state_2` (`id_state`),
  KEY `id_city_2` (`id_city`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuration` (
  `id_config` int NOT NULL AUTO_INCREMENT,
  `auto_pay_approval` int NOT NULL DEFAULT '2' COMMENT '0 => No , \r\n1 => Yes (Update status as success) ,\r\n 2 => Yes (Update status as success and insert data in intermediatetables)',
  `integration_type` int NOT NULL DEFAULT '5' COMMENT '0-None,1 - jilaba , 2 - sync tool, 3 - EJ ERP Integration, 4 - SKTM (SCM,TKTM only - Tool for offline, API for online), 5 - Khimji Integration (Directly integration with ACME without storing data in intermediate tables)',
  `auto_sync` int NOT NULL DEFAULT '1' COMMENT '1 - Yes, 0 - No',
  `one_signal_app_id` varchar(200) NOT NULL,
  `one_signal_auth_key` varchar(200) NOT NULL,
  `whats_app_url` varchar(200) NOT NULL,
  `instance_id` varchar(200) NOT NULL,
  `app_cus_email` int NOT NULL DEFAULT '1' COMMENT ' 0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory',
  `app_cus_address1` int DEFAULT '1' COMMENT ' 0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory',
  `app_cus_address2` int NOT NULL DEFAULT '1' COMMENT ' 0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory',
  `app_cus_country` int NOT NULL DEFAULT '2' COMMENT '0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory',
  `app_cus_state` int NOT NULL DEFAULT '2' COMMENT '0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory',
  `app_cus_city` int NOT NULL DEFAULT '2' COMMENT '0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory',
  `app_cus_lastname` int NOT NULL DEFAULT '1' COMMENT '0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory',
  `zoop_enabled` int NOT NULL DEFAULT '1' COMMENT '0 => No , 1 => Yes',
  `zoop_url` varchar(200) NOT NULL,
  `zoop_agency_id` varchar(200) NOT NULL,
  `zoop_api_key` varchar(200) NOT NULL,
  `khimji_baseURL` varchar(200) NOT NULL,
  `khimji_x_key` varchar(200) NOT NULL,
  `khimji_auth` varchar(300) NOT NULL,
  `sms_gateway` int NOT NULL DEFAULT '2' COMMENT '1 - Msg91 , 2 - Netty Fish ',
  `show_gcode` int NOT NULL DEFAULT '1' COMMENT ' 0 => No (1234) , 1 => Yes (SSA 1234)',
  `clt_id_code` varchar(200) NOT NULL COMMENT 'Client ID generation Short code',
  `play_str_url` varchar(200) DEFAULT NULL COMMENT 'paystore_url ',
  `app_a_pack` varchar(200) DEFAULT NULL COMMENT 'play store package',
  `app_i_pack` varchar(200) DEFAULT NULL COMMENT 'app store package',
  `erp_base_url` varchar(200) DEFAULT NULL,
  `ej_usr_nm` varchar(200) DEFAULT NULL,
  `ej_pwd` varchar(200) DEFAULT NULL,
  `current_android_version` varchar(100) DEFAULT NULL,
  `new_android_version` varchar(100) DEFAULT NULL,
  `current_ios_version` varchar(100) DEFAULT NULL,
  `new_ios_version` varchar(100) DEFAULT NULL,
  `show_language` tinyint NOT NULL DEFAULT '0' COMMENT 'For app to show the language option or not',
  `show_close` tinyint NOT NULL DEFAULT '0' COMMENT 'For app to show the closed accounts or not',
  `show_wgt_history` tinyint NOT NULL DEFAULT '0' COMMENT 'For app to show the paid weights history',
  `show_amt_history` tinyint NOT NULL DEFAULT '0' COMMENT 'For app to show the paid amounts history',
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `id_country` int NOT NULL AUTO_INCREMENT,
  `sortname` varchar(3) NOT NULL,
  `name` varchar(150) NOT NULL,
  `currency_name` varchar(10) NOT NULL,
  `currency_code` varchar(4) NOT NULL,
  `mob_code` varchar(5) NOT NULL,
  `mob_no_len` int unsigned NOT NULL,
  `min_mob_len` int DEFAULT NULL,
  `max_mob_len` int DEFAULT NULL,
  `date_upd` date DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_country`) USING BTREE,
  UNIQUE KEY `CIndex` (`id_country`,`sortname`),
  KEY `id_country` (`id_country`),
  KEY `id_country_2` (`id_country`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ct_advance_account`
--

DROP TABLE IF EXISTS `ct_advance_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ct_advance_account` (
  `booking_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'similar to id_scheme_account \r\n',
  `id_plan` int unsigned DEFAULT '0',
  `id_customer` int unsigned NOT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `booking_number` int DEFAULT NULL,
  `booking_name` varchar(300) DEFAULT NULL,
  `ref_no` varchar(100) DEFAULT NULL,
  `last_paid_date` datetime DEFAULT NULL,
  `booking_date` datetime DEFAULT NULL,
  `maturity_date` date DEFAULT NULL COMMENT 'If null, no maturity limit .ins count only',
  `employee_closed` int unsigned DEFAULT '0',
  `closing_date` datetime DEFAULT NULL,
  `closing_balance` decimal(10,3) DEFAULT '0.000',
  `closing_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_weight` decimal(12,3) DEFAULT NULL,
  `closed_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - self, 1 - representative',
  `remark` varchar(180) DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `custom_entry_date` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5 - Sync',
  `Closing_id_branch` int DEFAULT NULL,
  `id_employee` int unsigned DEFAULT '0',
  `booking_amount` decimal(10,2) DEFAULT NULL,
  `booking_weight` decimal(10,3) DEFAULT NULL,
  `booking_rate` int DEFAULT NULL,
  `advance_amt` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '\r\n1 - Open, 2 - Advance Done, 3 - Paid, 4 - Closed\r\n\r\n',
  PRIMARY KEY (`booking_id`),
  UNIQUE KEY `AccIndex` (`booking_id`,`id_customer`,`id_plan`,`booking_number`),
  KEY `FK_ct_advance_account_customer` (`id_customer`),
  KEY `FK_ct_advance_account_scheme` (`id_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ct_advance_booking_settings`
--

DROP TABLE IF EXISTS `ct_advance_booking_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ct_advance_booking_settings` (
  `id_plan` int NOT NULL AUTO_INCREMENT COMMENT 'primary key, auto increment plan id',
  `plan_name` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `plan_code` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sync_plan_code` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `maturity_type` tinyint NOT NULL DEFAULT '1' COMMENT '1- months, 2-days',
  `maturity_value` int NOT NULL DEFAULT '0' COMMENT 'total number of days or months based on maturity type',
  `accessible_branches` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_metal` int DEFAULT NULL,
  `purity` int DEFAULT NULL,
  `payable_by` tinyint NOT NULL DEFAULT '1' COMMENT '0- amount plan, 1- weight plan',
  `minimum_val` int DEFAULT NULL,
  `maximum_val` int DEFAULT NULL,
  `denomination` int DEFAULT NULL,
  `adv_limit_type` tinyint NOT NULL DEFAULT '0' COMMENT '0 - amount type, 1- percent type',
  `adv_limit_value_online` int DEFAULT NULL,
  `plan_image` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `plan_description` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` int NOT NULL DEFAULT '1' COMMENT '1- active , 0 - inactive',
  `is_visible` int NOT NULL DEFAULT '1' COMMENT ' 0 - restict to join, 1- show to all, 2 - show in admin',
  `date_add` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `last_edited_date` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `total_adv_limit_value` int DEFAULT NULL,
  `is_adv_limit_available` int NOT NULL DEFAULT '1' COMMENT '1-yes,0-no',
  PRIMARY KEY (`id_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='pre-advance booking plan settings (Lock your gold)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ct_advance_payment`
--

DROP TABLE IF EXISTS `ct_advance_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ct_advance_payment` (
  `id_payment` int NOT NULL AUTO_INCREMENT,
  `id_adv_booking` int unsigned NOT NULL,
  `id_employee` int DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `id_payGateway` int DEFAULT NULL,
  `id_transaction` varchar(50) DEFAULT NULL,
  `payu_id` varchar(25) DEFAULT NULL COMMENT 'Razor Pay --> order id,Payu - >payu id',
  `date_payment` datetime NOT NULL,
  `custom_entry_date` datetime DEFAULT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(45) DEFAULT NULL,
  `act_amount` decimal(10,0) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `transaction_type` varchar(5) DEFAULT NULL COMMENT 'AP- Advance Pay, BP - Balance Pay',
  `metal_rate` decimal(10,2) DEFAULT '0.00',
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `remark` text,
  `receipt_no` int DEFAULT NULL COMMENT 'receipt_no',
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 -admin , 1- web app, 2 - mobile app, 3 - Admin App, 4 - Cashfree Subscription, 5 - Sync',
  `ref_trans_id` varchar(45) DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gst` decimal(10,0) NOT NULL,
  `gst_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -amount inclusive of GST , 0 -amount exclusive of gst',
  `gst_amount` decimal(12,3) DEFAULT NULL COMMENT 'GST Calculated by LMX',
  `is_gateway_verified` tinyint(1) DEFAULT '0' COMMENT '1 - status verified with gateway',
  `receipt_year` int DEFAULT NULL COMMENT 'Receipt financial year code',
  `form_secret` varchar(250) DEFAULT NULL,
  `pay_email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_payment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ct_advbook_mode_detail`
--

DROP TABLE IF EXISTS `ct_advbook_mode_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ct_advbook_mode_detail` (
  `id_pay_mode_details` int NOT NULL AUTO_INCREMENT,
  `id_adv_payment` int DEFAULT NULL,
  `NB_type` int DEFAULT NULL COMMENT '1-RTGS,2-IMPS',
  `payment_date` datetime DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `payment_amount` decimal(10,0) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `net_banking_date` date DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `remark` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `card_type` int DEFAULT NULL COMMENT '1-RUPAY, 2-VISA,3-Mastro,4-Master',
  `id_pay_device` int DEFAULT NULL COMMENT 'From Payment device Master',
  `id_bank` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  PRIMARY KEY (`id_pay_mode_details`),
  UNIQUE KEY `id_payment` (`id_adv_payment`,`payment_mode`,`payment_ref_number`,`payment_status`,`id_pay_device`,`id_bank`) USING BTREE,
  KEY `id_billing_payment` (`id_pay_mode_details`),
  KEY `bill_id` (`id_adv_payment`),
  KEY `payment_date` (`payment_date`),
  KEY `payment_status` (`payment_status`),
  KEY `id_payment_2` (`id_adv_payment`),
  KEY `NB_type` (`NB_type`),
  KEY `id_pay_device` (`id_pay_device`),
  KEY `id_bank` (`id_bank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cust_enquiry`
--

DROP TABLE IF EXISTS `cust_enquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cust_enquiry` (
  `id_enquiry` int unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int DEFAULT NULL,
  `ticket_no` varchar(15) DEFAULT NULL COMMENT 'only for complaint',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Enquiry, 2-Suggestion, 3-Complaint, 4-Others, 5 - DTH, 6 - Experience Center, 7 - Coin enquiry',
  `chit_acc_number` varchar(16) DEFAULT NULL,
  `name` varchar(25) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `date_add` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0-Open, 1-In Follow up, 2-Closed',
  `comments` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `title` text,
  `address` text,
  `profession` varchar(45) DEFAULT NULL,
  `email` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL COMMENT ' ',
  `date_of_wed` date DEFAULT NULL COMMENT ' ',
  `enq_from` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Web app , 2 - Mobile app',
  `pincode` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id_enquiry`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cust_enquiry_product`
--

DROP TABLE IF EXISTS `cust_enquiry_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cust_enquiry_product` (
  `id_cust_enquiry_product` int NOT NULL AUTO_INCREMENT,
  `id_enquiry` int unsigned NOT NULL,
  `product_name` varchar(250) DEFAULT NULL,
  `gram` decimal(12,0) DEFAULT NULL,
  `coin_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - With Neck, 2 - Without Neck',
  `qty` int DEFAULT NULL,
  PRIMARY KEY (`id_cust_enquiry_product`),
  KEY `id_enquiry` (`id_enquiry`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cust_enquiry_status`
--

DROP TABLE IF EXISTS `cust_enquiry_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cust_enquiry_status` (
  `id_cusenq_status` int NOT NULL AUTO_INCREMENT,
  `id_enquiry` int unsigned NOT NULL,
  `id_employee` int unsigned NOT NULL,
  `enq_status` tinyint(1) NOT NULL COMMENT '	0-Open, 1-In Follow up, 2-Closed',
  `enq_description` text,
  `internal_status` text COMMENT 'For internal use',
  `date_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cusenq_status`),
  KEY `id_enquiry` (`id_enquiry`),
  KEY `id_employee` (`id_employee`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer` (
  `id_customer` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(100) DEFAULT NULL COMMENT 'reference number with Offline s/w',
  `app_cus_code` varchar(45) DEFAULT NULL,
  `id_company` int DEFAULT NULL COMMENT 'company wise customers',
  `id_branch` int DEFAULT NULL COMMENT 'cus register branch',
  `id_village` int DEFAULT NULL,
  `title` varchar(5) DEFAULT NULL,
  `initials` varchar(10) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  `firstname` varchar(32) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_of_wed` date DEFAULT NULL,
  `spouse_name` varchar(35) DEFAULT NULL,
  `spouse_dob` date DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `id_address` int unsigned DEFAULT '0',
  `id_employee` int unsigned DEFAULT '0',
  `email` varchar(128) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `nominee_name` varchar(50) DEFAULT NULL,
  `id_profession` int DEFAULT NULL,
  `nominee_relationship` varchar(32) DEFAULT NULL,
  `nominee_mobile` varchar(20) DEFAULT NULL,
  `cus_img` varchar(50) DEFAULT NULL,
  `pan` varchar(15) DEFAULT NULL,
  `pan_proof` varchar(50) DEFAULT NULL,
  `ispan_req` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'To disable editting pan no',
  `voterid` varchar(15) DEFAULT NULL,
  `voterid_proof` varchar(50) DEFAULT NULL,
  `rationcard` varchar(15) DEFAULT NULL,
  `rationcard_proof` varchar(50) DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `pwd_hash` varchar(300) DEFAULT NULL,
  `profile_complete` tinyint(1) DEFAULT '0',
  `active` tinyint unsigned NOT NULL DEFAULT '1',
  `is_new` varchar(1) NOT NULL DEFAULT 'Y',
  `date_add` datetime DEFAULT CURRENT_TIMESTAMP,
  `custom_entry_date` date DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `added_by` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5- Sync, 6-Import 7-Retail admin',
  `notification` tinyint unsigned NOT NULL DEFAULT '0',
  `gst_number` varchar(50) DEFAULT NULL,
  `cus_ref_code` varchar(45) DEFAULT NULL COMMENT 'cus ref code(code will be stored for  referral type single)',
  `is_refbenefit_crt_cus` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1-Benefit credited (used for referral type single)',
  `emp_ref_code` varchar(45) DEFAULT NULL COMMENT 'emp ref code(code will be stored for type single)',
  `is_refbenefit_crt_emp` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1-Benefit credited (used for referral type single)',
  `religion` int DEFAULT NULL COMMENT 'religion of custmer',
  `kyc_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - KYC Completed',
  `is_cus_synced` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `last_sync_time` datetime DEFAULT NULL COMMENT 'Offline data Last synced date time',
  `last_payment_on` datetime DEFAULT NULL COMMENT 'Last payment date',
  `is_vip` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Yes, 0 - No',
  `cus_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Individual ,2-Company',
  `send_promo_sms` int NOT NULL DEFAULT '0',
  `aadharid` varchar(20) DEFAULT NULL,
  `driving_license_no` varchar(20) DEFAULT NULL,
  `passport_no` varchar(20) DEFAULT NULL,
  `nominee_address1` varchar(150) DEFAULT NULL,
  `nominee_address2` varchar(150) DEFAULT NULL,
  `id_agent` int DEFAULT NULL,
  `pin_no` int DEFAULT NULL,
  `refer_person` varchar(35) DEFAULT NULL,
  `refer_mobile` varchar(15) DEFAULT NULL,
  `aadhar_ImgName` varchar(250) DEFAULT NULL,
  `pan_ImgName` varchar(250) DEFAULT NULL,
  `dl_ImgName` varchar(250) DEFAULT NULL,
  `passport` varchar(100) DEFAULT NULL,
  `dl_number` varchar(100) DEFAULT NULL,
  `pp_ImgName` varchar(250) DEFAULT NULL,
  `fin_year_code` varchar(15) DEFAULT NULL,
  `opening_balance_amount` decimal(10,0) DEFAULT NULL,
  `nominee_pan` varchar(15) DEFAULT NULL,
  `vip_up_by` int DEFAULT NULL,
  `vip_up_time` datetime DEFAULT NULL,
  `marital_status` int NOT NULL DEFAULT '0' COMMENT '0 - Rather not to say, 1 - single, 2 - married',
  `languages_known` varchar(50) DEFAULT NULL COMMENT 'From languages master',
  `allocated_employee` varchar(100) DEFAULT NULL COMMENT 'For collection employee',
  `allocated_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_customer`),
  UNIQUE KEY `mobile` (`mobile`),
  UNIQUE KEY `CusIndex` (`id_customer`,`firstname`,`lastname`,`mobile`),
  UNIQUE KEY `id_company_3` (`id_company`,`id_branch`,`mobile`),
  UNIQUE KEY `gst_number` (`gst_number`,`mobile`) USING BTREE,
  UNIQUE KEY `pan` (`pan`,`mobile`) USING BTREE,
  UNIQUE KEY `aadharid` (`aadharid`,`mobile`) USING BTREE,
  KEY `id_company` (`id_company`),
  KEY `id_company_2` (`id_company`),
  KEY `id_village` (`id_village`),
  KEY `active` (`active`),
  KEY `cus_type` (`cus_type`),
  KEY `added_by` (`added_by`),
  KEY `mobile_2` (`mobile`),
  KEY `id_customer` (`id_customer`),
  KEY `passwd` (`passwd`),
  KEY `active_2` (`active`),
  KEY `email` (`email`),
  KEY `date_add` (`date_add`),
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `username` (`username`),
  KEY `email_2` (`email`),
  KEY `added_by_2` (`added_by`),
  KEY `mobile_3` (`mobile`),
  KEY `id_customer_2` (`id_customer`),
  KEY `passwd_2` (`passwd`),
  KEY `active_3` (`active`),
  KEY `email_3` (`email`),
  KEY `date_add_2` (`date_add`),
  KEY `firstname_2` (`firstname`),
  KEY `lastname_2` (`lastname`),
  KEY `username_2` (`username`),
  KEY `email_4` (`email`),
  KEY `added_by_3` (`added_by`)
) ENGINE=InnoDB AUTO_INCREMENT=15024 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_associated`
--

DROP TABLE IF EXISTS `customer_associated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_associated` (
  `id_cus_associated` int NOT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `id_customer` int unsigned NOT NULL,
  `associated_cus` int unsigned NOT NULL,
  `verified_otp` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - Dissociated, 1 - Associated'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_childrens`
--

DROP TABLE IF EXISTS `customer_childrens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_childrens` (
  `cus_children_id` int NOT NULL AUTO_INCREMENT,
  `cus_id` int unsigned NOT NULL COMMENT 'References Customer Id From customer Table',
  `children_name` varchar(35) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `children_dob` date DEFAULT NULL,
  PRIMARY KEY (`cus_children_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_delivery_address`
--

DROP TABLE IF EXISTS `customer_delivery_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_delivery_address` (
  `id_delivery_addr` int NOT NULL AUTO_INCREMENT,
  `id_customer` int DEFAULT NULL,
  `address_name` varchar(40) DEFAULT NULL,
  `mobile` int DEFAULT NULL,
  `id_country` int DEFAULT NULL,
  `id_state` int DEFAULT NULL,
  `id_city` int DEFAULT NULL,
  `address1` varchar(50) DEFAULT NULL,
  `address2` varchar(50) DEFAULT NULL,
  `address3` varchar(50) DEFAULT NULL,
  `pincode` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_delivery_addr`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_edit_log`
--

DROP TABLE IF EXISTS `customer_edit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_edit_log` (
  `id_log` int NOT NULL,
  `id_customer` int NOT NULL,
  `previous_firstname` varchar(200) DEFAULT NULL,
  `previous_lastname` varchar(200) DEFAULT NULL,
  `previous_mobile` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `previous_email` varchar(200) DEFAULT NULL,
  `previous_address1` varchar(250) DEFAULT NULL,
  `updated_firstname` varchar(200) DEFAULT NULL,
  `updated_lastname` varchar(200) DEFAULT NULL,
  `updated_mobile` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `updated_email` varchar(200) DEFAULT NULL,
  `updated_address1` varchar(250) DEFAULT NULL,
  `updated_on` datetime NOT NULL,
  `updated_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_feedback`
--

DROP TABLE IF EXISTS `customer_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_feedback` (
  `id_cus_feedback` int NOT NULL,
  `id_customer` int DEFAULT NULL,
  `id_feedback` int DEFAULT NULL COMMENT 'From Customer Feedback Master',
  `feedback_response` tinyint(1) DEFAULT NULL COMMENT '1-Yes,2-No',
  `feedback_date` datetime DEFAULT NULL,
  `feedback_taken_by` int DEFAULT NULL,
  `comments` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_feedback_master`
--

DROP TABLE IF EXISTS `customer_feedback_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_feedback_master` (
  `id_feedback` int NOT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT 'FeedBack Questions',
  `created_by` int DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_feedback_response`
--

DROP TABLE IF EXISTS `customer_feedback_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_feedback_response` (
  `id_feedback_res` int NOT NULL,
  `id_cus_feedback` int DEFAULT NULL,
  `id_feedback` int DEFAULT NULL,
  `feedback_response` tinyint(1) DEFAULT NULL COMMENT '1-Excellent,2-Good,3-Fair ,4-Poor'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_import`
--

DROP TABLE IF EXISTS `customer_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_import` (
  `id_cus_import` int NOT NULL AUTO_INCREMENT,
  `mobile` varchar(10) DEFAULT NULL,
  `firstname` text,
  `address1` text,
  `address2` text,
  `address3` text,
  `city` text,
  `pincode` text,
  `pan` text,
  `gst_number` text,
  `import_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_cus_import`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_order_image`
--

DROP TABLE IF EXISTS `customer_order_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_order_image` (
  `id_order_img` int NOT NULL AUTO_INCREMENT,
  `id_orderdetails` int DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_order_img`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_order_other_details`
--

DROP TABLE IF EXISTS `customer_order_other_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_order_other_details` (
  `detail_id` int NOT NULL AUTO_INCREMENT,
  `item_type` tinyint(1) DEFAULT NULL COMMENT '0 -> Tag\r\n1 -> Non Tag\r\n2-> Home Bill',
  `id_orderdetails` int NOT NULL,
  `id_ret_category` int NOT NULL,
  `id_purity` int DEFAULT NULL,
  `id_product` int NOT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `pcs` int NOT NULL DEFAULT '0',
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `wast_percent` int DEFAULT NULL,
  `mc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 1-> Per Pc 2->Per Grm 3-> % on price ',
  `mc_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_order_stone_details`
--

DROP TABLE IF EXISTS `customer_order_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_order_stone_details` (
  `tag_stone_id` int NOT NULL AUTO_INCREMENT,
  `id_other_details` int DEFAULT NULL,
  `stone_id` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `wt` decimal(12,3) DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Stone Rate',
  `amount` decimal(10,2) DEFAULT NULL,
  `is_apply_in_lwt` tinyint DEFAULT '1' COMMENT 'Stone wt applied in LWT=>1-Yes,0-No',
  `is_certification_req` tinyint NOT NULL DEFAULT '0',
  `stone_cal_type` int NOT NULL DEFAULT '1' COMMENT ' 1-> By weight 2->By pcs',
  `certification_cost` decimal(10,2) DEFAULT NULL,
  `image` text,
  PRIMARY KEY (`tag_stone_id`),
  KEY `FK_tag_st_tag_id` (`id_other_details`),
  KEY `FK_tag_st_sto_id` (`stone_id`),
  KEY `FK_tag_uom_id` (`uom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_order_video`
--

DROP TABLE IF EXISTS `customer_order_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_order_video` (
  `id_order_video` int NOT NULL AUTO_INCREMENT,
  `id_orderdetails` int NOT NULL,
  `video` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_order_video`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_reg`
--

DROP TABLE IF EXISTS `customer_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_reg` (
  `id_customer_reg` int NOT NULL,
  `cus_ref_no` varchar(100) DEFAULT NULL COMMENT 'reference number with Offline s/w',
  `clientid` varchar(200) DEFAULT NULL,
  `id_branch` int unsigned DEFAULT NULL COMMENT 'Chit a/c Branch id',
  `branch_code` varchar(45) DEFAULT NULL,
  `warehouse` varchar(45) DEFAULT NULL COMMENT 'Warehouse Code (For reference only)',
  `record_to` tinyint(1) NOT NULL COMMENT '1 - Offline , 2 - Online',
  `is_modified` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1- yes ',
  `reg_date` date DEFAULT NULL COMMENT 'entry date',
  `maturity_date` date DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL COMMENT 'title of customer',
  `ac_name` varchar(300) DEFAULT NULL COMMENT 'account name',
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `address1` varchar(500) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `pincode` varchar(6) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL COMMENT 'without std code',
  `mobile` varchar(10) DEFAULT NULL COMMENT 'without isd code',
  `email` varchar(50) DEFAULT NULL,
  `dt_of_birth` date DEFAULT NULL,
  `wed_date` date DEFAULT NULL,
  `new_customer` varchar(1) DEFAULT NULL COMMENT 'Based on sch a/c no.',
  `ref_no` varchar(100) DEFAULT NULL COMMENT 'online approved payment id',
  `id_scheme_account` int unsigned DEFAULT NULL COMMENT 'online scheme a/c reference id',
  `account_name` varchar(300) DEFAULT NULL,
  `sync_scheme_code` varchar(100) NOT NULL COMMENT 'common code to identify scheme',
  `group_code` varchar(100) DEFAULT NULL COMMENT 'scheme code',
  `scheme_ac_no` int DEFAULT NULL,
  `paid_installments` int DEFAULT '0',
  `is_closed` tinyint(1) DEFAULT '0',
  `closed_by` tinyint unsigned DEFAULT NULL COMMENT '0 - self, 1 - representative',
  `closing_date` datetime DEFAULT NULL,
  `closing_amount` decimal(10,2) DEFAULT '0.00',
  `closing_weight` decimal(12,3) DEFAULT NULL,
  `closing_add_chgs` decimal(10,0) NOT NULL DEFAULT '0',
  `additional_benefits` decimal(10,0) NOT NULL DEFAULT '0',
  `remark_close` varchar(180) DEFAULT NULL,
  `is_transferred` varchar(2) DEFAULT 'N' COMMENT 'Y if trans is read',
  `transfer_date` date DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `custom_entry_date` datetime DEFAULT NULL,
  `is_registered_online` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1- Yes , 2 - online record',
  `nominee` varchar(45) DEFAULT NULL,
  `nominee_mobile` varchar(20) DEFAULT NULL,
  `rate_fixed_in` tinyint(1) DEFAULT NULL COMMENT '1 - web app , 2 - mobile app, 0 - offline   ',
  `fixed_metal_rate` decimal(10,2) DEFAULT NULL,
  `fixed_wgt` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'For, One time Premium(Both)',
  `fixed_rate_on` datetime DEFAULT NULL COMMENT 'Rate Fixed on .For  One time Premium(Amt)',
  `firstPayment_amt` decimal(10,2) DEFAULT NULL COMMENT 'For - First ins as payable settings, One time premium',
  `firstpayment_wgt` decimal(12,3) DEFAULT NULL,
  `one_time_premium` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  `is_online_cus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Yes , 0 - No',
  `country` varchar(30) DEFAULT NULL,
  `start_year` int DEFAULT NULL,
  `dev_remark` varchar(500) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `id_village` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customerorder`
--

DROP TABLE IF EXISTS `customerorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerorder` (
  `id_customerorder` int NOT NULL AUTO_INCREMENT,
  `fin_year_code` varchar(15) DEFAULT NULL,
  `order_no` varchar(20) DEFAULT NULL,
  `pur_no` varchar(20) DEFAULT NULL,
  `is_against_approval_stock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `id_karigar` int DEFAULT NULL,
  `order_type` int NOT NULL DEFAULT '1' COMMENT '1-> Stock Order 2-> Customer Order 3-> Customer Repair 4-> Stock Repair',
  `order_for` tinyint(1) DEFAULT '1' COMMENT '1->Branch 2->Customer 3->Repair',
  `order_to` int unsigned DEFAULT NULL COMMENT 'If Branch will store branch id else will store customer id',
  `work_at` tinyint(1) DEFAULT NULL COMMENT '1 -> INHOUSE\r\n2-> OUTSOURCE',
  `est_id` int DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `createdon` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `order_taken_by` int DEFAULT NULL COMMENT 'id_employee',
  `order_from` int unsigned NOT NULL DEFAULT '1' COMMENT 'id_branch',
  `is_partial_delivery` int NOT NULL DEFAULT '0' COMMENT '1 - Yes , 2 - No',
  `order_pcs` int DEFAULT NULL,
  `order_approx_wt` decimal(10,3) DEFAULT NULL,
  `delivered_wt` decimal(10,3) DEFAULT '0.000',
  `delivered_qty` int NOT NULL DEFAULT '0',
  `cus_remaining_days` int DEFAULT NULL,
  `rate_calc_from` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-order rate,2-current rate',
  `cus_ord_ref` int DEFAULT NULL,
  `order_status` tinyint NOT NULL DEFAULT '0',
  `id_stock_issue` int DEFAULT NULL,
  `balance_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 1 -> Metal Balance, 2 -> Cash Balance',
  `rate_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> order rate , 2 -> delivery rate',
  `reject_reason` text,
  `added_through` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-admin,2-Estimation App',
  `cancelled_date` datetime DEFAULT NULL,
  `cancelled_by` int DEFAULT NULL,
  `closed_date` datetime DEFAULT NULL,
  `closed_by` int DEFAULT NULL,
  `id_supp_catalogue` int DEFAULT NULL,
  `counter_id` int DEFAULT NULL COMMENT 'from session',
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_customerorder`),
  KEY `fin_year_code` (`fin_year_code`),
  KEY `order_no` (`order_no`),
  KEY `id_karigar` (`id_karigar`),
  KEY `order_type` (`order_type`),
  KEY `order_for` (`order_for`),
  KEY `order_to` (`order_to`),
  KEY `est_id` (`est_id`),
  KEY `order_from` (`order_from`),
  KEY `is_partial_delivery` (`is_partial_delivery`),
  KEY `order_status` (`order_status`)
) ENGINE=InnoDB AUTO_INCREMENT=588 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customerorderdetails`
--

DROP TABLE IF EXISTS `customerorderdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerorderdetails` (
  `id_orderdetails` int unsigned NOT NULL AUTO_INCREMENT,
  `id_customerorder` int NOT NULL,
  `orderno` varchar(55) DEFAULT NULL,
  `ortertype` int DEFAULT '1' COMMENT '1->Catalog order 2-> Customer order 3-> Repair order 4 -> Catalog Admin order ',
  `is_customeitem` tinyint unsigned DEFAULT '0' COMMENT 'Customer Item 0->No 1->Yes',
  `id_product` int unsigned DEFAULT NULL,
  `design_no` int unsigned DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `wast_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_mc_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1-Per Gram,2-Piece',
  `mc` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Making Charge amount',
  `charge_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stn_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `seal` varchar(20) DEFAULT NULL,
  `melting` varchar(20) DEFAULT NULL,
  `itemname` varchar(60) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `less_wt` decimal(10,3) DEFAULT NULL,
  `net_wt` decimal(10,3) DEFAULT NULL,
  `id_weight_range` int DEFAULT NULL,
  `size` varchar(100) DEFAULT NULL,
  `totalitems` varchar(100) DEFAULT NULL,
  `is_set_items` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-No need to generate multiple rows',
  `image` text COMMENT 'Multiple images will store separation of ##',
  `is_urgent` tinyint unsigned DEFAULT NULL,
  `deliverydate` date DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `description` varchar(345) DEFAULT NULL,
  `orderstatus` tinyint unsigned DEFAULT '0' COMMENT '0-> Pending 1->Process 2-> Confirm 3->Work in progress 4->delivery ready 5-> delivered 6->Canceled 7->Closed',
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_sgst` decimal(10,2) DEFAULT NULL,
  `total_cgst` decimal(10,2) DEFAULT NULL,
  `total_igst` decimal(10,2) DEFAULT NULL,
  `repair_percent` int DEFAULT NULL COMMENT 'Repair percentage',
  `repair_tot_tax` decimal(10,2) DEFAULT NULL COMMENT 'Total Tax Amount',
  `sample_details` varchar(200) DEFAULT NULL,
  `orderclosed` tinyint unsigned DEFAULT '0' COMMENT '0->no 1->yes',
  `cancelreason` varchar(345) DEFAULT NULL,
  `id_purity` int unsigned DEFAULT NULL,
  `rate_per_gram` decimal(10,2) DEFAULT NULL,
  `is_chain` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not chain , 1- chain',
  `hook_type` int DEFAULT NULL COMMENT '1 - S type , 2 - M type',
  `is_stockdelivery` int DEFAULT NULL COMMENT '0 - Assign karigar , 1 - Stock delivery',
  `branch_id` int DEFAULT NULL COMMENT 'order allocated branch id',
  `id_employee` int DEFAULT NULL COMMENT 'Order Taken Employee Detalis',
  `is_partial_delivery` int NOT NULL DEFAULT '0' COMMENT '1 - Yes , 2 - No',
  `delivered_qty` int NOT NULL DEFAULT '0',
  `delivered_wt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_ref_no` varchar(60) DEFAULT NULL COMMENT 'if custom order get refernece number',
  `smith_due_date` date DEFAULT NULL COMMENT 'custom order qive duedate to do this order',
  `is_reorder` tinyint(1) NOT NULL DEFAULT '0',
  `reject_reason` varchar(500) DEFAULT NULL,
  `delivered_date` datetime DEFAULT NULL,
  `smith_remainder_date` datetime DEFAULT NULL,
  `cus_due_date` datetime DEFAULT NULL,
  `delivered_by` int DEFAULT NULL,
  `order_cancelled_date` datetime DEFAULT NULL,
  `order_closed_date` datetime DEFAULT NULL,
  `cancelled_by` int DEFAULT NULL,
  `closed_by` int DEFAULT NULL,
  `bill_det_id` int DEFAULT NULL COMMENT 'For Repair Items From Bill Details',
  `completed_weight` decimal(10,3) DEFAULT NULL,
  `bill_id` int DEFAULT NULL COMMENT '	Repair Order Delivery Bill Id',
  `current_branch` int DEFAULT NULL,
  `tag_id` int DEFAULT NULL COMMENT 'For Stock Repair Order items',
  `tag_name` varchar(45) DEFAULT NULL,
  `approval_tagid` int DEFAULT NULL,
  `pure_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `id_karigar` int DEFAULT NULL COMMENT 'For Repair Orders',
  `id_repair_master` int DEFAULT NULL,
  `assign_to` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Karigar,2-Employee',
  `emp_id` int DEFAULT NULL,
  `id_supp_catalogue` int DEFAULT NULL,
  `cus_orderdet_ref` int DEFAULT NULL,
  `assigned_by` int DEFAULT NULL,
  `assigned_on` datetime DEFAULT NULL,
  `kar_remark` varchar(200) DEFAULT NULL COMMENT 'Karigar Reassigned In Repair order allotment',
  PRIMARY KEY (`id_orderdetails`),
  KEY `id_customerorder` (`id_customerorder`),
  KEY `orderno` (`orderno`),
  KEY `ortertype` (`ortertype`),
  KEY `is_customeitem` (`is_customeitem`),
  KEY `id_product` (`id_product`),
  KEY `design_no` (`design_no`),
  KEY `id_sub_design` (`id_sub_design`),
  KEY `orderstatus` (`orderstatus`),
  KEY `tag_id` (`tag_id`),
  KEY `bill_det_id` (`bill_det_id`),
  KEY `bill_id` (`bill_id`),
  KEY `current_branch` (`current_branch`),
  KEY `id_karigar` (`id_karigar`),
  KEY `emp_id` (`emp_id`),
  KEY `assign_to` (`assign_to`),
  KEY `IdxorderApprovalTagId` (`approval_tagid`)
) ENGINE=InnoDB AUTO_INCREMENT=620 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customeroutstanding`
--

DROP TABLE IF EXISTS `customeroutstanding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customeroutstanding` (
  `rowid` int NOT NULL AUTO_INCREMENT,
  `Customer` varchar(100) DEFAULT NULL,
  `CustomerId` int DEFAULT NULL,
  `Mobile` bigint DEFAULT NULL,
  `ClosingAmt` decimal(10,2) DEFAULT NULL,
  `OldRef` varchar(20) DEFAULT NULL,
  `Remarks` varchar(225) DEFAULT NULL,
  `Branch` varchar(100) DEFAULT NULL,
  `branchid` int NOT NULL,
  `cusId` int NOT NULL,
  `cuscreatedon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cusupdatestatus` int NOT NULL DEFAULT '0',
  `cusupdatedon` datetime NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_collection`
--

DROP TABLE IF EXISTS `daily_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_collection` (
  `id_daily_collection` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `date` date NOT NULL,
  `today_collection_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Today  amt collection for amount scheme',
  `today_collection_wgt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Today amt collection for weight scheme',
  `today_weight` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'Today''s wgt for weight scheme',
  `today_wgt_avg_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Average metal rate for weight scheme',
  `today_bonus_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Bonus amount allocated today',
  `closing_balance_amt` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT 'Today''s closing amt for amt scheme',
  `closing_balance_wgt` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT 'Today''s closing amt for wgt scheme',
  `closing_weight` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'Today''s closing wgt for weight scheme',
  `closing_bonus_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Balance Bonus amount',
  `today_cancelled_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Amt cancelled today for amount scheme',
  `today_cancelled_wgt` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'Amt cancelled today for weight scheme',
  `weight_cancelled` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'Weignt cancelled for weight scheme',
  `today_cancelled_bonus_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Today cancelled payment''s bonus amt',
  `amtSchClosedAmt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'amount scheme a/c closed amount',
  `amtSchBonusReversed` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Allocated bonus amount reversed today on preclose',
  `wgtSchClosedAmt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'wgt scheme a/c closed amount',
  `wgtSchClosedWgt` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'wgt scheme a/c closed wgt',
  `wgtSchAvgRate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Wgt sch average metal  rate',
  `date_add` datetime DEFAULT NULL,
  `id_scheme` int NOT NULL,
  PRIMARY KEY (`id_daily_collection`),
  UNIQUE KEY `id_branch` (`id_branch`,`date`),
  KEY `date` (`date`),
  KEY `date_2` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_collection_scheme_wise`
--

DROP TABLE IF EXISTS `daily_collection_scheme_wise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_collection_scheme_wise` (
  `id_daily_collection` int NOT NULL,
  `id_branch` int DEFAULT NULL,
  `id_scheme` int NOT NULL,
  `date` date NOT NULL,
  `today_collection_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Today  amt collection for amount scheme',
  `today_collection_wgt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Today amt collection for weight scheme',
  `today_wgt_avg_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Average metal rate for weight scheme',
  `today_bonus_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Bonus amount allocated today',
  `today_closed_amt` decimal(10,2) DEFAULT NULL,
  `today_closed_wgt` decimal(10,3) DEFAULT NULL,
  `today_closed_bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `today_bonus_deduction` decimal(10,2) DEFAULT NULL,
  `today_closed_add_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `today_cancelled_amt` decimal(10,2) DEFAULT NULL,
  `today_cancelled_wgt` decimal(10,2) DEFAULT NULL,
  `today_cancelled_bonus` decimal(10,2) DEFAULT NULL,
  `closing_balance_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Today''s closing amt for amt scheme',
  `closing_balance_wgt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Today''s closing amt for wgt scheme',
  `closing_bonus_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Balance Bonus amount',
  `bonus_amt_reversed` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Allocated bonus amount reversed today on preclose',
  `date_add` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dashboard_access`
--

DROP TABLE IF EXISTS `dashboard_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dashboard_access` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_profile` int unsigned DEFAULT NULL,
  `id_dashboardmenu` int unsigned DEFAULT NULL,
  `view` int unsigned DEFAULT NULL,
  `add` int unsigned DEFAULT NULL,
  `edit` int unsigned DEFAULT NULL,
  `delete` int unsigned DEFAULT NULL,
  `is_default` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `id_profile` (`id_profile`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dashboard_menu`
--

DROP TABLE IF EXISTS `dashboard_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dashboard_menu` (
  `id_dashboardmenu` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL DEFAULT '',
  `link` varchar(100) NOT NULL DEFAULT '#',
  `parent` int NOT NULL DEFAULT '0',
  `sort` int DEFAULT NULL,
  `icon` varchar(45) DEFAULT NULL,
  `active` int DEFAULT NULL COMMENT '0->Inactive1->Active ',
  PRIMARY KEY (`id_dashboardmenu`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dbbackup_log`
--

DROP TABLE IF EXISTS `dbbackup_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dbbackup_log` (
  `id_dbbackup` int unsigned NOT NULL AUTO_INCREMENT,
  `backup_date` datetime DEFAULT NULL,
  `id_employee` int unsigned DEFAULT NULL,
  `filename` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_dbbackup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dealer`
--

DROP TABLE IF EXISTS `dealer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dealer` (
  `id_dealer` int unsigned NOT NULL,
  `code` varchar(100) DEFAULT NULL COMMENT 'Dealer code ',
  `id_branch` int DEFAULT NULL COMMENT 'Registered branch',
  `id_village` int DEFAULT NULL,
  `title` varchar(5) DEFAULT NULL,
  `initials` varchar(10) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  `firstname` varchar(32) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_of_wed` date DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `id_address` int unsigned DEFAULT '0',
  `id_employee` int unsigned DEFAULT '0',
  `cus_img` varchar(50) DEFAULT NULL,
  `active` tinyint unsigned NOT NULL DEFAULT '0',
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5- Sync',
  `gst_number` varchar(50) NOT NULL,
  `religion` int DEFAULT NULL COMMENT 'Dealer religion',
  `kyc_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - KYC Completed',
  `comments` varchar(200) DEFAULT NULL,
  `custom_entry_date` date DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delete_notifications`
--

DROP TABLE IF EXISTS `delete_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delete_notifications` (
  `id_del_note` int NOT NULL AUTO_INCREMENT,
  `id_notification` int DEFAULT NULL COMMENT 'this id from sent_notification table ',
  `id_customer` int DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_del_note`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer wise deleted notifications from mobile app';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_payment`
--

DROP TABLE IF EXISTS `deleted_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deleted_payment` (
  `id_payment` int NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Saving Scheme, 2 - Gift Card',
  `id_scheme_account` int unsigned NOT NULL,
  `id_scheme` int DEFAULT NULL,
  `date_payment` datetime NOT NULL,
  `due_year_month` varchar(10) DEFAULT NULL COMMENT 'Used from v5 version',
  `installment` int unsigned DEFAULT NULL COMMENT 'installement no',
  `due_type` varchar(5) DEFAULT 'N',
  `id_employee` int DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `id_payGateway` int DEFAULT NULL,
  `id_transaction` varchar(50) DEFAULT NULL,
  `payu_id` varchar(25) DEFAULT NULL COMMENT 'Razor Pay --> order id,Payu - >payu id',
  `id_post_payment` int unsigned DEFAULT NULL,
  `id_drawee` int unsigned DEFAULT NULL,
  `due_month` int DEFAULT NULL COMMENT 'Paid for Month',
  `due_year` year DEFAULT NULL,
  `custom_entry_date` datetime DEFAULT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(45) DEFAULT NULL,
  `dues` tinyint unsigned DEFAULT '1',
  `act_amount` decimal(10,0) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `no_of_dues` int unsigned DEFAULT '1',
  `add_charges` decimal(10,2) DEFAULT NULL,
  `metal_rate` decimal(10,2) DEFAULT '0.00',
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `remark` text,
  `receipt_no` int DEFAULT NULL COMMENT 'receipt_no',
  `display_receipt_no` text COMMENT 'Display this receipt number in all pages ',
  `fix_weight` tinyint DEFAULT '0',
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 -admin , 1- web app, 2 - mobile app, 3 - Admin App, 4 - Cashfree Subscription, 5 - Sync, 6 - Import',
  `free_payment` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'not used',
  `ref_trans_id` varchar(45) DEFAULT NULL,
  `discountAmt` varchar(10) DEFAULT NULL,
  `actual_trans_amt` decimal(10,0) NOT NULL DEFAULT '0',
  `is_editing_enabled` tinyint unsigned DEFAULT '0',
  `approval_date` datetime DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date of insert',
  `is_offline` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -online , 1 - offline',
  `gst` decimal(10,0) NOT NULL,
  `gst_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -amount inclusive of GST , 0 -amount exclusive of gst',
  `gst_amount` decimal(12,3) DEFAULT NULL COMMENT 'GST Calculated by LMX',
  `redeemed_amount` decimal(10,2) DEFAULT NULL,
  `is_point_credited` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes , 2 - No credit required',
  `is_print_taken` tinyint NOT NULL DEFAULT '0' COMMENT '1 - taken , 0 -not taken',
  `id_otp` int DEFAULT NULL,
  `mer_net_amount` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `mer_service_fee` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `cgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `igst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `sgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `gateway_requestaction` varchar(45) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `is_settled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - If payment amount settled by payu to merchant.',
  `estimation_id` int DEFAULT NULL COMMENT 'Estimation Id',
  `offline_tran_uniqueid` varchar(150) DEFAULT NULL COMMENT 'Using for Khimji',
  `old_metal_amount` decimal(10,2) DEFAULT NULL,
  `old_metal_weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `is_gateway_verified` tinyint(1) DEFAULT '0' COMMENT '1 - status verified with gateway',
  `saved_benefits` decimal(10,3) DEFAULT NULL COMMENT 'For digi gold(weight) ',
  `saved_benefit_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Benefit Amount',
  `benefit_value` decimal(10,3) DEFAULT NULL COMMENT 'For digi gold',
  `benefit_type` int DEFAULT NULL COMMENT 'For digi gold',
  `istransfered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Tally integration sync flag',
  `istallytransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `id_agent` int unsigned DEFAULT NULL COMMENT 'Referred agent',
  `receipt_year` int DEFAULT NULL COMMENT 'Receipt financial year code',
  `paidBy_mobile` int DEFAULT NULL,
  `paidBy_id_customer` int DEFAULT NULL,
  `form_secret` varchar(250) DEFAULT NULL,
  `due_monthyear` varchar(10) DEFAULT NULL COMMENT 'used in v3 version and below',
  `pay_email` varchar(100) DEFAULT NULL,
  `counter_id` varchar(100) DEFAULT NULL COMMENT 'to check counter wise collection ',
  `grace_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `is_limit_exceed` tinyint NOT NULL DEFAULT '0' COMMENT '0- due duration not exceeded, 1- due duration limit exceeded',
  `due_date_to` date DEFAULT NULL,
  `refund_id` varchar(50) DEFAULT NULL,
  `metalrate_edit_date` datetime DEFAULT NULL,
  `dev_remark` text,
  `topup_slab_data` text COMMENT 'topup scheme custom payable slab data',
  PRIMARY KEY (`id_payment`),
  UNIQUE KEY `id_transaction` (`id_transaction`),
  UNIQUE KEY `id_transaction_2` (`id_transaction`,`ref_trans_id`),
  KEY `FK_payment_scheme_account` (`id_scheme_account`),
  KEY `id_branch` (`id_branch`),
  KEY `date_payment` (`date_payment`),
  KEY `payment_mode` (`payment_mode`),
  KEY `payment_ref_number` (`payment_ref_number`),
  KEY `payment_status` (`payment_status`),
  KEY `receipt_no` (`receipt_no`),
  KEY `type` (`type`),
  KEY `id_employee` (`id_employee`),
  KEY `id_payGateway` (`id_payGateway`),
  KEY `custom_entry_date` (`custom_entry_date`),
  KEY `installment` (`installment`),
  KEY `dues` (`dues`),
  KEY `entry_date` (`entry_date`),
  KEY `is_offline` (`is_offline`),
  KEY `is_print_taken` (`is_print_taken`),
  KEY `id_agent` (`id_agent`),
  KEY `istransfered` (`istransfered`),
  KEY `istallytransfered` (`istallytransfered`),
  KEY `is_settled` (`is_settled`),
  KEY `id_scheme_account` (`id_scheme_account`),
  KEY `payment_status_2` (`payment_status`),
  KEY `date_payment_2` (`date_payment`),
  KEY `date_add` (`date_add`),
  KEY `added_by` (`added_by`),
  KEY `payment_mode_2` (`payment_mode`),
  KEY `id_transaction_3` (`id_transaction`),
  KEY `ref_trans_id` (`ref_trans_id`),
  KEY `id_scheme_account_2` (`id_scheme_account`),
  KEY `payment_status_3` (`payment_status`),
  KEY `date_payment_3` (`date_payment`),
  KEY `date_add_2` (`date_add`),
  KEY `added_by_2` (`added_by`),
  KEY `payment_mode_3` (`payment_mode`),
  KEY `id_transaction_4` (`id_transaction`),
  KEY `ref_trans_id_2` (`ref_trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_scheme_account`
--

DROP TABLE IF EXISTS `deleted_scheme_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deleted_scheme_account` (
  `id_scheme_account` int unsigned NOT NULL AUTO_INCREMENT,
  `id_scheme` int unsigned DEFAULT '0',
  `id_customer` int unsigned NOT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `scheme_acc_number` int DEFAULT NULL,
  `total_paid_ins` int NOT NULL DEFAULT '0' COMMENT 'Installments paid',
  `dg_target_value_wgt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `dg_target_wgt_achieved` decimal(10,3) NOT NULL DEFAULT '0.000',
  `display_ac_no` text COMMENT 'Display this account number in all pages ',
  `account_name` varchar(300) DEFAULT NULL,
  `ref_no` varchar(100) DEFAULT NULL,
  `group_code` varchar(30) DEFAULT NULL,
  `msno` int DEFAULT NULL,
  `paid_installments` int DEFAULT '0',
  `balance_amount` decimal(10,2) DEFAULT '0.00',
  `is_opening` tinyint(1) DEFAULT '0',
  `balance_weight` decimal(12,3) DEFAULT '0.000',
  `last_paid_weight` decimal(12,3) DEFAULT '0.000',
  `last_paid_chances` int DEFAULT '0',
  `last_paid_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `maturity_date` date DEFAULT NULL COMMENT 'If null, no maturity limit .ins count only',
  `lapse_date` date DEFAULT NULL,
  `employee_approved` int unsigned DEFAULT '0',
  `employee_closed` int unsigned DEFAULT '0',
  `closing_date` datetime DEFAULT NULL,
  `closing_balance` decimal(10,3) DEFAULT '0.000',
  `closing_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_weight` decimal(12,3) DEFAULT NULL,
  `closed_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - self, 1 - representative',
  `rep_name` varchar(70) DEFAULT NULL COMMENT 'representative name',
  `rep_mobile` varchar(45) DEFAULT NULL COMMENT 'representative mobile',
  `remark_open` varchar(180) DEFAULT NULL,
  `remark_close` varchar(180) DEFAULT NULL,
  `req_close` tinyint(1) NOT NULL DEFAULT '0',
  `is_closed` tinyint(1) DEFAULT '0',
  `is_new` varchar(1) DEFAULT 'Y' COMMENT 'Y - New Customer, N - Existing Customer',
  `active` tinyint(1) DEFAULT '0',
  `disable_payment` tinyint(1) DEFAULT '0',
  `disable_pay_reason` text COMMENT 'Reason for disable payment',
  `date_add` datetime DEFAULT NULL,
  `custom_entry_date` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5 - Sync',
  `benefit` decimal(10,2) DEFAULT '0.00' COMMENT 'Benefit given based on scheme settings',
  `deduction` decimal(10,2) DEFAULT '0.00' COMMENT 'Deduction applied based on scheme settings',
  `closing_add_chgs` decimal(10,2) NOT NULL DEFAULT '0.00',
  `additional_benefits` decimal(10,2) NOT NULL DEFAULT '0.00',
  `add_ben_type` tinyint NOT NULL DEFAULT '0' COMMENT '0 -> amount, 1 -> weight',
  `is_registered` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 -  registered a/c',
  `referal_code` varchar(45) DEFAULT NULL COMMENT 'emp or cus referal code',
  `is_refferal_by` tinyint(1) DEFAULT NULL COMMENT '0-customer referal , 1 - emp referal',
  `cus_refferal_cal_value` int DEFAULT NULL,
  `emp_refferal_cal_value` int DEFAULT NULL,
  `avg_wgtlimit` int DEFAULT NULL,
  `pan_no` varchar(50) DEFAULT NULL,
  `firstPayment_amt` varchar(10) DEFAULT NULL COMMENT 'For - First ins as payable settings, One time premium',
  `firstpayment_wgt` decimal(12,3) DEFAULT NULL COMMENT 'First installment payment weight',
  `show_gift_article` tinyint(1) NOT NULL DEFAULT '0',
  `converted_amt` decimal(10,2) DEFAULT '0.00',
  `received_wgt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Received Weight for One time Preminum Scheme',
  `fixed_metal_rate` decimal(10,2) DEFAULT NULL,
  `fixed_wgt` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'For  One time Premium(Amt)',
  `fixed_rate_on` datetime DEFAULT NULL COMMENT 'Rate Fixed on .For  One time Premium(Amt)',
  `rate_fixed_in` int DEFAULT NULL COMMENT '1-Web App,2-Mobile App-3-Offline',
  `avg_payable` decimal(12,3) NOT NULL DEFAULT '0.000',
  `auto_debit_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Not Subscribed, 1- INITIALIZED, 2- BANK_APPROVAL_PENDING, 3- ACTIVE, 4- ON_HOLD, 5- CANCELLED, 6- COMPLETED	',
  `closing_paid_amt` decimal(10,2) DEFAULT NULL COMMENT 'Total amount paid by customer [Update on closing]',
  `closing_benefits` decimal(10,2) DEFAULT NULL COMMENT 'Applied Scheme Benefits[Update on closing]',
  `closing_interest_val` varchar(10) DEFAULT NULL,
  `closing_deductions` decimal(10,2) DEFAULT NULL COMMENT 'Applied Scheme Deductions[Update on closing]',
  `is_utilized` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0->No,1->Yes',
  `utilized_type` int DEFAULT NULL COMMENT '1-cash refund,2-adj in sales	',
  `Closing_id_branch` int DEFAULT NULL,
  `id_employee` int unsigned DEFAULT '0',
  `closed_remarks` text,
  `updated_by` int DEFAULT NULL,
  `gift_debt_amt` decimal(10,6) DEFAULT NULL COMMENT 'Gift issued deduction amount ',
  `istransfered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Tally integration sync flag',
  `agent_code` varchar(45) DEFAULT NULL COMMENT 'agent code',
  `id_agent` int unsigned DEFAULT NULL COMMENT 'Referred agent',
  `start_year` int DEFAULT NULL COMMENT 'A/c starting Financial year code',
  `form_secret` varchar(100) DEFAULT NULL,
  `issue_self_giftBonus` tinyint NOT NULL DEFAULT '2' COMMENT '0 - Bonus , 1- gift, 2 - none (Customer can choose the benefits as gift or bonus by them self from app) 3 - Gift issued from employee(Collection App)',
  `tot_genadv_amt_paid` int DEFAULT NULL,
  `tot_genadv_wgt_paid` decimal(10,3) DEFAULT NULL,
  `tot_genadv_benefit` int DEFAULT NULL,
  `tot_genadv_benefit_wgt` decimal(10,3) DEFAULT NULL,
  `duplicate_passbook_issued` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-yes.2-no',
  `lump_joined_weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'lump sum scheme-> customer choosing total weight at the time of scheme join for all total installments',
  `lump_payable_weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'lump sum scheme-> weight payable per installment calculated by (lump_joined_weight / total_installments)',
  `bonus_percent` int DEFAULT NULL COMMENT 'chit benefit percentage field added for retail purpose',
  `store_closing_balance_as` tinyint DEFAULT NULL,
  `topup_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Joining time booking amount',
  `topup_weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Joining time booking weight',
  `topup_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Joining time booking rate',
  PRIMARY KEY (`id_scheme_account`),
  UNIQUE KEY `AccIndex` (`id_scheme_account`,`id_customer`,`id_scheme`,`scheme_acc_number`),
  UNIQUE KEY `form_secret` (`form_secret`),
  UNIQUE KEY `ref_no` (`ref_no`),
  KEY `FK_scheme_account_scheme` (`id_scheme`),
  KEY `id_scheme` (`id_scheme`),
  KEY `id_scheme_account` (`id_scheme_account`),
  KEY `id_customer` (`id_customer`),
  KEY `scheme_acc_number` (`scheme_acc_number`),
  KEY `group_code` (`group_code`),
  KEY `is_closed` (`is_closed`),
  KEY `closed_by` (`closed_by`),
  KEY `closing_date` (`closing_date`),
  KEY `id_branch` (`id_branch`),
  KEY `id_scheme_2` (`id_scheme`),
  KEY `id_scheme_account_2` (`id_scheme_account`),
  KEY `id_customer_2` (`id_customer`),
  KEY `scheme_acc_number_2` (`scheme_acc_number`),
  KEY `group_code_2` (`group_code`),
  KEY `is_closed_2` (`is_closed`),
  KEY `closed_by_2` (`closed_by`),
  KEY `closing_date_2` (`closing_date`),
  KEY `id_branch_2` (`id_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=813 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `denomination`
--

DROP TABLE IF EXISTS `denomination`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `denomination` (
  `id_denomination` int NOT NULL AUTO_INCREMENT,
  `value` int NOT NULL,
  `sort_order` int NOT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_denomination`),
  UNIQUE KEY `value` (`value`),
  UNIQUE KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department` (
  `id_dept` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id_dept`),
  KEY `id_dept` (`id_dept`),
  KEY `id_dept_2` (`id_dept`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `designation`
--

DROP TABLE IF EXISTS `designation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `designation` (
  `id_design` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id_design`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `discount`
--

DROP TABLE IF EXISTS `discount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discount` (
  `id_discount` int NOT NULL AUTO_INCREMENT,
  `free_first_payment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0= off, 1= on',
  PRIMARY KEY (`id_discount`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `drawee_account`
--

DROP TABLE IF EXISTS `drawee_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drawee_account` (
  `id_drawee` int unsigned NOT NULL AUTO_INCREMENT,
  `account_no` varchar(45) DEFAULT NULL,
  `account_name` varchar(50) DEFAULT NULL,
  `id_bank` varchar(45) DEFAULT NULL,
  `branch` varchar(200) DEFAULT NULL,
  `ifsc_code` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_drawee`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ej_metalratehistory`
--

DROP TABLE IF EXISTS `ej_metalratehistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ej_metalratehistory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Metaltype` varchar(8) DEFAULT NULL,
  `InventLocationId` varchar(6) DEFAULT NULL,
  `CONFIGID` varchar(3) DEFAULT NULL,
  `TRANSDATE` varchar(10) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `RATE` decimal(6,2) DEFAULT NULL,
  `TIME` int DEFAULT NULL,
  `CREATEDDATETIME` varchar(7) DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emp_closing_incentive`
--

DROP TABLE IF EXISTS `emp_closing_incentive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emp_closing_incentive` (
  `id_incentive` int NOT NULL AUTO_INCREMENT,
  `id_scheme` int DEFAULT NULL,
  `incentive_from` decimal(10,2) DEFAULT NULL,
  `incentive_to` decimal(10,2) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Amt,2-Percentage',
  `value` decimal(10,2) DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id_incentive`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
  `id_employee` int unsigned NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT '1',
  `id_company` int DEFAULT NULL COMMENT 'company wise employee',
  `login_branches` varchar(45) DEFAULT NULL COMMENT 'Branches employees can access. Multiple branch id Separated by Comma(,)',
  `lastname` varchar(32) DEFAULT NULL,
  `firstname` varchar(32) DEFAULT NULL,
  `date_of_birth` datetime DEFAULT NULL,
  `emp_code` varchar(20) DEFAULT NULL,
  `dept` int unsigned DEFAULT '0',
  `designation` int unsigned DEFAULT '0',
  `date_of_join` datetime DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `pwd_hash` varchar(300) DEFAULT NULL,
  `id_profile` int unsigned DEFAULT '2' COMMENT 'user type',
  `is_lmx` tinyint(1) DEFAULT '0',
  `is_logged_in` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'is logged in to any device',
  `device_logged_in` varchar(100) DEFAULT NULL COMMENT 'device logged in last',
  `active` tinyint(1) DEFAULT '0',
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `emp_type` int NOT NULL DEFAULT '1' COMMENT '1-> Office Staffs 2->Transporter',
  `device_uuid` varchar(100) DEFAULT NULL,
  `id_collection_device` int DEFAULT NULL COMMENT 'id from employee devices table',
  `enable_chit_collection` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not allowed to login collection app, 1 - allowed to login collection app',
  `is_store_manager` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -> employee 1-> manager',
  PRIMARY KEY (`id_employee`),
  KEY `id_branch` (`id_branch`),
  KEY `id_company` (`id_company`),
  KEY `id_profile` (`id_profile`),
  KEY `active` (`active`),
  KEY `emp_type` (`emp_type`),
  KEY `is_lmx` (`is_lmx`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`),
  KEY `passwd` (`passwd`),
  KEY `username_2` (`username`),
  KEY `id_profile_2` (`id_profile`),
  KEY `emp_code` (`emp_code`),
  KEY `email_2` (`email`),
  KEY `mobile_2` (`mobile`),
  KEY `passwd_2` (`passwd`),
  KEY `username_3` (`username`),
  KEY `id_profile_3` (`id_profile`),
  KEY `emp_code_2` (`emp_code`)
) ENGINE=InnoDB AUTO_INCREMENT=586 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_devices`
--

DROP TABLE IF EXISTS `employee_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_devices` (
  `id_collection_device` int NOT NULL AUTO_INCREMENT,
  `device_uuid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` int DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `emp_id` int unsigned NOT NULL COMMENT 'Employee ID from employees table',
  `app_type` int NOT NULL DEFAULT '0' COMMENT '1 -> Chit Collection app, 2 -> Estimation app',
  `device_info` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Devices information',
  `device_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -> Device enabled, 1 -> Device disabled',
  PRIMARY KEY (`id_collection_device`),
  UNIQUE KEY `device_uuid` (`device_uuid`,`device_type`,`emp_id`,`app_type`),
  KEY `fkEmployee` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_settings`
--

DROP TABLE IF EXISTS `employee_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_settings` (
  `id_emp_sett` int unsigned NOT NULL AUTO_INCREMENT,
  `id_employee` int unsigned NOT NULL,
  `allowed_old_met_pur` tinyint(1) DEFAULT NULL COMMENT '1 - All Metal, 2 - Gold , 3 - Silver',
  `disc_limit_type` tinyint(1) DEFAULT NULL COMMENT '1 - Amount, 2 - Percent',
  `disc_limit` decimal(10,2) DEFAULT NULL,
  `allow_branch_transfer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `allow_day_close` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Yes, 0 - No',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `access_time_from` time DEFAULT NULL COMMENT 'Allowed access time for the user',
  `access_time_to` time DEFAULT NULL COMMENT 'Allowed access time for the user',
  `otp_dis_approval` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Yes,1-No',
  `otp_credit_approval` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0->No , 1->YES',
  `otp_mcva_dis_approval` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Yes,1-No',
  `allow_manual_rate` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Allow Manual Rate Edit in Estimation',
  `min_gold_tol` decimal(10,2) NOT NULL DEFAULT '50.00',
  `max_gold_tol` decimal(10,2) NOT NULL DEFAULT '50.00',
  `min_silver_tol` decimal(10,2) NOT NULL DEFAULT '50.00',
  `max_silver_tol` decimal(10,2) NOT NULL DEFAULT '50.00',
  `bulk_wast_disc_limit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dia_disc_limit` decimal(10,2) NOT NULL DEFAULT '0.00',
  UNIQUE KEY `id_emp_sett` (`id_emp_sett`),
  UNIQUE KEY `id_employee` (`id_employee`)
) ENGINE=InnoDB AUTO_INCREMENT=1037 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form_logger`
--

DROP TABLE IF EXISTS `form_logger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_logger` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `log_datetime` datetime NOT NULL,
  `log_emp_id` int NOT NULL,
  `log_form` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_operation` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_url` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_ip` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_useragent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_data` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7705 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gateway`
--

DROP TABLE IF EXISTS `gateway`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gateway` (
  `id_pg` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `pg_name` varchar(45) NOT NULL,
  `pg_code` int NOT NULL,
  `param_1` varchar(300) DEFAULT NULL COMMENT 'key, Atom MID',
  `param_2` varchar(300) DEFAULT NULL COMMENT 'salt, Atom TransPwd',
  `param_3` text COMMENT 'Access code for ccavenue/ Merchant code for Techprocess',
  `param_4` text COMMENT 'merchant_id for ccavenue / iv for ALTER TABLE `gateway` CHANGE `param_2` `param_2` VARCHAR(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT ''salt, Atom TransPwd',
  `api_url` varchar(150) DEFAULT NULL COMMENT 'api base url',
  `type` tinyint unsigned DEFAULT '0' COMMENT '0 - demo, 1- real',
  `is_default` tinyint unsigned DEFAULT '0',
  `pg_icon` text,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `saveCard` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `creditCard` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `debitCard` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `netBanking` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `date_add` datetime DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  `is_primary_gateway` tinyint(1) NOT NULL DEFAULT '0' COMMENT '( Default selected gateway ) 0 - No, 1- Yes',
  `description` text,
  `sort` int DEFAULT NULL COMMENT 'Sorting order',
  PRIMARY KEY (`id_pg`),
  UNIQUE KEY `id_branch` (`id_branch`,`type`,`sort`),
  KEY `id_pg` (`id_pg`),
  KEY `id_branch_2` (`id_branch`),
  KEY `is_default` (`is_default`),
  KEY `active` (`active`),
  KEY `id_pg_2` (`id_pg`),
  KEY `id_branch_3` (`id_branch`),
  KEY `is_default_2` (`is_default`),
  KEY `active_2` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gateway_settled_payments`
--

DROP TABLE IF EXISTS `gateway_settled_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gateway_settled_payments` (
  `id_gateway_settled` int NOT NULL AUTO_INCREMENT,
  `id_payGateway` int DEFAULT NULL,
  `txnid` varchar(45) NOT NULL,
  `gateway_id` varchar(45) NOT NULL COMMENT 'Payu - payu_id , CCAvenue- tracking_id , Cashfree - referenceId',
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_updated` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - updated as settled in payment table',
  `payment_date` datetime NOT NULL,
  `date_upd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mer_net_amount` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `mer_service_fee` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `cgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `igst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `sgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `gateway_requestaction` varchar(45) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  PRIMARY KEY (`id_gateway_settled`),
  UNIQUE KEY `txnid` (`txnid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `general`
--

DROP TABLE IF EXISTS `general`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general` (
  `id_general` int NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci,
  `content` text COLLATE utf8mb4_unicode_ci,
  `type` int DEFAULT NULL COMMENT '1-T&c,2-FAQ, 3- aboutus,4-privacy policy\r\n',
  `img` text COLLATE utf8mb4_unicode_ci,
  `lang` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 => Tamil , 2=> English',
  PRIMARY KEY (`id_general`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `general_advance_mode_detail`
--

DROP TABLE IF EXISTS `general_advance_mode_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_advance_mode_detail` (
  `id_pay_mode_details` int NOT NULL AUTO_INCREMENT,
  `id_adv_payment` int DEFAULT NULL,
  `NB_type` int DEFAULT NULL COMMENT '1-RTGS,2-IMPS',
  `payment_date` datetime DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `payment_amount` decimal(10,0) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `net_banking_date` date DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `remark` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `card_type` int DEFAULT NULL COMMENT '1-RUPAY, 2-VISA,3-Mastro,4-Master',
  `id_pay_device` int DEFAULT NULL COMMENT 'From Payment device Master',
  `id_bank` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  PRIMARY KEY (`id_pay_mode_details`),
  UNIQUE KEY `id_payment` (`id_adv_payment`,`payment_mode`,`payment_ref_number`,`payment_status`,`id_pay_device`,`id_bank`) USING BTREE,
  KEY `id_billing_payment` (`id_pay_mode_details`),
  KEY `bill_id` (`id_adv_payment`),
  KEY `payment_date` (`payment_date`),
  KEY `payment_status` (`payment_status`),
  KEY `id_payment_2` (`id_adv_payment`),
  KEY `NB_type` (`NB_type`),
  KEY `id_pay_device` (`id_pay_device`),
  KEY `id_bank` (`id_bank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `general_advance_payment`
--

DROP TABLE IF EXISTS `general_advance_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_advance_payment` (
  `id_adv_payment` int NOT NULL AUTO_INCREMENT,
  `id_scheme_account` int unsigned NOT NULL,
  `id_employee` int DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `id_payGateway` int DEFAULT NULL,
  `id_transaction` varchar(50) DEFAULT NULL,
  `payu_id` varchar(25) DEFAULT NULL COMMENT 'Razor Pay --> order id,Payu - >payu id',
  `date_payment` datetime NOT NULL,
  `custom_entry_date` datetime DEFAULT NULL,
  `installment` int unsigned DEFAULT NULL COMMENT 'installement no',
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(45) DEFAULT NULL,
  `act_amount` decimal(10,0) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `due_type` varchar(2) DEFAULT 'N',
  `metal_rate` decimal(10,2) DEFAULT '0.00',
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `remark` text,
  `receipt_no` int DEFAULT NULL COMMENT 'receipt_no',
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 -admin , 1- web app, 2 - mobile app, 3 - Admin App, 4 - Cashfree Subscription, 5 - Sync',
  `ref_trans_id` varchar(45) DEFAULT NULL,
  `gst` decimal(10,0) NOT NULL,
  `gst_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -amount inclusive of GST , 0 -amount exclusive of gst',
  `gst_amount` decimal(12,3) DEFAULT NULL COMMENT 'GST Calculated by LMX',
  `is_print_taken` tinyint NOT NULL DEFAULT '0' COMMENT '1 - taken , 0 -not taken',
  `cgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `igst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `sgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `is_gateway_verified` tinyint(1) DEFAULT '0' COMMENT '1 - status verified with gateway',
  `id_agent` int unsigned DEFAULT NULL COMMENT 'Referred agent',
  `receipt_year` int DEFAULT NULL COMMENT 'Receipt financial year code',
  `paidBy_mobile` int DEFAULT NULL,
  `paidBy_id_customer` int DEFAULT NULL,
  `form_secret` varchar(250) DEFAULT NULL,
  `pay_email` varchar(100) DEFAULT NULL,
  `no_of_dues` int NOT NULL DEFAULT '1',
  `discountAmt` int DEFAULT NULL,
  `add_charges` int DEFAULT NULL,
  `actual_trans_amt` int DEFAULT NULL,
  PRIMARY KEY (`id_adv_payment`),
  UNIQUE KEY `id_adv_payment` (`id_adv_payment`),
  UNIQUE KEY `id_transaction` (`id_transaction`),
  UNIQUE KEY `id_transaction_2` (`id_transaction`,`ref_trans_id`),
  KEY `FK_payment_scheme_account` (`id_scheme_account`),
  KEY `id_branch` (`id_branch`),
  KEY `date_payment` (`date_payment`),
  KEY `payment_mode` (`payment_mode`),
  KEY `payment_ref_number` (`payment_ref_number`),
  KEY `payment_status` (`payment_status`),
  KEY `receipt_no` (`receipt_no`),
  KEY `id_employee` (`id_employee`),
  KEY `id_payGateway` (`id_payGateway`),
  KEY `custom_entry_date` (`custom_entry_date`),
  KEY `installment` (`installment`),
  KEY `is_print_taken` (`is_print_taken`),
  KEY `id_agent` (`id_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gift_card`
--

DROP TABLE IF EXISTS `gift_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gift_card` (
  `id_gift_card` int NOT NULL AUTO_INCREMENT,
  `id_pay_gift_card` int NOT NULL,
  `id_branch` int DEFAULT NULL,
  `id_gift_voucher` int DEFAULT NULL,
  `id_set_gift_voucher` int DEFAULT NULL COMMENT '	From Purchase Voucher Settings	',
  `date_add` datetime DEFAULT NULL,
  `bill_id` int DEFAULT NULL,
  `free_card` tinyint(1) DEFAULT NULL COMMENT '1 - Free card from shop,2-Paid From Customer,3-Promotional,4- Scheme Joining',
  `gift_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Employee, 2 - Customer',
  `emp_created` int DEFAULT NULL COMMENT 'Gift voucher Created employee',
  `code` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `percentage` decimal(10,2) DEFAULT NULL,
  `credit_type` int DEFAULT NULL COMMENT '1 - amount, 2 - percentage',
  `weight` decimal(10,2) DEFAULT NULL,
  `rate_per_gram` decimal(10,2) DEFAULT NULL,
  `valid_from` datetime DEFAULT NULL,
  `purchased_by` int DEFAULT NULL COMMENT 'id of purchased user (id_employee or id_customer)',
  `valid_to` datetime DEFAULT NULL COMMENT 'expires on',
  `redeemed_by` int DEFAULT NULL,
  `redeemed_on` datetime DEFAULT NULL,
  `redeem_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- at store',
  `qr_code` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '4' COMMENT '0 - Open , 1 - Shared , 2 - Redeemed, 3 - Expired, 4- Pay realization pending,5-Cancelled',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - eGiftCard, 2 - Physical Voucher',
  `adjusted_bill_id` int DEFAULT NULL,
  `ref_no` int DEFAULT NULL COMMENT 'No of receipts generated',
  `id_scheme_account` int DEFAULT NULL COMMENT 'while free card type=4 sch acc id is mandatory',
  `img_url` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_gift_card`),
  UNIQUE KEY `code` (`code`),
  KEY `id_branch` (`id_branch`),
  KEY `emp_created` (`emp_created`),
  KEY `purchased_by` (`purchased_by`),
  KEY `redeemed_by` (`redeemed_by`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gift_card_payment`
--

DROP TABLE IF EXISTS `gift_card_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gift_card_payment` (
  `id_pay_gift_card` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `id_customer` int unsigned NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- eGiftCard',
  `id_payGateway` int DEFAULT NULL,
  `id_transaction` varchar(50) DEFAULT NULL,
  `payu_id` varchar(25) DEFAULT NULL,
  `date_purchase` datetime NOT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `add_charges` decimal(10,2) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `remark` text,
  `code` varchar(45) DEFAULT NULL COMMENT 'receipt_no',
  `date_add` datetime NOT NULL,
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 -admin , 1- web app, 2 - mobile app ',
  `free_gift_card` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'issued from shop',
  `ref_trans_id` varchar(45) DEFAULT NULL,
  `discountAmt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `actual_trans_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `approval_date` datetime DEFAULT NULL,
  `redeemed_amount` decimal(10,2) DEFAULT NULL,
  `id_gift_card` int DEFAULT NULL,
  PRIMARY KEY (`id_pay_gift_card`),
  UNIQUE KEY `id_transaction` (`id_transaction`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gift_card_trans`
--

DROP TABLE IF EXISTS `gift_card_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gift_card_trans` (
  `id_gcard_trans` int NOT NULL AUTO_INCREMENT,
  `id_gift_card` int NOT NULL,
  `trans_from` int NOT NULL COMMENT 'id_customer',
  `trans_to_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trans_to_mobile` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_name` text COLLATE utf8mb4_unicode_ci,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_gcard_trans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gift_issued`
--

DROP TABLE IF EXISTS `gift_issued`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gift_issued` (
  `id_gift_issued` int NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Gift, 2 - Lucky draw Prize',
  `status` int NOT NULL DEFAULT '0' COMMENT '0->Added 1 -> Issued, 2 -> Deducted',
  `id_gift` int DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `gift_desc` text,
  `quantity` int DEFAULT NULL,
  `id_scheme_account` int unsigned NOT NULL,
  `id_employee` int unsigned DEFAULT NULL COMMENT 'id_employee',
  `date_issued` datetime DEFAULT NULL,
  `verified_otp` varchar(6) DEFAULT NULL,
  `gift_amount` int DEFAULT '0' COMMENT 'Cost of gift given in \r\ninv_pur_itm_rate from ret_other_inventory_purchase_items',
  `item_ref_no` varchar(20) DEFAULT NULL COMMENT 'Gift reference no (qr code id) , item_ref_no from ret_other_inventory_purchase_items_details',
  `deducted_date` datetime(6) DEFAULT NULL,
  `deducted_by` int DEFAULT NULL,
  `deduct_remark` text,
  `paid_installments` int DEFAULT NULL COMMENT 'Paid ins count at the time of gift issue',
  `barcode` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id_gift_issued`),
  KEY `id_scheme_account` (`id_scheme_account`),
  KEY `issued_emp` (`id_employee`),
  KEY `item_ref_no` (`item_ref_no`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gift_mapping`
--

DROP TABLE IF EXISTS `gift_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gift_mapping` (
  `id_item_mapping` int NOT NULL AUTO_INCREMENT,
  `id_other_item` int NOT NULL COMMENT 'id_other_item from ret_other_inventory_item',
  `id_scheme` int NOT NULL COMMENT 'scheme id from scheme table',
  `item_issue_limit` int NOT NULL COMMENT 'no of count to give per account',
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_upd` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `from_ins` int NOT NULL,
  `to_ins` int NOT NULL,
  PRIMARY KEY (`id_item_mapping`),
  KEY `id_other_item` (`id_other_item`,`id_scheme`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gifts`
--

DROP TABLE IF EXISTS `gifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gifts` (
  `id_gift` int NOT NULL AUTO_INCREMENT,
  `gift_name` text COLLATE utf8mb4_unicode_ci,
  `id_branch` int DEFAULT NULL COMMENT 'branch',
  `gift_type` int DEFAULT NULL COMMENT '1 - Coins, 2- Bars, 3 - Jewel, 4 - Others',
  `metal` int DEFAULT NULL COMMENT 'Id comes from metal table',
  `net_weight` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `out_stock` int NOT NULL DEFAULT '0' COMMENT 'giftissued count',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_upd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gift`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gst_splitup_detail`
--

DROP TABLE IF EXISTS `gst_splitup_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gst_splitup_detail` (
  `id_gst_splitup` int unsigned NOT NULL AUTO_INCREMENT,
  `id_scheme` int unsigned NOT NULL DEFAULT '0',
  `splitup_name` varchar(45) NOT NULL DEFAULT '' COMMENT '	SGST , CGST , IGST',
  `percentage` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '	percentage allocated',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '	0- inactive , 1- active',
  `type` tinyint unsigned DEFAULT NULL COMMENT 'NULL- GST , 0 - same state GST , 1- other state GST',
  `effective_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date add',
  `date_upd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gst_splitup`),
  KEY `id_scheme` (`id_scheme`)
) ENGINE=InnoDB AUTO_INCREMENT=1985 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `import_log`
--

DROP TABLE IF EXISTS `import_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `import_log` (
  `id_import` int NOT NULL AUTO_INCREMENT,
  `id_employee` int DEFAULT '0',
  `total` int DEFAULT '0',
  `imported` int DEFAULT '0',
  `failed` int DEFAULT '0',
  `firstrecord` int DEFAULT '0',
  `lastrecord` int DEFAULT '0',
  `scheme_type` tinyint(1) DEFAULT '0',
  `send_sms` tinyint(1) DEFAULT '0',
  `import_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_import`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `indianpostal`
--

DROP TABLE IF EXISTS `indianpostal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `indianpostal` (
  `idpincode` int NOT NULL AUTO_INCREMENT,
  `officeName` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` double DEFAULT NULL,
  `taluk` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `districtName` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stateName` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_state` int DEFAULT NULL,
  `id_city` int DEFAULT NULL,
  PRIMARY KEY (`idpincode`)
) ENGINE=InnoDB AUTO_INCREMENT=154824 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `installment_amount`
--

DROP TABLE IF EXISTS `installment_amount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `installment_amount` (
  `id_ins_amount` int unsigned NOT NULL AUTO_INCREMENT,
  `ins_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_ins_amount`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inter_sync_wallet`
--

DROP TABLE IF EXISTS `inter_sync_wallet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inter_sync_wallet` (
  `id_inter_sync_wallet` int NOT NULL AUTO_INCREMENT,
  `mobile` varchar(45) DEFAULT NULL,
  `points` decimal(10,2) DEFAULT NULL,
  `branch_1` tinyint(1) DEFAULT NULL COMMENT '0 - unsynced , 1- synced',
  `branch_2` tinyint(1) DEFAULT NULL COMMENT '0 - unsynced , 1- synced',
  `branch_3` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - unsynced , 1- synced',
  `branch_4` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - unsynced , 1- synced',
  `branch_5` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - unsynced , 1- synced',
  `branch_6` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - unsynced , 1- synced',
  `branch_7` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - unsynced , 1- synced',
  `branch_8` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - unsynced , 1- synced',
  `date_add` datetime DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id_inter_sync_wallet`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inter_wallet_account`
--

DROP TABLE IF EXISTS `inter_wallet_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inter_wallet_account` (
  `id_inter_wal_ac` int NOT NULL AUTO_INCREMENT,
  `mobile` varchar(45) NOT NULL,
  `available_points` decimal(10,2) NOT NULL,
  `id_customer` int DEFAULT NULL COMMENT 'Customer id will be updated if registered in CRM',
  `date_add` datetime NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `id_wallet_ac` int DEFAULT NULL,
  PRIMARY KEY (`id_inter_wal_ac`),
  KEY `mobile` (`mobile`),
  KEY `mobile_2` (`mobile`),
  KEY `mobile_3` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inter_wallet_smssettings`
--

DROP TABLE IF EXISTS `inter_wallet_smssettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inter_wallet_smssettings` (
  `id_wal_smsSettings` int NOT NULL AUTO_INCREMENT,
  `sent_sms` int NOT NULL DEFAULT '0',
  `credited_sms` int NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_wal_smsSettings`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inter_wallet_trans`
--

DROP TABLE IF EXISTS `inter_wallet_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inter_wallet_trans` (
  `id_inter_wallet_trans` int NOT NULL AUTO_INCREMENT,
  `record_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Offline , 2- Online',
  `mobile` varchar(25) NOT NULL,
  `id_branch` int DEFAULT NULL,
  `trans_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - credit , 2 - debit',
  `bill_no` varchar(45) DEFAULT NULL,
  `entry_date` datetime NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `record_to` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Offline , 2 - Online',
  `is_modified` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1- yes',
  `is_transferred` varchar(2) NOT NULL DEFAULT 'Y' COMMENT 'Y if trans is read',
  `transfer_date` datetime DEFAULT NULL COMMENT 'date transferred',
  `use_points` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  `redeem_req_pts` decimal(10,2) DEFAULT NULL COMMENT 'redeem points requested from  sales',
  `actual_redeemed` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bill_availWalPt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Available wallet point at the time of bill entry',
  PRIMARY KEY (`id_inter_wallet_trans`),
  KEY `mobile` (`mobile`,`id_branch`,`bill_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inter_wallet_trans_detail`
--

DROP TABLE IF EXISTS `inter_wallet_trans_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inter_wallet_trans_detail` (
  `id_inter_waltransdetail` int NOT NULL AUTO_INCREMENT,
  `id_inter_wallet_trans` int NOT NULL,
  `category_code` varchar(45) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `trans_points` decimal(10,2) NOT NULL,
  `id_wcat_settings` int DEFAULT NULL,
  `allowed_redeem` decimal(10,2) DEFAULT NULL COMMENT 'redeem allowed from admin settings',
  `remark` text NOT NULL,
  `date_add` datetime DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_inter_waltransdetail`),
  UNIQUE KEY `UK_bill_catcode` (`id_inter_wallet_trans`,`category_code`),
  KEY `fk_wcat_settings_id` (`id_wcat_settings`),
  KEY `fk-walTransId` (`id_inter_wallet_trans`),
  KEY `id_inter_wallet_trans` (`id_inter_wallet_trans`,`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jil_customer_reg`
--

DROP TABLE IF EXISTS `jil_customer_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jil_customer_reg` (
  `id_customer_reg` int NOT NULL AUTO_INCREMENT,
  `reg_date` date DEFAULT NULL COMMENT 'entry date',
  `salutation` varchar(5) DEFAULT NULL COMMENT 'title of customer',
  `initials` varchar(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `doorno` varchar(25) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `pincode` varchar(7) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL COMMENT 'without std code',
  `mobile` varchar(10) DEFAULT NULL COMMENT 'without isd code',
  `email` varchar(50) DEFAULT NULL,
  `dt_of_birth` date DEFAULT NULL,
  `wed_date` date DEFAULT NULL,
  `ref_no` varchar(10) DEFAULT NULL COMMENT 'logimax scheme acc no',
  `transfer_jil` varchar(2) DEFAULT 'N',
  `transfer_date` date DEFAULT NULL,
  `new_customer` varchar(1) DEFAULT NULL COMMENT 'Y - new and N - existing customer .(For ''Y'' Once a/c no created it wil be ''N'' for future records)',
  `id_scheme_account` int unsigned DEFAULT NULL,
  `id_branch` int DEFAULT NULL COMMENT 'Chit a/c Branch id',
  `pan_no` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_customer_reg`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jil_new_customer`
--

DROP TABLE IF EXISTS `jil_new_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jil_new_customer` (
  `id_new_customer` int NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(10) DEFAULT NULL COMMENT 'lmx scheme account no',
  `mobile` varchar(10) DEFAULT NULL,
  `clientid` varchar(16) DEFAULT NULL COMMENT '16 code generated by jil',
  `group_code` varchar(3) DEFAULT NULL COMMENT 'generated by jil',
  `msno` int DEFAULT NULL COMMENT 'generated by jil',
  `new_customer` varchar(1) DEFAULT NULL COMMENT 'Y - New customer, N - existing customer',
  `receipt_jil` bigint DEFAULT NULL COMMENT 'jilaba receipt no',
  `transfer` varchar(1) DEFAULT 'N',
  `transfer_date` date DEFAULT NULL COMMENT 'updated by jil',
  `id_branch` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_new_customer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jil_offline_payments`
--

DROP TABLE IF EXISTS `jil_offline_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jil_offline_payments` (
  `id_offline_payment` int unsigned NOT NULL AUTO_INCREMENT,
  `id_metal` int unsigned NOT NULL DEFAULT '1',
  `metal_rate` decimal(10,2) DEFAULT NULL,
  `payment_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `payment_mode` varchar(80) DEFAULT NULL,
  `receipt_jil` bigint unsigned DEFAULT NULL,
  `remark` text,
  `discountAmt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transfer_date` date NOT NULL DEFAULT '0000-00-00',
  `mobile` varchar(30) DEFAULT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `ac_name` varchar(120) DEFAULT NULL,
  `scheme_ac_number` varchar(20) NOT NULL DEFAULT '' COMMENT 'msn no',
  `clientid` varchar(16) NOT NULL DEFAULT '',
  `date_payment` date NOT NULL DEFAULT '0000-00-00',
  `is_trans_completed` varchar(1) NOT NULL DEFAULT 'N' COMMENT 'Y - If record updated in payment table',
  `status` varchar(45) NOT NULL DEFAULT '',
  `brefno` varchar(45) NOT NULL DEFAULT '',
  `instalment` int NOT NULL,
  PRIMARY KEY (`id_offline_payment`),
  KEY `is_trans_completed` (`is_trans_completed`),
  KEY `status` (`status`),
  KEY `brefno` (`brefno`),
  KEY `receipt_jil` (`receipt_jil`),
  KEY `mobile` (`mobile`),
  KEY `id_branch` (`id_branch`),
  KEY `clientid` (`clientid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jil_transaction`
--

DROP TABLE IF EXISTS `jil_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jil_transaction` (
  `id_transaction` int NOT NULL AUTO_INCREMENT,
  `trans_date` date DEFAULT NULL COMMENT 'entry date',
  `custom_entry_date` date DEFAULT NULL COMMENT 'custom entry date (for shop ledger purpose)',
  `client_id` varchar(16) DEFAULT NULL COMMENT 'generation by Jilaba',
  `group_code` varchar(3) DEFAULT NULL COMMENT 'generation by Jilaba',
  `msno` int DEFAULT NULL COMMENT 'generation by Jilaba',
  `scheme_code` varchar(3) DEFAULT NULL COMMENT 'scheme code',
  `amount` decimal(12,2) DEFAULT NULL COMMENT 'amount received from customer',
  `weight` decimal(12,3) DEFAULT NULL COMMENT 'weight occured for the amount received',
  `rate` decimal(12,2) DEFAULT NULL COMMENT 'metal rate',
  `metal` varchar(1) DEFAULT NULL COMMENT 'metal code',
  `payment_mode` varchar(45) DEFAULT NULL COMMENT 'payment mode',
  `bank_name` varchar(45) DEFAULT NULL,
  `branch_name` varchar(45) DEFAULT NULL,
  `card_no` varchar(25) DEFAULT NULL COMMENT 'credit card number',
  `approval_no` varchar(25) DEFAULT NULL COMMENT 'credit card approval number',
  `ref_no` bigint unsigned DEFAULT NULL COMMENT 'logimax payment reference no',
  `transfer_jil` varchar(1) DEFAULT 'N' COMMENT 'N by lmx and Y by jil',
  `transfer_date` date DEFAULT NULL COMMENT 'update by jil',
  `new_customer` varchar(1) DEFAULT NULL COMMENT 'Y - new and N - existing customer .(For ''Y'' Once a/c no created it wil be ''N'' for future records)',
  `name` varchar(100) DEFAULT NULL,
  `mobile` varchar(10) DEFAULT NULL,
  `id_scheme_account` int DEFAULT NULL,
  `discountAmt` decimal(10,2) DEFAULT '0.00' COMMENT 'discount amount',
  `id_branch` int DEFAULT NULL COMMENT 'Chit  a/c branh',
  PRIMARY KEY (`id_transaction`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `joborder`
--

DROP TABLE IF EXISTS `joborder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `joborder` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_order` int unsigned NOT NULL,
  `id_vendor` int unsigned NOT NULL,
  `assigndate` datetime NOT NULL,
  `deliverydate` date DEFAULT NULL,
  `id_category` int unsigned DEFAULT NULL,
  `id_product` int unsigned DEFAULT NULL,
  `itemname` varchar(120) DEFAULT NULL,
  `orderstatus` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0->oreder assigned 1-> accepted 2-> rejected 3->progress 4->completed 5->Order closed by some one 6->Order Closed With Finish',
  `deliveredon` datetime DEFAULT NULL,
  `rejectreason` varchar(300) DEFAULT NULL,
  `is_partial_delivery` int NOT NULL DEFAULT '0' COMMENT '1 - Yes , 2 - No',
  `delivered_qty` int NOT NULL DEFAULT '0',
  `is_rework` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1->its already completed and allocate rework process',
  `is_reject` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 ->No Rejected 1->admin reject 2->karigar accept before reject 3-> karigar working process to reject 4->customer not taken this order',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=978 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `joborder_partial_delivery`
--

DROP TABLE IF EXISTS `joborder_partial_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `joborder_partial_delivery` (
  `id_jopartial_delivery` int unsigned NOT NULL AUTO_INCREMENT,
  `id_joborder` int NOT NULL,
  `delivered_on` int DEFAULT NULL COMMENT '1 - Partial Delivery Ready , 2 - Delivered to Customer',
  `delivered_qty` int unsigned DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '1 - Delivery Ready , 2 - Delivered',
  `delivered_date` datetime DEFAULT NULL,
  UNIQUE KEY `id_jopartial_delivery` (`id_jopartial_delivery`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Partial Delivery keeping unit Master';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kyc`
--

DROP TABLE IF EXISTS `kyc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kyc` (
  `id_kyc` int NOT NULL AUTO_INCREMENT,
  `id_customer` int unsigned NOT NULL COMMENT 'firstname',
  `id_agent` int unsigned DEFAULT NULL,
  `kyc_type` int DEFAULT NULL COMMENT '1- Bank Account 2- PAN Card 3- Aadhaar',
  `proof_name` int DEFAULT NULL,
  `number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'PAN No./Aadhaar No./Bank Ac No.',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'PAN Holder Name / Bank Ac holder name',
  `bank_ifsc` text COLLATE utf8mb4_unicode_ci,
  `bank_branch` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '	0 - Pending , 1 - In Progress , 2 - Verified, 3 -Rejected',
  `emp_verified_by` int DEFAULT NULL COMMENT 'Employee ID',
  `verification_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Manual , 2- Auto',
  `last_update` datetime DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci COMMENT 'Address from driving licence',
  `img_url` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cheque_img` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `back_img_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cheque_doc_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_by` tinyint NOT NULL DEFAULT '1' COMMENT ' 0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5- Sync, 6-Import',
  `type` int NOT NULL DEFAULT '1' COMMENT '1 --> Customer , 2--> Nominee',
  `nominee_mobile` int DEFAULT NULL,
  `verified_key` varchar(100) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS ((case when (`status` = 2) then concat(`id_customer`,_utf8mb4'-',`kyc_type`,_utf8mb4'-',`type`) else NULL end)) VIRTUAL,
  PRIMARY KEY (`id_kyc`),
  UNIQUE KEY `verified_key` (`verified_key`),
  KEY `id_customer_2` (`id_customer`),
  KEY `id_agent` (`id_agent`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ledger_mapping`
--

DROP TABLE IF EXISTS `ledger_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ledger_mapping` (
  `id_mapping` int NOT NULL AUTO_INCREMENT,
  `id_ledger` int NOT NULL,
  `type` enum('BANK','PAYMODE') NOT NULL,
  `reference_id` int NOT NULL,
  `opening_balance` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`id_mapping`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ledger_master`
--

DROP TABLE IF EXISTS `ledger_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ledger_master` (
  `id_ledger` int NOT NULL AUTO_INCREMENT,
  `ledger_name` varchar(255) NOT NULL,
  `opening_balance` decimal(15,2) DEFAULT '0.00',
  `opening_date` date DEFAULT NULL,
  `min_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` int DEFAULT '1',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ledger`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `limit_settings`
--

DROP TABLE IF EXISTS `limit_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `limit_settings` (
  `id_limit` int unsigned NOT NULL AUTO_INCREMENT,
  `limit_cust` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-No,1-yes',
  `cust_max_count` int unsigned NOT NULL,
  `limit_sch` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-No,1-yes',
  `sch_max_count` int unsigned NOT NULL,
  `limit_branch` tinyint unsigned NOT NULL DEFAULT '0',
  `branch_max_count` int unsigned NOT NULL,
  `limit_sch_acc` tinyint unsigned NOT NULL DEFAULT '0',
  `sch_acc_max_count` int unsigned NOT NULL,
  PRIMARY KEY (`id_limit`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log` (
  `id_log` int unsigned NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL COMMENT 'Logged in branch	',
  `id_employee` int unsigned DEFAULT NULL,
  `login_on` datetime DEFAULT NULL,
  `logout_on` datetime DEFAULT NULL,
  `id_otp` int DEFAULT NULL,
  `ip_address` text,
  `device_type` varchar(50) DEFAULT NULL,
  `device_id` int DEFAULT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB AUTO_INCREMENT=3925 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_detail`
--

DROP TABLE IF EXISTS `log_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_detail` (
  `id_log_detail` int unsigned NOT NULL AUTO_INCREMENT,
  `id_log` int unsigned DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `module` varchar(45) DEFAULT NULL,
  `operation` varchar(20) DEFAULT NULL,
  `record` varchar(45) DEFAULT NULL,
  `remark` text,
  `event_through` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Web admin, 2 - CRM mobile admin, 3 - Retail mobile admin',
  `form_data` longtext COMMENT 'In this we store a whole form content in json format',
  PRIMARY KEY (`id_log_detail`)
) ENGINE=InnoDB AUTO_INCREMENT=10535 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_inactive_hour_attempt`
--

DROP TABLE IF EXISTS `log_inactive_hour_attempt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_inactive_hour_attempt` (
  `id_log_attempt` int NOT NULL AUTO_INCREMENT,
  `id_branch` int unsigned DEFAULT NULL,
  `id_employee` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `ip_address` text,
  `device_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_log_attempt`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_payment`
--

DROP TABLE IF EXISTS `log_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_payment` (
  `id_lg_payment` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `operation` tinyint(1) DEFAULT NULL,
  `record` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_from` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `remark` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `error` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_lg_payment`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_cus_invited`
--

DROP TABLE IF EXISTS `ly_cus_invited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_cus_invited` (
  `id_cus_invited` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `mobile` varchar(45) NOT NULL,
  `invited_by` int NOT NULL COMMENT 'id_customer',
  `invited_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `invited_through` tinyint(1) DEFAULT NULL COMMENT '1 - SMS, 2 - Email',
  `bill_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No billing, 1 - Billing Generated',
  PRIMARY KEY (`id_cus_invited`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_cus_loyalty_trans_details`
--

DROP TABLE IF EXISTS `ly_cus_loyalty_trans_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_cus_loyalty_trans_details` (
  `id_cus_loyalty_trans_details` int NOT NULL AUTO_INCREMENT,
  `id_cus_loyalty_trans` int DEFAULT NULL,
  `cat_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `design_id` int DEFAULT NULL,
  `bill_item_amt` decimal(10,2) DEFAULT NULL,
  `earned_ponts` decimal(10,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `pts_status` int DEFAULT NULL COMMENT '0-> expired 1->active 2-> utlized',
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `utlized_on` datetime DEFAULT NULL,
  `utlized_pts` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id_cus_loyalty_trans_details`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_cus_referal_code`
--

DROP TABLE IF EXISTS `ly_cus_referal_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_cus_referal_code` (
  `id_cus_referal_code` int NOT NULL AUTO_INCREMENT,
  `id_customer` int NOT NULL,
  `code` varchar(11) NOT NULL,
  `expire_on` datetime DEFAULT NULL,
  `added_by` tinyint DEFAULT NULL COMMENT '1 - Admin, 2 - Mobile app',
  `created_on` datetime DEFAULT NULL,
  `created_emp` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cus_referal_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_customer_loyalty_transaction`
--

DROP TABLE IF EXISTS `ly_customer_loyalty_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_customer_loyalty_transaction` (
  `id_cus_loyal_tran` int NOT NULL AUTO_INCREMENT,
  `ly_trans_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Billing, 2 - Welcome bonus, 3 - Purchase plan referral, 4 - Purchase plan collection',
  `ly_issue_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 --> Credit  0--> Debit',
  `cus_loyal_cus_id` int DEFAULT NULL,
  `id_agent` int unsigned DEFAULT NULL,
  `id_scheme_account` int unsigned DEFAULT NULL,
  `id_payment` int DEFAULT NULL,
  `bill_id` int DEFAULT NULL,
  `redeem_point` decimal(10,2) DEFAULT NULL,
  `reward_pts` decimal(10,2) DEFAULT NULL,
  `cash_point` decimal(10,2) DEFAULT NULL,
  `unsettled_cash_pts` decimal(10,2) DEFAULT NULL COMMENT 'Pending cash points to be settled',
  `expiry_on` date DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT ' 0-> Expired, 1-> Active, 2-> Settled, 3 -> Partially Settled',
  `id_influencer_settlement` int DEFAULT NULL COMMENT 'Update Settlement ID after settlement',
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `tr_cus_type` tinyint(1) NOT NULL COMMENT '1 - Customer, 2 - Influencer, 3 - Referrer, 4 - Agent',
  `cr_based_on` tinyint(1) DEFAULT NULL COMMENT 'Credit based on [1 - General settings, 2 - Category settings, 3 - Purchase plan settings',
  `credit_for` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_cus_loyal_tran`),
  KEY `cus_loyal_cus_id` (`cus_loyal_cus_id`),
  KEY `id_payment` (`id_payment`),
  KEY `id_scheme_account` (`id_scheme_account`),
  KEY `id_agent` (`id_agent`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_influencer_earned_pts`
--

DROP TABLE IF EXISTS `ly_influencer_earned_pts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_influencer_earned_pts` (
  `id_influencer_earned_pts` int NOT NULL,
  `id_customer_trans` int DEFAULT NULL,
  `earned_pts` decimal(10,2) DEFAULT NULL,
  `earned_pts_type` int DEFAULT NULL COMMENT '1-> Reward pts 2-> Cash pts',
  `has_pts_expiry` int DEFAULT NULL COMMENT '0-> No 1-> Yes',
  `expiry_date` date DEFAULT NULL,
  `pts_status` int DEFAULT NULL COMMENT '1-> Active 0-> Expired',
  `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_influencer_earned_pts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_influencer_request`
--

DROP TABLE IF EXISTS `ly_influencer_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_influencer_request` (
  `id_influencer_req` int NOT NULL AUTO_INCREMENT,
  `cus_id` int unsigned NOT NULL,
  `requested_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `request_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - influencer, 2 - referrer',
  `request_status` tinyint NOT NULL DEFAULT '0' COMMENT '0 - Open, 1 - Approved, 2 - Rejected',
  `reject_reason` text,
  `id_employee` int DEFAULT NULL COMMENT 'Employee Processed the request',
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_influencer_req`),
  UNIQUE KEY `cus_id_2` (`cus_id`),
  KEY `cus_id` (`cus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_influencer_reward_categories`
--

DROP TABLE IF EXISTS `ly_influencer_reward_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_influencer_reward_categories` (
  `id_influencer_reward_categories` int NOT NULL AUTO_INCREMENT,
  `id_influ_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `category_earning_rule_value` decimal(10,2) DEFAULT NULL,
  `category_earning_rule_point` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_influencer_reward_categories`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_influencer_settings`
--

DROP TABLE IF EXISTS `ly_influencer_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_influencer_settings` (
  `id_influencer_settings` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the template',
  `type` int DEFAULT NULL COMMENT '1->influencer / 2->referral ',
  `point_based_category` int DEFAULT NULL COMMENT '1->yes / 0->no ',
  `point_type` int DEFAULT NULL COMMENT '1->point 2->cash',
  `accumulate_type` int DEFAULT NULL COMMENT '1->% 2-> value',
  `earning_rule_value` decimal(10,2) DEFAULT NULL,
  `earning_rule_point` decimal(10,2) DEFAULT NULL,
  `expiration` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `expire_after` int DEFAULT NULL,
  `expire_type` tinyint(1) DEFAULT NULL COMMENT ' 1-> Days 2-> month 3->year',
  PRIMARY KEY (`id_influencer_settings`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_influencer_settlement`
--

DROP TABLE IF EXISTS `ly_influencer_settlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_influencer_settlement` (
  `id_influencer_settlement` int NOT NULL AUTO_INCREMENT,
  `id_customer` int DEFAULT NULL,
  `id_agent` int unsigned DEFAULT NULL,
  `settlement_date` datetime DEFAULT NULL,
  `settlement_pts` decimal(10,2) DEFAULT NULL,
  `pts_type` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `settlement_branch` int DEFAULT NULL,
  `settlement_created_by` int DEFAULT NULL,
  `settlement_approved_by` int DEFAULT NULL,
  `utr_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_acc_no` int DEFAULT NULL,
  `ifsc_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acc_holder_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_influencer_settlement`),
  KEY `id_agent` (`id_agent`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_loyalty_settings`
--

DROP TABLE IF EXISTS `ly_loyalty_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_loyalty_settings` (
  `id_loyalty_settings` int NOT NULL AUTO_INCREMENT,
  `point_based_on_category` int DEFAULT NULL COMMENT '1-> Yes 0-> No',
  `earning_rule_value` decimal(10,2) DEFAULT NULL,
  `earning_rule_point` decimal(10,2) DEFAULT NULL,
  `earning_point_rounding` int DEFAULT NULL COMMENT '1->up,2-> down, 3->nearest',
  `minimum_order_value` decimal(10,2) DEFAULT NULL,
  `spending_rule_point` decimal(10,2) DEFAULT NULL,
  `spending_rule_equalent_value` decimal(10,2) DEFAULT NULL,
  `minimum_point_required_to_redemption` decimal(10,2) DEFAULT NULL,
  `minimum_redeemable_point` decimal(10,2) DEFAULT NULL,
  `minimum_redeemable_amount` decimal(10,2) DEFAULT NULL,
  `per_day_redeem_count` int DEFAULT NULL COMMENT '1->limit / 2->unlimit',
  `per_day_redeem_count_limit` int DEFAULT NULL COMMENT 'if limit can enter the value',
  `max_point_can_earned` int DEFAULT NULL COMMENT '1->limit / 2->unlimit',
  `earn_point_from_taxes` int DEFAULT NULL COMMENT '1 - inclusive and/or 2->exclusive tax amounts',
  `point_can_add_after_order` int DEFAULT NULL COMMENT '1-> After order 2-> on same order itself',
  `points_deducted_on_refund` int DEFAULT NULL COMMENT 'loyalty points are deducted after a refund has been given 1-> Yes 0-> No',
  `expiration` int DEFAULT NULL COMMENT '1-> Yes 0-> No',
  `expire_after` int DEFAULT NULL,
  `expire_type` int DEFAULT NULL COMMENT '1-> Days 2-> month 3->year',
  `expire_after_inactive` int DEFAULT NULL COMMENT '1-> Yes 0-> No',
  `inactive_expire_after` int DEFAULT NULL,
  `inactive_expire_type` int DEFAULT NULL COMMENT '1-> Days 2-> month 3->year',
  `welcome_ponus` int DEFAULT NULL COMMENT '1-> Yes 0-> No',
  `ponus_point` decimal(10,2) DEFAULT NULL,
  `ponus_point_type` int DEFAULT NULL COMMENT '1-> point 2->cash',
  `point_expire_warning` int DEFAULT NULL COMMENT '1-> Yes 0->No',
  `point_expire_warning_notify_period` int DEFAULT NULL COMMENT 'No.of.days before notify',
  `reward_collection_tire_available` int DEFAULT NULL COMMENT '1-> Yes 0-> No',
  `reward_collection_tire_based_on` int DEFAULT NULL COMMENT '1-> points 2->value',
  `reward_collection_tire_validity` int DEFAULT NULL COMMENT '1-> Yes 0-> No',
  `reward_collection_tire_validity_period` int DEFAULT NULL COMMENT 'if reward_collection_tire_validity yes',
  `reward_collection_tire_validity_period_type` int DEFAULT NULL COMMENT '1-> Days 2-> Month 3->Year',
  `reward_points_active` int DEFAULT NULL COMMENT '1-> Active 0->inactive',
  `redeem_points_active` int DEFAULT NULL COMMENT '1-> Active 0->inactive',
  `terms` text COLLATE utf8mb4_unicode_ci,
  `terms_link` varchar(145) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `influencer_settlement_date_type` int DEFAULT NULL COMMENT '1-> Month end 2-> Month Begining 3-> Month date',
  `influencer_settlement_date` int DEFAULT NULL COMMENT 'influencer_settlement_date_type if select as date in month',
  `influ_minimum_amt_required_to_settle` decimal(10,2) DEFAULT NULL,
  `influ_settle_amt_max_percent` decimal(10,2) DEFAULT NULL,
  `credit_pt_for_disc_bills` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Credit loyalty point for bills with discount also',
  `allowed_bill_rdm_type` tinyint NOT NULL DEFAULT '1' COMMENT '1 - %, 2 - Point',
  `allowed_bill_rdm_value` tinyint NOT NULL DEFAULT '0' COMMENT 'Max allowed rdm from bill value',
  `ref_code_exp_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-> Days 2-> month 3->year',
  `ref_code_exp_value` int DEFAULT NULL COMMENT 'Referal code will be expired based on this value',
  `welcome_bonus_exp_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-> Days 2-> month 3->year',
  `welcome_bonus_exp_value` int DEFAULT NULL COMMENT 'bonus will be expired based on this value',
  `tier_degrade` tinyint(1) DEFAULT '0' COMMENT '1 - degrade tier if no transaction',
  `tier_degrade_type` int DEFAULT NULL COMMENT '1-> Days 2-> month 3->year',
  `tier_degrade_value` int DEFAULT NULL,
  PRIMARY KEY (`id_loyalty_settings`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_reward_categories`
--

DROP TABLE IF EXISTS `ly_reward_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_reward_categories` (
  `id_ly_reward_categories` int NOT NULL AUTO_INCREMENT,
  `id_loy_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `category_earning_rule_value` decimal(10,2) DEFAULT NULL,
  `category_earning_rule_point` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_ly_reward_categories`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_reward_collection_tires`
--

DROP TABLE IF EXISTS `ly_reward_collection_tires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_reward_collection_tires` (
  `id_reward_collection_tires` int NOT NULL AUTO_INCREMENT,
  `tire_title` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_minimum_required` decimal(10,2) DEFAULT NULL,
  `tire_description` varchar(245) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_earning_rule_value` decimal(10,2) DEFAULT NULL,
  `tire_earning_rule_point` decimal(10,2) DEFAULT NULL,
  `tire_terms` text COLLATE utf8mb4_unicode_ci,
  `tire_terms_link` varchar(145) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `point_based_category` int DEFAULT '0' COMMENT '1->yes / 0->no',
  PRIMARY KEY (`id_reward_collection_tires`),
  UNIQUE KEY `tire_minimum_required` (`tire_minimum_required`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_reward_collection_tires_categories`
--

DROP TABLE IF EXISTS `ly_reward_collection_tires_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_reward_collection_tires_categories` (
  `id_reward_collection_tires_cat` int NOT NULL AUTO_INCREMENT,
  `id_tire_id` int DEFAULT NULL,
  `id_tire_category` int DEFAULT NULL,
  `category_earning_rule_value` decimal(10,2) DEFAULT NULL,
  `category_earning_rule_point` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_reward_collection_tires_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_settlement_req_log`
--

DROP TABLE IF EXISTS `ly_settlement_req_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_settlement_req_log` (
  `id_settlmt_request_log` int unsigned NOT NULL AUTO_INCREMENT,
  `id_employee` int unsigned DEFAULT NULL,
  `id_settlement_req` int DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `remark` text,
  PRIMARY KEY (`id_settlmt_request_log`),
  KEY `id_employee` (`id_employee`),
  KEY `id_settlement_req` (`id_settlement_req`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ly_settlement_request`
--

DROP TABLE IF EXISTS `ly_settlement_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_settlement_request` (
  `id_settlement_req` int NOT NULL AUTO_INCREMENT,
  `id_agent` int unsigned NOT NULL,
  `requested_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `requested_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `request_status` tinyint NOT NULL DEFAULT '0' COMMENT '0 - Open, 1 - Approved, 2 - Rejected, 3 - Settled, 4 - Partially settled',
  `id_settlement` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_settlement_req`),
  UNIQUE KEY `cus_id_2` (`id_agent`),
  KEY `cus_id` (`id_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id_menu` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL DEFAULT '',
  `link` varchar(100) NOT NULL DEFAULT '#',
  `parent` int NOT NULL DEFAULT '0',
  `sort` int DEFAULT NULL,
  `icon` varchar(45) DEFAULT NULL,
  `active` int DEFAULT '1',
  PRIMARY KEY (`id_menu`),
  KEY `id_menu` (`id_menu`),
  KEY `active` (`active`),
  KEY `parent` (`parent`),
  KEY `id_menu_2` (`id_menu`),
  KEY `active_2` (`active`),
  KEY `parent_2` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=528 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `metal`
--

DROP TABLE IF EXISTS `metal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metal` (
  `id_metal` int unsigned NOT NULL AUTO_INCREMENT,
  `metal` varchar(45) NOT NULL,
  `metal_code` varchar(45) DEFAULT NULL,
  `tgrp_id` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `metal_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active, 0 - Inactive',
  PRIMARY KEY (`id_metal`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `metal_rates`
--

DROP TABLE IF EXISTS `metal_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metal_rates` (
  `id_metalrates` int unsigned NOT NULL AUTO_INCREMENT,
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `add_date` datetime DEFAULT NULL COMMENT 'Time of Rate Added',
  `goldrate_22ct` int DEFAULT NULL,
  `mjdmagoldrate_22ct` int DEFAULT NULL,
  `goldrate_24ct` decimal(12,2) DEFAULT '0.00',
  `silverrate_1gm` decimal(12,2) DEFAULT '0.00',
  `silverrate_1kg` decimal(12,2) DEFAULT '0.00',
  `market_gold_20ct` decimal(12,2) NOT NULL DEFAULT '0.00',
  `market_gold_995` decimal(12,2) NOT NULL DEFAULT '0.00',
  `mjdmasilverrate_999` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_employee` int DEFAULT NULL,
  `platinum_1g` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goldrate_9ct` decimal(10,2) DEFAULT NULL,
  `goldrate_18ct` decimal(10,2) NOT NULL DEFAULT '0.00',
  `mjdmasilverrate_1gm` decimal(10,2) NOT NULL DEFAULT '0.00',
  `market_gold_18ct` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '18ct discount rate',
  `g_ref_no` varchar(250) DEFAULT NULL COMMENT 'unique reference no. from offline for gold',
  `s_ref_no` varchar(250) DEFAULT NULL COMMENT 'unique reference no. from offline for silver',
  `p_ref_no` varchar(250) DEFAULT NULL COMMENT 'unique reference no. from offline for platinum',
  `coin_gold_22ct` decimal(10,2) DEFAULT NULL COMMENT 'CRM Coin Sale',
  `coin_silver_1g` decimal(10,2) DEFAULT NULL COMMENT 'CRM Coin Sale',
  `goldrate_14ct` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_metalrates`) USING BTREE,
  UNIQUE KEY `ref_no` (`g_ref_no`),
  KEY `id_metalrates` (`id_metalrates`),
  KEY `id_employee` (`id_employee`),
  KEY `id_metalrates_2` (`id_metalrates`),
  KEY `id_employee_2` (`id_employee`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COMMENT='updating metal rates';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id_module` int NOT NULL AUTO_INCREMENT,
  `id_parent` int DEFAULT NULL,
  `m_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'This code will be used to check settings, DONOT change it.',
  `m_app` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Show in app',
  `m_web` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Show in web',
  `m_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Module active',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_module`),
  UNIQUE KEY `m_code` (`m_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mrptagitems`
--

DROP TABLE IF EXISTS `mrptagitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mrptagitems` (
  `MRP Tags` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `msg91_delivery_status`
--

DROP TABLE IF EXISTS `msg91_delivery_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `msg91_delivery_status` (
  `id_msg91_status` int NOT NULL AUTO_INCREMENT,
  `request_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '	Request ID (a unique 24 character alphanumeric value for identification of a particular SMS)',
  `user_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL COMMENT 'Displays the delivery time and date of SMS YY: year MM: month DD: date hh: hours mm: minutes',
  `receiver` text COLLATE utf8mb4_unicode_ci COMMENT 'Receiver’s Contact Number',
  `status` int DEFAULT NULL COMMENT 'Status of SMS delivery 1=delivered 2=failed 16=rejected',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Delivered/Failed/Rejected',
  `date_add` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_msg91_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `new_arrivals`
--

DROP TABLE IF EXISTS `new_arrivals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `new_arrivals` (
  `id_new_arrivals` int unsigned NOT NULL AUTO_INCREMENT,
  `id_branch` int unsigned DEFAULT NULL,
  `name` text NOT NULL,
  `new_arrivals_content` text NOT NULL,
  `date_add` datetime NOT NULL,
  `date_update` datetime DEFAULT NULL,
  `active` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-inactive , 1- active',
  `new_arrivals_img_path` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `product_code` text NOT NULL,
  `product_weight` decimal(12,3) NOT NULL DEFAULT '0.000',
  `purity` int unsigned DEFAULT NULL,
  `product_description` text NOT NULL,
  `show_rate` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '1 - yes , 0-no',
  `expiry_date` date DEFAULT NULL,
  `new_type` varchar(50) NOT NULL,
  `gift_type` varchar(50) NOT NULL,
  `send_notification` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 --> Disable 1 --> Enable',
  PRIMARY KEY (`id_new_arrivals`),
  KEY `id_branch` (`id_branch`),
  KEY `active` (`active`),
  KEY `new_type` (`new_type`),
  KEY `id_new_arrivals` (`id_new_arrivals`),
  KEY `active_2` (`active`),
  KEY `new_type_2` (`new_type`),
  KEY `id_new_arrivals_2` (`id_new_arrivals`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nominee`
--

DROP TABLE IF EXISTS `nominee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nominee` (
  `id_nominee` int unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int unsigned NOT NULL DEFAULT '0',
  `id_address` int unsigned NOT NULL DEFAULT '0',
  `id_employee` int unsigned NOT NULL DEFAULT '0',
  `lastname` varchar(32) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `relationship` varchar(45) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `passwd` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `pan` varchar(15) NOT NULL,
  `pan_proof` varchar(50) NOT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_nominee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification` (
  `id_notification` int unsigned NOT NULL AUTO_INCREMENT,
  `noti_code` varchar(25) DEFAULT NULL,
  `noti_name` varchar(25) NOT NULL,
  `noti_general` tinyint NOT NULL DEFAULT '0' COMMENT 'Main Notification On/Off',
  `noti_sub` tinyint NOT NULL DEFAULT '0' COMMENT 'Sub Notification On/Off',
  `noti_msg` text NOT NULL,
  `noti_footer` varchar(10) NOT NULL,
  `send_notif_on` varchar(14) NOT NULL DEFAULT '25' COMMENT 'multiple dates separated with comma (10,25)',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `send_daily_from` int NOT NULL DEFAULT '0' COMMENT 'Ex: 25 (Notification will be sent daily from 25 th till end of the month)',
  PRIMARY KEY (`id_notification`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notify_services`
--

DROP TABLE IF EXISTS `notify_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notify_services` (
  `id_noti_services` int unsigned NOT NULL DEFAULT '0',
  `serv_code` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `serv_name` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `serv_email` tinyint(1) NOT NULL DEFAULT '0',
  `serv_sms` tinyint(1) NOT NULL DEFAULT '0',
  `serv_whatsapp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `serv_push_noti` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nsk_trans1_apr8`
--

DROP TABLE IF EXISTS `nsk_trans1_apr8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nsk_trans1_apr8` (
  `id_transaction` varchar(5) DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `mobile` varchar(10) DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `branch_code` varchar(10) DEFAULT NULL,
  `warehouse` varchar(10) DEFAULT NULL,
  `record_to` varchar(10) DEFAULT NULL,
  `payment_date` varchar(10) DEFAULT NULL,
  `due_month` varchar(10) DEFAULT NULL,
  `due_year` varchar(10) DEFAULT NULL,
  `custom_entry_date` varchar(10) DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `weight` decimal(4,3) DEFAULT NULL,
  `rate` int DEFAULT NULL,
  `metal` varchar(10) DEFAULT NULL,
  `payment_mode` varchar(6) DEFAULT NULL,
  `bank_name` varchar(10) DEFAULT NULL,
  `branch_name` varchar(10) DEFAULT NULL,
  `card_no` varchar(10) DEFAULT NULL,
  `ref_no` varchar(10) DEFAULT NULL,
  `pay_trans_id` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(10) DEFAULT NULL,
  `paid_through` varchar(10) DEFAULT NULL,
  `is_transferred` varchar(10) DEFAULT NULL,
  `is_modified` varchar(10) DEFAULT NULL,
  `transfer_date` varchar(10) DEFAULT NULL,
  `new_customer` varchar(10) DEFAULT NULL,
  `id_scheme_account` varchar(10) DEFAULT NULL,
  `discountAmt` varchar(10) DEFAULT NULL,
  `payment_status` varchar(10) DEFAULT NULL,
  `payment_type` varchar(10) DEFAULT NULL,
  `due_type` varchar(10) DEFAULT NULL,
  `receipt_no` int DEFAULT NULL,
  `date_add` varchar(10) DEFAULT NULL,
  `date_upd` varchar(10) DEFAULT NULL,
  `installment_no` varchar(10) DEFAULT NULL,
  `remarks` varchar(10) DEFAULT NULL,
  `gst` varchar(10) DEFAULT NULL,
  `gst_type` varchar(10) DEFAULT NULL,
  `emp_code` varchar(10) DEFAULT NULL,
  `id_drawee` varchar(10) DEFAULT NULL,
  `dev_remark` varchar(10) DEFAULT NULL,
  `receipt_year` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offers`
--

DROP TABLE IF EXISTS `offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offers` (
  `id_offer` int unsigned NOT NULL AUTO_INCREMENT,
  `id_branch` int unsigned DEFAULT NULL,
  `name` text NOT NULL,
  `offer_content` text NOT NULL,
  `date_add` datetime NOT NULL,
  `date_update` datetime DEFAULT NULL,
  `active` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-inactive , 1- active',
  `offer_img_path` text NOT NULL,
  `type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-offer , 1- banner, 2 - Popup, 3 -Categories, 4 - brochure',
  `link` text,
  `offer_notification` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_offer`),
  KEY `id_branch` (`id_branch`),
  KEY `type` (`type`),
  KEY `active` (`active`),
  KEY `id_offer` (`id_offer`),
  KEY `type_2` (`type`),
  KEY `active_2` (`active`),
  KEY `id_offer_2` (`id_offer`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_cart`
--

DROP TABLE IF EXISTS `order_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart` (
  `id_cart_order` int unsigned NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL COMMENT 'For Which Branch',
  `id_product` int unsigned DEFAULT NULL,
  `design_no` int unsigned DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `size` int DEFAULT NULL,
  `totalitems` int DEFAULT NULL,
  `id_wt_range` int DEFAULT NULL COMMENT 'Weight range ID',
  `orderstatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-in Cart,1-Order Placed',
  `id_orderdetails` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `reject_reason` varchar(500) DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cart_order`)
) ENGINE=InnoDB AUTO_INCREMENT=525 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_status_message`
--

DROP TABLE IF EXISTS `order_status_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_message` (
  `id_order_msg` int NOT NULL AUTO_INCREMENT,
  `order_status` varchar(80) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `remark` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id_order_msg`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `otp`
--

DROP TABLE IF EXISTS `otp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp` (
  `id_otp` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sch_acc` int unsigned DEFAULT NULL,
  `id_emp` int unsigned NOT NULL,
  `otp_gen_time` datetime NOT NULL,
  `otp_code` int unsigned NOT NULL,
  `send_resend` tinyint unsigned NOT NULL DEFAULT '0',
  `is_verified` tinyint unsigned DEFAULT '0',
  `verified_time` datetime DEFAULT NULL,
  `module` varchar(45) DEFAULT NULL,
  `record` text,
  `mobile` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id_otp`),
  KEY `id_sch_acc` (`id_sch_acc`),
  KEY `id_sch_acc_2` (`id_sch_acc`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `otr_inven_purchase_items`
--

DROP TABLE IF EXISTS `otr_inven_purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otr_inven_purchase_items` (
  `otr_pur_itm_id` int NOT NULL AUTO_INCREMENT,
  `inventory_purchase_id` int NOT NULL,
  `inventory_item_id` int NOT NULL,
  `item_qty` int NOT NULL,
  `item_uom` int NOT NULL,
  `item_purchase_rate` decimal(10,2) NOT NULL,
  `item_purchase_unit` int NOT NULL,
  `item_purchase_tax_group` int NOT NULL,
  `item_purchase_total_cost` decimal(10,2) NOT NULL,
  PRIMARY KEY (`otr_pur_itm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `partial_payment`
--

DROP TABLE IF EXISTS `partial_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partial_payment` (
  `id_partial_payment` int unsigned NOT NULL AUTO_INCREMENT,
  `date_payment` datetime DEFAULT NULL,
  `id_scheme_account` int unsigned DEFAULT NULL,
  `id_branch` tinyint DEFAULT NULL,
  `id_employee` int DEFAULT NULL,
  `id_transaction` varchar(50) DEFAULT NULL,
  `installments` int unsigned DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `metal_rate` decimal(10,2) DEFAULT NULL,
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `last_paid_month` int unsigned DEFAULT NULL,
  `payment_status` int unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `processed` varchar(30) DEFAULT 'N',
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_agent` int DEFAULT NULL,
  `partial_receipt_no` int DEFAULT NULL,
  PRIMARY KEY (`id_partial_payment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `id_payment` int NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Saving Scheme, 2 - Gift Card',
  `id_scheme_account` int unsigned NOT NULL,
  `id_scheme` int DEFAULT NULL,
  `date_payment` datetime NOT NULL,
  `due_year_month` varchar(10) DEFAULT NULL COMMENT 'Used from v5 version',
  `installment` int unsigned DEFAULT NULL COMMENT 'installement no',
  `due_type` varchar(5) DEFAULT 'N',
  `id_employee` int DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `id_payGateway` int DEFAULT NULL,
  `id_transaction` varchar(50) DEFAULT NULL,
  `payu_id` varchar(25) DEFAULT NULL COMMENT 'Razor Pay --> order id,Payu - >payu id',
  `id_post_payment` int unsigned DEFAULT NULL,
  `id_drawee` int unsigned DEFAULT NULL,
  `due_month` int DEFAULT NULL COMMENT 'Paid for Month',
  `due_year` year DEFAULT NULL,
  `custom_entry_date` datetime DEFAULT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(45) DEFAULT NULL,
  `dues` tinyint unsigned DEFAULT '1',
  `act_amount` decimal(10,0) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `no_of_dues` int unsigned DEFAULT '1',
  `add_charges` decimal(10,2) DEFAULT NULL,
  `metal_rate` decimal(10,2) DEFAULT '0.00',
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `remark` text,
  `receipt_no` int DEFAULT NULL COMMENT 'receipt_no',
  `display_receipt_no` text COMMENT 'Display this receipt number in all pages ',
  `fix_weight` tinyint DEFAULT '0',
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 -admin , 1- web app, 2 - mobile app, 3 - Admin App, 4 - Cashfree Subscription, 5 - Sync, 6 - Import',
  `free_payment` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'not used',
  `ref_trans_id` varchar(45) DEFAULT NULL,
  `discountAmt` varchar(10) DEFAULT NULL,
  `actual_trans_amt` decimal(10,0) NOT NULL DEFAULT '0',
  `is_editing_enabled` tinyint unsigned DEFAULT '0',
  `approval_date` datetime DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date of insert',
  `is_offline` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -online , 1 - offline',
  `gst` decimal(10,0) NOT NULL,
  `gst_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -amount inclusive of GST , 0 -amount exclusive of gst',
  `gst_amount` decimal(12,3) DEFAULT NULL COMMENT 'GST Calculated by LMX',
  `redeemed_amount` decimal(10,2) DEFAULT NULL,
  `is_point_credited` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes , 2 - No credit required',
  `is_print_taken` tinyint NOT NULL DEFAULT '0' COMMENT '1 - taken , 0 -not taken',
  `id_otp` int DEFAULT NULL,
  `mer_net_amount` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `mer_service_fee` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `cgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `igst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `sgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `gateway_requestaction` varchar(45) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `is_settled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - If payment amount settled by payu to merchant.',
  `estimation_id` int DEFAULT NULL COMMENT 'Estimation Id',
  `offline_tran_uniqueid` varchar(150) DEFAULT NULL COMMENT 'Using for Khimji',
  `old_metal_amount` decimal(10,2) DEFAULT NULL,
  `old_metal_weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `is_gateway_verified` tinyint(1) DEFAULT '0' COMMENT '1 - status verified with gateway',
  `saved_benefits` decimal(10,4) DEFAULT NULL COMMENT 'For digi gold(weight) ',
  `saved_benefit_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Benefit Amount',
  `benefit_value` decimal(10,3) DEFAULT NULL COMMENT 'For digi gold',
  `benefit_type` int DEFAULT NULL COMMENT 'For digi gold',
  `dg_other_benefit_wgt` decimal(10,3) DEFAULT NULL,
  `dg_other_benefit_amt` decimal(10,2) DEFAULT NULL,
  `istransfered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Tally integration sync flag',
  `istallytransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `id_agent` int unsigned DEFAULT NULL COMMENT 'Referred agent',
  `receipt_year` int DEFAULT NULL COMMENT 'Receipt financial year code',
  `paidBy_mobile` int DEFAULT NULL,
  `paidBy_id_customer` int DEFAULT NULL,
  `form_secret` varchar(250) DEFAULT NULL,
  `due_monthyear` varchar(10) DEFAULT NULL COMMENT 'used in v3 version and below',
  `pay_email` varchar(100) DEFAULT NULL,
  `counter_id` varchar(100) DEFAULT NULL COMMENT 'to check counter wise collection ',
  `grace_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `is_limit_exceed` tinyint NOT NULL DEFAULT '0' COMMENT '0- due duration not exceeded, 1- due duration limit exceeded',
  `due_date_to` date DEFAULT NULL,
  `refund_id` varchar(50) DEFAULT NULL,
  `metalrate_edit_date` datetime DEFAULT NULL,
  `dev_remark` text,
  `topup_slab_data` text COMMENT 'topup scheme custom payable slab data',
  PRIMARY KEY (`id_payment`),
  UNIQUE KEY `id_transaction` (`id_transaction`),
  UNIQUE KEY `id_transaction_2` (`id_transaction`,`ref_trans_id`),
  KEY `FK_payment_scheme_account` (`id_scheme_account`),
  KEY `id_branch` (`id_branch`),
  KEY `date_payment` (`date_payment`),
  KEY `payment_mode` (`payment_mode`),
  KEY `payment_ref_number` (`payment_ref_number`),
  KEY `payment_status` (`payment_status`),
  KEY `receipt_no` (`receipt_no`),
  KEY `type` (`type`),
  KEY `id_employee` (`id_employee`),
  KEY `id_payGateway` (`id_payGateway`),
  KEY `custom_entry_date` (`custom_entry_date`),
  KEY `installment` (`installment`),
  KEY `dues` (`dues`),
  KEY `entry_date` (`entry_date`),
  KEY `is_offline` (`is_offline`),
  KEY `is_print_taken` (`is_print_taken`),
  KEY `id_agent` (`id_agent`),
  KEY `istransfered` (`istransfered`),
  KEY `istallytransfered` (`istallytransfered`),
  KEY `is_settled` (`is_settled`),
  KEY `id_scheme_account` (`id_scheme_account`),
  KEY `payment_status_2` (`payment_status`),
  KEY `date_payment_2` (`date_payment`),
  KEY `date_add` (`date_add`),
  KEY `added_by` (`added_by`),
  KEY `payment_mode_2` (`payment_mode`),
  KEY `id_transaction_3` (`id_transaction`),
  KEY `ref_trans_id` (`ref_trans_id`),
  KEY `id_scheme_account_2` (`id_scheme_account`),
  KEY `payment_status_3` (`payment_status`),
  KEY `date_payment_3` (`date_payment`),
  KEY `date_add_2` (`date_add`),
  KEY `added_by_2` (`added_by`),
  KEY `payment_mode_3` (`payment_mode`),
  KEY `id_transaction_4` (`id_transaction`),
  KEY `ref_trans_id_2` (`ref_trans_id`)
) ENGINE=InnoDB AUTO_INCREMENT=557 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_collection_remarks`
--

DROP TABLE IF EXISTS `payment_collection_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_collection_remarks` (
  `id_account` int NOT NULL AUTO_INCREMENT,
  `id_scheme_account` int DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `entry_made_by` int NOT NULL DEFAULT '1' COMMENT '1 --> Agent 2 --> Employee',
  `id_agent` int DEFAULT NULL COMMENT 'visited agent id',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_account`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_gateway`
--

DROP TABLE IF EXISTS `payment_gateway`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_gateway` (
  `id_pg_settings` int NOT NULL,
  `pg_name` varchar(45) NOT NULL,
  `pg_code` int NOT NULL,
  `pg_icon` text,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `saveCard` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `creditCard` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `debitCard` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `netBanking` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `date_add` datetime DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  `is_primary_gateway` tinyint(1) NOT NULL DEFAULT '0' COMMENT '( Default selected gateway ) 0 - No, 1- Yes',
  `description` text,
  `sort` int DEFAULT NULL COMMENT 'Sorting order',
  PRIMARY KEY (`id_pg_settings`),
  UNIQUE KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_mode`
--

DROP TABLE IF EXISTS `payment_mode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_mode` (
  `id_mode` int unsigned NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(50) NOT NULL,
  `short_code` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active, 0 - Inactive	',
  `sort_order` int NOT NULL,
  `show_in_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Yes, 0 - No',
  PRIMARY KEY (`id_mode`),
  UNIQUE KEY `short_code` (`short_code`),
  KEY `id_mode` (`id_mode`),
  KEY `id_mode_2` (`id_mode`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_mode_details`
--

DROP TABLE IF EXISTS `payment_mode_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_mode_details` (
  `id_pay_mode_details` int NOT NULL AUTO_INCREMENT,
  `id_payment` int DEFAULT NULL,
  `NB_type` int DEFAULT NULL COMMENT '1-RTGS,2-IMPS',
  `payment_date` datetime DEFAULT NULL,
  `payment_mode` varchar(15) DEFAULT NULL,
  `payment_amount` decimal(10,0) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `net_banking_date` date DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `remark` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `card_type` int DEFAULT NULL COMMENT '1-RUPAY, 2-VISA,3-Mastro,4-Master',
  `id_pay_device` int DEFAULT NULL COMMENT 'From Payment device Master',
  `id_bank` int DEFAULT NULL,
  `adv_receipt_no` varchar(45) DEFAULT NULL COMMENT 'In this column we store a bill no id thats column name is id_issue_receipt in ret_issue_receipt against a payment which is done by adj adjustment',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  PRIMARY KEY (`id_pay_mode_details`),
  UNIQUE KEY `id_payment` (`id_payment`,`payment_mode`,`payment_ref_number`,`payment_status`,`id_pay_device`,`id_bank`) USING BTREE,
  KEY `id_billing_payment` (`id_pay_mode_details`),
  KEY `bill_id` (`id_payment`),
  KEY `payment_date` (`payment_date`),
  KEY `payment_status` (`payment_status`),
  KEY `id_payment_2` (`id_payment`),
  KEY `NB_type` (`NB_type`),
  KEY `id_pay_device` (`id_pay_device`),
  KEY `id_bank` (`id_bank`)
) ENGINE=InnoDB AUTO_INCREMENT=630 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_old_metal`
--

DROP TABLE IF EXISTS `payment_old_metal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_old_metal` (
  `id_old_pay` int NOT NULL,
  `id_payment` int NOT NULL,
  `est_id` int NOT NULL,
  `bill_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_refund_log`
--

DROP TABLE IF EXISTS `payment_refund_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_refund_log` (
  `id_pay_refund_log` int NOT NULL AUTO_INCREMENT,
  `ref_trans_id` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `refund_id` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `refund_status` int NOT NULL,
  PRIMARY KEY (`id_pay_refund_log`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_status`
--

DROP TABLE IF EXISTS `payment_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_status` (
  `id_payment_status` int unsigned NOT NULL AUTO_INCREMENT,
  `id_payment` int unsigned DEFAULT NULL,
  `id_post_payment` int DEFAULT NULL,
  `id_status_msg` int unsigned DEFAULT NULL,
  `id_employee` int unsigned DEFAULT NULL,
  `charges` decimal(10,2) DEFAULT '0.00',
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_payment_status`),
  KEY `id_payment` (`id_payment`),
  KEY `id_post_payment` (`id_post_payment`),
  KEY `id_employee` (`id_employee`)
) ENGINE=InnoDB AUTO_INCREMENT=462 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_status_message`
--

DROP TABLE IF EXISTS `payment_status_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_status_message` (
  `id_status_msg` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_status` varchar(80) NOT NULL,
  `color` varchar(20) DEFAULT NULL,
  `remark` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_status_msg`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_sum`
--

DROP TABLE IF EXISTS `payment_sum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_sum` (
  `id_payment` int NOT NULL DEFAULT '0',
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `date_payment` datetime NOT NULL,
  `amount` decimal(32,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pending_payment`
--

DROP TABLE IF EXISTS `pending_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pending_payment` (
  `id_pending_payment` int unsigned NOT NULL AUTO_INCREMENT,
  `date_payment` datetime DEFAULT NULL,
  `id_scheme_account` int unsigned DEFAULT NULL,
  `id_transaction` varchar(50) DEFAULT NULL,
  `installments` int unsigned DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `metal_rate` decimal(10,2) DEFAULT NULL,
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `last_paid_month` int unsigned DEFAULT NULL,
  `payment_status` int unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `processed` tinyint DEFAULT '0',
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pending_payment`),
  KEY `id_scheme_account` (`id_scheme_account`),
  KEY `id_transaction` (`id_transaction`),
  KEY `payment_mode` (`payment_mode`),
  KEY `id_transaction_2` (`id_transaction`),
  KEY `id_transaction_3` (`id_transaction`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `postdate_payment`
--

DROP TABLE IF EXISTS `postdate_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `postdate_payment` (
  `id_post_payment` int NOT NULL AUTO_INCREMENT,
  `id_scheme_account` int DEFAULT NULL,
  `id_employee` int DEFAULT NULL,
  `date_payment` datetime DEFAULT NULL,
  `pay_mode` varchar(5) DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `payee_acc_no` varchar(80) DEFAULT NULL,
  `payee_bank` int DEFAULT NULL,
  `payee_branch` varchar(80) DEFAULT NULL,
  `payee_ifsc` varchar(25) DEFAULT NULL,
  `id_drawee` int DEFAULT NULL,
  `charges` decimal(10,2) DEFAULT '0.00',
  `amount` decimal(10,2) DEFAULT NULL,
  `metal_rate` decimal(10,2) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `date_presented` datetime DEFAULT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_post_payment`),
  KEY `payee_bank` (`payee_bank`),
  KEY `payment_status` (`payment_status`),
  KEY `id_scheme_account` (`id_scheme_account`),
  KEY `date_payment` (`date_payment`),
  KEY `payee_bank_2` (`payee_bank`),
  KEY `payment_status_2` (`payment_status`),
  KEY `id_scheme_account_2` (`id_scheme_account`),
  KEY `date_payment_2` (`date_payment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pp_plan_template`
--

DROP TABLE IF EXISTS `pp_plan_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pp_plan_template` (
  `id_plan_template` int NOT NULL AUTO_INCREMENT,
  `id_plan` int unsigned NOT NULL COMMENT 'Purchase plan id',
  `ins_from` int NOT NULL,
  `ins_to` int NOT NULL,
  `limit_by` tinyint(1) DEFAULT '1' COMMENT '1 - Amount, 2 - Weight',
  `min_formula` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Any, \r\n2- X times of I1 inst, \r\n3- Avg of I1 to I2,\r\n4- I1 inst payment\r\n5- X times of 1st payment',
  `min_param` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Multiple parameters separated by comma.',
  `min_condition` tinyint(1) DEFAULT '1' COMMENT '1- N/A,\r\n2- Avg < Y1 ins payable means set Y1 as payable',
  `min_condition_param` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Multiple condition parameters separated by comma.',
  `max_formula` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Any, \r\n2- X times of I1 inst, \r\n3- Avg of I1 to I2,\r\n4- I1 inst payment\r\n5- X times of 1st payment',
  `max_param` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Multiple parameters separated by comma.',
  `max_condition` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- N/A,\r\n2- Avg < Y1 ins payable means set Y1 as payable',
  `max_condition_param` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Multiple condition parameters separated by comma.',
  `denom_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- N/A, 2- Multiples, 3- Master, 4-Grouping',
  `denom_value` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apply_disc` tinyint NOT NULL DEFAULT '1' COMMENT '0 - N/A, 1 - Yes,2 - Give disc if paid ins on or before due date in single shot',
  `disc_by` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-%, 1- Amount',
  `disc_value` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_chance_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Monthly,2- Daily',
  `pay_chances` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_plan_template`),
  KEY `id_plan` (`id_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_enquiry`
--

DROP TABLE IF EXISTS `product_enquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_enquiry` (
  `id_enquiry` int NOT NULL AUTO_INCREMENT,
  `id_product` int NOT NULL,
  `id_customer` int DEFAULT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `message` text NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_enquiry`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id_product` int unsigned NOT NULL AUTO_INCREMENT,
  `id_category` int unsigned NOT NULL,
  `productname` varchar(45) NOT NULL,
  `description` varchar(245) DEFAULT NULL,
  `code` varchar(30) NOT NULL,
  `type` varchar(70) DEFAULT NULL COMMENT 'Men / Women ...',
  `weight` double(13,3) DEFAULT NULL,
  `purity` varchar(20) DEFAULT NULL,
  `price` double(17,2) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `proimage` varchar(120) DEFAULT NULL,
  `active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_product`),
  KEY `FK_products_1` (`id_category`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profession`
--

DROP TABLE IF EXISTS `profession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profession` (
  `id_profession` int NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_profession`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile` (
  `id_profile` int unsigned NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(50) DEFAULT NULL,
  `allow_acc_closing` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - Disable, 1- enable',
  `req_otplogin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0- No, 1 - Yes',
  `show_pending_download` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `show_cart` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `allow_bill_cancel` tinyint NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `bill_cancel_otp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `credit_sales_otp_req` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-NO,1-Yes',
  `order_cancel_otp` tinyint(1) NOT NULL DEFAULT '0',
  `counter_change_otp` tinyint(1) NOT NULL DEFAULT '0',
  `order_unlink_otp` tinyint(1) NOT NULL DEFAULT '0',
  `credit_collection_disc_otp` tinyint(1) NOT NULL DEFAULT '0',
  `allow_branch_transfer_cancel` tinyint NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `device_wise_login` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `tag_transfer` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No',
  `non_tag_transfer` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No',
  `purchase_item_transfer` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No',
  `packaging_item_transfer` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No',
  `est_tag` tinyint NOT NULL DEFAULT '1' COMMENT '1-YES,0-NO',
  `est_non_tag` tinyint NOT NULL DEFAULT '1' COMMENT '1-YES,0-NO',
  `est_home_bill` tinyint NOT NULL DEFAULT '1' COMMENT '1-YES,0-NO',
  `est_old_metal` tinyint NOT NULL DEFAULT '1' COMMENT '1-YES,0-NO',
  `allow_bill_type` int NOT NULL DEFAULT '1' COMMENT '1-Normal.2-EDA,3-All',
  `allow_mc_edit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> Allow MC to be edited, 0 -> No',
  `allow_va_edit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> Allow VA to be edited, , 0 -> No',
  `approval_type` tinyint DEFAULT '0' COMMENT '0-All,1-Supplier Contract Price,2-BT Approval,4-BT Download',
  `tag_details` tinyint(1) NOT NULL DEFAULT '1',
  `purchase_details` tinyint(1) NOT NULL COMMENT '0-no,1-yes',
  `stone_details` tinyint(1) NOT NULL COMMENT '0-no,1-yes',
  `estimation` tinyint(1) NOT NULL COMMENT '0-no,1-yes',
  `branch_transfer_details` tinyint(1) NOT NULL COMMENT '0-no,1-yes',
  `section_transfer_details` tinyint(1) NOT NULL COMMENT '0-no,1-yes',
  `scan_details` tinyint(1) NOT NULL COMMENT '0-no,1-yes',
  `stock_issue_details` tinyint(1) NOT NULL COMMENT '0-no,1-yes',
  `previous_bill_cancel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `allow_order_cancel` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No',
  `est_purity_edit` tinyint NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `allow_lot_cancel` tinyint(1) NOT NULL DEFAULT '1',
  `vendor_approval_otp_req` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes',
  `stock_issue_otp_req` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes',
  `pre_date_oi` tinyint NOT NULL DEFAULT '0' COMMENT '1 -> Show previous date other issue, 0 -> Hide previous date other issue',
  `bill_disc_approval_type` tinyint(1) NOT NULL DEFAULT '1',
  `credit_sales_approval_type` tinyint(1) NOT NULL DEFAULT '1',
  `BT_otp_approval_type` tinyint(1) NOT NULL DEFAULT '1',
  `order_delivery_otp` tinyint(1) DEFAULT '0' COMMENT '1-Yes,0-No',
  `allow_stock_type` int NOT NULL DEFAULT '1' COMMENT '1-Pcs 2-Weight 3-All',
  `order_cancel_otp_req` int NOT NULL DEFAULT '0',
  `order_delievery_otp` int NOT NULL DEFAULT '0',
  `metalrate_edit` tinyint NOT NULL DEFAULT '0',
  `wedding_wastage_slab` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -> No, 1 -> Yes',
  `metal_rate_datelimit` int DEFAULT '1',
  `allow_other_issue` int NOT NULL DEFAULT '0' COMMENT 'BT other issue checkbox control',
  PRIMARY KEY (`id_profile`),
  KEY `id_profile` (`id_profile`),
  KEY `id_profile_2` (`id_profile`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `promotion_api_settings`
--

DROP TABLE IF EXISTS `promotion_api_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_api_settings` (
  `id_promotion_api` int unsigned NOT NULL AUTO_INCREMENT,
  `gateway_type` tinyint(1) DEFAULT NULL COMMENT '1 - msg91, 2 - netty fish, 3 - Spear UC, 4 - Asterixt, 5-Qikberry',
  `promotion_sender_id` varchar(8) DEFAULT NULL,
  `promotion_url` varchar(200) DEFAULT NULL,
  `credit_promotion` decimal(10,0) NOT NULL DEFAULT '0',
  `debit_promotion` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_promotion_api`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_customer`
--

DROP TABLE IF EXISTS `purchase_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_customer` (
  `id_purch_customer` int NOT NULL AUTO_INCREMENT,
  `module_code` varchar(10) DEFAULT NULL COMMENT 'Refer Table : module',
  `title` varchar(25) DEFAULT NULL,
  `firstname` varchar(45) NOT NULL,
  `mobile` varchar(45) NOT NULL,
  `alter_mobile` varchar(15) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `verified_otp` int NOT NULL,
  `last_otp` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_purch_customer`),
  UNIQUE KEY `mobile` (`mobile`),
  KEY `firstname` (`firstname`),
  KEY `mobile_2` (`mobile`),
  KEY `firstname_2` (`firstname`),
  KEY `mobile_3` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_payment`
--

DROP TABLE IF EXISTS `purchase_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_payment` (
  `id_purch_payment` int NOT NULL AUTO_INCREMENT,
  `module_code` varchar(10) DEFAULT NULL COMMENT 'Refer Table : module',
  `mobile` varchar(45) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Purchase Amount, 2 - Purchase Weight, 3 - Jewellery Advance Payment',
  `delivery_preference` tinyint(1) DEFAULT '1' COMMENT '1.Ornament 2.Coin',
  `id_payGateway` int DEFAULT NULL,
  `id_transaction` varchar(50) DEFAULT NULL,
  `payu_id` varchar(25) DEFAULT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `payment_amount` decimal(10,0) DEFAULT NULL,
  `add_charges` decimal(10,2) DEFAULT NULL,
  `metal_rate` decimal(10,2) DEFAULT '0.00',
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `remark` text,
  `receipt_no` varchar(45) DEFAULT NULL COMMENT 'receipt_no',
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 -admin , 1- web app, 2 - mobile app ',
  `ref_trans_id` varchar(45) DEFAULT NULL,
  `discountAmt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `actual_trans_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `approval_date` datetime DEFAULT NULL,
  `gst` decimal(10,0) NOT NULL,
  `gst_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -amount inclusive of GST , 0 -amount exclusive of gst',
  `otp` int DEFAULT NULL COMMENT 'Session OTP at the time of payment made',
  `mer_net_amount` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `mer_service_fee` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `cgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `igst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `sgst` decimal(12,5) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `gateway_requestaction` varchar(45) DEFAULT NULL COMMENT 'Updated from gateway settlement api',
  `is_settled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - If purch_payment amount settled by payu to merchant.',
  `pan_no` varchar(15) DEFAULT NULL COMMENT 'Required if amount over 2L',
  `is_delivered` tinyint NOT NULL DEFAULT '0' COMMENT '0 - Not Delivered, 1 - Delivered',
  `delivery_verif_otp` int DEFAULT NULL COMMENT 'OTP provided by Customer at the time of delivery,',
  `delivery_remark` text,
  `offer_name` varchar(250) DEFAULT NULL COMMENT 'For JAP module',
  `disc_mc_percent` decimal(10,2) DEFAULT NULL COMMENT 'For JAP module',
  `no_of_month` int DEFAULT NULL COMMENT 'For JAP module',
  `adv_paid_percent` decimal(10,0) DEFAULT NULL COMMENT 'For JAP module',
  PRIMARY KEY (`id_purch_payment`),
  UNIQUE KEY `id_transaction` (`id_transaction`),
  UNIQUE KEY `id_transaction_2` (`id_transaction`,`ref_trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registered_devices`
--

DROP TABLE IF EXISTS `registered_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registered_devices` (
  `id_device_detail` int unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int unsigned NOT NULL,
  `token` varchar(500) DEFAULT NULL,
  `uuid` varchar(200) DEFAULT NULL,
  `device_type` tinyint unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_device_detail`),
  KEY `id_customer` (`id_customer`),
  KEY `id_customer_2` (`id_customer`),
  KEY `id_customer_3` (`id_customer`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registration`
--

DROP TABLE IF EXISTS `registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registration` (
  `id_register` int unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int unsigned NOT NULL,
  `id_scheme` int unsigned NOT NULL,
  `date_register` datetime DEFAULT NULL,
  `date_confirm` datetime DEFAULT NULL,
  `is_approved` tinyint unsigned NOT NULL,
  `id_employee` int unsigned NOT NULL,
  PRIMARY KEY (`id_register`),
  KEY `id_customer` (`id_customer`),
  KEY `id_scheme` (`id_scheme`),
  KEY `is_approved` (`is_approved`),
  KEY `id_customer_2` (`id_customer`),
  KEY `id_scheme_2` (`id_scheme`),
  KEY `is_approved_2` (`is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relationship`
--

DROP TABLE IF EXISTS `relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relationship` (
  `id_relationship` int unsigned NOT NULL AUTO_INCREMENT,
  `relationship` varchar(50) NOT NULL,
  PRIMARY KEY (`id_relationship`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_acc_stock_process`
--

DROP TABLE IF EXISTS `ret_acc_stock_process`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_acc_stock_process` (
  `id_process` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `type` tinyint DEFAULT NULL COMMENT '1-Sales Return,3-Partly Sale,4-Old Metal,5-Non Tag Sales Return,6-H.O Other Issue',
  `process_for` tinyint DEFAULT NULL COMMENT '1-Retag,4-Non Tag,2-Other Issue,5-Accounts Stock',
  `id_ret_category` int DEFAULT NULL,
  `id_ret_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `id_karigar` int DEFAULT NULL,
  `id_purity` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  UNIQUE KEY `id_process` (`id_process`)
) ENGINE=InnoDB AUTO_INCREMENT=1194 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_acc_stock_process_details`
--

DROP TABLE IF EXISTS `ret_acc_stock_process_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_acc_stock_process_details` (
  `id_process_details` int NOT NULL AUTO_INCREMENT,
  `id_process` int DEFAULT NULL,
  `ref_no` int DEFAULT NULL,
  `piece` int DEFAULT '0',
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `narration` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_process_details`)
) ENGINE=InnoDB AUTO_INCREMENT=1488 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_acc_stock_process_stone_details`
--

DROP TABLE IF EXISTS `ret_acc_stock_process_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_acc_stock_process_stone_details` (
  `id_acc_stck_stone_details` int NOT NULL AUTO_INCREMENT,
  `id_process_details` int DEFAULT NULL,
  `stone_id` int DEFAULT NULL,
  `stone_pcs` int DEFAULT NULL,
  `stone_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id_acc_stck_stone_details`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_account_head`
--

DROP TABLE IF EXISTS `ret_account_head`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_account_head` (
  `id_acc_head` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `short_code` varchar(25) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active, 0 - Inactive',
  `narration` text,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  UNIQUE KEY `id_acc_head` (`id_acc_head`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_admin_approval_estimates`
--

DROP TABLE IF EXISTS `ret_admin_approval_estimates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_admin_approval_estimates` (
  `app_est_id` int NOT NULL AUTO_INCREMENT,
  `app_est_appid` int NOT NULL,
  `app_est_estid` int NOT NULL,
  PRIMARY KEY (`app_est_id`),
  KEY `app_est_appid` (`app_est_appid`),
  KEY `app_est_estid` (`app_est_estid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_admin_approval_status`
--

DROP TABLE IF EXISTS `ret_admin_approval_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_admin_approval_status` (
  `apprl_id` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `bill_id` int DEFAULT NULL,
  `apprl_bt_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apprl_bt_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bt_dia_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `bt_gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `bt_net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `BT_approval_type` int DEFAULT NULL,
  `transfer_item_type` int DEFAULT NULL,
  `apprl_esti_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apprl_bill_discount` decimal(10,2) DEFAULT NULL,
  `apprl_type` int DEFAULT NULL COMMENT '1->Bill Discount 2->Credit Bill Sale 3-> BT Approval',
  `apprl_cus_id` int DEFAULT NULL,
  `apprl_requested_by` int DEFAULT NULL COMMENT 'Employee Id',
  `apprl_requested_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `apprl_disp_message` varchar(600) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apprl_approved_by` int DEFAULT NULL COMMENT 'Super admin id',
  `apprl_approved_on` datetime DEFAULT NULL,
  `apprl_tot_bill_amount` decimal(10,2) DEFAULT NULL,
  `apprl_cr_paid_amt` decimal(10,2) DEFAULT NULL,
  `is_credit` tinyint(1) DEFAULT NULL COMMENT '1-credit,2-ToBe',
  `apprl_status` int NOT NULL DEFAULT '0' COMMENT '0-> Waiting for Approval 1-> Approved 2-> Rejected 3-> Cancelled 4-> Expired\r\n',
  `disc_amt` int DEFAULT NULL,
  `is_disc_modified` tinyint(1) DEFAULT NULL,
  `items` text COLLATE utf8mb4_unicode_ci,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`apprl_id`),
  KEY `Idxaprlestid` (`apprl_bt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=792 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_adv_receipt_tags`
--

DROP TABLE IF EXISTS `ret_adv_receipt_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_adv_receipt_tags` (
  `id_adv_rcpt` int NOT NULL AUTO_INCREMENT,
  `adv_rcpt_issue_receipt_id` int NOT NULL,
  `adv_rcpt_tagid` int NOT NULL,
  `adv_rcpt_esti_detail_id` int NOT NULL,
  PRIMARY KEY (`id_adv_rcpt`),
  KEY `adv_rcpt_issue_receipt_id` (`adv_rcpt_issue_receipt_id`),
  KEY `adv_rcpt_tagid` (`adv_rcpt_tagid`),
  KEY `adv_rcpt_esti_detail_id` (`adv_rcpt_esti_detail_id`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_adv_receipt_weight`
--

DROP TABLE IF EXISTS `ret_adv_receipt_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_adv_receipt_weight` (
  `id_weight_adv` int NOT NULL AUTO_INCREMENT,
  `id_issue_receipt` int NOT NULL,
  `est_old_metal_sale_id` int NOT NULL,
  PRIMARY KEY (`id_weight_adv`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_advance_refund`
--

DROP TABLE IF EXISTS `ret_advance_refund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_advance_refund` (
  `id_refund` int NOT NULL AUTO_INCREMENT,
  `id_issue_receipt` int NOT NULL,
  `refund_receipt` int NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_refund`),
  KEY `id_issue_receipt` (`id_issue_receipt`),
  KEY `refund_receipt` (`refund_receipt`)
) ENGINE=InnoDB AUTO_INCREMENT=334 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_advance_transfer`
--

DROP TABLE IF EXISTS `ret_advance_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_advance_transfer` (
  `id_advance_transfer` int NOT NULL AUTO_INCREMENT,
  `id_issue_receipt` int NOT NULL,
  `transfer_receipt_id` int NOT NULL,
  `transfer_amount` decimal(10,0) NOT NULL,
  `transfer_cash_amt` decimal(12,2) DEFAULT '0.00' COMMENT 'Cash amount transferred',
  `otp` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_advance_transfer`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_advance_utilized`
--

DROP TABLE IF EXISTS `ret_advance_utilized`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_advance_utilized` (
  `id_adv_utilized` int NOT NULL AUTO_INCREMENT,
  `adv_utilized_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> General Adv\r\n2 -> Ord Adv',
  `bill_adv_id` int DEFAULT NULL,
  `id_issue_receipt` int DEFAULT NULL,
  `adjusted_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Aganist bill , 2 - Crm Payment',
  `bill_id` int DEFAULT NULL,
  `id_payment` int DEFAULT NULL,
  `utilized_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cash_utilized_amt` decimal(12,2) DEFAULT '0.00' COMMENT 'Cash amt utilized on adv adjustment',
  `adj_weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_adv_utilized`),
  KEY `bill_id` (`bill_id`),
  KEY `id_issue_receipt` (`id_issue_receipt`),
  KEY `id_payment` (`id_payment`)
) ENGINE=InnoDB AUTO_INCREMENT=1795 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_attribute`
--

DROP TABLE IF EXISTS `ret_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_attribute` (
  `attr_id` int NOT NULL AUTO_INCREMENT,
  `attr_name` varchar(35) COLLATE utf8mb4_general_ci NOT NULL,
  `attr_group_type` tinyint(1) NOT NULL DEFAULT '1',
  `attr_status` tinyint(1) NOT NULL DEFAULT '1',
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime(1) DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`attr_id`),
  KEY `attr_group_type` (`attr_group_type`),
  KEY `attr_status` (`attr_status`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_attribute_values`
--

DROP TABLE IF EXISTS `ret_attribute_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_attribute_values` (
  `attr_val_id` int NOT NULL AUTO_INCREMENT,
  `attr_id` int NOT NULL,
  `attr_val` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`attr_val_id`),
  KEY `attribute_id` (`attr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bank_deposit`
--

DROP TABLE IF EXISTS `ret_bank_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bank_deposit` (
  `dep_id` int NOT NULL AUTO_INCREMENT,
  `dep_branch` int DEFAULT '0' COMMENT 'Branch id, if branch wise cash deposit',
  `type` int DEFAULT NULL COMMENT '1 -> Retail, 2 -> Chit',
  `dep_amount` decimal(12,2) NOT NULL,
  `dep_type` tinyint(1) NOT NULL COMMENT '1 -> Credit, 0 -> Debit',
  `dep_bank` int unsigned NOT NULL COMMENT 'Refer Table - bank',
  `dep_ref_id` varchar(35) COLLATE utf8mb4_general_ci NOT NULL,
  `dep_cur_balance` decimal(12,2) NOT NULL,
  `dep_narration` varchar(300) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dep_by` int unsigned NOT NULL COMMENT 'Refer Table - employee',
  `dep_date` datetime NOT NULL,
  `cash_date` date DEFAULT NULL COMMENT 'Cash In Hand Date',
  `dep_mode` int unsigned DEFAULT NULL COMMENT 'Refer Table payment_mode',
  `created_on` datetime NOT NULL,
  `created_by` int unsigned NOT NULL COMMENT 'Refer Table - employee',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`dep_id`),
  KEY `dep_bank_constraint` (`dep_bank`),
  KEY `dep_by_constraint` (`dep_by`),
  KEY `dep_mode_constraint` (`dep_mode`),
  KEY `dep_branch` (`dep_branch`),
  KEY `type` (`type`),
  KEY `dep_type` (`dep_type`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_delivery`
--

DROP TABLE IF EXISTS `ret_bill_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_delivery` (
  `id_delivery` int NOT NULL AUTO_INCREMENT,
  `bill_id` int DEFAULT NULL,
  `id_customer` int DEFAULT NULL,
  `address_name` text,
  `id_country` int DEFAULT NULL,
  `id_state` int DEFAULT NULL,
  `id_city` int DEFAULT NULL,
  `address1` text,
  `address2` text,
  `address3` text,
  `pincode` int DEFAULT NULL,
  PRIMARY KEY (`id_delivery`),
  KEY `bill_id` (`bill_id`),
  KEY `id_customer` (`id_customer`),
  KEY `id_country` (`id_country`),
  KEY `id_state` (`id_state`),
  KEY `id_city` (`id_city`)
) ENGINE=InnoDB AUTO_INCREMENT=2726 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_details`
--

DROP TABLE IF EXISTS `ret_bill_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_details` (
  `bill_det_id` int NOT NULL AUTO_INCREMENT,
  `esti_item_id` int DEFAULT NULL,
  `bill_id` int DEFAULT NULL,
  `item_type` int DEFAULT NULL COMMENT '1-> Catalog 2-> Custom,3-From repair Order Extra weight',
  `item_emp_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `design_id` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `tag_id` int DEFAULT NULL,
  `order_no` int DEFAULT NULL,
  `id_orderdetails` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `purity` int DEFAULT NULL,
  `size` decimal(10,2) DEFAULT NULL,
  `uom` int DEFAULT NULL,
  `show_huid` tinyint NOT NULL DEFAULT '1',
  `huid` varchar(50) DEFAULT NULL,
  `piece` int DEFAULT NULL,
  `less_wt` decimal(12,3) DEFAULT NULL,
  `net_wt` decimal(12,3) DEFAULT NULL,
  `gross_wt` decimal(12,3) DEFAULT NULL,
  `calculation_based_on` int DEFAULT NULL,
  `wastage_percent` decimal(10,2) DEFAULT NULL,
  `mc_type` int DEFAULT NULL COMMENT '1-> Per Pc 2->Per Grm 3-> % on price',
  `mc_value` decimal(10,2) DEFAULT NULL,
  `tax_group_id` int DEFAULT NULL,
  `total_sgst` decimal(10,2) DEFAULT NULL,
  `total_cgst` decimal(10,2) DEFAULT NULL,
  `total_igst` decimal(10,2) DEFAULT NULL,
  `item_total_tax` decimal(10,2) DEFAULT NULL,
  `bill_type` int DEFAULT NULL COMMENT '1-> Purchase 2->Sales',
  `bill_discount` decimal(10,2) DEFAULT NULL,
  `mc_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `wastage_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_blc_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bill_discount_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-General Disc,2-Apply in V.A & M.C',
  `rate_per_grm` decimal(10,2) DEFAULT NULL,
  `item_cost` decimal(10,2) DEFAULT NULL,
  `sales_return_discount` decimal(10,0) DEFAULT NULL COMMENT 'Other charges for return item will be added as discount',
  `return_item_cost` decimal(10,0) DEFAULT NULL COMMENT 'Returned Item cost',
  `round_of_amt` decimal(10,0) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Sold, 2 - Returned',
  `is_partial_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Partly Sale',
  `is_non_tag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-tagged items,1-Non_tagged items',
  `transferred_to_acc_stock` int NOT NULL DEFAULT '0' COMMENT '1-yes,0-No for Non Tag Sales return only',
  `current_branch` int DEFAULT NULL COMMENT 'For non tag Sales return',
  `acc_stock_process` int DEFAULT NULL COMMENT '1-Added to Non Tag Stock',
  `id_lot_inward_detail` int DEFAULT NULL,
  `id_collecion_maping_det` int DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `is_delivered` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 0-No,1-Normal Delivery,2-Later Delivery',
  `delivered_date` datetime DEFAULT NULL,
  `delivered_by` int DEFAULT NULL,
  PRIMARY KEY (`bill_det_id`),
  KEY `bill_id` (`bill_id`),
  KEY `tag_id` (`tag_id`),
  KEY `product_id` (`product_id`),
  KEY `design_id` (`design_id`),
  KEY `gross_wt` (`gross_wt`),
  KEY `esti_item_id` (`esti_item_id`),
  KEY `istransfered` (`istransfered`),
  KEY `id_orderdetails` (`id_orderdetails`),
  KEY `id_sub_design` (`id_sub_design`),
  KEY `item_type` (`item_type`),
  KEY `calculation_based_on` (`calculation_based_on`),
  KEY `mc_type` (`mc_type`),
  KEY `bill_type` (`bill_type`),
  KEY `tax_group_id` (`tax_group_id`),
  KEY `status` (`status`),
  KEY `is_partial_sale` (`is_partial_sale`)
) ENGINE=InnoDB AUTO_INCREMENT=2512 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_duplicate_copy`
--

DROP TABLE IF EXISTS `ret_bill_duplicate_copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_duplicate_copy` (
  `copy_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int NOT NULL,
  `id_employee` int NOT NULL,
  `print_date` datetime DEFAULT NULL,
  PRIMARY KEY (`copy_id`),
  KEY `bill_id` (`bill_id`),
  KEY `id_employee` (`id_employee`),
  KEY `print_date` (`print_date`)
) ENGINE=MyISAM AUTO_INCREMENT=4006 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_gift_voucher_settings`
--

DROP TABLE IF EXISTS `ret_bill_gift_voucher_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_gift_voucher_settings` (
  `id_set_gift_voucher` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `metal` int DEFAULT NULL,
  `gift_type` int DEFAULT NULL COMMENT '1- Amt to Amt , 2- Amt to weight based, 3 - Wgt to amount, 4 - Wgt to Wgt]',
  `sale_value` decimal(10,2) DEFAULT NULL,
  `credit_value` decimal(10,2) DEFAULT NULL,
  `calc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Flat,2-Each',
  `utilize_for` tinyint(1) DEFAULT NULL COMMENT '1-Gold, 2 -Silver',
  `validity_days` int DEFAULT NULL,
  `status` tinyint DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `note` text COLLATE utf8mb4_unicode_ci,
  `sort` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_set_gift_voucher`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_old_metal_sale_details`
--

DROP TABLE IF EXISTS `ret_bill_old_metal_sale_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_old_metal_sale_details` (
  `old_metal_sale_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int DEFAULT NULL,
  `esti_old_metal_sale_id` int DEFAULT NULL,
  `est_id` int DEFAULT NULL,
  `purpose` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Cash, 2 - Exchange',
  `metal_type` int DEFAULT NULL COMMENT '1-> Gold 2->Silver',
  `id_old_metal_type` int DEFAULT NULL,
  `id_old_metal_category` int DEFAULT NULL,
  `item_type` int DEFAULT NULL COMMENT '1->Oranament 2-> Coin 3-> Bar',
  `piece` int NOT NULL DEFAULT '1',
  `gross_wt` decimal(12,3) DEFAULT NULL,
  `stone_wt` decimal(12,3) DEFAULT NULL,
  `dust_wt` decimal(12,3) DEFAULT NULL,
  `wast_wt` decimal(12,3) DEFAULT '0.000',
  `net_wt` decimal(12,3) NOT NULL DEFAULT '0.000',
  `wastage_percent` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `touch` decimal(10,2) NOT NULL DEFAULT '100.00',
  `purity` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `is_retag` int DEFAULT NULL COMMENT '1-> Yes 0-> No => It will comes in tag screen',
  `rate_per_grm` decimal(10,2) DEFAULT NULL,
  `bill_discount` decimal(10,2) DEFAULT NULL,
  `old_metal_rate` decimal(10,2) DEFAULT NULL,
  `current_branch` int DEFAULT NULL,
  `is_transferred` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `is_pocketed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No,2-Added to Acc Stock,3-Previous stock Adj,4-Added to Non Tag,5-Generate as Lot',
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `id_employee` int NOT NULL,
  PRIMARY KEY (`old_metal_sale_id`),
  KEY `bill_id` (`bill_id`),
  KEY `esti_old_metal_sale_id` (`esti_old_metal_sale_id`),
  KEY `est_id` (`est_id`),
  KEY `purpose` (`purpose`),
  KEY `metal_type` (`metal_type`),
  KEY `item_type` (`item_type`),
  KEY `is_retag` (`is_retag`),
  KEY `current_branch` (`current_branch`),
  KEY `is_transferred` (`is_transferred`),
  KEY `is_pocketed` (`is_pocketed`),
  KEY `istransfered` (`istransfered`)
) ENGINE=InnoDB AUTO_INCREMENT=867 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_other_charges`
--

DROP TABLE IF EXISTS `ret_bill_other_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_other_charges` (
  `id_bill_charge` int NOT NULL AUTO_INCREMENT,
  `bill_det_id` int NOT NULL,
  `id_charge` int NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_bill_charge`)
) ENGINE=InnoDB AUTO_INCREMENT=939 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_other_metals`
--

DROP TABLE IF EXISTS `ret_bill_other_metals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_other_metals` (
  `bill_other_itm_id` int NOT NULL AUTO_INCREMENT,
  `bill_det_id` int NOT NULL,
  `tag_other_itm_metal_id` int NOT NULL,
  `tag_other_itm_pur_id` int DEFAULT NULL,
  `tag_other_itm_grs_weight` decimal(10,3) NOT NULL,
  `tag_other_itm_wastage` decimal(10,2) DEFAULT NULL,
  `tag_other_itm_uom` int NOT NULL,
  `tag_other_itm_cal_type` int NOT NULL COMMENT '1-> Weight 2->pcs',
  `tag_other_itm_mc` decimal(10,2) NOT NULL,
  `tag_other_itm_rate` decimal(10,2) NOT NULL,
  `tag_other_itm_pcs` int NOT NULL,
  `tag_other_itm_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`bill_other_itm_id`),
  KEY `bill_det_id` (`bill_det_id`),
  KEY `tag_other_itm_metal_id` (`tag_other_itm_metal_id`),
  KEY `tag_other_itm_pur_id` (`tag_other_itm_pur_id`),
  KEY `tag_other_itm_cal_type` (`tag_other_itm_cal_type`),
  KEY `tag_other_itm_uom` (`tag_other_itm_uom`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_pay_device`
--

DROP TABLE IF EXISTS `ret_bill_pay_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_pay_device` (
  `id_device` int NOT NULL AUTO_INCREMENT,
  `device_name` varchar(20) NOT NULL,
  `sort_order` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `device_type` int NOT NULL DEFAULT '1' COMMENT '1-> Wallet 2-> Bank',
  `created_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_device`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_return_details`
--

DROP TABLE IF EXISTS `ret_bill_return_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_return_details` (
  `return_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int NOT NULL,
  `ret_bill_id` int NOT NULL COMMENT 'Return ref bil id',
  `ret_bill_det_id` int NOT NULL COMMENT 'Return ref bil items detail id',
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `ret_cash_paid` decimal(12,2) DEFAULT '0.00' COMMENT 'Cash paid earlier on that returned bill.',
  PRIMARY KEY (`return_id`),
  KEY `bill_id` (`bill_id`),
  KEY `ret_bill_det_id` (`ret_bill_det_id`),
  KEY `ret_bill_id` (`ret_bill_id`)
) ENGINE=MyISAM AUTO_INCREMENT=207 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bill_supplier_sales_details`
--

DROP TABLE IF EXISTS `ret_bill_supplier_sales_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bill_supplier_sales_details` (
  `pur_ret_cat_itm_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int NOT NULL,
  `pur_ret_cat_id` int DEFAULT NULL,
  `pur_ret_cat_pcs` int NOT NULL,
  `pur_ret_cat_gwt` decimal(10,3) NOT NULL,
  `pur_ret_cat_leswt` decimal(10,3) NOT NULL,
  `pur_ret_cat_netwt` decimal(10,3) NOT NULL,
  `calc_type` int DEFAULT NULL,
  `pur_ret_rate` decimal(10,2) DEFAULT NULL,
  `pur_ret_tax_rate` decimal(10,2) DEFAULT NULL,
  `pur_ret_tax_value` decimal(10,2) DEFAULT NULL,
  `pur_ret_cgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_sgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_igst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_item_cost` decimal(10,2) DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`pur_ret_cat_itm_id`),
  KEY `pur_ret_cat_itm_id` (`pur_ret_cat_itm_id`),
  KEY `pur_ret_id` (`bill_id`),
  KEY `pur_ret_cat_id` (`pur_ret_cat_id`),
  KEY `istransfered` (`istransfered`),
  KEY `pur_ret_cat_pcs` (`pur_ret_cat_pcs`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_billing`
--

DROP TABLE IF EXISTS `ret_billing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_billing` (
  `bill_id` int NOT NULL AUTO_INCREMENT,
  `fin_year_code` varchar(15) DEFAULT NULL,
  `bill_type` tinyint NOT NULL COMMENT '1-Sales,2-Sales&Purchase,3-Sales&Return,4-Purchase,5-Order Advance,6-Advance,7-Sales Return,8-Credit Bill Payment,9-order Delivery,10-Chit Proclose,11-Repair Order Delivery,12-Supplier Sales Bill,13-Sales Transfer,14-Sales Ret Transfer 15->Approval stock bill delivery',
  `metal_type` int DEFAULT NULL,
  `bill_no` varchar(45) DEFAULT NULL,
  `sales_ref_no` varchar(45) DEFAULT NULL COMMENT 'Sales Ref No (br_code/FinYear/SA/num)',
  `pur_ref_no` varchar(45) DEFAULT NULL COMMENT 'Purchase Ref No (br_code/FinYear/PU/num)',
  `order_adv_ref_no` varchar(45) DEFAULT NULL COMMENT 'Order Advance Ref No (br_code/FinYear/OD/num)',
  `s_ret_refno` varchar(45) DEFAULT NULL COMMENT 'Sale Return Ref No (br_code/FinYear/SR/num)',
  `credit_coll_refno` varchar(20) DEFAULT NULL,
  `chit_preclose_refno` varchar(20) DEFAULT NULL,
  `repair_del_ref_no` varchar(20) DEFAULT NULL,
  `approval_ref_no` varchar(20) DEFAULT NULL,
  `ref_bill_id` int DEFAULT NULL COMMENT 'Sales Return Reference Bill Id',
  `bill_date` datetime DEFAULT NULL,
  `bill_cus_id` int DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `old_bill_cus_id` int DEFAULT NULL COMMENT 'Used during CRM,Retail merge',
  `mobile` varchar(45) DEFAULT NULL COMMENT 'Used during CRM,Retail merge',
  `tot_tax_value` decimal(10,2) DEFAULT NULL,
  `tot_discount` decimal(10,2) DEFAULT NULL,
  `return_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credit_disc_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tot_purchase_amt` decimal(10,0) DEFAULT NULL,
  `tot_amt_received` decimal(10,0) DEFAULT NULL COMMENT 'Total Payment  received from customer',
  `tot_bill_amount` decimal(10,0) DEFAULT NULL COMMENT 'Total Bill Amount',
  `handling_charges` decimal(10,2) DEFAULT NULL,
  `round_off_amt` varchar(40) DEFAULT '0',
  `return_round_off_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pan_no` varchar(50) DEFAULT NULL,
  `gst_number` varchar(30) DEFAULT NULL,
  `aadhar_no` text,
  `is_bill_split` int DEFAULT NULL,
  `bill_pancard` varchar(75) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `is_credit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Credit given to cus',
  `is_to_be` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Credit is to be',
  `credit_due_date` datetime DEFAULT NULL,
  `credit_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Paid, 2 - Pending',
  `tot_sale_amt` decimal(10,0) DEFAULT NULL,
  `return_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - None, 1 - Partially Returned, 2 - Fully Returned',
  `remark` text,
  `pan_image` text COMMENT 'Multiple Image seperated by#',
  `print_taken` int NOT NULL DEFAULT '0',
  `bill_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Success, 2 - Cancelled',
  `cancelled_date` datetime DEFAULT NULL COMMENT 'Bill cancel Date',
  `id_delivery` int DEFAULT NULL COMMENT 'Place of Delivery',
  `cancel_reason` text COMMENT 'Bill Cancel Reason',
  `is_trail` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,2-No',
  `goldrate_22ct` decimal(10,2) DEFAULT NULL,
  `silverrate_1gm` decimal(10,2) DEFAULT NULL,
  `goldrate_9ct` decimal(10,2) DEFAULT NULL,
  `goldrate_14ct` decimal(10,2) DEFAULT NULL,
  `goldrate_18ct` decimal(10,2) NOT NULL DEFAULT '0.00',
  `counter_id` int DEFAULT NULL,
  `billing_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Customer,2-Company',
  `id_cmp_emp` int DEFAULT NULL,
  `tcs_tax_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'TCS Total tax amt',
  `tcs_tax_per` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'TCS Tax Percentage',
  `tds_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tds_tax_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivered_at` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Show Room,2-Customer Address',
  `delivery_address_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Customer Registered Address,2-Others',
  `make_as_advance` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes,2-Chit Deposit',
  `advance_deposit` decimal(10,0) NOT NULL DEFAULT '0',
  `form_secret` varchar(100) DEFAULT NULL COMMENT 'For Avoid Dulpicate Form Submit',
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `credit_ret_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credit_due_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credit_reference` varchar(45) DEFAULT NULL,
  `from_branch` int DEFAULT NULL,
  `to_branch` int DEFAULT NULL,
  `download_date` datetime DEFAULT NULL,
  `download_by` int DEFAULT NULL,
  `is_eda` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Sale,2- Est. Appr',
  `id_employee` int DEFAULT NULL,
  `eda_tax_calc` int NOT NULL DEFAULT '0',
  `bill_split_ref_id` varchar(20) DEFAULT NULL,
  `cusdel_irn` varchar(200) DEFAULT NULL,
  `qrcodeimage` text,
  `cusdel_signature` text,
  `irndelivery` int DEFAULT NULL,
  `is_otp_approved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -> No 1 - > Yes',
  `otp_approved_by` int DEFAULT NULL COMMENT 'id_employee',
  `disc_approved_id` int DEFAULT NULL,
  `disc_approved_by` int DEFAULT NULL,
  `credit_approved_id` int DEFAULT NULL,
  `credit_approved_by` int DEFAULT NULL,
  `irn_date` date DEFAULT NULL,
  PRIMARY KEY (`bill_id`),
  UNIQUE KEY `form_secret` (`form_secret`),
  UNIQUE KEY `Bill No` (`fin_year_code`,`bill_no`,`id_branch`,`is_eda`) USING BTREE,
  KEY `id_branch` (`id_branch`),
  KEY `bill_date` (`bill_date`),
  KEY `bill_status` (`bill_status`),
  KEY `bill_type` (`bill_type`),
  KEY `ref_bill_id` (`ref_bill_id`),
  KEY `bill_cus_id` (`bill_cus_id`),
  KEY `id_delivery` (`id_delivery`),
  KEY `billing_for` (`billing_for`),
  KEY `istransfered` (`istransfered`),
  KEY `is_credit` (`is_credit`),
  KEY `Idx_sal_approval_no` (`approval_ref_no`)
) ENGINE=InnoDB AUTO_INCREMENT=3136 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_billing_advance`
--

DROP TABLE IF EXISTS `ret_billing_advance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_billing_advance` (
  `bill_adv_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int NOT NULL,
  `advance_type` tinyint(1) DEFAULT '1' COMMENT '1 - Amount for Order, 2 - Old Metal for Order',
  `order_no` varchar(25) DEFAULT NULL,
  `id_customerorder` int DEFAULT NULL,
  `advance_amount` int DEFAULT NULL,
  `advance_weight` decimal(10,4) DEFAULT NULL,
  `store_as` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Amount , 2 - Weight. (Convert based on current rate)',
  `rate_calc` tinyint DEFAULT NULL COMMENT '1-Gold,2-Silver',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `received_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'If received as amount',
  `received_weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'If received as weight',
  `old_metal_sale_id` int DEFAULT NULL COMMENT 'If received as weight',
  `advance_date` datetime DEFAULT NULL,
  `is_adavnce_adjusted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Yes, 0 - No',
  `adjusted_bill_id` int DEFAULT NULL,
  `adjusted_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remark` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`bill_adv_id`),
  KEY `bill_id` (`bill_id`),
  KEY `advance_type` (`advance_type`),
  KEY `id_customerorder` (`id_customerorder`),
  KEY `store_as` (`store_as`),
  KEY `is_adavnce_adjusted` (`is_adavnce_adjusted`),
  KEY `old_metal_sale_id` (`old_metal_sale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=744 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_billing_chit_utilization`
--

DROP TABLE IF EXISTS `ret_billing_chit_utilization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_billing_chit_utilization` (
  `bill_chit_ut_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int DEFAULT NULL,
  `scheme_account_id` int NOT NULL,
  `utilized_amt` decimal(10,2) DEFAULT NULL,
  `closing_weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `wastage_per` int DEFAULT NULL,
  `savings_in_wastage` decimal(10,3) NOT NULL DEFAULT '0.000',
  `mc_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `savings_in_making_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`bill_chit_ut_id`),
  KEY `bill_id` (`bill_id`),
  KEY `scheme_account_id` (`scheme_account_id`),
  KEY `istransfered` (`istransfered`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_billing_gift_voucher_details`
--

DROP TABLE IF EXISTS `ret_billing_gift_voucher_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_billing_gift_voucher_details` (
  `bill_gift_voucher_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int DEFAULT NULL,
  `voucher_no` varchar(45) DEFAULT NULL,
  `gift_voucher_details` varchar(95) DEFAULT NULL,
  `gift_voucher_amt` decimal(10,2) DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`bill_gift_voucher_id`),
  KEY `bill_id` (`bill_id`),
  KEY `istransfered` (`istransfered`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_billing_item_other_materials`
--

DROP TABLE IF EXISTS `ret_billing_item_other_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_billing_item_other_materials` (
  `bill_other_material_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int DEFAULT NULL,
  `bill_det_id` int DEFAULT NULL,
  `material_id` int DEFAULT NULL,
  `wt` decimal(12,4) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`bill_other_material_id`),
  KEY `bill_id` (`bill_id`),
  KEY `bill_det_id` (`bill_det_id`),
  KEY `material_id` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_billing_item_stones`
--

DROP TABLE IF EXISTS `ret_billing_item_stones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_billing_item_stones` (
  `bill_item_stone_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int DEFAULT NULL,
  `bill_det_id` int DEFAULT NULL,
  `old_metal_sale_id` int DEFAULT NULL COMMENT 'Old Metal Sale Id',
  `item_type` tinyint DEFAULT NULL COMMENT '1-Sale Item,2-Purchase Item',
  `stone_id` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `wt` decimal(12,4) DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `certification_price` decimal(10,0) DEFAULT NULL,
  `is_apply_in_lwt` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Stone wt applied in LWT=>1-Yes,0-No',
  `stone_cal_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-> By weight 2->By pcs',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`bill_item_stone_id`),
  KEY `bill_id` (`bill_id`),
  KEY `bill_det_id` (`bill_det_id`),
  KEY `old_metal_sale_id` (`old_metal_sale_id`),
  KEY `item_type` (`item_type`)
) ENGINE=InnoDB AUTO_INCREMENT=995 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_billing_other_inventory_issue`
--

DROP TABLE IF EXISTS `ret_billing_other_inventory_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_billing_other_inventory_issue` (
  `id_inv_issue` int NOT NULL AUTO_INCREMENT,
  `bill_id` int NOT NULL,
  `id_other_item` int NOT NULL,
  `no_of_piece` int DEFAULT NULL,
  PRIMARY KEY (`id_inv_issue`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_billing_payment`
--

DROP TABLE IF EXISTS `ret_billing_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_billing_payment` (
  `id_billing_payment` int NOT NULL AUTO_INCREMENT,
  `bill_id` int NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Credit, 2 - Debit',
  `id_bank` int DEFAULT NULL,
  `NB_type` int DEFAULT NULL COMMENT '1-RTGS,2-IMPS,3-UPI',
  `card_type` int DEFAULT NULL COMMENT '1-RUPAY, 2-VISA,3-Mastro,4-Master',
  `payment_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Sale, 2 - Advance',
  `payment_date` datetime DEFAULT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `payment_amount` decimal(10,0) DEFAULT NULL,
  `net_banking_date` date DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `remark` text,
  `id_pay_device` int DEFAULT NULL COMMENT 'From Payment device Master',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_billing_payment`),
  KEY `bill_id` (`bill_id`),
  KEY `type` (`type`),
  KEY `istransfered` (`istransfered`),
  KEY `payment_status` (`payment_status`),
  KEY `id_pay_device` (`id_pay_device`),
  KEY `payment_for` (`payment_for`),
  KEY `payment_mode` (`payment_mode`),
  KEY `id_bank` (`id_bank`),
  KEY `NB_type` (`NB_type`),
  KEY `payment_date` (`payment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3896 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_branch_floor`
--

DROP TABLE IF EXISTS `ret_branch_floor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_branch_floor` (
  `floor_id` int NOT NULL AUTO_INCREMENT,
  `branch_id` int unsigned DEFAULT NULL,
  `floor_name` varchar(100) DEFAULT NULL,
  `floor_short_code` varchar(10) DEFAULT NULL,
  `floor_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `sort` int DEFAULT NULL COMMENT 'Display Order',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`floor_id`),
  KEY `branch_id` (`branch_id`),
  KEY `floor_status` (`floor_status`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_branch_floor_counter`
--

DROP TABLE IF EXISTS `ret_branch_floor_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_branch_floor_counter` (
  `counter_id` int NOT NULL AUTO_INCREMENT,
  `floor_id` int NOT NULL,
  `counter_name` varchar(100) DEFAULT NULL,
  `counter_short_code` varchar(10) DEFAULT NULL,
  `system_fp_id` varchar(250) DEFAULT NULL COMMENT 'Unique ID for counter identification generated by FingerPrintJS. ID differs, if different browsers used. ',
  `counter_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `sort` int DEFAULT NULL COMMENT 'Display Order',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`counter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_branch_transfer`
--

DROP TABLE IF EXISTS `ret_branch_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_branch_transfer` (
  `branch_transfer_id` int NOT NULL AUTO_INCREMENT,
  `is_other_issue` tinyint(1) NOT NULL DEFAULT '0',
  `added_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Through Branch Transfer, 2 - Through Tagging',
  `branch_trans_code` varchar(45) NOT NULL,
  `transfer_from_branch` int unsigned DEFAULT NULL,
  `transfer_to_branch` int unsigned DEFAULT NULL,
  `transfer_item_type` int DEFAULT NULL COMMENT '1-> Taged 2->Non taged,3-Purchase Items.4-Packaging Items',
  `id_lot_inward_detail` int DEFAULT NULL,
  `id_nontag_item` int DEFAULT NULL,
  `id_nontag_receipt` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `grs_wt` decimal(12,4) DEFAULT NULL,
  `net_wt` decimal(12,4) DEFAULT NULL,
  `create_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Yet to Approve, 2 - Approved(InTransit), 3 - Rejected, 4 - Stock Updated',
  `approved_by` int DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL COMMENT 'Date of Transit Approval [Day close date]',
  `dwnload_datetime` datetime DEFAULT NULL COMMENT 'Date of stock download [Day close date]',
  `downloaded_by` int DEFAULT NULL,
  `remark` text COMMENT 'Dev remark',
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `outistransfered` int NOT NULL DEFAULT '0',
  `outtally_guid` varchar(100) DEFAULT NULL,
  `outtally_updated_on` datetime DEFAULT NULL,
  `form_secret` varchar(100) DEFAULT NULL,
  `is_eda` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> normal transfer , 2 -> EDA transfer',
  PRIMARY KEY (`branch_transfer_id`),
  UNIQUE KEY `branch_trans_code` (`branch_trans_code`,`is_eda`) USING BTREE,
  UNIQUE KEY `form_secret` (`form_secret`),
  KEY `transfer_from_branch` (`transfer_from_branch`),
  KEY `transfer_to_branch` (`transfer_to_branch`),
  KEY `transfer_item_type` (`transfer_item_type`),
  KEY `status` (`status`),
  KEY `istransfered` (`istransfered`),
  KEY `created_time` (`created_time`),
  KEY `is_other_issue` (`is_other_issue`),
  KEY `added_type` (`added_type`),
  KEY `id_lot_inward_detail` (`id_lot_inward_detail`),
  KEY `id_nontag_item` (`id_nontag_item`),
  KEY `approved_by` (`approved_by`),
  KEY `outistransfered` (`outistransfered`),
  KEY `branch_transfer_id` (`branch_transfer_id`),
  KEY `id_nontag_receipt` (`id_nontag_receipt`)
) ENGINE=InnoDB AUTO_INCREMENT=5979 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_branch_transfer_other_inventory`
--

DROP TABLE IF EXISTS `ret_branch_transfer_other_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_branch_transfer_other_inventory` (
  `id_trans_other_inv` int NOT NULL AUTO_INCREMENT,
  `branch_transfer_id` int NOT NULL,
  `id_other_inv_item` int NOT NULL,
  `no_of_pcs` int DEFAULT NULL,
  PRIMARY KEY (`id_trans_other_inv`),
  KEY `branch_transfer_id` (`branch_transfer_id`),
  KEY `id_other_inv_item` (`id_other_inv_item`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_brch_transfer_lot_nontag_items`
--

DROP TABLE IF EXISTS `ret_brch_transfer_lot_nontag_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_brch_transfer_lot_nontag_items` (
  `id_lot_nontag` int NOT NULL AUTO_INCREMENT,
  `transfer_id` int NOT NULL,
  `id_product` int NOT NULL,
  `id_design` int NOT NULL,
  `id_sub_design` int NOT NULL,
  `piece` int DEFAULT NULL,
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id_lot_nontag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_brch_transfer_non_tag_items`
--

DROP TABLE IF EXISTS `ret_brch_transfer_non_tag_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_brch_transfer_non_tag_items` (
  `nontag_transfer_id` int NOT NULL AUTO_INCREMENT,
  `transfer_id` int DEFAULT NULL,
  `id_nontag_item` int DEFAULT NULL,
  `id_lot_inward_detail` int DEFAULT NULL,
  `id_nontag_receipt` int DEFAULT NULL,
  `grs_wt` decimal(10,3) DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `net_wt` decimal(10,3) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`nontag_transfer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_brch_transfer_old_metal`
--

DROP TABLE IF EXISTS `ret_brch_transfer_old_metal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_brch_transfer_old_metal` (
  `id_old_metal_transfer` int NOT NULL AUTO_INCREMENT,
  `transfer_id` int DEFAULT NULL,
  `item_type` tinyint DEFAULT NULL COMMENT '1-Old Metal,2-Sales Return,3-Partly Sale,4-Non Tag Sales return',
  `is_non_tag` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'For Sales return',
  `tag_id` int DEFAULT NULL COMMENT 'sales return tag',
  `old_metal_sale_id` int DEFAULT NULL,
  `sold_bill_det_id` int DEFAULT NULL,
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id_old_metal_transfer`),
  KEY `transfer_id` (`transfer_id`),
  KEY `item_type` (`item_type`),
  KEY `is_non_tag` (`is_non_tag`),
  KEY `tag_id` (`tag_id`),
  KEY `old_metal_sale_id` (`old_metal_sale_id`),
  KEY `sold_bill_det_id` (`sold_bill_det_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8654 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_brch_transfer_tag_items`
--

DROP TABLE IF EXISTS `ret_brch_transfer_tag_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_brch_transfer_tag_items` (
  `tag_transfer_id` int NOT NULL AUTO_INCREMENT,
  `transfer_id` int DEFAULT NULL,
  `tag_id` int DEFAULT NULL,
  `id_lot_inward_detail` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `download_date` datetime DEFAULT NULL,
  `download_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `tag_remark` text,
  PRIMARY KEY (`tag_transfer_id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `tag_id` (`tag_id`),
  KEY `id_lot_inward_detail` (`id_lot_inward_detail`)
) ENGINE=InnoDB AUTO_INCREMENT=50751 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bt_order_log`
--

DROP TABLE IF EXISTS `ret_bt_order_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bt_order_log` (
  `id_order_transfer` int NOT NULL AUTO_INCREMENT,
  `branch_transfer_id` int NOT NULL,
  `date` date NOT NULL,
  `id_orderdetails` int NOT NULL,
  `status` int DEFAULT NULL COMMENT '1 - Open, 2 - In transit, 3 - Downloaded',
  `from_branch` int DEFAULT NULL,
  `to_branch` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id_order_transfer`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_bulk_tag_edit_log`
--

DROP TABLE IF EXISTS `ret_bulk_tag_edit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_bulk_tag_edit_log` (
  `edit_log_id` int NOT NULL AUTO_INCREMENT,
  `edit_datetime` datetime NOT NULL,
  `edit_tag` int NOT NULL,
  `edit_branch_id` int NOT NULL,
  `edit_emp_id` int unsigned NOT NULL,
  `edit_field` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_values` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_values` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`edit_log_id`),
  KEY `FKTagId` (`edit_tag`),
  KEY `FKEmployeeId` (`edit_emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=279 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_cash_collection`
--

DROP TABLE IF EXISTS `ret_cash_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_cash_collection` (
  `cash_collection_id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `branch_id` int NOT NULL,
  `counter_id` int DEFAULT NULL,
  `cash_type` int DEFAULT NULL COMMENT '1 - CRM, 2 - Retail, 3 - Both',
  `sales_amount` decimal(10,2) DEFAULT NULL,
  `cash_on_hand` decimal(10,2) DEFAULT NULL,
  `opening_balance` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`cash_collection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_cash_collection_details`
--

DROP TABLE IF EXISTS `ret_cash_collection_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_cash_collection_details` (
  `cash_collection_details_id` int NOT NULL AUTO_INCREMENT,
  `cash_collection_id` int NOT NULL,
  `denomination_id` int NOT NULL,
  `value` int DEFAULT NULL,
  `amount` int DEFAULT NULL,
  PRIMARY KEY (`cash_collection_details_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_cash_transition`
--

DROP TABLE IF EXISTS `ret_cash_transition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_cash_transition` (
  `id_transfer` int NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL,
  `from_branch` int DEFAULT NULL,
  `to_branch` int DEFAULT NULL,
  `ref_no` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_transfer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_cash_transition_details`
--

DROP TABLE IF EXISTS `ret_cash_transition_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_cash_transition_details` (
  `id_cash_details` int NOT NULL AUTO_INCREMENT,
  `transition_id` int NOT NULL,
  `denomination_id` int NOT NULL,
  `value` int DEFAULT NULL,
  `amount` int DEFAULT NULL,
  PRIMARY KEY (`id_cash_details`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_category`
--

DROP TABLE IF EXISTS `ret_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_category` (
  `id_ret_category` int unsigned NOT NULL AUTO_INCREMENT,
  `id_metal` int DEFAULT NULL,
  `tgrp_id` int DEFAULT NULL,
  `sort` int DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `cat_type` int DEFAULT NULL COMMENT 'Category type for filtering 1 -> Ornament, 2-> Bullion, 3 -> Stone, 4 -> Alloy',
  `hsn_code` varchar(45) DEFAULT NULL,
  `cat_code` varchar(10) DEFAULT NULL,
  `description` text,
  `is_multimetal` tinyint(1) NOT NULL DEFAULT '0',
  `is_multi_metal_cateory` int NOT NULL DEFAULT '0' COMMENT 'Whether need to show this category on creation on multi metal',
  `tally_qty_type` int NOT NULL DEFAULT '0' COMMENT '0-> Both 1-> PCS 2->Weight	',
  `scheme_closure_benefit` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `image` varchar(120) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL COMMENT 'id_emp',
  `is_916` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-yes,0-No',
  PRIMARY KEY (`id_ret_category`),
  UNIQUE KEY `name` (`name`),
  KEY `id_metal` (`id_metal`),
  KEY `tgrp_id` (`tgrp_id`),
  KEY `cat_type` (`cat_type`),
  KEY `status` (`status`),
  KEY `is_multimetal` (`is_multimetal`)
) ENGINE=InnoDB AUTO_INCREMENT=335 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_charges`
--

DROP TABLE IF EXISTS `ret_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_charges` (
  `id_charge` int NOT NULL AUTO_INCREMENT,
  `code_charge` varchar(45) DEFAULT NULL,
  `name_charge` varchar(45) DEFAULT NULL,
  `description_charge` varchar(245) DEFAULT NULL,
  `value_charge` decimal(12,2) NOT NULL DEFAULT '0.00',
  `charge_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tag_display` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_charge`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_clarity`
--

DROP TABLE IF EXISTS `ret_clarity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_clarity` (
  `id_clarity` int unsigned NOT NULL AUTO_INCREMENT,
  `clarity` varchar(45) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '0 -inactive , 1 -active',
  `description` varchar(45) DEFAULT NULL,
  `id_employee` int unsigned NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_clarity`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_collection_master`
--

DROP TABLE IF EXISTS `ret_collection_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_collection_master` (
  `id_collection` int NOT NULL AUTO_INCREMENT,
  `collection_name` varchar(30) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_collection`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_color`
--

DROP TABLE IF EXISTS `ret_color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_color` (
  `id_color` int unsigned NOT NULL AUTO_INCREMENT,
  `color` varchar(45) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1-active',
  `description` varchar(45) DEFAULT NULL,
  `id_employee` int unsigned NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_color`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_cost_taging`
--

DROP TABLE IF EXISTS `ret_cost_taging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_cost_taging` (
  `New Tag No` varchar(13) DEFAULT NULL,
  `Old Tag No` varchar(12) DEFAULT NULL,
  `Sales Cost` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_cover_up`
--

DROP TABLE IF EXISTS `ret_cover_up`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_cover_up` (
  `id_coverup` int NOT NULL AUTO_INCREMENT,
  `id_metal` int DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id_coverup`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_crdr_note`
--

DROP TABLE IF EXISTS `ret_crdr_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_crdr_note` (
  `crdrid` int NOT NULL AUTO_INCREMENT,
  `supid` int NOT NULL,
  `po_id` int NOT NULL,
  `id_supplier_rate_cut` int NOT NULL,
  `transbillno` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accountto` int NOT NULL,
  `transtype` int NOT NULL DEFAULT '1' COMMENT '1-> Cr 2->Dr',
  `transamount` decimal(10,2) DEFAULT NULL,
  `weight` double(12,3) NOT NULL,
  `naration` varchar(600) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdby` int DEFAULT NULL,
  `createdon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedby` int DEFAULT NULL,
  `updatedon` datetime DEFAULT NULL,
  `crdr_status` int NOT NULL DEFAULT '1' COMMENT '	1-Success, 2-Failed	',
  `cancel_reason` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `cancelled_by` int DEFAULT NULL,
  PRIMARY KEY (`crdrid`),
  KEY `IdxcrdrSupId` (`supid`),
  KEY `transtype` (`transtype`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_customer_company_users`
--

DROP TABLE IF EXISTS `ret_customer_company_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_customer_company_users` (
  `id_cmp_emp` int NOT NULL AUTO_INCREMENT,
  `id_customer` int NOT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` int DEFAULT NULL,
  PRIMARY KEY (`id_cmp_emp`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_customer_review`
--

DROP TABLE IF EXISTS `ret_customer_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_customer_review` (
  `id_customer_review` int NOT NULL AUTO_INCREMENT,
  `branch_id` int unsigned DEFAULT NULL,
  `cus_id` int unsigned NOT NULL,
  `esti_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `review` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suggestion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int unsigned NOT NULL,
  PRIMARY KEY (`id_customer_review`),
  KEY `fk_cr_esti_id` (`esti_id`),
  KEY `fk_cr_cus_id` (`cus_id`),
  KEY `fk_cr_branch_id` (`branch_id`),
  KEY `fk_cr_emp_id` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_cut`
--

DROP TABLE IF EXISTS `ret_cut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_cut` (
  `id_cut` int unsigned NOT NULL AUTO_INCREMENT,
  `cut` varchar(45) NOT NULL,
  `description` varchar(45) DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1-active',
  `id_employee` int unsigned NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cut`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_day_closing`
--

DROP TABLE IF EXISTS `ret_day_closing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_day_closing` (
  `id_day_closing` int NOT NULL AUTO_INCREMENT,
  `id_branch` int unsigned DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Auto, 2 - Manual',
  `day_close_time` time NOT NULL,
  `is_day_closed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No',
  `entry_date` date NOT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_day_closing`),
  UNIQUE KEY `id_branch` (`id_branch`) USING BTREE,
  KEY `created_by` (`created_by`),
  KEY `type` (`type`),
  KEY `is_day_closed` (`is_day_closed`),
  KEY `entry_date` (`entry_date`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_day_closing_log`
--

DROP TABLE IF EXISTS `ret_day_closing_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_day_closing_log` (
  `day_closing_log_id` int NOT NULL AUTO_INCREMENT,
  `id_branch` int unsigned DEFAULT NULL,
  `stock_type` int DEFAULT NULL COMMENT '1-Tagged Items , 2-Non Tagged Items',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Auto, 2 - Manual',
  `date` date NOT NULL,
  `service` varchar(100) NOT NULL,
  `records` int NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `created_by` int unsigned DEFAULT NULL,
  PRIMARY KEY (`day_closing_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2999 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_attributes`
--

DROP TABLE IF EXISTS `ret_design_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_attributes` (
  `attr_des_id` int NOT NULL AUTO_INCREMENT,
  `id_sub_design_mapping` int NOT NULL,
  `attr_id` int NOT NULL,
  `attr_val_id` int NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`attr_des_id`),
  KEY `sub_design_map_id` (`id_sub_design_mapping`),
  KEY `attribute_value_id` (`attr_val_id`),
  KEY `attr_id` (`attr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1048 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_images`
--

DROP TABLE IF EXISTS `ret_design_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_images` (
  `id_image` int unsigned NOT NULL,
  `id_design` int DEFAULT NULL,
  `image` text,
  `date_add` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_default` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_karigars`
--

DROP TABLE IF EXISTS `ret_design_karigars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_karigars` (
  `des_kar_id` int NOT NULL,
  `design_id` int DEFAULT NULL,
  `karigar_id` int unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_mapping_images`
--

DROP TABLE IF EXISTS `ret_design_mapping_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_mapping_images` (
  `id_desmap_img` int NOT NULL AUTO_INCREMENT,
  `id_design_mapping` int DEFAULT NULL,
  `image_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_desmap_img`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_master`
--

DROP TABLE IF EXISTS `ret_design_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_master` (
  `design_no` int NOT NULL AUTO_INCREMENT,
  `design_code` varchar(5) DEFAULT NULL,
  `design_name` varchar(75) DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `theme` int DEFAULT NULL,
  `hook_type` int DEFAULT NULL,
  `screw_type` int DEFAULT NULL,
  `design_for` int DEFAULT NULL COMMENT '1->Male 2-> Female 3-> Unisex 4-> Kids',
  `mc_cal_type` int NOT NULL DEFAULT '1' COMMENT '1-> Per Pc 2->Per Grm 3-> % on price',
  `mc_cal_value` decimal(10,2) DEFAULT '0.00',
  `wastage_type` int DEFAULT '1' COMMENT '1-> Fixed 2-> Base on Range Minimum wastage and  Max wastage',
  `wastag_value` decimal(10,2) DEFAULT '0.00',
  `min_length` decimal(10,2) DEFAULT NULL,
  `max_length` decimal(10,2) DEFAULT NULL,
  `min_width` decimal(10,2) DEFAULT NULL,
  `max_width` decimal(10,2) DEFAULT NULL,
  `min_dia` decimal(10,2) DEFAULT NULL,
  `max_dia` decimal(10,2) DEFAULT NULL,
  `min_weight` decimal(10,2) DEFAULT NULL,
  `max_weight` decimal(10,2) DEFAULT NULL,
  `fixed_rate` int DEFAULT NULL,
  `id_size` int DEFAULT NULL,
  `usage_type` int DEFAULT NULL COMMENT '1->Regular 2-> Function',
  `design_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Active , 0 - Inactive',
  `create_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`design_no`),
  UNIQUE KEY `design_name` (`design_name`),
  UNIQUE KEY `design_code` (`design_code`),
  KEY `Idx_Mcfixtype` (`mc_cal_type`),
  KEY `wastage_type` (`wastage_type`),
  KEY `product_id` (`product_id`),
  KEY `id_size` (`id_size`),
  KEY `design_status` (`design_status`)
) ENGINE=InnoDB AUTO_INCREMENT=503 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_other_materials`
--

DROP TABLE IF EXISTS `ret_design_other_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_other_materials` (
  `other_material_id` int NOT NULL,
  `design_id` int DEFAULT NULL,
  `material_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_purity`
--

DROP TABLE IF EXISTS `ret_design_purity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_purity` (
  `des_pur_id` int NOT NULL,
  `design_id` int DEFAULT NULL,
  `pur_id` int unsigned DEFAULT NULL COMMENT 'From purity master'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_sizes`
--

DROP TABLE IF EXISTS `ret_design_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_sizes` (
  `design_size_id` int NOT NULL,
  `design_id` int DEFAULT NULL,
  `size` decimal(10,2) DEFAULT NULL,
  `uom_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_stone`
--

DROP TABLE IF EXISTS `ret_design_stone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_stone` (
  `des_stone_id` int unsigned NOT NULL,
  `design_id` int DEFAULT NULL,
  `stone_id` int DEFAULT NULL,
  `stone_pcs` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_design_weight_range_wc`
--

DROP TABLE IF EXISTS `ret_design_weight_range_wc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_design_weight_range_wc` (
  `id_wc` int NOT NULL AUTO_INCREMENT,
  `id_sub_design_mapping` int DEFAULT NULL,
  `id_selling_settings` int DEFAULT NULL,
  `wc_from_weight` decimal(10,3) DEFAULT NULL,
  `wc_to_weight` decimal(10,3) DEFAULT NULL,
  `wc_percent` decimal(10,3) DEFAULT NULL,
  `wc_wt` decimal(12,3) DEFAULT '0.000' COMMENT 'wastage wt will be given based on wastage method',
  `mc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Per Piece,2-Per Gram',
  `mc` decimal(10,3) NOT NULL,
  `mcrg_min` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Min value for mc if value is editable ',
  `mcrg_max` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Max value for mc if value is editable ',
  `wc_method` tinyint(1) DEFAULT '1' COMMENT '1 -> Percentage based, 2-> Weight based',
  `wc_min` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Min value for wastage if value is editable',
  `wc_min_wt` decimal(12,3) DEFAULT '0.000',
  `wc_max` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Max value for wastage if value is editable',
  PRIMARY KEY (`id_wc`)
) ENGINE=MyISAM AUTO_INCREMENT=602 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_diamond_cent_rates`
--

DROP TABLE IF EXISTS `ret_diamond_cent_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_diamond_cent_rates` (
  `id_cents_rate` int NOT NULL AUTO_INCREMENT,
  `from_cent` decimal(12,3) NOT NULL,
  `to_cent` decimal(12,3) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `updated_on` datetime(6) NOT NULL,
  `id_rate_id` int NOT NULL,
  `updated_by_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id_cents_rate`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_diamond_rate`
--

DROP TABLE IF EXISTS `ret_diamond_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_diamond_rate` (
  `rate_id` int NOT NULL AUTO_INCREMENT,
  `effective_date` date NOT NULL,
  `created_on` datetime(6) NOT NULL,
  `updated_on` datetime(6) DEFAULT NULL,
  `created_by_id` bigint NOT NULL,
  `quality_code_id` int NOT NULL,
  `rate_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No',
  `updated_by_id` bigint DEFAULT NULL,
  PRIMARY KEY (`rate_id`),
  UNIQUE KEY `quality_code_id` (`quality_code_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_duplicate_print_log`
--

DROP TABLE IF EXISTS `ret_duplicate_print_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_duplicate_print_log` (
  `id_duplicate_print` int NOT NULL AUTO_INCREMENT,
  `tag_id` int DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `printer_name` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_duplicate_print`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_est_chit_utilization`
--

DROP TABLE IF EXISTS `ret_est_chit_utilization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_est_chit_utilization` (
  `chit_ut_id` int NOT NULL AUTO_INCREMENT,
  `est_id` int DEFAULT NULL,
  `scheme_account_id` int unsigned DEFAULT NULL,
  `utl_amount` decimal(12,2) DEFAULT NULL,
  `closing_weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `wastage_per` int DEFAULT NULL,
  `savings_in_wastage` decimal(10,3) NOT NULL DEFAULT '0.000',
  `mc_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `savings_in_making_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  UNIQUE KEY `chit_ut_id` (`chit_ut_id`),
  KEY `est_id` (`est_id`),
  KEY `scheme_account_id` (`scheme_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=411 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_est_gift_voucher_details`
--

DROP TABLE IF EXISTS `ret_est_gift_voucher_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_est_gift_voucher_details` (
  `gift_voucher_id` int NOT NULL,
  `est_id` int DEFAULT NULL,
  `voucher_no` varchar(45) DEFAULT NULL,
  `gift_voucher_details` varchar(365) DEFAULT NULL,
  `gift_voucher_amt` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_est_other_metals`
--

DROP TABLE IF EXISTS `ret_est_other_metals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_est_other_metals` (
  `est_other_itm_id` int NOT NULL AUTO_INCREMENT,
  `est_item_id` int NOT NULL,
  `tag_other_itm_metal_id` int NOT NULL,
  `tag_other_itm_pur_id` int DEFAULT NULL,
  `tag_other_itm_grs_weight` decimal(10,3) NOT NULL,
  `tag_other_itm_wastage` decimal(10,2) DEFAULT NULL,
  `tag_other_itm_uom` int NOT NULL,
  `tag_other_itm_cal_type` int NOT NULL COMMENT '1-> Weight 2->pcs',
  `tag_other_itm_mc` decimal(10,2) NOT NULL,
  `tag_other_itm_rate` decimal(10,2) NOT NULL,
  `tag_other_itm_pcs` int NOT NULL,
  `tag_other_itm_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`est_other_itm_id`),
  KEY `est_item_id` (`est_item_id`),
  KEY `tag_other_itm_metal_id` (`tag_other_itm_metal_id`),
  KEY `tag_other_itm_pur_id` (`tag_other_itm_pur_id`),
  KEY `tag_other_itm_uom` (`tag_other_itm_uom`),
  KEY `tag_other_itm_cal_type` (`tag_other_itm_cal_type`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_est_sales_return_utilization`
--

DROP TABLE IF EXISTS `ret_est_sales_return_utilization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_est_sales_return_utilization` (
  `sr_ut_id` int NOT NULL AUTO_INCREMENT,
  `est_id` int DEFAULT NULL,
  `bill_id` int DEFAULT NULL,
  `bill_det_id` int DEFAULT NULL,
  `esti_date` date DEFAULT NULL,
  PRIMARY KEY (`sr_ut_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_est_tag_merge`
--

DROP TABLE IF EXISTS `ret_est_tag_merge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_est_tag_merge` (
  `id_est_tag_merge` int NOT NULL AUTO_INCREMENT,
  `est_item_id` int NOT NULL,
  `ref_est_item_id` int NOT NULL,
  PRIMARY KEY (`id_est_tag_merge`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_esti_old_metal_stone_details`
--

DROP TABLE IF EXISTS `ret_esti_old_metal_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_esti_old_metal_stone_details` (
  `est_old_metal_stone_id` int NOT NULL AUTO_INCREMENT,
  `est_id` int NOT NULL,
  `est_item_id` int DEFAULT NULL,
  `est_old_metal_sale_id` int NOT NULL,
  `stone_id` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `wt` decimal(12,4) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `is_apply_in_lwt` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Stone wt applied in LWT=>1-Yes,0-No',
  `stone_cal_type` tinyint NOT NULL DEFAULT '1' COMMENT '1-> By weight 2->By pcs',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `uom_id` int DEFAULT NULL,
  PRIMARY KEY (`est_old_metal_stone_id`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_estimation`
--

DROP TABLE IF EXISTS `ret_estimation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_estimation` (
  `estimation_id` int NOT NULL AUTO_INCREMENT,
  `fin_year_code` varchar(15) DEFAULT NULL,
  `esti_no` int NOT NULL,
  `esti_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Customer, 2 - Branch Transfer,3-Company',
  `estimation_datetime` datetime DEFAULT NULL,
  `est_date` date DEFAULT NULL,
  `cus_id` int unsigned DEFAULT NULL,
  `old_cus_id` int unsigned DEFAULT NULL COMMENT 'Used during CRM,Retail merge',
  `mobile` varchar(45) DEFAULT NULL COMMENT 'Used during CRM,Retail merge',
  `created_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_time` datetime DEFAULT NULL,
  `has_converted_order` int DEFAULT NULL COMMENT '1-> Yes 0-> No',
  `discount` decimal(10,2) DEFAULT NULL,
  `gift_voucher_amt` decimal(10,2) DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `id_other_item` int DEFAULT NULL,
  `no_of_pcs` int NOT NULL DEFAULT '0',
  `added_through` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Admin , 2- Estimation app',
  `is_eda` int NOT NULL DEFAULT '0' COMMENT '	0-> No need approval 1-> Need approval on discount	',
  `is_eda_approved` int NOT NULL DEFAULT '0' COMMENT '0 -> Not Approved, 1 -> Approved, 2 -> Rejected',
  `estimate_final_amt` decimal(10,2) unsigned DEFAULT NULL COMMENT 'Final value after discount for EDA',
  `goldrate_22ct` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goldrate_18ct` int DEFAULT NULL,
  `silverrate_1gm` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goldrate_9ct` decimal(10,2) DEFAULT NULL,
  `goldrate_14ct` decimal(10,2) DEFAULT NULL,
  `form_secret` varchar(100) DEFAULT NULL,
  `estbillid` int DEFAULT NULL,
  `created_through` tinyint(1) NOT NULL DEFAULT '0' COMMENT '	1 -> Through Web, 2 ->Through App',
  `disc_per` int DEFAULT NULL,
  `bulk_was_disc_per` int DEFAULT NULL,
  `manual_rate` tinyint NOT NULL DEFAULT '0' COMMENT '0 -> unchecked 1 -> checked',
  PRIMARY KEY (`estimation_id`),
  UNIQUE KEY `est_no` (`esti_no`,`est_date`,`id_branch`),
  UNIQUE KEY `form_secret` (`form_secret`),
  KEY `id_branch` (`id_branch`),
  KEY `estimation_datetime` (`estimation_datetime`),
  KEY `cus_id` (`cus_id`),
  KEY `Idxiseda` (`is_eda`),
  KEY `fin_year_code` (`fin_year_code`),
  KEY `esti_for` (`esti_for`),
  KEY `added_through` (`added_through`),
  KEY `has_converted_order` (`has_converted_order`),
  KEY `is_eda_approved` (`is_eda_approved`)
) ENGINE=InnoDB AUTO_INCREMENT=5851 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_estimation_item_other_materials`
--

DROP TABLE IF EXISTS `ret_estimation_item_other_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_estimation_item_other_materials` (
  `est_other_material_id` int NOT NULL AUTO_INCREMENT,
  `est_id` int DEFAULT NULL,
  `est_item_id` int NOT NULL,
  `material_id` int DEFAULT NULL,
  `wt` decimal(14,4) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`est_other_material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_estimation_item_stones`
--

DROP TABLE IF EXISTS `ret_estimation_item_stones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_estimation_item_stones` (
  `est_item_stone_id` int NOT NULL AUTO_INCREMENT,
  `est_id` int DEFAULT NULL,
  `est_item_id` int NOT NULL,
  `stone_id` int DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `wt` decimal(12,4) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `is_apply_in_lwt` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Stone wt applied in LWT=>1-Yes,0-No',
  `stone_cal_type` tinyint NOT NULL DEFAULT '1' COMMENT '1-> By weight 2->By pcs',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quality_id` int DEFAULT NULL,
  `max_stn_amt` decimal(10,2) DEFAULT NULL COMMENT 'Stone amount before stone discount applied.',
  PRIMARY KEY (`est_item_stone_id`),
  KEY `est_id` (`est_id`),
  KEY `est_item_id` (`est_item_id`),
  KEY `stone_id` (`stone_id`),
  KEY `is_apply_in_lwt` (`is_apply_in_lwt`),
  KEY `stone_cal_type` (`stone_cal_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2765 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_estimation_items`
--

DROP TABLE IF EXISTS `ret_estimation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_estimation_items` (
  `est_item_id` int NOT NULL AUTO_INCREMENT,
  `esti_id` int DEFAULT NULL,
  `item_type` int DEFAULT NULL COMMENT '0-> Tag 1->Catalog 2-> Custom',
  `product_id` int DEFAULT NULL,
  `tag_id` int DEFAULT NULL,
  `item_emp_id` int DEFAULT NULL,
  `tag_remark` varchar(50) DEFAULT NULL,
  `search_field` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> Tag Code, 2 -> Old Tag Id',
  `orderno` varchar(20) DEFAULT NULL,
  `id_orderdetails` int DEFAULT NULL,
  `design_id` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `purity` int DEFAULT NULL,
  `size` decimal(10,2) DEFAULT NULL,
  `uom` int DEFAULT NULL,
  `piece` int DEFAULT NULL,
  `less_wt` decimal(12,3) DEFAULT NULL,
  `net_wt` decimal(12,3) DEFAULT NULL,
  `gross_wt` decimal(12,3) DEFAULT NULL,
  `calculation_based_on` tinyint(1) DEFAULT '2' COMMENT '0-> Mc & Wast On Gross, 1 -> Mc & Wast On Net, 2 ->Mc on Gross,Wast On Net, 3-> Fixed Rate, 4-> Fixed Rate based on Weight',
  `wastage_percent` decimal(10,2) DEFAULT NULL,
  `max_va_per` int DEFAULT NULL,
  `max_VA` int DEFAULT NULL,
  `max_mc` int DEFAULT NULL,
  `mc_value` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,0) DEFAULT NULL,
  `item_cost` decimal(10,2) DEFAULT NULL,
  `est_rate_per_grm` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_partial` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Partly Sale',
  `mc_type` int DEFAULT NULL COMMENT '1-> Per Pc 2->Per Grm 3-> % on price',
  `tax_group_id` int NOT NULL DEFAULT '1',
  `item_total_tax` decimal(10,2) DEFAULT NULL COMMENT 'Total Tax',
  `market_rate_cost` decimal(10,2) DEFAULT NULL COMMENT 'Market Rate Cost',
  `market_rate_tax` decimal(10,2) DEFAULT NULL COMMENT 'Market Rate Tax',
  `is_non_tag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-tagged items,1-Non_tagged items',
  `lot_no` int DEFAULT NULL,
  `purchase_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Purchased, 2 - Returned',
  `bil_detail_id` int DEFAULT NULL,
  `id_division` int NOT NULL DEFAULT '0',
  `id_collecion_maping_det` int DEFAULT NULL,
  `esti_purchase_cost` decimal(12,2) DEFAULT NULL COMMENT 'Purchase cost from tag and non tag',
  `istag_merged` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `isTagsplitted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'tag split for weight scheme closure',
  `is_split_row` int NOT NULL DEFAULT '0' COMMENT ' 0=> normal 1=> is_split_row',
  `wastage_slab_id` int DEFAULT NULL,
  `tag_blk_disc` tinyint NOT NULL DEFAULT '0' COMMENT '0 -> Bulk disc is not applied\r\n1 -> Bulk disc applied.',
  `act_wast_per` int NOT NULL DEFAULT '0',
  `stone_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `diamond_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `wast_slab_value` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`est_item_id`),
  KEY `esti_id` (`esti_id`),
  KEY `item_type` (`item_type`),
  KEY `product_id` (`product_id`),
  KEY `tag_id` (`tag_id`),
  KEY `id_orderdetails` (`id_orderdetails`),
  KEY `design_id` (`design_id`),
  KEY `id_sub_design` (`id_sub_design`),
  KEY `purity` (`purity`),
  KEY `calculation_based_on` (`calculation_based_on`),
  KEY `is_partial` (`is_partial`),
  KEY `mc_type` (`mc_type`),
  KEY `purchase_status` (`purchase_status`),
  KEY `tax_group_id` (`tax_group_id`),
  KEY `bil_detail_id` (`bil_detail_id`),
  KEY `is_non_tag` (`is_non_tag`)
) ENGINE=InnoDB AUTO_INCREMENT=6848 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_estimation_old_metal_sale_details`
--

DROP TABLE IF EXISTS `ret_estimation_old_metal_sale_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_estimation_old_metal_sale_details` (
  `old_metal_sale_id` int NOT NULL AUTO_INCREMENT,
  `est_id` int DEFAULT NULL,
  `id_old_metal_type` int DEFAULT NULL,
  `id_old_metal_category` int DEFAULT NULL,
  `id_category` int DEFAULT NULL COMMENT 'metal type like Gold , SIlver',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Melting , 2 - Re-tag',
  `item_type` int DEFAULT NULL COMMENT '1->ornament 2-> coin 3-> bar',
  `piece` int unsigned NOT NULL DEFAULT '1',
  `gross_wt` decimal(14,3) unsigned DEFAULT NULL,
  `net_wt` decimal(14,3) unsigned DEFAULT NULL,
  `stone_wt` decimal(14,3) unsigned DEFAULT NULL,
  `dust_wt` decimal(14,3) DEFAULT NULL,
  `touch` decimal(10,2) NOT NULL DEFAULT '100.00',
  `purity` decimal(10,2) unsigned DEFAULT NULL,
  `wastage_percent` decimal(10,2) DEFAULT NULL,
  `wastage_wt` decimal(14,3) DEFAULT NULL,
  `rate_per_gram` decimal(10,2) unsigned DEFAULT NULL COMMENT 'metal rate per gram',
  `amount` decimal(10,2) unsigned DEFAULT NULL,
  `purpose` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Cash, 2-Exchange',
  `bill_id` int DEFAULT NULL,
  `purchase_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1 - Purchased,2-Deposited in Chit,3-Advance Deposit',
  `tally_guid` varchar(100) DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_updated_on` datetime DEFAULT NULL,
  `remark` text,
  `old_metal_prod_id` int DEFAULT NULL,
  `old_stone_set` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`old_metal_sale_id`),
  KEY `est_id` (`est_id`),
  KEY `id_old_metal_type` (`id_old_metal_type`),
  KEY `id_old_metal_category` (`id_old_metal_category`),
  KEY `id_category` (`id_category`),
  KEY `type` (`type`),
  KEY `item_type` (`item_type`),
  KEY `bill_id` (`bill_id`),
  KEY `purchase_status` (`purchase_status`),
  KEY `istransfered` (`istransfered`)
) ENGINE=InnoDB AUTO_INCREMENT=2333 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_estimation_other_charges`
--

DROP TABLE IF EXISTS `ret_estimation_other_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_estimation_other_charges` (
  `id_est_charge` int NOT NULL AUTO_INCREMENT,
  `est_item_id` int DEFAULT NULL,
  `id_charge` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_est_charge`),
  KEY `est_item_id` (`est_item_id`),
  KEY `id_charge` (`id_charge`)
) ENGINE=InnoDB AUTO_INCREMENT=3535 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_estimation_other_inventory_issue`
--

DROP TABLE IF EXISTS `ret_estimation_other_inventory_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_estimation_other_inventory_issue` (
  `id_inv_issue` int NOT NULL AUTO_INCREMENT,
  `esti_id` int NOT NULL,
  `id_other_item` int NOT NULL,
  `no_of_piece` int DEFAULT NULL,
  PRIMARY KEY (`id_inv_issue`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_financial_year`
--

DROP TABLE IF EXISTS `ret_financial_year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_financial_year` (
  `fin_id` int NOT NULL AUTO_INCREMENT,
  `fin_year_name` varchar(10) DEFAULT NULL,
  `fin_year_code` varchar(15) DEFAULT NULL,
  `fin_year_from` datetime DEFAULT NULL,
  `fin_year_to` datetime DEFAULT NULL,
  `fin_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`fin_id`),
  KEY `fin_year_from` (`fin_year_from`),
  KEY `fin_year_to` (`fin_year_to`),
  KEY `fin_status` (`fin_status`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_gift_issue_redeem_prod`
--

DROP TABLE IF EXISTS `ret_gift_issue_redeem_prod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_gift_issue_redeem_prod` (
  `id_prod_set` int NOT NULL,
  `id_gift_voucher` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `issue` tinyint(1) DEFAULT '0' COMMENT '1-Yes,0-No',
  `utilize` tinyint(1) DEFAULT '0' COMMENT '1-Yes,0-No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_gift_master_redeem_prod`
--

DROP TABLE IF EXISTS `ret_gift_master_redeem_prod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_gift_master_redeem_prod` (
  `id_prod_set` int NOT NULL,
  `id_gift_voucher` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `issue` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `utilize` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_gift_voucher_master`
--

DROP TABLE IF EXISTS `ret_gift_voucher_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_gift_voucher_master` (
  `id_gift_voucher` int NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `voucher_type` tinyint(1) DEFAULT '1' COMMENT '1- Amt  2-Weight ',
  `utilize_for` int DEFAULT NULL COMMENT 'id metal',
  `sale_value` decimal(10,0) DEFAULT NULL,
  `credit_value` decimal(10,0) DEFAULT NULL,
  `credit_type` int DEFAULT NULL COMMENT '1 - Amount, 2 - Percentage',
  `validity_days` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `description` text,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  UNIQUE KEY `id_gift_voucher` (`id_gift_voucher`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_grn_entry`
--

DROP TABLE IF EXISTS `ret_grn_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_grn_entry` (
  `grn_id` int NOT NULL AUTO_INCREMENT,
  `grn_fin_year_code` varchar(45) DEFAULT NULL,
  `grn_karigar_id` int NOT NULL COMMENT 'Karikar Id',
  `is_aganist_order` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `id_customerorder` int DEFAULT NULL,
  `grn_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `grn_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Bill,2-Receipt,3-Charges',
  `grn_ref_no` varchar(50) DEFAULT NULL,
  `grn_supplier_ref_no` varchar(20) DEFAULT NULL,
  `grn_ref_date` date DEFAULT NULL,
  `grn_ewaybillno` varchar(60) DEFAULT NULL,
  `grn_despatch_through` int NOT NULL DEFAULT '1' COMMENT '1-> Courier 2-> Manual Delivery',
  `grn_irnno` varchar(160) DEFAULT NULL,
  `grn_other_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_purchase_amt` decimal(10,2) DEFAULT '0.00',
  `grn_purchase_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `created_by` int DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `grn_pay_tds_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_pay_tds_value` decimal(10,2) DEFAULT '0.00',
  `grn_other_charges_tds_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_other_charges_tds_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_tcs_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_tcs_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_round_off` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_bill_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Success,2-Cancelled',
  `cancel_reason` text,
  `form_secret` varchar(100) NOT NULL,
  `remarks` text,
  `purchase_type` int DEFAULT NULL COMMENT '1-stock, 2-walkin, 3-expo',
  PRIMARY KEY (`grn_id`),
  UNIQUE KEY `form_secret_2` (`form_secret`),
  KEY `grn_karigar_id` (`grn_karigar_id`),
  KEY `grn_fin_year_code` (`grn_fin_year_code`),
  KEY `grn_bill_status` (`grn_bill_status`),
  KEY `form_secret` (`form_secret`),
  KEY `grn_type` (`grn_type`),
  KEY `grn_date` (`grn_date`)
) ENGINE=MyISAM AUTO_INCREMENT=1538 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_grn_images`
--

DROP TABLE IF EXISTS `ret_grn_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_grn_images` (
  `id_grn_img` int unsigned NOT NULL AUTO_INCREMENT,
  `grn_id` int NOT NULL,
  `image` text,
  `date_add` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_default` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  PRIMARY KEY (`id_grn_img`),
  KEY `grn_id` (`grn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32173 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_grn_item_stone`
--

DROP TABLE IF EXISTS `ret_grn_item_stone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_grn_item_stone` (
  `grn_stone_id` int NOT NULL AUTO_INCREMENT,
  `grn_item_id` int DEFAULT NULL,
  `stone_id` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `wt` decimal(12,3) DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Stone Rate',
  `amount` decimal(10,2) DEFAULT NULL,
  `is_apply_in_lwt` tinyint DEFAULT '1' COMMENT 'Stone wt applied in LWT=>1-Yes,0-No',
  `stone_cal_type` int NOT NULL DEFAULT '1' COMMENT '	1-> By weight 2->By pcs',
  PRIMARY KEY (`grn_stone_id`)
) ENGINE=InnoDB AUTO_INCREMENT=655 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_grn_items`
--

DROP TABLE IF EXISTS `ret_grn_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_grn_items` (
  `grn_item_id` int NOT NULL AUTO_INCREMENT,
  `grn_item_grn_id` int NOT NULL,
  `grn_item_is_order` int NOT NULL DEFAULT '0' COMMENT '0-> No 1->Against Order',
  `grn_order_no` int DEFAULT NULL,
  `grn_item_cat_id` int DEFAULT NULL,
  `grn_gross_wt` decimal(10,3) DEFAULT NULL,
  `grn_less_wt` decimal(10,3) DEFAULT NULL,
  `grn_wastage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_net_wt` decimal(10,3) DEFAULT NULL,
  `grn_uom` int DEFAULT NULL,
  `grn_no_of_pcs` int DEFAULT NULL,
  `grn_rate_per_grm` decimal(10,2) DEFAULT NULL,
  `itemratecaltype` int NOT NULL DEFAULT '1' COMMENT '1-> Per Grm 2->Per Pcs',
  `grn_item_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_item_gst_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_item_gst_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_item_cgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_item_sgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_item_igst` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`grn_item_id`),
  KEY `grn_item_grn_id` (`grn_item_grn_id`),
  KEY `grn_item_is_order` (`grn_item_is_order`),
  KEY `grn_item_cat_id` (`grn_item_cat_id`),
  KEY `grn_uom` (`grn_uom`),
  KEY `itemratecaltype` (`itemratecaltype`)
) ENGINE=MyISAM AUTO_INCREMENT=1785 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_grn_other_charges`
--

DROP TABLE IF EXISTS `ret_grn_other_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_grn_other_charges` (
  `grn_other_charge_id` int NOT NULL AUTO_INCREMENT,
  `grn_charge_id` int DEFAULT NULL,
  `grn_id` int DEFAULT NULL,
  `grn_charge_value` decimal(12,2) DEFAULT NULL,
  `char_with_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `char_tax` decimal(10,2) DEFAULT '0.00',
  `total_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`grn_other_charge_id`),
  KEY `grn_charge_id` (`grn_charge_id`),
  KEY `grn_id` (`grn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=225 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_grn_other_metals`
--

DROP TABLE IF EXISTS `ret_grn_other_metals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_grn_other_metals` (
  `grn_other_itm_id` int NOT NULL AUTO_INCREMENT,
  `grn_itms_id` int NOT NULL,
  `grn_other_itm_metal_id` int NOT NULL COMMENT 'category id from ret_category_master',
  `grn_other_itm_pur_id` int DEFAULT NULL,
  `grn_other_itm_grs_weight` decimal(10,3) NOT NULL,
  `grn_other_itm_wastage` int DEFAULT NULL,
  `grn_other_itm_mc` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grn_other_itm_uom` int NOT NULL,
  `grn_other_itm_cal_type` int NOT NULL COMMENT '1-> Weight 2->pcs',
  `grn_other_itm_rate` decimal(10,2) NOT NULL,
  `grn_other_itm_pcs` int NOT NULL,
  `grn_other_itm_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`grn_other_itm_id`),
  KEY `grn_itms_id` (`grn_itms_id`),
  KEY `grn_other_itm_metal_id` (`grn_other_itm_metal_id`),
  KEY `grn_other_itm_pur_id` (`grn_other_itm_pur_id`),
  KEY `grn_other_itm_cal_type` (`grn_other_itm_cal_type`)
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_home_section_item`
--

DROP TABLE IF EXISTS `ret_home_section_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_home_section_item` (
  `id_hometag_item` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `no_of_piece` int DEFAULT NULL,
  `gross_wt` decimal(10,3) NOT NULL,
  `net_wt` decimal(10,3) NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_hometag_item`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_home_section_item_log`
--

DROP TABLE IF EXISTS `ret_home_section_item_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_home_section_item_log` (
  `id_home_section_itm_log` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0-> Not yet sale 1-> Sold out 3-> Other Issue, 4-> In Transit',
  `from_branch` int unsigned DEFAULT NULL,
  `to_branch` int unsigned DEFAULT NULL,
  `from_section` int unsigned DEFAULT NULL,
  `to_section` int unsigned DEFAULT NULL,
  `tag_id` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `no_of_piece` int DEFAULT NULL,
  `gross_wt` decimal(14,4) DEFAULT '0.0000',
  `net_wt` decimal(14,4) DEFAULT '0.0000',
  `less_wt` decimal(14,4) DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `form_secret` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_home_section_itm_log`),
  UNIQUE KEY `form_secret` (`form_secret`)
) ENGINE=InnoDB AUTO_INCREMENT=410 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_hook_type`
--

DROP TABLE IF EXISTS `ret_hook_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_hook_type` (
  `hook_id` int NOT NULL AUTO_INCREMENT,
  `hook_name` varchar(100) DEFAULT NULL,
  `hook_short_code` varchar(10) DEFAULT NULL,
  `hook_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`hook_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_import_charges`
--

DROP TABLE IF EXISTS `ret_import_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_import_charges` (
  `chid` int NOT NULL AUTO_INCREMENT,
  `Tag Number` varchar(12) DEFAULT NULL,
  `Charge Name` varchar(10) DEFAULT NULL,
  `Charge Value` int DEFAULT NULL,
  `chstatus` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`chid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_import_stone_details`
--

DROP TABLE IF EXISTS `ret_import_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_import_stone_details` (
  `stiid` int NOT NULL AUTO_INCREMENT,
  `Tag Number` varchar(20) DEFAULT NULL,
  `Less Wt` varchar(10) DEFAULT NULL,
  `StoneName` varchar(45) DEFAULT NULL,
  `Stone ID` int DEFAULT NULL,
  `Pieces` int DEFAULT NULL,
  `Weight` decimal(10,3) DEFAULT NULL,
  `Unit` varchar(30) DEFAULT NULL,
  `CAL TYPE` varchar(30) DEFAULT NULL,
  `Rate/Gram` varchar(20) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `ststatus` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`stiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_import_tag_details`
--

DROP TABLE IF EXISTS `ret_import_tag_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_import_tag_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `catid` int NOT NULL,
  `Category` varchar(59) DEFAULT NULL,
  `Product Name` varchar(92) DEFAULT NULL,
  `Design Name` varchar(92) DEFAULT NULL,
  `Sub Design Name` varchar(92) DEFAULT NULL,
  `Sub Design ID` int DEFAULT NULL,
  `Purity` decimal(3,1) DEFAULT NULL,
  `Pieces` int DEFAULT NULL,
  `Gross Wt` decimal(10,3) DEFAULT NULL,
  `Net Wt` decimal(10,3) DEFAULT NULL,
  `Less Wt` decimal(10,3) DEFAULT NULL,
  `Wastage Per` decimal(10,2) DEFAULT NULL,
  `MC Type` varchar(92) DEFAULT NULL,
  `MC Value` decimal(10,2) DEFAULT NULL,
  `Sales Value` decimal(10,2) DEFAULT NULL,
  `Branch` varchar(92) DEFAULT NULL,
  `MFR Code` varchar(92) DEFAULT NULL,
  `Style Code` varchar(19) DEFAULT NULL,
  `Tag Number` varchar(22) DEFAULT NULL,
  `Certification No` varchar(20) DEFAULT NULL,
  `Purchase Cost` decimal(10,2) DEFAULT NULL,
  `Tag Date` varchar(10) DEFAULT NULL,
  `HUID 1` varchar(20) DEFAULT NULL,
  `HUID 2` varchar(20) DEFAULT NULL,
  `K ID` int DEFAULT NULL,
  `KARIGAR` varchar(92) DEFAULT NULL,
  `importdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatestatus` int NOT NULL DEFAULT '0',
  `calltype` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_insurance`
--

DROP TABLE IF EXISTS `ret_insurance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_insurance` (
  `insurance_id` int NOT NULL AUTO_INCREMENT,
  `insurance_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `bill_id` int NOT NULL,
  `insurance_amount` decimal(10,2) NOT NULL,
  `policy_from` date NOT NULL,
  `policy_to` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`insurance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_issue_credit_collection_details`
--

DROP TABLE IF EXISTS `ret_issue_credit_collection_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_issue_credit_collection_details` (
  `id_credit_collection` int NOT NULL AUTO_INCREMENT,
  `id_issue_receipt` int DEFAULT NULL,
  `receipt_for` int DEFAULT NULL,
  `received_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_credit_collection`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_issue_expense_details`
--

DROP TABLE IF EXISTS `ret_issue_expense_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_issue_expense_details` (
  `id_expense_det` int NOT NULL AUTO_INCREMENT,
  `id_issue_receipt` int NOT NULL,
  `id_account_head` int NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_expense_det`)
) ENGINE=InnoDB AUTO_INCREMENT=590 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_issue_rcpt_payment`
--

DROP TABLE IF EXISTS `ret_issue_rcpt_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_issue_rcpt_payment` (
  `id_issue_rcpt_pay` int NOT NULL AUTO_INCREMENT,
  `id_issue_rcpt` int NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Credit, 2 - Debit',
  `NB_type` int DEFAULT NULL COMMENT '1-RTGS,2-IMPS',
  `card_type` int DEFAULT NULL COMMENT '1-RUPAY,2-VISA,3-Mastro,4-Master',
  `payment_date` datetime DEFAULT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `payment_amount` decimal(10,0) DEFAULT NULL,
  `net_banking_date` date DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `id_bank` int DEFAULT NULL,
  `id_pay_device` int DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `remark` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  UNIQUE KEY `id_issue_rcpt_pay` (`id_issue_rcpt_pay`),
  KEY `id_issue_rcpt` (`id_issue_rcpt`),
  KEY `type` (`type`),
  KEY `istransfered` (`istransfered`),
  KEY `payment_status` (`payment_status`),
  KEY `id_pay_device` (`id_pay_device`),
  KEY `payment_mode` (`payment_mode`),
  KEY `NB_type` (`NB_type`),
  KEY `payment_date` (`payment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2786 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_issue_receipt`
--

DROP TABLE IF EXISTS `ret_issue_receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_issue_receipt` (
  `id_issue_receipt` int NOT NULL AUTO_INCREMENT,
  `fin_year_code` varchar(15) DEFAULT NULL,
  `bill_no` varchar(45) NOT NULL,
  `bill_date` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Issue, 2 - Receipt',
  `id_branch` int DEFAULT NULL,
  `mobile` varchar(25) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `issue_to` tinyint(1) DEFAULT NULL COMMENT '1 - Employee, 2 - Customer, 3 - Others',
  `receipt_to` tinyint(1) DEFAULT NULL,
  `id_customer` int unsigned DEFAULT NULL,
  `id_employee` int unsigned DEFAULT NULL,
  `is_closed` tinyint(1) DEFAULT '0',
  `id_karigar` int DEFAULT NULL,
  `emp_id` int DEFAULT NULL,
  `issue_type` tinyint(1) DEFAULT NULL COMMENT '1 - Petty Cash, 2 - Credit,3-Advance Refund,4-Existing Credit Sales, 6 - Non jewellery Expenses',
  `id_acc_head` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `weight` decimal(12,3) DEFAULT NULL,
  `receipt_type` tinyint(1) DEFAULT NULL COMMENT '1 - Credit Collection, 2 - Advance,3-Advance Deposit,4-Opening Blc,5-Order Advance to General Advance,6-Chit Close,7-Advance Transfer',
  `receipt_as` tinyint(1) DEFAULT NULL COMMENT '1 - Amount, 2 - Weight',
  `store_receipt_as` tinyint DEFAULT NULL COMMENT '1 - Amount, 2 - Weight',
  `rate_calc` tinyint(1) DEFAULT '1' COMMENT '1-Gold,2-Silver',
  `rate_per_gram` decimal(10,2) DEFAULT NULL,
  `receipt_for` int DEFAULT NULL COMMENT 'id_issue_receipt of credit collected',
  `bill_status` tinyint NOT NULL DEFAULT '1',
  `is_collect` tinyint NOT NULL DEFAULT '0' COMMENT '0->Not Collected,1->Collected',
  `narration` text NOT NULL,
  `pan_no` varchar(50) DEFAULT NULL,
  `pan_image` text COMMENT 'Multiple Image seperated by#',
  `counter_id` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `is_refunded` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `refno` varchar(20) DEFAULT NULL,
  `id_customerorder` int DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(200) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `deposit_bill_id` int DEFAULT NULL,
  `ref_bill_id` int DEFAULT NULL,
  `form_secret` varchar(100) DEFAULT NULL,
  `is_eda` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 1-Normal Receipt,2-Type2(No2) Receipt',
  `deposit_type` tinyint(1) DEFAULT NULL COMMENT '1-From old Metal,2-From Sales return,3- chit pre close',
  UNIQUE KEY `id_issue_receipt` (`id_issue_receipt`),
  UNIQUE KEY `Bill No` (`fin_year_code`,`bill_no`,`id_branch`,`is_eda`) USING BTREE,
  UNIQUE KEY `form_secret` (`form_secret`),
  KEY `tally_guid` (`tally_guid`),
  KEY `type` (`type`),
  KEY `issue_to` (`issue_to`),
  KEY `id_customer` (`id_customer`),
  KEY `issue_type` (`issue_type`),
  KEY `receipt_type` (`receipt_type`),
  KEY `bill_status` (`bill_status`),
  KEY `is_refunded` (`is_refunded`),
  KEY `istransfered` (`istransfered`)
) ENGINE=InnoDB AUTO_INCREMENT=4993 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_issue_receipt_advance_adj`
--

DROP TABLE IF EXISTS `ret_issue_receipt_advance_adj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_issue_receipt_advance_adj` (
  `id_adv_adj` int NOT NULL AUTO_INCREMENT,
  `id_issue_receipt` int DEFAULT NULL,
  `receipt_for` int DEFAULT NULL,
  `adjusted_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_adv_adj`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar`
--

DROP TABLE IF EXISTS `ret_karigar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar` (
  `id_karigar` int unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(75) NOT NULL,
  `lastname` varchar(75) DEFAULT NULL,
  `code_karigar` varchar(25) DEFAULT NULL,
  `karigar_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-INdividual,1-Company,2-Firm',
  `karigar_for` tinyint NOT NULL DEFAULT '1' COMMENT '1-Jewel Manufacturer,2-Jewel Supplier,3-Other Center(Like Halmarking),4-Other Inventory Supplier,5-Supplier & Manufacturer',
  `address1` varchar(200) DEFAULT NULL,
  `address2` varchar(200) DEFAULT NULL,
  `address3` varchar(200) DEFAULT NULL,
  `id_country` int DEFAULT '101',
  `id_state` int DEFAULT NULL,
  `id_city` int DEFAULT NULL,
  `pincode` varchar(6) DEFAULT NULL,
  `email` varchar(85) DEFAULT NULL,
  `contactno1` varchar(20) DEFAULT NULL,
  `contactno2` varchar(20) DEFAULT NULL,
  `status_karigar` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '1 -> Approve 0 -> Not approve',
  `urname` varchar(75) DEFAULT NULL,
  `psword` varchar(200) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `createdby` int unsigned DEFAULT NULL,
  `updateon` datetime DEFAULT NULL,
  `updatedby` int unsigned DEFAULT NULL,
  `image` varchar(345) DEFAULT NULL,
  `company` varchar(145) DEFAULT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `acc_number` varchar(20) DEFAULT NULL,
  `bank_name` varchar(20) DEFAULT NULL,
  `acc_holder_name` varchar(50) DEFAULT NULL,
  `pan_no` varchar(20) DEFAULT NULL,
  `is_tcs` int NOT NULL DEFAULT '0' COMMENT '0-> None 1-> yes',
  `tcs_tax` decimal(10,3) NOT NULL DEFAULT '0.000',
  `is_tds` int NOT NULL DEFAULT '0',
  `tds_tax` decimal(10,3) NOT NULL DEFAULT '0.000',
  `karigar_calc_type` int DEFAULT NULL,
  `remarks` varchar(50) DEFAULT NULL,
  `fin_year_code` varchar(15) DEFAULT NULL,
  `opening_balance_amount` decimal(10,0) DEFAULT NULL,
  `owner_account` int NOT NULL,
  PRIMARY KEY (`id_karigar`),
  UNIQUE KEY `pan_no` (`pan_no`),
  UNIQUE KEY `gst_number` (`gst_number`),
  KEY `karigar_type` (`karigar_type`),
  KEY `karigar_for` (`karigar_for`),
  KEY `id_country` (`id_country`),
  KEY `id_state` (`id_state`),
  KEY `id_city` (`id_city`),
  KEY `status_karigar` (`status_karigar`),
  KEY `is_tcs` (`is_tcs`),
  KEY `is_tds` (`is_tds`)
) ENGINE=InnoDB AUTO_INCREMENT=891 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_advance_payment`
--

DROP TABLE IF EXISTS `ret_karigar_advance_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_advance_payment` (
  `kar_adv_pay_id` int NOT NULL,
  `job_order_id` int DEFAULT NULL,
  `pay_type` int DEFAULT NULL COMMENT '1->Metal 2-> Cash',
  `amount` decimal(10,2) DEFAULT NULL,
  `purity` int DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `pay_date` datetime DEFAULT NULL,
  `metal_type` int DEFAULT NULL COMMENT '1->Oranament 2-> Coin 3-> Bar',
  `item_type` int DEFAULT NULL COMMENT '1->Gold 2->Silver'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_bank_acc_details`
--

DROP TABLE IF EXISTS `ret_karigar_bank_acc_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_bank_acc_details` (
  `id_acc_details` int NOT NULL AUTO_INCREMENT,
  `id_karigar` int NOT NULL,
  `account_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifsc_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_acc_details`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_charges`
--

DROP TABLE IF EXISTS `ret_karigar_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_charges` (
  `id_karigar_charges` int NOT NULL AUTO_INCREMENT,
  `id_karikar_wast` int NOT NULL,
  `id_karigar` int NOT NULL,
  `charge_id` int NOT NULL,
  `calc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Per Item,2-Per pcs',
  `charge_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_karigar_charges`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_import`
--

DROP TABLE IF EXISTS `ret_karigar_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_import` (
  `import_id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_karigar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `karigar_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contactno1` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_name` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_tcs` int DEFAULT NULL,
  `tcs_tax` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_tds` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tds_tax` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_number` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`import_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_item_wast_pro_images`
--

DROP TABLE IF EXISTS `ret_karigar_item_wast_pro_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_item_wast_pro_images` (
  `id_pro_image` int NOT NULL AUTO_INCREMENT,
  `id_karikar_wast` int NOT NULL,
  `image_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id_pro_image` (`id_pro_image`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_kyc`
--

DROP TABLE IF EXISTS `ret_karigar_kyc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_kyc` (
  `id_karigar_kyc` int NOT NULL AUTO_INCREMENT,
  `id_karigar` int NOT NULL,
  `id_kyc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'id from kyc master',
  `kyc_number` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `front_images` varchar(345) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `back_images` varchar(345) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `document` varchar(345) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_karigar_kyc`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_metal_issue`
--

DROP TABLE IF EXISTS `ret_karigar_metal_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_metal_issue` (
  `met_issue_id` int NOT NULL AUTO_INCREMENT,
  `fin_year_code` int NOT NULL,
  `id_branch` int DEFAULT NULL,
  `is_against_opening` tinyint(1) NOT NULL DEFAULT '0',
  `met_issue_karid` int NOT NULL,
  `met_issue_ref_id` varchar(10) NOT NULL,
  `met_issue_date` datetime NOT NULL,
  `met_issue_created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `met_issue_created_by` int NOT NULL,
  `issue_aganist` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Aganist Order	',
  `issue_from` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Tag,2-Non Tag',
  `metalissue_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1 -> Normal issue 2 -> Against po',
  `po_id` int DEFAULT NULL,
  `issue_against_po` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-> no , 1->yes',
  `tag_issue_from` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> Available Stock , 2 -> Sales Return , 3-> Partly Sales',
  `nontag_issue_from` tinyint(1) DEFAULT '1' COMMENT '1 ->Available Stock , 2 -> Nontag Sales Return , 3 -> Nontag Other Issue',
  `id_order` int DEFAULT NULL,
  `bill_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Success,2-Cancel',
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`met_issue_id`),
  KEY `Idx_metal_issue_kar_id` (`met_issue_karid`),
  KEY `Idx_met_issue_refId` (`met_issue_ref_id`),
  KEY `Idx_met_iss_date` (`met_issue_date`)
) ENGINE=MyISAM AUTO_INCREMENT=526 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_metal_issue_details`
--

DROP TABLE IF EXISTS `ret_karigar_metal_issue_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_metal_issue_details` (
  `issue_met_id` int NOT NULL AUTO_INCREMENT,
  `issue_met_parent_id` int NOT NULL,
  `issue_metal` int NOT NULL,
  `issue_cat_id` int NOT NULL,
  `issue_pur_id` int NOT NULL,
  `issu_met_id_section` int DEFAULT NULL,
  `issu_met_pro_id` int NOT NULL,
  `issu_met_id_design` int DEFAULT NULL,
  `issu_met_id_sub_design` int DEFAULT NULL,
  `issue_pcs` int DEFAULT NULL,
  `issue_metal_wt` decimal(10,3) NOT NULL,
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_uom_id` int DEFAULT NULL,
  `mc` decimal(10,2) DEFAULT '0.00',
  `mc_type` tinyint(1) DEFAULT '1',
  `wastage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `touch` decimal(10,2) DEFAULT NULL,
  `calc_type` tinyint(1) DEFAULT NULL,
  `issue_metal_pur_wt` decimal(10,3) NOT NULL,
  `issue_metal_purrate` decimal(10,2) DEFAULT NULL,
  `po_item_id` int DEFAULT NULL COMMENT 'Id from ret_purchase_order_items table',
  `tag_id` int DEFAULT NULL,
  `bill_det_id` int DEFAULT NULL,
  `branch_trans_id` int DEFAULT NULL,
  `id_smith_company_op_balance` int DEFAULT NULL,
  `is_repair_item` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`issue_met_id`),
  KEY `Idx_parent_metal_issueId` (`issue_met_parent_id`),
  KEY `IdxIssue_metalId` (`issue_metal`),
  KEY `IdxMetIssueCatId` (`issue_cat_id`),
  KEY `IdxmetIssueProId` (`issu_met_pro_id`)
) ENGINE=MyISAM AUTO_INCREMENT=763 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_metal_issue_po_details`
--

DROP TABLE IF EXISTS `ret_karigar_metal_issue_po_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_metal_issue_po_details` (
  `met_iss_po_det_id` int NOT NULL AUTO_INCREMENT,
  `met_iss_po_metissuid` int NOT NULL,
  `met_iss_po_itm_id` int NOT NULL,
  `met_iss_po_issue_wt` decimal(10,3) NOT NULL,
  `met_iss_po_issue_purwt` decimal(10,3) NOT NULL,
  PRIMARY KEY (`met_iss_po_det_id`),
  KEY `IdxPORefDetailsId` (`met_iss_po_itm_id`),
  KEY `IdxMetalIssueDetailId` (`met_iss_po_metissuid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_metal_issue_stone_details`
--

DROP TABLE IF EXISTS `ret_karigar_metal_issue_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_metal_issue_stone_details` (
  `id_stn_detail` int NOT NULL AUTO_INCREMENT,
  `issue_met_id` int NOT NULL,
  `stone_id` int NOT NULL,
  `uom_id` int NOT NULL,
  `stone_pcs` int DEFAULT NULL,
  `stone_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `is_apply_in_lwt` tinyint(1) NOT NULL DEFAULT '1',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stone_cal_type` tinyint(1) NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stone_quality_id` int DEFAULT NULL,
  PRIMARY KEY (`id_stn_detail`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_products`
--

DROP TABLE IF EXISTS `ret_karigar_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_products` (
  `id_karigar_product` int NOT NULL AUTO_INCREMENT,
  `id_product` int NOT NULL,
  `id_design` int NOT NULL,
  `id_karigar` int NOT NULL,
  `approx_delivery_days` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_karigar_product`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_stones`
--

DROP TABLE IF EXISTS `ret_karigar_stones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_stones` (
  `id_karigar_stone` int NOT NULL AUTO_INCREMENT,
  `id_karigar` int NOT NULL,
  `stone_type` int NOT NULL,
  `stone_id` int NOT NULL,
  `uom_id` int DEFAULT NULL,
  `stone_cal_type` int NOT NULL DEFAULT '1',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quality_id` int DEFAULT NULL,
  `from_wt` decimal(12,3) DEFAULT NULL,
  `to_wt` decimal(12,3) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_on` datetime DEFAULT NULL,
  `approved_through` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> ADMIN\r\n2 -> APP',
  `rejected_by` int DEFAULT NULL,
  `rejected_on` datetime DEFAULT NULL,
  `rejected_through` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> ADMIN\r\n2 -> APP',
  `holded_by` int DEFAULT NULL,
  `holded_on` datetime DEFAULT NULL,
  `holded_through` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> ADMIN\r\n2 -> APP',
  PRIMARY KEY (`id_karigar_stone`)
) ENGINE=InnoDB AUTO_INCREMENT=822 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_wallet`
--

DROP TABLE IF EXISTS `ret_karigar_wallet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_wallet` (
  `id_wallet` int NOT NULL AUTO_INCREMENT,
  `id_karigar` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id_wallet`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karigar_wallet_transcation`
--

DROP TABLE IF EXISTS `ret_karigar_wallet_transcation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karigar_wallet_transcation` (
  `id_transcation` int NOT NULL AUTO_INCREMENT,
  `id_wallet` int NOT NULL,
  `po_pay_id` int DEFAULT NULL,
  `type` tinyint DEFAULT NULL COMMENT '1-Credit,2-Debit',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remarks` text,
  UNIQUE KEY `id_transcation` (`id_transcation`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_karikar_items_wastage`
--

DROP TABLE IF EXISTS `ret_karikar_items_wastage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_karikar_items_wastage` (
  `id_karikar_wast` int NOT NULL AUTO_INCREMENT,
  `id_karikar` int NOT NULL,
  `id_category` int NOT NULL,
  `id_product` int NOT NULL,
  `id_design` int NOT NULL,
  `id_sub_design` int NOT NULL,
  `id_purity` int DEFAULT NULL,
  `karigar_calc_type` int DEFAULT NULL,
  `wastage_type` int DEFAULT '1' COMMENT '1-Percentage ,2-Per weight',
  `calc_type` tinyint(1) DEFAULT '2' COMMENT '0 - Mc & Wast on Gross wt,1 - Mc & Wast on Net wt,2 - Mc on Gross, Wast on Net ',
  `wastage_per` decimal(10,2) NOT NULL,
  `wastage_wt` decimal(10,3) NOT NULL,
  `mc_type` int DEFAULT NULL COMMENT '1-Per Gram ,2-Per pcs',
  `mc_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_touch` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stone_cal_type` int DEFAULT '1',
  `uom_id` int DEFAULT NULL,
  `quality_id` int DEFAULT NULL,
  `from_wt` decimal(12,3) DEFAULT NULL,
  `to_wt` decimal(12,3) DEFAULT NULL,
  `rate_per_gram` decimal(10,2) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active,0-In Active',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Yet to approve,1-Approved,2-Rejected',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_on` datetime DEFAULT NULL,
  `approved_through` tinyint(1) DEFAULT '1' COMMENT '1 -> ADMIN\r\n2 -> APP',
  `rejected_by` int DEFAULT NULL,
  `rejected_on` datetime DEFAULT NULL,
  `rejected_through` tinyint(1) DEFAULT '1' COMMENT '1 -> ADMIN\r\n2 -> APP',
  `holded_by` int DEFAULT NULL,
  `holded_on` datetime DEFAULT NULL,
  `holded_through` tinyint(1) DEFAULT '1' COMMENT '1 -> ADMIN\r\n2 -> APP',
  PRIMARY KEY (`id_karikar_wast`),
  KEY `idxwaskarikarid` (`id_karikar`),
  KEY `Idxwassubdesign` (`id_sub_design`),
  KEY `Idxwasproid` (`id_product`),
  KEY `Idxwasdesignid` (`id_design`)
) ENGINE=InnoDB AUTO_INCREMENT=1227 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_kyc_master`
--

DROP TABLE IF EXISTS `ret_kyc_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_kyc_master` (
  `id_kyc_master` int NOT NULL AUTO_INCREMENT,
  `id_karigar` int DEFAULT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Document Name',
  `image` tinyint NOT NULL DEFAULT '1' COMMENT '1-Front,2Front & Back',
  `image_type` int DEFAULT NULL COMMENT '1->JPG 2->JPEG 3->PNG',
  `is_id_req` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-No,1-Yes(Document No)',
  `is_regular_expression_req` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `regular_expression` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_type` int DEFAULT NULL COMMENT '1- PDF.2- WORD',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1-Active,0-Inactive',
  PRIMARY KEY (`id_kyc_master`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_ledger_transfer`
--

DROP TABLE IF EXISTS `ret_ledger_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_ledger_transfer` (
  `transfer_id` int NOT NULL AUTO_INCREMENT,
  `from_ledger_id` int NOT NULL,
  `to_ledger_id` int DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT '0.00',
  `narration` text,
  `transfer_type` int DEFAULT '1' COMMENT '1 for Transfer, 2 for Manual',
  `transaction_type` int DEFAULT NULL COMMENT '1 - Credit, 2 - Debit',
  `created_by` int DEFAULT NULL,
  `transfer_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transfer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_inwards`
--

DROP TABLE IF EXISTS `ret_lot_inwards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_inwards` (
  `lot_no` int NOT NULL AUTO_INCREMENT,
  `lot_type` tinyint(1) DEFAULT '1' COMMENT '1->Normal 2->Customer Order 3->Repair',
  `lot_received_at` int unsigned NOT NULL,
  `lot_date` datetime DEFAULT NULL,
  `gold_smith` int unsigned DEFAULT NULL,
  `order_no` varchar(45) DEFAULT NULL,
  `order_branch` int DEFAULT NULL,
  `id_category` int DEFAULT NULL,
  `id_purity` int DEFAULT NULL,
  `product_division` int DEFAULT NULL,
  `stock_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Tagged , 2 - NonTagged	',
  `lot_images` text COMMENT 'Multiple images separated by #	',
  `narration` varchar(445) DEFAULT NULL,
  `created_branch` int unsigned DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `lot_from` tinyint(1) NOT NULL DEFAULT '1' COMMENT '''1-Manual Entry,2-From Supplier Entry'',3-From Import,4-From Tag Process,5-From Old Metal Process,6-From Retagging,7 -> Lot Merge',
  `po_id` int DEFAULT NULL,
  `grn_id` int DEFAULT NULL,
  `id_metal_process` int DEFAULT NULL,
  `is_lot_split` tinyint(1) DEFAULT '0',
  `is_closed` tinyint(1) DEFAULT '0' COMMENT '0 -> No , 1 -> YES',
  `closed_by` int unsigned DEFAULT NULL,
  `closed_on` datetime DEFAULT NULL,
  `id_acc_process` int DEFAULT NULL COMMENT 'From ret_acc_stock_process Table',
  `lot_status` tinyint(1) NOT NULL DEFAULT '1',
  `cancel_reason` varchar(100) DEFAULT NULL,
  `cancelled_on` datetime DEFAULT NULL,
  `cancelled_by` int DEFAULT NULL,
  PRIMARY KEY (`lot_no`),
  UNIQUE KEY `lot_no` (`lot_no`)
) ENGINE=InnoDB AUTO_INCREMENT=9584 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_inwards_detail`
--

DROP TABLE IF EXISTS `ret_lot_inwards_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_inwards_detail` (
  `id_lot_inward_detail` int NOT NULL AUTO_INCREMENT,
  `lot_no` int NOT NULL,
  `gold_smith` int unsigned DEFAULT NULL,
  `order_no` varchar(45) DEFAULT NULL,
  `lot_id_category` int NOT NULL,
  `lot_id_purity` int NOT NULL,
  `lot_product` int unsigned DEFAULT NULL,
  `lot_id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `id_section` int DEFAULT NULL COMMENT 'For Non Tag Items',
  `design_for` int DEFAULT NULL COMMENT '1->Male 2-> Female 3-> Unisex 4-> Kids',
  `no_of_piece` int DEFAULT '1',
  `size` decimal(10,2) DEFAULT NULL,
  `gross_wt` decimal(14,3) DEFAULT '0.000',
  `gross_wt_uom` int DEFAULT NULL,
  `net_wt` decimal(14,3) DEFAULT '0.000',
  `net_wt_uom` int DEFAULT NULL,
  `less_wt` decimal(14,4) DEFAULT NULL,
  `less_wt_uom` int DEFAULT NULL,
  `wastage_percentage` decimal(10,2) DEFAULT '0.00' COMMENT 'Purchase Wastage',
  `mc_type` tinyint(1) DEFAULT '1' COMMENT 'Purchase MC Type\r\n1 - MC per gram, 2 - MC per piece',
  `making_charge` decimal(10,0) DEFAULT '0' COMMENT 'Purchase MC',
  `precious_stone` tinyint(1) DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  `precious_st_pcs` int DEFAULT '0',
  `precious_st_wt` decimal(10,2) DEFAULT '0.00',
  `precious_st_uom` int DEFAULT NULL,
  `semi_precious_stone` tinyint(1) DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  `semi_precious_st_pcs` int DEFAULT NULL,
  `semi_precious_st_wt` decimal(10,2) DEFAULT NULL,
  `semi_precious_st_uom` int DEFAULT NULL,
  `normal_stone` tinyint(1) DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  `normal_st_pcs` int DEFAULT NULL,
  `normal_st_wt` decimal(10,2) DEFAULT NULL,
  `normal_st_wt_uom` int DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `current_branch` int unsigned DEFAULT NULL,
  `tag_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Tagged',
  `normal_st_certif` text COMMENT 'Multiple images separated by #',
  `precious_st_certif` text COMMENT 'Multiple images separated by #',
  `semiprecious_st_certif` text COMMENT 'Multiple images separated by #',
  `buy_rate` decimal(10,2) DEFAULT NULL COMMENT 'Buying Rate ( From Vendor)',
  `sell_rate` decimal(10,2) DEFAULT NULL COMMENT 'Selling Rate ( To Customer)',
  `rate` decimal(10,2) DEFAULT '0.00' COMMENT 'Purchase Rate',
  `rate_calc_type` int DEFAULT NULL COMMENT 'Purchase Rate Calculation type',
  `purchase_touch` decimal(10,2) DEFAULT NULL COMMENT 'Purchase Touch',
  `calc_type` int DEFAULT NULL COMMENT 'Purchase Calculation type',
  `pur_wt` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Purchase Pure Wt',
  `item_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Inclusive,2-Exclusive',
  `total_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(10,2) NOT NULL,
  `tax_group` int DEFAULT NULL,
  `total_cgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_sgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_igst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `po_item_id` int DEFAULT NULL COMMENT 'From purchase order items',
  PRIMARY KEY (`id_lot_inward_detail`),
  KEY `lot_no` (`lot_no`),
  KEY `gold_smith` (`gold_smith`),
  KEY `lot_product` (`lot_product`),
  KEY `lot_id_design` (`lot_id_design`),
  KEY `id_sub_design` (`id_sub_design`),
  KEY `no_of_piece` (`no_of_piece`),
  KEY `mc_type` (`mc_type`),
  KEY `tag_status` (`tag_status`),
  KEY `current_branch` (`current_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=17811 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_inwards_stone_detail`
--

DROP TABLE IF EXISTS `ret_lot_inwards_stone_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_inwards_stone_detail` (
  `id_stn_detail` int NOT NULL AUTO_INCREMENT,
  `id_lot_inward_detail` int NOT NULL,
  `stone_id` int NOT NULL,
  `uom_id` int NOT NULL,
  `stone_pcs` int DEFAULT NULL,
  `stone_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `is_apply_in_lwt` tinyint(1) NOT NULL DEFAULT '1',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stone_cal_type` tinyint(1) NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stone_quality_id` int DEFAULT NULL,
  PRIMARY KEY (`id_stn_detail`)
) ENGINE=InnoDB AUTO_INCREMENT=584 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_merge`
--

DROP TABLE IF EXISTS `ret_lot_merge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_merge` (
  `id_lot_merge` int NOT NULL AUTO_INCREMENT,
  `lot_no` int NOT NULL COMMENT 'Refer id from ret_lot_inwards',
  `id_lot_inward_detail` int NOT NULL COMMENT 'Refer id from ret_lot_inwards_detail',
  `created_by` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_lot_merge`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_other_charges`
--

DROP TABLE IF EXISTS `ret_lot_other_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_other_charges` (
  `id_lot_other_charge` int NOT NULL AUTO_INCREMENT,
  `id_lot_inward_detail` int NOT NULL,
  `charge_id` int NOT NULL,
  `calc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '	1-Per Item,2-Per pcs',
  `charge_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_total_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(11,0) NOT NULL,
  `total_charge_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_lot_other_charge`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_other_items`
--

DROP TABLE IF EXISTS `ret_lot_other_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_other_items` (
  `other_item_id` int NOT NULL AUTO_INCREMENT,
  `id_lot_inward_detail` int NOT NULL,
  `item_metal` int NOT NULL,
  `item_gross_weight` decimal(10,3) NOT NULL,
  `other_item_uom` tinyint(1) DEFAULT NULL,
  `other_wastage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `other_mc_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `other_mc_type` tinyint(1) NOT NULL,
  `other_item_purity` tinyint(1) NOT NULL,
  `other_item_cal_type` tinyint(1) DEFAULT NULL,
  `other_item_rate` decimal(10,2) NOT NULL,
  `other_item_pcs` int NOT NULL,
  `other_item_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`other_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_split`
--

DROP TABLE IF EXISTS `ret_lot_split`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_split` (
  `id_lot_split` int NOT NULL AUTO_INCREMENT,
  `lot_received_at` int unsigned NOT NULL,
  `id_employee` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_lot_split`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_split_details`
--

DROP TABLE IF EXISTS `ret_lot_split_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_split_details` (
  `id_lot_split_details` int NOT NULL AUTO_INCREMENT,
  `id_lot_inward_detail` int NOT NULL COMMENT 'Refer id from ret_lot_inwards_detail table',
  `id_employee` int NOT NULL,
  `id_category` int DEFAULT NULL,
  `id_purity` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `split_pcs` int NOT NULL DEFAULT '0',
  `split_grs_wt` decimal(14,3) NOT NULL DEFAULT '0.000',
  `split_net_wt` decimal(14,3) NOT NULL DEFAULT '0.000',
  `split_stn_pcs` int DEFAULT NULL,
  `split_stn_wt` decimal(14,3) DEFAULT '0.000',
  `split_dia_pcs` int DEFAULT '0',
  `split_dia_wt` decimal(14,3) DEFAULT '0.000',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_lot_split_details`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_split_stone_details`
--

DROP TABLE IF EXISTS `ret_lot_split_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_split_stone_details` (
  `id_split_stn_detail` int NOT NULL AUTO_INCREMENT,
  `id_lot_split_details` int NOT NULL,
  `stone_id` int NOT NULL,
  `uom_id` int NOT NULL,
  `stone_pcs` int DEFAULT NULL,
  `stone_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id_split_stn_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_lot_types`
--

DROP TABLE IF EXISTS `ret_lot_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_lot_types` (
  `lot_type_id` int NOT NULL,
  `lot_type_name` varchar(95) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_making_type`
--

DROP TABLE IF EXISTS `ret_making_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_making_type` (
  `mak_id` int NOT NULL AUTO_INCREMENT,
  `mak_name` varchar(100) DEFAULT NULL,
  `mak_short_code` varchar(10) DEFAULT NULL,
  `mak_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`mak_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_material`
--

DROP TABLE IF EXISTS `ret_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_material` (
  `material_id` int NOT NULL AUTO_INCREMENT,
  `material_name` varchar(100) DEFAULT NULL,
  `material_code` varchar(10) DEFAULT NULL,
  `material_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `uom_id` int DEFAULT NULL,
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_material_rate`
--

DROP TABLE IF EXISTS `ret_material_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_material_rate` (
  `mat_rate_id` int NOT NULL AUTO_INCREMENT,
  `material_id` int DEFAULT NULL,
  `mat_rate` varchar(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `effective_date` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` int DEFAULT NULL,
  `updated_by` datetime DEFAULT NULL,
  PRIMARY KEY (`mat_rate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_metai_issue_receipt`
--

DROP TABLE IF EXISTS `ret_metai_issue_receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_metai_issue_receipt` (
  `id_issue_receipt` int NOT NULL AUTO_INCREMENT,
  `id_metal_issue` int DEFAULT NULL,
  `receipt_date` datetime NOT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id_issue_receipt`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_metai_issue_receipt_details`
--

DROP TABLE IF EXISTS `ret_metai_issue_receipt_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_metai_issue_receipt_details` (
  `id_receipt_details` int NOT NULL AUTO_INCREMENT,
  `id_issue_receipt` int DEFAULT NULL,
  `issue_met_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `design_id` int DEFAULT NULL,
  `sub_design_id` int DEFAULT NULL,
  `piece` int NOT NULL DEFAULT '0',
  `weight` decimal(10,3) DEFAULT '0.000' COMMENT 'Gross wt',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id_receipt_details`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_metai_issue_receipt_stone_details`
--

DROP TABLE IF EXISTS `ret_metai_issue_receipt_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_metai_issue_receipt_stone_details` (
  `id_stn_detail` int NOT NULL AUTO_INCREMENT,
  `id_receipt_details` int NOT NULL,
  `stone_id` int NOT NULL,
  `uom_id` int NOT NULL,
  `stone_pcs` int DEFAULT NULL,
  `stone_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `is_apply_in_lwt` tinyint(1) NOT NULL DEFAULT '1',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stone_cal_type` tinyint(1) NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stone_quality_id` int DEFAULT NULL,
  PRIMARY KEY (`id_stn_detail`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_metal_cat_purity`
--

DROP TABLE IF EXISTS `ret_metal_cat_purity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_metal_cat_purity` (
  `id_met_cat_pur` int NOT NULL AUTO_INCREMENT,
  `id_category` int NOT NULL,
  `id_purity` varchar(100) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`id_met_cat_pur`)
) ENGINE=InnoDB AUTO_INCREMENT=817 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_metal_purity_rate`
--

DROP TABLE IF EXISTS `ret_metal_purity_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_metal_purity_rate` (
  `id_metal_purity_rate` int NOT NULL AUTO_INCREMENT,
  `id_metal` int unsigned NOT NULL,
  `id_purity` int unsigned NOT NULL,
  `rate_field` varchar(250) NOT NULL COMMENT 'Rate field of metal_rates table',
  `market_rate_field` varchar(250) DEFAULT NULL COMMENT 'Market Rate field of metal_rates table',
  `created_on` datetime NOT NULL,
  `created_by` int unsigned NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_metal_purity_rate`),
  KEY `id_metal` (`id_metal`),
  KEY `id_purity` (`id_purity`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_metal_stock`
--

DROP TABLE IF EXISTS `ret_metal_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_metal_stock` (
  `id_metal_stock` int NOT NULL,
  `stock_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Old Metal,1-Bullion Purchase,2-Sales Return,3-Partly sale',
  `is_opening` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `id_branch` int DEFAULT NULL,
  `date` date NOT NULL,
  `id_metal` int DEFAULT NULL,
  `id_ret_category` int DEFAULT NULL,
  `id_old_metal_type` int DEFAULT NULL,
  `op_blc_gwt` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Opening Blc Weight',
  `op_blc_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `op_blc_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Opening Blc Amount',
  `op_avg_purity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inw_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `inw_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `inw_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inw_purity` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Average Purity',
  `outward_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `outward_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `outward_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `outward_purity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `closing_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `closing_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_purity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_nontag_item`
--

DROP TABLE IF EXISTS `ret_nontag_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_nontag_item` (
  `id_nontag_item` int NOT NULL AUTO_INCREMENT,
  `branch` int unsigned DEFAULT NULL COMMENT 'Stock Branch',
  `id_section` int DEFAULT NULL,
  `product` int DEFAULT NULL,
  `design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `no_of_piece` int DEFAULT NULL,
  `gross_wt` decimal(14,3) DEFAULT '0.000',
  `net_wt` decimal(14,3) DEFAULT '0.000',
  `less_wt` decimal(14,4) DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  UNIQUE KEY `id_nontag_item` (`id_nontag_item`),
  UNIQUE KEY `branch` (`branch`,`product`,`design`,`id_sub_design`,`id_section`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_nontag_item_log`
--

DROP TABLE IF EXISTS `ret_nontag_item_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_nontag_item_log` (
  `id_nontag_log` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0-> Not yet sale 1-> Sold out 3-> Other Issue, 4-> In Transit 6->Cancel,7-Karigar Metal Issue 8-> Purchase Return,9-Ret Cancel,10-Added to pocket',
  `bill_id` int DEFAULT NULL,
  `ref_no` int DEFAULT NULL,
  `from_branch` int unsigned DEFAULT NULL,
  `to_branch` int unsigned DEFAULT NULL,
  `product` int DEFAULT NULL,
  `design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `no_of_piece` int DEFAULT NULL,
  `gross_wt` decimal(14,4) DEFAULT '0.0000',
  `net_wt` decimal(14,4) DEFAULT '0.0000',
  `less_wt` decimal(14,4) DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `form_secret` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_nontag_log`),
  UNIQUE KEY `form_secret` (`form_secret`)
) ENGINE=InnoDB AUTO_INCREMENT=902 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_nontag_receipt`
--

DROP TABLE IF EXISTS `ret_nontag_receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_nontag_receipt` (
  `id_nontag_receipt` int NOT NULL AUTO_INCREMENT,
  `nt_receipt_no` varchar(56) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lot_id` int NOT NULL,
  `id_lot_inward_detail` int NOT NULL,
  `id_branch` int NOT NULL,
  `id_section` int NOT NULL,
  `id_product` int NOT NULL,
  `id_design` int NOT NULL,
  `id_sub_design` int NOT NULL,
  `pcs` int DEFAULT NULL,
  `grs_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `remark` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_nontag_receipt`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_noticeboard`
--

DROP TABLE IF EXISTS `ret_noticeboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_noticeboard` (
  `id_noticeboard` int NOT NULL,
  `id_branch` int DEFAULT NULL,
  `noticeboard_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT 'Notice board content',
  `noticeboard_status` int DEFAULT NULL COMMENT '1->Active 0->Inactive',
  `visible_to` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Option to concadinate and update user role wise. Multiple user rolse can concadinate with ##',
  `reminder_on` date DEFAULT NULL COMMENT 'Reminder datetime will update in this field',
  `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL COMMENT 'Created by employee',
  `updated_by` int DEFAULT NULL COMMENT 'Updated employee id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_noticeboard_view_details`
--

DROP TABLE IF EXISTS `ret_noticeboard_view_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_noticeboard_view_details` (
  `view_det_id` int NOT NULL,
  `id_noticeboard` int NOT NULL,
  `id_employee` int NOT NULL,
  `is_viewed` tinyint(1) NOT NULL DEFAULT '0',
  `updated_on` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_category`
--

DROP TABLE IF EXISTS `ret_old_metal_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_category` (
  `id_old_metal_cat` int NOT NULL AUTO_INCREMENT,
  `id_metal` int NOT NULL,
  `id_old_metal_type` int NOT NULL,
  `old_metal_cat` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `hsn_code` int NOT NULL,
  `old_metal_perc` decimal(12,2) NOT NULL,
  `old_metal_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_old_metal_cat`),
  KEY `Old_Metal_Type` (`id_old_metal_type`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_melting`
--

DROP TABLE IF EXISTS `ret_old_metal_melting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_melting` (
  `id_melting` int NOT NULL AUTO_INCREMENT,
  `id_old_metal_process` int DEFAULT NULL COMMENT 'For Issue Process',
  `piece` int NOT NULL DEFAULT '1',
  `gross_wt` decimal(10,3) DEFAULT NULL,
  `net_wt` decimal(10,3) DEFAULT NULL,
  `dia_wt` decimal(10,3) DEFAULT '0.000',
  `amount` decimal(10,2) DEFAULT NULL,
  `purity` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `melting_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Issue,1-Melting Completed,2-Given to Testing,3-Testing Completed,4-Refining Issue,5-Refining Receipt',
  `received_category` int DEFAULT NULL,
  `received_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `received_purity` int DEFAULT NULL,
  `received_less_wt` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Production Loss',
  `id_old_metal_process_receipt` int DEFAULT NULL COMMENT 'For Receipt Process',
  `receipt_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `receipt_ref_no` text,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_melting`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_melting_details`
--

DROP TABLE IF EXISTS `ret_old_metal_melting_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_melting_details` (
  `id_melting_details` int NOT NULL AUTO_INCREMENT,
  `id_melting` int DEFAULT NULL,
  `id_pocket` int DEFAULT NULL,
  `id_pocket_details` int DEFAULT NULL,
  `id_metal_type` int DEFAULT NULL,
  `id_category` int DEFAULT NULL,
  `issue_pcs` int DEFAULT '0',
  `issue_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_diawt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_purity` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_rate` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_melting_details`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_melting_recd_details`
--

DROP TABLE IF EXISTS `ret_old_metal_melting_recd_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_melting_recd_details` (
  `id_melting_recd` int NOT NULL AUTO_INCREMENT,
  `id_melting` int DEFAULT NULL,
  `melting_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Issue,1-Melting Completed,2-Given to Testing,3-Testing Completed,4-Refining Issue,5-Refining Receipt,6-Testing Completed and added to non tag stock',
  `received_category` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `recd_pcs` int DEFAULT NULL,
  `received_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `testing_completed_wt` decimal(10,3) DEFAULT NULL,
  `tested_purity` decimal(10,3) DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  UNIQUE KEY `id_melting_recd` (`id_melting_recd`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_pocket`
--

DROP TABLE IF EXISTS `ret_old_metal_pocket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_pocket` (
  `id_metal_pocket` int NOT NULL AUTO_INCREMENT,
  `pocket_no` varchar(40) DEFAULT NULL,
  `trans_type` int NOT NULL DEFAULT '1' COMMENT '1-Old Metal,2-Tagged Items.3-Non Tag Items',
  `is_against_opening` tinyint(1) NOT NULL DEFAULT '0' COMMENT '	0-No,1-Yes',
  `id_branch` int DEFAULT NULL COMMENT 'Created Branch',
  `date` date DEFAULT NULL,
  `piece` int DEFAULT '0',
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `stone_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `dust_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `final_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `wast_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) DEFAULT '0.000',
  `dia_wt` decimal(10,3) DEFAULT '0.000',
  `total_purity` decimal(10,3) NOT NULL DEFAULT '0.000',
  `avg_purity` decimal(10,3) NOT NULL DEFAULT '0.000',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` int DEFAULT '0' COMMENT '0-Not given to any Process',
  `issue_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_diawt` decimal(10,3) DEFAULT '0.000',
  `issue_pcs` int NOT NULL DEFAULT '0',
  `issue_purity` decimal(10,3) NOT NULL DEFAULT '0.000',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_metal_pocket`),
  KEY `id_branch` (`id_branch`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_pocket_details`
--

DROP TABLE IF EXISTS `ret_old_metal_pocket_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_pocket_details` (
  `id_pocket_details` int NOT NULL AUTO_INCREMENT,
  `id_metal_pocket` int DEFAULT NULL,
  `id_metal_type` int DEFAULT NULL,
  `id_category` int DEFAULT NULL,
  `type` int DEFAULT NULL COMMENT '1-Old Metal,2-Sales Return,3-Partly Sale',
  `tag_id` int DEFAULT NULL,
  `old_metal_sale_id` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `piece` int NOT NULL DEFAULT '0',
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `dust_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `stone_wt` decimal(10,0) NOT NULL DEFAULT '0',
  `wastage_wt` decimal(10,0) NOT NULL DEFAULT '0',
  `diawt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `purity` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'in Percentage',
  PRIMARY KEY (`id_pocket_details`)
) ENGINE=InnoDB AUTO_INCREMENT=327 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_pocket_stone_details`
--

DROP TABLE IF EXISTS `ret_old_metal_pocket_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_pocket_stone_details` (
  `id_pocket_stone_details` int NOT NULL AUTO_INCREMENT,
  `id_pocket_details` int DEFAULT NULL,
  `stone_id` int DEFAULT NULL,
  `stone_pcs` int DEFAULT NULL,
  `stone_wt` decimal(10,3) DEFAULT '0.000',
  PRIMARY KEY (`id_pocket_stone_details`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_polishing`
--

DROP TABLE IF EXISTS `ret_old_metal_polishing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_polishing` (
  `id_polishing` int NOT NULL AUTO_INCREMENT,
  `id_old_metal_process` int DEFAULT NULL COMMENT 'For Issue Process',
  `no_of_piece` int DEFAULT NULL,
  `gross_wt` decimal(10,3) NOT NULL,
  `less_wt` decimal(10,3) NOT NULL,
  `net_wt` decimal(10,3) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Issue,1-Completed',
  `id_old_metal_process_receipt` int DEFAULT NULL COMMENT 'For Receipt Process',
  `received_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `receipt_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `receipt_ref_no` varchar(100) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_polishing`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_polishing_details`
--

DROP TABLE IF EXISTS `ret_old_metal_polishing_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_polishing_details` (
  `id_polishing_details` int NOT NULL AUTO_INCREMENT,
  `id_polishing` int DEFAULT NULL,
  `id_pocket` int DEFAULT NULL,
  `id_old_metal_type` int DEFAULT NULL,
  `id_category` int DEFAULT NULL,
  `issue_pcs` int DEFAULT '0',
  `issue_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_diawt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_purity` decimal(10,3) NOT NULL DEFAULT '0.000',
  `received_pcs` int NOT NULL DEFAULT '0',
  `received_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `received_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `received_diawt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Issue,1-Received',
  UNIQUE KEY `id_polishing_details` (`id_polishing_details`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_polishing_recd_details`
--

DROP TABLE IF EXISTS `ret_old_metal_polishing_recd_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_polishing_recd_details` (
  `id_recd_details` int NOT NULL AUTO_INCREMENT,
  `id_polishing` int DEFAULT NULL,
  `stock_type` int DEFAULT NULL,
  `id_category` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `id_purity` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `no_of_piece` int DEFAULT NULL,
  `gross_wt` decimal(10,3) DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `dia_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id_recd_details`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_process`
--

DROP TABLE IF EXISTS `ret_old_metal_process`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_process` (
  `id_old_metal_process` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `date_add` datetime DEFAULT CURRENT_TIMESTAMP,
  `trans_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Old metal ,2-Tagged Items,3-Non Tag Items',
  `process_no` varchar(20) DEFAULT NULL,
  `process_for` int DEFAULT NULL COMMENT '1-Issue,2-Receipt',
  `from_process` int DEFAULT NULL COMMENT 'From Which Process',
  `to_process` int DEFAULT NULL COMMENT 'To Which Process',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `remark` text,
  `id_metal_process` int DEFAULT NULL COMMENT 'From Old Metal Process Master',
  `id_karigar` int DEFAULT NULL,
  `next_process_for` int DEFAULT '0' COMMENT '1-> Process 2->Stock',
  `issue_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_lwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `recd_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `recd_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `prod_loss` decimal(10,3) NOT NULL DEFAULT '0.000',
  `recd_category` int DEFAULT NULL,
  `issue_date` datetime DEFAULT NULL,
  `issued_by` int DEFAULT NULL,
  `received_date` datetime DEFAULT NULL,
  `received_by` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `outistransfered` int NOT NULL DEFAULT '0',
  `outtally_guid` varchar(100) DEFAULT NULL,
  `outtally_updated_on` datetime DEFAULT NULL,
  `inistransfered` int NOT NULL DEFAULT '0',
  `intally_guid` varchar(100) DEFAULT NULL,
  `intally_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_old_metal_process`),
  UNIQUE KEY `CK_estimate_Unique` (`process_for`,`id_metal_process`,`process_no`)
) ENGINE=InnoDB AUTO_INCREMENT=255 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_process_master`
--

DROP TABLE IF EXISTS `ret_old_metal_process_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_process_master` (
  `id_metal_process` int NOT NULL AUTO_INCREMENT,
  `process_name` varchar(20) DEFAULT NULL,
  `process_code` varchar(20) DEFAULT NULL,
  `has_charge` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `charge_type` tinyint(1) DEFAULT '1' COMMENT '1-Per Gram,2-Flat',
  `process_type` int NOT NULL DEFAULT '3' COMMENT '1->Issue 2-> Receipt 3->Both',
  `is_multi_metal` int NOT NULL DEFAULT '0' COMMENT '0-> No 1-> Yes (Multiple metal possible on receipt)',
  `process_from` int NOT NULL DEFAULT '0' COMMENT '0-> Origin 1-> From other process Id (Start from which process id)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active,0-inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_metal_process`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_process_payment`
--

DROP TABLE IF EXISTS `ret_old_metal_process_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_process_payment` (
  `id_old_metal_process_amount` int NOT NULL AUTO_INCREMENT,
  `id_old_metal_process` int NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Credit, 2 - Debit',
  `NB_type` int DEFAULT NULL COMMENT '1-RTGS,2-IMPS',
  `payment_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Sale, 2 - Advance',
  `payment_date` datetime DEFAULT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `payment_amount` decimal(10,0) DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `remark` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id_old_metal_process_amount`),
  KEY `id_old_metal_process_amount` (`id_old_metal_process_amount`),
  KEY `bill_id` (`id_old_metal_process`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_rate`
--

DROP TABLE IF EXISTS `ret_old_metal_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_rate` (
  `id_old_metal_rate` int NOT NULL AUTO_INCREMENT,
  `id_metal` int unsigned DEFAULT NULL,
  `id_purity` int unsigned DEFAULT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_old_metal_rate`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_refining`
--

DROP TABLE IF EXISTS `ret_old_metal_refining`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_refining` (
  `id_metal_refining` int NOT NULL AUTO_INCREMENT,
  `id_old_metal_process` int NOT NULL,
  `id_melting` int DEFAULT NULL,
  `id_metal_testing` int DEFAULT NULL,
  `id_melting_recd` int NOT NULL COMMENT 'From melting recd table',
  `issue_weight` decimal(10,3) DEFAULT NULL,
  `id_old_metal_process_receipt` int DEFAULT NULL,
  `vocuher_no` varchar(30) DEFAULT NULL,
  `receipt_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `receipt_charges_tax_perc` float(10,2) DEFAULT NULL,
  `receipt_charges_tax_amt` float(10,2) DEFAULT NULL,
  `receipt_charges_tax_cgst` float(10,2) DEFAULT NULL,
  `receipt_charges_tax_sgst` float(10,2) DEFAULT NULL,
  `receipt_charges_tax_igst` float(10,2) DEFAULT NULL,
  `receipt_ref_no` text,
  `refining_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Given for Refining,1-Refining Completed',
  `charges` decimal(10,2) DEFAULT '0.00',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_metal_refining`),
  KEY `id_old_metal_process` (`id_old_metal_process`),
  KEY `id_melting` (`id_melting`),
  KEY `id_metal_testing` (`id_metal_testing`),
  KEY `id_old_metal_process_receipt` (`id_old_metal_process_receipt`),
  KEY `refining_status` (`refining_status`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_refining_details`
--

DROP TABLE IF EXISTS `ret_old_metal_refining_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_refining_details` (
  `id_refining_details` int NOT NULL AUTO_INCREMENT,
  `id_metal_refining` int NOT NULL,
  `received_category` int NOT NULL,
  `id_section` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `purity` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `received_wt` decimal(10,3) NOT NULL,
  `piece` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  PRIMARY KEY (`id_refining_details`),
  KEY `id_metal_refining` (`id_metal_refining`),
  KEY `received_category` (`received_category`),
  KEY `id_product` (`id_product`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_stock_log`
--

DROP TABLE IF EXISTS `ret_old_metal_stock_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_stock_log` (
  `id_old_metal_log` int NOT NULL AUTO_INCREMENT,
  `old_metal_sale_id` int DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1-inward,2-in transit,3-out ward',
  `from_branch` int DEFAULT NULL,
  `to_branch` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id_old_metal_log`),
  KEY `old_metal_sale_id` (`old_metal_sale_id`),
  KEY `status` (`status`),
  KEY `from_branch` (`from_branch`),
  KEY `to_branch` (`to_branch`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_testing`
--

DROP TABLE IF EXISTS `ret_old_metal_testing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_testing` (
  `id_metal_testing` int NOT NULL AUTO_INCREMENT,
  `id_old_metal_process` int NOT NULL,
  `id_melting` int NOT NULL,
  `id_melting_recd` int DEFAULT NULL,
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `purity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_old_metal_process_receipt` int DEFAULT NULL,
  `received_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `production_loss` decimal(10,3) NOT NULL DEFAULT '0.000',
  `received_purity` decimal(10,2) DEFAULT NULL,
  `vocuher_no` varchar(30) DEFAULT NULL,
  `receipt_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `receipt_ref_no` text,
  `testing_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Given for Testing,1-Testing Completed,2-Testing Completed and Added to Acc Stock',
  `image` varchar(20) DEFAULT NULL,
  `charges` decimal(10,2) DEFAULT '0.00',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_metal_testing`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_metal_type`
--

DROP TABLE IF EXISTS `ret_old_metal_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_metal_type` (
  `id_metal_type` int NOT NULL AUTO_INCREMENT,
  `id_metal` int DEFAULT NULL,
  `metal_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_metal_type`),
  KEY `id_metal` (`id_metal`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_old_tag_import`
--

DROP TABLE IF EXISTS `ret_old_tag_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_old_tag_import` (
  `import_id` int NOT NULL AUTO_INCREMENT,
  `import_tag_code` varchar(15) DEFAULT NULL COMMENT 'Old Tag code from existing s/w',
  `tran_no` text,
  `import_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `import_branch` int NOT NULL COMMENT 'Import happen branch',
  `import_tag_status` int NOT NULL COMMENT '1-> Updated 2->Mismatch',
  `import_by` int NOT NULL COMMENT 'Imported by Employee Id',
  PRIMARY KEY (`import_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_order_email_logs`
--

DROP TABLE IF EXISTS `ret_order_email_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_order_email_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_customerorder` int NOT NULL,
  `id_karigar` int NOT NULL,
  `email_id` varchar(255) DEFAULT NULL,
  `token` varchar(100) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Not Sent, 1: Sent, 2: Failed, 3: Accepted',
  `due_date` date DEFAULT NULL,
  `error_msg` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `accepted_at` datetime DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `rejection_reason` text,
  PRIMARY KEY (`id`),
  KEY `id_customerorder` (`id_customerorder`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_order_item_stones`
--

DROP TABLE IF EXISTS `ret_order_item_stones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_order_item_stones` (
  `order_item_stone_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `order_item_id` int NOT NULL,
  `stone_type` int DEFAULT NULL,
  `stone_id` int DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `wt` decimal(12,4) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `is_apply_in_lwt` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Stone wt applied in LWT=>1-Yes,0-No',
  `stone_cal_type` tinyint NOT NULL DEFAULT '1' COMMENT '1-> By weight 2->By pcs',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`order_item_stone_id`),
  KEY `order_id` (`order_id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `stone_type` (`stone_type`),
  KEY `stone_id` (`stone_id`),
  KEY `uom_id` (`uom_id`),
  KEY `stone_cal_type` (`stone_cal_type`)
) ENGINE=InnoDB AUTO_INCREMENT=776 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_order_other_charges`
--

DROP TABLE IF EXISTS `ret_order_other_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_order_other_charges` (
  `id_order_charge` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `order_item_id` int DEFAULT NULL,
  `id_charge` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_order_charge`),
  KEY `order_id` (`order_id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `id_charge` (`id_charge`)
) ENGINE=InnoDB AUTO_INCREMENT=449 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_order_other_metals`
--

DROP TABLE IF EXISTS `ret_order_other_metals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_order_other_metals` (
  `order_other_itm_id` int NOT NULL AUTO_INCREMENT,
  `order_detail_id` int NOT NULL,
  `tag_other_itm_metal_id` int NOT NULL,
  `tag_other_itm_pur_id` int DEFAULT NULL,
  `tag_other_itm_grs_weight` decimal(10,3) NOT NULL,
  `tag_other_itm_wastage` decimal(10,2) DEFAULT NULL,
  `tag_other_itm_uom` int NOT NULL,
  `tag_other_itm_cal_type` int NOT NULL COMMENT '1-> Weight 2->pcs',
  `tag_other_itm_mc` decimal(10,2) NOT NULL,
  `tag_other_itm_rate` decimal(10,2) NOT NULL,
  `tag_other_itm_pcs` int NOT NULL,
  `tag_other_itm_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_other_itm_id`),
  KEY `order_detail_id` (`order_detail_id`),
  KEY `tag_other_itm_pur_id` (`tag_other_itm_pur_id`),
  KEY `tag_other_itm_metal_id` (`tag_other_itm_metal_id`),
  KEY `tag_other_itm_uom` (`tag_other_itm_uom`),
  KEY `tag_other_itm_cal_type` (`tag_other_itm_cal_type`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_item`
--

DROP TABLE IF EXISTS `ret_other_inventory_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_item` (
  `id_other_item` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `short_code` varchar(20) DEFAULT NULL,
  `id_inv_size` int NOT NULL,
  `purchase_id_uom` int DEFAULT NULL,
  `stock_id_uom` int DEFAULT NULL,
  `issue_preference` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-> FIFO ,2 -> FILO',
  `item_image` varchar(50) DEFAULT NULL,
  `qr_image` varchar(25) DEFAULT NULL,
  `sku_id` varchar(20) DEFAULT NULL,
  `item_for` tinyint(1) DEFAULT NULL COMMENT '1-General Items,2-Packing Items,3-Gift Items',
  `is_tax_applicable` int NOT NULL DEFAULT '0' COMMENT '0-> No 1->Yes',
  `item_tax_group_id` int DEFAULT NULL,
  `item_hsn_code` varchar(15) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `item_minimum_required` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `issue_to` int NOT NULL COMMENT '0 - All  1 - Chit  2 - Retail',
  PRIMARY KEY (`id_other_item`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_item_type`
--

DROP TABLE IF EXISTS `ret_other_inventory_item_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_item_type` (
  `id_other_item_type` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `outward_type` int NOT NULL DEFAULT '1' COMMENT '1-> Billing 2->Issue',
  `asbillable` int NOT NULL DEFAULT '0' COMMENT '0-> Free 1-> Cost',
  `expirydatevalidate` int NOT NULL DEFAULT '0' COMMENT '0-> No validity 1-> Having',
  `reorderlevel` int NOT NULL DEFAULT '0' COMMENT '1-> Yes 2->No',
  `qrcode` int NOT NULL DEFAULT '0' COMMENT '0-> No 1->Yes',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active,0-Inactive',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_On` datetime DEFAULT NULL,
  PRIMARY KEY (`id_other_item_type`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_product_link`
--

DROP TABLE IF EXISTS `ret_other_inventory_product_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_product_link` (
  `inv_des_id` int NOT NULL AUTO_INCREMENT,
  `inv_pro_id` int NOT NULL,
  `inv_des_otheritemid` int NOT NULL,
  `inv_des_created_by` int NOT NULL,
  `inv_des_created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`inv_des_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1667 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_purchase`
--

DROP TABLE IF EXISTS `ret_other_inventory_purchase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_purchase` (
  `otr_inven_pur_id` int NOT NULL AUTO_INCREMENT,
  `otr_inven_pur_supplier` int NOT NULL,
  `entry_date` datetime DEFAULT NULL,
  `otr_inven_pur_order_ref` varchar(20) DEFAULT NULL,
  `supplier_order_ref_no` varchar(50) DEFAULT NULL,
  `supplier_bill_date` date DEFAULT NULL,
  `otr_inven_pur_order_created_on` date DEFAULT NULL,
  `otr_inven_pur_created_on` datetime NOT NULL,
  `otr_inven_pur_created_by` int DEFAULT NULL,
  `otr_inven_pur_updated_by` int DEFAULT NULL,
  `otr_inven_pur_updated_on` datetime DEFAULT NULL,
  `purchase_bill_status` tinyint NOT NULL DEFAULT '1' COMMENT '1-Success 2 - Cancelled',
  `cancel_reason` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`otr_inven_pur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_purchase_images`
--

DROP TABLE IF EXISTS `ret_other_inventory_purchase_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_purchase_images` (
  `id_purchase_img` int unsigned NOT NULL AUTO_INCREMENT,
  `otr_inven_pur_id` int NOT NULL,
  `image` text,
  `date_add` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_default` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  PRIMARY KEY (`id_purchase_img`),
  KEY `otr_inven_pur_id` (`otr_inven_pur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_purchase_items`
--

DROP TABLE IF EXISTS `ret_other_inventory_purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_purchase_items` (
  `inv_pur_itm_id` int NOT NULL AUTO_INCREMENT,
  `otr_inven_pur_id` int DEFAULT NULL,
  `inv_pur_itm_itemid` int DEFAULT NULL COMMENT 'From  ret_other_inventory_item table',
  `inv_pur_itm_qty` decimal(10,3) DEFAULT NULL,
  `inv_pur_itm_uom` int DEFAULT NULL,
  `inv_pur_itm_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inv_pur_itm_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inv_pur_itm_gst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gst_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`inv_pur_itm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_purchase_items_details`
--

DROP TABLE IF EXISTS `ret_other_inventory_purchase_items_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_purchase_items_details` (
  `pur_item_detail_id` int NOT NULL AUTO_INCREMENT,
  `inv_pur_itm_id` int DEFAULT NULL,
  `other_invnetory_item_id` int DEFAULT NULL,
  `item_ref_no` varchar(20) NOT NULL,
  `ref_no` varchar(45) DEFAULT NULL,
  `piece` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `current_branch` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0-on sale,1-Sold,2-Deleted,3-Other Issue,4-Intransit',
  `id_inventory_issue` int DEFAULT NULL COMMENT 'for Sold items',
  PRIMARY KEY (`pur_item_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1520 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_purchase_items_log`
--

DROP TABLE IF EXISTS `ret_other_inventory_purchase_items_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_purchase_items_log` (
  `id_item_log` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL COMMENT 'From ret_other_inventory_item',
  `date` datetime DEFAULT NULL,
  `status` tinyint DEFAULT NULL COMMENT '0-inward,1-outward,3-other issue,4-Intransit',
  `no_of_pieces` int NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `from_branch` int DEFAULT NULL,
  `to_branch` int DEFAULT NULL,
  `id_inventory_issue` int DEFAULT NULL COMMENT 'for Sold items',
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  UNIQUE KEY `id_item_log` (`id_item_log`)
) ENGINE=InnoDB AUTO_INCREMENT=1661 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_reorder_settings`
--

DROP TABLE IF EXISTS `ret_other_inventory_reorder_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_reorder_settings` (
  `id_inv_reorder_settings` int NOT NULL AUTO_INCREMENT,
  `id_branch` int NOT NULL,
  `id_other_item` int NOT NULL,
  `min_pcs` int NOT NULL,
  `max_pcs` int NOT NULL,
  PRIMARY KEY (`id_inv_reorder_settings`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_size`
--

DROP TABLE IF EXISTS `ret_other_inventory_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_size` (
  `id_inv_size` int NOT NULL AUTO_INCREMENT,
  `size_name` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No',
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_inv_size`),
  UNIQUE KEY `size_name` (`size_name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_inventory_stock`
--

DROP TABLE IF EXISTS `ret_other_inventory_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_inventory_stock` (
  `id_stock` int NOT NULL AUTO_INCREMENT,
  `id_other_item` int DEFAULT NULL,
  `date` date NOT NULL,
  `id_branch` int NOT NULL,
  `op_blc_pcs` int DEFAULT NULL,
  `op_blc_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inw_pcs` int DEFAULT NULL,
  `inw_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `out_pcs` int DEFAULT NULL,
  `out_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_pcs` int DEFAULT NULL,
  `closing_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_stock`),
  UNIQUE KEY `date` (`date`,`id_other_item`,`id_branch`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_other_invnetory_issue`
--

DROP TABLE IF EXISTS `ret_other_invnetory_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_other_invnetory_issue` (
  `id_inventory_issue` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `issue_date` datetime DEFAULT NULL,
  `id_other_item` int NOT NULL,
  `issue_form` tinyint DEFAULT NULL COMMENT '1-From Billing,2-General Issue',
  `bill_id` int DEFAULT NULL,
  `cus_id` int DEFAULT NULL,
  `no_of_pieces` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `remarks` text,
  PRIMARY KEY (`id_inventory_issue`),
  KEY `id_branch` (`id_branch`),
  KEY `issue_date` (`issue_date`),
  KEY `id_other_item` (`id_other_item`),
  KEY `issue_form` (`issue_form`),
  KEY `bill_id` (`bill_id`),
  KEY `cus_id` (`cus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=303 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_partlysold`
--

DROP TABLE IF EXISTS `ret_partlysold`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_partlysold` (
  `id_rep_partlysale` int NOT NULL AUTO_INCREMENT,
  `tag_id` int DEFAULT NULL,
  `product` int DEFAULT NULL,
  `design` int DEFAULT NULL,
  `sold_gross_wt` decimal(12,4) DEFAULT NULL,
  `sold_less_wt` decimal(12,4) DEFAULT NULL,
  `sold_net_wt` decimal(12,4) DEFAULT NULL,
  `blc_gross_wt` decimal(10,3) DEFAULT NULL,
  `blc_less_wt` decimal(10,3) DEFAULT NULL,
  `blc_net_wt` decimal(10,3) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1 - Available, 0 - Sold',
  `sold_bill_det_id` int NOT NULL,
  PRIMARY KEY (`id_rep_partlysale`),
  KEY `tag_id` (`tag_id`),
  KEY `product` (`product`),
  KEY `design` (`design`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=760 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_bill_payment_details`
--

DROP TABLE IF EXISTS `ret_po_bill_payment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_bill_payment_details` (
  `pay_bill_id` int NOT NULL,
  `pay_id` int NOT NULL,
  `po_id` int NOT NULL,
  `opening_id` int NOT NULL,
  `id_supplier_rate_cut` int NOT NULL,
  `bill_amount` double(15,2) NOT NULL,
  `total_amount` double(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_halmark_process`
--

DROP TABLE IF EXISTS `ret_po_halmark_process`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_halmark_process` (
  `hm_process_id` int NOT NULL AUTO_INCREMENT,
  `hm_ref_no` varchar(40) NOT NULL,
  `hm_po_item_id` int DEFAULT NULL,
  `hm_vendor_ref_id` varchar(20) DEFAULT NULL,
  `hm_vendor_id` int NOT NULL,
  `hm_process_pcs` int NOT NULL,
  `hm_process_gwt` decimal(10,3) NOT NULL,
  `hm_process_lwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `hm_process_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Issue,2-HM Completed',
  `total_hm_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hm_process_narration` text,
  `hm_process_created_by` int NOT NULL,
  `hm_process_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hm_received_at` datetime DEFAULT NULL,
  PRIMARY KEY (`hm_process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_hm_issue_stone_details`
--

DROP TABLE IF EXISTS `ret_po_hm_issue_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_hm_issue_stone_details` (
  `ret_hm_issue_stn_id` int NOT NULL AUTO_INCREMENT,
  `ret_hm_receipt_id` int DEFAULT NULL,
  `po_st_id` int DEFAULT NULL,
  `stone_pcs` int DEFAULT NULL,
  `stone_wt` decimal(10,3) DEFAULT NULL,
  `hm_rejected_pcs` int DEFAULT NULL,
  `hm_rejected_wt` decimal(10,3) DEFAULT NULL,
  `hm_passed_pcs` int DEFAULT NULL,
  `hm_passed_wt` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`ret_hm_issue_stn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_hm_process_details`
--

DROP TABLE IF EXISTS `ret_po_hm_process_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_hm_process_details` (
  `hm_receipt_id` int NOT NULL AUTO_INCREMENT,
  `hm_issue_id` int NOT NULL,
  `hm_po_item_id` int DEFAULT NULL,
  `hm_po_qc_issue_id` int DEFAULT NULL,
  `hm_issue_pcs` int DEFAULT NULL,
  `hm_issue_gwt` decimal(10,3) DEFAULT NULL,
  `hm_issue_nwt` decimal(10,3) DEFAULT NULL,
  `hm_issue_lwt` decimal(10,3) NOT NULL,
  `hm_received_by` int DEFAULT NULL,
  `hm_received_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hm_received_certificate` varchar(300) DEFAULT NULL,
  `hm_failed_pcs` int DEFAULT NULL,
  `hm_failed_gwt` decimal(10,3) DEFAULT NULL,
  `hm_failed_nwt` decimal(10,3) DEFAULT NULL,
  `hm_failed_lwt` decimal(10,3) DEFAULT NULL,
  `hm_passed_pcs` int DEFAULT NULL,
  `hm_passed_gwt` decimal(10,3) DEFAULT NULL,
  `hm_passed_nwt` decimal(10,3) DEFAULT NULL,
  `hm_passed_lwt` decimal(10,3) DEFAULT NULL,
  `hm_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -> no , 1-> yes',
  PRIMARY KEY (`hm_receipt_id`),
  KEY `idxHMIssueid` (`hm_issue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_other_item`
--

DROP TABLE IF EXISTS `ret_po_other_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_other_item` (
  `po_other_item_id` int NOT NULL AUTO_INCREMENT,
  `po_item_id` int NOT NULL,
  `po_item_metal` int NOT NULL,
  `po_other_item_gross_weight` decimal(10,3) NOT NULL,
  `po_other_item_uom` int DEFAULT NULL,
  `po_other_item_purity` int NOT NULL,
  `po_other_item_cal_type` int DEFAULT NULL,
  `po_other_item_rate` decimal(10,2) NOT NULL,
  `po_other_item_pcs` int NOT NULL,
  `po_other_item_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`po_other_item_id`),
  KEY `Idxpoitemid_oritem` (`po_item_id`),
  KEY `Idxpoitemstid_oritem` (`po_item_metal`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_payment`
--

DROP TABLE IF EXISTS `ret_po_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_payment` (
  `pay_id` int NOT NULL AUTO_INCREMENT,
  `pay_sup_id` int NOT NULL,
  `pay_refno` varchar(20) NOT NULL,
  `bill_type` tinyint(1) DEFAULT NULL COMMENT '1-For Bill,2-For REceipt 3-> For Approval',
  `pay_amt` decimal(10,2) unsigned NOT NULL,
  `pay_wt` decimal(10,3) unsigned DEFAULT NULL,
  `pay_narration` varchar(300) DEFAULT NULL,
  `pay_status` int NOT NULL DEFAULT '1' COMMENT '1-> Live 2->Cancel',
  `pay_created_by` int NOT NULL,
  `pay_create_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `cancel_reason` text,
  `cancelled_date` datetime DEFAULT NULL,
  `cancelled_by` int DEFAULT NULL,
  `id_supplier_rate_cut` int DEFAULT NULL COMMENT 'From ret_supplier_rate_cut table',
  PRIMARY KEY (`pay_id`),
  KEY `pay_sup_id` (`pay_sup_id`),
  KEY `bill_type` (`bill_type`),
  KEY `pay_status` (`pay_status`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_payment_detail`
--

DROP TABLE IF EXISTS `ret_po_payment_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_payment_detail` (
  `id_pay_details` int NOT NULL AUTO_INCREMENT,
  `pay_id` int NOT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '1-Net Banking Payment,2-Sales Adj,3-Advance Adj,4-Cash Payment',
  `pay_mode` varchar(20) DEFAULT NULL,
  `payment_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ref_no` text,
  `bill_id` int DEFAULT NULL,
  `id_bank` int DEFAULT NULL,
  `ref_date` datetime DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `payee_name` varchar(50) NOT NULL,
  UNIQUE KEY `id_pay_details` (`id_pay_details`)
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_payment_grns`
--

DROP TABLE IF EXISTS `ret_po_payment_grns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_payment_grns` (
  `ret_pay_grnpay_id` int NOT NULL AUTO_INCREMENT,
  `ret_pay_grn_id` int NOT NULL,
  `ret_pay_amount` decimal(10,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`ret_pay_grnpay_id`),
  KEY `Idx_grn_payid` (`ret_pay_grn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_qc_issue_details`
--

DROP TABLE IF EXISTS `ret_po_qc_issue_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_qc_issue_details` (
  `id_qc_issue_details` int NOT NULL AUTO_INCREMENT,
  `qc_process_id` int NOT NULL,
  `po_item_id` int NOT NULL,
  `issue_pcs` int NOT NULL DEFAULT '0',
  `issue_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_lwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `failed_pcs` int NOT NULL DEFAULT '0',
  `failed_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `failed_lwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `failed_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `passed_pcs` int NOT NULL DEFAULT '0',
  `passed_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `passed_lwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `passed_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-QC Issue,1-Completed',
  `is_lot_created` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `lot_no` int DEFAULT NULL,
  PRIMARY KEY (`id_qc_issue_details`),
  KEY `status` (`status`),
  KEY `qc_process_id` (`qc_process_id`),
  KEY `po_item_id` (`po_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1072 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_qc_issue_process`
--

DROP TABLE IF EXISTS `ret_po_qc_issue_process`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_qc_issue_process` (
  `qc_process_id` int NOT NULL AUTO_INCREMENT,
  `qc_id_vendor` int NOT NULL,
  `date_add` datetime DEFAULT NULL,
  `ref_no` varchar(20) NOT NULL,
  `issue_pcs` int DEFAULT NULL,
  `issue_gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `cancel_reason` varchar(50) DEFAULT NULL,
  `cancelled_by` int NOT NULL,
  `cancelled_on` datetime DEFAULT NULL,
  `qc_status` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `qc_process_id` (`qc_process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=730 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_qc_issue_stone_details`
--

DROP TABLE IF EXISTS `ret_po_qc_issue_stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_qc_issue_stone_details` (
  `ret_qc_issue_stn_id` int NOT NULL AUTO_INCREMENT,
  `id_qc_issue_details` int NOT NULL,
  `po_st_id` int DEFAULT NULL COMMENT 'From PO Stone Items Table',
  `stone_pcs` int DEFAULT NULL,
  `stone_wt` decimal(10,3) NOT NULL,
  `qc_rejected_pcs` int NOT NULL DEFAULT '0',
  `qc_rejected_wt` decimal(10,3) NOT NULL,
  `qc_passed_pcs` int NOT NULL DEFAULT '0',
  `qc_passed_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`ret_qc_issue_stn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=688 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_rate_fix`
--

DROP TABLE IF EXISTS `ret_po_rate_fix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_rate_fix` (
  `rate_fix_id` int NOT NULL AUTO_INCREMENT,
  `rate_fix_po_item_id` int DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-NO,1-Yes',
  `rate_fix_type` int NOT NULL COMMENT '1-> Normal Rate Fixing 2->Approval Rate fixing',
  `id_approval_ratecut` int DEFAULT NULL COMMENT 'From supplier rate cut  Master',
  `rate_fix_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rate_fix_wt` decimal(10,3) DEFAULT NULL,
  `rate_fix_rate` decimal(10,2) DEFAULT NULL,
  `tax_group_id` int NOT NULL,
  `total_tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rate_fix_created_from` int NOT NULL DEFAULT '1' COMMENT '1-> Rate Fix form 2-> From PO itself for multi metal',
  `rate_fix_create_by` int NOT NULL,
  `rate_fix_created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rate_fix_approved_by` int DEFAULT NULL,
  `rate_fix_approved_on` datetime DEFAULT NULL,
  `approved_through` tinyint(1) DEFAULT NULL COMMENT '1 -> WEB\r\n2 -> APP',
  `approved_by` int DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `bill_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No',
  `remark` varchar(75) DEFAULT NULL,
  `cancelled_by` int DEFAULT NULL,
  `cancelled_on` datetime DEFAULT NULL,
  `cancel_reason` text,
  `cancel_through` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> WEB\r\n2 -> APP',
  PRIMARY KEY (`rate_fix_id`)
) ENGINE=MyISAM AUTO_INCREMENT=199 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_po_stone_items`
--

DROP TABLE IF EXISTS `ret_po_stone_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_po_stone_items` (
  `po_st_id` int NOT NULL AUTO_INCREMENT,
  `po_item_id` int NOT NULL,
  `po_stone_id` int NOT NULL,
  `is_apply_in_lwt` int NOT NULL DEFAULT '0' COMMENT 'Stone wt applied in LWT=>1-Yes,0-No',
  `po_stone_pcs` int NOT NULL,
  `po_stone_wt` decimal(10,3) NOT NULL,
  `po_stone_uom` int NOT NULL,
  `po_stone_calc_based_on` int DEFAULT NULL,
  `po_quality_id` int DEFAULT NULL,
  `po_stone_cut` int DEFAULT NULL,
  `po_stone_color` int DEFAULT NULL,
  `po_stone_clarity` int DEFAULT NULL,
  `po_stone_shape` int DEFAULT NULL,
  `po_stone_sievesize` decimal(10,2) DEFAULT NULL,
  `po_stone_rate` decimal(10,2) DEFAULT NULL,
  `po_stone_amount` decimal(10,2) DEFAULT NULL,
  `po_stone_rejected_pcs` int NOT NULL DEFAULT '0',
  `po_stone_rejected_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `po_stone_accepted_pcs` int NOT NULL DEFAULT '0',
  `po_stone_accepted_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `issue_met_id` int DEFAULT NULL COMMENT 'id from ret_karigar_metal_issue_details table',
  PRIMARY KEY (`po_st_id`),
  KEY `Idxstid_po_stone` (`po_item_id`),
  KEY `Idxststoneid_po_stone` (`po_stone_id`)
) ENGINE=MyISAM AUTO_INCREMENT=990 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_product_charges`
--

DROP TABLE IF EXISTS `ret_product_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_product_charges` (
  `prod_charge_id` int NOT NULL AUTO_INCREMENT,
  `prod_id` int NOT NULL,
  `charge_id` int unsigned NOT NULL,
  `charge_value` decimal(12,2) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`prod_charge_id`),
  KEY `FK_ProductId` (`prod_id`),
  KEY `FK_ChargeId` (`charge_id`)
) ENGINE=InnoDB AUTO_INCREMENT=349 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_product_division`
--

DROP TABLE IF EXISTS `ret_product_division`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_product_division` (
  `id_pro_division` int NOT NULL AUTO_INCREMENT,
  `div_value` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 -> Active, 0 -> Inactive',
  `created_on` datetime NOT NULL,
  `created_by` int NOT NULL COMMENT 'Reference Employee Master',
  `updated_on` datetime NOT NULL,
  `updated_by` int NOT NULL COMMENT 'Reference Employee Master',
  PRIMARY KEY (`id_pro_division`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_product_grouping`
--

DROP TABLE IF EXISTS `ret_product_grouping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_product_grouping` (
  `id_prod_group` int unsigned NOT NULL AUTO_INCREMENT,
  `group_metal` int unsigned NOT NULL COMMENT 'Refer Metal Master',
  `group_name` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `sort` int DEFAULT NULL COMMENT 'Sorting based number',
  `created_on` datetime NOT NULL,
  `created_by` int NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_prod_group`),
  KEY `fk_id_metal` (`group_metal`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_product_mapping`
--

DROP TABLE IF EXISTS `ret_product_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_product_mapping` (
  `mapping_id` int NOT NULL AUTO_INCREMENT,
  `pro_id` int NOT NULL COMMENT 'From Product Master',
  `id_design` int DEFAULT NULL COMMENT 'From Design Master',
  PRIMARY KEY (`mapping_id`),
  KEY `pro_id` (`pro_id`),
  KEY `id_design` (`id_design`)
) ENGINE=MyISAM AUTO_INCREMENT=765 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_product_master`
--

DROP TABLE IF EXISTS `ret_product_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_product_master` (
  `pro_id` int NOT NULL AUTO_INCREMENT,
  `cat_id` int unsigned NOT NULL,
  `tgrp_id` int DEFAULT NULL COMMENT 'For Sales',
  `job_work_receipt_tgrp_id` int DEFAULT NULL COMMENT 'For job work receipt',
  `id_section` int DEFAULT NULL,
  `hsn_code` varchar(45) DEFAULT NULL COMMENT 'No need can get from category',
  `stock_type` int DEFAULT NULL COMMENT '(Tagged / Non Tagged) 1.Tagged 2.NonTagged',
  `stone_type` tinyint(1) DEFAULT '0' COMMENT '1-Stone,2-Diamond',
  `sales_mode` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1 - Fixed Rate, 2 - Flexible',
  `purchase_mode` int NOT NULL DEFAULT '2' COMMENT '1-> Fixed 2-> Flexi ',
  `disc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1->From final price,2-From V.A and MC',
  `wastage_type` tinyint(1) DEFAULT '2' COMMENT '1.Fixed 2.Product base',
  `min_wastage` decimal(10,2) DEFAULT NULL,
  `max_wastage` decimal(10,2) DEFAULT NULL,
  `other_materials` int DEFAULT NULL COMMENT '1-> yes  0->No',
  `has_stone` int DEFAULT NULL COMMENT '1-> yes  0->No',
  `has_hook` int DEFAULT NULL COMMENT '1-> yes  0->No',
  `has_screw` int DEFAULT NULL COMMENT '1-> yes  0->No',
  `has_fixed_price` int DEFAULT NULL COMMENT '1-> yes  0->No',
  `metal_type` int DEFAULT NULL COMMENT 'From master',
  `product_short_code` varchar(3) NOT NULL,
  `product_name` varchar(75) DEFAULT NULL,
  `has_size` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `less_stone_wt` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `tag_split` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `tag_merge` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `tag_type` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `other_charges` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `net_wt` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `stock_report` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `central_exces_duty` int DEFAULT NULL COMMENT '1-> Yes  0->No',
  `no_of_pieces` int DEFAULT NULL,
  `rfid_required` int DEFAULT NULL COMMENT '1-> Yes  0-> No',
  `rfid_in_stock` int DEFAULT NULL COMMENT '1-> Yes  0-> No',
  `hallmark` int DEFAULT NULL COMMENT '1-> Yes  0-> No',
  `counter` int DEFAULT NULL COMMENT '1-> KDM 2-> Non KDM',
  `stone_board_rate_cal` int DEFAULT NULL COMMENT '1-> Yes  0-> No',
  `calculation_based_on` int NOT NULL DEFAULT '0' COMMENT '0-> Mc & Wast On Gross, 1 -> Mc & Wast On Net, 2 ->Mc on Gross,Wast On Net, 3-> Fixed Rate, 4-> Fixed Rate based on Weight',
  `sales_markup` int DEFAULT NULL COMMENT '1-> Yes  0-> No',
  `max_markup_per_for_rateitems` decimal(10,2) DEFAULT NULL,
  `no_of_tags_to_print` int DEFAULT NULL,
  `tax_group_id` int DEFAULT NULL COMMENT 'From tax group master (For Purchase)',
  `tax_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '(Sales)1-Inclusive,2-Exclusive',
  `reorder_based_on` int NOT NULL DEFAULT '1' COMMENT '1.Weight Range 2.Size',
  `purchase_tax_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1-Inclusive,2-Exclusive',
  `product_status` int DEFAULT '1' COMMENT '0-> Inactive 1-> Active',
  `image` varchar(125) DEFAULT NULL,
  `weight_range_based` tinyint NOT NULL DEFAULT '0' COMMENT '0-product,1-Design',
  `gift_applicable` int NOT NULL DEFAULT '0' COMMENT 'Gift Applicable For Product',
  `display_purity` tinyint(1) DEFAULT '1' COMMENT '1 -> Display purity, 2 -> Do Not Display',
  `create_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `prod_group_id` int DEFAULT NULL,
  PRIMARY KEY (`pro_id`),
  UNIQUE KEY `product_short_code` (`product_short_code`),
  UNIQUE KEY `product_name` (`product_name`),
  KEY `cat_id` (`cat_id`),
  KEY `id_section` (`id_section`),
  KEY `sales_mode` (`sales_mode`),
  KEY `stock_type` (`stock_type`),
  KEY `metal_type` (`metal_type`),
  KEY `calculation_based_on` (`calculation_based_on`),
  KEY `product_status` (`product_status`)
) ENGINE=InnoDB AUTO_INCREMENT=888 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_product_section`
--

DROP TABLE IF EXISTS `ret_product_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_product_section` (
  `id_pro_section` int NOT NULL AUTO_INCREMENT,
  `pro_id` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  PRIMARY KEY (`id_pro_section`),
  UNIQUE KEY `product id` (`pro_id`,`id_section`)
) ENGINE=InnoDB AUTO_INCREMENT=24146 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_product_sub_product`
--

DROP TABLE IF EXISTS `ret_product_sub_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_product_sub_product` (
  `pro_sub_product_id` int NOT NULL,
  `pro_id` int DEFAULT NULL,
  `sub_pro_id` int DEFAULT NULL COMMENT 'From sub product table'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_product_weight`
--

DROP TABLE IF EXISTS `ret_product_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_product_weight` (
  `id_product_weight` int NOT NULL AUTO_INCREMENT,
  `id_weight` int NOT NULL,
  `id_product` int NOT NULL,
  `id_deisgn` int NOT NULL,
  `id_sub_deisgn` int NOT NULL,
  PRIMARY KEY (`id_product_weight`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_approval_tags`
--

DROP TABLE IF EXISTS `ret_purchase_approval_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_approval_tags` (
  `po_aprl_tag_refid` int NOT NULL AUTO_INCREMENT,
  `po_aprl_tag_tag_id` int NOT NULL,
  `po_aprl_tag_poid` int NOT NULL,
  PRIMARY KEY (`po_aprl_tag_refid`),
  KEY `Idxtagapprovalid` (`po_aprl_tag_tag_id`),
  KEY `Idxpoapprovapoid` (`po_aprl_tag_poid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_item_stock`
--

DROP TABLE IF EXISTS `ret_purchase_item_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_item_stock` (
  `id_metal_stock` int NOT NULL AUTO_INCREMENT,
  `stock_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Old Metal,1-Bullion Purchase,2-Sales Return,3-Partly sale,4-Old Metal Process',
  `is_opening` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `id_branch` int DEFAULT NULL,
  `date` date NOT NULL,
  `id_product` int DEFAULT NULL,
  `id_ret_category` int DEFAULT NULL,
  `id_old_metal_type` int DEFAULT NULL,
  `op_blc_pcs` int DEFAULT NULL,
  `op_blc_gwt` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Opening Blc Weight',
  `op_blc_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `op_blc_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Opening Blc Amount',
  `op_avg_purity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inw_pcs` int DEFAULT NULL,
  `inw_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `inw_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `inw_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inw_purity` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Average Purity',
  `out_ward_pcs` int DEFAULT NULL,
  `outward_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `outward_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `outward_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `outward_purity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_pcs` int DEFAULT NULL,
  `closing_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `closing_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `closing_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_purity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id_metal_stock`),
  KEY `stock_type` (`stock_type`),
  KEY `is_opening` (`is_opening`),
  KEY `id_branch` (`id_branch`),
  KEY `id_product` (`id_product`),
  KEY `id_ret_category` (`id_ret_category`),
  KEY `id_old_metal_type` (`id_old_metal_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_item_stock_summary`
--

DROP TABLE IF EXISTS `ret_purchase_item_stock_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_item_stock_summary` (
  `id_stock_summary` int NOT NULL AUTO_INCREMENT,
  `type` int NOT NULL COMMENT '0-Old Metal Process Refine,1-Bullion Purchase,2-Sales Return,3-Partly Sale,4-Old Metal Direct Stock,5-Old Metal Process Stone',
  `id_branch` int DEFAULT NULL,
  `id_ret_category` int DEFAULT NULL,
  `purity` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT 'Stock Based on Purity wise',
  `id_product` int DEFAULT NULL,
  `pieces` int NOT NULL DEFAULT '0',
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_stock_summary`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_item_stock_summary_log`
--

DROP TABLE IF EXISTS `ret_purchase_item_stock_summary_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_item_stock_summary_log` (
  `id_summary_log` int NOT NULL AUTO_INCREMENT,
  `date_add` datetime DEFAULT NULL,
  `id_stock_summary` int NOT NULL,
  `credit_type` int DEFAULT NULL COMMENT '0-Opening Balance,1-From Bullion Purchase,2-From Old Refining Metal Process ',
  `debit_type` int DEFAULT NULL COMMENT '1-From Supplier Sales Bill',
  `transcation_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Credit,1-Debit',
  `ref_no` int DEFAULT NULL,
  `piece` int NOT NULL DEFAULT '0',
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `remarks` text,
  UNIQUE KEY `id_summary_log` (`id_summary_log`)
) ENGINE=InnoDB AUTO_INCREMENT=316 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_items_log`
--

DROP TABLE IF EXISTS `ret_purchase_items_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_items_log` (
  `id_old_metal_log` int NOT NULL AUTO_INCREMENT,
  `id_product` int DEFAULT NULL,
  `id_old_metal_cat` int DEFAULT NULL,
  `old_metal_sale_id` int DEFAULT NULL COMMENT 'For Old Metal Purchase Details',
  `id_issue_receipt` int DEFAULT NULL,
  `tag_id` int DEFAULT NULL COMMENT 'For Sales Return and Partly Sale Details',
  `bill_id` int DEFAULT NULL COMMENT 'For Sales Return',
  `sold_bill_det_id` int DEFAULT NULL COMMENT 'For Partly Sale ',
  `id_old_metal_process` int DEFAULT NULL COMMENT 'For Old Metal  Process Details',
  `date` datetime DEFAULT NULL,
  `item_type` int DEFAULT NULL COMMENT '1-Old Metal,2-Sales Return,3-Partly Sale,4-Old Metal Process In,5-Old Metal Process Out ,6-Bullion or Stone Purchase,7-Old Metal Deposit,8-Old Metal to Direct Acc Stock,9-Non tag Sales return',
  `po_id` int DEFAULT NULL COMMENT 'for Bullion and Stone Purchase (From Supplier Bill Entry)',
  `status` tinyint(1) DEFAULT NULL COMMENT '1-inward,2-in transit,3-out ward',
  `piece` int DEFAULT NULL,
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `less_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `from_branch` int DEFAULT NULL,
  `to_branch` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id_old_metal_log`)
) ENGINE=InnoDB AUTO_INCREMENT=30880 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_order`
--

DROP TABLE IF EXISTS `ret_purchase_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_order` (
  `po_id` int NOT NULL AUTO_INCREMENT,
  `po_grn_id` int DEFAULT NULL,
  `fin_year_code` varchar(45) DEFAULT NULL,
  `purchase_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1-> Against Order 2-> Purchase',
  `purchase_order_no` int DEFAULT NULL COMMENT 'from customerorder table',
  `po_karigar_id` int NOT NULL COMMENT 'Karikar Id',
  `po_type` tinyint(1) DEFAULT '1' COMMENT '1-Oranments,2-Metal,3-Stone',
  `po_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `po_delivery_date` date NOT NULL,
  `po_ref_no` varchar(50) DEFAULT NULL,
  `is_suspense_stock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `isratefixed` int NOT NULL DEFAULT '1',
  `is_po_halmarked` tinyint NOT NULL DEFAULT '1' COMMENT '0-> No , 1 -> Yes',
  `po_supplier_ref_no` varchar(20) DEFAULT NULL,
  `po_ref_date` date DEFAULT NULL,
  `ewaybillno` varchar(60) DEFAULT NULL,
  `despatch_through` int NOT NULL DEFAULT '1' COMMENT '1-> Courier 2-> Manual Delivery',
  `id_category` int DEFAULT NULL,
  `id_purity` int NOT NULL,
  `qc_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Issue,2-QC Completed',
  `po_irnno` varchar(160) DEFAULT NULL,
  `po_othere_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `po_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tot_purchase_amt` decimal(10,2) DEFAULT '0.00',
  `tot_purchase_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `total_payable_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_payable_wt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_by` int DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `pur_approval_type` int NOT NULL DEFAULT '0' COMMENT '0-> Normal entry 1->Againist Approval',
  `pur_approval_refid` int DEFAULT NULL COMMENT 'If against approval po id will get store',
  `gst_bill_type` tinyint(1) DEFAULT '1' COMMENT '1-Gst Bill,0-Non GST Bill',
  `tcs_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tds_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tcs_tax_value` decimal(10,2) DEFAULT '0.00',
  `tds_tax_value` decimal(10,2) DEFAULT '0.00',
  `bill_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Success,2-Cancelled',
  `cancel_reason` text,
  `cancelled_by` int DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `form_secret` varchar(100) DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-APPROVED 0-YET TO APPROVED',
  `approved_by` int DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  PRIMARY KEY (`po_id`),
  UNIQUE KEY `form_secret` (`form_secret`),
  KEY `po_grn_id` (`po_grn_id`),
  KEY `purchase_type` (`purchase_type`),
  KEY `po_karigar_id` (`po_karigar_id`),
  KEY `Idxisratefixedpo` (`isratefixed`),
  KEY `po_ref_no` (`po_ref_no`,`is_suspense_stock`,`fin_year_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=910 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_order_description`
--

DROP TABLE IF EXISTS `ret_purchase_order_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_order_description` (
  `id_order_des` int NOT NULL AUTO_INCREMENT,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_order_des`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_order_items`
--

DROP TABLE IF EXISTS `ret_purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_order_items` (
  `po_item_id` int NOT NULL AUTO_INCREMENT,
  `po_item_po_id` int NOT NULL,
  `po_order_no` int DEFAULT NULL,
  `po_item_cat_id` int DEFAULT NULL,
  `po_item_pro_id` int DEFAULT NULL,
  `po_item_des_id` int DEFAULT NULL,
  `po_item_sub_des_id` int DEFAULT NULL,
  `id_purity` int DEFAULT NULL,
  `po_purchase_mode` int NOT NULL DEFAULT '2' COMMENT '1-> Fixed 2->Flexi',
  `gross_wt` decimal(10,3) DEFAULT NULL,
  `less_wt` decimal(10,3) DEFAULT NULL,
  `net_wt` decimal(10,3) DEFAULT NULL,
  `uom` int DEFAULT NULL,
  `quality_id` int DEFAULT NULL,
  `calculation_based_on` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Mc & Wast on Gross wt,1 - Mc & Wast on Net wt,2 - Mc on Gross, Wast on Net',
  `item_wastage` decimal(10,3) DEFAULT NULL,
  `purchase_touch` decimal(10,2) NOT NULL DEFAULT '92.00',
  `no_of_pcs` int DEFAULT NULL,
  `mc_type` int DEFAULT NULL COMMENT '1-Per Gram,2-Piece',
  `mc_value` decimal(10,2) DEFAULT NULL,
  `is_suspense_stock` int DEFAULT NULL,
  `is_halmarked` int DEFAULT NULL,
  `is_halmark_from` int DEFAULT NULL COMMENT '1-From Supplier Entry,2-From Others',
  `is_rate_fixed` int DEFAULT NULL,
  `fix_rate_per_grm` decimal(10,2) DEFAULT NULL,
  `margin_rate` decimal(10,2) DEFAULT NULL,
  `rate_calc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - per gram , 2 - piece',
  `item_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Inclusive,2-Exclusive',
  `total_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_group` int DEFAULT NULL,
  `total_cgst` decimal(10,2) DEFAULT '0.00',
  `total_sgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_igst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1- QC Issue,2-QC Completd,3-Halmarking Issue,4-Halmarking Completed',
  `qc_failed_pcs` int DEFAULT NULL,
  `qc_failed_gwt` decimal(10,3) DEFAULT NULL,
  `qc_failed_lwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `qc_failed_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `hm_rejected_pcs` int NOT NULL DEFAULT '0',
  `hm_rejected_gwt` decimal(10,3) DEFAULT NULL,
  `hm_rejected_lwt` decimal(10,3) DEFAULT NULL,
  `hm_rejected_nwt` decimal(10,3) DEFAULT NULL,
  `halmarking_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `qc_checked_by` int DEFAULT NULL,
  `qc_checked_on` datetime DEFAULT NULL,
  `qc_passed_pcs` int DEFAULT NULL,
  `qc_passed_gwt` decimal(10,3) DEFAULT NULL,
  `qc_passed_lwt` decimal(10,3) DEFAULT '0.000',
  `qc_passed_nwt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_pure_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `pure_wt_calc_type` int DEFAULT NULL COMMENT 'From Supplier Master',
  `is_lot_created` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `lot_no` int DEFAULT NULL,
  `po_returned_pcs` int NOT NULL DEFAULT '0',
  `po_returned_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `cal_type` int DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `issue_met_id` int DEFAULT NULL COMMENT 'id from ret_karigar_metal_issue_details',
  PRIMARY KEY (`po_item_id`),
  KEY `IDXPOid_oitem` (`po_item_po_id`),
  KEY `IDXPOcatid_oitem` (`po_item_cat_id`),
  KEY `IDXPOproid_oitem` (`po_item_pro_id`),
  KEY `IDXPOdesid_oitem` (`po_item_des_id`),
  KEY `IDXPOsubdesid_oitem` (`po_item_sub_des_id`),
  KEY `IDXPOissuspense_oitem` (`is_suspense_stock`),
  KEY `IDXPOisratefixed_oitem` (`is_rate_fixed`)
) ENGINE=MyISAM AUTO_INCREMENT=1408 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_other_charges`
--

DROP TABLE IF EXISTS `ret_purchase_other_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_other_charges` (
  `pur_othr_chrg_id` int NOT NULL AUTO_INCREMENT,
  `pur_othr_po_id` int NOT NULL,
  `pur_po_item_id` int DEFAULT NULL COMMENT 'From purchase order items',
  `pur_othr_charge_id` int NOT NULL,
  `calc_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '	1-Per Item,2-Per pcs	',
  `pur_othr_charge_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(1,0) NOT NULL DEFAULT '0',
  `item_total_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_charge_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`pur_othr_chrg_id`),
  KEY `Idxpurothrpoid` (`pur_othr_po_id`),
  KEY `Idxpurothrchrgid` (`pur_othr_charge_id`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_return`
--

DROP TABLE IF EXISTS `ret_purchase_return`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_return` (
  `pur_return_id` int NOT NULL AUTO_INCREMENT,
  `bill_date` datetime DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `stock_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Normal Stock ,1 - Approval stock',
  `purchase_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Purchase ,1 - Sales',
  `pur_ret_convert_to` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> Supplier , 2 -> Manufaucturers, 3 -> Approval Ledger,4 -> Stone Supplier , 5 -> Diamond Supplier ',
  `tag_issue_from` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> Available Stock , 2 -> Sales Return , 3-> Partly Sales',
  `nontag_issue_from` tinyint(1) DEFAULT NULL COMMENT '1 - > Available stock , 2 -> Nontag Sales return , 3 -> Nontag Other issue',
  `pur_ret_reason` tinyint NOT NULL COMMENT '1-> Damage 2-> Excess',
  `pur_ret_remark` varchar(600) DEFAULT NULL,
  `pur_ret_ref_no` varchar(20) DEFAULT NULL,
  `pur_ret_supplier_id` int DEFAULT NULL,
  `pur_ret_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_other_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_tds_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_tds_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_other_charges_tds_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_other_charges_tds_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_tcs_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_tcs_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_round_off` decimal(10,2) DEFAULT '0.00',
  `return_total_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_created_by` int NOT NULL,
  `pur_ret_created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pur_ret_approved_by` int DEFAULT NULL,
  `pur_ret_approved_on` datetime DEFAULT NULL,
  `bill_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Success,2-Cancel',
  `cancelled_by` int DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `cancel_reason` text,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `supdel_irn` varchar(200) DEFAULT NULL,
  `cusdel_signature` text,
  `qrcodeimage` text,
  PRIMARY KEY (`pur_return_id`)
) ENGINE=MyISAM AUTO_INCREMENT=227 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_return_cat_items`
--

DROP TABLE IF EXISTS `ret_purchase_return_cat_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_return_cat_items` (
  `pur_ret_cat_itm_id` int NOT NULL AUTO_INCREMENT,
  `pur_ret_id` int NOT NULL,
  `pur_ret_cat_id` int DEFAULT NULL,
  `pur_ret_cat_pcs` int NOT NULL,
  `pur_ret_cat_gwt` decimal(10,3) NOT NULL,
  `pur_ret_cat_leswt` decimal(10,3) NOT NULL,
  `pur_ret_cat_netwt` decimal(10,3) NOT NULL,
  `pur_ret_cat_pur_wt` decimal(10,3) DEFAULT NULL,
  `pur_ret_rate` decimal(10,2) DEFAULT NULL,
  `purreturncaltype` int NOT NULL DEFAULT '1' COMMENT '1-> Grm 2-> Pcs',
  `pur_ret_tax_rate` decimal(10,2) DEFAULT NULL,
  `pur_ret_tax_value` decimal(10,2) DEFAULT NULL,
  `pur_ret_cgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_sgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_igst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_item_cost` decimal(10,2) DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`pur_ret_cat_itm_id`),
  KEY `pur_ret_cat_itm_id` (`pur_ret_cat_itm_id`),
  KEY `pur_ret_id` (`pur_ret_id`),
  KEY `pur_ret_cat_id` (`pur_ret_cat_id`),
  KEY `istransfered` (`istransfered`),
  KEY `pur_ret_cat_pcs` (`pur_ret_cat_pcs`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_return_items`
--

DROP TABLE IF EXISTS `ret_purchase_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_return_items` (
  `pur_ret_itm_id` int NOT NULL AUTO_INCREMENT,
  `pur_ret_id` int NOT NULL,
  `return_item_type` int NOT NULL COMMENT '1-> PO 2-> Tag 3-> Non Tag',
  `id_qc_issue_details` int DEFAULT NULL COMMENT 'From QC Item Details',
  `pur_ret_po_item_id` int DEFAULT NULL,
  `tag_id` int DEFAULT NULL,
  `bill_det_id` int DEFAULT NULL,
  `branch_trans_id` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `pur_ret_pcs` int NOT NULL,
  `pur_ret_gwt` decimal(10,3) NOT NULL,
  `pur_ret_lwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `pur_ret_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `pur_ret_pur_wt` decimal(10,3) DEFAULT NULL,
  `pur_ret_purchase_touch` decimal(10,2) DEFAULT NULL,
  `pur_ret_wastage` decimal(10,3) DEFAULT NULL,
  `pur_ret_wastage_wt` decimal(10,3) DEFAULT NULL,
  `pur_ret_mc_type` int DEFAULT NULL COMMENT '1-Per Gram,2-Piece',
  `pur_ret_mc_value` decimal(10,2) DEFAULT NULL,
  `pur_ret_tax_id` int DEFAULT NULL,
  `pur_ret_tax_percent` decimal(5,2) DEFAULT NULL,
  `pur_ret_tax_type` int DEFAULT NULL COMMENT '1 -> Inclusive , 2 -> Exclusive',
  `pur_ret_rate` int DEFAULT NULL,
  `pur_ret_debit_note_amt` decimal(10,2) DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  `total_cgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_sgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_igst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_total_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `calculation_based_on` int DEFAULT NULL,
  `pure_wt_calc_type` int DEFAULT NULL,
  `ref_pur_ret_itm_id` int DEFAULT NULL,
  PRIMARY KEY (`pur_ret_itm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=252 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_return_other_charges`
--

DROP TABLE IF EXISTS `ret_purchase_return_other_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_return_other_charges` (
  `pur_ret_othr_chrg_id` int NOT NULL AUTO_INCREMENT,
  `pur_ret_itm_id` int NOT NULL,
  `pur_ret_charge_id` int NOT NULL,
  `pur_ret_charge_value` decimal(10,2) NOT NULL,
  `tax_percentage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_total_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_charge_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_ret_charge_tax` int DEFAULT NULL,
  `pur_ret_charge_tax_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`pur_ret_othr_chrg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_return_other_metal`
--

DROP TABLE IF EXISTS `ret_purchase_return_other_metal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_return_other_metal` (
  `pur_ret_othermet_id` int NOT NULL AUTO_INCREMENT,
  `pur_ret_return_id` int NOT NULL,
  `ret_other_itm_metal_id` int NOT NULL,
  `ret_other_itm_pur_id` int NOT NULL,
  `ret_other_itm_grs_weight` decimal(10,3) NOT NULL COMMENT 'Return gross wt',
  `ret_other_itm_cal_type` int NOT NULL,
  `ret_other_itm_uom` int NOT NULL,
  `ret_other_itm_pcs` int NOT NULL,
  `ret_other_itm_rate` decimal(10,2) NOT NULL,
  `ret_other_itm_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`pur_ret_othermet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_return_sale_items`
--

DROP TABLE IF EXISTS `ret_purchase_return_sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_return_sale_items` (
  `ret_sale_item_id` int NOT NULL AUTO_INCREMENT,
  `pur_ret_id` int NOT NULL,
  `pur_ret_itm_id` int NOT NULL,
  PRIMARY KEY (`ret_sale_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purchase_return_stone_items`
--

DROP TABLE IF EXISTS `ret_purchase_return_stone_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purchase_return_stone_items` (
  `pur_ret_stone_id` int NOT NULL AUTO_INCREMENT,
  `pur_ret_cat_itm_id` int NOT NULL,
  `pur_ret_return_id` int NOT NULL,
  `po_st_id` int DEFAULT NULL COMMENT 'From PO Stone Items Table',
  `ret_stone_id` int NOT NULL,
  `ret_stone_pcs` int NOT NULL COMMENT 'Return pcs',
  `ret_stone_wt` decimal(10,3) NOT NULL,
  `ret_stone_uom` int NOT NULL,
  `ret_stone_calc_based_on` int NOT NULL COMMENT '1-> Weight 2->pcs',
  `ret_stone_rate` decimal(10,2) NOT NULL,
  `ret_stone_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`pur_ret_stone_id`)
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_purity`
--

DROP TABLE IF EXISTS `ret_purity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_purity` (
  `id_purity` int unsigned NOT NULL AUTO_INCREMENT,
  `purity` decimal(10,4) NOT NULL,
  `description` varchar(45) DEFAULT NULL,
  `is_default` int NOT NULL DEFAULT '0' COMMENT '1 -> yes 0 -> no',
  `met_iss_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 - active',
  `created_by` int unsigned NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_purity`),
  KEY `purity` (`purity`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_qc_cancel_reason`
--

DROP TABLE IF EXISTS `ret_qc_cancel_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_qc_cancel_reason` (
  `id_cancel` int NOT NULL AUTO_INCREMENT,
  `cancel_reason` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `date_add` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cancel`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_qc_receipt_lot`
--

DROP TABLE IF EXISTS `ret_qc_receipt_lot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_qc_receipt_lot` (
  `id_rect_lot_details` int NOT NULL AUTO_INCREMENT,
  `id_qc_issue_details` int NOT NULL,
  `lot_pcs` int DEFAULT NULL,
  `lot_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `lot_lwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `lot_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `lot_no` int DEFAULT NULL,
  PRIMARY KEY (`id_rect_lot_details`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_quality_code`
--

DROP TABLE IF EXISTS `ret_quality_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_quality_code` (
  `quality_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `created_on` datetime(6) NOT NULL,
  `updated_on` datetime(6) DEFAULT NULL,
  `clarity_id` int NOT NULL,
  `color_id` int NOT NULL,
  `created_by_id` bigint NOT NULL,
  `cut_id` int NOT NULL,
  `shape_id` int NOT NULL,
  `updated_by_id` bigint DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`quality_id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `quality` (`cut_id`,`color_id`,`clarity_id`,`shape_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_quick_link`
--

DROP TABLE IF EXISTS `ret_quick_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_quick_link` (
  `link_id` int NOT NULL AUTO_INCREMENT,
  `id_menu` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_receipt_wgt_detail`
--

DROP TABLE IF EXISTS `ret_receipt_wgt_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_receipt_wgt_detail` (
  `id_rcpt_wgt_detail` int NOT NULL,
  `id_issue_receipt` int NOT NULL,
  `esti_detail_id` int DEFAULT NULL,
  `purpose` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Cash, 2 - Exchange',
  `metal_type` int DEFAULT NULL COMMENT '1-> Gold 2->Silver',
  `item_type` int DEFAULT NULL COMMENT '1->Oranament 2-> Coin 3-> Bar',
  `gross_wt` decimal(12,4) DEFAULT NULL,
  `stone_wt` decimal(12,4) DEFAULT NULL,
  `dust_wt` decimal(12,4) DEFAULT NULL,
  `wast_wt` decimal(12,0) DEFAULT '0',
  `net_wt` decimal(12,0) NOT NULL DEFAULT '0',
  `wastage_percent` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `rate_per_grm` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_reorder_settings`
--

DROP TABLE IF EXISTS `ret_reorder_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_reorder_settings` (
  `id_reorder_settings` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `weight_based_on` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Product Based,1->design Based',
  `ref_no` varchar(45) DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `size` int DEFAULT NULL,
  `id_wt_range` int DEFAULT NULL,
  `min_pcs` int DEFAULT NULL,
  `max_pcs` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_reorder_settings`),
  KEY `id_branch` (`id_branch`),
  KEY `weight_based_on` (`weight_based_on`),
  KEY `id_wt_range` (`id_wt_range`),
  KEY `id_product` (`id_product`),
  KEY `size` (`size`),
  KEY `Idx_reorder_design_id` (`id_design`),
  KEY `id_sub_design` (`id_sub_design`),
  KEY `min_pcs` (`min_pcs`),
  KEY `max_pcs` (`max_pcs`)
) ENGINE=MyISAM AUTO_INCREMENT=145 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_repair_master`
--

DROP TABLE IF EXISTS `ret_repair_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_repair_master` (
  `id_repair_master` int NOT NULL AUTO_INCREMENT,
  `name` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_repair_master`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_sale_delivery`
--

DROP TABLE IF EXISTS `ret_sale_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_sale_delivery` (
  `id_sale_delivery` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `is_default` tinyint NOT NULL DEFAULT '0',
  `date_add` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_sale_delivery`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_screw_type`
--

DROP TABLE IF EXISTS `ret_screw_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_screw_type` (
  `screw_id` int NOT NULL AUTO_INCREMENT,
  `screw_name` varchar(100) DEFAULT NULL,
  `screw_short_code` varchar(10) DEFAULT NULL,
  `screw_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`screw_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_section`
--

DROP TABLE IF EXISTS `ret_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_section` (
  `id_section` int NOT NULL AUTO_INCREMENT,
  `section_name` varchar(20) NOT NULL,
  `section_short_code` varchar(10) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1-Active,-0-Inactive',
  `is_home_bill_counter` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `created_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_section`)
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_section_branch`
--

DROP TABLE IF EXISTS `ret_section_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_section_branch` (
  `id_section_branch` int NOT NULL AUTO_INCREMENT,
  `id_section` int NOT NULL,
  `id_branch` varchar(100) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`id_section_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=427 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_section_nontag_item_log`
--

DROP TABLE IF EXISTS `ret_section_nontag_item_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_section_nontag_item_log` (
  `id_nontag_log` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0-> Not yet sale 1-> Sold out 3-> Other Issue, 4-> In Transit 6->Cancel,7-Karigar Metal Issue 8-> Purchase Return,9-Ret Cancel,10-Added to pocket',
  `bill_id` int DEFAULT NULL,
  `ref_no` int DEFAULT NULL,
  `from_branch` int unsigned DEFAULT NULL,
  `to_branch` int unsigned DEFAULT NULL,
  `from_section` int unsigned DEFAULT NULL,
  `to_section` int unsigned DEFAULT NULL,
  `product` int DEFAULT NULL,
  `design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `no_of_piece` int DEFAULT NULL,
  `gross_wt` decimal(14,4) DEFAULT '0.0000',
  `net_wt` decimal(14,4) DEFAULT '0.0000',
  `less_wt` decimal(14,4) DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `form_secret` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_nontag_log`),
  UNIQUE KEY `form_secret` (`form_secret`)
) ENGINE=InnoDB AUTO_INCREMENT=769 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_section_tag_status_log`
--

DROP TABLE IF EXISTS `ret_section_tag_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_section_tag_status_log` (
  `id_sec_tag_status_log` int NOT NULL AUTO_INCREMENT,
  `tag_id` int NOT NULL,
  `date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0-> Not yet sale 1-> Sold out 2->Deleted, 3-> Other Issue, 4-> In Transit,5->Deleted For Issue,6->Cancel,7-Tag Issued,8-Repair Item,9-Old Tag Sale,10-Purchase Return ,11-Sales Trasnfer,12-Sales Return Transfer',
  `from_branch` int DEFAULT NULL,
  `to_branch` int DEFAULT NULL,
  `from_section` int unsigned DEFAULT NULL,
  `to_section` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL,
  `issuspensestock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-> Normal 1-> Suspense stock',
  `form_secret` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_sec_tag_status_log`),
  KEY `from_branch` (`from_branch`),
  KEY `status` (`status`),
  KEY `issuspensestock` (`issuspensestock`),
  KEY `tag_id` (`tag_id`),
  KEY `Idxsectiontobrch` (`to_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=12327 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_selling_diamond_cent_rates`
--

DROP TABLE IF EXISTS `ret_selling_diamond_cent_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_selling_diamond_cent_rates` (
  `id_selling_cents_rate` int NOT NULL AUTO_INCREMENT,
  `from_cent` decimal(12,3) NOT NULL,
  `to_cent` decimal(12,3) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `updated_on` datetime(6) NOT NULL,
  `id_selling_rate_id` int NOT NULL,
  `updated_by_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id_selling_cents_rate`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_selling_diamond_rate`
--

DROP TABLE IF EXISTS `ret_selling_diamond_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_selling_diamond_rate` (
  `selling_rate_id` int NOT NULL AUTO_INCREMENT,
  `effective_date` date NOT NULL,
  `created_on` datetime(6) NOT NULL,
  `updated_on` datetime(6) DEFAULT NULL,
  `created_by_id` bigint NOT NULL,
  `quality_code_id` int NOT NULL,
  `rate_status` tinyint NOT NULL DEFAULT '1' COMMENT '1-Yes,0-No ',
  `updated_by_id` bigint DEFAULT NULL,
  PRIMARY KEY (`selling_rate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_selling_settings`
--

DROP TABLE IF EXISTS `ret_selling_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_selling_settings` (
  `id_selling_settings` int NOT NULL AUTO_INCREMENT,
  `id_product` int NOT NULL,
  `id_design` int NOT NULL,
  `id_sub_design` int DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT ' Fixed 2-> Base on Range Minimum wastage and Max wastage',
  `wastag_method` tinyint(1) DEFAULT '1' COMMENT '1 -> percentage based, 2 -> weight based',
  `wastage_perc` decimal(5,2) DEFAULT NULL,
  `wastage_wt` decimal(12,3) DEFAULT '0.000',
  `mc_value` decimal(10,2) DEFAULT NULL,
  `mc_type` tinyint(1) DEFAULT '1' COMMENT '1-Per Gram,2-Per Piece',
  `wastag_min` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Min value for wastage if value is editable',
  `wastag_min_wt` decimal(12,3) DEFAULT '0.000' COMMENT 'Wastage wt for validation of wastage',
  `wastag_max` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Max value for wastage if value is editable',
  `mc_min` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Min value for mc if value is editable',
  `mc_max` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Max value for wastage if value is editable',
  `margin_mrp` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Margin for MRP values to validate sales cost',
  `id_branch` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_selling_settings`)
) ENGINE=InnoDB AUTO_INCREMENT=670 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_service_bill`
--

DROP TABLE IF EXISTS `ret_service_bill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_service_bill` (
  `id_service_bill` int NOT NULL AUTO_INCREMENT,
  `fin_year_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_date` datetime DEFAULT NULL,
  `id_branch` int NOT NULL,
  `id_customer` int NOT NULL,
  `total_bill_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount_received` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bill_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Success,2-Cancel',
  `counter_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `cancelled_by` int DEFAULT NULL,
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `form_secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_service_bill`),
  UNIQUE KEY `Bill No` (`fin_year_code`,`bill_no`,`id_branch`),
  UNIQUE KEY `form_secret` (`form_secret`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_service_bill_details`
--

DROP TABLE IF EXISTS `ret_service_bill_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_service_bill_details` (
  `bill_detail_id` int NOT NULL AUTO_INCREMENT,
  `id_service_bill` int NOT NULL,
  `id_product` int NOT NULL,
  `id_service` int DEFAULT NULL COMMENT 'From repair Service Master',
  `piece` int DEFAULT NULL,
  `weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `total_cgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_sgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_igst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_total_tax` decimal(10,0) NOT NULL DEFAULT '0',
  `item_total_cost` decimal(10,0) NOT NULL DEFAULT '0',
  `tax_percentage` int DEFAULT NULL,
  PRIMARY KEY (`bill_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_service_bill_payment`
--

DROP TABLE IF EXISTS `ret_service_bill_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_service_bill_payment` (
  `id_billing_payment` int NOT NULL AUTO_INCREMENT,
  `id_service_bill` int NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Credit, 2 - Debit',
  `id_bank` int DEFAULT NULL,
  `NB_type` int DEFAULT NULL COMMENT '1-RTGS,2-IMPS,3-UPI',
  `card_type` int DEFAULT NULL COMMENT '1-RUPAY, 2-VISA,3-Mastro,4-Master',
  `payment_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Sale, 2 - Advance',
  `payment_date` datetime DEFAULT NULL,
  `payment_type` varchar(80) DEFAULT NULL,
  `payment_mode` varchar(10) DEFAULT NULL,
  `payment_amount` decimal(10,0) DEFAULT NULL,
  `net_banking_date` date DEFAULT NULL,
  `cheque_date` datetime DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `bank_acc_no` varchar(80) DEFAULT NULL,
  `bank_name` varchar(80) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_IFSC` varchar(45) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_holder` varchar(20) DEFAULT NULL,
  `cvv` varchar(10) DEFAULT NULL,
  `exp_date` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(25) DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `remark` text,
  `id_pay_device` int DEFAULT NULL COMMENT 'From Payment device Master',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_billing_payment`),
  KEY `id_service_bill` (`id_service_bill`),
  KEY `type` (`type`),
  KEY `istransfered` (`istransfered`),
  KEY `payment_status` (`payment_status`),
  KEY `id_pay_device` (`id_pay_device`)
) ENGINE=InnoDB AUTO_INCREMENT=20735 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_settings`
--

DROP TABLE IF EXISTS `ret_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_settings` (
  `id_ret_settings` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `value` text,
  `description` text,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_ret_settings`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `name_2` (`name`),
  UNIQUE KEY `name_3` (`name`),
  UNIQUE KEY `name_4` (`name`),
  UNIQUE KEY `name_5` (`name`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_shape`
--

DROP TABLE IF EXISTS `ret_shape`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_shape` (
  `shape_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active ,0-inactive',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`shape_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_size`
--

DROP TABLE IF EXISTS `ret_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_size` (
  `id_size` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active ,2-Inactive',
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_size`)
) ENGINE=InnoDB AUTO_INCREMENT=300 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_stock_balance`
--

DROP TABLE IF EXISTS `ret_stock_balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_stock_balance` (
  `id_ret_collection` int NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT '1' COMMENT '1 - Tagged, 2 - Non Tagged',
  `date` date NOT NULL,
  `id_branch` int DEFAULT NULL,
  `id_product` int NOT NULL,
  `op_blc_pcs` int DEFAULT NULL,
  `op_blc_gwt` decimal(10,3) DEFAULT NULL,
  `op_blc_nwt` decimal(10,3) DEFAULT NULL,
  `inw_pcs` int DEFAULT NULL,
  `inw_gwt` decimal(10,3) DEFAULT NULL,
  `inw_nwt` decimal(10,2) DEFAULT NULL,
  `sold_pcs` int DEFAULT NULL,
  `sold_gwt` decimal(10,3) DEFAULT NULL,
  `sold_nwt` decimal(10,3) DEFAULT NULL,
  `closing_pcs` int DEFAULT NULL,
  `closing_gwt` decimal(10,3) DEFAULT NULL,
  `closing_nwt` decimal(10,3) DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id_ret_collection`),
  UNIQUE KEY `date` (`date`,`id_branch`,`id_product`,`type`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=362401 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_stock_issue`
--

DROP TABLE IF EXISTS `ret_stock_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_stock_issue` (
  `id_stock_issue` int NOT NULL AUTO_INCREMENT,
  `id_branch` int unsigned DEFAULT NULL,
  `stock_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Tagged items,2-Non Tag Items',
  `issue_date` datetime DEFAULT NULL,
  `issue_no` varchar(45) DEFAULT NULL,
  `issue_type` int DEFAULT NULL COMMENT 'From ret_stock_issue_types',
  `issued_to` int DEFAULT NULL COMMENT 'id_employee',
  `id_customer` int DEFAULT NULL,
  `id_karigar` int DEFAULT NULL,
  `id_employee` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0 - Approval pending, 1 - Issued, 2 - Rejected',
  `repair_type` tinyint DEFAULT NULL COMMENT '1-Stock Repair,2-Customer Repair',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `approved_by` int unsigned DEFAULT NULL,
  `approved_on` datetime DEFAULT NULL,
  `id_sale_delivery` int DEFAULT NULL,
  `remarks` text,
  `form_secret` varchar(100) DEFAULT NULL,
  `outistransfered` int NOT NULL DEFAULT '0',
  `fin_year` int NOT NULL,
  `issue_emp` int DEFAULT NULL,
  PRIMARY KEY (`id_stock_issue`),
  UNIQUE KEY `form_secret` (`form_secret`),
  KEY `approved_by` (`approved_by`),
  KEY `id_branch` (`id_branch`),
  KEY `created_by` (`created_by`),
  KEY `id_sale_delivery` (`id_sale_delivery`),
  KEY `issue_type` (`issue_type`),
  KEY `issued_to` (`issued_to`),
  KEY `status` (`status`),
  KEY `repair_type` (`repair_type`),
  KEY `issue_date` (`issue_date`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_stock_issue_detail`
--

DROP TABLE IF EXISTS `ret_stock_issue_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_stock_issue_detail` (
  `id_stock_issue_detail` int NOT NULL AUTO_INCREMENT,
  `id_stock_issue` int NOT NULL,
  `tag_id` int DEFAULT NULL,
  `id_non_tag_item` int DEFAULT NULL,
  `piece` int DEFAULT NULL,
  `gross_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `net_wt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` int NOT NULL DEFAULT '1' COMMENT '0 - Approval pending, 1 - Issued, 2 - Rejected, 3 - Received, 4 - Sold',
  `received_date` date DEFAULT NULL,
  `received_by` int DEFAULT NULL,
  `received_time` varchar(100) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_stock_issue_detail`),
  KEY `tag_id` (`tag_id`),
  KEY `updated_by` (`updated_by`),
  KEY `id_stock_issue` (`id_stock_issue`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_stock_issue_types`
--

DROP TABLE IF EXISTS `ret_stock_issue_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_stock_issue_types` (
  `id_stock_issue_type` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `is_remove_from_stock` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Remove From Stock,0-No',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active,0-Inactive',
  `issue_to_cus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_stock_issue_type`),
  KEY `is_remove_from_stock` (`is_remove_from_stock`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_stone`
--

DROP TABLE IF EXISTS `ret_stone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_stone` (
  `stone_id` int NOT NULL AUTO_INCREMENT,
  `stone_name` varchar(100) DEFAULT NULL,
  `stone_code` varchar(100) DEFAULT NULL,
  `stone_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Precious, 2-Semi Precious, 3-Normal Stone',
  `uom_id` int DEFAULT NULL,
  `is_certificate_req` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1- Yes, 0 - No',
  `is_4c_req` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1- Yes, 0 - No',
  `stone_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`stone_id`),
  KEY `uom_id` (`uom_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `stone_type` (`stone_type`),
  KEY `stone_status` (`stone_status`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_stone_discount_master`
--

DROP TABLE IF EXISTS `ret_stone_discount_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_stone_discount_master` (
  `id` int NOT NULL,
  `id_stone_type` int NOT NULL,
  `min_disc_per` decimal(5,2) DEFAULT NULL,
  `max_disc_per` decimal(5,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - Inactive, 1 -Active',
  `created_by` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_stone_rate_settings`
--

DROP TABLE IF EXISTS `ret_stone_rate_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_stone_rate_settings` (
  `id_stone_rate` int NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> loose stone , 2 -> loose product',
  `id_branch` int NOT NULL,
  `id_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `stone_type` int DEFAULT NULL,
  `stone_id` int DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `stone_calc_type` int NOT NULL DEFAULT '1' COMMENT '1 -> wt, 2 -> pcs',
  `quality_id` int DEFAULT NULL,
  `from_wt` decimal(12,3) DEFAULT NULL,
  `to_wt` decimal(12,3) DEFAULT NULL,
  `min_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active,0-In Active',
  `created_by` int DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id_stone_rate`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_stone_type`
--

DROP TABLE IF EXISTS `ret_stone_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_stone_type` (
  `id_stone_type` int NOT NULL AUTO_INCREMENT,
  `stone_type` varchar(20) DEFAULT NULL,
  `stone_code` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active,0-Inactive',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_stone_type`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_sub_design_mapping`
--

DROP TABLE IF EXISTS `ret_sub_design_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_sub_design_mapping` (
  `id_sub_design_mapping` int NOT NULL AUTO_INCREMENT,
  `id_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int NOT NULL COMMENT 'From Sub Design Master',
  `mc_cal_type` int DEFAULT NULL COMMENT '	1-> Per Pc 2->Per Grm 3-> % on price',
  `mc_cal_value` decimal(10,2) DEFAULT NULL,
  `wastage_type` int DEFAULT NULL COMMENT '	1-> Fixed 2-> Base on Range Minimum wastage and Max wastage',
  `wastag_value` decimal(10,2) DEFAULT NULL,
  `mc_min` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Min value for mc if value is editable',
  `mc_max` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Max value for mc if value is editable',
  `wastag_min` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Min value for wastage if value is editable',
  `wastag_max` decimal(12,2) unsigned DEFAULT NULL COMMENT 'Max value for wastage if value is editable',
  `margin_mrp` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Margin for MRP values to validate sales cost',
  `description` text,
  PRIMARY KEY (`id_sub_design_mapping`),
  KEY `id_product` (`id_product`),
  KEY `id_design` (`id_design`),
  KEY `id_design_2` (`id_design`),
  KEY `id_sub_design` (`id_sub_design`),
  KEY `mc_cal_type` (`mc_cal_type`),
  KEY `wastage_type` (`wastage_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1512 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_sub_design_mapping_images`
--

DROP TABLE IF EXISTS `ret_sub_design_mapping_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_sub_design_mapping_images` (
  `id_design_img` int NOT NULL AUTO_INCREMENT,
  `id_sub_design_mapping` int DEFAULT NULL,
  `image_name` varchar(50) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_design_img`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_sub_design_master`
--

DROP TABLE IF EXISTS `ret_sub_design_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_sub_design_master` (
  `id_sub_design` int NOT NULL AUTO_INCREMENT,
  `sub_design_name` varchar(40) DEFAULT NULL,
  `sub_design_code` varchar(5) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active,0-Inactive',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_sub_design`),
  UNIQUE KEY `sub_design_name` (`sub_design_name`),
  UNIQUE KEY `sub_design_code` (`sub_design_code`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1366 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_sub_product_master`
--

DROP TABLE IF EXISTS `ret_sub_product_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_sub_product_master` (
  `sub_pro_id` int NOT NULL AUTO_INCREMENT,
  `sub_pro_name` varchar(75) DEFAULT NULL,
  `sub_pro_code` varchar(45) DEFAULT NULL,
  `stock_type` int DEFAULT NULL COMMENT '1-> Tagged 2-> Non Tagged',
  `sales_mode` int DEFAULT NULL COMMENT '1->weight 2-> rate 3-> both',
  `wastage_type` int DEFAULT NULL COMMENT '1->Fixed 2-> Product base',
  `other_materials` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `has_stone` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `metal_type` int DEFAULT NULL,
  `has_size` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `less_stone_wt` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `tag_split` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `tag_merge` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `tag_type` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `other_charges` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `net_wt` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `less_tax` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `wastage_billing` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `stock_report` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `central_exces_duty` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `no_of_pieces` int DEFAULT NULL,
  `rfid_required` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `rfid_in_stock` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `hallmark` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `counter` int DEFAULT NULL COMMENT '1->KTM 2-> Non KDM',
  `stone_board_rate_cal` int DEFAULT NULL COMMENT '1->Yes 0->No',
  `calculation_based_on` int DEFAULT NULL COMMENT '1->Gross 2-> Net 3-> wastage on net and mc on grs4->Fixed',
  `tax_group_id` int DEFAULT NULL COMMENT 'From tax group master',
  `sub_pro_status` int DEFAULT NULL COMMENT '1-> Active 0->Inactive',
  `create_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`sub_pro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_supp_catalogue`
--

DROP TABLE IF EXISTS `ret_supp_catalogue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_supp_catalogue` (
  `id_supp_catalogue` int NOT NULL AUTO_INCREMENT,
  `ctl_datetime` datetime NOT NULL,
  `product_id` int NOT NULL,
  `design_id` int NOT NULL,
  `id_sub_design` int NOT NULL,
  `min_order_weight` decimal(12,2) NOT NULL DEFAULT '0.00',
  `delivery_duration` int DEFAULT NULL,
  `image` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_customer` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 -> Active, 0 -> Inactive',
  `is_new_arrival` tinyint(1) NOT NULL DEFAULT '0',
  `design_code` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_supp_catalogue`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_supp_catalogue_weight`
--

DROP TABLE IF EXISTS `ret_supp_catalogue_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_supp_catalogue_weight` (
  `id_catalogue_weight` int NOT NULL AUTO_INCREMENT,
  `id_supp_catalogue` int NOT NULL,
  `weight` decimal(12,3) NOT NULL,
  `from_weight` decimal(12,3) DEFAULT NULL,
  `to_weight` decimal(12,3) DEFAULT NULL,
  `purity` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'comma seperated purity',
  `size` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'comma seperated sizes',
  `wastage` decimal(12,2) NOT NULL,
  `mc_value` decimal(12,2) NOT NULL,
  `mc_type` tinyint(1) NOT NULL COMMENT '1 -> Pcs 2-> Gram',
  `delivery_duration` int DEFAULT NULL,
  `display_mc` tinyint(1) NOT NULL DEFAULT '1',
  `display_va` tinyint(1) NOT NULL DEFAULT '1',
  `display_duration` tinyint(1) NOT NULL DEFAULT '1',
  `calculation_based_on` int NOT NULL,
  `karigar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Multiple karigar items',
  PRIMARY KEY (`id_catalogue_weight`),
  KEY `fk_supp_catalogue` (`id_supp_catalogue`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_supplier_ledger_log`
--

DROP TABLE IF EXISTS `ret_supplier_ledger_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_supplier_ledger_log` (
  `trans_id` int NOT NULL AUTO_INCREMENT,
  `id_karigar` int DEFAULT NULL,
  `trans_date` datetime DEFAULT NULL,
  `cat_id` int DEFAULT NULL,
  `id_charge` int DEFAULT NULL COMMENT 'From ret charges Master',
  `trans_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-> Cr, 2 -> Dr',
  `trans_rec_type` int DEFAULT NULL COMMENT '1-> Wt ,2-> Amt',
  `trans_pcs` decimal(10,0) NOT NULL DEFAULT '0',
  `trans_grswt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `trans_netwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `trans_purewt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `trans_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `trans_screen_id` int DEFAULT NULL COMMENT '1-> Supplier bill entry, 2-> Payment 3->Metal issue,4-Purchase return',
  `ref_id` int DEFAULT NULL COMMENT 'If Supplier Bill entry - po_item_id , If purchase return - pur_ret_itm_id',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1715 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_supplier_pay_po_details`
--

DROP TABLE IF EXISTS `ret_supplier_pay_po_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_supplier_pay_po_details` (
  `pay_po_det_id` int NOT NULL AUTO_INCREMENT,
  `po_pay_id` int DEFAULT NULL,
  `pay_po_ref_id` int NOT NULL,
  `pay_po_adj_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`pay_po_det_id`),
  KEY `po_pay_id` (`po_pay_id`),
  KEY `pay_po_ref_id` (`pay_po_ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_supplier_rate_cut`
--

DROP TABLE IF EXISTS `ret_supplier_rate_cut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_supplier_rate_cut` (
  `id_supplier_rate_cut` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `id_karigar` int NOT NULL,
  `convert_to` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-BIll,2-Receipt',
  `ref_no` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_id` int DEFAULT NULL,
  `id_metal` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `rate_cut_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Direct Payment , 2-Convert to bill',
  `conversion_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Fixed bill,2-Unfix bill',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `charges_amount` decimal(10,0) NOT NULL DEFAULT '0',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` int DEFAULT NULL,
  `igst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cgst_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00',
  `round_off` decimal(10,0) NOT NULL DEFAULT '0',
  `is_opening_blc` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `op_blc_id` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1-Success , 2-Cancelled',
  `narration` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `cancelled_by` int DEFAULT NULL,
  `cancelled_on` datetime DEFAULT NULL,
  `cancelled_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_supplier_rate_cut`),
  KEY `po_id` (`po_id`)
) ENGINE=InnoDB AUTO_INCREMENT=214 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tag_collection_mapping`
--

DROP TABLE IF EXISTS `ret_tag_collection_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tag_collection_mapping` (
  `id_tag_mapping` int NOT NULL AUTO_INCREMENT,
  `id_collection_master` int NOT NULL,
  `date_add` datetime DEFAULT NULL,
  `ref_no` varchar(30) NOT NULL,
  `total_pcs` int DEFAULT NULL,
  `total_gwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `total_nwt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0-Not yet sale,1-Sold,2-Cancel',
  `sold_bill` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_tag_mapping`),
  UNIQUE KEY `ref_no` (`ref_no`),
  KEY `id_collection_master` (`id_collection_master`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tag_collection_mapping_details`
--

DROP TABLE IF EXISTS `ret_tag_collection_mapping_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tag_collection_mapping_details` (
  `id_mapping_details` int NOT NULL AUTO_INCREMENT,
  `id_tag_mapping` int NOT NULL,
  `tag_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Not Yet Sale,1-Sold',
  PRIMARY KEY (`id_mapping_details`),
  UNIQUE KEY `tag_map` (`id_tag_mapping`,`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tag_duplicate_copy`
--

DROP TABLE IF EXISTS `ret_tag_duplicate_copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tag_duplicate_copy` (
  `copy_id` int NOT NULL AUTO_INCREMENT,
  `tag_id` int NOT NULL,
  `id_employee` int NOT NULL,
  `print_date` datetime DEFAULT NULL,
  PRIMARY KEY (`copy_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3772 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tag_generation`
--

DROP TABLE IF EXISTS `ret_tag_generation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tag_generation` (
  `tag_id` int NOT NULL AUTO_INCREMENT,
  `tag_design_code` int DEFAULT NULL,
  `tag_lot_no` int DEFAULT NULL,
  `purity` decimal(10,4) DEFAULT NULL,
  `size` decimal(10,2) DEFAULT NULL,
  `piece` int DEFAULT NULL,
  `less_wt` decimal(10,3) DEFAULT NULL,
  `net_wt` decimal(10,3) DEFAULT NULL,
  `gross_wt` decimal(10,3) DEFAULT NULL,
  `max_wastage_per` decimal(10,3) DEFAULT NULL,
  `max_mc_per_grm` decimal(10,3) DEFAULT NULL,
  `max_wastage` decimal(10,3) DEFAULT NULL,
  `max_mak_charge` decimal(10,3) DEFAULT NULL,
  `min_wastage_per` decimal(10,3) DEFAULT NULL,
  `min_mc_per_grm` decimal(10,3) DEFAULT NULL,
  `min_wastage` decimal(10,3) DEFAULT NULL,
  `min_mak_charge` decimal(10,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `created_branch` int DEFAULT NULL,
  `current_branch` int DEFAULT NULL,
  `current_location` int DEFAULT NULL COMMENT 'In this also mention whether sold out or not',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tag_other_metals`
--

DROP TABLE IF EXISTS `ret_tag_other_metals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tag_other_metals` (
  `tag_other_itm_id` int NOT NULL AUTO_INCREMENT,
  `tag_other_itm_tag_id` int NOT NULL,
  `id_metal` int DEFAULT NULL,
  `tag_other_itm_metal_id` int NOT NULL,
  `tag_other_itm_pur_id` int DEFAULT NULL,
  `tag_other_itm_grs_weight` decimal(10,3) NOT NULL,
  `tag_other_itm_wastage` decimal(10,2) DEFAULT NULL,
  `tag_other_itm_uom` int NOT NULL,
  `tag_other_itm_cal_type` int NOT NULL COMMENT '1-> Weight 2->pcs',
  `tag_other_itm_mc` decimal(10,2) NOT NULL,
  `tag_other_itm_rate` decimal(10,2) NOT NULL,
  `tag_other_itm_pcs` int NOT NULL,
  `tag_other_itm_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`tag_other_itm_id`),
  KEY `Idx_tag_others_tag_id` (`tag_other_itm_tag_id`),
  KEY `tag_other_itm_met_id` (`tag_other_itm_metal_id`),
  KEY `idxtagotheritmpurId` (`tag_other_itm_pur_id`),
  KEY `id_metal` (`id_metal`),
  KEY `tag_other_itm_cal_type` (`tag_other_itm_cal_type`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tag_scan`
--

DROP TABLE IF EXISTS `ret_tag_scan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tag_scan` (
  `id_scanned` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `id_section` int DEFAULT NULL,
  `from_time` datetime DEFAULT NULL,
  `to_time` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id_scanned`),
  KEY `id_branch` (`id_branch`),
  KEY `id_product` (`id_product`),
  KEY `status` (`status`),
  KEY `from_time` (`from_time`),
  KEY `to_time` (`to_time`)
) ENGINE=InnoDB AUTO_INCREMENT=618 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tag_scanned`
--

DROP TABLE IF EXISTS `ret_tag_scanned`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tag_scanned` (
  `id_tag_scanned` int NOT NULL AUTO_INCREMENT,
  `id_scanned` int NOT NULL,
  `tag_id` int NOT NULL,
  `id_branch` int DEFAULT NULL COMMENT 'Scanned Branch',
  `date_add` datetime DEFAULT NULL,
  `created_by` int NOT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_tag_scanned`),
  UNIQUE KEY `Idx_scan_uni_ids` (`id_scanned`,`tag_id`),
  KEY `id_branch` (`id_branch`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tag_type_master`
--

DROP TABLE IF EXISTS `ret_tag_type_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tag_type_master` (
  `tag_id` int NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(95) DEFAULT NULL,
  `tag_status` int DEFAULT '1' COMMENT '0->In active 1->Active',
  `create_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tagging_attributes`
--

DROP TABLE IF EXISTS `ret_tagging_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tagging_attributes` (
  `attr_tag_id` int NOT NULL AUTO_INCREMENT,
  `id_tagging` int NOT NULL,
  `attr_id` int NOT NULL,
  `attr_val_id` int NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`attr_tag_id`),
  KEY `attribute_constraint` (`attr_id`),
  KEY `tagging_constraint` (`id_tagging`),
  KEY `attr_val_constraint` (`attr_val_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6671 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taging`
--

DROP TABLE IF EXISTS `ret_taging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taging` (
  `tag_id` int NOT NULL AUTO_INCREMENT,
  `tag_code` varchar(45) DEFAULT NULL,
  `tag_datetime` datetime DEFAULT NULL,
  `stock_date` datetime DEFAULT NULL COMMENT 'Last stock update date and time',
  `hu_id` varchar(20) DEFAULT NULL,
  `hu_id2` varchar(20) DEFAULT NULL,
  `tag_type` int DEFAULT '0' COMMENT '0 OR NULL normal 1->Approval tag',
  `is_approval_stock_converted` tinyint(1) NOT NULL DEFAULT '0',
  `counter` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - KDM, 2 - Non KDM',
  `tag_lot_id` int DEFAULT NULL,
  `id_orderdetails` int DEFAULT NULL COMMENT 'Order Details ID',
  `product_id` int DEFAULT NULL,
  `cat_type` int DEFAULT NULL COMMENT 'Category type from category master',
  `id_lot_inward_detail` int NOT NULL,
  `tot_print_taken` int NOT NULL DEFAULT '0' COMMENT 'No. of times tag print taken',
  `design_id` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `design_for` int DEFAULT '0' COMMENT '1->Male 2-> Female 3-> Unisex ',
  `cost_center` int DEFAULT NULL,
  `purity` int DEFAULT NULL,
  `size` int DEFAULT NULL,
  `uom` int DEFAULT NULL,
  `piece` int DEFAULT '1',
  `less_wt` decimal(12,3) DEFAULT '0.000',
  `net_wt` decimal(12,3) DEFAULT NULL,
  `gross_wt` decimal(12,3) DEFAULT NULL,
  `uom_gross_wt` int DEFAULT NULL COMMENT 'Stone weight unit ',
  `calculation_based_on` int DEFAULT '2' COMMENT '0-> Both Mc, Wast on Gross 1 -> Both Mc, Wast on Net 2 ->Wastage on net and MC on Gross 3->Fixed',
  `stone_calculation_based_on` int DEFAULT NULL COMMENT '1 -> Calc based on weight, 2 -> Calc based on Pcs',
  `quality_id` int DEFAULT NULL,
  `retail_max_wastage_percent` decimal(10,2) DEFAULT NULL,
  `tag_mc_type` tinyint(1) DEFAULT '1' COMMENT '1-> Per Pc 2->Per Grm 3-> % on price',
  `tag_mc_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `retail_max_mc` varchar(45) DEFAULT NULL,
  `halmarking` int DEFAULT NULL,
  `sell_rate` decimal(10,2) DEFAULT NULL COMMENT 'Sell Rate from Lot',
  `item_rate` decimal(10,2) DEFAULT NULL COMMENT 'Adjusted item Rate for Sale',
  `sales_value` decimal(10,2) DEFAULT NULL,
  `tag_status` int NOT NULL DEFAULT '0' COMMENT '0-> Not yet sale 1-> Sold out 2->Deleted, 3-> Other Issue, 4-> In Transit,5-Deleted for Stock,6->Sales Return,7-Stock Issue for Marketing / Photoshoot,8-Repair Item,9-Purchase Return,10-EDA Sale,11-Tag Booked for Advance,13-Added to Pocket,14-Transfered to home section,17-Metal Issue',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `current_branch` int DEFAULT NULL,
  `current_counter` int DEFAULT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `is_partial` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Tag Partial Sale',
  `image` text,
  `ref_no` varchar(45) DEFAULT NULL COMMENT 'ref no. to map multi created tags',
  `tag_mark` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - None, 1 - Green',
  `is_green_tag_printed` tinyint NOT NULL DEFAULT '0' COMMENT '0-Not Printed,1-Printed',
  `manufacture_code` varchar(50) DEFAULT NULL,
  `style_code` varchar(50) DEFAULT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  `sold_from` int DEFAULT NULL,
  `cert_no` varchar(20) DEFAULT NULL,
  `cert_img` varchar(35) DEFAULT NULL,
  `tag_purchase_cost` decimal(10,2) DEFAULT NULL,
  `tag_approved_on` datetime DEFAULT NULL,
  `trans_to_acc_stock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Transfer to Acc Stock(Only Sales Ret Items)',
  `tag_year` int DEFAULT '23',
  `is_pocketed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `old_tag_id` varchar(20) DEFAULT NULL,
  `old_tag_date` date DEFAULT NULL,
  `is_tag_imported` int NOT NULL DEFAULT '0',
  `tag_process` tinyint NOT NULL DEFAULT '0' COMMENT '0-No,1-Retagged,2-Other Issue,3-Previous Stock Adj,4-Add to Non Tag,5-Add to Acc Stock',
  `tag_process_by` int DEFAULT NULL,
  `tag_process_date` datetime DEFAULT NULL,
  `ref_tag_id` int DEFAULT NULL COMMENT 'Sales Return Tag Id',
  `is_colection_tag` tinyint(1) NOT NULL DEFAULT '0',
  `id_section` int DEFAULT NULL COMMENT ' Section Id from section master',
  `product_division` int DEFAULT NULL COMMENT 'Refer Product Division Table	',
  `app_stk_converted_date` datetime DEFAULT NULL COMMENT 'Approval stock converted date',
  `app_stk_converted_by` int DEFAULT NULL COMMENT 'Approval stock converted by',
  `is_new_arrival` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 -> New arrival, 0 -> Not new',
  `tag_split_emp` int DEFAULT NULL,
  `tagged_to_branch` int DEFAULT NULL,
  `lot_rate` decimal(10,2) DEFAULT '0.00' COMMENT 'Purchase Rate',
  `lot_rate_calc_type` tinyint(1) DEFAULT '1' COMMENT 'Purchase Rate Cal_type',
  `lot_purchase_touch` decimal(10,2) DEFAULT NULL COMMENT 'Purchase Touch',
  `lot_calc_type` tinyint(1) DEFAULT NULL COMMENT 'Purchase Cal_type',
  `lot_mc_type` tinyint(1) DEFAULT NULL COMMENT 'Purchase MC Type',
  `lot_making_charge` decimal(10,2) DEFAULT NULL COMMENT 'Purchase MC',
  `lot_wastage_percentage` decimal(10,2) DEFAULT NULL,
  `tag_purchase_tax` decimal(12,2) DEFAULT '0.00',
  `tag_purchase_taxable` decimal(12,2) DEFAULT '0.00',
  `narration` text,
  `green_tag_date` datetime DEFAULT NULL,
  `green_tag_marked_by` int DEFAULT NULL,
  `unmark_date` datetime DEFAULT NULL,
  `unmark_by` int DEFAULT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_code` (`tag_code`),
  UNIQUE KEY `old_tag_id` (`old_tag_id`),
  UNIQUE KEY `remarks` (`remarks`),
  KEY `FK_taging_brch_id` (`id_branch`),
  KEY `tag_code_2` (`tag_code`),
  KEY `tag_status` (`tag_status`),
  KEY `product_id` (`product_id`),
  KEY `design_id` (`design_id`),
  KEY `id_sub_design` (`id_sub_design`),
  KEY `gross_wt` (`gross_wt`),
  KEY `current_branch` (`current_branch`),
  KEY `size` (`size`),
  KEY `net_wt` (`net_wt`),
  KEY `tag_id` (`tag_id`),
  KEY `tag_type` (`tag_type`),
  KEY `tag_lot_id` (`tag_lot_id`),
  KEY `cat_type` (`cat_type`),
  KEY `id_orderdetails` (`id_orderdetails`),
  KEY `id_lot_inward_detail` (`id_lot_inward_detail`),
  KEY `calculation_based_on` (`calculation_based_on`),
  KEY `tag_mc_type` (`tag_mc_type`),
  KEY `is_partial` (`is_partial`),
  KEY `tag_mark` (`tag_mark`),
  KEY `is_pocketed` (`is_pocketed`),
  KEY `tag_process` (`tag_process`)
) ENGINE=InnoDB AUTO_INCREMENT=194328 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taging_charges`
--

DROP TABLE IF EXISTS `ret_taging_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taging_charges` (
  `tag_charge_id` int NOT NULL AUTO_INCREMENT,
  `tag_id` int NOT NULL,
  `charge_id` int NOT NULL,
  `charge_value` decimal(12,2) NOT NULL,
  PRIMARY KEY (`tag_charge_id`),
  KEY `tag_id` (`tag_id`),
  KEY `charge_id` (`charge_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48462 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taging_huid`
--

DROP TABLE IF EXISTS `ret_taging_huid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taging_huid` (
  `id_tag_huid` int NOT NULL AUTO_INCREMENT,
  `tag_id` int NOT NULL,
  `huid` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` tinyint DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` tinyint DEFAULT NULL,
  PRIMARY KEY (`id_tag_huid`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taging_images`
--

DROP TABLE IF EXISTS `ret_taging_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taging_images` (
  `id_tag_img` int unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int NOT NULL,
  `image` text,
  `date_add` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  PRIMARY KEY (`id_tag_img`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32231 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taging_other_materials`
--

DROP TABLE IF EXISTS `ret_taging_other_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taging_other_materials` (
  `other_material_id` int NOT NULL AUTO_INCREMENT,
  `tag_id` int DEFAULT NULL,
  `material_id` int DEFAULT NULL,
  `wt` decimal(12,4) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`other_material_id`),
  KEY `FK_oth_mat_tag_id` (`tag_id`),
  KEY `FK_oth_mat_mat_id` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taging_status_log`
--

DROP TABLE IF EXISTS `ret_taging_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taging_status_log` (
  `id_tag_status_log` int NOT NULL AUTO_INCREMENT,
  `tag_id` int NOT NULL,
  `date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0-> Not yet sale 1-> Sold out 2->Deleted, 3-> Other Issue, 4-> In Transit,5->Deleted For Issue,6->Cancel,7-Tag Issued,8-Repair Item,9-Old Tag Sale,10-Purchase Return ,11-Sales Trasnfer,12-Sales Return Transfer,13-Approval stock to Normal Stock,14-Purchase Return,15-Added to Pocket,16-Transfered to home section,17-Metal Issue',
  `from_branch` int unsigned DEFAULT NULL,
  `to_branch` int unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL,
  `issuspensestock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-> Normal 1-> Suspense stock',
  `form_secret` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_tag_status_log`),
  UNIQUE KEY `Form sceret` (`tag_id`,`form_secret`),
  KEY `from_branch` (`from_branch`),
  KEY `to_branch` (`to_branch`),
  KEY `created_by` (`created_by`),
  KEY `tag_id` (`tag_id`),
  KEY `IdxStockissuspens` (`issuspensestock`),
  KEY `tag_id_2` (`tag_id`),
  KEY `status` (`status`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=36022 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taging_stone`
--

DROP TABLE IF EXISTS `ret_taging_stone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taging_stone` (
  `tag_stone_id` int NOT NULL AUTO_INCREMENT,
  `tag_id` int DEFAULT NULL,
  `stone_id` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `wt` decimal(12,3) DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `rate_per_gram` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Stone Rate',
  `amount` decimal(10,2) DEFAULT NULL,
  `is_apply_in_lwt` tinyint DEFAULT '1' COMMENT 'Stone wt applied in LWT=>1-Yes,0-No',
  `is_certification_req` tinyint NOT NULL DEFAULT '0',
  `stone_cal_type` int NOT NULL DEFAULT '1' COMMENT '	1-> By weight 2->By pcs',
  `stone_quality_id` int DEFAULT NULL,
  `certification_cost` decimal(10,2) DEFAULT NULL,
  `image` text,
  `pur_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pur_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`tag_stone_id`),
  KEY `FK_tag_st_tag_id` (`tag_id`),
  KEY `FK_tag_st_sto_id` (`stone_id`),
  KEY `FK_tag_uom_id` (`uom_id`),
  KEY `is_apply_in_lwt` (`is_apply_in_lwt`),
  KEY `stone_cal_type` (`stone_cal_type`)
) ENGINE=InnoDB AUTO_INCREMENT=16955 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tally_import_chitpayments`
--

DROP TABLE IF EXISTS `ret_tally_import_chitpayments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tally_import_chitpayments` (
  `tal_imp_id` int NOT NULL AUTO_INCREMENT,
  `tal_bran_row_id` int NOT NULL,
  `tal_imp_date` date DEFAULT NULL,
  `tal_imp_branch` int NOT NULL,
  `tal_imp_fin_code` varchar(11) NOT NULL,
  `istransfered` int NOT NULL DEFAULT '0',
  `tally_guid` varchar(100) DEFAULT NULL,
  `tally_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`tal_imp_id`),
  UNIQUE KEY `unq_chitpayment_import` (`tal_imp_date`,`tal_imp_branch`),
  KEY `istransfered` (`istransfered`),
  KEY `tal_imp_fin_code` (`tal_imp_fin_code`)
) ENGINE=InnoDB AUTO_INCREMENT=871 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_tasks`
--

DROP TABLE IF EXISTS `ret_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_tasks` (
  `id_task` int NOT NULL AUTO_INCREMENT,
  `task_type` int DEFAULT NULL COMMENT '1->Hot2->Warn3->Cold',
  `task_name` text COLLATE utf8mb4_unicode_ci,
  `task_attachments` text COLLATE utf8mb4_unicode_ci COMMENT '''attachment list with ## delimiter''',
  `task_has_pre_checklist` int DEFAULT NULL COMMENT 'Task having pre check list',
  `task_has_post_checklist` int DEFAULT NULL COMMENT 'Task having post check list',
  `task_pre_checklist_attachments` text COLLATE utf8mb4_unicode_ci COMMENT '''attachment list with ## delimiter''',
  `task_post_checklist_attachments` text COLLATE utf8mb4_unicode_ci COMMENT '''attachment list with ## delimiter''',
  `task_assign_to` int DEFAULT NULL COMMENT 'Employee code',
  `task_status` int DEFAULT NULL COMMENT '0->Created 1->Assigned 2->Completed 3-> Deleted',
  `task_completed_on` datetime DEFAULT NULL COMMENT 'Completd datetime',
  `task_created_by` int DEFAULT NULL,
  `task_created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `task_updated_on` datetime DEFAULT NULL,
  `task_updated_by` int DEFAULT NULL,
  `completed_on` datetime DEFAULT NULL,
  `is_viewed` tinyint NOT NULL DEFAULT '0' COMMENT '1-Yes,0-No',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `view_owned_by` tinyint NOT NULL DEFAULT '0' COMMENT '0-No,1-Update if task viewed by creator',
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_task`),
  KEY `task_type` (`task_type`),
  KEY `task_has_pre_checklist` (`task_has_pre_checklist`),
  KEY `task_status` (`task_status`),
  KEY `is_viewed` (`is_viewed`),
  KEY `view_owned_by` (`view_owned_by`),
  KEY `task_assign_to` (`task_assign_to`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taxgroupitems`
--

DROP TABLE IF EXISTS `ret_taxgroupitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taxgroupitems` (
  `tgi_sno` int NOT NULL AUTO_INCREMENT,
  `tgi_tgrpcode` int NOT NULL,
  `tgi_taxcode` int NOT NULL,
  `tgi_calculation` varchar(20) DEFAULT NULL COMMENT '1 - Base Value , 2 - Arrived Value',
  `tgi_type` tinyint(1) DEFAULT NULL COMMENT '1 - Add , 2 - Sub',
  `branch_code` varchar(35) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`tgi_sno`),
  KEY `FK_dt_taxgroupitems_2` (`tgi_taxcode`),
  KEY `FK_ret_taxgroupitems_1` (`tgi_tgrpcode`),
  KEY `tgi_type` (`tgi_type`),
  KEY `branch_code` (`branch_code`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taxgroupmaster`
--

DROP TABLE IF EXISTS `ret_taxgroupmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taxgroupmaster` (
  `tgrp_id` int NOT NULL AUTO_INCREMENT,
  `tgrp_name` varchar(35) DEFAULT NULL,
  `tgrp_status` tinyint unsigned DEFAULT NULL,
  `effective_date` datetime DEFAULT NULL,
  `created_by` varchar(35) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_by` varchar(35) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `branch_code` varchar(35) DEFAULT NULL,
  PRIMARY KEY (`tgrp_id`),
  UNIQUE KEY `Index_2` (`tgrp_name`),
  KEY `tgrp_status` (`tgrp_status`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_taxmaster`
--

DROP TABLE IF EXISTS `ret_taxmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_taxmaster` (
  `tax_id` int NOT NULL AUTO_INCREMENT,
  `tax_code` varchar(20) NOT NULL,
  `tax_name` varchar(35) NOT NULL,
  `tax_percentage` decimal(5,2) DEFAULT NULL,
  `tax_status` tinyint(1) DEFAULT NULL,
  `created_by` varchar(15) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` varchar(15) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `branch_code` varchar(35) DEFAULT NULL,
  PRIMARY KEY (`tax_id`),
  UNIQUE KEY `tax_name` (`tax_name`),
  KEY `created_by` (`created_by`),
  KEY `tax_status` (`tax_status`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_theme`
--

DROP TABLE IF EXISTS `ret_theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_theme` (
  `id_theme` int NOT NULL AUTO_INCREMENT,
  `theme_code` varchar(45) DEFAULT NULL,
  `theme_name` varchar(75) DEFAULT NULL,
  `theme_desc` text,
  `theme_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1->Active 0 ->Inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`id_theme`),
  UNIQUE KEY `code_theme_UNIQUE` (`theme_code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_uom`
--

DROP TABLE IF EXISTS `ret_uom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_uom` (
  `uom_id` int NOT NULL AUTO_INCREMENT,
  `uom_name` varchar(100) DEFAULT NULL,
  `uom_short_code` varchar(10) DEFAULT NULL,
  `uom_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1- Active, 0 - Inactive',
  `divided_by_value` int DEFAULT NULL COMMENT 'For Convertion to Gram',
  `is_default` tinyint NOT NULL DEFAULT '0' COMMENT '0-No,1-Yes',
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL COMMENT 'id_emp',
  PRIMARY KEY (`uom_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `uom_status` (`uom_status`),
  KEY `divided_by_value` (`divided_by_value`),
  KEY `is_default` (`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_versioning_details`
--

DROP TABLE IF EXISTS `ret_versioning_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_versioning_details` (
  `id_version_det` int NOT NULL AUTO_INCREMENT,
  `version` decimal(10,0) NOT NULL DEFAULT '0',
  `date_add` datetime DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_version_det`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_view_customer_ledger`
--

DROP TABLE IF EXISTS `ret_view_customer_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_view_customer_ledger` (
  `cusId` int unsigned DEFAULT NULL,
  `cusName` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billDate` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refNo` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `narration` text COLLATE utf8mb4_unicode_ci,
  `accType` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Debit` decimal(10,2) DEFAULT NULL,
  `Credit` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `ret_view_grn_pay_ledger`
--

DROP TABLE IF EXISTS `ret_view_grn_pay_ledger`;
/*!50001 DROP VIEW IF EXISTS `ret_view_grn_pay_ledger`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ret_view_grn_pay_ledger` AS SELECT 
 1 AS `sup_id`,
 1 AS `sup_name`,
 1 AS `accType`,
 1 AS `credit`,
 1 AS `debit`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ret_view_smith_combined_ledger`
--

DROP TABLE IF EXISTS `ret_view_smith_combined_ledger`;
/*!50001 DROP VIEW IF EXISTS `ret_view_smith_combined_ledger`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ret_view_smith_combined_ledger` AS SELECT 
 1 AS `ledger_type`,
 1 AS `category`,
 1 AS `product`,
 1 AS `trans_date`,
 1 AS `referenceno`,
 1 AS `po_date`,
 1 AS `po_ref_no`,
 1 AS `link`,
 1 AS `print_id`,
 1 AS `qry_type`,
 1 AS `trans_type`,
 1 AS `trans_id`,
 1 AS `gross_wt`,
 1 AS `net_wt`,
 1 AS `no_of_pcs`,
 1 AS `purchase_touch`,
 1 AS `purewt`,
 1 AS `customer_id`,
 1 AS `trans_rec_type`,
 1 AS `trans_amount`,
 1 AS `catid`,
 1 AS `proid`,
 1 AS `trans_screen_id`,
 1 AS `id_metal`,
 1 AS `metal`,
 1 AS `rate`,
 1 AS `narration`,
 1 AS `unixtransdate`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ret_view_smith_ledger`
--

DROP TABLE IF EXISTS `ret_view_smith_ledger`;
/*!50001 DROP VIEW IF EXISTS `ret_view_smith_ledger`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ret_view_smith_ledger` AS SELECT 
 1 AS `category`,
 1 AS `product`,
 1 AS `trans_date`,
 1 AS `referenceno`,
 1 AS `trans_type`,
 1 AS `trans_id`,
 1 AS `gross_wt`,
 1 AS `net_wt`,
 1 AS `no_of_pcs`,
 1 AS `purchase_touch`,
 1 AS `purewt`,
 1 AS `customer_id`,
 1 AS `trans_rec_type`,
 1 AS `trans_amount`,
 1 AS `catid`,
 1 AS `stone_type`,
 1 AS `proid`,
 1 AS `trans_screen_id`,
 1 AS `id_metal`,
 1 AS `metal`,
 1 AS `rate`,
 1 AS `narration`,
 1 AS `unixtransdate`,
 1 AS `dispuom`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ret_view_smith_metal_amt_ledger`
--

DROP TABLE IF EXISTS `ret_view_smith_metal_amt_ledger`;
/*!50001 DROP VIEW IF EXISTS `ret_view_smith_metal_amt_ledger`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ret_view_smith_metal_amt_ledger` AS SELECT 
 1 AS `ref_no`,
 1 AS `sup_id`,
 1 AS `sup_name`,
 1 AS `transdate`,
 1 AS `unixtransdate`,
 1 AS `purchase_wt`,
 1 AS `issue_wt`,
 1 AS `accType`,
 1 AS `met_credit`,
 1 AS `met_debit`,
 1 AS `amt_credit`,
 1 AS `amt_debit`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ret_view_smith_metal_ledger`
--

DROP TABLE IF EXISTS `ret_view_smith_metal_ledger`;
/*!50001 DROP VIEW IF EXISTS `ret_view_smith_metal_ledger`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ret_view_smith_metal_ledger` AS SELECT 
 1 AS `ref_no`,
 1 AS `sup_id`,
 1 AS `sup_name`,
 1 AS `transdate`,
 1 AS `unixtransdate`,
 1 AS `purchase_wt`,
 1 AS `issue_wt`,
 1 AS `catname`,
 1 AS `cat_id`,
 1 AS `metal`,
 1 AS `metid`,
 1 AS `accType`,
 1 AS `credit`,
 1 AS `debit`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ret_view_supplier_approval_ledger`
--

DROP TABLE IF EXISTS `ret_view_supplier_approval_ledger`;
/*!50001 DROP VIEW IF EXISTS `ret_view_supplier_approval_ledger`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ret_view_supplier_approval_ledger` AS SELECT 
 1 AS `category`,
 1 AS `product`,
 1 AS `trans_date`,
 1 AS `referenceno`,
 1 AS `link`,
 1 AS `print_id`,
 1 AS `trans_type`,
 1 AS `trans_id`,
 1 AS `gross_wt`,
 1 AS `net_wt`,
 1 AS `no_of_pcs`,
 1 AS `purchase_touch`,
 1 AS `purewt`,
 1 AS `customer_id`,
 1 AS `trans_rec_type`,
 1 AS `trans_amount`,
 1 AS `catid`,
 1 AS `proid`,
 1 AS `trans_screen_id`,
 1 AS `id_metal`,
 1 AS `metal`,
 1 AS `rate`,
 1 AS `narration`,
 1 AS `unixtransdate`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ret_view_supplier_ledger`
--

DROP TABLE IF EXISTS `ret_view_supplier_ledger`;
/*!50001 DROP VIEW IF EXISTS `ret_view_supplier_ledger`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ret_view_supplier_ledger` AS SELECT 
 1 AS `category`,
 1 AS `po_id`,
 1 AS `id_metal`,
 1 AS `product`,
 1 AS `trans_date`,
 1 AS `referenceno`,
 1 AS `trans_type`,
 1 AS `trans_id`,
 1 AS `gross_wt`,
 1 AS `net_wt`,
 1 AS `no_of_pcs`,
 1 AS `purchase_touch`,
 1 AS `purewt`,
 1 AS `customer_id`,
 1 AS `trans_rec_type`,
 1 AS `trans_amount`,
 1 AS `catid`,
 1 AS `proid`,
 1 AS `trans_screen_id`,
 1 AS `metal`,
 1 AS `rate`,
 1 AS `narration`,
 1 AS `unixtransdate`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ret_view_supplier_metal_ledger`
--

DROP TABLE IF EXISTS `ret_view_supplier_metal_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_view_supplier_metal_ledger` (
  `ref_no` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sup_id` int DEFAULT NULL,
  `sup_name` varchar(75) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transdate` datetime DEFAULT NULL,
  `purchase_wt` int DEFAULT NULL,
  `issue_wt` decimal(10,3) DEFAULT NULL,
  `id_purity` int DEFAULT NULL,
  `purity` decimal(10,4) DEFAULT NULL,
  `catname` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cat_id` int DEFAULT NULL,
  `metal` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metid` int DEFAULT NULL,
  `accType` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit` int DEFAULT NULL,
  `debit` decimal(10,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_wallet`
--

DROP TABLE IF EXISTS `ret_wallet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_wallet` (
  `id_ret_wallet` int NOT NULL AUTO_INCREMENT,
  `id_customer` int unsigned NOT NULL,
  `amount` varchar(10) DEFAULT NULL,
  `weight` decimal(12,4) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id_ret_wallet`),
  KEY `id_customer` (`id_customer`)
) ENGINE=InnoDB AUTO_INCREMENT=1540 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_wallet_transcation`
--

DROP TABLE IF EXISTS `ret_wallet_transcation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_wallet_transcation` (
  `id_ret_wallet_trans` int NOT NULL AUTO_INCREMENT,
  `adjusted_in` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Billing, 2 - Scheme Payment',
  `id_ret_wallet` int NOT NULL,
  `id_issue_receipt` int DEFAULT NULL,
  `id_payment` int DEFAULT NULL,
  `bill_no` int DEFAULT NULL,
  `deposit_bill_id` int DEFAULT NULL COMMENT 'From ret_illing (Deposit Amount)',
  `transaction_type` tinyint(1) DEFAULT NULL COMMENT '0-Issue , 1 - Debit	',
  `amount` decimal(10,2) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `rate_per_gram` decimal(10,2) DEFAULT '0.00' COMMENT 'Adjusted rate',
  `remarks` varchar(100) DEFAULT NULL,
  `oldrefno` varchar(50) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_ret_wallet_trans`),
  KEY `id_payment` (`id_payment`),
  KEY `adjusted_in` (`adjusted_in`),
  KEY `id_ret_wallet` (`id_ret_wallet`),
  KEY `id_issue_receipt` (`id_issue_receipt`),
  KEY `bill_no` (`bill_no`),
  KEY `deposit_bill_id` (`deposit_bill_id`),
  KEY `transaction_type` (`transaction_type`)
) ENGINE=MyISAM AUTO_INCREMENT=2864 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_wastage_discount_master`
--

DROP TABLE IF EXISTS `ret_wastage_discount_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_wastage_discount_master` (
  `id_wastage` int NOT NULL,
  `id_metal` int NOT NULL,
  `from_range` decimal(5,2) DEFAULT NULL,
  `to_range` decimal(5,2) DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `created_by` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `modified_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_weight`
--

DROP TABLE IF EXISTS `ret_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_weight` (
  `id_weight` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `value` decimal(10,3) DEFAULT NULL,
  `id_uom` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `id_design` int DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `from_weight` decimal(10,3) DEFAULT NULL,
  `to_weight` decimal(10,3) DEFAULT NULL,
  `weight_description` varchar(200) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_weight`),
  UNIQUE KEY `idxuniqweight` (`value`,`id_uom`,`id_product`,`id_design`,`id_sub_design`,`from_weight`,`to_weight`,`weight_description`),
  KEY `id_product` (`id_product`),
  KEY `id_design` (`id_design`),
  KEY `id_sub_design` (`id_sub_design`),
  KEY `from_weight` (`from_weight`),
  KEY `to_weight` (`to_weight`),
  KEY `weight_description` (`weight_description`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=259 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_wishlist`
--

DROP TABLE IF EXISTS `ret_wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_wishlist` (
  `id_wishlist` int unsigned NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `id_customer` int unsigned DEFAULT NULL,
  `item_type` int DEFAULT '1' COMMENT '1->Catalog item 2-> Custom item 3->E-Catalog item',
  `is_customized` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Item customized',
  `id_product` int unsigned DEFAULT NULL,
  `design_no` int unsigned DEFAULT NULL,
  `wast_percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_mc_type` int DEFAULT NULL COMMENT '1-Per Gram,2-Piece',
  `mc` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Making Charge amount',
  `stn_amt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `seal` varchar(20) DEFAULT NULL,
  `melting` varchar(20) DEFAULT NULL,
  `itemname` varchar(60) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `id_weight_range` int DEFAULT NULL,
  `size` int DEFAULT NULL,
  `pcs` varchar(100) DEFAULT NULL,
  `id_purity` int unsigned NOT NULL,
  `is_chain` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not chain , 1- chain',
  `hook_type` int DEFAULT NULL COMMENT '1 - S type , 2 - M type',
  `rate_per_gram` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sample_images` text COMMENT 'Multiple images will store separation of ##',
  `sample_details` varchar(200) DEFAULT NULL,
  `status` tinyint unsigned DEFAULT '1' COMMENT '1 - In Wishlist, 2 - In Cart, 3 - Converted As Order',
  `id_orderdetails` int unsigned DEFAULT NULL COMMENT 'Update if converted as order',
  `wishlist_date` datetime DEFAULT NULL,
  `expiry_date` date DEFAULT NULL COMMENT 'item will be deleted if not converted as order',
  `updated_by` int DEFAULT NULL,
  `upd_date` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `cus_due_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_wishlist`),
  KEY `FK_cusorder_details_idx` (`id_customer`),
  KEY `id_purity` (`id_purity`),
  KEY `id_product` (`id_product`),
  KEY `design_no` (`design_no`),
  KEY `id_orderdetails` (`id_orderdetails`),
  KEY `updated_by` (`updated_by`),
  KEY `id_branch` (`id_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_wishlist_enquiry`
--

DROP TABLE IF EXISTS `ret_wishlist_enquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_wishlist_enquiry` (
  `id_wishlist` int NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `customer_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag_id` int DEFAULT NULL,
  `tag_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_supp_catalogue` int DEFAULT NULL COMMENT 'Refer supplier catalogue table',
  `esti_id` int DEFAULT NULL,
  `product_id` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'comma separated product ids',
  `design_id` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'comma separated design ids',
  `sub_design_id` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'comma separated sub design ids',
  `price_range` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_range` int unsigned DEFAULT NULL,
  `weight` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `images` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reasons_for_leaving` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enq_product` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Customer enquired product',
  `due_days` int DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emp_login_branch` int unsigned DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1 -> Created, 2 -> Converted, 3 -> Not Converted',
  `remarks` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Remarks given by customer',
  `close_date` date DEFAULT NULL,
  `close_employee` int DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_wishlist`),
  UNIQUE KEY `customer_id` (`customer_id`,`tag_id`,`type`) USING BTREE,
  UNIQUE KEY `customer_id_3` (`customer_id`,`id_supp_catalogue`,`type`) USING BTREE,
  KEY `fk_wl_tag_id` (`tag_id`),
  KEY `fk_wl_cus_id` (`customer_id`),
  KEY `fk_wl_branch_id` (`emp_login_branch`),
  KEY `fk_wl_emp_id` (`created_by`),
  KEY `fk_supp_cat_id` (`id_supp_catalogue`),
  KEY `fk_wl_esti_id` (`esti_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ret_wishlist_enquiry_followup`
--

DROP TABLE IF EXISTS `ret_wishlist_enquiry_followup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ret_wishlist_enquiry_followup` (
  `id_enq_followup` int NOT NULL AUTO_INCREMENT,
  `id_wishlist_enq` int NOT NULL,
  `followup_date` date NOT NULL,
  `followup_remarks` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `followup_employee` int DEFAULT NULL,
  PRIMARY KEY (`id_enq_followup`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `revert_approve_log`
--

DROP TABLE IF EXISTS `revert_approve_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `revert_approve_log` (
  `id_revert_log` int NOT NULL AUTO_INCREMENT,
  `is_reverted` varchar(1) NOT NULL DEFAULT 'N',
  `is_transferred` varchar(1) NOT NULL DEFAULT 'N',
  `transfer_date` int DEFAULT NULL,
  `delete_customer_reg` tinyint(1) DEFAULT '0' COMMENT '1 - delete customer reg record',
  `id_branch` int DEFAULT NULL,
  `ref_no` int NOT NULL,
  `clientid` varchar(45) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_upd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_employee` int unsigned NOT NULL,
  PRIMARY KEY (`id_revert_log`),
  KEY `id_employee` (`id_employee`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sch_classify`
--

DROP TABLE IF EXISTS `sch_classify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sch_classify` (
  `id_classification` int NOT NULL AUTO_INCREMENT,
  `id_company` int DEFAULT NULL,
  `classification_name` varchar(45) NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `logo` text COMMENT 'name of logo with extension',
  PRIMARY KEY (`id_classification`),
  KEY `active` (`active`),
  KEY `id_classification` (`id_classification`),
  KEY `active_2` (`active`),
  KEY `id_classification_2` (`id_classification`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sch_enquiry`
--

DROP TABLE IF EXISTS `sch_enquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sch_enquiry` (
  `id_sch_enquiry` int unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int DEFAULT NULL,
  `id_scheme` int DEFAULT NULL,
  `intresred_amt` int DEFAULT NULL,
  `message` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `intrested_wgt` decimal(10,3) DEFAULT NULL,
  `enquiry_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_sch_enquiry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme`
--

DROP TABLE IF EXISTS `scheme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme` (
  `id_scheme` int unsigned NOT NULL AUTO_INCREMENT,
  `id_company` int DEFAULT NULL COMMENT 'company wise schemes',
  `id_classification` int NOT NULL DEFAULT '0',
  `sync_scheme_code` varchar(45) NOT NULL COMMENT 'common code for online & offline to identify scheme',
  `code` text NOT NULL,
  `scheme_name` varchar(120) NOT NULL,
  `id_metal` int unsigned DEFAULT '0',
  `id_purity` int DEFAULT NULL,
  `scheme_type` tinyint(1) DEFAULT '0' COMMENT '0 - Amount , 1 - weight',
  `plan_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' [ 0->Amount / 1->Weight ] Note : Amount - payable will be in amount form. Weight - payable will be in wgt form',
  `pp_payable_type` tinyint(1) DEFAULT NULL COMMENT ' [ 1->Fixed / 2->Flexible ]',
  `cus_refferal` int DEFAULT NULL,
  `emp_refferal` int DEFAULT NULL,
  `cus_refferal_by` int DEFAULT NULL,
  `emp_refferal_by` int DEFAULT NULL,
  `emp_refferal_value` int DEFAULT NULL,
  `cus_refferal_value` int DEFAULT NULL,
  `amount` decimal(10,0) DEFAULT NULL,
  `total_installments` int DEFAULT NULL,
  `show_ins_type` int NOT NULL DEFAULT '1' COMMENT 'To display paid installment count; 0- no.of.ins paid ,\r\n1- no.of.ins paid / total ins',
  `maturity_installment` int DEFAULT NULL,
  `maturity_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Flexible[Can pay installments and close], 2 - Fixed Maturity, 3 - Fixed Flexible[Increase maturity if has Default]',
  `closing_maturity_days` int DEFAULT NULL COMMENT 'in days. [ benefits will be applied this field] 0 - Shall close on same day',
  `payment_chances` tinyint(1) DEFAULT '0' COMMENT '0 - once, 1 - multiple',
  `min_chance` int unsigned DEFAULT '0' COMMENT 'minmum no of chance',
  `max_chance` int unsigned DEFAULT '1' COMMENT 'maximum no of chance',
  `allow_advance_in` varchar(50) DEFAULT NULL COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - CollectionApp, 4 - All',
  `allow_unpaid_in` varchar(50) DEFAULT NULL COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - CollectionApp, 4 - All',
  `mark_unpaid_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Create unpaid due as default in payment table',
  `allow_unpaid` tinyint(1) DEFAULT '0' COMMENT '0 - no, 1 - yes',
  `unpaid_months` int DEFAULT '0' COMMENT 'no of unpaid months allowed',
  `unpaid_weight_limit` decimal(10,3) DEFAULT '0.000',
  `allow_advance` tinyint(1) DEFAULT '0',
  `advance_months` int DEFAULT '0',
  `advance_weight_limit` decimal(10,3) DEFAULT '0.000',
  `allow_preclose` tinyint(1) DEFAULT '0',
  `preclose_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 -> Days , 1-> Installments',
  `preclose_months` int DEFAULT '0',
  `preclose_benefits` tinyint(1) DEFAULT '0',
  `min_weight` decimal(10,3) DEFAULT '0.000' COMMENT 'minimum weight limit',
  `max_weight` decimal(10,3) DEFAULT '0.000' COMMENT 'maximum weight limit',
  `interest` tinyint(1) DEFAULT '0',
  `tax` tinyint unsigned DEFAULT NULL,
  `tax_by` tinyint unsigned DEFAULT NULL,
  `tax_value` decimal(10,2) DEFAULT NULL,
  `total_tax` decimal(10,2) DEFAULT '0.00',
  `interest_by` tinyint(1) DEFAULT '0' COMMENT '0 - value, 1 - percentage',
  `interest_value` decimal(10,2) DEFAULT '0.00',
  `total_interest` decimal(10,2) DEFAULT '0.00',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `key_benifits_description` text NOT NULL,
  `is_pan_required` tinyint(1) DEFAULT '0',
  `pan_req_amt` decimal(10,2) DEFAULT NULL COMMENT 'If amount is 100 means ask pan when scheme amount is 100 and above',
  `fix_weight` tinyint(1) DEFAULT '0' COMMENT '0- None, 1-fix weight, 2-Amount to weight',
  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - Restrict new joining, 1 - Show to all, 2 - Show in admin ',
  `active` tinyint(1) DEFAULT '0',
  `free_payment` tinyint(1) NOT NULL DEFAULT '0',
  `approvalReqForFP` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No (credit as success) , 1 - Yes  (credit as awaiting) . Admin need to approve in payment approval page',
  `allowSecondPay` tinyint(1) DEFAULT '0' COMMENT '0 - No , 1 - Yes (Allow for 2nd payment after 1st free ins)',
  `free_payInstallments` varchar(200) DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `setlmnt_type` tinyint(1) DEFAULT '3' COMMENT '1 -monthly ,2-purchase,3- nosettlement',
  `setlmnt_adjust_by` tinyint(1) DEFAULT '2' COMMENT '1 - highest , 2- lowest , 3 -avg , 4 -manual',
  `interest_weight` decimal(12,0) NOT NULL DEFAULT '0',
  `charge_head` varchar(45) NOT NULL DEFAULT 'Convenience fee' COMMENT 'Convenience fee',
  `charge_type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0',
  `charge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '0.00',
  `has_free_ins` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'Free installments other than 1 st',
  `firstPayDisc` tinyint(2) unsigned zerofill NOT NULL DEFAULT '00' COMMENT '0 - No , 1-Yes (first instalment discount for new joining)',
  `all_pay_disc` tinyint NOT NULL DEFAULT '0' COMMENT '0 - No , 1-Yes',
  `firstPayDisc_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0-percentage , 1- amount',
  `allpay_disc_by` tinyint NOT NULL DEFAULT '0' COMMENT '0-percentage , 1- amount',
  `firstPayDisc_value` decimal(10,2) DEFAULT NULL,
  `allpay_disc_value` decimal(10,2) DEFAULT NULL,
  `gst_type` tinyint(1) NOT NULL COMMENT '0 - amount inclusive of GST, 1 - amount exclusive of GST',
  `gst` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'GST in percentage',
  `emp_referal_benefit` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'emp reference benefit(amount)',
  `hsn_code` varchar(45) DEFAULT NULL,
  `Emp_ref_values` int DEFAULT NULL,
  `cus_ref_values` int DEFAULT NULL,
  `avg_applicable` int DEFAULT NULL,
  `avg_installments` int DEFAULT NULL,
  `min_amt_chance` int unsigned NOT NULL DEFAULT '0',
  `max_amt_chance` int unsigned NOT NULL DEFAULT '0',
  `min_amount` int unsigned NOT NULL DEFAULT '0',
  `max_amount` int unsigned NOT NULL DEFAULT '0',
  `pay_duration` tinyint unsigned NOT NULL DEFAULT '0',
  `wgt_convert` tinyint(1) NOT NULL DEFAULT '2' COMMENT ' 0 -> Payment, 1 -> Scheme Closing, 2 - None , 3 - Anytime [OTP based]',
  `ref_benifitadd_ins` int DEFAULT NULL COMMENT 'scheme specific installments',
  `ref_benifitadd_ins_type` tinyint unsigned DEFAULT NULL COMMENT '0 for scheme all installment add ref benefits or 1 for scheme specific installment',
  `sch_limit_value` int DEFAULT NULL,
  `discount_type` tinyint DEFAULT NULL COMMENT '0-all installments,1-specific',
  `discount_installment` int DEFAULT NULL COMMENT 'scheme specific installments',
  `discount` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'if discsount nedd or not',
  `logo` text,
  `otp_price_fixing` tinyint(1) DEFAULT '0' COMMENT '0 - Enable, 1 - Disable (Yet to work)',
  `otp_price_fix_type` tinyint(1) DEFAULT '2' COMMENT '1 - Single , 2 Multiple (Yet to work)',
  `one_time_premium` tinyint(1) DEFAULT '0' COMMENT '0 - No , 1 - Yes',
  `is_enquiry` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Can join scheme & pay, 1 - Enquiry Only',
  `maturity_days` int DEFAULT NULL COMMENT 'in days. (If null no fixed maturity)',
  `flx_denomintion` int DEFAULT NULL COMMENT 'multiples of value',
  `flexible_sch_type` int DEFAULT NULL COMMENT '1-Amount,2-Amt-Wt(amt),3-Amt-Wt(wt),4-Weight(wt),5-Weight(Amt), 9 - custom_payable_by_topup_value',
  `apply_template` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Yes',
  `noti_msg` text COMMENT 'Due alert noti/sms content',
  `disable_sch_payment` tinyint(1) DEFAULT '0',
  `stop_payment_installment` int DEFAULT NULL,
  `firstPayamt_as_payamt` tinyint(1) DEFAULT '0' COMMENT 'First payment amount as payable',
  `firstPayamt_maxpayable` tinyint(1) DEFAULT '0' COMMENT 'First payment amount as max payable',
  `get_amt_in_schjoin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Get payment amount in scheme join',
  `avg_calc_ins` int DEFAULT NULL COMMENT 'Installment no. limit to calculate max payable',
  `apply_benefit_min_ins` int DEFAULT NULL COMMENT 'Minimun installment for preclose with discount/benefit.	',
  `apply_benefit_by_chart` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Apply benefit based on pre-close chart ',
  `apply_debit_on_preclose` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Apply debit for customer on preclose based on chart',
  `wgt_store_as` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Amt,1-Weight',
  `is_lucky_draw` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes ',
  `max_members` varchar(10) DEFAULT NULL COMMENT 'Max. members allowed per group [For lucky draw scheme only]',
  `has_prize` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes ',
  `has_gift` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes ',
  `allow_due_alert` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Can send due alert [If service enabled], 0 - Due alert not allowed',
  `auto_debit_plan_type` tinyint(1) DEFAULT '0' COMMENT '0 - Not Applicable, 1 - Periodic, 2 - OnDemand[Not Yet Developed]',
  `rate_fix_by` tinyint(1) DEFAULT NULL COMMENT '0-> scheme join 1->scheme close 2-> anytime',
  `rate_select` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1->Rate fix by Current rate 0-> Rate fix by rate history',
  `is_nominee_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Not required, 1 - Nominee required account-wise , 2 - Nominee required Customer-wise',
  `emp_incentive_closing` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No,1-Give incentive while closing',
  `closing_incentive_based_on` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Based on Installment,2-Based on Chit Weight',
  `interest_type` tinyint DEFAULT NULL COMMENT '1 - All installment, 2 - All installment specific payment, 3 - Specific installment, 4 - Specific installment specific payment',
  `interest_ins` int DEFAULT NULL COMMENT 'installment no. or payment no.',
  `set_as_min_from` int DEFAULT NULL COMMENT 'Ins. No. from which received payment amount will be applied as min. amount',
  `set_as_max_from` int DEFAULT NULL COMMENT 'Ins. No. from which received payment amount will be applied as max. amount',
  `is_aadhaar_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `aadhaar_required_amt` decimal(10,2) DEFAULT NULL COMMENT 'Get aadhaar if amount is greater or equal to this amt',
  `max_total_installments` int DEFAULT NULL COMMENT 'Installment limit after complete of total months for restrict payment',
  `agent_refferal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Yes, 0 - No',
  `agent_credit_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Credit based on scheme joined distributor, 2 - Based on payment collected distributor',
  `min_installments` int DEFAULT NULL COMMENT 'Installment limit to restrict advance payment',
  `no_of_dues` int DEFAULT NULL COMMENT 'No of times Scheme amount will multiply',
  `emp_deduct_ins` int DEFAULT '0' COMMENT 'To deduct employee referal amount if customer closed before this installment',
  `agent_deduct_ins` int DEFAULT '0' COMMENT 'To deduct agent referal amount if customer closed before this installment',
  `cus_deduct_ins` int DEFAULT '0',
  `daily_pay_limit` int DEFAULT NULL COMMENT 'Settings for payment limit per day',
  `restrict_payment` int NOT NULL DEFAULT '0' COMMENT 'Setting to restrict payment based on, 0 - By installment count , 1 - By maturity days',
  `chit_detail_days` int DEFAULT NULL COMMENT 'Setting for dislaying account ledger till given days',
  `total_days_to_pay` int DEFAULT NULL,
  `digi_target` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - set target disabled, 1- set target enabled',
  `digi_target_split_unit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1- days, 2- week, 3- month',
  `apply_interest_by` int NOT NULL DEFAULT '0' COMMENT '0 - by installments,1- by days',
  `is_wast_and_mc_benefit_apply` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Benefit for V.A & M.C in Billing',
  `disable_pay` int DEFAULT '0' COMMENT '1 --> Yes, 0 --> No',
  `disable_pay_amt` int DEFAULT NULL COMMENT 'Restrict CASH payment based on limit',
  `is_digi` int NOT NULL DEFAULT '0' COMMENT '0 -> normal scheme, 1 -> digital gold scheme',
  `is_partial_payment` tinyint(1) DEFAULT '0',
  `amt_based_on` tinyint(1) NOT NULL DEFAULT '0',
  `firstPayment_as_wgt` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 --> Yes 0 --> No',
  `has_voucher` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 -> Yes , 0 -> No',
  `calculation_type` tinyint NOT NULL DEFAULT '1' COMMENT '1--> installment wise 2--> maturity wise',
  `installment_cycle` int NOT NULL DEFAULT '0' COMMENT '0-monthly pay,1-daily pay,2-By days duration pay,3-one time pay, 4- custom payable cycle',
  `ins_days_duration` int DEFAULT NULL,
  `grace_days` int DEFAULT NULL,
  `allow_general_advance` int DEFAULT '0',
  `adv_min_amt` int DEFAULT NULL,
  `adv_max_amt` int DEFAULT NULL,
  `adv_denomination` int DEFAULT NULL,
  `apply_adv_benefit` int NOT NULL DEFAULT '0',
  `amt_restrict_by` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 --> by Total scheme amount , 2--> Month wise',
  `show_paymentMonthwise` int NOT NULL DEFAULT '0' COMMENT '0 --> No 1 --> Yes\r\n(If 1 need to show payments by grouping monthwise)',
  `avg_calc_by` int NOT NULL DEFAULT '0' COMMENT '0 ---> By installments 1-> By months',
  `disc_days` int DEFAULT NULL COMMENT 'If 5 means, give disc from due_date -5 day to due date',
  `disc_ins_from` int DEFAULT NULL COMMENT 'Apply discount From installment',
  `disc_ins_to` int DEFAULT NULL COMMENT 'Apply discount To installment',
  `trans_limit_for` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Month, 2 - Day',
  `wgt_conversion` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' 1 -->Yes,0-->No',
  `show_bonus_gift` int NOT NULL DEFAULT '0',
  `is_lumpSum` int NOT NULL DEFAULT '0' COMMENT 'lump sum scheme -> 0 - No , 1 - Yes',
  `joinTime_weight_slabs` varchar(100) DEFAULT NULL COMMENT 'Lump sum scheme -> id weight fro =m weight table that has to be shown as slabs at the time of scheme joining',
  `sch_approval` tinyint NOT NULL DEFAULT '0',
  `store_closing_balance` tinyint NOT NULL DEFAULT '0' COMMENT '0->Auto, 1->Customize',
  `rate_type` int NOT NULL DEFAULT '1' COMMENT '1 - Board Rate, 2 - Avg rate per gram',
  `display_payable` int DEFAULT NULL COMMENT '0-daily,1-weekly,2-monthly,3-one time',
  `is_topup_scheme` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - yes , 0 - no',
  `topup_booking_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Amount , 2 - weight',
  `topup_min_value` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Booking minimum value',
  `topup_max_value` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Booking maximum value',
  `topup_denomination` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Booking denomination value',
  `topup_store_slab` tinyint NOT NULL DEFAULT '1' COMMENT '1 - store fully completed slab, 2 - store current slab ',
  PRIMARY KEY (`id_scheme`),
  UNIQUE KEY `sync_scheme_code` (`sync_scheme_code`),
  UNIQUE KEY `SchIndex` (`id_scheme`,`scheme_type`),
  KEY `id_company` (`id_company`),
  KEY `id_metal` (`id_metal`),
  KEY `show_ins_type` (`show_ins_type`),
  KEY `maturity_type` (`maturity_type`),
  KEY `active` (`active`),
  KEY `is_lucky_draw` (`is_lucky_draw`),
  KEY `id_classification` (`id_classification`),
  KEY `active_2` (`active`),
  KEY `visible` (`visible`),
  KEY `id_scheme` (`id_scheme`),
  KEY `id_classification_2` (`id_classification`),
  KEY `active_3` (`active`),
  KEY `visible_2` (`visible`),
  KEY `id_scheme_2` (`id_scheme`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_account`
--

DROP TABLE IF EXISTS `scheme_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_account` (
  `id_scheme_account` int unsigned NOT NULL AUTO_INCREMENT,
  `id_scheme` int unsigned DEFAULT '0',
  `id_customer` int unsigned NOT NULL,
  `id_branch` int unsigned DEFAULT NULL,
  `scheme_acc_number` int DEFAULT NULL,
  `total_paid_ins` int NOT NULL DEFAULT '0' COMMENT 'Installments paid',
  `dg_target_value_wgt` decimal(10,3) NOT NULL DEFAULT '0.000',
  `dg_target_wgt_achieved` decimal(10,3) NOT NULL DEFAULT '0.000',
  `dg_other_benefit_wgt` decimal(10,3) DEFAULT NULL,
  `dg_other_benefit_amt` decimal(10,2) DEFAULT NULL,
  `display_ac_no` text COMMENT 'Display this account number in all pages ',
  `account_name` varchar(300) DEFAULT NULL,
  `ref_no` varchar(100) DEFAULT NULL,
  `group_code` varchar(30) DEFAULT NULL,
  `msno` int DEFAULT NULL,
  `paid_installments` int DEFAULT '0',
  `balance_amount` decimal(10,2) DEFAULT '0.00',
  `is_opening` tinyint(1) DEFAULT '0',
  `balance_weight` decimal(12,3) DEFAULT '0.000',
  `last_paid_weight` decimal(12,3) DEFAULT '0.000',
  `last_paid_chances` int DEFAULT '0',
  `last_paid_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `maturity_date` date DEFAULT NULL COMMENT 'If null, no maturity limit .ins count only',
  `lapse_date` date DEFAULT NULL,
  `employee_approved` int unsigned DEFAULT '0',
  `employee_closed` int unsigned DEFAULT '0',
  `closing_date` datetime DEFAULT NULL,
  `closing_balance` decimal(10,3) DEFAULT '0.000',
  `closing_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_weight` decimal(12,3) DEFAULT NULL,
  `closed_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - self, 1 - representative',
  `rep_name` varchar(70) DEFAULT NULL COMMENT 'representative name',
  `rep_mobile` varchar(45) DEFAULT NULL COMMENT 'representative mobile',
  `remark_open` varchar(180) DEFAULT NULL,
  `remark_close` varchar(180) DEFAULT NULL,
  `req_close` tinyint(1) NOT NULL DEFAULT '0',
  `is_closed` tinyint(1) DEFAULT '0',
  `is_new` varchar(1) DEFAULT 'Y' COMMENT 'Y - New Customer, N - Existing Customer',
  `active` tinyint(1) DEFAULT '0',
  `disable_payment` tinyint(1) DEFAULT '0',
  `disable_pay_reason` text COMMENT 'Reason for disable payment',
  `date_add` datetime DEFAULT NULL,
  `custom_entry_date` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `added_by` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5 - Sync',
  `benefit` decimal(10,2) DEFAULT '0.00' COMMENT 'Benefit given based on scheme settings',
  `deduction` decimal(10,2) DEFAULT '0.00' COMMENT 'Deduction applied based on scheme settings',
  `closing_add_chgs` decimal(10,2) NOT NULL DEFAULT '0.00',
  `additional_benefits` decimal(10,2) NOT NULL DEFAULT '0.00',
  `add_ben_type` tinyint NOT NULL DEFAULT '0' COMMENT '0 -> amount, 1 -> weight',
  `is_registered` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 -  registered a/c',
  `referal_code` varchar(45) DEFAULT NULL COMMENT 'emp or cus referal code',
  `is_refferal_by` tinyint(1) DEFAULT NULL COMMENT '0-customer referal , 1 - emp referal',
  `cus_refferal_cal_value` int DEFAULT NULL,
  `emp_refferal_cal_value` int DEFAULT NULL,
  `avg_wgtlimit` int DEFAULT NULL,
  `pan_no` varchar(50) DEFAULT NULL,
  `firstPayment_amt` varchar(10) DEFAULT NULL COMMENT 'For - First ins as payable settings, One time premium',
  `firstpayment_wgt` decimal(12,3) DEFAULT NULL COMMENT 'First installment payment weight',
  `show_gift_article` tinyint(1) NOT NULL DEFAULT '0',
  `converted_amt` decimal(10,2) DEFAULT '0.00',
  `received_wgt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Received Weight for One time Preminum Scheme',
  `fixed_metal_rate` decimal(10,2) DEFAULT NULL,
  `fixed_wgt` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT 'For  One time Premium(Amt)',
  `fixed_rate_on` datetime DEFAULT NULL COMMENT 'Rate Fixed on .For  One time Premium(Amt)',
  `rate_fixed_in` int DEFAULT NULL COMMENT '1-Web App,2-Mobile App-3-Offline',
  `avg_payable` decimal(12,3) NOT NULL DEFAULT '0.000',
  `auto_debit_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Not Subscribed, 1- INITIALIZED, 2- BANK_APPROVAL_PENDING, 3- ACTIVE, 4- ON_HOLD, 5- CANCELLED, 6- COMPLETED	',
  `closing_paid_amt` decimal(10,2) DEFAULT NULL COMMENT 'Total amount paid by customer [Update on closing]',
  `closing_benefits` decimal(10,2) DEFAULT NULL COMMENT 'Applied Scheme Benefits[Update on closing]',
  `closing_interest_val` decimal(10,3) DEFAULT NULL,
  `closing_deductions` decimal(10,2) DEFAULT NULL COMMENT 'Applied Scheme Deductions[Update on closing]',
  `is_utilized` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0->No,1->Yes',
  `utilized_type` int DEFAULT NULL COMMENT '1-cash refund,2-adj in sales	',
  `Closing_id_branch` int DEFAULT NULL,
  `id_employee` int unsigned DEFAULT '0',
  `closed_remarks` text,
  `updated_by` int DEFAULT NULL,
  `gift_debt_amt` decimal(10,6) DEFAULT NULL COMMENT 'Gift issued deduction amount ',
  `istransfered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Tally integration sync flag',
  `agent_code` varchar(45) DEFAULT NULL COMMENT 'agent code',
  `id_agent` int unsigned DEFAULT NULL COMMENT 'Referred agent',
  `start_year` int DEFAULT NULL COMMENT 'A/c starting Financial year code',
  `form_secret` varchar(100) DEFAULT NULL,
  `issue_self_giftBonus` tinyint NOT NULL DEFAULT '2' COMMENT '0 - Bonus , 1- gift, 2 - none (Customer can choose the benefits as gift or bonus by them self from app) 3 - Gift issued from employee(Collection App)',
  `tot_genadv_amt_paid` int DEFAULT NULL,
  `tot_genadv_wgt_paid` decimal(10,3) DEFAULT NULL,
  `tot_genadv_benefit` int DEFAULT NULL,
  `tot_genadv_benefit_wgt` decimal(10,3) DEFAULT NULL,
  `duplicate_passbook_issued` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-yes.2-no',
  `lump_joined_weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'lump sum scheme-> customer choosing total weight at the time of scheme join for all total installments',
  `lump_payable_weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'lump sum scheme-> weight payable per installment calculated by (lump_joined_weight / total_installments)',
  `bonus_percent` int DEFAULT NULL COMMENT 'chit benefit percentage field added for retail purpose',
  `store_closing_balance_as` tinyint DEFAULT NULL,
  `topup_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Joining time booking amount',
  `topup_weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Joining time booking weight',
  `topup_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Joining time booking rate',
  `old_scheme_acc_number` int DEFAULT NULL,
  PRIMARY KEY (`id_scheme_account`),
  UNIQUE KEY `AccIndex` (`id_scheme_account`,`id_customer`,`id_scheme`,`scheme_acc_number`),
  UNIQUE KEY `form_secret` (`form_secret`),
  UNIQUE KEY `ref_no` (`ref_no`),
  KEY `FK_scheme_account_scheme` (`id_scheme`),
  KEY `id_scheme` (`id_scheme`),
  KEY `id_scheme_account` (`id_scheme_account`),
  KEY `id_customer` (`id_customer`),
  KEY `scheme_acc_number` (`scheme_acc_number`),
  KEY `group_code` (`group_code`),
  KEY `is_closed` (`is_closed`),
  KEY `closed_by` (`closed_by`),
  KEY `closing_date` (`closing_date`),
  KEY `id_branch` (`id_branch`),
  KEY `id_scheme_2` (`id_scheme`),
  KEY `id_scheme_account_2` (`id_scheme_account`),
  KEY `id_customer_2` (`id_customer`),
  KEY `scheme_acc_number_2` (`scheme_acc_number`),
  KEY `group_code_2` (`group_code`),
  KEY `is_closed_2` (`is_closed`),
  KEY `closed_by_2` (`closed_by`),
  KEY `closing_date_2` (`closing_date`),
  KEY `id_branch_2` (`id_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=834 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_agent_benefit`
--

DROP TABLE IF EXISTS `scheme_agent_benefit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_agent_benefit` (
  `id_scheme_agent_benefit` int NOT NULL,
  `id_scheme` int unsigned NOT NULL,
  `benefit_mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Amount, 2 - Discount on Wastage in purchase, 3 - Discount on Wastage & GST',
  `benefit_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - Percent, 1 - Amount',
  `benefit_value` decimal(10,0) NOT NULL DEFAULT '0',
  `installment_from` int DEFAULT NULL,
  `installment_to` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_scheme_agent_benefit`),
  KEY `id_scheme` (`id_scheme`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Apply benefit on pre-close based on this chart';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_benefit_deduct_settings`
--

DROP TABLE IF EXISTS `scheme_benefit_deduct_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_benefit_deduct_settings` (
  `id_benefit_deduct_settings` int NOT NULL AUTO_INCREMENT,
  `id_scheme` int unsigned NOT NULL,
  `interest_by` int NOT NULL DEFAULT '0' COMMENT '0 - By installments, 1 - By days',
  `interest_mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Amount, 2 - Discount on Wastage in purchase, 3 - Discount on Wastage & GST',
  `interest_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - Percent, 1 - Amount',
  `interest_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `installment_from` int DEFAULT NULL,
  `installment_to` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `int_calc_on` tinyint DEFAULT NULL COMMENT '1-->By Value 2--> By installment',
  `installment_no` int DEFAULT NULL COMMENT 'interest has to be calculated based on this installment',
  `commodity` int DEFAULT NULL,
  PRIMARY KEY (`id_benefit_deduct_settings`),
  KEY `id_scheme` (`id_scheme`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Apply benefit on pre-close based on this chart';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_branch`
--

DROP TABLE IF EXISTS `scheme_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_branch` (
  `id_scheme_branch` int unsigned NOT NULL AUTO_INCREMENT,
  `id_scheme` int unsigned NOT NULL DEFAULT '0',
  `id_branch` int unsigned NOT NULL,
  `scheme_active` tinyint unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_scheme_branch`),
  KEY `id_scheme` (`id_scheme`),
  KEY `id_branch` (`id_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_custom_payable_settings`
--

DROP TABLE IF EXISTS `scheme_custom_payable_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_custom_payable_settings` (
  `id_custom_setting` int NOT NULL AUTO_INCREMENT,
  `id_scheme` int DEFAULT NULL COMMENT 'Scheme table : id_scheme ',
  `payable_by` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Based on days',
  `range_from` int DEFAULT NULL,
  `range_to` int DEFAULT NULL,
  `payable_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - amount, 1 - percentage',
  `payable_value` decimal(11,3) DEFAULT NULL,
  `created_by` int DEFAULT NULL COMMENT 'Employee table : id_employee',
  `created_on` varchar(30) DEFAULT NULL COMMENT 'entry created datetime',
  `updated_by` int DEFAULT NULL COMMENT 'Employee table : id_employee',
  `updated_on` varchar(30) DEFAULT NULL COMMENT 'Update entry current date time',
  `range_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - active, 0 - inactive',
  PRIMARY KEY (`id_custom_setting`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_debit_settings`
--

DROP TABLE IF EXISTS `scheme_debit_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_debit_settings` (
  `id_debit_settings` int NOT NULL AUTO_INCREMENT,
  `id_scheme` int unsigned NOT NULL,
  `deduction_by` int NOT NULL DEFAULT '0' COMMENT '0 -> by installment , 1 -> by days',
  `deduction_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - Percent, 1 - Amount',
  `deduction_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `installment_from` int DEFAULT NULL,
  `installment_to` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deduct_in` tinyint NOT NULL DEFAULT '0' COMMENT '0 - scheme payments, 1- gift issued	',
  PRIMARY KEY (`id_debit_settings`),
  KEY `id_scheme` (`id_scheme`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Apply debit for customer on pre-close based on this chart';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_flexi_settings`
--

DROP TABLE IF EXISTS `scheme_flexi_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_flexi_settings` (
  `id_sch_flexi_settings` int NOT NULL AUTO_INCREMENT,
  `id_scheme` int unsigned NOT NULL,
  `ins_from` tinyint DEFAULT NULL,
  `ins_to` tinyint DEFAULT NULL,
  `limit_based_on` tinyint(1) NOT NULL DEFAULT '2' COMMENT '0 - Avg amt, 1 - 1st ins amount, 2 -Plan Min Max Value',
  `min_value` decimal(12,3) DEFAULT NULL,
  `max_value` decimal(12,3) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_sch_flexi_settings`),
  KEY `id_sch_flexi_settings` (`id_sch_flexi_settings`),
  KEY `id_scheme` (`id_scheme`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_general_advance_benefit_settings`
--

DROP TABLE IF EXISTS `scheme_general_advance_benefit_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_general_advance_benefit_settings` (
  `id_adv_set` int NOT NULL AUTO_INCREMENT,
  `id_scheme` int NOT NULL,
  `interest_by` int NOT NULL DEFAULT '0' COMMENT '0 - Month wise',
  `installment_from` int DEFAULT NULL,
  `installment_to` int DEFAULT NULL,
  `interest_type` int NOT NULL DEFAULT '1' COMMENT '0 - Percent, 1 - Amount',
  `interest_value` decimal(10,2) NOT NULL,
  `created_by` int NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_adv_set`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_group`
--

DROP TABLE IF EXISTS `scheme_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_group` (
  `id_scheme_group` int NOT NULL AUTO_INCREMENT,
  `id_scheme` int unsigned NOT NULL,
  `id_branch` int DEFAULT NULL,
  `group_code` varchar(25) NOT NULL COMMENT 'IFauto generation is enabled concatenated value of group_code_param_1,group_code_param_2,group_code_suffix will be added here',
  `group_code_param_1` varchar(10) DEFAULT NULL COMMENT 'For auto generation',
  `group_code_param_2` varchar(10) DEFAULT NULL COMMENT 'For auto generation',
  `group_code_suffix` int DEFAULT NULL COMMENT 'For auto generation',
  `start_date` datetime DEFAULT NULL,
  `last_update` datetime NOT NULL,
  `date_add` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Upcoming, 1 - Active, 2 - Reached Limit, 3 - Group closed',
  `added_by` int NOT NULL DEFAULT '0' COMMENT '0 - manual group creation, 1 - auto group generation',
  PRIMARY KEY (`id_scheme_group`),
  KEY `id_scheme` (`id_scheme`),
  KEY `id_scheme_2` (`id_scheme`,`id_branch`,`group_code`),
  KEY `id_scheme_3` (`id_scheme`),
  KEY `id_scheme_4` (`id_scheme`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_incentive_settings`
--

DROP TABLE IF EXISTS `scheme_incentive_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_incentive_settings` (
  `id_incentive` int NOT NULL AUTO_INCREMENT,
  `id_scheme` int DEFAULT NULL,
  `credit_to` int DEFAULT NULL COMMENT '1 - employee, 2 - agent',
  `credit_for` int DEFAULT NULL COMMENT '0 - new scheme join, 1 - customer intro scheme join , 2 - payment(On day),3 - payment(On date)',
  `from_range` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `to_range` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `credit_type` int NOT NULL DEFAULT '1' COMMENT '0 - amount , 1 - percent',
  `credit_value` float(10,2) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_incentive`),
  KEY `id_scheme` (`id_scheme`),
  KEY `credit_to` (`credit_to`),
  KEY `credit_for` (`credit_for`),
  KEY `credit_type` (`credit_type`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_reg_request`
--

DROP TABLE IF EXISTS `scheme_reg_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheme_reg_request` (
  `id_reg_request` int NOT NULL AUTO_INCREMENT,
  `id_customer` int unsigned NOT NULL,
  `id_scheme` int unsigned NOT NULL,
  `id_scheme_group` int DEFAULT NULL,
  `scheme_acc_number` varchar(25) DEFAULT NULL,
  `ac_name` varchar(25) NOT NULL COMMENT 'account name',
  `id_branch` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - Processing , 1 - A/c Created , 2 - Rejected',
  `remark` text NOT NULL,
  `id_employee` int DEFAULT NULL,
  `added_by` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -web ,1- admin, 2 -mobile',
  `pan_no` varchar(50) DEFAULT NULL,
  `firstPayment_amt` decimal(10,2) DEFAULT NULL COMMENT 'amt to wt scheme first payment amount',
  `paid_installments` int NOT NULL DEFAULT '0',
  `is_opening` int NOT NULL DEFAULT '0',
  `balance_amount` decimal(10,0) NOT NULL DEFAULT '0',
  `balance_weight` decimal(12,3) NOT NULL DEFAULT '0.000',
  `last_paid_weight` decimal(12,3) NOT NULL DEFAULT '0.000',
  `last_paid_chances` int NOT NULL DEFAULT '0',
  `last_paid_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_reg_request`),
  UNIQUE KEY `uk_scheme_ac` (`scheme_acc_number`,`id_branch`,`id_scheme`,`id_customer`) USING BTREE,
  KEY `id_customer` (`id_customer`),
  KEY `id_employee` (`id_employee`),
  KEY `fk-idbranch` (`id_branch`),
  KEY `fk-idscheme` (`id_scheme`),
  KEY `id_scheme` (`id_scheme`),
  KEY `id_scheme_group` (`id_scheme_group`),
  KEY `id_scheme_2` (`id_scheme`),
  KEY `id_scheme_group_2` (`id_scheme_group`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sent_notifications`
--

DROP TABLE IF EXISTS `sent_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sent_notifications` (
  `id_sent_noti` int NOT NULL AUTO_INCREMENT,
  `id_branch` int unsigned DEFAULT NULL,
  `noti_service` int NOT NULL,
  `id_customer` int NOT NULL DEFAULT '0' COMMENT 'if 0 show to all customers , otherwise specific customer',
  `noti_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `noti_subtitle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `noti_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `targetUrl` text,
  `noti_img` text,
  `date_add` datetime DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id_sent_noti`),
  KEY `id_branch` (`id_branch`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service_log`
--

DROP TABLE IF EXISTS `service_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_log` (
  `id_service_log` int unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(250) NOT NULL,
  `event_date` datetime NOT NULL,
  `module` varchar(45) NOT NULL,
  `operation` varchar(10) NOT NULL,
  `record` text,
  `remark` text,
  PRIMARY KEY (`id_service_log`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id_services` int unsigned NOT NULL AUTO_INCREMENT,
  `serv_code` varchar(25) DEFAULT NULL,
  `serv_name` varchar(25) NOT NULL,
  `serv_email` tinyint(1) NOT NULL DEFAULT '0',
  `serv_sms` tinyint(1) NOT NULL DEFAULT '0',
  `serv_whatsapp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `sms_msg` varchar(300) DEFAULT NULL,
  `sms_footer` varchar(25) DEFAULT NULL,
  `dlt_te_id` varchar(100) DEFAULT NULL COMMENT 'DLT Template Id',
  `template_name` varchar(512) DEFAULT NULL COMMENT 'WhatsApp Template Name Registered in Qikchat',
  `send_sms_on` varchar(14) NOT NULL DEFAULT '25',
  `send_daily_from` int NOT NULL DEFAULT '0' COMMENT 'Ex: 25 (Sms will be sent daily from 25 th)',
  PRIMARY KEY (`id_services`),
  KEY `id_services` (`id_services`),
  KEY `id_services_2` (`id_services`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settlement`
--

DROP TABLE IF EXISTS `settlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settlement` (
  `id_settlement` int unsigned NOT NULL AUTO_INCREMENT,
  `schemes` int unsigned DEFAULT NULL,
  `id_employee` int unsigned DEFAULT NULL,
  `date_upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `success` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_settlement`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settlement_detail`
--

DROP TABLE IF EXISTS `settlement_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settlement_detail` (
  `id_settlement_detail` int unsigned NOT NULL AUTO_INCREMENT,
  `id_settlement` int DEFAULT NULL,
  `id_payment` int unsigned DEFAULT NULL,
  `id_scheme_account` int unsigned DEFAULT NULL,
  `metal_rate` decimal(10,2) DEFAULT NULL,
  `metal_weight` decimal(12,3) DEFAULT NULL,
  `type` tinyint unsigned DEFAULT NULL,
  `adjust_by` tinyint unsigned DEFAULT NULL,
  PRIMARY KEY (`id_settlement_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `smith_company_op_balance`
--

DROP TABLE IF EXISTS `smith_company_op_balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `smith_company_op_balance` (
  `id_smith_company_op_balance` int NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_type` tinyint(1) NOT NULL COMMENT '1 -> company stock , 2 -> smith stock''',
  `smith_type` tinyint(1) NOT NULL COMMENT '1 -> Supplier , 2 -> Smith , 3 ->Approval supplier , 4-> Stone Supplier',
  `balance_type` tinyint(1) NOT NULL COMMENT '1 -> Metal , 2-> Diamond/stone',
  `id_metal` int NOT NULL,
  `metal_type` int NOT NULL DEFAULT '1' COMMENT '1-> New 2-> Old metal',
  `id_category` int DEFAULT NULL,
  `id_old_metal_catid` int DEFAULT NULL,
  `id_product` int DEFAULT NULL,
  `id_karigar` int DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `weight` decimal(12,3) DEFAULT '0.000',
  `net_wt` decimal(10,3) DEFAULT '0.000',
  `dia_wt` decimal(10,3) DEFAULT '0.000',
  `pure_wt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `uom_id` int DEFAULT NULL,
  `weight_type` tinyint(1) DEFAULT NULL COMMENT '1 -> credit , 2 -> debit',
  `amount` decimal(10,2) DEFAULT '0.00',
  `amount_type` tinyint(1) DEFAULT NULL,
  `remarks` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `createdby` int DEFAULT NULL,
  PRIMARY KEY (`id_smith_company_op_balance`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_api_settings`
--

DROP TABLE IF EXISTS `sms_api_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_api_settings` (
  `id_sms_api` int unsigned NOT NULL AUTO_INCREMENT,
  `gateway_type` int DEFAULT NULL COMMENT '1 - msg91, 2 - netty fish, 3 - Spear UC, 4 - Asterixt, 5-Qikberry',
  `sms_sender_id` varchar(8) DEFAULT NULL,
  `sms_url` varchar(200) DEFAULT NULL,
  `credit_sms` decimal(10,0) NOT NULL DEFAULT '0',
  `debit_sms` decimal(10,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_sms_api`) USING BTREE,
  KEY `id_sms_api` (`id_sms_api`),
  KEY `debit_sms` (`debit_sms`),
  KEY `id_sms_api_2` (`id_sms_api`),
  KEY `debit_sms_2` (`debit_sms`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_purchase_log`
--

DROP TABLE IF EXISTS `sms_purchase_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_purchase_log` (
  `id_purchase_log` int NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Transaction , 2 - Promotion , 3 - OTP',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Amount Paid',
  `credits` int NOT NULL DEFAULT '0' COMMENT 'SMS Credited',
  `description` text COLLATE utf8mb4_unicode_ci,
  `credited_by` text COLLATE utf8mb4_unicode_ci COMMENT 'Credited User',
  `last_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id_purchase_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `state`
--

DROP TABLE IF EXISTS `state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `state` (
  `id_state` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `id_country` int NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `state_code` int DEFAULT NULL,
  PRIMARY KEY (`id_state`) USING BTREE,
  UNIQUE KEY `SIndex` (`id_state`,`id_country`),
  KEY `id_country` (`id_country`),
  KEY `id_state` (`id_state`),
  KEY `name` (`name`),
  KEY `id_country_2` (`id_country`),
  KEY `id_state_2` (`id_state`),
  KEY `name_2` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4121 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stone_details`
--

DROP TABLE IF EXISTS `stone_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stone_details` (
  `sid` int NOT NULL AUTO_INCREMENT,
  `Tag Number` varchar(21) DEFAULT NULL,
  `Less Wt` varchar(3) DEFAULT NULL,
  `StoneName` varchar(53) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Stone ID` int DEFAULT NULL,
  `Pieces` int DEFAULT NULL,
  `Weight` decimal(10,3) DEFAULT NULL,
  `Unit` varchar(5) DEFAULT NULL,
  `CAL TYPE` varchar(6) DEFAULT NULL,
  `Rate/Gram` varchar(10) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=4878 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supp_catalogue_images`
--

DROP TABLE IF EXISTS `supp_catalogue_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supp_catalogue_images` (
  `id_supp_cat_img` int NOT NULL AUTO_INCREMENT,
  `id_supp_catalogue` int NOT NULL,
  `id_catalogue_weight` int DEFAULT NULL,
  `image` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_add` datetime NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -> No, 1 -> Yes',
  PRIMARY KEY (`id_supp_cat_img`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sv_trans1_oct6`
--

DROP TABLE IF EXISTS `sv_trans1_oct6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sv_trans1_oct6` (
  `id_transaction` int DEFAULT NULL,
  `clientid` varchar(16) DEFAULT NULL,
  `mobile` varchar(10) DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `branch_code` varchar(10) DEFAULT NULL,
  `warehouse` varchar(10) DEFAULT NULL,
  `record_to` varchar(10) DEFAULT NULL,
  `payment date` varchar(10) DEFAULT NULL,
  `due_month` varchar(10) DEFAULT NULL,
  `due_year` varchar(10) DEFAULT NULL,
  `custom_entry_date` varchar(10) DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `weight` decimal(5,3) DEFAULT NULL,
  `rate` decimal(6,2) DEFAULT NULL,
  `metal` varchar(10) DEFAULT NULL,
  `payment_mode` varchar(18) DEFAULT NULL,
  `bank_name` varchar(10) DEFAULT NULL,
  `branch_name` varchar(10) DEFAULT NULL,
  `card_no` varchar(10) DEFAULT NULL,
  `ref_no` varchar(10) DEFAULT NULL,
  `pay_trans_id` varchar(10) DEFAULT NULL,
  `payment_ref_number` varchar(10) DEFAULT NULL,
  `paid_through` varchar(10) DEFAULT NULL,
  `is_transferred` varchar(10) DEFAULT NULL,
  `is_modified` varchar(10) DEFAULT NULL,
  `transfer_date` varchar(10) DEFAULT NULL,
  `new_customer` varchar(10) DEFAULT NULL,
  `id_scheme_account` varchar(10) DEFAULT NULL,
  `discountAmt` varchar(10) DEFAULT NULL,
  `payment_status` varchar(10) DEFAULT NULL,
  `payment_type` varchar(10) DEFAULT NULL,
  `due_type` varchar(10) DEFAULT NULL,
  `receipt_no` int DEFAULT NULL,
  `date_add` varchar(10) DEFAULT NULL,
  `date_upd` varchar(10) DEFAULT NULL,
  `installment_no` int DEFAULT NULL,
  `remarks` varchar(10) DEFAULT NULL,
  `gst` varchar(10) DEFAULT NULL,
  `gst_type` varchar(10) DEFAULT NULL,
  `emp_code` varchar(10) DEFAULT NULL,
  `id_drawee` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sync_log`
--

DROP TABLE IF EXISTS `sync_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sync_log` (
  `id_sync` int NOT NULL AUTO_INCREMENT,
  `total_records` int DEFAULT NULL,
  `scheme_accounts` int DEFAULT NULL,
  `payments` int DEFAULT NULL,
  `sync_date` datetime DEFAULT NULL,
  `remark` text,
  PRIMARY KEY (`id_sync`),
  KEY `scheme_accounts` (`scheme_accounts`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag_age_import`
--

DROP TABLE IF EXISTS `tag_age_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag_age_import` (
  `tagageid` int NOT NULL AUTO_INCREMENT,
  `branchId` int NOT NULL,
  `Branch` varchar(11) DEFAULT NULL,
  `Tag Number` varchar(13) DEFAULT NULL,
  `Tag Date` varchar(10) DEFAULT NULL,
  `TaggedOn` date NOT NULL,
  `Size ID` int DEFAULT '0',
  `Size` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`tagageid`),
  KEY `Size ID` (`Size ID`),
  KEY `Tag Number` (`Tag Number`),
  KEY `branchId` (`branchId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag_charges`
--

DROP TABLE IF EXISTS `tag_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag_charges` (
  `Tag Number` varchar(21) DEFAULT NULL,
  `Charge Name` varchar(100) DEFAULT NULL,
  `Charge Value` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag_details`
--

DROP TABLE IF EXISTS `tag_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `PRODUCTID` varchar(10) DEFAULT NULL,
  `Category` varchar(100) DEFAULT NULL,
  `Product Name` varchar(100) DEFAULT NULL,
  `Design Name` varchar(100) DEFAULT NULL,
  `Sub Design Name` varchar(100) DEFAULT NULL,
  `Sub Design ID` int DEFAULT NULL,
  `Purity` decimal(10,2) DEFAULT NULL,
  `Pieces` int DEFAULT NULL,
  `Gross Wt` decimal(10,3) DEFAULT NULL,
  `Net Wt` decimal(10,3) DEFAULT NULL,
  `Less Wt` decimal(10,3) DEFAULT NULL,
  `Wastage Per` decimal(10,2) DEFAULT NULL,
  `Wastage Wt` decimal(10,3) DEFAULT NULL,
  `MC Type` varchar(20) DEFAULT NULL,
  `MC Value` decimal(10,2) DEFAULT NULL,
  `Sales Value` decimal(10,2) DEFAULT NULL,
  `RATEPERGRAM` decimal(10,2) DEFAULT NULL,
  `Branch` varchar(100) DEFAULT NULL,
  `MFR Code` varchar(100) DEFAULT NULL,
  `Style Code` varchar(100) DEFAULT NULL,
  `Tag Number` varchar(30) DEFAULT NULL,
  `Certification No` varchar(100) DEFAULT NULL,
  `Purchase Cost` decimal(10,2) DEFAULT NULL,
  `Tag Date` varchar(20) DEFAULT NULL,
  `HUID 1` varchar(20) DEFAULT NULL,
  `HUID 2` varchar(20) DEFAULT NULL,
  `K ID` int DEFAULT NULL,
  `KARIGAR` varchar(190) DEFAULT NULL,
  `Counter` varchar(124) DEFAULT NULL,
  `TRANSIT NO` varchar(100) DEFAULT NULL,
  `IMAGE FILE` varchar(200) DEFAULT NULL,
  `size` varchar(60) DEFAULT NULL,
  `SALEMODE` varchar(10) DEFAULT NULL,
  `TAGTYPE` varchar(20) DEFAULT NULL,
  `METALID` varchar(20) DEFAULT NULL,
  `MCPERGRAM` decimal(10,2) NOT NULL DEFAULT '0.00',
  `FMC` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39001 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag_lot_det_ids`
--

DROP TABLE IF EXISTS `tag_lot_det_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag_lot_det_ids` (
  `tag_id` int NOT NULL DEFAULT '0',
  `id_lot_inward_detail` int NOT NULL,
  `current_branch` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag_other_issue`
--

DROP TABLE IF EXISTS `tag_other_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag_other_issue` (
  `Tag No` varchar(13) DEFAULT NULL,
  `rowid` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=506 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag_remarks`
--

DROP TABLE IF EXISTS `tag_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag_remarks` (
  `lmxtag` varchar(13) DEFAULT NULL,
  `remarksnew` varchar(11) DEFAULT NULL,
  `Branch` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tn_village`
--

DROP TABLE IF EXISTS `tn_village`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tn_village` (
  `id_village` varchar(10) DEFAULT NULL,
  `village_name` varchar(70) DEFAULT NULL,
  `pincode` int DEFAULT NULL,
  `po_name` varchar(37) DEFAULT NULL,
  `sub_distname` varchar(23) DEFAULT NULL,
  `dist_name` varchar(15) DEFAULT NULL,
  `id_state` varchar(10) DEFAULT NULL,
  `is_default` varchar(10) DEFAULT NULL,
  `active` varchar(10) DEFAULT NULL,
  `date_add` varchar(10) DEFAULT NULL,
  `date_upd` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction` (
  `id_transaction` int NOT NULL AUTO_INCREMENT,
  `client_id` varchar(16) DEFAULT NULL COMMENT 'generated by offline s/w',
  `mobile` varchar(45) DEFAULT NULL,
  `id_branch` int DEFAULT NULL COMMENT 'Chit  a/c branh',
  `branch_code` varchar(45) DEFAULT NULL,
  `warehouse` varchar(45) DEFAULT NULL COMMENT 'Warehouse Code (For reference only)',
  `record_to` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Offline , 2 - Online',
  `payment_date` date DEFAULT NULL COMMENT 'entry date',
  `due_month` int DEFAULT NULL COMMENT 'Paid for Month',
  `due_year` year DEFAULT NULL,
  `custom_entry_date` date DEFAULT NULL COMMENT 'custom entry date (for shop ledger purpose)',
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'amount received from customer',
  `weight` decimal(12,4) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL COMMENT 'metal rate',
  `metal` varchar(1) DEFAULT NULL COMMENT 'metal code',
  `payment_mode` varchar(45) DEFAULT NULL COMMENT 'payment mode',
  `bank_name` varchar(45) DEFAULT NULL,
  `branch_name` varchar(45) DEFAULT NULL,
  `card_no` varchar(25) DEFAULT NULL COMMENT 'credit card number',
  `ref_no` varchar(100) NOT NULL COMMENT 'logimax payment reference no / OFFLINE brefno',
  `pay_trans_id` varchar(100) DEFAULT NULL COMMENT 'Payment gateway Transaction ID / Order ID',
  `payment_ref_number` varchar(100) DEFAULT NULL COMMENT 'Payment reference number like UTR',
  `paid_through` tinyint(1) DEFAULT NULL COMMENT '0 - admin , 1- web app, 2 - mobile app, 3 - Offline',
  `is_transferred` varchar(1) DEFAULT 'N' COMMENT 'Y if trans is read',
  `is_modified` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - No , 1- yes',
  `transfer_date` datetime DEFAULT NULL,
  `new_customer` varchar(1) DEFAULT NULL COMMENT 'Y - new and N - existing customer',
  `id_scheme_account` int DEFAULT NULL,
  `discountAmt` decimal(10,2) DEFAULT '0.00' COMMENT 'discount amount',
  `payment_status` int DEFAULT NULL COMMENT '1 - Success, 2 - Awaiting, 3 - Failed, 4 - Cancelled, 5 - Returned, 6 - Refund, 7 - Pending',
  `payment_type` tinyint(1) DEFAULT '1' COMMENT '1- Online , 2 - Offine',
  `due_type` varchar(2) DEFAULT 'ND',
  `receipt_no` varchar(25) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_upd` datetime DEFAULT NULL,
  `installment_no` int DEFAULT NULL,
  `remarks` text,
  `gst` decimal(10,2) NOT NULL COMMENT 'gst percentage',
  `gst_type` tinyint(1) NOT NULL COMMENT '0 - inclusive , 1 - exclusive',
  `emp_code` varchar(20) DEFAULT NULL COMMENT 'employee code (only for admin payments)',
  `id_drawee` int unsigned DEFAULT NULL,
  `benefit_value` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Digi gold benefit percentage value',
  `saved_benefit_amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Digi gold benefit amount',
  `saved_benefits_wgt` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Digi gold benefit weight',
  PRIMARY KEY (`id_transaction`),
  UNIQUE KEY `ref_no` (`ref_no`),
  KEY `id_drawee` (`id_drawee`),
  KEY `client_id` (`client_id`),
  KEY `id_branch` (`id_branch`),
  KEY `record_to` (`record_to`),
  KEY `metal` (`metal`),
  KEY `is_transferred` (`is_transferred`),
  KEY `pay_trans_id` (`pay_trans_id`),
  KEY `payment_type` (`payment_type`),
  KEY `payment_mode` (`payment_mode`),
  KEY `is_modified` (`is_modified`),
  KEY `payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `version`
--

DROP TABLE IF EXISTS `version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `version` (
  `id_version` int NOT NULL AUTO_INCREMENT,
  `version_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `client` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_version`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `view_reorder_details`
--

DROP TABLE IF EXISTS `view_reorder_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `view_reorder_details` (
  `min_pcs` decimal(32,0) DEFAULT NULL,
  `max_pcs` decimal(32,0) DEFAULT NULL,
  `size` mediumtext COLLATE utf8mb4_unicode_ci,
  `product_name` varchar(75) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `design_name` varchar(75) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `design_id` int DEFAULT NULL,
  `branch_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `weight_name` varchar(112) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_weight` decimal(10,3) DEFAULT NULL,
  `to_weight` decimal(10,3) DEFAULT NULL,
  `sub_design_name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_sub_design` int DEFAULT NULL,
  `id_wt_range` int DEFAULT NULL,
  `id_size` int DEFAULT NULL,
  `id_cart_order` decimal(10,0) DEFAULT NULL,
  `id_orderdetails` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totpcs` decimal(32,0) DEFAULT NULL,
  `totgrosswt` decimal(34,3) DEFAULT NULL,
  `totnet_wt` decimal(34,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `village`
--

DROP TABLE IF EXISTS `village`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `village` (
  `id_village` int NOT NULL AUTO_INCREMENT,
  `village_name` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `id_zone` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-Active,0-Inactive',
  `taluk` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `post_office` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `pincode` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-> Default, 0 -> Not Default.',
  `date_add` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id_village`),
  KEY `pincode` (`pincode`)
) ENGINE=InnoDB AUTO_INCREMENT=17574 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `village_zone`
--

DROP TABLE IF EXISTS `village_zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `village_zone` (
  `id_zone` int NOT NULL AUTO_INCREMENT,
  `id_branch` int DEFAULT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_zone`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wallet_account`
--

DROP TABLE IF EXISTS `wallet_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallet_account` (
  `id_wallet_account` int NOT NULL AUTO_INCREMENT,
  `id_customer` int DEFAULT NULL,
  `idemployee` int DEFAULT NULL,
  `id_employee` int DEFAULT NULL,
  `wallet_acc_number` varchar(20) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `opening` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00',
  `remark` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `date_upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_wallet_account`),
  KEY `id_wallet_account` (`id_wallet_account`),
  KEY `id_customer` (`id_customer`),
  KEY `id_employee` (`id_employee`),
  KEY `id_wallet_account_2` (`id_wallet_account`),
  KEY `id_customer_2` (`id_customer`),
  KEY `id_employee_2` (`id_employee`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wallet_category`
--

DROP TABLE IF EXISTS `wallet_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallet_category` (
  `id_wallet_category` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(25) DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - active , 0 - inactive',
  PRIMARY KEY (`id_wallet_category`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wallet_category_settings`
--

DROP TABLE IF EXISTS `wallet_category_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallet_category_settings` (
  `id_wcat_settings` int NOT NULL AUTO_INCREMENT,
  `id_category` int unsigned NOT NULL,
  `value` int NOT NULL,
  `point` decimal(5,0) DEFAULT NULL COMMENT 'point to be credited per value. Ex - 10 point per 100 Rs',
  `redeem_percent` decimal(5,0) DEFAULT NULL,
  `remark` text NOT NULL,
  `date_add` datetime NOT NULL,
  `last_update` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - active , 1 - inactive',
  PRIMARY KEY (`id_wcat_settings`),
  KEY `id_category_2` (`id_category`),
  KEY `active` (`active`),
  KEY `id_category` (`id_category`),
  KEY `active_2` (`active`),
  KEY `id_category_3` (`id_category`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wallet_settings`
--

DROP TABLE IF EXISTS `wallet_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallet_settings` (
  `id_wallet` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint unsigned DEFAULT NULL COMMENT '0 - Currency, 1 - Point',
  `currency` decimal(10,2) DEFAULT NULL,
  `value` int unsigned DEFAULT NULL,
  `effective_date` datetime DEFAULT NULL,
  `effect_previous` tinyint unsigned DEFAULT NULL,
  `active` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id_wallet`),
  KEY `type` (`type`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wallet_transaction`
--

DROP TABLE IF EXISTS `wallet_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallet_transaction` (
  `id_wallet_transaction` int NOT NULL AUTO_INCREMENT,
  `id_wallet_account` int DEFAULT NULL,
  `ref_no` text COMMENT 'bill no',
  `bill_id` int DEFAULT NULL,
  `date_transaction` datetime DEFAULT NULL,
  `id_employee` int DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-CRM,1-Retail',
  `transaction_type` tinyint(1) DEFAULT NULL COMMENT '0-Issue , 1 - Debit',
  `value` decimal(10,2) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_add` datetime DEFAULT NULL COMMENT 'Date of record added',
  `id_sch_ac` int DEFAULT NULL COMMENT 'Sch id of referral credit',
  `id_payment` int DEFAULT NULL,
  `credit_for` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_wallet_transaction`),
  KEY `id_wallet_account` (`id_wallet_account`),
  KEY `id_wallet_account_2` (`id_wallet_account`),
  KEY `id_wallet_account_3` (`id_wallet_account`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `web_registered_devices`
--

DROP TABLE IF EXISTS `web_registered_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `web_registered_devices` (
  `id_device` int NOT NULL AUTO_INCREMENT,
  `device_name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_branch` int DEFAULT NULL,
  `id_floor` int DEFAULT NULL,
  `id_counter` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `browser` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_device`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weight`
--

DROP TABLE IF EXISTS `weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weight` (
  `id_weight` int unsigned NOT NULL AUTO_INCREMENT,
  `weight` decimal(15,3) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_weight`),
  KEY `active` (`active`),
  KEY `id_weight` (`id_weight`),
  KEY `active_2` (`active`),
  KEY `id_weight_2` (`id_weight`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'retail_dev'
--

--
-- Dumping routines for database 'retail_dev'
--

--
-- Final view structure for view `ret_view_grn_pay_ledger`
--

/*!50001 DROP VIEW IF EXISTS `ret_view_grn_pay_ledger`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`retaillmx_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ret_view_grn_pay_ledger` AS select `grn`.`grn_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,'Purchase' AS `accType`,0 AS `credit`,sum(`grn`.`grn_purchase_amt`) AS `debit` from (`ret_grn_entry` `grn` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `grn`.`grn_karigar_id`))) where ((`grn`.`grn_bill_status` = 1) and (`grn`.`grn_purchase_amt` > 0)) group by `grn`.`grn_karigar_id` union select `po`.`po_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,'Purchase' AS `accType`,0 AS `credit`,sum(`po`.`tot_purchase_amt`) AS `debit` from (`ret_purchase_order` `po` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `po`.`po_karigar_id`))) where ((`po`.`is_suspense_stock` = 1) and (`po`.`isratefixed` = 0) and (`po`.`tot_purchase_amt` > 0)) group by `po`.`po_karigar_id` union select `po`.`pay_sup_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,'Payment' AS `accType`,sum(`po`.`pay_amt`) AS `credit`,0 AS `debit` from (`ret_po_payment` `po` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `po`.`pay_sup_id`))) where ((`po`.`pay_status` = 1) and (`po`.`pay_amt` > 0)) group by `po`.`pay_sup_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ret_view_smith_combined_ledger`
--

/*!50001 DROP VIEW IF EXISTS `ret_view_smith_combined_ledger`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`retaillmx_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ret_view_smith_combined_ledger` AS select 2 AS `ledger_type`,`pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/purchase/job_receipt/' AS `link`,`po`.`po_id` AS `print_id`,'type 1' AS `qry_type`,1 AS `trans_type`,`pitm`.`po_order_no` AS `trans_id`,`pitm`.`gross_wt` AS `gross_wt`,`pitm`.`net_wt` AS `net_wt`,`pitm`.`no_of_pcs` AS `no_of_pcs`,`pitm`.`purchase_touch` AS `purchase_touch`,`pitm`.`item_pure_wt` AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,1 AS `trans_rec_type`,sum(`pitm`.`item_cost`) AS `trans_amount`,`pitm`.`po_item_cat_id` AS `catid`,`pitm`.`po_item_pro_id` AS `proid`,1 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,concat(`pitm`.`fix_rate_per_grm`,if((`pitm`.`is_rate_fixed` = 1),'','(Un Fixed)')) AS `rate`,ifnull(`pitm`.`remark`,'') AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate` from ((((((`ret_purchase_order_items` `pitm` left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pitm`.`po_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pitm`.`po_item_pro_id`))) where ((`grn`.`grn_type` = 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1) and (`po`.`is_suspense_stock` = 1)) group by `pitm`.`po_item_id` union all select 2 AS `ledger_type`,'PAYMENT' AS `category`,'' AS `product`,`rf`.`date_add` AS `trans_date`,`rf`.`id_supplier_rate_cut` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/supplier_rate_cut/job_receipt/' AS `link`,`rf`.`id_supplier_rate_cut` AS `print_id`,'type 2' AS `qry_type`,2 AS `trans_type`,`rf`.`id_supplier_rate_cut` AS `trans_id`,`rf`.`weight` AS `gross_wt`,`rf`.`weight` AS `net_wt`,'' AS `no_of_pcs`,'' AS `purchase_touch`,`rf`.`weight` AS `purewt`,`rf`.`id_karigar` AS `customer_id`,2 AS `trans_rec_type`,`rf`.`amount` AS `trans_amount`,'' AS `catid`,'' AS `proid`,7 AS `trans_screen_id`,`rf`.`id_metal` AS `id_metal`,`rf`.`id_metal` AS `metal`,`rf`.`rate_per_gram` AS `rate`,ifnull(`rf`.`narration`,'') AS `narration`,unix_timestamp(`rf`.`date_add`) AS `unixtransdate` from (`ret_supplier_rate_cut` `rf` left join `ret_purchase_order` `po` on((`po`.`po_id` = `rf`.`po_id`))) where ((`rf`.`rate_cut_type` = 1) and (`rf`.`status` = 1)) group by `rf`.`id_supplier_rate_cut` union all select 2 AS `ledger_type`,if((`rf`.`weight` = 0),'Bill Conv(Amount)','Bill Conv(A to P)') AS `category`,'' AS `product`,`rf`.`date_add` AS `trans_date`,`rf`.`id_supplier_rate_cut` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/supplier_rate_cut/job_receipt/' AS `link`,`rf`.`id_supplier_rate_cut` AS `print_id`,'type 3' AS `qry_type`,2 AS `trans_type`,`rf`.`id_supplier_rate_cut` AS `trans_id`,`rf`.`weight` AS `gross_wt`,`rf`.`weight` AS `net_wt`,'' AS `no_of_pcs`,if((`rf`.`weight` = 0),'','100') AS `purchase_touch`,`rf`.`weight` AS `purewt`,`rf`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,if((`rf`.`charges_amount` > 0),`rf`.`charges_amount`,'') AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,`rf`.`id_metal` AS `id_metal`,`rf`.`id_metal` AS `metal`,`rf`.`rate_per_gram` AS `rate`,ifnull(`rf`.`narration`,'') AS `narration`,unix_timestamp(`rf`.`date_add`) AS `unixtransdate` from (`ret_supplier_rate_cut` `rf` left join `ret_purchase_order` `po` on((`po`.`po_id` = `rf`.`po_id`))) where ((`rf`.`rate_cut_type` = 2) and (`rf`.`status` = 1)) group by `rf`.`id_supplier_rate_cut` union all select 2 AS `ledger_type`,`pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/return_receipt_acknowladgement/' AS `link`,`ret`.`pur_return_id` AS `print_id`,'type 4' AS `qry_type`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,`pret`.`pur_ret_pur_wt` AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,1 AS `trans_rec_type`,sum(ifnull(`pret`.`pur_ret_debit_note_amt`,0)) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,`pret`.`pur_ret_rate` AS `rate`,ifnull(`ret`.`pur_ret_remark`,'') AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate` from ((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 3) and (`ret`.`purchase_type` = 0) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union all select 2 AS `ledger_type`,`pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/return_receipt_acknowladgement/' AS `link`,`ret`.`pur_return_id` AS `print_id`,'type 5' AS `qry_type`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,`pret`.`pur_ret_pur_wt` AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,1 AS `trans_rec_type`,sum(ifnull(`pret`.`pur_ret_debit_note_amt`,0)) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,`pret`.`pur_ret_rate` AS `rate`,ifnull(`ret`.`pur_ret_remark`,'') AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate` from ((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 3) and (`ret`.`purchase_type` = 1) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union all select 2 AS `ledger_type`,`pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`iss`.`met_issue_date` AS `trans_date`,`iss`.`met_issue_ref_id` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/karigarmetalissue_acknowladgement/' AS `link`,`iitm`.`issue_met_parent_id` AS `print_id`,'type 6' AS `qry_type`,2 AS `trans_type`,`iitm`.`issue_met_parent_id` AS `trans_id`,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3)) AS `gross_wt`,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3)) AS `net_wt`,ifnull(`iitm`.`issue_pcs`,1) AS `no_of_pcs`,ifnull(`iitm`.`touch`,'') AS `purchase_touch`,if((`pr`.`stone_type` = 0),`iitm`.`issue_metal_pur_wt`,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `purewt`,`iss`.`met_issue_karid` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,`iitm`.`issue_cat_id` AS `catid`,`iitm`.`issu_met_pro_id` AS `proid`,2 AS `trans_screen_id`,`iitm`.`issue_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,ifnull(`iss`.`remark`,'') AS `narration`,unix_timestamp(`iss`.`met_issue_date`) AS `unixtransdate` from ((((((((`ret_karigar_metal_issue_details` `iitm` left join `ret_karigar_metal_issue` `iss` on((`iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `iss`.`met_issue_karid`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `iitm`.`issue_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `iitm`.`issu_met_pro_id`))) left join `ret_uom` `uom` on((`uom`.`uom_id` = `iitm`.`issue_uom_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `iss`.`po_id`))) left join `ret_purchase_order_items` `poitm` on((`poitm`.`po_item_id` = `iitm`.`po_item_id`))) where ((`iss`.`metalissue_type` = 2) and (`iss`.`bill_status` = 1)) group by `iitm`.`issue_met_id`,`iitm`.`issue_cat_id`,`iitm`.`issu_met_pro_id`,`iitm`.`issue_met_parent_id` union all select 2 AS `ledger_type`,'OPENING' AS `category`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,'' AS `po_date`,'' AS `po_ref_no`,'' AS `link`,'' AS `print_id`,'type 7' AS `qry_type`,`pay`.`amount_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,sum(`pay`.`amount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`remarks`,'') AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate` from `smith_company_op_balance` `pay` where ((`pay`.`smith_type` = 3) and (`pay`.`stock_type` = 2) and (`pay`.`amount` > 0)) group by `pay`.`id_smith_company_op_balance` union all select 2 AS `ledger_type`,'OPENING' AS `category`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,'' AS `po_date`,'' AS `po_ref_no`,'' AS `link`,'' AS `print_id`,'type 8' AS `qry_type`,`pay`.`weight_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,ifnull(`pay`.`weight`,0) AS `gross_wt`,ifnull(`pay`.`weight`,0) AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,ifnull(`pay`.`weight`,0) AS `purewt`,`pay`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,`pay`.`id_metal` AS `id_metal`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`remarks`,'') AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate` from `smith_company_op_balance` `pay` where ((`pay`.`smith_type` = 3) and (`pay`.`stock_type` = 2) and (`pay`.`weight` > 0)) group by `pay`.`id_smith_company_op_balance` union all select 2 AS `ledger_type`,if((`pay`.`transtype` = 1),'Credit Note','Debit Note') AS `category`,'' AS `product`,`pay`.`transdate` AS `trans_date`,`pay`.`transbillno` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/credit_debit_acknolodgement/' AS `link`,`pay`.`crdrid` AS `print_id`,'type 9' AS `qry_type`,`pay`.`transtype` AS `trans_type`,`pay`.`transbillno` AS `trans_id`,ifnull(`pay`.`weight`,0) AS `gross_wt`,ifnull(`pay`.`weight`,0) AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,ifnull(`pay`.`weight`,0) AS `purewt`,`pay`.`supid` AS `customer_id`,1 AS `trans_rec_type`,sum(`pay`.`transamount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,`cate`.`id_metal` AS `id_metal`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`naration`,'') AS `narration`,unix_timestamp(`pay`.`transdate`) AS `unixtransdate` from (((`ret_crdr_note` `pay` left join `ret_purchase_order` `po` on((`po`.`po_id` = `pay`.`po_id`))) left join (select `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,`ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id` from `ret_purchase_order_items` group by `ret_purchase_order_items`.`po_item_po_id`) `poitm` on((`pay`.`po_id` = `poitm`.`po_item_po_id`))) left join `ret_category` `cate` on((`cate`.`id_ret_category` = `poitm`.`po_item_cat_id`))) where ((`pay`.`accountto` = 3) and (`pay`.`weight` > 0) and (`pay`.`crdr_status` = 1)) group by `pay`.`crdrid` union all select 1 AS `ledger_type`,`pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/purchase/job_receipt/' AS `link`,`po`.`po_id` AS `print_id`,'type 10' AS `qry_type`,1 AS `trans_type`,`pitm`.`po_order_no` AS `trans_id`,`pitm`.`gross_wt` AS `gross_wt`,`pitm`.`net_wt` AS `net_wt`,`pitm`.`no_of_pcs` AS `no_of_pcs`,`pitm`.`purchase_touch` AS `purchase_touch`,`pitm`.`item_pure_wt` AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`pitm`.`item_cost`) AS `trans_amount`,`pitm`.`po_item_cat_id` AS `catid`,`pitm`.`po_item_pro_id` AS `proid`,1 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,concat(`pitm`.`fix_rate_per_grm`,if((`pitm`.`is_rate_fixed` = 1),'','(Un Fixed)')) AS `rate`,ifnull(`pitm`.`remark`,'') AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate` from ((((((`ret_purchase_order_items` `pitm` left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pitm`.`po_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pitm`.`po_item_pro_id`))) where ((`grn`.`grn_type` <> 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1) and (`po`.`is_suspense_stock` = 0)) group by `pitm`.`po_item_id` union all select 1 AS `ledger_type`,'PAYMENT' AS `category`,'' AS `product`,`pay`.`pay_create_on` AS `trans_date`,`pay`.`pay_refno` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/supplier_po_payment/paymentacknolodgement/' AS `link`,`pay`.`pay_id` AS `print_id`,'type 11' AS `qry_type`,2 AS `trans_type`,`pay`.`pay_id` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`pay_sup_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`pd`.`payment_amount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,`cate`.`id_metal` AS `id_metal`,'' AS `metal`,'' AS `rate`,`pay`.`pay_narration` AS `narration`,unix_timestamp(`pay`.`pay_create_on`) AS `unixtransdate` from (((((`ret_po_payment` `pay` left join `ret_po_payment_detail` `pd` on((`pd`.`pay_id` = `pay`.`pay_id`))) left join `ret_po_bill_payment_details` `po_bill` on((`po_bill`.`pay_id` = `pay`.`pay_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `po_bill`.`po_id`))) left join (select distinct `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,`ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id` from `ret_purchase_order_items` limit 1) `order` on((`order`.`po_item_po_id` = `po`.`po_id`))) left join `ret_category` `cate` on((`cate`.`id_ret_category` = `order`.`po_item_cat_id`))) where ((`pay`.`pay_status` = 1) and (`pay`.`bill_type` = 1)) group by `pay`.`pay_id` union all select 1 AS `ledger_type`,concat('RATE FIXING',if((`pitm`.`rate` > `rf`.`rate_fix_rate`),'(Dr)','(Cr)')) AS `category`,'' AS `product`,`rf`.`rate_fix_created_on` AS `trans_date`,`rf`.`rate_fix_id` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'' AS `link`,'' AS `print_id`,'type 12' AS `qry_type`,if((`pitm`.`rate` > `rf`.`rate_fix_rate`),2,1) AS `trans_type`,`rf`.`rate_fix_id` AS `trans_id`,'' AS `gross_wt`,'' AS `net_wt`,'' AS `no_of_pcs`,'' AS `purchase_touch`,'' AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,2 AS `trans_rec_type`,round(abs((((`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt`) * 1.03)),2) AS `trans_amount`,'' AS `catid`,'' AS `proid`,7 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,`rf`.`rate_fix_rate` AS `rate`,'' AS `narration`,unix_timestamp(`rf`.`rate_fix_created_on`) AS `unixtransdate` from ((((`ret_po_rate_fix` `rf` left join `ret_purchase_order` `po` on((`po`.`po_id` = `rf`.`rate_fix_po_item_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join (select `pitm`.`po_item_po_id` AS `poid`,`pitm`.`fix_rate_per_grm` AS `rate` from `ret_purchase_order_items` `pitm` group by `pitm`.`po_item_po_id`) `pitm` on((`pitm`.`poid` = `po`.`po_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) where ((`po`.`isratefixed` = 0) and (`po`.`is_suspense_stock` = 0) and (`rf`.`bill_status` = 1)) group by `rf`.`rate_fix_id` union all select 1 AS `ledger_type`,concat('RATE FIXING',if((`rc`.`rate_per_gram` > `rf`.`rate_fix_rate`),'(Dr)','(Cr)')) AS `category`,'' AS `product`,`rf`.`rate_fix_created_on` AS `trans_date`,`rf`.`rate_fix_id` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'' AS `link`,'' AS `print_id`,'type 13' AS `qry_type`,if((`rc`.`rate_per_gram` > `rf`.`rate_fix_rate`),2,1) AS `trans_type`,`rf`.`rate_fix_id` AS `trans_id`,'' AS `gross_wt`,'' AS `net_wt`,'' AS `no_of_pcs`,'' AS `purchase_touch`,'' AS `purewt`,`rc`.`id_karigar` AS `customer_id`,2 AS `trans_rec_type`,round(abs((((`rc`.`rate_per_gram` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt`) * 1.03)),2) AS `trans_amount`,'' AS `catid`,'' AS `proid`,7 AS `trans_screen_id`,`rc`.`id_metal` AS `id_metal`,'' AS `metal`,`rf`.`rate_fix_rate` AS `rate`,'' AS `narration`,unix_timestamp(`rf`.`rate_fix_created_on`) AS `unixtransdate` from ((`ret_po_rate_fix` `rf` left join `ret_supplier_rate_cut` `rc` on((`rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `rc`.`po_id`))) where ((`rf`.`rate_fix_type` = 2) and (`rf`.`bill_status` = 1) and (`rc`.`conversion_type` = 1)) group by `rf`.`rate_fix_id` union all select 1 AS `ledger_type`,`pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/return_receipt_acknowladgement/' AS `link`,`ret`.`pur_return_id` AS `print_id`,'type 14' AS `qry_type`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,`pret`.`pur_ret_pur_wt` AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,2 AS `trans_rec_type`,sum(ifnull(`pret`.`pur_ret_debit_note_amt`,0)) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate` from ((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 1) and (`ret`.`purchase_type` = 0) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union all select 1 AS `ledger_type`,`pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/return_receipt_acknowladgement/' AS `link`,`ret`.`pur_return_id` AS `print_id`,'type 15' AS `qry_type`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,`pret`.`pur_ret_pur_wt` AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,2 AS `trans_rec_type`,sum(ifnull(`pret`.`pur_ret_debit_note_amt`,0)) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate` from ((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 1) and (`ret`.`purchase_type` = 1) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union all select 1 AS `ledger_type`,ifnull(`pr`.`product_name`,'PURE') AS `category`,'' AS `product`,`rf`.`date_add` AS `trans_date`,`rf`.`id_supplier_rate_cut` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/supplier_rate_cut/job_receipt/' AS `link`,`rf`.`id_supplier_rate_cut` AS `print_id`,'type 16' AS `qry_type`,1 AS `trans_type`,`rf`.`id_supplier_rate_cut` AS `trans_id`,`rf`.`weight` AS `gross_wt`,`rf`.`weight` AS `net_wt`,'' AS `no_of_pcs`,'100' AS `purchase_touch`,`rf`.`weight` AS `purewt`,`rf`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,`rf`.`amount` AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,`rf`.`id_metal` AS `id_metal`,`rf`.`id_metal` AS `metal`,if((`rf`.`conversion_type` = 2),concat(`rf`.`rate_per_gram`,'(Unfix)'),`rf`.`rate_per_gram`) AS `rate`,ifnull(`rf`.`narration`,'') AS `narration`,unix_timestamp(`rf`.`date_add`) AS `unixtransdate` from ((`ret_supplier_rate_cut` `rf` left join `ret_product_master` `pr` on((`pr`.`pro_id` = `rf`.`id_product`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `rf`.`po_id`))) where ((`rf`.`rate_cut_type` = 2) and (`rf`.`status` = 1)) group by `rf`.`id_supplier_rate_cut` union all select 1 AS `ledger_type`,'OPENING' AS `category`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,'' AS `po_date`,'' AS `po_ref_no`,'' AS `link`,'' AS `print_id`,'type 17' AS `qry_type`,`pay`.`amount_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`id_karigar` AS `customer_id`,2 AS `trans_rec_type`,sum(`pay`.`amount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`remarks`,'') AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate` from `smith_company_op_balance` `pay` where (((`pay`.`smith_type` = 1) or (`pay`.`smith_type` = 4)) and (`pay`.`stock_type` = 2) and (`pay`.`amount` > 0)) group by `pay`.`id_smith_company_op_balance` union all select 1 AS `ledger_type`,'OPENING' AS `category`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,'' AS `po_date`,'' AS `po_ref_no`,'' AS `link`,'' AS `print_id`,'type 18' AS `qry_type`,`pay`.`amount_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,ifnull(`pay`.`weight`,0) AS `gross_wt`,ifnull(`pay`.`weight`,0) AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`remarks`,'') AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate` from `smith_company_op_balance` `pay` where (((`pay`.`smith_type` = 1) or (`pay`.`smith_type` = 4)) and (`pay`.`stock_type` = 2) and (`pay`.`weight` > 0)) group by `pay`.`id_smith_company_op_balance` union all select 1 AS `ledger_type`,if((`pay`.`transtype` = 1),'Credit Note','Debit Note') AS `category`,'' AS `product`,`pay`.`transdate` AS `trans_date`,`pay`.`transbillno` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/credit_debit_acknolodgement/' AS `link`,`pay`.`crdrid` AS `print_id`,'type 19' AS `qry_type`,`pay`.`transtype` AS `trans_type`,`pay`.`transbillno` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`supid` AS `customer_id`,1 AS `trans_rec_type`,sum(`pay`.`transamount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,`cate`.`id_metal` AS `id_metal`,'' AS `metal`,'' AS `rate`,`pay`.`naration` AS `narration`,unix_timestamp(`pay`.`transdate`) AS `unixtransdate` from (((`ret_crdr_note` `pay` left join `ret_purchase_order` `po` on((`po`.`po_id` = `pay`.`po_id`))) left join (select `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,`ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id` from `ret_purchase_order_items` group by `ret_purchase_order_items`.`po_item_po_id`) `poitm` on((`pay`.`po_id` = `poitm`.`po_item_po_id`))) left join `ret_category` `cate` on((`cate`.`id_ret_category` = `poitm`.`po_item_cat_id`))) where ((`pay`.`accountto` = 1) and (`pay`.`transamount` > 0) and (`pay`.`crdr_status` = 1)) group by `pay`.`crdrid` union all select 1 AS `ledger_type`,'TDS' AS `category`,'' AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/purchase/job_receipt/' AS `link`,`po`.`po_id` AS `print_id`,'type 20' AS `qry_type`,2 AS `trans_type`,`po`.`po_id` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`po`.`tds_tax_value`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate` from ((`ret_purchase_order` `po` left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) where ((`grn`.`grn_type` <> 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1) and (`po`.`is_suspense_stock` = 0) and (`po`.`tds_tax_value` > 0)) group by `po`.`po_id` union all select 1 AS `ledger_type`,'TCS' AS `category`,'' AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,`po`.`po_date` AS `po_date`,`po`.`po_ref_no` AS `po_ref_no`,'/purchase/job_receipt/' AS `link`,`po`.`po_id` AS `print_id`,'type 21' AS `qry_type`,2 AS `trans_type`,`po`.`po_id` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`po`.`tcs_tax_value`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate` from ((`ret_purchase_order` `po` left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) where ((`grn`.`grn_type` <> 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1) and (`po`.`is_suspense_stock` = 0) and (`po`.`tcs_tax_value` > 0)) group by `po`.`po_id` order by `unixtransdate` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ret_view_smith_ledger`
--

/*!50001 DROP VIEW IF EXISTS `ret_view_smith_ledger`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`retaillmx_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ret_view_smith_ledger` AS select `pd`.`design_name` AS `category`,`pr`.`product_name` AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,1 AS `trans_type`,`pitm`.`po_order_no` AS `trans_id`,sum(if((ifnull(`uom`.`divided_by_value`,0) = 0),`pitm`.`gross_wt`,round((`pitm`.`gross_wt` / `uom`.`divided_by_value`),3))) AS `gross_wt`,sum(if((ifnull(`uom`.`divided_by_value`,0) = 0),`pitm`.`net_wt`,round((`pitm`.`net_wt` / `uom`.`divided_by_value`),3))) AS `net_wt`,sum(`pitm`.`no_of_pcs`) AS `no_of_pcs`,`pitm`.`purchase_touch` AS `purchase_touch`,sum(`pitm`.`item_pure_wt`) AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,1 AS `trans_rec_type`,((ifnull(`pochr`.`charge`,0) + ifnull(`post`.`stamount`,0)) + if((`pitm`.`mc_type` = 1),(`pitm`.`mc_value` * `pitm`.`gross_wt`),(`pitm`.`mc_value` * `pitm`.`no_of_pcs`))) AS `trans_amount`,`pitm`.`po_item_cat_id` AS `catid`,`pr`.`stone_type` AS `stone_type`,`pitm`.`po_item_pro_id` AS `proid`,1 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,ifnull(`pitm`.`remark`,'') AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate`,`uom`.`uom_short_code` AS `dispuom` from ((((((((((`ret_purchase_order_items` `pitm` left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pitm`.`po_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pitm`.`po_item_pro_id`))) left join `ret_design_master` `pd` on((`pd`.`design_no` = `pitm`.`po_item_des_id`))) left join `ret_uom` `uom` on((`uom`.`uom_id` = `pitm`.`uom`))) left join (select sum(`ret_purchase_other_charges`.`total_charge_value`) AS `charge`,`ret_purchase_other_charges`.`pur_po_item_id` AS `pur_po_item_id` from `ret_purchase_other_charges` group by `ret_purchase_other_charges`.`pur_po_item_id`) `pochr` on((`pochr`.`pur_po_item_id` = `pitm`.`po_item_id`))) left join (select sum(`ret_po_stone_items`.`po_stone_amount`) AS `stamount`,`ret_po_stone_items`.`po_item_id` AS `po_item_id` from `ret_po_stone_items` group by `ret_po_stone_items`.`po_item_id`) `post` on((`post`.`po_item_id` = `pitm`.`po_item_id`))) where ((`grn`.`grn_type` = 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1)) group by `pitm`.`po_item_id` union select `pd`.`design_name` AS `category`,`pr`.`product_name` AS `product`,`po`.`po_date` AS `trans_date`,`iss`.`met_issue_ref_id` AS `referenceno`,1 AS `trans_type`,`pitm`.`po_order_no` AS `trans_id`,sum(if((ifnull(`uom`.`divided_by_value`,0) = 0),`posi`.`po_stone_wt`,round((`posi`.`po_stone_wt` / `uom`.`divided_by_value`),3))) AS `gross_wt`,sum(if((ifnull(`uom`.`divided_by_value`,0) = 0),`posi`.`po_stone_wt`,round((`posi`.`po_stone_wt` / `uom`.`divided_by_value`),3))) AS `net_wt`,sum(`posi`.`po_stone_pcs`) AS `no_of_pcs`,'' AS `purchase_touch`,'' AS `purewt`,`iss`.`met_issue_karid` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,`pitm`.`po_item_cat_id` AS `catid`,`pr`.`stone_type` AS `stone_type`,`pitm`.`po_item_pro_id` AS `proid`,1 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,ifnull(`pitm`.`remark`,'') AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate`,`uom`.`uom_short_code` AS `dispuom` from ((((((((((`ret_po_stone_items` `posi` left join `ret_karigar_metal_issue_details` `iitm` on((`iitm`.`issue_met_id` = `posi`.`issue_met_id`))) left join `ret_karigar_metal_issue` `iss` on((`iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `iss`.`met_issue_karid`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `iitm`.`issue_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `iitm`.`issu_met_pro_id`))) left join `ret_design_master` `pd` on((`pd`.`design_no` = `iitm`.`issu_met_id_design`))) left join `ret_uom` `uom` on((`uom`.`uom_id` = `posi`.`po_stone_uom`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `posi`.`po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) where ((`posi`.`issue_met_id` is not null) and (`po`.`bill_status` = 1)) group by `posi`.`po_st_id` union select `pd`.`design_name` AS `category`,`pr`.`product_name` AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,1 AS `trans_type`,`pitm`.`po_order_no` AS `trans_id`,sum(if((ifnull(`uom`.`divided_by_value`,0) = 0),`pitm`.`gross_wt`,round((`pitm`.`gross_wt` / `uom`.`divided_by_value`),3))) AS `gross_wt`,sum(if((ifnull(`uom`.`divided_by_value`,0) = 0),`pitm`.`net_wt`,round((`pitm`.`net_wt` / `uom`.`divided_by_value`),3))) AS `net_wt`,sum(`pitm`.`no_of_pcs`) AS `no_of_pcs`,'' AS `purchase_touch`,'' AS `purewt`,`iss`.`met_issue_karid` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,`pitm`.`po_item_cat_id` AS `catid`,`pr`.`stone_type` AS `stone_type`,`pitm`.`po_item_pro_id` AS `proid`,1 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,ifnull(`pitm`.`remark`,'') AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate`,`uom`.`uom_short_code` AS `dispuom` from (((((((((`ret_purchase_order_items` `pitm` left join `ret_karigar_metal_issue_details` `iitm` on((`iitm`.`issue_met_id` = `pitm`.`issue_met_id`))) left join `ret_karigar_metal_issue` `iss` on((`iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `iss`.`met_issue_karid`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `iitm`.`issue_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `iitm`.`issu_met_pro_id`))) left join `ret_design_master` `pd` on((`pd`.`design_no` = `iitm`.`issu_met_id_design`))) left join `ret_uom` `uom` on((`uom`.`uom_id` = `pitm`.`uom`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) where ((`pitm`.`issue_met_id` is not null) and (`po`.`bill_status` = 1)) group by `pitm`.`po_item_id` union select `pd`.`design_name` AS `category`,`pr`.`product_name` AS `product`,`iss`.`met_issue_date` AS `trans_date`,`iss`.`met_issue_ref_id` AS `referenceno`,2 AS `trans_type`,`iitm`.`issue_met_parent_id` AS `trans_id`,if((`cat`.`cat_type` = 2),0,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `gross_wt`,if((`cat`.`cat_type` = 2),0,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `net_wt`,ifnull(`iitm`.`issue_pcs`,1) AS `no_of_pcs`,'100' AS `purchase_touch`,if((`pr`.`stone_type` = 0),`iitm`.`issue_metal_pur_wt`,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `purewt`,`iss`.`met_issue_karid` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,`iitm`.`issue_cat_id` AS `catid`,`pr`.`stone_type` AS `stone_type`,`iitm`.`issu_met_pro_id` AS `proid`,2 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`iss`.`met_issue_date`) AS `unixtransdate`,`uom`.`uom_short_code` AS `dispuom` from (((((((`ret_karigar_metal_issue_details` `iitm` left join `ret_karigar_metal_issue` `iss` on((`iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `iss`.`met_issue_karid`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `iitm`.`issue_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `iitm`.`issu_met_pro_id`))) left join `ret_design_master` `pd` on((`pd`.`design_no` = `iitm`.`issu_met_id_design`))) left join `ret_uom` `uom` on((`uom`.`uom_id` = `iitm`.`issue_uom_id`))) where (((`kr`.`karigar_for` = 1) or (`kr`.`karigar_for` = 5)) and (`iss`.`bill_status` = 1)) group by `iitm`.`issue_cat_id`,`iitm`.`issu_met_pro_id`,`iitm`.`issue_met_parent_id` union select 'PAYMENT' AS `category`,'' AS `product`,`pay`.`pay_create_on` AS `trans_date`,`pay`.`pay_refno` AS `referenceno`,2 AS `trans_type`,`pay`.`pay_id` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`pay_sup_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`pd`.`payment_amount`) AS `trans_amount`,'' AS `catid`,0 AS `stone_type`,'' AS `proid`,3 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`pay`.`pay_create_on`) AS `unixtransdate`,'' AS `dispuom` from (`ret_po_payment` `pay` left join `ret_po_payment_detail` `pd` on((`pd`.`pay_id` = `pay`.`pay_id`))) where ((`pay`.`pay_status` = 1) and (`pay`.`bill_type` = 2)) group by `pay`.`pay_id` union select `pd`.`design_name` AS `category`,`pr`.`product_name` AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,sum(`pret`.`pur_ret_pur_wt`) AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,1 AS `trans_rec_type`,sum(`pret`.`pur_ret_debit_note_amt`) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pr`.`stone_type` AS `stone_type`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate`,'' AS `dispuom` from (((((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join (select sum(`ret_purchase_return_other_charges`.`total_charge_value`) AS `charges`,`ret_purchase_return_other_charges`.`pur_ret_itm_id` AS `pur_ret_itm_id` from `ret_purchase_return_other_charges` group by `ret_purchase_return_other_charges`.`pur_ret_itm_id`) `retchr` on((`retchr`.`pur_ret_itm_id` = `pret`.`pur_ret_itm_id`))) left join (select sum(`ret_purchase_return_stone_items`.`ret_stone_amount`) AS `stamount`,`ret_purchase_return_stone_items`.`pur_ret_return_id` AS `pur_ret_return_id` from `ret_purchase_return_stone_items` group by `ret_purchase_return_stone_items`.`pur_ret_return_id`) `retst` on((`retst`.`pur_ret_return_id` = `pret`.`pur_ret_itm_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_design_master` `pd` on((`pd`.`design_no` = `pret`.`id_design`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 2) and (`ret`.`purchase_type` = 0) and (`grn`.`grn_type` = 2) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union select `pd`.`design_name` AS `category`,`pr`.`product_name` AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,sum(`pret`.`pur_ret_pur_wt`) AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,1 AS `trans_rec_type`,sum(`pret`.`pur_ret_debit_note_amt`) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pr`.`stone_type` AS `stone_type`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate`,'' AS `dispuom` from (((((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join (select sum(`ret_purchase_return_other_charges`.`total_charge_value`) AS `charges`,`ret_purchase_return_other_charges`.`pur_ret_itm_id` AS `pur_ret_itm_id` from `ret_purchase_return_other_charges` group by `ret_purchase_return_other_charges`.`pur_ret_itm_id`) `retchr` on((`retchr`.`pur_ret_itm_id` = `pret`.`pur_ret_itm_id`))) left join (select sum(`ret_purchase_return_stone_items`.`ret_stone_amount`) AS `stamount`,`ret_purchase_return_stone_items`.`pur_ret_return_id` AS `pur_ret_return_id` from `ret_purchase_return_stone_items` group by `ret_purchase_return_stone_items`.`pur_ret_return_id`) `retst` on((`retst`.`pur_ret_return_id` = `pret`.`pur_ret_itm_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_design_master` `pd` on((`pd`.`design_no` = `pret`.`id_design`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 2) and (`grn`.`grn_type` = 2) and (`ret`.`bill_status` = 1)) group by `ret`.`pur_return_id` union select 'OPENING' AS `category`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,`pay`.`amount_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,sum(`pay`.`amount`) AS `trans_amount`,'' AS `catid`,0 AS `stone_type`,'' AS `proid`,1 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate`,'' AS `dispuom` from `smith_company_op_balance` `pay` where ((`pay`.`smith_type` = 2) and (`pay`.`stock_type` = 2) and (`pay`.`amount` > 0)) group by `pay`.`id_smith_company_op_balance` union select 'OPENING' AS `category`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,`pay`.`weight_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,ifnull(`pay`.`weight`,0) AS `gross_wt`,ifnull(`pay`.`weight`,0) AS `net_wt`,0 AS `no_of_pcs`,100 AS `purchase_touch`,ifnull(`pay`.`weight`,0) AS `purewt`,`pay`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,'' AS `catid`,0 AS `stone_type`,'' AS `proid`,1 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate`,'' AS `dispuom` from `smith_company_op_balance` `pay` where ((`pay`.`smith_type` = 2) and (`pay`.`stock_type` = 2) and (`pay`.`weight` > 0)) group by `pay`.`id_smith_company_op_balance` union select if((`pay`.`transtype` = 1),'Credit Note','Debit Note') AS `category`,'' AS `product`,`pay`.`transdate` AS `trans_date`,`pay`.`transbillno` AS `referenceno`,`pay`.`transtype` AS `trans_type`,`pay`.`transbillno` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`supid` AS `customer_id`,1 AS `trans_rec_type`,sum(`pay`.`transamount`) AS `trans_amount`,'' AS `catid`,0 AS `stone_type`,'' AS `proid`,1 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,`pay`.`naration` AS `narration`,unix_timestamp(`pay`.`transdate`) AS `unixtransdate`,'' AS `dispuom` from `ret_crdr_note` `pay` where ((`pay`.`accountto` = 2) and (`pay`.`transamount` > 0) and (`pay`.`crdr_status` = 1)) group by `pay`.`crdrid` union select `pd`.`design_name` AS `category`,`pr`.`product_name` AS `product`,`ki`.`met_issue_date` AS `trans_date`,`iss`.`ref_no` AS `referenceno`,2 AS `trans_type`,`iitm`.`issue_met_parent_id` AS `trans_id`,if((`cat`.`cat_type` = 2),0,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `gross_wt`,if((`cat`.`cat_type` = 2),0,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `net_wt`,ifnull(`iitm`.`issue_pcs`,1) AS `no_of_pcs`,'100' AS `purchase_touch`,if((`pr`.`stone_type` = 0),`iitm`.`issue_metal_pur_wt`,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `purewt`,`iss`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,`iitm`.`issue_cat_id` AS `catid`,`pr`.`stone_type` AS `stone_type`,`iitm`.`issu_met_pro_id` AS `proid`,2 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`ki`.`met_issue_date`) AS `unixtransdate`,`uom`.`uom_short_code` AS `dispuom` from ((((((((`ret_karigar_metal_issue_details` `iitm` left join `ret_karigar_metal_issue` `ki` on((`ki`.`met_issue_id` = `iitm`.`issue_met_parent_id`))) left join `smith_company_op_balance` `iss` on((`iss`.`id_smith_company_op_balance` = `iitm`.`id_smith_company_op_balance`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `iss`.`id_karigar`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `iitm`.`issue_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `iitm`.`issu_met_pro_id`))) left join `ret_design_master` `pd` on((`pd`.`design_no` = `iitm`.`issu_met_id_design`))) left join `ret_uom` `uom` on((`uom`.`uom_id` = `iitm`.`issue_uom_id`))) where ((`iitm`.`id_smith_company_op_balance` is not null) and (`ki`.`bill_status` = 1)) group by `iitm`.`issue_cat_id`,`iitm`.`issu_met_pro_id`,`iitm`.`issue_met_id` order by `unixtransdate` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ret_view_smith_metal_amt_ledger`
--

/*!50001 DROP VIEW IF EXISTS `ret_view_smith_metal_amt_ledger`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`retaillmx_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ret_view_smith_metal_amt_ledger` AS select `ms`.`met_issue_ref_id` AS `ref_no`,`ms`.`met_issue_karid` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`ms`.`met_issue_date` AS `transdate`,unix_timestamp(`ms`.`met_issue_date`) AS `unixtransdate`,0 AS `purchase_wt`,sum(`msdet`.`issue_metal_pur_wt`) AS `issue_wt`,'Material Issue' AS `accType`,0 AS `met_credit`,sum(`msdet`.`issue_metal_pur_wt`) AS `met_debit`,0 AS `amt_credit`,0 AS `amt_debit` from ((((`ret_karigar_metal_issue` `ms` left join `ret_karigar_metal_issue_details` `msdet` on((`msdet`.`issue_met_parent_id` = `ms`.`met_issue_id`))) left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `ms`.`met_issue_karid`))) left join `ret_category` `retcat` on((`retcat`.`id_ret_category` = `msdet`.`issue_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `retcat`.`id_metal`))) where (`msdet`.`issue_metal_pur_wt` > 0) group by `ms`.`met_issue_ref_id`,`msdet`.`issue_cat_id` union select `grn`.`grn_ref_no` AS `ref_no`,`grn`.`grn_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`grn`.`grn_date` AS `transdate`,unix_timestamp(`grn`.`grn_date`) AS `unixtransdate`,sum(`grni`.`grn_net_wt`) AS `purchase_wt`,0 AS `issue_wt`,'Material Receipt' AS `accType`,sum(`grni`.`grn_net_wt`) AS `met_credit`,0 AS `met_debit`,0 AS `amt_credit`,0 AS `amt_debit` from ((((`ret_grn_entry` `grn` left join `ret_grn_items` `grni` on((`grni`.`grn_item_grn_id` = `grn`.`grn_id`))) left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `grn`.`grn_karigar_id`))) left join `ret_category` `retcat` on((`retcat`.`id_ret_category` = `grni`.`grn_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `retcat`.`id_metal`))) where ((`grn`.`grn_type` = 2) and (`grni`.`grn_net_wt` > 0)) group by `grni`.`grn_item_grn_id`,`grni`.`grn_item_cat_id` union select `grn`.`grn_ref_no` AS `ref_no`,`grn`.`grn_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`grn`.`grn_date` AS `transdate`,unix_timestamp(`grn`.`grn_date`) AS `unixtransdate`,sum(`grni`.`grn_wastage`) AS `purchase_wt`,0 AS `issue_wt`,'Material Receipt' AS `accType`,sum(`grni`.`grn_wastage`) AS `met_credit`,0 AS `met_debit`,0 AS `amt_credit`,0 AS `amt_debit` from ((((`ret_grn_entry` `grn` left join `ret_grn_items` `grni` on((`grni`.`grn_item_grn_id` = `grn`.`grn_id`))) left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `grn`.`grn_karigar_id`))) left join `ret_category` `retcat` on((`retcat`.`id_ret_category` = `grni`.`grn_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `retcat`.`id_metal`))) where ((`grn`.`grn_type` = 2) and (`grni`.`grn_wastage` > 0)) group by `grni`.`grn_item_grn_id`,`grni`.`grn_item_cat_id` union select `po`.`grn_ref_no` AS `ref_no`,`po`.`grn_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`po`.`grn_date` AS `transdate`,unix_timestamp(`po`.`grn_date`) AS `unixtransdate`,0 AS `purchase_wt`,0 AS `issue_wt`,'GRN Entry' AS `accType`,0 AS `met_credit`,0 AS `met_debit`,`po`.`grn_purchase_amt` AS `amt_credit`,0 AS `amt_debit` from (`ret_grn_entry` `po` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `po`.`grn_karigar_id`))) where ((`po`.`grn_type` <> 2) and (`po`.`grn_bill_status` = 1) and (`po`.`grn_purchase_amt` > 0)) union select `po`.`po_ref_no` AS `ref_no`,`po`.`po_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`po`.`po_date` AS `transdate`,unix_timestamp(`po`.`po_date`) AS `unixtransdate`,0 AS `purchase_wt`,0 AS `issue_wt`,'Supplier Bill Entry' AS `accType`,`po`.`tot_purchase_wt` AS `met_credit`,0 AS `met_debit`,0 AS `amt_credit`,0 AS `amt_debit` from (`ret_purchase_order` `po` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `po`.`po_karigar_id`))) where ((`po`.`is_suspense_stock` = 1) and (`po`.`isratefixed` = 0) and (`po`.`tot_purchase_amt` > 0)) union select `po`.`po_ref_no` AS `ref_no`,`po`.`po_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`po`.`po_date` AS `transdate`,unix_timestamp(`po`.`po_date`) AS `unixtransdate`,0 AS `purchase_wt`,0 AS `issue_wt`,'Supplier Bill Entry' AS `accType`,0 AS `met_credit`,0 AS `met_debit`,`po`.`tot_purchase_amt` AS `amt_credit`,0 AS `amt_debit` from (`ret_purchase_order` `po` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `po`.`po_karigar_id`))) where ((`po`.`is_suspense_stock` = 1) and (`po`.`isratefixed` = 0) and (`po`.`tot_purchase_amt` > 0)) union select `pay`.`pay_refno` AS `ref_no`,`pay`.`pay_sup_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`pay`.`pay_create_on` AS `transdate`,unix_timestamp(`pay`.`pay_create_on`) AS `unixtransdate`,0 AS `purchase_wt`,0 AS `issue_wt`,'PO Payment' AS `accType`,0 AS `met_credit`,0 AS `met_debit`,0 AS `amt_credit`,`pay`.`pay_amt` AS `amt_debit` from (`ret_po_payment` `pay` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `pay`.`pay_sup_id`))) where (`pay`.`pay_status` = 1) union select `bill`.`bill_no` AS `ref_no`,`bill`.`bill_cus_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`bill`.`bill_date` AS `transdate`,unix_timestamp(`bill`.`bill_date`) AS `unixtransdate`,0 AS `purchase_wt`,0 AS `issue_wt`,'Billing Payment' AS `accType`,0 AS `met_credit`,0 AS `met_debit`,0 AS `amt_credit`,`bill`.`tot_bill_amount` AS `amt_debit` from (`ret_billing` `bill` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `bill`.`bill_cus_id`))) where ((`bill`.`bill_status` = 1) and (`bill`.`billing_for` = 3) and (`bill`.`tot_bill_amount` > 0)) group by `bill`.`bill_id` union select `ret`.`pur_ret_ref_no` AS `ref_no`,`ret`.`pur_ret_supplier_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,ifnull(`ret`.`bill_date`,'') AS `transdate`,ifnull(unix_timestamp(`ret`.`bill_date`),'') AS `unixtransdate`,0 AS `purchase_wt`,0 AS `issue_wt`,'Purchase Return' AS `accType`,0 AS `met_credit`,0 AS `met_debit`,0 AS `amt_credit`,`ret`.`return_total_cost` AS `amt_debit` from (`ret_purchase_return` `ret` left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) where ((`ret`.`bill_status` = 1) and (`ret`.`return_total_cost` > 0)) order by `unixtransdate` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ret_view_smith_metal_ledger`
--

/*!50001 DROP VIEW IF EXISTS `ret_view_smith_metal_ledger`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`retaillmx_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ret_view_smith_metal_ledger` AS select `ms`.`met_issue_ref_id` AS `ref_no`,`ms`.`met_issue_karid` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`ms`.`met_issue_date` AS `transdate`,unix_timestamp(`ms`.`met_issue_date`) AS `unixtransdate`,0 AS `purchase_wt`,sum(`msdet`.`issue_metal_wt`) AS `issue_wt`,`retcat`.`name` AS `catname`,`msdet`.`issue_cat_id` AS `cat_id`,`met`.`metal` AS `metal`,`retcat`.`id_metal` AS `metid`,'Material Issue' AS `accType`,0 AS `credit`,sum(`msdet`.`issue_metal_wt`) AS `debit` from ((((`ret_karigar_metal_issue` `ms` left join `ret_karigar_metal_issue_details` `msdet` on((`msdet`.`issue_met_parent_id` = `ms`.`met_issue_id`))) left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `ms`.`met_issue_karid`))) left join `ret_category` `retcat` on((`retcat`.`id_ret_category` = `msdet`.`issue_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `retcat`.`id_metal`))) where ((`ms`.`bill_status` = 1) and (`msdet`.`issue_metal_wt` > 0)) group by `ms`.`met_issue_ref_id`,`msdet`.`issue_cat_id` union select `grn`.`grn_ref_no` AS `ref_no`,`grn`.`grn_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`grn`.`grn_date` AS `transdate`,unix_timestamp(`grn`.`grn_date`) AS `unixtransdate`,sum(`grni`.`grn_net_wt`) AS `purchase_wt`,0 AS `issue_wt`,`retcat`.`name` AS `catname`,`grni`.`grn_item_cat_id` AS `cat_id`,`met`.`metal` AS `metal`,`retcat`.`id_metal` AS `metid`,'Material Receipt' AS `accType`,sum(`grni`.`grn_net_wt`) AS `credit`,0 AS `debit` from ((((`ret_grn_entry` `grn` left join `ret_grn_items` `grni` on((`grni`.`grn_item_grn_id` = `grn`.`grn_id`))) left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `grn`.`grn_karigar_id`))) left join `ret_category` `retcat` on((`retcat`.`id_ret_category` = `grni`.`grn_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `retcat`.`id_metal`))) where ((`grn`.`grn_type` = 2) and (`grn`.`grn_bill_status` = 1) and (`grni`.`grn_net_wt` > 0)) group by `grni`.`grn_item_grn_id`,`grni`.`grn_item_cat_id` union select `grn`.`grn_ref_no` AS `ref_no`,`grn`.`grn_karigar_id` AS `sup_id`,`retkar`.`firstname` AS `sup_name`,`grn`.`grn_date` AS `transdate`,unix_timestamp(`grn`.`grn_date`) AS `unixtransdate`,sum(`grni`.`grn_wastage`) AS `purchase_wt`,0 AS `issue_wt`,'Wastage' AS `catname`,`grni`.`grn_item_cat_id` AS `cat_id`,`met`.`metal` AS `metal`,`retcat`.`id_metal` AS `metid`,'Material Receipt' AS `accType`,sum(`grni`.`grn_wastage`) AS `credit`,0 AS `debit` from ((((`ret_grn_entry` `grn` left join `ret_grn_items` `grni` on((`grni`.`grn_item_grn_id` = `grn`.`grn_id`))) left join `ret_karigar` `retkar` on((`retkar`.`id_karigar` = `grn`.`grn_karigar_id`))) left join `ret_category` `retcat` on((`retcat`.`id_ret_category` = `grni`.`grn_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `retcat`.`id_metal`))) where ((`grn`.`grn_type` = 2) and (`grn`.`grn_bill_status` = 1) and (`grni`.`grn_wastage` > 0)) group by `grni`.`grn_item_grn_id`,`grni`.`grn_item_cat_id` order by `unixtransdate` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ret_view_supplier_approval_ledger`
--

/*!50001 DROP VIEW IF EXISTS `ret_view_supplier_approval_ledger`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`retaillmx_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ret_view_supplier_approval_ledger` AS select `pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,'/purchase/job_receipt/' AS `link`,`po`.`po_id` AS `print_id`,1 AS `trans_type`,`pitm`.`po_order_no` AS `trans_id`,`pitm`.`gross_wt` AS `gross_wt`,`pitm`.`net_wt` AS `net_wt`,`pitm`.`no_of_pcs` AS `no_of_pcs`,`pitm`.`purchase_touch` AS `purchase_touch`,`pitm`.`item_pure_wt` AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,1 AS `trans_rec_type`,sum(`pitm`.`item_cost`) AS `trans_amount`,`pitm`.`po_item_cat_id` AS `catid`,`pitm`.`po_item_pro_id` AS `proid`,1 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,concat(`pitm`.`fix_rate_per_grm`,if((`pitm`.`is_rate_fixed` = 1),'','(Un Fixed)')) AS `rate`,ifnull(`pitm`.`remark`,'') AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate` from ((((((`ret_purchase_order_items` `pitm` left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pitm`.`po_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pitm`.`po_item_pro_id`))) where ((`grn`.`grn_type` = 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1)) group by `pitm`.`po_item_id` union all select 'PAYMENT' AS `category`,'' AS `product`,`rf`.`date_add` AS `trans_date`,`rf`.`id_supplier_rate_cut` AS `referenceno`,'/supplier_rate_cut/job_receipt/' AS `link`,`rf`.`id_supplier_rate_cut` AS `print_id`,2 AS `trans_type`,`rf`.`id_supplier_rate_cut` AS `trans_id`,`rf`.`weight` AS `gross_wt`,`rf`.`weight` AS `net_wt`,'' AS `no_of_pcs`,'' AS `purchase_touch`,`rf`.`weight` AS `purewt`,`rf`.`id_karigar` AS `customer_id`,2 AS `trans_rec_type`,`rf`.`amount` AS `trans_amount`,'' AS `catid`,'' AS `proid`,7 AS `trans_screen_id`,`rf`.`id_metal` AS `id_metal`,`rf`.`id_metal` AS `metal`,`rf`.`rate_per_gram` AS `rate`,ifnull(`rf`.`narration`,'') AS `narration`,unix_timestamp(`rf`.`date_add`) AS `unixtransdate` from ((`ret_supplier_rate_cut` `rf` left join `ret_purchase_order_items` `order` on((`order`.`po_item_po_id` = `rf`.`po_id`))) left join `ret_category` `cate` on((`cate`.`id_ret_category` = `order`.`po_item_cat_id`))) where ((`rf`.`rate_cut_type` = 1) and (`rf`.`status` = 1)) group by `rf`.`id_supplier_rate_cut` union all select if((`rf`.`weight` = 0),'Bill Conv(Amount)','Bill Conv(A to P)') AS `category`,'' AS `product`,`rf`.`date_add` AS `trans_date`,`rf`.`id_supplier_rate_cut` AS `referenceno`,'/supplier_rate_cut/job_receipt/' AS `link`,`rf`.`id_supplier_rate_cut` AS `print_id`,2 AS `trans_type`,`rf`.`id_supplier_rate_cut` AS `trans_id`,`rf`.`weight` AS `gross_wt`,`rf`.`weight` AS `net_wt`,'' AS `no_of_pcs`,if((`rf`.`weight` = 0),'','100') AS `purchase_touch`,`rf`.`weight` AS `purewt`,`rf`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,if((`rf`.`charges_amount` > 0),`rf`.`charges_amount`,'') AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,`rf`.`id_metal` AS `id_metal`,`rf`.`id_metal` AS `metal`,`rf`.`rate_per_gram` AS `rate`,ifnull(`rf`.`narration`,'') AS `narration`,unix_timestamp(`rf`.`date_add`) AS `unixtransdate` from `ret_supplier_rate_cut` `rf` where ((`rf`.`rate_cut_type` = 2) and (`rf`.`status` = 1)) group by `rf`.`id_supplier_rate_cut` union all select `pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,'/return_receipt_acknowladgement/' AS `link`,`ret`.`pur_return_id` AS `print_id`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,`pret`.`pur_ret_pur_wt` AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,1 AS `trans_rec_type`,sum(ifnull(`pret`.`pur_ret_debit_note_amt`,0)) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,`pret`.`pur_ret_rate` AS `rate`,ifnull(`ret`.`pur_ret_remark`,'') AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate` from ((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 3) and (`ret`.`purchase_type` = 0) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union all select `pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,'/return_receipt_acknowladgement/' AS `link`,`ret`.`pur_return_id` AS `print_id`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,`pret`.`pur_ret_pur_wt` AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,1 AS `trans_rec_type`,sum(ifnull(`pret`.`pur_ret_debit_note_amt`,0)) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,`pret`.`pur_ret_rate` AS `rate`,ifnull(`ret`.`pur_ret_remark`,'') AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate` from ((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 3) and (`ret`.`purchase_type` = 1) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union all select `pr`.`product_name` AS `category`,`pr`.`product_name` AS `product`,`iss`.`met_issue_date` AS `trans_date`,`iss`.`met_issue_ref_id` AS `referenceno`,'/karigarmetalissue_acknowladgement/' AS `link`,`iitm`.`issue_met_parent_id` AS `print_id`,2 AS `trans_type`,`iitm`.`issue_met_parent_id` AS `trans_id`,if((`cat`.`cat_type` = 2),0,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `gross_wt`,if((`cat`.`cat_type` = 2),0,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `net_wt`,ifnull(`iitm`.`issue_pcs`,1) AS `no_of_pcs`,'100' AS `purchase_touch`,if((`pr`.`stone_type` = 0),`iitm`.`issue_metal_pur_wt`,if((ifnull(`uom`.`divided_by_value`,0) = 0),`iitm`.`issue_metal_wt`,round((`iitm`.`issue_metal_wt` / `uom`.`divided_by_value`),3))) AS `purewt`,`iss`.`met_issue_karid` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,`iitm`.`issue_cat_id` AS `catid`,`iitm`.`issu_met_pro_id` AS `proid`,2 AS `trans_screen_id`,`met`.`id_metal` AS `id_metal`,`met`.`metal` AS `metal`,'' AS `rate`,ifnull(`iss`.`remark`,'') AS `narration`,unix_timestamp(`iss`.`met_issue_date`) AS `unixtransdate` from ((((((`ret_karigar_metal_issue_details` `iitm` left join `ret_karigar_metal_issue` `iss` on((`iss`.`met_issue_id` = `iitm`.`issue_met_parent_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `iss`.`met_issue_karid`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `iitm`.`issue_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `iitm`.`issu_met_pro_id`))) left join `ret_uom` `uom` on((`uom`.`uom_id` = `iitm`.`issue_uom_id`))) where ((`iss`.`metalissue_type` = 2) and (`iss`.`bill_status` = 1)) group by `iitm`.`issue_met_id`,`iitm`.`issue_cat_id`,`iitm`.`issu_met_pro_id`,`iitm`.`issue_met_parent_id` union all select 'OPENING' AS `category`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,'' AS `link`,'' AS `print_id`,`pay`.`amount_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,sum(`pay`.`amount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,'' AS `id_metal`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`remarks`,'') AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate` from `smith_company_op_balance` `pay` where ((`pay`.`smith_type` = 3) and (`pay`.`stock_type` = 2) and (`pay`.`amount` > 0)) group by `pay`.`id_smith_company_op_balance` union all select 'OPENING' AS `category`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,'' AS `link`,'' AS `print_id`,`pay`.`weight_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,ifnull(`pay`.`weight`,0) AS `gross_wt`,ifnull(`pay`.`weight`,0) AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,ifnull(`pay`.`weight`,0) AS `purewt`,`pay`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,`pay`.`id_metal` AS `id_metal`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`remarks`,'') AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate` from `smith_company_op_balance` `pay` where ((`pay`.`smith_type` = 3) and (`pay`.`stock_type` = 2) and (`pay`.`weight` > 0)) group by `pay`.`id_smith_company_op_balance` union all select if((`pay`.`transtype` = 1),'Credit Note','Debit Note') AS `category`,'' AS `product`,`pay`.`transdate` AS `trans_date`,`pay`.`transbillno` AS `referenceno`,'/credit_debit_acknolodgement/' AS `link`,`pay`.`crdrid` AS `print_id`,`pay`.`transtype` AS `trans_type`,`pay`.`transbillno` AS `trans_id`,`pay`.`weight` AS `gross_wt`,`pay`.`weight` AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,`pay`.`weight` AS `purewt`,`pay`.`supid` AS `customer_id`,1 AS `trans_rec_type`,sum(`pay`.`transamount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,`cate`.`id_metal` AS `id_metal`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`naration`,'') AS `narration`,unix_timestamp(`pay`.`transdate`) AS `unixtransdate` from ((`ret_crdr_note` `pay` left join `ret_purchase_order_items` `order` on((`order`.`po_item_po_id` = `pay`.`po_id`))) left join `ret_category` `cate` on((`cate`.`id_ret_category` = `order`.`po_item_cat_id`))) where ((`pay`.`accountto` = 3) and (`pay`.`crdr_status` = 1) and (`pay`.`weight` > 0)) group by `pay`.`crdrid` order by `unixtransdate` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ret_view_supplier_ledger`
--

/*!50001 DROP VIEW IF EXISTS `ret_view_supplier_ledger`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`retaillmx_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ret_view_supplier_ledger` AS select ifnull(`pr`.`product_name`,'Product Unavailable') AS `category`,`po`.`po_id` AS `po_id`,`met`.`id_metal` AS `id_metal`,ifnull(`pr`.`product_name`,'') AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,1 AS `trans_type`,`pitm`.`po_order_no` AS `trans_id`,`pitm`.`gross_wt` AS `gross_wt`,`pitm`.`net_wt` AS `net_wt`,`pitm`.`no_of_pcs` AS `no_of_pcs`,`pitm`.`purchase_touch` AS `purchase_touch`,`pitm`.`item_pure_wt` AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`pitm`.`item_cost`) AS `trans_amount`,`pitm`.`po_item_cat_id` AS `catid`,`pitm`.`po_item_pro_id` AS `proid`,1 AS `trans_screen_id`,`met`.`metal` AS `metal`,concat(`pitm`.`fix_rate_per_grm`,if((`pitm`.`is_rate_fixed` = 1),'','(Un Fixed)')) AS `rate`,ifnull(`pitm`.`remark`,'') AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate` from ((((((`ret_purchase_order_items` `pitm` left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pitm`.`po_item_cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pitm`.`po_item_pro_id`))) where ((`grn`.`grn_type` <> 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1) and (`po`.`is_suspense_stock` = 0)) group by `pitm`.`po_item_id` union all select 'PAYMENT' AS `category`,ifnull(`bill`.`po_id`,'') AS `po_id`,`cat`.`id_metal` AS `id_metal`,'' AS `product`,`pay`.`pay_create_on` AS `trans_date`,`pay`.`pay_refno` AS `referenceno`,2 AS `trans_type`,`pay`.`pay_id` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`pay_sup_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`pd`.`payment_amount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,'' AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`pay`.`pay_create_on`) AS `unixtransdate` from ((((`ret_po_payment` `pay` left join `ret_po_payment_detail` `pd` on((`pd`.`pay_id` = `pay`.`pay_id`))) left join `ret_po_bill_payment_details` `bill` on((`bill`.`pay_id` = `pay`.`pay_id`))) left join (select `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,`ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id` from `ret_purchase_order_items` group by `ret_purchase_order_items`.`po_item_po_id`) `po` on((`bill`.`po_id` = `po`.`po_item_po_id`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `po`.`po_item_cat_id`))) where ((`pay`.`pay_status` = 1) and (`pay`.`bill_type` = 1)) group by `pay`.`pay_id` union all select concat('RATE FIXING',if((`pitm`.`rate` > `rf`.`rate_fix_rate`),'(Dr)','(Cr)')) AS `category`,ifnull(`po`.`po_id`,'') AS `po_id`,`pitm`.`id_metal` AS `id_metal`,'' AS `product`,`rf`.`rate_fix_created_on` AS `trans_date`,`rf`.`rate_fix_id` AS `referenceno`,if((`pitm`.`rate` > `rf`.`rate_fix_rate`),2,1) AS `trans_type`,`rf`.`rate_fix_id` AS `trans_id`,'' AS `gross_wt`,'' AS `net_wt`,'' AS `no_of_pcs`,'' AS `purchase_touch`,'' AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,2 AS `trans_rec_type`,round(abs((((`pitm`.`rate` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt`) * 1.03)),2) AS `trans_amount`,'' AS `catid`,'' AS `proid`,7 AS `trans_screen_id`,'' AS `metal`,`rf`.`rate_fix_rate` AS `rate`,'' AS `narration`,unix_timestamp(`rf`.`rate_fix_created_on`) AS `unixtransdate` from (((`ret_po_rate_fix` `rf` left join `ret_purchase_order` `po` on((`po`.`po_id` = `rf`.`rate_fix_po_item_id`))) left join (select `pitm`.`po_item_po_id` AS `poid`,`pitm`.`fix_rate_per_grm` AS `rate`,`cate`.`id_metal` AS `id_metal` from (`ret_purchase_order_items` `pitm` left join `ret_category` `cate` on((`cate`.`id_ret_category` = `pitm`.`po_item_cat_id`))) group by `pitm`.`po_item_po_id`) `pitm` on((`pitm`.`poid` = `po`.`po_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) where ((`po`.`isratefixed` = 0) and (`po`.`is_suspense_stock` = 0) and (`rf`.`bill_status` = 1) and (`rf`.`is_approved` = 1)) group by `rf`.`rate_fix_id` union all select concat('RATE FIXING',if((`rc`.`rate_per_gram` > `rf`.`rate_fix_rate`),'(Dr)','(Cr)')) AS `category`,ifnull(`rc`.`po_id`,'') AS `po_id`,`rc`.`id_metal` AS `id_metal`,'' AS `product`,`rf`.`rate_fix_created_on` AS `trans_date`,`rf`.`rate_fix_id` AS `referenceno`,if((`rc`.`rate_per_gram` > `rf`.`rate_fix_rate`),2,1) AS `trans_type`,`rf`.`rate_fix_id` AS `trans_id`,`rc`.`weight` AS `gross_wt`,`rc`.`weight` AS `net_wt`,'' AS `no_of_pcs`,'' AS `purchase_touch`,`rc`.`weight` AS `purewt`,`rc`.`id_karigar` AS `customer_id`,2 AS `trans_rec_type`,round(abs((((`rc`.`rate_per_gram` - `rf`.`rate_fix_rate`) * `rf`.`rate_fix_wt`) * 1.03)),2) AS `trans_amount`,'' AS `catid`,'' AS `proid`,7 AS `trans_screen_id`,'' AS `metal`,`rf`.`rate_fix_rate` AS `rate`,'' AS `narration`,unix_timestamp(`rf`.`rate_fix_created_on`) AS `unixtransdate` from (`ret_po_rate_fix` `rf` left join `ret_supplier_rate_cut` `rc` on((`rc`.`id_supplier_rate_cut` = `rf`.`id_approval_ratecut`))) where ((`rf`.`rate_fix_type` = 2) and (`rf`.`bill_status` = 1) and (`rf`.`is_approved` = 1)) group by `rf`.`rate_fix_id` union all select ifnull(`pr`.`product_name`,'Product Unavailable') AS `category`,ifnull(`po`.`po_id`,'') AS `po_id`,`met`.`id_metal` AS `id_metal`,ifnull(`pr`.`product_name`,'') AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,`pret`.`pur_ret_pur_wt` AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,2 AS `trans_rec_type`,sum(ifnull(`pret`.`pur_ret_debit_note_amt`,0)) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`metal` AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate` from ((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 1) and (`ret`.`purchase_type` = 0) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union all select ifnull(`pr`.`product_name`,'Product Unavailable') AS `category`,ifnull(`po`.`po_id`,'') AS `po_id`,`met`.`id_metal` AS `id_metal`,ifnull(`pr`.`product_name`,'') AS `product`,`ret`.`bill_date` AS `trans_date`,`ret`.`pur_ret_ref_no` AS `referenceno`,2 AS `trans_type`,`ret`.`pur_return_id` AS `trans_id`,sum(`pret`.`pur_ret_gwt`) AS `gross_wt`,sum(`pret`.`pur_ret_nwt`) AS `net_wt`,sum(`pret`.`pur_ret_pcs`) AS `no_of_pcs`,`pret`.`pur_ret_purchase_touch` AS `purchase_touch`,`pret`.`pur_ret_pur_wt` AS `purewt`,`ret`.`pur_ret_supplier_id` AS `customer_id`,2 AS `trans_rec_type`,sum(ifnull(`pret`.`pur_ret_debit_note_amt`,0)) AS `trans_amount`,`cat`.`id_ret_category` AS `catid`,`pret`.`id_product` AS `proid`,5 AS `trans_screen_id`,`met`.`metal` AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`ret`.`bill_date`) AS `unixtransdate` from ((((((((`ret_purchase_return_items` `pret` left join `ret_purchase_return` `ret` on((`ret`.`pur_return_id` = `pret`.`pur_ret_id`))) left join `ret_purchase_order_items` `pitm` on((`pitm`.`po_item_id` = `pret`.`pur_ret_po_item_id`))) left join `ret_purchase_order` `po` on((`po`.`po_id` = `pitm`.`po_item_po_id`))) left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `ret`.`pur_ret_supplier_id`))) left join `ret_product_master` `pr` on((`pr`.`pro_id` = `pret`.`id_product`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `pr`.`cat_id`))) left join `metal` `met` on((`met`.`id_metal` = `cat`.`id_metal`))) where ((`ret`.`pur_ret_convert_to` = 1) and (`ret`.`purchase_type` = 1) and (`ret`.`bill_status` = 1)) group by `pret`.`pur_ret_itm_id` union all select ifnull(`pr`.`product_name`,'RATE FIXING') AS `category`,ifnull(`rf`.`po_id`,'') AS `po_id`,`rf`.`id_metal` AS `id_metal`,'' AS `product`,`rf`.`date_add` AS `trans_date`,`rf`.`id_supplier_rate_cut` AS `referenceno`,1 AS `trans_type`,`rf`.`id_supplier_rate_cut` AS `trans_id`,`rf`.`weight` AS `gross_wt`,`rf`.`weight` AS `net_wt`,'' AS `no_of_pcs`,'100' AS `purchase_touch`,`rf`.`weight` AS `purewt`,`rf`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,`rf`.`amount` AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,`rf`.`id_metal` AS `metal`,if((`rf`.`conversion_type` = 2),concat('(Unfix)',`rf`.`rate_per_gram`),`rf`.`rate_per_gram`) AS `rate`,ifnull(`rf`.`narration`,'') AS `narration`,unix_timestamp(`rf`.`date_add`) AS `unixtransdate` from (`ret_supplier_rate_cut` `rf` left join `ret_product_master` `pr` on((`pr`.`pro_id` = `rf`.`id_product`))) where ((`rf`.`rate_cut_type` = 2) and (`rf`.`status` = 1)) group by `rf`.`id_supplier_rate_cut` union all select 'OPENING' AS `category`,'' AS `po_id`,`pay`.`id_metal` AS `id_metal`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,`pay`.`amount_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,`pay`.`weight` AS `gross_wt`,`pay`.`net_wt` AS `net_wt`,`pay`.`pieces` AS `no_of_pcs`,'' AS `purchase_touch`,`pay`.`pure_wt` AS `purewt`,`pay`.`id_karigar` AS `customer_id`,2 AS `trans_rec_type`,sum(`pay`.`amount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`remarks`,'') AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate` from `smith_company_op_balance` `pay` where (((`pay`.`smith_type` = 1) or (`pay`.`smith_type` = 4)) and (`pay`.`stock_type` = 2) and (`pay`.`amount` > 0)) group by `pay`.`id_smith_company_op_balance` union all select 'OPENING' AS `category`,'' AS `po_id`,`pay`.`id_metal` AS `id_metal`,'' AS `product`,`pay`.`createdon` AS `trans_date`,`pay`.`id_smith_company_op_balance` AS `referenceno`,`pay`.`amount_type` AS `trans_type`,`pay`.`id_smith_company_op_balance` AS `trans_id`,ifnull(`pay`.`weight`,0) AS `gross_wt`,ifnull(`pay`.`net_wt`,0) AS `net_wt`,`pay`.`pieces` AS `no_of_pcs`,'' AS `purchase_touch`,`pay`.`pure_wt` AS `purewt`,`pay`.`id_karigar` AS `customer_id`,1 AS `trans_rec_type`,0 AS `trans_amount`,'' AS `catid`,'' AS `proid`,3 AS `trans_screen_id`,'' AS `metal`,'' AS `rate`,ifnull(`pay`.`remarks`,'') AS `narration`,unix_timestamp(`pay`.`createdon`) AS `unixtransdate` from `smith_company_op_balance` `pay` where (((`pay`.`smith_type` = 1) or (`pay`.`smith_type` = 4)) and (`pay`.`stock_type` = 2) and (`pay`.`weight` > 0)) group by `pay`.`id_smith_company_op_balance` union all select if((`pay`.`transtype` = 1),'Credit Note','Debit Note') AS `category`,`pay`.`po_id` AS `po_id`,`cat`.`id_metal` AS `id_metal`,'' AS `product`,`pay`.`transdate` AS `trans_date`,`pay`.`transbillno` AS `referenceno`,`pay`.`transtype` AS `trans_type`,`pay`.`transbillno` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`pay`.`supid` AS `customer_id`,1 AS `trans_rec_type`,sum(`pay`.`transamount`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,'' AS `metal`,'' AS `rate`,`pay`.`naration` AS `narration`,unix_timestamp(`pay`.`transdate`) AS `unixtransdate` from ((`ret_crdr_note` `pay` left join (select `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,`ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id` from `ret_purchase_order_items` group by `ret_purchase_order_items`.`po_item_po_id`) `po` on((`pay`.`po_id` = `po`.`po_item_po_id`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `po`.`po_item_cat_id`))) where ((`pay`.`accountto` = 1) and (`pay`.`transamount` > 0) and (`pay`.`crdr_status` = 1)) group by `pay`.`crdrid` union all select 'TDS' AS `category`,`po`.`po_id` AS `po_id`,`cat`.`id_metal` AS `id_metal`,'' AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,2 AS `trans_type`,`po`.`po_id` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`po`.`tds_tax_value`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,'' AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate` from ((((`ret_purchase_order` `po` left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) left join (select `ret_purchase_order_items`.`po_item_po_id` AS `po_item_po_id`,`ret_purchase_order_items`.`po_item_cat_id` AS `po_item_cat_id` from `ret_purchase_order_items` group by `ret_purchase_order_items`.`po_item_po_id`) `item` on((`po`.`po_id` = `item`.`po_item_po_id`))) left join `ret_category` `cat` on((`cat`.`id_ret_category` = `item`.`po_item_cat_id`))) where ((`grn`.`grn_type` <> 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1) and (`po`.`is_suspense_stock` = 0) and (`po`.`tds_tax_value` > 0)) group by `po`.`po_id` union all select 'TCS' AS `category`,`po`.`po_id` AS `po_id`,'' AS `id_metal`,'' AS `product`,`po`.`po_date` AS `trans_date`,`po`.`po_ref_no` AS `referenceno`,2 AS `trans_type`,`po`.`po_id` AS `trans_id`,0 AS `gross_wt`,0 AS `net_wt`,0 AS `no_of_pcs`,'' AS `purchase_touch`,0 AS `purewt`,`po`.`po_karigar_id` AS `customer_id`,2 AS `trans_rec_type`,sum(`po`.`tcs_tax_value`) AS `trans_amount`,'' AS `catid`,'' AS `proid`,1 AS `trans_screen_id`,'' AS `metal`,'' AS `rate`,'' AS `narration`,unix_timestamp(`po`.`po_date`) AS `unixtransdate` from ((`ret_purchase_order` `po` left join `ret_grn_entry` `grn` on((`grn`.`grn_id` = `po`.`po_grn_id`))) left join `ret_karigar` `kr` on((`kr`.`id_karigar` = `po`.`po_karigar_id`))) where ((`grn`.`grn_type` <> 2) and (`po`.`is_approved` = 1) and (`po`.`bill_status` = 1) and (`po`.`is_suspense_stock` = 0) and (`po`.`tcs_tax_value` > 0)) group by `po`.`po_id` order by `unixtransdate` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-17  5:50:18
