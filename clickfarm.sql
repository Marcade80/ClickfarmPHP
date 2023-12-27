-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 27, 2023 at 11:55 AM
-- Server version: 5.7.36
-- PHP Version: 8.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clickfarm`
--

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
CREATE TABLE IF NOT EXISTS `resources` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ResType` varchar(64) NOT NULL,
  `Amount` bigint(20) NOT NULL,
  `Price` int(11) NOT NULL,
  `Update_Timestamp` bigint(8) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ResType` (`ResType`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `resources`
--

TRUNCATE TABLE `resources`;
--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`ID`, `ResType`, `Amount`, `Price`, `Update_Timestamp`) VALUES
(1, 'used_fuelrod', 0, 0, 0),
(2, 'steel', 0, 0, 0),
(3, 'FlourHigh', 0, 390, 1703678131),
(4, 'eggs', 0, 0, 0),
(5, 'GrainLow', 0, 308, 1703678131),
(6, 'FlourLow', 0, 460, 1703678131),
(7, 'GrainHigh', 0, 234, 1703678131),
(8, 'Pasta', 0, 0, 0),
(9, 'Coal', 0, 31, 0),
(10, 'Oil', 0, 12, 1703678131),
(11, 'CopperOre', 0, 229, 0);

-- --------------------------------------------------------

--
-- Table structure for table `saves`
--

DROP TABLE IF EXISTS `saves`;
CREATE TABLE IF NOT EXISTS `saves` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UID` varchar(40) NOT NULL,
  `Data` text NOT NULL,
  `Update_Timestamp` bigint(8) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `GUID` (`UID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `saves`
--

TRUNCATE TABLE `saves`;
--
-- Dumping data for table `saves`
--

INSERT INTO `saves` (`ID`, `UID`, `Data`, `Update_Timestamp`) VALUES
(3, 'd24591d8-d00d-143a-0311-6b7f6eb9d391', 'WyJGYXJtbGFuZDo1NCIsIm5ld1VzZXI6MSIsImF1dG9PaWw6MjAiLCJGYWN0b3J5Q29zdDoxMCIsIkZsb3VyU2lsb0NvbnRlbnRzOjAiLCJub3RpZmljYXRpb25zX2F1dG9jbG9zZTowIiwiRmFybWxhbmRDb3N0OjEyNiIsIkdyYWluU2lsb0NvbnRlbnRzOjAiLCJkZWJ1ZzoxIiwiUVM6MCIsIk9pbFByaWNlOjYiLCJhdXRvRmxvdXI6ODc1IiwiT2lsOjEiLCJDb2FsUG93ZXJQbGFudFByaWNlOjUwMDAwMDAwIiwib25saW5lVXNlcklEOnVuZGVmaW5lZCIsIm1hZGU6MjQ0NC43NCIsImF1dG9QYXN0YTo0NTAiLCJFbmVyZ3lBdmFpbGFibGU6MCIsIkdyYWluU2lsb0Ftb3VudDoyIiwiR3JhaW5SZXNlcnZlOjAiLCJHcmFpblNpbG9QcmljZToyMDIwIiwiZ3VpZDpkMjQ1OTFkOC1kMDBkLTE0M2EtMDMxMS02YjdmNmViOWQzOTEiLCJNb25leTo5MSIsIkhhcnZlc3Q6MTAwIiwic3BlbmQ6NDM2MC4yNSIsImF1dG9LdW5zdHN0b2Y6MjQwIl0=', 1703675100),
(4, '849a4808-51ae-f0a8-3fa7-f9cc756987a2', 'WyJGYXJtbGFuZDo1MCIsImF1dG9PaWw6MjAiLCJGYWN0b3J5Q29zdDoxMCIsIk5leHRMZXZlbDo3NTAwMCIsIlBsYXllckxldmVsOjMiLCJub3RpZmljYXRpb25zX2F1dG9jbG9zZTowIiwiRmxvdXJTaWxvQ29udGVudHM6NDUwMCIsIkZhcm1sYW5kQ29zdDoxMDkuMjQiLCJHcmFpblNpbG9Db250ZW50czo1MDAwIiwiZGVidWc6MCIsIlFTOjAiLCJPaWxQcmljZToyIiwiYXV0b0Zsb3VyOjg3NSIsIk9pbDowIiwiQ29hbFBvd2VyUGxhbnRQcmljZTo1MDAwMDAwMCIsIkVuZXJneUF2YWlsYWJsZTowIiwibWFkZToxODQ0MiIsImF1dG9QYXN0YTo0NTAiLCJHcmFpblJlc2VydmU6MCIsImd1aWQ6ODQ5YTQ4MDgtNTFhZS1mMGE4LTNmYTctZjljYzc1Njk4N2EyIiwiTW9uZXk6MTY0NTYuODciLCJIYXJ2ZXN0OjEwMCIsIldpbmRtaWxsQW1vdW50OjUiLCJzcGVuZDozOTk1LjEzIiwiYXV0b0t1bnN0c3RvZjoyNDAiXQ==', 1703676970);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
