-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 06, 2022 at 10:06 PM
-- Server version: 8.0.27
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `knowledge_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_sessions`
--

DROP TABLE IF EXISTS `access_sessions`;
CREATE TABLE IF NOT EXISTS `access_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `access_sessions`
--

INSERT INTO `access_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('2mn29jo5mqeku3491fe72u0j2lj566vj', '::1', 1665091861, 0x5f5f63695f6c6173745f726567656e65726174657c693a313636353039313836313b757365727c4f3a383a22737464436c617373223a31313a7b733a323a226964223b733a323a223130223b733a353a22656d61696c223b4e3b733a373a22636f6e74616374223b4e3b733a383a22757365726e616d65223b733a333a226d6f68223b733a343a226e616d65223b733a31383a224d696e6973747279206f66204865616c7468223b733a343a22726f6c65223b733a323a223130223b733a363a22737461747573223b733a313a2231223b733a31303a22637265617465645f6174223b733a31393a22323032312d30362d32312032313a33323a3330223b733a373a226368616e676564223b4e3b733a393a2269734368616e676564223b733a313a2231223b733a31303a2267726f75705f6e616d65223b733a363a227361646d696e223b7d),
('3t0r20rt075dprif2tmid8aqf655hte4', '::1', 1665093529, 0x5f5f63695f6c6173745f726567656e65726174657c693a313636353039333532393b757365727c4f3a383a22737464436c617373223a31313a7b733a323a226964223b733a323a223130223b733a353a22656d61696c223b4e3b733a373a22636f6e74616374223b4e3b733a383a22757365726e616d65223b733a333a226d6f68223b733a343a226e616d65223b733a31383a224d696e6973747279206f66204865616c7468223b733a343a22726f6c65223b733a323a223130223b733a363a22737461747573223b733a313a2231223b733a31303a22637265617465645f6174223b733a31393a22323032312d30362d32312032313a33323a3330223b733a373a226368616e676564223b4e3b733a393a2269734368616e676564223b733a313a2231223b733a31303a2267726f75705f6e616d65223b733a363a227361646d696e223b7d),
('7k6gkp51a2nqq37egtlsatef4j7tkr3s', '::1', 1665093894, 0x5f5f63695f6c6173745f726567656e65726174657c693a313636353039333836303b757365727c4f3a383a22737464436c617373223a31313a7b733a323a226964223b733a323a223130223b733a353a22656d61696c223b4e3b733a373a22636f6e74616374223b4e3b733a383a22757365726e616d65223b733a333a226d6f68223b733a343a226e616d65223b733a31383a224d696e6973747279206f66204865616c7468223b733a343a22726f6c65223b733a323a223130223b733a363a22737461747573223b733a313a2231223b733a31303a22637265617465645f6174223b733a31393a22323032312d30362d32312032313a33323a3330223b733a373a226368616e676564223b4e3b733a393a2269734368616e676564223b733a313a2231223b733a31303a2267726f75705f6e616d65223b733a363a227361646d696e223b7d),
('e4eanmd03eqcr480ejqkojai7a5qie2k', '::1', 1665092980, 0x5f5f63695f6c6173745f726567656e65726174657c693a313636353039323938303b757365727c4f3a383a22737464436c617373223a31313a7b733a323a226964223b733a323a223130223b733a353a22656d61696c223b4e3b733a373a22636f6e74616374223b4e3b733a383a22757365726e616d65223b733a333a226d6f68223b733a343a226e616d65223b733a31383a224d696e6973747279206f66204865616c7468223b733a343a22726f6c65223b733a323a223130223b733a363a22737461747573223b733a313a2231223b733a31303a22637265617465645f6174223b733a31393a22323032312d30362d32312032313a33323a3330223b733a373a226368616e676564223b4e3b733a393a2269734368616e676564223b733a313a2231223b733a31303a2267726f75705f6e616d65223b733a363a227361646d696e223b7d),
('jli0rsgito430eb5kil27vgstoojhrs8', '::1', 1665093860, 0x5f5f63695f6c6173745f726567656e65726174657c693a313636353039333836303b757365727c4f3a383a22737464436c617373223a31313a7b733a323a226964223b733a323a223130223b733a353a22656d61696c223b4e3b733a373a22636f6e74616374223b4e3b733a383a22757365726e616d65223b733a333a226d6f68223b733a343a226e616d65223b733a31383a224d696e6973747279206f66204865616c7468223b733a343a22726f6c65223b733a323a223130223b733a363a22737461747573223b733a313a2231223b733a31303a22637265617465645f6174223b733a31393a22323032312d30362d32312032313a33323a3330223b733a373a226368616e676564223b4e3b733a393a2269734368616e676564223b733a313a2231223b733a31303a2267726f75705f6e616d65223b733a363a227361646d696e223b7d),
('qdkc22g758v00rscidclod1psg16bul4', '::1', 1665091518, 0x5f5f63695f6c6173745f726567656e65726174657c693a313636353039313531383b757365727c4f3a383a22737464436c617373223a31313a7b733a323a226964223b733a323a223130223b733a353a22656d61696c223b4e3b733a373a22636f6e74616374223b4e3b733a383a22757365726e616d65223b733a333a226d6f68223b733a343a226e616d65223b733a31383a224d696e6973747279206f66204865616c7468223b733a343a22726f6c65223b733a323a223130223b733a363a22737461747573223b733a313a2231223b733a31303a22637265617465645f6174223b733a31393a22323032312d30362d32312032313a33323a3330223b733a373a226368616e676564223b4e3b733a393a2269734368616e676564223b733a313a2231223b733a31303a2267726f75705f6e616d65223b733a363a227361646d696e223b7d67726f75707c733a323a223130223b5f5f63695f766172737c613a313a7b733a353a2267726f7570223b733a333a226e6577223b7d),
('rdca2otcuhls65u7b4os7badbcv6pcbg', '::1', 1665092647, 0x5f5f63695f6c6173745f726567656e65726174657c693a313636353039323634373b757365727c4f3a383a22737464436c617373223a31313a7b733a323a226964223b733a323a223130223b733a353a22656d61696c223b4e3b733a373a22636f6e74616374223b4e3b733a383a22757365726e616d65223b733a333a226d6f68223b733a343a226e616d65223b733a31383a224d696e6973747279206f66204865616c7468223b733a343a22726f6c65223b733a323a223130223b733a363a22737461747573223b733a313a2231223b733a31303a22637265617465645f6174223b733a31393a22323032312d30362d32312032313a33323a3330223b733a373a226368616e676564223b4e3b733a393a2269734368616e676564223b733a313a2231223b733a31303a2267726f75705f6e616d65223b733a363a227361646d696e223b7d),
('vfkhmgq79q7b7eil75pg008o1muaon57', '::1', 1665092227, 0x5f5f63695f6c6173745f726567656e65726174657c693a313636353039323232373b757365727c4f3a383a22737464436c617373223a31313a7b733a323a226964223b733a323a223130223b733a353a22656d61696c223b4e3b733a373a22636f6e74616374223b4e3b733a383a22757365726e616d65223b733a333a226d6f68223b733a343a226e616d65223b733a31383a224d696e6973747279206f66204865616c7468223b733a343a22726f6c65223b733a323a223130223b733a363a22737461747573223b733a313a2231223b733a31303a22637265617465645f6174223b733a31393a22323032312d30362d32312032313a33323a3330223b733a373a226368616e676564223b4e3b733a393a2269734368616e676564223b733a313a2231223b733a31303a2267726f75705f6e616d65223b733a363a227361646d696e223b7d);

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
CREATE TABLE IF NOT EXISTS `author` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `is_organsiation` varchar(10) NOT NULL DEFAULT 'Yes',
  `address` varchar(200) NOT NULL,
  `telephone` varchar(13) NOT NULL,
  `email` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`id`, `name`, `is_organsiation`, `address`, `telephone`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Africa CDC', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'WHO', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'GHSI', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'African Union', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'World Bank', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'WHO AFRO', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 'Global Preparedness Monitoring Board', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'East African Health', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, 'Burundi', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 'Namibia', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 'South Sudan', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 'Eritrea', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, 'Tanzania', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 'Uganda', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 'Ethiopia', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(16, 'Africa CDC/ African Society for Laboratory Medicine (ASLM)', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 'African Society for Laboratory Medicine (ASLM)', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 'WHO (Global Health Training Alliance)', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, 'Sudan', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 'CDC (US)', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 'UNDRR', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 'Kenya', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 'Mauritius', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 'Democratic Republic of Congo', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 'Central African Republic', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 'Chad', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 'Botswana', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(28, 'Zambia', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(29, 'South Africa', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 'Southern African Development Community', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, 'AU/Africa CDC', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 'UK Health Security Agency', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, 'Organization for the Prohibition of Chemical Weapons', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, 'WHO AFRO/ Africa CDC', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 'WHO (Global Health Observatory)', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 'Cameroon', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, 'Equatorial Guinea', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, 'Congo Brazzaville', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 'Mozambique', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 'Zimbabwe', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 'Seychelles', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 'European Observatory on Health Systems and Policies', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 'International Federation of Red Cross', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 'USAID', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 'UNICEF', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, 'Demographic Health Survey', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, 'International Organization for Migration (IOM)', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(48, 'IOM\'s Global Migration Data analysis Centre (GMDAC)', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(49, 'UNHCR', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(50, 'WHO EURO', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(51, 'World Meteorological Organization (WMO)', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(52, 'Insuresilience', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(53, 'ARC/AU', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(54, 'Lesotho', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(55, 'UN OCHA', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, 'Our World in Data', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, 'International Peace Institute', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, 'Global Health Security Agenda', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 'Independent Panel for Pandemic Preparedness and Response', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(60, 'SeroTracker', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, 'SADC', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 'Eritrea', 'Yes', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `id` int NOT NULL AUTO_INCREMENT,
  `national` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `region_id` int NOT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  `population` int NOT NULL,
  `color` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `id` (`id`),
  KEY `longitude` (`longitude`),
  KEY `region_id` (`region_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `national`, `name`, `region_id`, `longitude`, `latitude`, `population`, `color`) VALUES
(1, 'National', 'Algeria', 3, 2.675551, 28.42191, 43900000, '#b4a168'),
(2, 'National', 'Angola', 4, 17.55463, -12.3557, 32900000, '#b4a168'),
(3, 'National', 'Benin', 5, 2.340591, 9.664139, 12100000, '#b4a168'),
(4, 'National', 'Botswana', 4, 24.6871044, -22.342841, 2400000, '#b4a168'),
(5, 'National', 'Burkina Faso', 5, -1.73222, 12.28355, 20900000, '#b4a168'),
(6, 'National', 'Burundi', 1, 29.347916, -3.36226, 11900000, '#b4a168'),
(7, 'National', 'Cameroon', 1, 12.74493, 5.710275, 26500000, '#b4a168'),
(8, 'National', 'Cape Verde', 5, -23.9838, 15.94668, 600000, '#b4a168'),
(9, 'National', 'Central African Republic', 1, 20.48695, 6.583564, 4800000, '#b4a168'),
(10, 'National', 'Chad', 1, 18.67092, 15.47049, 16400000, '#b4a168'),
(11, 'National', 'Comoros', 2, 43.8722191, -11.875001, 900000, '#b4a168'),
(12, 'National', 'Congo Republic', 1, 15.21983, -0.84251, 5500000, '#b4a168'),
(13, 'National', 'Cote d`Ivoire', 5, -5.55283, 7.62996, 26400000, '#b4a168'),
(14, 'National', 'Djibouti', 2, 42.58079, 11.73524, 1000000, '#b4a168'),
(15, 'National', 'DR Congo', 1, 23.65858, -2.90452, 89600000, '#b4a168'),
(16, 'National', 'Egypt', 3, 29.77112, 26.67132, 102300000, '#b4a168'),
(17, 'National', 'Equatorial Guinea', 1, 10.33565, 1.702984, 1400000, '#b4a168'),
(18, 'National', 'Eritrea', 2, 38.83073, 15.39178, 3500000, '#b4a168'),
(19, 'National', 'Eswatini', 4, 31.5415, -26.5667, 1200000, '#b4a168'),
(20, 'National', 'Ethiopia', 2, 39.63025, 8.66147, 115000000, '#b4a168'),
(21, 'National', 'Gabon', 1, 11.78715, -0.60736, 2200000, '#b4a168'),
(22, 'National', 'Gambia', 5, -15.4003, 13.45286, 2400000, '#b4a168'),
(23, 'National', 'Ghana', 5, -1.20553, 7.975415, 31100000, '#b4a168'),
(24, 'National', 'Guinea', 5, -10.9379, 10.44495, 13100000, '#b4a168'),
(25, 'National', 'Guinea-Bissau', 5, -14.9809, 12.01208, 2000000, '#b4a168'),
(26, 'National', 'Kenya', 2, 37.86243, 0.534379, 53800000, '#b4a168'),
(27, 'National', 'Lesotho', 4, 28.2337082, -29.6099873, 2100000, '#b4a168'),
(28, 'National', 'Liberia', 5, -9.313, 6.454377, 5100000, '#b4a168'),
(29, 'National', 'Libya', 3, 18.00221, 27.16464, 6900000, '#b4a168'),
(30, 'National', 'Madagascar', 2, 46.67876, -19.4729, 27700000, '#b4a168'),
(31, 'National', 'Malawi', 4, 34.3025278, -13.2542161, 19100000, '#b4a168'),
(32, 'National', 'Mali', 5, -3.54484, 17.45831, 20300000, '#b4a168'),
(33, 'National', 'Mauritania', 3, -10.3296, 20.351, 4600000, '#b4a168'),
(34, 'National', 'Mauritius', 2, 57.89986, -20.109, 1300000, '#b4a168'),
(35, 'National', 'Morocco', 3, -6.244, 31.90538, 36900000, '#b4a168'),
(36, 'National', 'Mozambique', 4, 35.54833, -17.3993, 31300000, '#b4a168'),
(37, 'National', 'Namibia', 4, 17.22642, -22.2542, 2500000, '#b4a168'),
(38, 'National', 'Niger', 5, 9.430494, 17.48594, 24200000, '#b4a168'),
(39, 'National', 'Nigeria', 5, 8.102206, 9.608718, 206100000, '#b4a168'),
(40, 'National', 'Rwanda', 2, 29.92306, -1.99997, 13000000, '#b4a168'),
(41, 'National', 'Sahrawi Republic', 3, -12.8858337, 24.2155266, 600000, '#b4a168'),
(42, 'National', 'Sao Tome and Principe', 1, 6.613081, 0.18636, 200000, '#b4a168'),
(43, 'National', 'Senegal', 5, -14.4687, 14.36753, 16700000, '#b4a168'),
(44, 'National', 'Seychelles', 2, 54.93702, -6.70448, 100000, '#b4a168'),
(45, 'National', 'Sierra Leone', 5, -11.7903, 8.460555, 8000000, '#b4a168'),
(46, 'National', 'Somalia', 2, 46.1695531, 5.0955895, 15900000, '#b4a168'),
(47, 'National', 'South Africa', 4, 25.0392, -29.1304, 59300000, '#b4a168'),
(48, 'National', 'South Sudan', 2, 31.3069782, 6.8769908, 11200000, '#b4a168'),
(49, 'National', 'Sudan', 2, 30.2176362, 12.8628073, 43800000, '#b4a168'),
(50, 'National', 'Tanzania', 2, 34.83643, -6.28547, 59700000, '#b4a168'),
(51, 'National', 'Togo', 5, 0.977479, 8.539761, 8300000, '#b4a168'),
(52, 'National', 'Tunisia', 3, 9.577107, 34.16453, 11800000, '#b4a168'),
(53, 'National', 'Uganda', 2, 32.39122, 1.280724, 45700000, '#b4a168'),
(54, 'National', 'Zambia', 4, 27.7769, -13.4913, 18400000, '#b4a168'),
(55, 'National', 'Zimbabwe', 4, 29.8694, -19.0241, 14900000, '#b4a168'),
(56, '', 'Global\r\n', 0, 0, 0, 0, ''),
(57, 'National', 'Regional -AFR\r\n', 0, 0, 0, 0, ''),
(58, '', 'Sub-region - Eastern Africa\r\n', 0, 0, 0, 0, ''),
(59, '', 'Sub-region - Southern Africa\r\n', 0, 0, 0, 0, ''),
(60, '', 'Global', 0, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `file_type`
--

DROP TABLE IF EXISTS `file_type`;
CREATE TABLE IF NOT EXISTS `file_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `icon` varchar(100) NOT NULL DEFAULT 'fa-file',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `file_type`
--

INSERT INTO `file_type` (`id`, `name`, `icon`) VALUES
(1, 'PDF', 'fa-file'),
(2, 'Word Document', 'fa-file'),
(3, 'Excel', 'fa-file'),
(4, 'Web PDF Link', 'fa-file'),
(5, 'Web System', 'fa-file'),
(6, 'Dashboard', 'fa-file'),
(7, 'Database', 'fa-file'),
(8, 'Technical Report', 'fa-file'),
(9, 'Report', 'fa-file'),
(10, 'Survey Report', 'fa-file'),
(11, 'Policy document/ technical guidance', 'fa-file'),
(12, 'Training', 'fa-file'),
(13, 'Technical Guidance/ training materials', 'fa-file'),
(14, 'Technical guidance/policy documents', 'fa-file'),
(15, 'Policy Document', 'fa-file'),
(16, 'Technical guidance', 'fa-file'),
(17, 'Training Materials', 'fa-file');

-- --------------------------------------------------------

--
-- Table structure for table `geographical_coverage`
--

DROP TABLE IF EXISTS `geographical_coverage`;
CREATE TABLE IF NOT EXISTS `geographical_coverage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `last_update` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=354 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `geographical_coverage`
--

INSERT INTO `geographical_coverage` (`id`, `name`, `last_update`, `created_at`) VALUES
(1, 'Global', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Regional -AFR', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Sub-region - Eastern Africa', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'National - Burundi', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'National - Namibia', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'National - South Sudan', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 'National - Eritrea', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'National - Tanzania', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, 'National - Uganda', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 'National - Ethiopia', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 'National - Sudan', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 'National - Kenya', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, 'National - Mauritius', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 'National - Democratic Republic of Congo', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 'National - Central African Republic', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(16, 'National - Chad', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 'National - Botswana', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 'National - Zambia', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, 'National - South Africa', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 'Sub-region - Southern Africa', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 'National - UK', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 'National - Cameroon', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 'National - Equatorial Guinea', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 'National - Congo Brazzaville', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 'National - Mozambique', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 'National - Zimbabwe', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 'National - Seychelles', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(28, 'Regional - EUR', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(29, 'National - Lesotho', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 'National - Eritrea', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, 'National  - Cameroon', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 'National  - Burundi', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `group_permissions`
--

DROP TABLE IF EXISTS `group_permissions`;
CREATE TABLE IF NOT EXISTS `group_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `group_id` (`group_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3909 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `group_permissions`
--

INSERT INTO `group_permissions` (`id`, `group_id`, `permission_id`, `last_updated`) VALUES
(103, 17, 26, '2019-02-13 09:51:09'),
(104, 17, 27, '2019-02-13 09:51:09'),
(132, 18, 13, '2019-08-17 16:46:09'),
(133, 18, 14, '2019-08-17 16:46:09'),
(134, 18, 17, '2019-08-17 16:46:09'),
(135, 18, 18, '2019-08-17 16:46:09'),
(136, 18, 19, '2019-08-17 16:46:09'),
(137, 18, 21, '2019-08-17 16:46:09'),
(138, 18, 22, '2019-08-17 16:46:09'),
(139, 18, 25, '2019-08-17 16:46:09'),
(140, 18, 26, '2019-08-17 16:46:09'),
(141, 18, 27, '2019-08-17 16:46:09'),
(142, 18, 28, '2019-08-17 16:46:09'),
(143, 18, 30, '2019-08-17 16:46:09'),
(3822, 16, 13, '2021-09-14 14:58:17'),
(3823, 16, 14, '2021-09-14 14:58:17'),
(3824, 16, 15, '2021-09-14 14:58:17'),
(3825, 16, 16, '2021-09-14 14:58:17'),
(3826, 16, 17, '2021-09-14 14:58:17'),
(3827, 16, 18, '2021-09-14 14:58:17'),
(3828, 16, 19, '2021-09-14 14:58:17'),
(3829, 16, 20, '2021-09-14 14:58:17'),
(3830, 16, 21, '2021-09-14 14:58:17'),
(3831, 16, 22, '2021-09-14 14:58:17'),
(3832, 16, 23, '2021-09-14 14:58:17'),
(3833, 16, 25, '2021-09-14 14:58:17'),
(3834, 16, 26, '2021-09-14 14:58:17'),
(3835, 16, 27, '2021-09-14 14:58:17'),
(3836, 16, 28, '2021-09-14 14:58:17'),
(3837, 16, 30, '2021-09-14 14:58:17'),
(3838, 16, 31, '2021-09-14 14:58:17'),
(3839, 16, 32, '2021-09-14 14:58:17'),
(3840, 16, 33, '2021-09-14 14:58:17'),
(3841, 16, 34, '2021-09-14 14:58:17'),
(3842, 16, 35, '2021-09-14 14:58:17'),
(3843, 16, 37, '2021-09-14 14:58:17'),
(3844, 16, 38, '2021-09-14 14:58:17'),
(3868, 11, 13, '2021-09-14 15:00:51'),
(3869, 11, 14, '2021-09-14 15:00:51'),
(3870, 11, 17, '2021-09-14 15:00:51'),
(3871, 11, 37, '2021-09-14 15:00:51'),
(3872, 11, 38, '2021-09-14 15:00:51'),
(3885, 19, 13, '2021-12-05 20:27:23'),
(3886, 19, 14, '2021-12-05 20:27:23'),
(3887, 19, 15, '2021-12-05 20:27:23'),
(3888, 19, 17, '2021-12-05 20:27:23'),
(3899, 10, 13, '2022-10-06 21:26:29'),
(3900, 10, 14, '2022-10-06 21:26:29'),
(3901, 10, 15, '2022-10-06 21:26:29'),
(3902, 10, 17, '2022-10-06 21:26:29'),
(3903, 10, 32, '2022-10-06 21:26:29'),
(3904, 10, 35, '2022-10-06 21:26:29'),
(3905, 10, 36, '2022-10-06 21:26:29'),
(3906, 10, 37, '2022-10-06 21:26:29'),
(3907, 10, 38, '2022-10-06 21:26:29'),
(3908, 10, 39, '2022-10-06 21:26:29');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text,
  `definition` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `definition`) VALUES
(13, 'Manage RCCS', 'Manage RCCS'),
(14, 'Add Authors', 'Add Authors'),
(15, 'Manage Standard Form Lists', 'Manage Standard Form Lists'),
(17, 'Add Users', 'Add Users'),
(32, 'Manage Front Page Slider', 'Manage Front Page Slider'),
(35, 'View User Audit Log', 'View User Audit Log'),
(36, 'Add KPIs', 'Add KPIs'),
(37, 'Manage KPIs', 'Manage KPIs'),
(38, 'View Knowledge Hub Dashboard', 'View Knowledge Hub Dashboard'),
(39, 'View RRCs KPIs Dashboard', 'View RRCs KPIs Dashboard');

-- --------------------------------------------------------

--
-- Table structure for table `publication`
--

DROP TABLE IF EXISTS `publication`;
CREATE TABLE IF NOT EXISTS `publication` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `sub_thematic_area_id` int NOT NULL,
  `publication` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text NOT NULL,
  `file_type_id` int NOT NULL,
  `geographical_coverage_id` int NOT NULL,
  `cover` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `is_active` varchar(10) NOT NULL DEFAULT 'Active',
  `visits` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `author_id` (`author_id`),
  KEY `file_type_id` (`file_type_id`),
  KEY `is_active` (`is_active`),
  KEY `sub_thematic_area_id` (`sub_thematic_area_id`),
  KEY `geographical_coverage_id` (`geographical_coverage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`id`, `author_id`, `sub_thematic_area_id`, `publication`, `title`, `description`, `file_type_id`, `geographical_coverage_id`, `cover`, `is_active`, `visits`, `created_at`, `updated_at`) VALUES
(1, 36, 36, 'http://onsp.minsante.cm/fr/publications', 'Health system strengthening publications', 'Health system strengthening publications', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 52, 54, 'https://climateinitiativesplatform.org/index.php/InsuResilience_Global_Partnership', 'Global Partnership for Climate and Disaster Risk Finance and Insurance Solutions (also has Knowledge hub, map showing all active projects and aggregates country information)', 'Global Partnership for Climate and Disaster Risk Finance and Insurance Solutions (also has Knowledge hub, map showing all active projects and aggregates country information)', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 47, 58, 'https://migration.iom.int/ (home page)\n https://migration.iom.int/countries/south-africa (country pages)', 'Human mobility impacts (due to COVID-19)\n Points of Entry and travel restrictions (countries)', 'Human mobility impacts (due to COVID-19)\n Points of Entry and travel restrictions (countries)', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 48, 52, 'https://www.migrationdataportal.org/themes/forced-migration-or-displacement', 'Migration and data portal', 'Migration and data portal', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 56, 56, 'https://ourworldindata.org/terrorism', 'Terrorism', 'Terrorism', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 56, 56, 'https://ourworldindata.org/war-and-peace', 'War and peace', 'War and peace', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 56, 58, 'https://ourworldindata.org/coronavirus', 'COVID-19 pandemic', 'COVID-19 pandemic', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 60, 60, 'https://serotracker.com/en/Explore', 'SARS-CoV-2 serosurvey global database', 'SARS-CoV-2 serosurvey global database', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, 55, 56, 'https://3w.unocha.org/', 'Global humanitarian operational presence', 'Global humanitarian operational presence', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 55, 56, 'https://www.humanitarianresponse.info/en/assessments', 'Humanitarian response assessments', 'Humanitarian response assessments', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 49, 52, 'https://www.unhcr.org/uk/data.html', 'UNHCR Database', 'UNHCR Database', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 45, 60, 'https://www.unicef.org/supply/covid-19-vaccine-market-dashboard', 'COVID-19 vaccine market dashboard', 'COVID-19 vaccine market dashboard', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, 2, 1, 'https://portal.who.int/triplebillions/PowerBIDashboards/HealthEmergencies', 'Country progress towards health emergencies protection', 'Country progress towards health emergencies protection', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 2, 2, 'https://extranet.who.int/sph/jee', 'JEE dashboard', 'JEE dashboard', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 2, 2, 'https://extranet.who.int/sph/home', 'Strategic Partnership for Health Security and Emergency Preparedness (SPH) Portal', 'Strategic Partnership for Health Security and Emergency Preparedness (SPH) Portal', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(16, 2, 3, 'https://www.who.int/initiatives/glass', 'Global antimicrobial resistance and use surveillance system', 'Global antimicrobial resistance and use surveillance system', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 2, 3, 'https://amrcountryprogress.org/#/map-view', 'Tripartite antimicrobial resistance country self-assessment survey', 'Tripartite antimicrobial resistance country self-assessment survey', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 2, 6, 'https://immunizationdata.who.int/', 'Global health observatory immunization dashboard', 'Global health observatory immunization dashboard', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, 2, 9, 'https://extranet.who.int/e-spar/#submission-details', 'State party self-assessment annual reporting tool', 'State party self-assessment annual reporting tool', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 2, 9, 'https://www.who.int/teams/ihr/national-focal-points', 'IHR national focal points', 'IHR national focal points', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 2, 18, 'https://partnersplatform.who.int/en/', 'Partners platform for health in emergencies', 'Partners platform for health in emergencies', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 2, 34, 'https://apps.who.int/nhwaportal/', 'National health workforce accounts data portal', 'National health workforce accounts data portal', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 2, 40, 'https://extranet.who.int/countryplanningcycles/', 'Country planning cycle database', 'Country planning cycle database', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 2, 42, 'https://extranet.who.int/ssa/Index.aspx', 'Surveillance system for attacks on healthcare', 'Surveillance system for attacks on healthcare', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 2, 46, 'https://www.who.int/data/gho/data/themes/noncommunicable-diseases/GHO/noncommunicable-diseases', 'The Global Health Observatory NCD\'s', 'The Global Health Observatory NCD\'s', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 2, 58, 'https://covid19supply.who.int/', 'COVID-19 supply portal', 'COVID-19 supply portal', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 2, 58, 'https://www.who.int/publications/m/item/access-to-covid-19-tools-tracker', 'Access to COVID-19 tools funding commitment tracker', 'Access to COVID-19 tools funding commitment tracker', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(28, 35, 34, 'https://apps.who.int/gho/data/node.main-afro.HWFGRP?lang=en', 'Global health workforce statistics', 'Global health workforce statistics', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(29, 35, 48, 'https://www.who.int/data/gho/health-equity', 'Health equity monitor', 'Health equity monitor', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 5, 34, 'https://data.worldbank.org/indicator/SH.MED.PHYS.ZS', 'Physicians per 1000 people', 'Physicians per 1000 people', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, 5, 34, 'https://data.worldbank.org/indicator/SH.MED.NUMW.P3', 'Nurses and midwives per 1000 people', 'Nurses and midwives per 1000 people', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 5, 42, 'https://data.worldbank.org/indicator/SH.MED.BEDS.ZS', 'Hospital beds per 1000 people', 'Hospital beds per 1000 people', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, 5, 48, 'https://data.worldbank.org/indicator/SI.POV.LMIC.GP?end=2015&locations=1W&start=1981&view=chart', 'Poverty gap', 'Poverty gap', 7, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, 1, 14, 'https://africacdc.org/download/africas-emergency-response-workforce-rapid-response-team-directory/', 'Africa', 'Africa', 7, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 1, 60, 'https://africacdc.org/covid-19-vaccination/', 'Africa CDC COVID-19 vaccine dashboard', 'Africa CDC COVID-19 vaccine dashboard', 7, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 1, 60, 'https://africacdc.org/trusted-vaccines/', 'Trusted vaccines', 'Trusted vaccines', 7, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, 2, 10, 'https://africacdc.org/programme/laboratory-systems-and-networks/geo-mapping-of-laboratory-capacity/', 'Geomapping of laboratory capacity', 'Geomapping of laboratory capacity', 7, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, 53, 54, 'https://www.arc.int/africa-riskview', 'African Risk Capacity: RiskView', 'African Risk Capacity: RiskView', 7, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 31, 26, 'https://africacdc.org/trusted-travel/', 'Trusted travel', 'Trusted travel', 7, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 8, 10, 'https://mobilelabs.eac.int/network/', 'Mobile labs project: EAC regional network of public health labs', 'Mobile labs project: EAC regional network of public health labs', 7, 3, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 2, 3, 'https://www.who.int/publications/i/item/9789241509763', 'Global action plan on antiomicrobial resistance', 'Global action plan on antiomicrobial resistance', 15, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 2, 7, 'https://www.who.int/publications/i/item/9789241506281', 'Advancing food safety initiatives: strategic plan for food safety including foodborne zoonoses 2013-2022', 'Advancing food safety initiatives: strategic plan for food safety including foodborne zoonoses 2013-2022', 15, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 2, 8, 'https://www.who.int/news-room/questions-and-answers/item/emergencies-ten-things-you-need-to-do-to-implement-the-international-health-regulations', 'Emergencies: Ten things you need to do to implement the International Health Regulations', 'Emergencies: Ten things you need to do to implement the International Health Regulations', 15, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 2, 16, 'A strategic framework for emergency preparedness (who.int)', 'A Strategic Framework for Emergency Preparedness', 'A Strategic Framework for Emergency Preparedness', 15, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 2, 34, 'https://www.who.int/publications/i/item/9789240033863', 'The WHO Global Strategic Directions for Nursing and Midwifery (2021', 'The WHO Global Strategic Directions for Nursing and Midwifery (2021', 15, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, 9, 8, 'http://minisante.bi/wp-content/uploads/document_valide/Plan%20d%20action%20national%20pour%20la%20securit%C3%A9%20sanitaire%20au%20Burundi%202019%20-%202023.pdf', 'National Action plan for health security (2019 - 2023)', 'National Action plan for health security (2019 - 2023)', 15, 4, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, 9, 36, 'http://minisante.bi/wp-content/uploads/politiques/Politique%20Nationale%20Sante%202016%202025%20VF%2021052016.pdf', 'The National Health Policy 2016 - 2025', 'The National Health Policy 2016 - 2025', 15, 4, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(48, 9, 44, 'http://minisante.bi/wp-content/uploads/document_valide/Plan%20National%20de%20D%C3%A9veloppement%20de%20l%20Informatique%20de%20Sant%C3%A9%20II%202020-2024.pdf', 'National Digital Health Strategic Plan 2020 - 2024', 'National Digital Health Strategic Plan 2020 - 2024', 15, 4, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(49, 36, 34, 'https://www.minsante.cm/site/?q=fr/content/plan-de-developpement-des-ressources-humaines-de-sante-2013-2017', 'Human resource development plan 2017', 'Human resource development plan 2017', 15, 31, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(50, 36, 36, 'https://www.minsante.cm/site/?q=fr/content/plan-national-de-d%C3%A9veloppement-sanitaire-pnds-2016-2020', 'Plan National de d', 'Plan National de d', 15, 31, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(51, 36, 36, 'https://www.minsante.cm/site/?q=fr/content/strat%C3%A9gie-sectorielle-de-sant%C3%A9-2016-2027-1', 'Strat', 'Strat', 15, 31, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(52, 36, 44, 'https://www.minsante.cm/site/?q=fr/content/plan-int%C3%A9gr%C3%A9-de-suivi-evaluation-pise-2016-2020', 'Integrated Monitoring and Evaluation Plan 2020', 'Integrated Monitoring and Evaluation Plan 2020', 15, 31, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(53, 36, 44, 'https://www.minsante.cm/site/?q=fr/content/2020-2024-national-digital-health-strategic-plan', 'National Digital Health Strategic Plan 2020 - 2024', 'National Digital Health Strategic Plan 2020 - 2024', 15, 31, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(54, 25, 24, 'https://www.ilo.org/wcmsp5/groups/public/---ed_protect/---protrav/---ilo_aids/documents/legaldocument/wcms_301805.pdf', 'Strategic Framework for the fight against HIV/AIDS 2012 - 2016', 'Strategic Framework for the fight against HIV/AIDS 2012 - 2016', 15, 15, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(55, 25, 36, 'http://apps.who.int/iris/bitstream/handle/10665/258612/ccs-caf-2016-2018-fr.pdf;jsessionid=4124F26372247A3B1FF57F978028240A?sequence=1', 'WHO and CAR Strategic Cooperation document', 'WHO and CAR Strategic Cooperation document', 15, 15, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, 25, 44, 'http://msp-centrafrique.net/docs_pdf/Plan_Operationnel_SNIS_16052018_finale.pdf', 'National Health Information Systems Operational Plan 2018', 'National Health Information Systems Operational Plan 2018', 15, 15, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, 26, 24, 'https://espen.afro.who.int/system/files/content/resources/CHAD_NTD_Master_Plan_2016_2020.pdf', 'National Strategic Plan for the Fight against NTDs', 'National Strategic Plan for the Fight against NTDs', 15, 16, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, 26, 36, 'https://www.prb.org/wp-content/uploads/2020/06/Tchad-Plan-National-de-Developpement-Sanitaire-2018-2021.pdf', 'Plan National de d', 'Plan National de d', 15, 16, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 26, 36, 'https://www.prb.org/wp-content/uploads/2020/06/Tchad-Politique-Nationale-de-Sante-2016-2030.pdf', 'The National Health Policy 2016 - 2030', 'The National Health Policy 2016 - 2030', 15, 16, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(60, 26, 58, 'https://chad.un.org/sites/default/files/2020-04/COVID19_MoH_PLAN_CONTINGENC_FINAL_OK.pdf', 'COVID-19 Preparedness and Response plan 2021', 'COVID-19 Preparedness and Response plan 2021', 15, 16, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, 38, 36, 'https://sante.gouv.cg/axes-strategiques-sur-le-plan-national-de-developpement-sanitaire-2018-2022/', 'Plan National de d', 'Plan National de d', 15, 24, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 38, 44, 'https://sante.gouv.cg/bulletin-national-de-linformation-sanitaire-fevrier-2022/', 'National Health Bulletin Feb 2022', 'National Health Bulletin Feb 2022', 15, 24, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(63, 38, 60, 'https://sante.gouv.cg/note-circulaire-faisant-obligation-aux-personnels-de-sante-de-se-faire-vacciner-contre-la-covid-19/', 'COVID-19 vaccination mandate for healthcare workers', 'COVID-19 vaccination mandate for healthcare workers', 15, 24, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(64, 24, 24, 'https://reliefweb.int/sites/reliefweb.int/files/resources/PLAN%20ELIMINATION%20CHOLERA%202013%202017.pdf', 'Multisectoral Strategic Plan for Cholera Elimination in DRC 2013 - 2017', 'Multisectoral Strategic Plan for Cholera Elimination in DRC 2013 - 2017', 15, 14, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, 24, 24, 'https://www.who.int/ebola/DRC-ebola-disease-outbreak-response-plan-16May2018-fr.pdf', 'EVD Strategic Response Plan 2018', 'EVD Strategic Response Plan 2018', 15, 14, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 24, 36, 'https://www.sante.gouv.cd/index.php/rapports#', 'Plan National de d', 'Plan National de d', 15, 14, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, 24, 36, 'https://www.prb.org/wp-content/uploads/2020/06/RDC-Plan-National-de-Developpement-Sanitaire-2016-2020.pdf', 'Plan National de d', 'Plan National de d', 15, 14, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(68, 24, 36, 'https://www.who.int/emergencies/crises/cod/rdc-css-2017-2021.pdf', 'WHO and DRC Strategic Cooperation document 2017 - 2021', 'WHO and DRC Strategic Cooperation document 2017 - 2021', 15, 14, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(69, 24, 58, 'https://fscluster.org/sites/default/files/documents/plan_de_preparation_et_riposte_contre_epidemie_covid-19_rdc.pdf', 'COVID-19 Preparedness and Response plan 2020', 'COVID-19 Preparedness and Response plan 2020', 15, 14, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(70, 37, 36, 'https://www.guineasalud.org/archivos/Protocolos/0503FolletoPODSBaney_2411.pdf', 'Strategic District Health Plan', 'Strategic District Health Plan', 15, 23, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, 37, 36, 'https://www.guineasalud.org/archivos/Protocolos/Plan.pdf', 'National Health Development Plan 2021 - 2025', 'National Health Development Plan 2021 - 2025', 15, 23, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, 37, 44, 'https://www.guineasalud.org/archivos/Protocolos/0567FolletoPlanEstrategicoSNIS_0303.pdf', 'National Health Information Systems Strategic Plan', 'National Health Information Systems Strategic Plan', 15, 23, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(73, 62, 8, 'https://extranet.who.int/sph/sites/default/files/document-library/document/Final%20NAPHS%20Eritrea%20Document%2018%20August%202017.pdf', 'National Action plan for health security (2017-2021)', 'National Action plan for health security (2017-2021)', 15, 30, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(74, 15, 8, 'http://www.onehealthethiopia.org/index.php/resources/strategies-and-guidelines/download/3-strategies-and-guidelines/17-national-action-plan-for-health-security', 'National Action plan for health security (2018-2022)', 'National Action plan for health security (2018-2022)', 15, 10, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(75, 22, 22, 'https://www.afro.who.int/sites/default/files/2017-05/drm-2014---2018.pdf', 'Health Sector Disaster Risk Management Strategic Plan', 'Health Sector Disaster Risk Management Strategic Plan', 15, 12, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(76, 22, 36, 'https://www.health.go.ke/wp-content/uploads/2020/11/Kenya-Health-Sector-Strategic-Plan-2018-231.pdf', 'Health sector strategic plan', 'Health sector strategic plan', 15, 12, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(77, 23, 22, 'https://ndrrmc.govmu.org/Documents/Strategic%20Framework.pdf', 'National Disaster Risk Reduction and Management', 'National Disaster Risk Reduction and Management', 15, 13, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(78, 10, 8, 'https://reliefweb.int/report/namibia/national-action-plan-health-security-naphs-2021-2025?gclid=CjwKCAjwiuuRBhBvEiwAFXKaNBkKCQ26aL90NRm-g4Hjs_AAtx8z0-Qys5uS7VSRRuNXEv1kPlq-BxoCtBkQAvD_BwE', 'National Action plan for health security (2021-2025)', 'National Action plan for health security (2021-2025)', 15, 5, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(79, 61, 36, 'http://www.health.gov.sc/wp-content/uploads/SEYCHELLES-NATIONAL-HEALTH-STRATEGIC-PLAN.pdf', 'Seychelles National Health Strategic Plan 2016-2020', 'Seychelles National Health Strategic Plan 2016-2020', 15, 27, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(80, 29, 46, 'https://www.health.gov.za/wp-content/uploads/2020/11/NationalPolicyFrameworkandStrategyonPalliativeCare20172022.pdf', 'National Policy Framework and Strategy on Palliative Care 2017-2022', 'National Policy Framework and Strategy on Palliative Care 2017-2022', 15, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, 29, 48, 'https://www.knowledgehub.org.za/system/files/elibdownloads/2019-07/National%2520health%2520promotion%2520policy%2520and%2520strategy%25202015%2520-%25202019.pdf', 'The National Health Promotion Policy and Strategy | 2015 - 19', 'The National Health Promotion Policy and Strategy | 2015 - 19', 15, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, 11, 8, 'https://www.afro.who.int/sites/default/files/2020-12/South%20Sudan%20Signed%20NAPHS%20Proposal.pdf', 'National Action plan for health security (2020-2024)', 'National Action plan for health security (2020-2024)', 15, 6, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(83, 19, 16, 'https://www.who.int/health-cluster/news-and-events/news/Multi-hazard-Country-Prepardness-Plan-May-2020.pdf', 'Multihazard Preparedness and Response Plan', 'Multihazard Preparedness and Response Plan', 15, 11, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(84, 13, 8, 'https://extranet.who.int/sph/sites/default/files/document-library/document/NAP_Final_12092017.pdf', 'National Action plan for health security (2017-2021)', 'National Action plan for health security (2017-2021)', 15, 8, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(85, 13, 16, 'https://drmims.sadc.int/sites/default/files/document/2020-03/Tanzania%20Emergence%20Preparedness%20and%20Response%20Plan.pdf', 'Tanzania Emergency Preparedness and Response Plan', 'Tanzania Emergency Preparedness and Response Plan', 15, 8, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(86, 14, 8, 'http://library.health.go.ug/sites/default/files/resources/National%20Action%20Plan%20for%20Health%20Security%202019-2023_0.pdf', 'National Action plan for health security (2019-2023)', 'National Action plan for health security (2019-2023)', 15, 9, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(87, 14, 16, 'https://www.preventionweb.net/files/21032_ugandanationalpolicyfordisasterprep.pdf', 'National Policy for Disaster Preparedness and Management', 'National Policy for Disaster Preparedness and Management', 15, 9, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(88, 28, 36, 'https://www.afro.who.int/sites/default/files/2019-05/NATIONAL%20HEALTH%20IN%20ALL%20POLICIES%20%20STRATEGIC%20FRAMEWORK%20%20%283%29.pdf', 'National Health in All Policies Strategic Framework 2017 - 2021', 'National Health in All Policies Strategic Framework 2017 - 2021', 15, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(89, 28, 36, 'https://www.moh.gov.zm/?page_id=1306', 'ZAMBIA NATIONAL HEALTH STRATEGIC PLAN 2017 ', 'ZAMBIA NATIONAL HEALTH STRATEGIC PLAN 2017 ', 15, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(90, 28, 60, 'https://www.moh.gov.zm/?page_id=1306', 'Zambia Covid 19 Vaccination Strategy _v15', 'Zambia Covid 19 Vaccination Strategy _v15', 15, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(91, 1, 3, 'https://africacdc.org/download/africa-cdc-framework-for-antimicrobial-resistance/', 'Africa CDC framework for antimicrobial resistance 2018-2023', 'Africa CDC framework for antimicrobial resistance 2018-2023', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(92, 1, 5, 'https://africacdc.org/download/biosafety-and-biosecurity-initiative-2021-2025-strategic-plan/', 'Biosafety and Biosecurity Initiative 2021 ', 'Biosafety and Biosecurity Initiative 2021 ', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(93, 1, 5, 'https://africacdc.org/download/advocacy-and-communication-strategy-for-the-biosafety-and-biosecurity-legal-framework/', 'Advocacy and Communication Strategy for the Biosafety and Biosecurity Legal Framework', 'Advocacy and Communication Strategy for the Biosafety and Biosecurity Legal Framework', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(94, 1, 20, 'https://africacdc.org/download/public-health-emergency-operations-center-pheoc-legal-framework-guide/', 'Public Health Emergency Operations Center (PHEOC) Legal Framework Guide', 'Public Health Emergency Operations Center (PHEOC) Legal Framework Guide', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(95, 1, 60, 'https://africacdc.org/download/partnerships-for-african-vaccine-manufacturing-pavm-framework-for-action/#:~:text=The%20Framework%20for%20Action%20answers,vaccine%20needs%20locally%20by%202040.', 'Partnerships for African Vaccine Manufacturing (PAVM) Framework for Action', 'Partnerships for African Vaccine Manufacturing (PAVM) Framework for Action', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(96, 4, 3, 'https://africacdc.org/download/african-union-framework-for-antimicrobial-resistance-control-2020-2025/', 'African Union framework for antimicrobial resistance and control 2020-2025', 'African Union framework for antimicrobial resistance and control 2020-2025', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(97, 4, 7, 'https://au.int/sites/default/files/documents/33005-doc-prioritizing_food_safety_in_africa-eng.pdf', 'Prioritizing food safety in Africa', 'Prioritizing food safety in Africa', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(98, 4, 9, 'https://africacdc.org/download/declaration-on-accelerating-implementation-of-international-health-regulations-in-africa/', 'Declaration on accelerating implementation of IHR in Africa', 'Declaration on accelerating implementation of IHR in Africa', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(99, 31, 58, 'https://au.int/sites/default/files/documents/38264-doc-africa_joint_continental_strategy_for_covid-19_outbreak.pdf', 'Africa Joint Continental Strategy for COVID-19 outbreak', 'Africa Joint Continental Strategy for COVID-19 outbreak', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(100, 47, 52, 'https://publications.iom.int/system/files/pdf/iom-continental-strategy-for-africa_2020-2024.pdf', 'IOM Continental Strategy for Africa 2020-2024', 'IOM Continental Strategy for Africa 2020-2024', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(101, 6, 7, 'https://apps.who.int/iris/handle/10665/1817', 'Food safety and health: a strategy for the WHO African Region 2011', 'Food safety and health: a strategy for the WHO African Region 2011', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(102, 6, 12, 'https://www.afro.who.int/sites/default/files/2019-08/AFR-RC69-6%20Regional%20Strategy%20for%20IDSR%202020-2030.pdf', 'Regional strategy for integrated disease surveillance and response 2020-2030', 'Regional strategy for integrated disease surveillance and response 2020-2030', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(103, 8, 8, 'https://www.eac.int/health/pandemic-preparedness', 'Support to Pandemic Preparedness in the EAC Region', 'Support to Pandemic Preparedness in the EAC Region', 15, 3, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(104, 30, 48, 'https://www.sadc.int/files/4716/1434/6113/RISDP_2020-2030_F.pdf', 'SADC Regional Indicative Strategic Development Plan (RISDP) 2020', 'SADC Regional Indicative Strategic Development Plan (RISDP) 2020', 15, 20, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(105, 4, 58, 'https://au.int/sites/default/files/documents/38264-doc-africa_joint_continental_strategy_for_covid-19_outbreak.pdf', 'Africa Joint Continental Strategy for COVID-19 outbreak', 'Africa Joint Continental Strategy for COVID-19 outbreak', 15, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(106, 9, 38, 'http://minisante.bi/wp-content/uploads/document_valide/Rapport%20annuel%20FBP%202018.pdf', 'Annual report of PBF implementation 2018', 'Annual report of PBF implementation 2018', 9, 4, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(107, 40, 46, 'http://www.mohcc.gov.zw/index.php?option=com_phocadownload&view=category&id=10&Itemid=720', 'Report on the Rapid Assessment of Avoidable Blindness survey in Masvingo Province, Zimbabwe', 'Report on the Rapid Assessment of Avoidable Blindness survey in Masvingo Province, Zimbabwe', 10, 26, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(108, 1, 18, 'https://africacdc.org/download/handbook-for-public-health-emergency-operations-center-operations-and-management/', 'Handbook for Public Health Emergency Operations Center Operations and Management', 'Handbook for Public Health Emergency Operations Center Operations and Management', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(109, 20, 20, 'https://emergency.cdc.gov/cerc/manual/index.asp', 'Crisis and emergency risk communication manual', 'Crisis and emergency risk communication manual', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(110, 58, 58, 'https://ghsagenda.org/wp-content/uploads/2020/07/who-2019-ncov-urban_preparedness-2020.pdf', 'Strengthening Preparedness for COVID-19 in Cities and Urban Settings', 'Strengthening Preparedness for COVID-19 in Cities and Urban Settings', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(111, 33, 28, 'https://www.opcw.org/resources/assistance-and-protection/medical-aspects-assistance-and-protection-against-chemical', 'Medical Aspects of Assistance and Protection against Chemical Weapons', 'Medical Aspects of Assistance and Protection against Chemical Weapons', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(112, 33, 28, 'https://www.opcw.org/resources/assistance-and-protection/practical-guide-medical-management-chemical-warfare-casualties', 'Practical Guide for Medical Management of Chemical Warfare Casualties', 'Practical Guide for Medical Management of Chemical Warfare Casualties', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(113, 21, 22, 'https://www.undrr.org/publication/sendai-framework-disaster-risk-reduction-2015-2030', 'Sendai Framework for Disaster Risk Reduction 2015-2030', 'Sendai Framework for Disaster Risk Reduction 2015-2030', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(114, 2, 4, 'https://apps.who.int/iris/handle/10665/325620', 'Taking a multisectoral, one health approach: a tripartite guide to addressing zoonotic diseases in countries 2019', 'Taking a multisectoral, one health approach: a tripartite guide to addressing zoonotic diseases in countries 2019', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(115, 2, 5, 'https://www.who.int/publications/i/item/9789240011311', 'Laboratory biosafety manual, 4th edition 2020', 'Laboratory biosafety manual, 4th edition 2020', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(116, 2, 5, 'https://www.who.int/influenza/pip/BiosecurityandBiosafety_EN_20Mar2018.pdf', 'Biosecurity and biosafety (2018)', 'Biosecurity and biosafety (2018)', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(117, 2, 7, 'https://www.who.int/news-room/fact-sheets/detail/food-safety', 'Food safety', 'Food safety', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(118, 2, 8, 'https://apps.who.int/iris/handle/10665/342006', 'Health systems for health security: a framework for developing capacities for International health regulations, and components in health systems and other sectors that work in synergy to meet the dema', 'Health systems for health security: a framework for developing capacities for International health regulations, and components in health systems and other sectors that work in synergy to meet the demands imposed by health emergencies', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(119, 2, 10, 'https://www.who.int/ihr/publications/lqms_en.pdf', 'Laboratory quality management system handbook', 'Laboratory quality management system handbook', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(120, 2, 12, 'https://www.who.int/csr/resources/publications/surveillance/whocdscsrisr992.pdf', 'WHO recommended surveillance standards, second edition', 'WHO recommended surveillance standards, second edition', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(121, 2, 16, 'https://www.who.int/publications/i/item/draft-operational-planning-guidance-for-un-country-teams', 'Operational planning guidance to support country preparedness and response', 'Operational planning guidance to support country preparedness and response', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(122, 2, 18, 'Handbook for developing a public health emergency operations centre: part A (who.int)', 'Handbook for developing a public health emergency operations centre', 'Handbook for developing a public health emergency operations centre', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(123, 2, 20, 'https://www.who.int/publications/i/item/9789241550208', 'Communicating risk in Public Health Emergencies (2018)', 'Communicating risk in Public Health Emergencies (2018)', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(124, 2, 22, 'health-emergency-and-disaster-risk-management-framework-eng.pdf (who.int)', 'Health Emergency and Disaster Risk Management Framework', 'Health Emergency and Disaster Risk Management Framework', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(125, 2, 26, 'https://www.euro.who.int/en/health-topics/health-emergencies/international-health-regulations/points-of-entry/points-of-entry', 'Points of entry: IHR, Annex 1b and relevant articles', 'Points of entry: IHR, Annex 1b and relevant articles', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(126, 2, 32, 'https://apps.who.int/iris/handle/10665/345251', 'Framework and toolkit for Infection Prevention and Control in outbreak preparedness, readiness and response at the national level', 'Framework and toolkit for Infection Prevention and Control in outbreak preparedness, readiness and response at the national level', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(127, 2, 32, 'https://www.who.int/health-topics/infection-prevention-and-control#tab=tab_1', 'Infection Prevention and Control', 'Infection Prevention and Control', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(128, 2, 34, 'https://www.who.int/publications/i/item/9789241511131', 'Global strategy on human resources for health: Workforce 2030', 'Global strategy on human resources for health: Workforce 2030', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(129, 2, 34, 'https://www.who.int/publications/i/item/9789241511308', 'Working for health and growth: investing in the health workforce', 'Working for health and growth: investing in the health workforce', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(130, 2, 40, 'https://www.who.int/health-topics/health-systems-governance#tab=tab_1', 'Health System Governance', 'Health System Governance', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(131, 2, 46, 'https://www.who.int/health-topics/noncommunicable-diseases#tab=tab_1', 'Non-communicable diseases', 'Non-communicable diseases', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(132, 2, 48, 'https://www.who.int/health-topics/social-determinants-of-health#tab=tab_1', 'Social determinants of health', 'Social determinants of health', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(133, 2, 58, 'https://www.who.int/publications/i/item/WHO-UHL-PHC-SP-2021.01', 'Building health systems resilience for universal health coverage and health security during the COVID-19 pandemic and beyond: WHO position paper', 'Building health systems resilience for universal health coverage and health security during the COVID-19 pandemic and beyond: WHO position paper', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(134, 2, 60, 'https://www.who.int/groups/strategic-advisory-group-of-experts-on-immunization/covid-19-materials', 'COVID-19 vaccines technical documents', 'COVID-19 vaccines technical documents', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(135, 2, 60, 'https://www.who.int/tools/covid-19-vaccine-introduction-toolkit', 'COVID-19 vaccines introduction toolkit', 'COVID-19 vaccines introduction toolkit', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(136, 6, 58, 'https://www.afro.who.int/sites/default/files/Covid-19/Techinical%20documents/Guidance%20for%20Implementation%20of%20Antigen%20Rapid%20Diagnostic%20Tests%20for%20COVID-19.pdf', 'Guidance for Implementation of Antigen Rapid Diagnostic Tests for COVID-19', 'Guidance for Implementation of Antigen Rapid Diagnostic Tests for COVID-19', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(137, 5, 4, 'https://documents1.worldbank.org/curated/en/703711517234402168/pdf/123023-REVISED-PUBLIC-World-Bank-One-Health-Framework-2018.pdf', 'Operational framework for strengthening human, animal and environmental public health systems at their interface 2018', 'Operational framework for strengthening human, animal and environmental public health systems at their interface 2018', 16, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(138, 27, 24, 'https://www.moh.gov.bw/Publications/Handbook_HIV_treatment_guidelines.pdf', 'Handbook  on HIV Treatment Guidelines', 'Handbook  on HIV Treatment Guidelines', 16, 17, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(139, 27, 36, 'https://www.moh.gov.bw/Publications/ICBHS_Guidelines-v7.pdf', 'National guideline for implementation of integrated community-based health services in Botswana. Gaborone: Government of Botswana; 2020.', 'National guideline for implementation of integrated community-based health services in Botswana. Gaborone: Government of Botswana; 2020.', 16, 17, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(140, 38, 46, 'https://sante.gouv.cg/normes-et-procedures-en-sante-de-la-reproduction/', 'Reproductive health guidelines', 'Reproductive health guidelines', 16, 24, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(141, 37, 36, 'https://www.guineasalud.org/archivos/Protocolos/Manual_indicadores_Salud.pdf', 'Manual of Basic indicators of the National Health System', 'Manual of Basic indicators of the National Health System', 16, 23, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(142, 15, 16, 'http://repository.iifphc.org/bitstream/handle/123456789/685/3%20National%20Disaster%20Health%20Preparedness%20and%20Response%20Guideline.pdf?sequence=1&isAllowed=y', 'National Disaster Health Preparedness and Response Guideline', 'National Disaster Health Preparedness and Response Guideline', 16, 10, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(143, 29, 46, 'https://www.health.gov.za/wp-content/uploads/2020/11/national-cancer-strategic-framework-2017-2022-min.pdf', 'National Cancer Strategic Framework-2017-2022', 'National Cancer Strategic Framework-2017-2022', 16, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(144, 29, 58, 'https://www.health.gov.za/wp-content/uploads/2021/08/GUIDE-TO-ANTIGEN-TESTING-FOR-SARS-COV-2-IN-SOUTH-AFRICA_V4_06.07.2021.pdf', 'GUIDE TO ANTIGEN TESTING FOR SARS COV-2 IN SOUTH AFRICA V4_06.07.2021', 'GUIDE TO ANTIGEN TESTING FOR SARS COV-2 IN SOUTH AFRICA V4_06.07.2021', 16, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(145, 29, 58, 'https://www.health.gov.za/wp-content/uploads/2021/08/MANAGING-COVID19-2-FINAL_v3.35.pdf', 'MANAGING COVID19 FINAL_v3.35', 'MANAGING COVID19 FINAL_v3.35', 16, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(146, 32, 28, 'https://assets.publishing.service.gov.uk/government/uploads/system/uploads/attachment_data/file/712888/Chemical_biological_radiological_and_nuclear_incidents_clinical_management_and_health_protection.pdf', 'Chemical, biological, radiological and nuclear incidents : clinical management and health protection', 'Chemical, biological, radiological and nuclear incidents : clinical management and health protection', 16, 21, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(147, 28, 46, 'https://www.moh.gov.zm/?page_id=1306', 'Essential Newborn Care Guidelines', 'Essential Newborn Care Guidelines', 16, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(148, 28, 46, 'https://www.moh.gov.zm/?page_id=1306', 'Breast Cancer Screening Guideline February 2020', 'Breast Cancer Screening Guideline February 2020', 16, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(149, 40, 36, 'http://www.mohcc.gov.zw/index.php?option=com_phocadownload&view=category&id=10&Itemid=720', 'Zimbabwe Health Sector Development Support Project', 'Zimbabwe Health Sector Development Support Project', 16, 26, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(150, 42, 58, 'https://eurohealthobservatory.who.int/publications/i/health-systems-resilience-during-covid-19-lessons-for-building-back-better', 'Health systems resilience during COVID-19: Lessons for building back better', 'Health systems resilience during COVID-19: Lessons for building back better', 16, 28, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(151, 50, 52, 'https://apps.who.int/iris/bitstream/handle/10665/326924/9789289054270-eng.pdf?sequence=1&isAllowed=y', 'Delivery of immunization services for refugees and migrants', 'Delivery of immunization services for refugees and migrants', 16, 28, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(152, 50, 52, 'https://www.euro.who.int/__data/assets/pdf_file/0018/325611/Toolkit-assessing-HS-capacity-manage-large-influxes-refugees-asylum-seekers-migrants.pdf', 'Toolkit for assessing health system capacity to manage large influxes of refugees, asylum-seekers and migrant', 'Toolkit for assessing health system capacity to manage large influxes of refugees, asylum-seekers and migrant', 16, 28, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(153, 1, 4, 'https://africacdc.org/download/framework-for-one-health-practice-in-national-public-health-institutes/', 'Framework for one health practice in national public health institutes', 'Framework for one health practice in national public health institutes', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(154, 1, 8, 'https://africacdc.org/download/providing-a-legal-framework-for-a-national-public-health-institute-nphi/', 'Providing a legal framework for a National Public Health Institute', 'Providing a legal framework for a National Public Health Institute', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(155, 1, 12, 'https://africacdc.org/download/africa-cdc-event-based-surveillance-framework/', 'Africa CDC event-based surveillance framework', 'Africa CDC event-based surveillance framework', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(156, 1, 30, 'https://africacdc.org/download/framework-for-supply-chain-management-for-public-health-emergency-preparedness-and-response/', 'Framework for Supply Chain Management for Public Health Emergency Preparedness and Response', 'Framework for Supply Chain Management for Public Health Emergency Preparedness and Response', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(157, 1, 32, 'https://africacdc.org/download/personal-protective-equipment-for-different-clinical-settings-and-activities/', 'Personal Protective Equipment for Different Clinical Settings and Activities', 'Personal Protective Equipment for Different Clinical Settings and Activities', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(158, 1, 46, 'https://africacdc.org/programme/division-of-disease-control-and-prevention/unit-of-non-communicable-diseases-and-mental-health/', 'Unit of NCD\'s and mental health', 'Unit of NCD\'s and mental health', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(159, 1, 58, 'https://africacdc.org/download/revealing-the-toll-of-covid-19-a-technical-package-for-rapid-mortality-surveillance-and-epidemic-response/', 'Revealing the toll of COVID-19: a technical package for rapid mortality surveillance and epidemic response', 'Revealing the toll of COVID-19: a technical package for rapid mortality surveillance and epidemic response', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(160, 1, 58, 'https://africacdc.org/download/africa-cdc-guidance-for-assessment-monitoring-and-movement-restrictions-of-people-at-risk-for-covid-19-in-africa/', 'Africa CDC Guidance for Assessment, Monitoring, and Movement Restrictions of People at Risk for COVID-19 in Africa', 'Africa CDC Guidance for Assessment, Monitoring, and Movement Restrictions of People at Risk for COVID-19 in Africa', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(161, 1, 60, 'https://africacdc.org/download/guidance-on-administration-of-covid-19-vaccine-boosters-in-africa/', 'Guidance on administration of COVID-19 vaccine boosters in Africa', 'Guidance on administration of COVID-19 vaccine boosters in Africa', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(162, 6, 6, 'https://www.afro.who.int/publications/strategic-framework-research-immunization-who-african-region-immunization-and-vaccine', 'Strategic framework for research on immunization in the WHO African Region', 'Strategic framework for research on immunization in the WHO African Region', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(163, 6, 6, 'https://www.afro.who.int/publications/guide-developing-immunization-policies', 'Guide for developing immunization policies 2017', 'Guide for developing immunization policies 2017', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(164, 6, 12, 'https://www.afro.who.int/publications/technical-guidelines-integrated-disease-surveillance-and-response-african-region-third', 'Technical Guidelines for Integrated Disease Surveillance and Response in the African Region: Third edition', 'Technical Guidelines for Integrated Disease Surveillance and Response in the African Region: Third edition', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(165, 6, 58, 'https://www.afro.who.int/sites/default/files/Covid-19/Techinical%20documents/Technical%20guidance%20on%20laboratory%20operations%20for%20coronavirus%20disease%20COVID-19%20testing%20in%20the%20WHO%20African%20region.pdf', 'Technical guidance on laboratory operations for coronavirus disease (COVID-19) testing in the WHO African region', 'Technical guidance on laboratory operations for coronavirus disease (COVID-19) testing in the WHO African region', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(166, 34, 30, 'https://www.afro.who.int/sites/default/files/2021-03/AFRO_PHEOC-Legal-Framework-Guide_.pdf', 'Public Health Emergency Operations Center (PHEOC) Legal Framework Guide: A Guide for the Development of a Legal Framework to Authorize the Establishment and Operationalization of a PHEOC', 'Public Health Emergency Operations Center (PHEOC) Legal Framework Guide: A Guide for the Development of a Legal Framework to Authorize the Establishment and Operationalization of a PHEOC', 16, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(167, 30, 24, 'https://www.sadc.int/files/8514/1172/0467/Regional_Minimum_Standards_for_theHarmonised_Guidance_on_HIV_Testingand_Counselling_in_the_SADC_Region.pdf', 'Regional Minimum Standards for the Harmonised Guidance on HIV Testing and Counselling in the SADC Region', 'Regional Minimum Standards for the Harmonised Guidance on HIV Testing and Counselling in the SADC Region', 16, 20, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `publication` (`id`, `author_id`, `sub_thematic_area_id`, `publication`, `title`, `description`, `file_type_id`, `geographical_coverage_id`, `cover`, `is_active`, `visits`, `created_at`, `updated_at`) VALUES
(168, 30, 48, 'https://ihrda.uwazi.io/api/files/1511871326922vuha6u00d5xe8b67ibic0udi.pdf', 'Protocol on Health', 'Protocol on Health', 16, 20, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(169, 30, 48, 'https://www.sadc.int/files/8815/8724/1999/SADC_Regional_Response_to_COVID19-_PORTUGUESE.pdf', 'Key Strategies, Guidelines, SADC Technical & Thematic Reports', 'Key Strategies, Guidelines, SADC Technical & Thematic Reports', 16, 20, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(170, 2, 4, 'https://www.who.int/initiatives/tripartite-zoonosis-guide#:~:text=Navigating%20the%20Tripartite%20Zoonoses%20Guide,approach%20to%20address%20zoonotic%20diseases.', 'Tripartite Zoonoses guidance', 'Tripartite Zoonoses guidance', 13, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(171, 1, 10, 'https://africacdc.org/programme/laboratory-systems-and-networks/', 'Laboratory systems and networks', 'Laboratory systems and networks', 14, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(172, 3, 1, 'https://www.ghsindex.org/report-model/', 'GHSI report 2021', 'GHSI report 2021', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(173, 7, 8, 'https://ghsagenda.org/wp-content/uploads/2020/07/gpmb_annualreport_2019_08-11-2019.pdf', 'A world at risk: annual report on global preparedness for health emergencies (2019)', 'A world at risk: annual report on global preparedness for health emergencies (2019)', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(174, 59, 58, 'https://theindependentpanel.org/wp-content/uploads/2021/05/COVID-19-Make-it-the-Last-Pandemic_final.pdf', 'COVID-19: make it the last pandemic', 'COVID-19: make it the last pandemic', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(175, 43, 42, 'https://www.ifrc.org/sites/default/files/2021-07/IFRC-GHS_July2021-2.pdf', 'Global health security', 'Global health security', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(176, 47, 52, 'https://reliefweb.int/sites/reliefweb.int/files/resources/WMR-2022-EN.pdf', 'World migration report 2022', 'World migration report 2022', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(177, 45, 56, 'https://www.unicef.org/reports/humanitarian-action-children-2022-overview', 'Humanitarian Action for Children 2022 Overview', 'Humanitarian Action for Children 2022 Overview', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(178, 2, 2, 'https://www.who.int/emergencies/operations/international-health-regulations-monitoring-evaluation-framework/joint-external-evaluations', 'JEE mission reports', 'JEE mission reports', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(179, 2, 6, 'https://www.who.int/publications/m/item/implementing-the-immunization-agenda-2030', 'Implementing the immunization agenda 2030', 'Implementing the immunization agenda 2030', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(180, 2, 7, 'https://www.who.int/publications/i/item/9789241565165', 'WHO estimates of the global burden of foodborne diseases 2007-2015', 'WHO estimates of the global burden of foodborne diseases 2007-2015', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(181, 2, 30, 'https://www.who.int/publications/m/item/act-accelerator-facilitation-council-vaccine-manufacturing-working-group---report-to-the-g20', 'ACT-Accelerator Facilitation Council: Vaccine Manufacturing Working Group - Report to the G20', 'ACT-Accelerator Facilitation Council: Vaccine Manufacturing Working Group - Report to the G20', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(182, 2, 34, 'https://www.who.int/publications/i/item/978-92-4-151546-7', 'Delivered by women, led by men: A gender and equity analysis of the global health and social workforce', 'Delivered by women, led by men: A gender and equity analysis of the global health and social workforce', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(183, 2, 58, 'https://www.who.int/publications/i/item/WHO-2019-nCoV-health_workforce-2020.1', 'Health workforce policy and management in the context of the COVID-19 pandemic response', 'Health workforce policy and management in the context of the COVID-19 pandemic response', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(184, 2, 58, 'https://www.who.int/publications/m/item/a74-9-who-s-work-in-health-emergencies', 'Report of the Review Committee on the Functioning of the International Health Regulations (2005) during the COVID-19 response', 'Report of the Review Committee on the Functioning of the International Health Regulations (2005) during the COVID-19 response', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(185, 2, 58, 'https://www.who.int/publications/m/item/assessment-of-the-covid-19-supply-chain-system-report', 'Assessment of the COVID-19 supply chain system: full report', 'Assessment of the COVID-19 supply chain system: full report', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(186, 6, 54, 'https://www.afro.who.int/health-topics/climate-change', 'Climate change', 'Climate change', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(187, 5, 7, 'https://thedocs.worldbank.org/en/doc/890291523304595565-0090022018/original/FINALIWGReport3.5.18.pdf', 'From Panic and Neglect to Investing in Health Security: Financing Pandemic Preparedness at a National Level', 'From Panic and Neglect to Investing in Health Security: Financing Pandemic Preparedness at a National Level', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(188, 5, 22, 'https://documents.worldbank.org/en/publication/documents-reports/documentdetail/099920102112210580/p17651608cd5ae0120bcf801c3db57fdfdf', 'Inclusive Approaches to Disaster Risk Management ', 'Inclusive Approaches to Disaster Risk Management ', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(189, 5, 42, 'https://openknowledge.worldbank.org/handle/10986/35429', 'Frontline : Preparing Healthcare Systems for Shocks from Disasters to Pandemics', 'Frontline : Preparing Healthcare Systems for Shocks from Disasters to Pandemics', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(190, 51, 54, 'https://public.wmo.int/en (home page)\n https://public.wmo.int/en/media/press-release/weather-related-disasters-increase-over-past-50-years-causing-more-damage-fewer (article)', 'Weather-related disasters increase over past 50 years, causing more damage but fewer deaths', 'Weather-related disasters increase over past 50 years, causing more damage but fewer deaths', 8, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(191, 9, 10, 'http://minisante.bi/wp-content/uploads/document_valide/Audit%20environnemental%20et%20social%20des%20laboratoires%20de%20Sant%C3%A9%20Publique.pdf', 'Audit environnemental et social des laboratoires de sant', 'Audit environnemental et social des laboratoires de sant', 8, 4, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(192, 9, 54, 'http://minisante.bi/wp-content/uploads/politiques/covid19/Projet%20de%20pr%C3%A9paration%20et%20de%20riposte%20strat%C3%A9gique%20du%20Burundi%20face%20au%20COVID-19_Plan%20d\'engagement%20environnemental%20et%20social%20(pees).pdf', 'ENVIRONMENTAL AND SOCIAL COMMITMENT PLAN', 'ENVIRONMENTAL AND SOCIAL COMMITMENT PLAN', 8, 4, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(193, 36, 50, 'https://www.minsante.cm/site/?q=fr/content/enqu%C3%AAte-d%C3%A9mographique-de-sant%C3%A9-v2018', 'Health Demographic Survey 2018', 'Health Demographic Survey 2018', 8, 31, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(194, 36, 54, 'https://www.minsante.cm/site/?q=fr/content/environmental-and-social-commitment-plan-escp', 'ENVIRONMENTAL AND SOCIAL COMMITMENT PLAN', 'ENVIRONMENTAL AND SOCIAL COMMITMENT PLAN', 8, 31, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(195, 25, 34, 'https://www.who.int/hac/techguidance/training/analysing_health_systems/2_renforcement_des_ressources_humaines_en_rca_02.PDF', 'Analysis of strategies to strengthen Human ressource 2002', 'Analysis of strategies to strengthen Human ressource 2002', 8, 15, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(196, 38, 38, 'https://sante.gouv.cg/comptes-de-la-sante-2016-2018/', 'National Health Account 2016 - 2018', 'National Health Account 2016 - 2018', 8, 24, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(197, 38, 50, 'https://sante.gouv.cg/annuaire-statistique-2017-2018/', 'Annual Health Statistical Report 2018', 'Annual Health Statistical Report 2018', 8, 24, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(198, 38, 54, 'https://sante.gouv.cg/cadre-de-gestion-environnementale-et-sociale-cges-2/', 'ENVIRONMENTAL AND SOCIAL COMMITMENT PLAN', 'ENVIRONMENTAL AND SOCIAL COMMITMENT PLAN', 8, 24, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(199, 54, 54, 'https://www.gov.ls/wp-content/uploads/2022/03/Lesotho-Biennial-Update-Report.pdf', 'ENVIRONMENTAL AND SOCIAL COMMITMENT PLAN', 'ENVIRONMENTAL AND SOCIAL COMMITMENT PLAN', 8, 29, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(200, 54, 54, 'https://www.gov.ls/wp-content/uploads/2022/03/Lesotho-Biennial-Update-Report.pdf', 'Lesotho Bieniel Updated Report 2022', 'Lesotho Bieniel Updated Report 2022', 8, 29, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(201, 54, 58, 'https://www.gov.ls/wp-content/uploads/2022/03/Lesotho-Biennial-Update-Report.pdf', 'LESOTHO COVID-19 Situation Summary Report', 'LESOTHO COVID-19 Situation Summary Report', 8, 29, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(202, 39, 36, 'https://www.uhc2030.org/fileadmin/uploads/ihp/Documents/Results___Evidence/JANS_Lessons/2013.09.02%20JANS%20English%20Report%20Final%20with%20Annexes.pdf', 'Joint Assessment of the Mozambican Health Sector Strategic Plan', 'Joint Assessment of the Mozambican Health Sector Strategic Plan', 8, 25, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(203, 39, 38, 'https://www.afro.who.int/sites/default/files/2017-06/moz-Manual_en.pdf', 'Mozambique National Health Account', 'Mozambique National Health Account', 8, 25, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(204, 39, 38, 'https://www.healthpolicyproject.com/pubs/7887/Mozambique_HFP.pdf', 'Health Financing Proflie', 'Health Financing Proflie', 8, 25, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(205, 39, 40, 'http://www.heart-resources.org/wp-content/uploads/2012/05/290340-Mozambique_Health_Partnership_Report_F1_29.06.11.pdf', 'Review of Health Partner Engagement with the Ministry of Health, Mozambique', 'Review of Health Partner Engagement with the Ministry of Health, Mozambique', 8, 25, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(206, 29, 24, 'https://www.thebrenthurstfoundation.org/books-publications/vital-signs-health-security-in-south-africa/', 'Vital Signs: Health Security in South Africa', 'Vital Signs: Health Security in South Africa', 8, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(207, 29, 36, 'https://www.health.gov.za/annual-reports/', 'Annual Performance Plan 2021 - 2022', 'Annual Performance Plan 2021 - 2022', 8, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(208, 29, 50, 'https://www.health.gov.za/annual-reports/', 'Annual Health Report 2018 - 2019', 'Annual Health Report 2018 - 2019', 8, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(209, 29, 50, 'https://www.health.gov.za/annual-reports/', 'Annual Health Report 2020-2021', 'Annual Health Report 2020-2021', 8, 19, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(210, 46, 50, 'https://dhsprogram.com/pubs/pdf/FR361/FR361.pdf', 'Zambia Demographic and Health Survey (2018 ZDHS)', 'Zambia Demographic and Health Survey (2018 ZDHS)', 8, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(211, 45, 46, 'https://www.unicef.org/zambia/media/661/file/Zambia-home-fortification-study-2015.pdf', 'Zambia Home Fortification study 2015', 'Zambia Home Fortification study 2015', 8, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(212, 45, 50, 'https://www.unicef.org/zambia/media/881/file/Zambia-DHS-2013.pdf', 'Zambia Demographic and Health Survey (2013-14 ZDHS)', 'Zambia Demographic and Health Survey (2013-14 ZDHS)', 8, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(213, 44, 46, 'https://www.usaid.gov/sites/default/files/documents/tagged_Zambia-Nutrition-Profile.pdf', 'Zambia: Nutrition Profile', 'Zambia: Nutrition Profile', 8, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(214, 28, 24, 'https://www.moh.gov.zm/?page_id=1306', 'Zambia MIS 2018 Overview and Results', 'Zambia MIS 2018 Overview and Results', 8, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(215, 28, 50, 'https://www.moh.gov.zm/?page_id=1306', 'Zambia Annual Health Statistical Report 2020', 'Zambia Annual Health Statistical Report 2020', 8, 18, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(216, 40, 38, 'http://www.mohcc.gov.zw/index.php?option=com_phocadownload&view=category&id=10&Itemid=720', 'Strengthening the health delivery system in Zimbabwe', 'Strengthening the health delivery system in Zimbabwe', 8, 26, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(217, 42, 40, 'https://www.euro.who.int/__data/assets/pdf_file/0004/307939/Strengthening-health-system-governance-better-policies-stronger-performance.pdf', 'Strengthening health system governance', 'Strengthening health system governance', 8, 28, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(218, 1, 5, 'https://africacdc.org/download/africa-cdc-biosafety-and-biosecurity-initiative-report-on-the-consultative-process-to-identify-priorities-for-strengthening-biosafety-and-biosecurity/', 'Africa CDC Biosafety and Biosecurity Initiative Report on the Consultative Process to Identify Priorities for Strengthening Biosafety and Biosecurity', 'Africa CDC Biosafety and Biosecurity Initiative Report on the Consultative Process to Identify Priorities for Strengthening Biosafety and Biosecurity', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(219, 1, 14, 'https://africacdc.org/download/framework-for-public-health-workforce-development-2020-2025/', 'Framework for public health workforce development 2020-2025', 'Framework for public health workforce development 2020-2025', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(220, 18, 14, 'https://www.who.int/workforcealliance/knowledge/resources/africawglearning/en/', 'The health workforce in Africa: challenges and prospects', 'The health workforce in Africa: challenges and prospects', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(221, 6, 12, 'https://www.afro.who.int/sites/default/files/2019-11/VPD_Surv_Brochure_Final_20190918_WEB.pdf', 'Investment case for vaccine-preventable disease surveillance in the African Region', 'Investment case for vaccine-preventable disease surveillance in the African Region', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(222, 6, 12, 'https://www.afro.who.int/health-topics/disease-outbreaks/outbreaks-and-other-emergencies-updates', 'Outbreak and Emergencies bulletin', 'Outbreak and Emergencies bulletin', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(223, 6, 16, 'https://apps.who.int/iris/handle/10665/1679', 'Emergency preparedness and response in the African Region: current situation and way forward', 'Emergency preparedness and response in the African Region: current situation and way forward', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(224, 6, 38, 'https://www.afro.who.int/publications/state-health-financing-african-region', 'State of health financing in the African Region (2013)', 'State of health financing in the African Region (2013)', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(225, 6, 46, 'https://www.afro.who.int/health-topics/noncommunicable-diseases', 'Non-communicable disease country profiles (AFRO, 2014) part of the Global NCD platform', 'Non-communicable disease country profiles (AFRO, 2014) part of the Global NCD platform', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(226, 6, 48, 'https://www.afro.who.int/health-topics/social-and-economic-determinants-health', 'Social and economic determinants of health', 'Social and economic determinants of health', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(227, 6, 58, 'https://www.afro.who.int/sites/default/files/2021-04/WHO%20AFR%20Covid-19%202021%20SRP_Final_16042021.pdf', 'COVID-19 strategic preparedness and response plan for the WHO African Region (Feb 2021 - Jan 2022)', 'COVID-19 strategic preparedness and response plan for the WHO African Region (Feb 2021 - Jan 2022)', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(228, 5, 38, 'https://openknowledge.worldbank.org/handle/10986/29177', 'Going Universal in Africa : How 46 African Countries Reformed User Fees and Implemented Health Care Priorities', 'Going Universal in Africa : How 46 African Countries Reformed User Fees and Implemented Health Care Priorities', 8, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(229, 57, 56, 'https://www.ipinst.org/wp-content/uploads/publications/rpt_safrica.pdf', 'Southern Africa: Threats and Capabilities', 'Southern Africa: Threats and Capabilities', 8, 20, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(230, 30, 24, 'https://www.sadc.int/files/4314/1172/0046/Assessment_Report_on_the_Status_ofHIV_Testing_and_Counselling_Policies_inthe_SADC_Region.pdf', 'Assessment Report on the Status of HIV Testing and Counselling Policies in the SADC Region.', 'Assessment Report on the Status of HIV Testing and Counselling Policies in the SADC Region.', 8, 20, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(231, 30, 48, 'https://www.sadc.int/files/4516/3152/3357/Annual_Report_2020_-_2021.pdf', 'Annual Report 2020 - 2021', 'Annual Report 2020 - 2021', 8, 20, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(232, 30, 50, 'https://www.sadc.int/files/4816/1279/0669/SADC_SYB_2019_Print_version_V1.0.pdf', 'SADC Demographics and Social Statistics 2019', 'SADC Demographics and Social Statistics 2019', 8, 20, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(233, 1, 18, 'https://africacdc.org/news-item/public-health-emergency-operations-centres-pheocs-management-webinar-series/', 'Public Health Emergency Operations Centres (PHEOCs) Management Webinar Series', 'Public Health Emergency Operations Centres (PHEOCs) Management Webinar Series', 12, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(234, 16, 58, 'https://aslm.org/courses/covid-19-courses-and-training/', 'COVID-19 courses and training', 'COVID-19 courses and training', 12, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(235, 2, 14, 'https://openwho.org/', 'Open WHO courses in health emergencies', 'Open WHO courses in health emergencies', 12, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(236, 2, 40, 'https://www.who.int/publications/m/item/e-learning-course-on-health-financing-policy-for-universal-health-coverage-(uhc)', 'e-Learning Course on Health Financing Policy for universal health coverage', 'e-Learning Course on Health Financing Policy for universal health coverage', 12, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(237, 2, 48, 'https://www.who.int/publications/i/item/9789241507981', 'Health in all policies: training manual', 'Health in all policies: training manual', 12, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(238, 2, 58, 'https://www.who.int/emergencies/diseases/novel-coronavirus-2019/training/online-training', 'COVID-19 online training', 'COVID-19 online training', 12, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(239, 1, 14, 'https://africacdc.org/news-item/training-on-the-use-of-the-africa-volunteer-health-corps-avohc-net-tool/', 'Training on the use of the Africa Volunteer Health Corps tool', 'Training on the use of the Africa Volunteer Health Corps tool', 12, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(240, 2, 4, 'https://www.who.int/news/item/24-05-2018-online-course-on-global-health-at-the-human-animal-ecosystem-interface', 'Online course for global health at the human-animal-ecosystem interface', 'Online course for global health at the human-animal-ecosystem interface', 17, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(241, 6, 6, 'https://www.afro.who.int/publications/mid-level-management-course-epi-managers', 'Mid-level management course for EPI managers', 'Mid-level management course for EPI managers', 17, 1, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(242, 16, 10, 'What We Do - African Society for Laboratory Medicine (aslm.org)', 'Diagnostic evidence hub', 'Diagnostic evidence hub', 17, 2, 'cover.png', 'Active', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

DROP TABLE IF EXISTS `regions`;
CREATE TABLE IF NOT EXISTS `regions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `region_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `region_name`) VALUES
(1, 'AU_Central'),
(2, 'AU_Eastern'),
(3, 'AU_Northern'),
(4, 'AU_Southern'),
(5, 'AU_Western');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
CREATE TABLE IF NOT EXISTS `setting` (
  `id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `site_name` varchar(100) NOT NULL,
  `seo_keywords` text NOT NULL,
  `site_description` text NOT NULL,
  `address` text,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `timezone` varchar(150) NOT NULL,
  `default_password` varchar(20) DEFAULT 'africacdc.org',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `title`, `site_name`, `seo_keywords`, `site_description`, `address`, `email`, `phone`, `language`, `timezone`, `default_password`) VALUES
(1, 'RRC Knowledge Hub', 'RRC Knowledge Hub', 'African Union, Maternal Health, Africa', 'RCCs serve as hubs for Africa CDC surveillance, preparedness and emergency response activities and coordinate regional public health initiatives by Member States in consultation with Africa CDC headquarters', '', 'admin@admin.com', '0702787688', 'english', 'Africa/Nairobi', 'africacdc.org');

-- --------------------------------------------------------

--
-- Table structure for table `slides`
--

DROP TABLE IF EXISTS `slides`;
CREATE TABLE IF NOT EXISTS `slides` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caption` varchar(200) DEFAULT NULL,
  `slide_image` varchar(200) NOT NULL,
  `position` int NOT NULL DEFAULT '1',
  `published` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `slides`
--

INSERT INTO `slides` (`id`, `caption`, `slide_image`, `position`, `published`) VALUES
(1, NULL, 'img-slide-1.jpg', 1, 0),
(2, NULL, 'img-slide-2.jpg', 2, 0),
(3, NULL, 'img-slide-3.jpg', 3, 0),
(4, NULL, 'img-slide-3.jpg', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sub_thematic_area`
--

DROP TABLE IF EXISTS `sub_thematic_area`;
CREATE TABLE IF NOT EXISTS `sub_thematic_area` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa-circle',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `thematic_area_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `thematic_area_id` (`thematic_area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_thematic_area`
--

INSERT INTO `sub_thematic_area` (`id`, `description`, `icon`, `created_at`, `updated_at`, `thematic_area_id`) VALUES
(1, 'Global Health Security Progress\r\n', 'fa-circle', NULL, NULL, 1),
(2, 'Joint External Evaluation\r\n', 'fa-circle', '2022-10-04 09:59:18', '2022-10-04 09:59:18', 1),
(3, 'Antimicrobial Resistance', 'fa-circle', '2022-10-04 10:00:20', '2022-10-04 10:00:20', 2),
(4, 'One Health\r\n', 'fa-circle', '2022-10-04 10:00:20', '2022-10-04 10:00:20', 2),
(5, 'Biosafety and biosecurity\r\n', 'fa-circle', '2022-10-04 10:00:20', '2022-10-04 10:00:20', 2),
(6, 'Immunization\r\n', 'fa-circle', '2022-10-04 10:00:20', '2022-10-04 10:00:20', 2),
(7, 'Food safety\r\n', 'fa-circle', '2022-10-04 10:00:20', '2022-10-04 10:00:20', 2),
(8, 'National legislation, policy and financing\r\n', 'fa-circle', '2022-10-04 10:00:20', '2022-10-04 10:00:20', 2),
(9, 'IHR coordination, communication and advocacy', 'fa-circle', '2022-10-04 10:00:20', '2022-10-04 10:00:20', 2),
(10, 'Laboratory systems', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 3),
(12, 'Surveillance and reporting', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 3),
(14, 'PH/Epi workforce', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 3),
(16, 'EPR planning', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4),
(18, 'Emergency response operations', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4),
(20, 'Risk communication', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4),
(22, 'Risk Management', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4),
(24, 'Infectious disease response', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4),
(26, 'Points of entry/travel', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4),
(28, 'Chemical and radiation events', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4),
(30, 'Medical countermeasures', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(32, 'Infection prevention and control', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(34, 'Workforce', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(36, 'Health policy and planning', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(38, 'Health financing', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(40, 'Health system governance', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(42, 'Health system resilience', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(44, 'Health information systems', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 5),
(46, 'Non-communicable diseases and risk factors', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 6),
(48, 'Social determinants of health', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 6),
(50, 'Demography, population and health', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 6),
(52, 'Migration and displacement', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 6),
(54, 'Climate, environment and natural disasters', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 6),
(56, 'Peace, security and humanitarian response', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 6),
(58, 'COVID-19', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 7),
(60, 'Immunization: COVID-19', 'fa-circle', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 7);

-- --------------------------------------------------------

--
-- Table structure for table `thematic_area`
--

DROP TABLE IF EXISTS `thematic_area`;
CREATE TABLE IF NOT EXISTS `thematic_area` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `display_index` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `display_index` (`display_index`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thematic_area`
--

INSERT INTO `thematic_area` (`id`, `description`, `icon`, `created_at`, `updated_at`, `display_index`) VALUES
(1, 'Overall Global Health Security capacities', 'fa-shield', NULL, NULL, 1),
(2, 'Prevention', 'fa-ban', '2022-10-04 08:58:34', '2022-10-04 08:58:34', 1),
(3, 'Detection\r\n', 'fa-microscope', '2022-10-04 09:00:55', '2022-10-04 09:00:55', 1),
(4, 'Response', 'fa-reply-all', '2022-10-04 09:33:21', '2022-10-04 09:33:21', 1),
(5, 'Health system', 'fa-notes-medical', '2022-10-04 09:36:30', '2022-10-04 09:36:30', 1),
(6, 'Wider risk environment', 'fa-seedling', '2022-10-04 09:38:43', '2022-10-04 09:38:43', 1),
(7, 'COVID-19', 'fa-virus-covid', '2022-10-04 09:41:55', '2022-10-04 09:41:55', 1),
(8, 'Health', 'fa-notes-medical', '2022-10-04 09:56:02', '2022-10-04 09:56:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(13) DEFAULT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `changed` date DEFAULT NULL,
  `isChanged` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1871 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `contact`, `username`, `password`, `name`, `role`, `status`, `created_at`, `changed`, `isChanged`) VALUES
(1, 'admin@admin.com', NULL, 'africacdc', '21232f297a57a5a743894a0e4a801fc3', 'Africa CDC', '10', 1, '2022-10-06 13:51:29', NULL, 0),
(2, 'mnanozi@yahoo.com', '0772675898', '549', 'ac3b167e495e0d64b0380a2a99b95712', 'Nanozi Margaret ', '17', 1, '0000-00-00 00:00:00', '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_access_level1`
--

DROP TABLE IF EXISTS `user_access_level1`;
CREATE TABLE IF NOT EXISTS `user_access_level1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `access_id` varchar(100) NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_id` (`access_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_access_level1`
--

INSERT INTO `user_access_level1` (`id`, `access_id`, `user_id`) VALUES
(1, '1', 1),
(2, '1', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_access_level2`
--

DROP TABLE IF EXISTS `user_access_level2`;
CREATE TABLE IF NOT EXISTS `user_access_level2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) NOT NULL,
  `access_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `region_id` (`access_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_access_level2`
--

INSERT INTO `user_access_level2` (`id`, `user_id`, `access_id`) VALUES
(1, '1', '54'),
(2, '1', '31');

-- --------------------------------------------------------

--
-- Table structure for table `user_access_level3`
--

DROP TABLE IF EXISTS `user_access_level3`;
CREATE TABLE IF NOT EXISTS `user_access_level3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `access_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `region_id` (`access_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_access_level4`
--

DROP TABLE IF EXISTS `user_access_level4`;
CREATE TABLE IF NOT EXISTS `user_access_level4` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `access_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `region_id` (`access_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_access_level5`
--

DROP TABLE IF EXISTS `user_access_level5`;
CREATE TABLE IF NOT EXISTS `user_access_level5` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `access_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `region_id` (`access_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_name` text,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `group_name`) VALUES
(10, 'sadmin'),
(17, 'enroller'),
(19, 'Supervisor');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
