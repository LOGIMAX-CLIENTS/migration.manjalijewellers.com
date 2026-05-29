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
-- Table structure for table `print_template_custom_blocks`
--

CREATE TABLE `print_template_custom_blocks` (
  `id` int(11) NOT NULL,
  `block_name` varchar(255) NOT NULL,
  `block_html` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `print_template_custom_blocks`
--

INSERT INTO `print_template_custom_blocks` (`id`, `block_name`, `block_html`, `created_at`, `updated_at`) VALUES
(1, 'rudhra', '<div>Start designing your block here</div>', '2026-03-10 06:13:47', '2026-03-10 06:13:47'),
(2, 'summery details', '<div id=\"ioes\" class=\"payment-info-block\" style=\"font-size: 12px; padding: 5px;\"><div id=\"ixsi\" style=\"font-weight: bold; margin-bottom: 5px;\">Summery Details</div><div id=\"full\" style=\"display: flex; width: 100%;\"><div id=\"half1\" style=\"width: 50%;\"></div><div id=\"half2\" style=\"width: 50%;\"><table id=\"i7fp\" style=\"width: 100%; border-collapse: collapse; font-size: inherit;\"><tbody><tr><td id=\"i5vj\" style=\"padding: 2px;\">Sales Amount : </td><td id=\"ikfea\" style=\"padding: 2px; text-align: right;\">??? {{sales_total_amount}}</td></tr><tr><td id=\"icwdh\" style=\"padding: 2px;\">Purchase Amount : </td><td id=\"irfle\" style=\"padding: 2px; text-align: right;\">??? {{purchase_total_amount}}</td></tr><tr><td id=\"iupon\" style=\"padding: 2px;\">Return Amount :</td><td id=\"i6r8k\" style=\"padding: 2px; text-align: right;\">??? {{return_total_amount}}</td></tr><tr><td id=\"ib7vk\" style=\"padding: 2px;\">Round off:</td><td id=\"in49z\" style=\"padding: 2px; text-align: right;\">??? {{round_off}}</td></tr><tr id=\"ivfvo\" style=\"font-weight: bold;\"><td id=\"ivc8i\" style=\"padding: 2px;\">Net Amount:</td><td id=\"i99ni\" style=\"padding: 2px; text-align: right;\"><hr>??? {{net_amount}}<hr></td></tr></tbody></table></div></div></div><div id=\"imece\" class=\"gjs-row\" style=\"display: flex; justify-content: flex-start; align-items: stretch; flex-wrap: nowrap; padding: 10px;\"><div class=\"gjs-cell\" id=\"ispek\" style=\"min-height: 75px; flex-grow: 1; flex-basis: 100%;\"></div><div id=\"ilt5j\" class=\"gjs-cell\" style=\"min-height: 75px; flex-grow: 1; flex-basis: 100%;\"></div></div>', '2026-03-10 07:56:38', '2026-03-10 07:56:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `print_template_custom_blocks`
--
ALTER TABLE `print_template_custom_blocks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `print_template_custom_blocks`
--
ALTER TABLE `print_template_custom_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
