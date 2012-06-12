-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 12, 2012 at 10:49 AM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `newsconverter`
--

-- --------------------------------------------------------

--
-- Table structure for table `broadcastusers`
--

CREATE TABLE IF NOT EXISTS `broadcastusers` (
  `username` varchar(255) NOT NULL,
  `comments` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `category_order` int(11) NOT NULL,
  `Type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `contentlink`
--

CREATE TABLE IF NOT EXISTS `contentlink` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tweet_title` varchar(255) NOT NULL,
  `articleTitle` varchar(500) NOT NULL,
  `oldLink` varchar(500) NOT NULL,
  `link` varchar(1000) NOT NULL,
  `category` int(11) DEFAULT NULL,
  `contentBody` longtext,
  `notes` varchar(1000) NOT NULL,
  `createon` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1744 ;

-- --------------------------------------------------------

--
-- Table structure for table `lastid`
--

CREATE TABLE IF NOT EXISTS `lastid` (
  `lastid` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutedusers`
--

CREATE TABLE IF NOT EXISTS `mutedusers` (
  `username` varchar(255) NOT NULL,
  `comment` varchar(500) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rawtweets`
--

CREATE TABLE IF NOT EXISTS `rawtweets` (
  `str_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` varchar(200) NOT NULL,
  `link` varchar(1000) NOT NULL,
  `user_screen_name` varchar(200) NOT NULL,
  `tweet_create_on` datetime NOT NULL,
  `createon` datetime NOT NULL,
  `state` varchar(50) NOT NULL,
  PRIMARY KEY (`str_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
