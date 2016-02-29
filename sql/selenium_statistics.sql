-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: dev-mysql51
-- Erstellungszeit: 08. Feb 2016 um 13:00
-- Server Version: 5.1.68-log
-- PHP-Version: 5.4.39-1+deb.sury.org~precise+2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


--
-- Tabellenstruktur für Tabelle `cases`
--

CREATE TABLE IF NOT EXISTS `test_cases` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `test_suite_id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  `passed_time` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `case_errors`
--

CREATE TABLE IF NOT EXISTS `test_case_errors` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `test_case_id` int(255) NOT NULL,
  `error_message` varchar(1200) NOT NULL,
  `error_url` varchar(650) NOT NULL,
  `screenshot_url` varchar(650) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `suites`
--

CREATE TABLE IF NOT EXISTS `test_suites` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `suite_name` varchar(255) NOT NULL,
  `build_number` varchar(255) NOT NULL,
  `shop_version` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  `passed_time` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;