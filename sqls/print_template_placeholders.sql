-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 14, 2026 at 06:50 AM
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
-- Table structure for table `print_template_placeholders`
--

CREATE TABLE `print_template_placeholders` (
  `id_placeholder` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `placeholder_key` varchar(100) NOT NULL,
  `placeholder_label` varchar(150) NOT NULL,
  `placeholder_group` varchar(50) DEFAULT 'General',
  `sample_value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `print_template_placeholders`
--

INSERT INTO `print_template_placeholders` (`id_placeholder`, `category`, `placeholder_key`, `placeholder_label`, `placeholder_group`, `sample_value`, `description`, `display_order`) VALUES
(1, 'billing', 'company_name', 'Company Name', 'Company', 'ABC Jewellers', NULL, 1),
(2, 'billing', 'company_logo', 'Company Logo', 'Company', '/assets/images/logo.png', NULL, 2),
(3, 'billing', 'branch_name', 'Branch Name', 'Branch', 'Main Branch', NULL, 3),
(4, 'billing', 'branch_address', 'Branch Address', 'Branch', '123 Main Street', NULL, 4),
(5, 'billing', 'branch_phone', 'Branch Phone', 'Branch', '+91 9876543210', NULL, 5),
(6, 'billing', 'branch_gstin', 'Branch GSTIN', 'Branch', '29ABCDE1234F1ZK', NULL, 6),
(7, 'billing', 'bill_no', 'Bill Number', 'Invoice', 'INV-2024-001', NULL, 7),
(8, 'billing', 'bill_date', 'Bill Date', 'Invoice', '24-Dec-2024', NULL, 8),
(9, 'billing', 'customer_name', 'Customer Name', 'Customer', 'John Doe', NULL, 9),
(10, 'billing', 'customer_mobile', 'Customer Mobile', 'Customer', '9876543210', NULL, 10),
(11, 'billing', 'customer_address', 'Customer Address', 'Customer', '456 Customer Street', NULL, 11),
(12, 'billing', 'items_table', 'Items Table', 'Items', '[TABLE]', NULL, 12),
(13, 'billing', 'subtotal', 'Subtotal', 'Totals', '50,000.00', NULL, 13),
(14, 'billing', 'cgst', 'CGST Amount', 'Totals', '750.00', NULL, 14),
(15, 'billing', 'sgst', 'SGST Amount', 'Totals', '750.00', NULL, 15),
(16, 'billing', 'grand_total', 'Grand Total', 'Totals', '51,500.00', NULL, 16),
(17, 'billing', 'amount_in_words', 'Amount in Words', 'Totals', 'Fifty One Thousand Five Hundred Only', NULL, 17);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `print_template_placeholders`
--
ALTER TABLE `print_template_placeholders`
  ADD PRIMARY KEY (`id_placeholder`),
  ADD UNIQUE KEY `unique_placeholder` (`category`,`placeholder_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `print_template_placeholders`
--
ALTER TABLE `print_template_placeholders`
  MODIFY `id_placeholder` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
