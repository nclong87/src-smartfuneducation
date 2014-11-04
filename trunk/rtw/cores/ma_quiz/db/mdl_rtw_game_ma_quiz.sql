-- phpMyAdmin SQL Dump
-- version 4.2.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 04, 2014 at 08:24 AM
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
-- Table structure for table `mdl_rtw_game_ma_quiz`
--

CREATE TABLE IF NOT EXISTS `mdl_rtw_game_ma_quiz` (
`id` int(11) NOT NULL,
  `game_player_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `show_time` datetime DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `submit_time` datetime DEFAULT NULL,
  `user_answer` text,
  `is_correct` int(1) DEFAULT '0',
  `coin_id` int(11) DEFAULT NULL,
  `experience_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mdl_rtw_game_ma_quiz`
--
ALTER TABLE `mdl_rtw_game_ma_quiz`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mdl_rtw_game_ma_quiz`
--
ALTER TABLE `mdl_rtw_game_ma_quiz`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
