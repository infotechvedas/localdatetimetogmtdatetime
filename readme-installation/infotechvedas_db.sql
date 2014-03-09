-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 08, 2014 at 03:49 PM
-- Server version: 5.5.35
-- PHP Version: 5.5.9-1+sury.org~precise+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `infotechvedas_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE IF NOT EXISTS `membership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(22) DEFAULT NULL,
  `emaild` varchar(45) DEFAULT NULL,
  `registered_date` datetime DEFAULT NULL,
  `country_name` varchar(40) NOT NULL,
  `country_timezone` decimal(8,2) NOT NULL,
  `gmt_date_time` datetime NOT NULL,
  `local_date_time` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `membership`
--

INSERT INTO `membership` (`id`, `name`, `emaild`, `registered_date`, `country_name`, `country_timezone`, `gmt_date_time`, `local_date_time`) VALUES
(1, 'dhananjay k sharma', 'dhananjayksharma@gmail.com', '2014-03-08 15:46:27', 'India', -5.50, '2014-03-08 10:16:27', '08-03-2014 15:46:27'),
(2, 'info tech vedas', 'infotechvedas@yahoo.co.in', '2014-03-08 15:46:54', 'India', -5.50, '2014-03-08 10:16:54', '08-03-2014 15:46:54'),
(3, 'testing for NY', 'infotechvedas@yahoo.co.in', '2014-03-08 15:47:32', 'New York', 5.00, '2014-03-08 20:47:32', '08-03-2014 15:47:32'),
(4, 'testing for Tripoli', 'infotechvedas@yahoo.co.in', '2014-03-08 15:48:30', 'Tripoli', -1.00, '2014-03-08 14:48:30', '08-03-2014 15:48:30');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
