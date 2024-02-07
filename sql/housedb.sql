-- phpMyAdmin SQL Dump
-- version 5.1.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 07, 2024 at 01:14 PM
-- Server version: 5.7.44-48-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbyrqpvdjpzamq`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbCourses`
--

CREATE TABLE `dbCourses` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `abbrevName` text NOT NULL,
  `staffId` text NOT NULL,
  `eventId` text NOT NULL,
  `date` char(10) NOT NULL,
  `startTime` char(5) NOT NULL,
  `endTime` char(5) NOT NULL,
  `description` text NOT NULL,
  `location` text NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dbCourses`
--

INSERT INTO `dbCourses` (`id`, `name`, `abbrevName`, `staffId`, `eventId`, `date`, `startTime`, `endTime`, `description`, `location`, `capacity`) VALUES
(78, 'History', 'History', 'Empower', '14', '2024-02-21', '17:30', '20:30', 'Great course!', 'Empowerhouse HQ', 12),
(79, 'Listening/Boundaries', 'L&B', 't1', '14', '2024-02-22', '17:30', '20:30', 'd1', 'l1', 11),
(80, 'DV101', 'DV 101', 't1', '14', '2024-02-23', '17:30', '21:30', 'd1', 'l1', 11),
(81, 'Legal1', 'Legal 1', 't1', '14', '2024-02-24', '17:30', '21:30', 'd1', 'l1', 11),
(82, 'Legal2', 'Legal 2', 't1', '14', '2024-02-25', '17:30', '21:30', 'd1', 'l1', 11),
(83, 'Diversity-Latinx/LGBTQ', 'Diversity', 't1', '14', '2024-02-25', '17:30', '21:30', 'd1', 'l1', 11),
(84, 'Court/Legal', 'Court/Legal', 't1', '14', '2024-02-26', '17:30', '21:30', 'd1', 'l1', 11),
(85, 'Shelter', 'Shelter', 't1', '14', '2024-02-28', '17:30', '21:30', 'd1', 'l1', 11),
(86, 'RCASA/MenFS', 'RCASA/ MenFS', 't1', '14', '2024-03-01', '17:30', '21:30', 'd1', 'l1', 12),
(87, 'MH/SU/SupGr/YthPr', 'MH/SU/SupGr', 't1', '14', '2024-03-02', '17:30', '21:30', 'd1', 'l1', 11),
(88, 'Hotline,CI,SP,DA', 'Hotline+', 't1', '14', '2024-02-28', '17:30', '21:30', 'd1', 'l1', 11),
(89, 'Graduation', 'Graduation', 't1', '14', '2024-03-04', '17:30', '21:30', 'd1', 'l1', 11),
(90, 'History', 'History', 'Bob', '15', '2024-01-25', '12:00', '13:00', 'History', 'Fredericksburg', 10);

-- --------------------------------------------------------

--
-- Table structure for table `dbEventMedia`
--

CREATE TABLE `dbEventMedia` (
  `id` int(11) NOT NULL,
  `eventID` int(11) NOT NULL,
  `url` text NOT NULL,
  `type` text NOT NULL,
  `format` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `dbEvents`
--

CREATE TABLE `dbEvents` (
  `id` int(11) NOT NULL,
  `eventname` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dbEvents`
--

INSERT INTO `dbEvents` (`id`, `eventname`) VALUES
(14, 'Late February Training'),
(15, 'Spring 2024');

-- --------------------------------------------------------

--
-- Table structure for table `dbEventVolunteers`
--

