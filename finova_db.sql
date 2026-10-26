-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 04:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finova_db`
--
CREATE DATABASE IF NOT EXISTS `finova_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `finova_db`;

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

DROP TABLE IF EXISTS `about`;
CREATE TABLE `about` (
  `student_id` char(9) NOT NULL,
  `name` varchar(20) NOT NULL,
  `part1_contribution` text NOT NULL,
  `part2_contribution` text NOT NULL,
  `original_quote` text NOT NULL,
  `translated_quote` text NOT NULL,
  `fun_fact` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`student_id`, `name`, `part1_contribution`, `part2_contribution`, `original_quote`, `translated_quote`, `fun_fact`) VALUES
('106154958', 'Ada Yong', 'Shared responsibility: CSS File, About Page and individual responsibility: Apply page.', 'Shared responsibility: CSS File and individual responsibility: Apply page, Process EOI, EOI Table.', '\"船到桥头自然直\" - Chinese', '\"The boat will go straight when it reaches the bridge.\"', 'Has a favourite spoon'),
('106505815', 'Juliette Grove', 'Shared responsibility: CSS File, About Page and individual responsibility: About page.', 'Shared responsibility: CSS File and individual responsibility: About page, About table.', '\"Tutto scorre.\" – Italian', '\"Everything flows.\"', 'Has been to 10 countries'),
('106507921', 'Andrea McPadden', 'Shared responsibility: CSS File, About Page and individual responsibility: Jobs page.', 'Shared responsibility: CSS File and individual responsibility: Jobs page, Jobs Table, Manage page', '\"Цена — это то, что вы платите. Ценность — это то, что вы получаете.\" - Russian', '\"Price is what you pay. Value is what you get.\"', 'Has read 60 books this year.'),
('106843502', 'Alisla Foley', 'Shared responsibility: CSS File, About Page and individual responsibility: Index/Home Page, Header (Navigation bar), and Footer.', 'Shared responsibility: CSS File and individual responsibility: Index/Home Page, Header (Navigation bar), Footer, Login page, Profile page, User Table.', '\"L\'argent coûte souvent trop cher.\" - French', '\"Money often costs too much.\"', 'Her favourite colour is red.');

-- --------------------------------------------------------

--
-- Table structure for table `eoi`
--

