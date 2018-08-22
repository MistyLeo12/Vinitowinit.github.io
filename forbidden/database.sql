-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2015 at 05:58 AM
-- Server version: 5.5.32
-- PHP Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fmc`
--
CREATE DATABASE IF NOT EXISTS `fmc` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `fmc`;

-- --------------------------------------------------------

--
-- Table structure for table `fmc_departments`
--

CREATE TABLE IF NOT EXISTS `fmc_departments` (
  `unique_id` int(11) NOT NULL AUTO_INCREMENT,
  `department` text NOT NULL,
  `is_deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `fmc_departments` VALUES (1,'test');
-- --------------------------------------------------------

--
-- Table structure for table `fmc_members`
--

CREATE TABLE IF NOT EXISTS `fmc_members` (
  `member_id` text NOT NULL,
  `member` text NOT NULL,
  `is_deleted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fmc_members`
--

# INSERT INTO `fmc_members` (`member_id`, `member`, `is_deleted`) VALUES
# ('951502334866593', 'Kedar Prabhudesai', 0),
# ('955160041168102', 'Courtney Scoufis', 0),
# ('10202857490750007', 'Bernice Kwan', 0),
# ('10102283624874383', 'Nick Czarnek', 0),
#  ('10101035343989524', 'Jordan Malof', 0),
# ('10152876342445712', 'Shane Loomis', 0),
# ('855427637811769', 'Tristan Haas', 0),
# ('10203378218192518', 'Pooja Mehta', 0),
# ('10152801640207348', 'Luke Wolf', 0),
# ('10203415753608655', 'Betty Chen', 0),
#  ('10152608920624340', 'Yolanda Qin', 0),
# ('10205102806716235', 'JP Lucaci', 0),
# ('10203610728363681', 'Hannah McCracken', 0),
# ('10153306879329167', 'Cameron Henry Tripp', 0),
# ('10152439719061603', 'Lavanya Sunder', 0);

-- --------------------------------------------------------

--
-- Table structure for table `table_main`
--

CREATE TABLE IF NOT EXISTS `table_main` (
  `upload_unique_id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_timestamp` double NOT NULL,
  `user_name_facebook` text NOT NULL,
  `user_location_x` double NOT NULL,
  `user_location_y` double NOT NULL,
  `upload_origin` int(11) NOT NULL,
  `upload_unique_facebook_id` text NOT NULL,
  `upload_text` text NOT NULL,
  `upload_image_address` text NOT NULL,
  `upload_audio_address` text NOT NULL,
  `upload_video_address` text NOT NULL,
  `fmc_member_assigned` text NOT NULL,
  `fmc_project_status` int(11) NOT NULL,
  `fmc_department` text NOT NULL,
  `user_access_token_facebook` text NOT NULL,
  `isDeleted` int(11) NOT NULL,
  `isAnonymous` int(11) NOT NULL,
  `user_unique_facebook_id` text NOT NULL,
  PRIMARY KEY (`upload_unique_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Dumping data for table `table_main`
--
/*
INSERT INTO `table_main` (`upload_unique_id`, `upload_timestamp`, `user_name_facebook`, `user_location_x`, `user_location_y`, `upload_origin`, `upload_unique_facebook_id`, `upload_text`, `upload_image_address`, `upload_audio_address`, `upload_video_address`, `fmc_member_assigned`, `fmc_project_status`, `fmc_department`, `user_access_token_facebook`, `isDeleted`, `isAnonymous`, `user_unique_facebook_id`) VALUES
-- (1, 1414685247, 'Jordan Malof', 0, 0, 3, '703437566430186_703437966430146', 'test post 1', '//uploads//1_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
-- (2, 1415238763, 'Jordan Malof', 0, 0, 3, '703437566430186_706746672765942', 'test [see media in attached link]', '//uploads//2_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(3, 1415238810, 'Jordan Malof', 0, 0, 3, '703437566430186_706746889432587', 'test 2 [see media in attached link]', '//uploads//3_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(4, 1415247218, 'Jordan Malof', 0, 0, 3, '703437566430186_706789906094952', 'test 3 [see media in attached link]', '//uploads//4_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(5, 1415250829, 'Jordan Malof', 0, 0, 3, '703437566430186_706811002759509', 'test 3 [see media in attached link]', '//uploads//5_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(6, 1415252054, 'Jordan Malof', 0, 0, 3, '703437566430186_706815632759046', 'Hello [see media in attached link]', '//uploads//6_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(7, 1415473414, 'Kedar Prabhudesai', 0, 0, 3, '703437566430186_708372202603389', '[see media in attached link]', '//uploads//7_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '951502334866593'),
(8, 1415570538, 'Nick Czarnek', 0, 0, 3, '703437566430186_708953962545213', 'This is an fmc test post for page refreshing.', '', '', '', '', 0, '', '', 0, 0, '10102283624874383'),
(9, 1415570870, 'Nick Czarnek', 0, 0, 3, '703437566430186_708956292544980', 'Second test for page refresh.', '', '', '', '', 0, '', '', 0, 0, '10102283624874383'),
(10, 1415571175, 'Nick Czarnek', 0, 0, 3, '703437566430186_708957875878155', 'Refresh t3', '', '', '', '', 0, '', '', 0, 0, '10102283624874383'),
(11, 1415572322, 'Nick Czarnek', 0, 0, 3, '703437566430186_708964879210788', 'refresh t4', '', '', '', '', 0, '', '', 0, 0, '10102283624874383'),
(12, 1415572324, 'Jordan Malof', 0, 0, 3, '703437566430186_708964889210787', 'test 5', '', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(13, 1415572688, 'Jordan Malof', 0, 0, 3, '703437566430186_708966869210589', 'test 6', '', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(14, 1415572729, 'Jordan Malof', 0, 0, 3, '703437566430186_708967015877241', 'test 7', '', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(15, 1415572795, 'Nick Czarnek', 0, 0, 3, '703437566430186_708967452543864', 't6', '', '', '', '', 0, '', '', 0, 0, '10102283624874383'),
(16, 1415573795, 'Nick Czarnek', 0, 0, 3, '703437566430186_708972625876680', 't7', '', '', '', '', 0, '', '', 0, 0, '10102283624874383'),
(17, 1415578948, 'Nick Czarnek', 0, 0, 3, '703437566430186_708999905873952', 't8', '', '', '', '', 0, '', '', 0, 0, '10102283624874383'),
(18, 1415579544, 'Jordan Malof', 0, 0, 3, '703437566430186_709003379206938', 'mobile test 4  [see media in attached link]', '//uploads//18_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(19, 1415661527, 'JP Lucaci', 0, 0, 3, '703437566430186_709479692492640', 'http://colab-sbx-209.oit.duke.edu//admin//index.html', '', '', '', '', 0, '', '', 0, 0, '10205102806716235'),
(20, 1415840539, 'Kedar Prabhudesai', 0, 0, 3, '703437566430186_710586949048581', 'Test 1, new push with back button [see media in attached link]', '//uploads//20_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '951502334866593'),
(21, 1415841491, 'Kedar Prabhudesai', 0, 0, 3, '703437566430186_710591769048099', 'Test 2: Latest push to Colab [see media in attached link]', '//uploads//21_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '951502334866593'),
(22, 1415841723, 'Kedar Prabhudesai', 0, 0, 3, '703437566430186_710592872381322', 'Hello World!!!', '', '', '', '', 0, '', '', 0, 0, '951502334866593'),
(23, 1415842172, 'Kedar Prabhudesai', 0, 0, 3, '703437566430186_710594865714456', 'Test 3: To live page', '', '', '', '', 0, '', '', 0, 0, '951502334866593'),
(24, 1415842546, 'Kedar Prabhudesai', 0, 0, 3, '703437566430186_710596602380949', 'Test 3', '', '', '', '', 0, '', '', 0, 0, '951502334866593'),
(25, 1415844237, 'Kedar Prabhudesai', 0, 0, 3, '703437566430186_710603769046899', 'Test 6', '', '', '', '', 0, '', '', 0, 0, '951502334866593'),
(26, 1415844560, 'Jordan Malof', 0, 0, 3, '703437566430186_710605209046755', 'test 5', '', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(27, 1415929195, 'Kedar Prabhudesai', 0, 0, 3, '703437566430186_711060235667919', 'Test 1', '', '', '', '', 0, '', '', 0, 0, '951502334866593'),
(28, 1418195823, 'Jordan Malof', 0, 0, 3, '703437566430186_724858090954800', 'auto text \n  [posted via FMC mobile app]', '', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(29, 1418196215, 'Jordan Malof', 0, 0, 3, '703437566430186_724861184287824', 'auto text 2 \n  [posted via FMC mobile app]', '', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(30, 1418196600, 'Jordan Malof', 0, 0, 3, '703437566430186_724862717621004', 'test upload \n  [posted via FMC mobile app]', '', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(31, 1418196809, 'Jordan Malof', 0, 0, 3, '703437566430186_724863950954214', 'test \n  [posted via FMC mobile app]', '//uploads//31_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(32, 1418200218, 'Jordan Malof', 0, 0, 3, '703437566430186_724882304285712', 'test upload 3 \n  [posted via FMC mobile app]', '//uploads//32_attachment_image.jpg', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(33, 1418200244, 'Jordan Malof', 0, 0, 3, '703437566430186_724882464285696', 'test upload no attach \n  [posted via FMC mobile app]', '', '', '', '', 0, '', '', 0, 0, '10101035343989524'),
(34, 1418200285, 'Jordan Malof', 0, 0, 3, '703437566430186_724883070952302', 'test upload with video \n  [posted via FMC mobile app]', '', '', '', '', 0, '', '', 0, 0, '10101035343989524');
*/
-- --------------------------------------------------------

--
-- Table structure for table `table_notes`
--

CREATE TABLE IF NOT EXISTS `table_notes` (
  `unique_id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_unique_id` text NOT NULL,
  `note` text NOT NULL,
  `note_timestamp` double NOT NULL,
  `fmc_member_unique_id` text NOT NULL,
  PRIMARY KEY (`unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

