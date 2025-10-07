-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 02:46 PM
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
-- Database: `jobboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `other_docs` varchar(255) DEFAULT NULL,
  `status` enum('pending','interview','hired','rejected') DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_letter_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`) VALUES
(1, 'TechNova Solutions'),
(2, 'GreenLeaf Corp'),
(3, 'XYZ Enterprise'),
(4, 'EduPro Institute'),
(5, 'HealthFirst Medical');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `required_employees` int(11) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `employment_duration` varchar(100) DEFAULT NULL,
  `preferred_sex` varchar(10) DEFAULT NULL,
  `sector_of_vacancy` varchar(100) DEFAULT NULL,
  `qualification` text DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `employer` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `company_id`, `title`, `required_employees`, `salary`, `employment_duration`, `preferred_sex`, `sector_of_vacancy`, `qualification`, `job_description`, `employer`, `location`) VALUES
(45, 1, 'Frontend Developer', 1, 18000.00, 'June 1 - Dec 1', 'Male', 'Yes', 'BS in Computer Science or related field', 'Develop responsive web interfaces using HTML, CSS, JavaScript', 'TechNova Solutions', 'Makati City'),
(46, 1, 'Backend Developer', 1, 20000.00, 'July 15 - Dec 15', 'Female', 'Yes', 'BS in Information Technology or related field', 'Build and maintain server-side applications and APIs', 'TechNova Solutions', 'Taguig City'),
(47, 1, 'UI/UX Designer', 1, 17000.00, 'Aug 1 - Nov 30', 'Any', 'Yes', 'BS in Graphic Design or related field', 'Design user-friendly interfaces and conduct usability testing', 'TechNova Solutions', 'Quezon City'),
(48, 2, 'Environmental Analyst', 1, 16500.00, 'June 10 - Nov 10', 'Female', 'Yes', 'BS in Environmental Science', 'Analyze data related to environmental impact', 'GreenLeaf Corp', 'Iloilo City'),
(49, 2, 'Sustainability Manager', 1, 22000.00, 'July 5 - Jan 5', 'Any', 'Yes', 'BS in Environmental Management or equivalent', 'Oversee sustainability programs and compliance', 'GreenLeaf Corp', 'Davao City'),
(50, 2, 'Renewable Energy Technician', 1, 18000.00, 'May 25 - Oct 25', 'Male', 'Yes', 'BS in Electrical Engineering or Renewable Energy', 'Install and maintain renewable energy systems', 'GreenLeaf Corp', 'Cebu City'),
(51, 3, 'IT Specialist', 1, 19000.00, 'May 20 - Nov 20', 'Any', 'Yes', 'BS in Information Technology or Computer Engineering', 'Provide technical support and system maintenance', 'XYZ Enterprise', 'Pasig City'),
(52, 3, 'Data Analyst', 1, 21000.00, 'June 15 - Dec 15', 'Female', 'Yes', 'BS in Statistics, Math or related field', 'Interpret data trends and generate reports', 'XYZ Enterprise', 'Manila City'),
(53, 4, 'Curriculum Developer', 1, 16000.00, 'May 20 - Sept 20', 'Female', 'Yes', 'BS in Education or Curriculum Studies', 'Develop and revise academic course materials', 'EduPro Institute', 'Baguio City'),
(54, 4, 'Online Learning Coordinator', 1, 17500.00, 'June 10 - Nov 10', 'Any', 'Yes', 'BS in Education Technology or related field', 'Coordinate online learning platforms and support', 'EduPro Institute', 'Cavite City'),
(55, 4, 'Academic Advisor', 1, 18500.00, 'Aug 1 - Dec 1', 'Male', 'Yes', 'BS in Psychology or Education', 'Guide students in academic planning and goals', 'EduPro Institute', 'Bulacan'),
(56, 5, 'Registered Nurse', 1, 22000.00, 'June 1 - Dec 1', 'Female', 'Yes', 'BS in Nursing and licensed RN', 'Provide nursing care and support to patients', 'HealthFirst Medical', 'Laguna'),
(57, 5, 'Medical Laboratory Technician', 1, 19500.00, 'July 1 - Nov 30', 'Male', 'Yes', 'BS in Medical Technology or Laboratory Science', 'Conduct laboratory tests and procedures', 'HealthFirst Medical', 'Batangas'),
(58, 5, 'Health Information Specialist', 1, 18000.00, 'May 15 - October 15', 'Any', 'Yes', 'BS in Health Information Management', 'Manage and organize patient health records', 'HealthFirst Medical', 'Pampanga');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','jobseeker') DEFAULT 'jobseeker',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role`, `profile_picture`) VALUES
(1, 'edwinc', '$2y$10$kR.ANn0IlBffAH347KpMEeD3ZOQ59IgMPeYFgmw/qPXO0yRNoufxW', 'Edwin Christian E. Bacay', 'edwinchristianedjanbacay143@gmail.com', 'jobseeker', '680ccd32100db-edwin.jpg'),
(2, 'faithv', '$2y$10$awFkzsXvXfOiPKaiM59jn.lB1qpRw4Qx.DwM2M4v7dEv5Gt7sEzJO', 'Faith M. Valencia', 'valenciafaithmaramot05@gmail.com', 'jobseeker', '680cca4ecf2d5-faith.jpg'),
(4, 'admin', '$2y$10$2jHuqmRp9H0g8i09uEAZmufxSFWY9oAujmzpRRIuCnBWB4Llx31cK', 'Charles B. Xavier', 'christianbacay143@gmail.com', 'admin', '680ccb2f040f6-admin profile.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`);

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