DROP TABLE IF EXISTS `eoi`;
CREATE TABLE `eoi` (
  `eoi_number` int(11) NOT NULL,
  `job_ref` char(6) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('female','male','nonbinary','prefernot') NOT NULL,
  `street` varchar(40) NOT NULL,
  `suburb` varchar(40) NOT NULL,
  `state` enum('vic','nsw','qld','nt','wa','sa','tas','act') NOT NULL,
  `postcode` char(4) NOT NULL,
  `email` varchar(254) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `skills` text NOT NULL,
  `other_skills` text DEFAULT NULL,
  `status` enum('new','current','final') NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eoi`
--

INSERT INTO `eoi` (`eoi_number`, `job_ref`, `first_name`, `last_name`, `dob`, `gender`, `street`, `suburb`, `state`, `postcode`, `email`, `phone`, `skills`, `other_skills`, `status`) VALUES
(1, 'DAT551', 'Liam', 'Miller', '2000-03-12', 'male', '15 Bourke St', 'Melbourne', 'vic', '3000', 'liam.miller@example.com', '0412345678', 'sql_database, problem_solving', 'Oracle, PostgreSQL', 'new'),
(2, 'DAT551', 'Olivia', 'Davis', '1992-08-19', 'prefernot', '42 George St', 'Sydney', 'nsw', '2000', 'olivia.davis@example.com', '0423456789', 'web_development, sql_database', 'Vue.js, HTML', 'current'),
(3, 'DEV042', 'Noah', 'Wilson', '1990-11-05', 'male', '108 St Georges Terrace', 'Perth', 'wa', '6000', 'noah.wilson@example.com', '0434567890', 'cloud_cicd, cybersecurity, problem_solving', 'AWS, Docker', 'new'),
(4, 'DEV042', 'Emma', 'Taylor', '1995-07-22', 'female', '73 Queen St', 'Brisbane', 'qld', '4000', 'emma.taylor@example.com', '0445678901', 'problem_solving', 'Agile, Scrum', 'final'),
(5, 'SEC918', 'Lucas', 'Anderson', '2002-01-14', 'male', '50 King William St', 'Adelaide', 'sa', '5000', 'lucas.anderson@example.com', '0456789012', 'cybersecurity, cloud_cicd', 'Network Security, Pen Testing', 'new'),
(6, 'SEC918', 'Ava', 'Thomas', '1994-09-30', 'female', '22 Smith St', 'Darwin', 'nt', '0800', 'ava.thomas@example.com', '0467890123', 'web_development, cybersecurity', 'Angular, TypeScript', 'current'),
(7, 'DAT551', 'Mason', 'Jackson', '1991-05-18', 'male', '99 Collins St', 'Hobart', 'tas', '7000', 'mason.jackson@example.com', '0478901234', 'sql_database', 'MongoDB, MySQL', 'new'),
(8, 'DEV042', 'Sophia', 'White', '1997-12-03', 'nonbinary', '14 Northbourne Ave', 'Canberra', 'act', '2600', 'sophia.white@example.com', '0489012345', 'problem_solving, web_development, cloud_cicd', 'Data Structures, Python', 'new'),
(9, 'SEC918', 'Logan', 'Harris', '2005-06-25', 'male', '81 Elizabeth St', 'Launceston', 'tas', '7250', 'logan.harris@example.com', '0490123456', 'cybersecurity', 'CISSP, Firewall Mgmt', 'current'),
(10, 'DAT551', 'Mia', 'Martin', '1998-02-11', 'female', '35 St Kilda Rd', 'Melbourne', 'vic', '3004', 'mia.martin@example.com', '0401234567', 'web_development, sql_database', 'React, Node.js', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `Job_Code` char(6) NOT NULL,
  `Job_Title` text NOT NULL,
  `Salary` int(10) NOT NULL,
  `Description` longtext NOT NULL,
  `Min_Experience` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`Job_Code`, `Job_Title`, `Salary`, `Description`, `Min_Experience`) VALUES
('DAT551', 'Financial Data Analyst', 110000, 'Translate customer transaction data and financial trends into actionable business insights and drive product strategies, fraud prevention models, and user engagement', 2),
('DEV042', 'Senior Full Stack Developer', 155000, 'Lead the engineering of our core web applications, ensuring high scalability, performance, and security across all platforms', 5),
('SEC918', 'Cybersecurity Analyst', 130000, 'Continuously monitoring threats, assessing vulnerabilities, and enforcing security compliance across all operational systems', 3);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$CHAR8JW3T3X03JSbUg7ChOgwAbQbA5p63xvmx6yGBiAqRFgGTZO02');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE `user_profile` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(20) DEFAULT NULL,
  `last_name` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('female','male','nonbinary','prefernot') DEFAULT NULL,
  `street` varchar(40) DEFAULT NULL,
  `suburb` varchar(40) DEFAULT NULL,
  `state` enum('vic','nsw','qld','nt','wa','sa','tas','act') DEFAULT NULL,
  `postcode` char(4) DEFAULT NULL,
  `email` varchar(254) DEFAULT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `other_skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `eoi`
--
ALTER TABLE `eoi`
  ADD PRIMARY KEY (`eoi_number`),
  ADD KEY `job_ref` (`job_ref`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`Job_Code`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eoi`
--
ALTER TABLE `eoi`
  MODIFY `eoi_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eoi`
--
ALTER TABLE `eoi`
  ADD CONSTRAINT `eoi_ibfk_1` FOREIGN KEY (`job_ref`) REFERENCES `jobs` (`Job_Code`) ON UPDATE CASCADE;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `fk_profile_user` FOREIGN KEY (`id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
