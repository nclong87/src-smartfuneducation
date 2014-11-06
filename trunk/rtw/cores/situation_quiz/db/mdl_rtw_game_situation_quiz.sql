-- phpMyAdmin SQL Dump
-- version 4.2.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 05, 2014 at 04:47 AM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `smartfunedu`
--

-- --------------------------------------------------------

--
-- Table structure for table `mdl_rtw_game_situation_quiz`
--

CREATE TABLE IF NOT EXISTS `mdl_rtw_game_situation_quiz` (
`id` int(11) NOT NULL,
  `game_player_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `show_time` datetime DEFAULT NULL,
  `submit_time` datetime DEFAULT NULL,
  `user_answer` text,
  `coin_id` int(11) DEFAULT NULL,
  `experience_id` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `self_evaluation` int(1) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mdl_rtw_game_situation_quiz`
--
ALTER TABLE `mdl_rtw_game_situation_quiz`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mdl_rtw_game_situation_quiz`
--
ALTER TABLE `mdl_rtw_game_situation_quiz`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=53;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
