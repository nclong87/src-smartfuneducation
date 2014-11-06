-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 06, 2014 at 12:15 AM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `moodle`
--

-- --------------------------------------------------------

--
-- Table structure for table `mdl_rtw_game_situation_quiz`
--

CREATE TABLE IF NOT EXISTS `mdl_rtw_game_situation_quiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_player_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `show_time` datetime DEFAULT NULL,
  `submit_time` datetime DEFAULT NULL,
  `user_answer` text CHARACTER SET utf8 COLLATE utf8_bin,
  `coin_id` int(11) DEFAULT NULL,
  `experience_id` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `self_evaluation` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=66 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