CREATE TABLE `dbEventVolunteers` (
  `eventID` int(11) NOT NULL,
  `userID` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `dbMessages`
--

CREATE TABLE `dbMessages` (
  `id` int(11) NOT NULL,
  `senderID` varchar(256) NOT NULL,
  `recipientID` varchar(256) NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` text NOT NULL,
  `time` varchar(16) NOT NULL,
  `wasRead` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dbMessages`
--

INSERT INTO `dbMessages` (`id`, `senderID`, `recipientID`, `title`, `body`, `time`, `wasRead`) VALUES
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
-- Table structure for table `dbPersons`
--

CREATE TABLE `dbPersons` (
  `id` varchar(256) CHARACTER SET utf8mb4 NOT NULL,
  `start_date` text,
  `venue` text,
  `first_name` text NOT NULL,
  `last_name` text,
  `address` text,
  `city` text,
  `state` varchar(2) DEFAULT NULL,
  `zip` text,
  `phone1` varchar(12) NOT NULL,
  `phone1type` text,
  `phone2` varchar(12) DEFAULT NULL,
  `phone2type` text,
  `birthday` text,
  `email` text,
  `shirt_size` varchar(3) DEFAULT NULL,
  `computer` varchar(3) DEFAULT NULL,
  `camera` varchar(3) NOT NULL,
  `transportation` varchar(3) NOT NULL,
  `contact_name` text NOT NULL,
  `contact_num` varchar(12) NOT NULL,
  `relation` text NOT NULL,
  `contact_time` text NOT NULL,
  `cMethod` text,
  `position` text,
  `credithours` text,
  `howdidyouhear` text,
  `commitment` text,
  `motivation` text,
  `specialties` text,
  `convictions` text,
  `type` text,
  `status` text,
  `availability` text,
  `schedule` text,
  `hours` text,
  `notes` text,
  `password` text,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dbPersons`
--

INSERT INTO `dbPersons` (`id`, `start_date`, `venue`, `first_name`, `last_name`, `address`, `city`, `state`, `zip`, `phone1`, `phone1type`, `phone2`, `phone2type`, `birthday`, `email`, `shirt_size`, `computer`, `camera`, `transportation`, `contact_name`, `contact_num`, `relation`, `contact_time`, `cMethod`, `position`, `credithours`, `howdidyouhear`, `commitment`, `motivation`, `specialties`, `convictions`, `type`, `status`, `availability`, `schedule`, `hours`, `notes`, `password`, `sundays_start`, `sundays_end`, `mondays_start`, `mondays_end`, `tuesdays_start`, `tuesdays_end`, `wednesdays_start`, `wednesdays_end`, `thursdays_start`, `thursdays_end`, `fridays_start`, `fridays_end`, `saturdays_start`, `saturdays_end`, `profile_pic`, `force_password_change`, `gender`) VALUES
('japwahl@gmail.com', '2024-01-09', 'portland', 'Jennifer', 'Polack', '15 Wallace Farms Lane', 'Fredericksburg', 'VA', '22406', '5402959700', 'work', '', '', '1970-05-01', 'japwahl@gmail.com', 'S', '1', '1', '1', 'Brianna', '5404550567', 'Sister', 'Evening', 'text', '', '', '', '', '', '', '', 'trainer', 'Active', '', '', '', '', '$2y$10$V044vIzY3Y/soykziVZM5O.xXpK4MVEgCmSTMBBMskiYc9s.VJK8y', '16:00', '21:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Female'),
('justin.raze.50@gmail.com', '2023-12-12', 'portland', 'Justin', 'Raze', '1701 College Avenue, 1920', 'Fredericksburg', 'VA', '22401', '7035699027', 'cellphone', '', '', '2001-07-27', 'justin.raze.50@gmail.com', 'S', '1', '1', '1', 'Karen Jean Raze', '7035699027', 'Mother', 'evenings', 'text', '', '', '', '', '', '', '', 'volunteer', 'Active', '', '', '', '', '$2y$10$ft5wXJUt0reTdbjt5SOp.OjEXdxCEv7mFRN.M0Q.7w1H5zJqslz7S', '04:00', '12:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Male'),
('polack@umw.edu', '2024-01-09', 'portland', 'Jennifer', 'Polack', '15 Wallace Farms Lane', 'Fredericksburg', 'VA', '22406', '5402979700', 'cellphone', '', '', '1970-05-01', 'polack@umw.edu', 'S', '1', '1', '1', 'Jenni', '5404550789', 'Daughter', 'Evening', 'text', '', '', '', '', '', '', '', 'volunteer', 'Active', '', '', '', '', '$2y$10$v0eAbE4wUot6auFR3Nkdv.AFMiOk0oQ64OsLVrfqklUMKUDx0il3y', '15:00', '18:00', '', '', '', '', '', '', '', '', '', '', '01:00', '17:00', '', 0, 'Male'),
('scarlet@gmail.com', '2023-12-12', 'portland', 'Karen', 'Scarlet', '6210 Merryvale ct', 'Springfield', 'OR', '22152', '5554446666', 'home', '', '', '1978-07-25', 'scarlet@gmail.com', 'S', '1', '1', '1', 'test', '5000000000', 'test', 'Evenings', 'phone', '', '', '', '', '', '', '', 'admin', 'Active', '', '', '', '', '$2y$10$4Z9ij3hM4W1ejZkZGtUDtuJdhaQK3BIJemYt/zUlHXO36OsQEBdPW', '11:00', '17:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Female'),
('testadmin@test.com', '2023-12-12', 'portland', 'testA', 'testAdmin', '1701 College Avenue, 1920', 'Fredericksburg', 'VA', '22401', '7034866673', 'home', '', '', '1983-02-05', 'testadmin@test.com', 'S', '1', '1', '1', 'test', '6000000000', 'friend', 'days', 'phone', '', '', '', '', '', '', '', 'admin', 'Active', '', '', '', '', '$2y$10$tcmQE.he.BDzKuJ.6YuLt.hsUsOC7vCPE4zjP5sus5qdwyrVpFnOC', '16:00', '18:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Male'),
('vmsroot', 'N/A', 'portland', 'vmsroot', '', 'N/A', 'N/A', 'VA', 'N/A', '', 'N/A', 'N/A', 'N/A', 'N/A', 'vmsroot', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '$2y$10$8GyfE491ix9MD.1UVOgsZ.3PbX0w/AP8xGUNAgORt8nvvXprfHFeC', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbCourses`
--
ALTER TABLE `dbCourses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbEventMedia`
--
ALTER TABLE `dbEventMedia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKeventID2` (`eventID`);

--
-- Indexes for table `dbEvents`
--
ALTER TABLE `dbEvents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbEventVolunteers`
--
ALTER TABLE `dbEventVolunteers`
  ADD KEY `FKeventID` (`eventID`),
  ADD KEY `FKpersonID` (`userID`);

--
-- Indexes for table `dbMessages`
--
ALTER TABLE `dbMessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbPersons`
--
ALTER TABLE `dbPersons`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbCourses`
--
ALTER TABLE `dbCourses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `dbEventMedia`
--
ALTER TABLE `dbEventMedia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbEvents`
--
ALTER TABLE `dbEvents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `dbMessages`
--
ALTER TABLE `dbMessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbEventMedia`
--
ALTER TABLE `dbEventMedia`
  ADD CONSTRAINT `FKeventID2` FOREIGN KEY (`eventID`) REFERENCES `dbEvents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbEventVolunteers`
--
ALTER TABLE `dbEventVolunteers`
  ADD CONSTRAINT `FKeventID` FOREIGN KEY (`eventID`) REFERENCES `dbEvents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKpersonID` FOREIGN KEY (`userID`) REFERENCES `dbPersons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
