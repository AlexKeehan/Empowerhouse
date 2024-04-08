-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2024 at 12:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Drop tables
DROP TABLE IF EXISTS `dbcourses`;
DROP TABLE IF EXISTS `dbeventmedia`;
DROP TABLE IF EXISTS `dbevents`;
DROP TABLE IF EXISTS `dbeventvolunteers`;
DROP TABLE IF EXISTS `dbmessages`;
DROP TABLE IF EXISTS `dbpersons`;
DROP TABLE IF EXISTS `dbtrainingperiods`;

--
-- Database: `housedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbcourses`
--

CREATE TABLE `dbcourses` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `abbrevName` text NOT NULL,
  `staffId` text NOT NULL,
  `eventId` text NOT NULL,
  `periodId` int(11) NOT NULL,
  `date` char(10) NOT NULL,
  `startTime` char(5) NOT NULL,
  `endTime` char(5) NOT NULL,
  `description` text NOT NULL,
  `location` text NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbeventmedia`
--

CREATE TABLE `dbeventmedia` (
  `id` int(11) NOT NULL,
  `eventID` int(11) NOT NULL,
  `url` text NOT NULL,
  `type` text NOT NULL,
  `format` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbevents`
--

CREATE TABLE `dbevents` (
  `id` int(11) NOT NULL,
  `eventname` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbevents`
--

INSERT INTO `dbevents` (`id`, `eventname`) VALUES
(14, 'Late February Training'),
(15, 'Spring 2024');

-- --------------------------------------------------------

--
-- Table structure for table `dbeventvolunteers`
--

CREATE TABLE `dbeventvolunteers` (
  `eventID` int(11) NOT NULL,
  `userID` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbmessages`
--

CREATE TABLE `dbmessages` (
  `id` int(11) NOT NULL,
  `senderID` varchar(256) NOT NULL,
  `recipientID` varchar(256) NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` text NOT NULL,
  `time` varchar(16) NOT NULL,
  `wasRead` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbmessages`
--

INSERT INTO `dbmessages` (`id`, `senderID`, `recipientID`, `title`, `body`, `time`, `wasRead`) VALUES
(1, 'justin.raze.50@gmail.com', 'vmsroot', 'New User Registered', 'Go Verify or Delete Them', '2023-12-12-14:29', 1),
(2, 'test@test.com', 'vmsroot', 'New User Registered', 'Go Verify or Delete Them', '2023-12-12-17:06', 1),
(3, 'testadmin@test.com', 'vmsroot', 'New User Registered', 'Go Verify or Delete Them', '2023-12-12-17:13', 0),
(4, 'scarlet@gmail.com', 'vmsroot', 'New User Registered', 'Go Verify or Delete Them', '2023-12-12-17:53', 0),
(5, 'vmsroot', 'justin.raze.50@gmail.com', 'A new event was created!', 'Exciting news!\r\n\r\nThe [](event: 11) event from 5:30 PM to 9:30 PM on Monday, February 12, 2024 was added!\r\nSign up today!', '2023-12-14-19:47', 0),
(6, 'vmsroot', 'scarlet@gmail.com', 'A new event was created!', 'Exciting news!\r\n\r\nThe [](event: 11) event from 5:30 PM to 9:30 PM on Monday, February 12, 2024 was added!\r\nSign up today!', '2023-12-14-19:47', 0),
(7, 'vmsroot', 'test@test.com', 'A new event was created!', 'Exciting news!\r\n\r\nThe [](event: 11) event from 5:30 PM to 9:30 PM on Monday, February 12, 2024 was added!\r\nSign up today!', '2023-12-14-19:47', 0),
(8, 'vmsroot', 'testadmin@test.com', 'A new event was created!', 'Exciting news!\r\n\r\nThe [](event: 11) event from 5:30 PM to 9:30 PM on Monday, February 12, 2024 was added!\r\nSign up today!', '2023-12-14-19:47', 0),
(9, 'testadmin2@test.com', 'vmsroot', 'New User Registered', 'Go Verify or Delete Them', '2023-12-15-10:51', 1),
(10, 'polack@umw.edu', 'vmsroot', 'New User Registered', 'Go Verify or Delete Them', '2024-01-09-14:20', 1),
(11, 'japwahl@gmail.com', 'vmsroot', 'New User Registered', 'Go Verify or Delete Them', '2024-01-09-14:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dbpersons`
--

CREATE TABLE `dbpersons` (
  `id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `start_date` text DEFAULT NULL,
  `venue` text DEFAULT NULL,
  `first_name` text NOT NULL,
  `last_name` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` text DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` text DEFAULT NULL,
  `phone1` varchar(12) NOT NULL,
  `phone1type` text DEFAULT NULL,
  `phone2` varchar(12) DEFAULT NULL,
  `phone2type` text DEFAULT NULL,
  `birthday` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `shirt_size` varchar(3) DEFAULT NULL,
  `computer` varchar(3) DEFAULT NULL,
  `camera` varchar(3) NOT NULL,
  `transportation` varchar(3) NOT NULL,
  `contact_name` text NOT NULL,
  `contact_num` varchar(12) NOT NULL,
  `relation` text NOT NULL,
  `contact_time` text NOT NULL,
  `cMethod` text DEFAULT NULL,
  `position` text DEFAULT NULL,
  `credithours` text DEFAULT NULL,
  `howdidyouhear` text DEFAULT NULL,
  `commitment` text DEFAULT NULL,
  `motivation` text DEFAULT NULL,
  `specialties` text DEFAULT NULL,
  `convictions` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `completedTraining` text NOT NULL DEFAULT 'False',
  `availability` text DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `hours` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `sundays_start` char(5) DEFAULT NULL,
  `sundays_end` char(5) DEFAULT NULL,
  `mondays_start` char(5) DEFAULT NULL,
  `mondays_end` char(5) DEFAULT NULL,
  `tuesdays_start` char(5) DEFAULT NULL,
  `tuesdays_end` char(5) DEFAULT NULL,
  `wednesdays_start` char(5) DEFAULT NULL,
  `wednesdays_end` char(5) DEFAULT NULL,
  `thursdays_start` char(5) DEFAULT NULL,
  `thursdays_end` char(5) DEFAULT NULL,
  `fridays_start` char(5) DEFAULT NULL,
  `fridays_end` char(5) DEFAULT NULL,
  `saturdays_start` char(5) DEFAULT NULL,
  `saturdays_end` char(5) DEFAULT NULL,
  `profile_pic` text NOT NULL,
  `force_password_change` tinyint(1) NOT NULL,
  `gender` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `dbpersons`
--

INSERT INTO `dbpersons` (`id`, `start_date`, `venue`, `first_name`, `last_name`, `address`, `city`, `state`, `zip`, `phone1`, `phone1type`, `phone2`, `phone2type`, `birthday`, `email`, `shirt_size`, `computer`, `camera`, `transportation`, `contact_name`, `contact_num`, `relation`, `contact_time`, `cMethod`, `position`, `credithours`, `howdidyouhear`, `commitment`, `motivation`, `specialties`, `convictions`, `type`, `status`, `completedTraining`, `availability`, `schedule`, `hours`, `notes`, `password`, `sundays_start`, `sundays_end`, `mondays_start`, `mondays_end`, `tuesdays_start`, `tuesdays_end`, `wednesdays_start`, `wednesdays_end`, `thursdays_start`, `thursdays_end`, `fridays_start`, `fridays_end`, `saturdays_start`, `saturdays_end`, `profile_pic`, `force_password_change`, `gender`) VALUES
('japwahl@gmail.com', '2024-01-09', 'portland', 'Jennifer', 'Polack', '15 Wallace Farms Lane', 'Fredericksburg', 'VA', '22406', '5402959700', 'work', '', '', '1970-05-01', 'japwahl@gmail.com', 'S', '1', '1', '1', 'Brianna', '5404550567', 'Sister', 'Evening', 'text', '', '', '', '', '', '', '', 'trainer', 'Active', '0', '', '', '', '', '$2y$10$V044vIzY3Y/soykziVZM5O.xXpK4MVEgCmSTMBBMskiYc9s.VJK8y', '16:00', '21:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Female'),
('justin.raze.50@gmail.com', '2023-12-12', 'portland', 'Justin', 'Raze', '1701 College Avenue, 1920', 'Fredericksburg', 'VA', '22401', '7035699027', 'cellphone', '', '', '2001-07-27', 'justin.raze.50@gmail.com', 'S', '1', '1', '1', 'Karen Jean Raze', '7035699027', 'Mother', 'evenings', 'text', '', '', '', '', '', '', '', 'volunteer', 'Active', 'True', '', '', '', '', '$2y$10$ft5wXJUt0reTdbjt5SOp.OjEXdxCEv7mFRN.M0Q.7w1H5zJqslz7S', '04:00', '12:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Male'),
('polack@umw.edu', '2024-01-09', 'portland', 'Jennifer', 'Polack', '15 Wallace Farms Lane', 'Fredericksburg', 'VA', '22406', '5402979700', 'cellphone', '', '', '1970-05-01', 'polack@umw.edu', 'S', '1', '1', '1', 'Jenni', '5404550789', 'Daughter', 'Evening', 'text', '', '', '', '', '', '', '', 'volunteer', 'Active', '0', '', '', '', '', '$2y$10$v0eAbE4wUot6auFR3Nkdv.AFMiOk0oQ64OsLVrfqklUMKUDx0il3y', '15:00', '18:00', '', '', '', '', '', '', '', '', '', '', '01:00', '17:00', '', 0, 'Male'),
('scarlet@gmail.com', '2023-12-12', 'portland', 'Karen', 'Scarlet', '6210 Merryvale ct', 'Springfield', 'OR', '22152', '5554446666', 'home', '', '', '1978-07-25', 'scarlet@gmail.com', 'S', '1', '1', '1', 'test', '5000000000', 'test', 'Evenings', 'phone', '', '', '', '', '', '', '', 'admin', 'Active', '0', '', '', '', '', '$2y$10$4Z9ij3hM4W1ejZkZGtUDtuJdhaQK3BIJemYt/zUlHXO36OsQEBdPW', '11:00', '17:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Female'),
('testadmin@test.com', '2023-12-12', 'portland', 'testA', 'testAdmin', '1701 College Avenue, 1920', 'Fredericksburg', 'VA', '22401', '7034866673', 'home', '', '', '1983-02-05', 'testadmin@test.com', 'S', '1', '1', '1', 'test', '6000000000', 'friend', 'days', 'phone', '', '', '', '', '', '', '', 'admin', 'Active', '1', '', '', '', '', '$2y$10$tcmQE.he.BDzKuJ.6YuLt.hsUsOC7vCPE4zjP5sus5qdwyrVpFnOC', '16:00', '18:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Male'),
('testvol@test.com', NULL, NULL, 'Alex', 'Keehan', NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, 'alex.k@gmail.com', NULL, NULL, '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Volunteer', 'Active', 'True', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, ''),
('vmsroot', 'N/A', 'portland', 'vmsroot', '', 'N/A', 'N/A', 'VA', 'N/A', '', 'N/A', 'N/A', 'N/A', 'N/A', 'vmsroot', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', '0', 'N/A', 'N/A', 'N/A', 'N/A', '$2y$10$8GyfE491ix9MD.1UVOgsZ.3PbX0w/AP8xGUNAgORt8nvvXprfHFeC', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `dbtrainingperiods`
--

CREATE TABLE `dbtrainingperiods` (
  `id` int(11) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `year` varchar(50) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbtrainingperiods`
--

INSERT INTO `dbtrainingperiods` (`id`, `semester`, `year`, `startdate`, `enddate`) VALUES
(1, 'Spring', '2025', '2025-01-01', '2025-02-28'),
(2, 'Spring', '2029', '2029-01-01', '2029-02-28'),
(3, 'Summer', '2028', '2028-05-01', '2028-06-30'),
(4, 'Spring', '2032', '2032-01-01', '2032-02-28'),
(5, 'Summer', '2030', '2030-05-01', '2030-06-30'),
(6, 'Spring', '2026', '2026-01-01', '2026-02-28'),
(7, 'Spring', '2026', '2026-01-01', '2026-02-28'),
(8, 'Spring', '2026', '2026-01-01', '2026-02-28'),
(9, 'Fall', '2030', '2030-09-01', '2030-10-31'),
(10, 'Summer', '2032', '2032-05-01', '2032-06-30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbcourses`
--
ALTER TABLE `dbcourses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FkPeriodID` (`periodId`);

--
-- Indexes for table `dbeventmedia`
--
ALTER TABLE `dbeventmedia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKeventID2` (`eventID`);

--
-- Indexes for table `dbevents`
--
ALTER TABLE `dbevents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbeventvolunteers`
--
ALTER TABLE `dbeventvolunteers`
  ADD KEY `FKeventID` (`eventID`),
  ADD KEY `FKpersonID` (`userID`);

--
-- Indexes for table `dbmessages`
--
ALTER TABLE `dbmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbpersons`
--
ALTER TABLE `dbpersons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbtrainingperiods`
--
ALTER TABLE `dbtrainingperiods`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbcourses`
--
ALTER TABLE `dbcourses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `dbeventmedia`
--
ALTER TABLE `dbeventmedia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbevents`
--
ALTER TABLE `dbevents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `dbmessages`
--
ALTER TABLE `dbmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `dbtrainingperiods`
--
ALTER TABLE `dbtrainingperiods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbcourses`
--
ALTER TABLE `dbcourses`
  ADD CONSTRAINT `FkPeriodID` FOREIGN KEY (`periodId`) REFERENCES `dbtrainingperiods` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbeventmedia`
--
ALTER TABLE `dbeventmedia`
  ADD CONSTRAINT `FKeventID2` FOREIGN KEY (`eventID`) REFERENCES `dbevents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbeventvolunteers`
--
ALTER TABLE `dbeventvolunteers`
  ADD CONSTRAINT `FKeventID` FOREIGN KEY (`eventID`) REFERENCES `dbevents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKpersonID` FOREIGN KEY (`userID`) REFERENCES `dbpersons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
