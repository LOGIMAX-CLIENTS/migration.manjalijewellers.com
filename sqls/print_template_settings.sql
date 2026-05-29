-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 14, 2026 at 06:51 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `retail_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `print_template_settings`
--

CREATE TABLE `print_template_settings` (
  `id_setting` int(11) NOT NULL,
  `id_template` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_label` varchar(100) DEFAULT NULL,
  `setting_type` enum('boolean','text','number','color','image') DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `print_template_settings`
--
ALTER TABLE `print_template_settings`
  ADD PRIMARY KEY (`id_setting`),
  ADD UNIQUE KEY `unique_setting` (`id_template`,`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `print_template_settings`
--
ALTER TABLE `print_template_settings`
  MODIFY `id_setting` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `print_template_settings`
--
ALTER TABLE `print_template_settings`
  ADD CONSTRAINT `print_template_settings_ibfk_1` FOREIGN KEY (`id_template`) REFERENCES `print_templates` (`id_template`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
