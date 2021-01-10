-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2021 at 03:01 PM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.2.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `catmakeup`
--

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `Id` int(20) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`Id`, `full_name`, `email`) VALUES
(0, 'dkenga@strathmore.edu', 'dkenga@strathmore.edu'),
(0, 'prettyshish38@gmail.com', 'prettyshish38@gmail.com'),
(0, 'Kelvin Kibunja', 'kkibunja@strathmore.edu'),
(0, 'Esther Gathenya', 'egathenya@strathmore.edu'),
(0, 'easaugnov@gmail.com', 'easaugnov@gmail.com'),
(0, 'Yvonne Karanja', 'ykaranja@strathmore.edu'),
(0, 'fitadministrativestaff@strathmore.edu', 'fitadministrativestaff@strathmore.edu'),
(0, 'Bridget Thomas', 'bridget.thomas@strathmore.edu'),
(0, 'wc1quiz@gmail.com', 'wc1quiz@gmail.com'),
(0, 'refg.jobs@sothebysrealty.com', 'refg.jobs@sothebysrealty.com'),
(0, 'Elections Committee', 'electionscommittee@strathmore.edu'),
(0, 'Daniel Ruiru', 'daniel.ruiru@strathmore.edu'),
(0, 'zhangyiming@gmail.com', 'zhangyiming@gmail.com'),
(0, 'Ruby Kimondo', 'rkimondo@strathmore.edu'),
(0, 'Doreen Mukasa', 'dmukasa@strathmore.edu'),
(0, 'Justus Ondigi', 'JOndigi@strathmore.edu'),
(0, 'Platform Notifications', 'PlatformNotifications-noreply@google.com'),
(0, 'Rich from BetterCloud', 'monitor@bettercloud.com'),
(0, 'Student Council', 'studentcouncil@strathmore.edu'),
(0, 'Coursera', 'Coursera@email.coursera.org'),
(0, 'Canva', 'start@engage.canva.com'),
(0, 'Strathmore Communications', 'communications@strathmore.edu'),
(0, 'Careers Job Alerts', 'careers_jobalerts@strathmore.edu'),
(0, 'Coursera', 'no-reply@m.mail.coursera.org'),
(0, 'Instagram', 'no-reply@mail.instagram.com'),
(0, 'Google', 'no-reply@accounts.google.com'),
(0, 'Rees Alumasa', 'reesalumasa@gmail.com');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
