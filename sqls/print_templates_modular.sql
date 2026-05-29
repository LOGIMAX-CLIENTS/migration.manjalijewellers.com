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
-- Table structure for table `print_templates_modular`
--

CREATE TABLE `print_templates_modular` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `module_type` enum('sales','purchase','estimate','order') DEFAULT 'sales',
  `config_json` longtext NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `print_templates_modular`
--

INSERT INTO `print_templates_modular` (`id`, `template_name`, `module_type`, `config_json`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Standard Billing Template v2', 'sales', '{\"globalSettings\":{\"pageSize\":\"A4\",\"orientation\":\"portrait\",\"margins\":{\"top\":10,\"right\":10,\"bottom\":10,\"left\":10},\"fontFamily\":\"Inter\",\"fontSize\":10,\"blockSpacing\":10},\"blocks\":[{\"id\":\"comp_header_1\",\"type\":\"company_header\",\"visible\":true,\"style\":{\"border\":{\"bottom\":{\"width\":2,\"style\":\"solid\",\"color\":\"#2563eb\"}},\"padding\":[10,0,10,0]}},{\"id\":\"inv_info_1\",\"type\":\"invoice_info\",\"visible\":true,\"style\":{\"margin\":[10,0,10,0]}},{\"id\":\"cust_info_1\",\"type\":\"customer_info\",\"visible\":true,\"style\":{\"padding\":[10,10,10,10],\"backgroundColor\":\"#f9fafb\"}},{\"id\":\"items_1\",\"type\":\"item_table\",\"visible\":true,\"columns\":[{\"key\":\"sno\",\"label\":\"S.No\",\"width\":\"50px\",\"align\":\"center\"},{\"key\":\"description\",\"label\":\"Product Description\",\"width\":\"auto\",\"align\":\"left\"},{\"key\":\"gross_wt\",\"label\":\"Gross Wt\",\"width\":\"100px\",\"align\":\"right\"},{\"key\":\"net_wt\",\"label\":\"Net Wt\",\"width\":\"100px\",\"align\":\"right\"},{\"key\":\"amount\",\"label\":\"Total Amount\",\"width\":\"120px\",\"align\":\"right\"}]},{\"id\":\"totals_1\",\"type\":\"billing_total\",\"visible\":true,\"fields\":[{\"key\":\"sub_total\",\"label\":\"Taxable Value\",\"visible\":true},{\"key\":\"total_tax\",\"label\":\"GST Total\",\"visible\":true},{\"key\":\"grand_total\",\"label\":\"Invoice Total\",\"visible\":true}]},{\"id\":\"footer_1\",\"type\":\"terms_signature\",\"visible\":true}]}', 1, '2026-02-16 04:19:05', '2026-02-16 04:19:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `print_templates_modular`
--
ALTER TABLE `print_templates_modular`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `print_templates_modular`
--
ALTER TABLE `print_templates_modular`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
