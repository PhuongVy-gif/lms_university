-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2025 at 08:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_university`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `update_file` varchar(255) DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `max_points` decimal(5,2) DEFAULT 100.00,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `course_id`, `title`, `description`, `update_file`, `due_date`, `max_points`, `created_by`, `created_at`) VALUES
(47, 15, 'Java Classes', 'Create a Java class to represent a Book with appropriate attributes and methods.', NULL, '2025-08-10 23:59:00', 10.00, 17, '2025-08-11 07:50:13'),
(49, 16, 'Linked List Implementation', 'Implement a singly linked list with insert, delete, and search methods.', NULL, '2025-09-18 23:59:59', 100.00, 18, '2025-08-11 07:50:13'),
(50, 16, 'Binary Tree Traversals', 'Implement in-order, pre-order, and post-order traversals of a binary tree.', NULL, '2025-09-25 23:59:59', 100.00, 18, '2025-08-11 07:50:13'),
(55, 19, 'Requirements Document', 'Write a software requirements specification for a sample project.', NULL, '2025-09-16 23:59:00', 10.00, 17, '2025-08-11 07:50:13'),
(59, 19, 'Design Patterns', 'Implement three design patterns in a sample application.', 'assignment_1755079770_3367.pdf', '2025-08-14 07:10:00', 10.00, 17, '2025-08-13 10:09:30'),
(61, 15, 'Java Classes', 'Implement three design patterns in a sample application.', 'assignment_1755152947_3861.pdf', '2025-08-22 18:30:00', 10.00, 17, '2025-08-14 06:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission_text` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `assignment_id`, `student_id`, `submission_text`, `file_path`, `submitted_at`, `grade`, `feedback`, `graded_by`, `graded_at`) VALUES
(10, 55, 16, NULL, '1755106513_AI-0214225348.pdf', '2025-08-13 17:35:13', 8.00, 'Good', 17, '2025-08-13 12:38:12'),
(11, 59, 16, NULL, '1755107006_AI-0214225348.pdf', '2025-08-13 17:43:26', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL,
  `instructor_id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `credits` int(11) DEFAULT 3,
  `semester` varchar(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `max_students` int(11) DEFAULT 50,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `images`, `instructor_id`, `course_code`, `credits`, `semester`, `year`, `max_students`, `status`, `created_at`) VALUES
(5, 'Python', 'Python', 'course_6895760e57f716.72868951.jpg', 18, '244', 3, 'Fall', 2025, 50, 'active', '2025-08-08 03:59:10'),
(6, 'PHP', 'PHP', 'course_68974fd6da8c28.47672371.jpg', 17, '444', 5, 'Fall', 2025, 50, 'active', '2025-08-09 13:40:38'),
(7, 'Web Development Fundamentals', 'Web Development Fundamentals', 'course_68975652f2e092.44346749.jpg', 17, '011', 4, 'Summer', 2025, 50, 'active', '2025-08-09 14:08:18'),
(15, 'Object-Oriented Programming', 'Learn OOP concepts with Java', 'course_689996dbdaa071.36428244.jpg', 17, 'CS102', 3, 'Spring', 2025, 50, 'active', '2025-08-11 06:29:23'),
(16, 'Data Structures', 'Covers arrays, linked lists, stacks, queues, and trees', 'course_689996a64d0415.14783658.jpg', 18, 'CS201', 4, 'Fall', 2025, 55, 'active', '2025-08-11 06:29:23'),
(18, 'Web Development', 'HTML, CSS, JavaScript, and backend basics', 'course_68999648682865.13759867.jpg', 17, 'CS301', 4, 'Fall', 2025, 45, 'active', '2025-08-11 06:29:23'),
(19, 'Software Engineering', 'Software development lifecycle, design patterns', 'course_689996050e8b03.54268606.jpg', 17, 'CS302', 3, 'Spring', 2025, 40, 'inactive', '2025-08-11 06:29:23'),
(26, 'Introduction to Programming', 'Learn the basics of programming with Python.', 'course_python.jpg', 1, 'CS101A', 3, 'Fall', 2025, 50, 'active', '2025-08-13 15:34:36'),
(27, 'Database Systems', 'Understand relational databases and SQL.', 'course_database.jpg', 1, 'CS202A', 4, 'Spring', 2025, 40, 'active', '2025-08-13 15:34:36'),
(28, 'Web Development', 'Build modern websites using HTML, CSS, JavaScript, and PHP.', 'course_689cb23c732629.06646655.jpg', 17, 'CS303A', 3, 'Fall', 2025, 45, 'active', '2025-08-13 15:34:36'),
(29, 'Data Structures', 'Learn about arrays, linked lists, stacks, queues, and trees.', 'course_689cb1dcc8ac65.38411310.jpg', 17, 'CS204A', 3, 'Summer', 2025, 35, 'active', '2025-08-13 15:34:36'),
(30, 'Software Engineering', 'Study the software development lifecycle and project management.', 'course_689cb13ee9c898.41256972.jpg', 17, 'CS305A', 3, 'Fall', 2025, 50, 'active', '2025-08-13 15:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('enrolled','completed','dropped') DEFAULT 'enrolled',
  `final_grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `enrollment_date`, `status`, `final_grade`) VALUES
(5, 8, 5, '2025-08-08 03:59:20', 'enrolled', NULL),
(7, 8, 6, '2025-08-09 13:40:46', 'enrolled', NULL),
(10, 16, 7, '2025-08-11 06:03:55', 'enrolled', NULL),
(11, 16, 6, '2025-08-11 07:19:29', 'enrolled', NULL),
(12, 16, 18, '2025-08-11 07:21:16', 'enrolled', NULL),
(13, 16, 19, '2025-08-11 08:09:46', 'enrolled', NULL),
(14, 16, 15, '2025-08-11 08:09:49', 'enrolled', NULL),
(16, 16, 29, '2025-08-13 15:42:26', 'enrolled', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('student','instructor','admin') NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `role`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@university.edu', 'admin', 'System', 'Administrator', 'admin', NULL, '2025-08-06 16:52:05', '2025-08-06 16:52:05'),
(3, 'student1', 'student1@university.edu', '12345', 'Jane', 'Doe', 'student', NULL, '2025-08-06 16:52:05', '2025-08-06 16:52:05'),
(8, '', 'admin@example.com', '$2y$10$LnwCOkmy1mmUDM6nazV5L.ffnOYmmBDQF1fyEq742Ly6KizoJ315u', '', '', 'admin', NULL, '2025-08-07 08:02:38', '2025-08-07 08:04:35'),
(16, 'Student2', 'student2@university.edu', '$2y$10$YLUtxvedsrxa/XOXGmyB6uJlfrVj7y./VOEUaYdxGSS.HPX98AT2W', 'Student', '2', 'student', NULL, '2025-08-11 06:03:39', '2025-08-11 06:03:39'),
(17, 'instructor1', 'instructor1@example.com', '$2y$10$kN5yIG0nQLrNxPo3htUd0ei3Qlm/O2TyIGxqcEpRy4GN2hW/dwkiu', 'Alice', 'Johnson', 'instructor', 'profile3.jpg', '2025-08-11 06:23:39', '2025-08-11 08:07:43'),
(18, 'instructor2', 'instructor2@example.com', '$2y$10$vE.1NgY.01ZhKQp6I9UkzeZ2Osrfud43LHyS27jDaOY6d9B6KvoEW', 'Bob', 'Williams', 'instructor', 'profile4.jpg', '2025-08-11 06:23:39', '2025-08-11 08:07:55'),
(19, 'admin1', 'admin1@university.edu', '123456', 'Alice', 'Nguyen', 'admin', NULL, '2025-08-14 06:33:51', '2025-08-14 06:33:51'),
(22, 'instructor3', 'instructor3@university.edu', '123456', 'John', 'Doe', 'instructor', NULL, '2025-08-14 06:34:51', '2025-08-14 06:34:51'),
(23, 'instructor4', 'instructor4@university.edu', '123456', 'David', 'Tran', 'instructor', NULL, '2025-08-14 06:34:51', '2025-08-14 06:34:51'),
(24, 'student6', 'student6@university.edu', '123456', 'Linh', 'Pham', 'student', NULL, '2025-08-14 06:34:51', '2025-08-14 06:34:51'),
(25, 'student7', 'student7@university.edu', '123456', 'Minh', 'Le', 'student', NULL, '2025-08-14 06:34:51', '2025-08-14 06:34:51'),
(26, 'student8', 'student8@university.edu', '123456', 'Huy', 'Nguyen', 'student', NULL, '2025-08-14 06:34:51', '2025-08-14 06:34:51'),
(27, 'student4', 'student4@university.edu', '123456', 'Trang', 'Vo', 'student', NULL, '2025-08-14 06:34:51', '2025-08-14 06:34:51'),
(28, 'student5', 'student5@university.edu', '123456', 'Khanh', 'Bui', 'student', NULL, '2025-08-14 06:34:51', '2025-08-14 06:34:51'),
(29, 'student10', 'student10@university.edu', '123456', 'An', 'Nguyen', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(30, 'student11', 'student11@university.edu', '123456', 'Binh', 'Tran', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(31, 'student12', 'student12@university.edu', '123456', 'Chau', 'Pham', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(32, 'student13', 'student13@university.edu', '123456', 'Dung', 'Le', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(33, 'student14', 'student14@university.edu', '123456', 'Hanh', 'Ho', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(34, 'student15', 'student15@university.edu', '123456', 'Khoa', 'Vo', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(36, 'student17', 'student17@university.edu', '123456', 'My', 'Phan', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(37, 'student18', 'student18@university.edu', '123456', 'Nam', 'Dang', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(38, 'student19', 'student19@university.edu', '123456', 'Oanh', 'Ly', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(39, 'student20', 'student20@university.edu', '123456', 'Phat', 'Nguyen', 'student', NULL, '2025-08-14 06:35:48', '2025-08-14 06:35:48'),
(40, 'student21', 'student21@university.edu', '123456', 'Quang', 'Tran', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(41, 'student22', 'student22@university.edu', '123456', 'Rang', 'Nguyen', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(42, 'student23', 'student23@university.edu', '123456', 'Son', 'Pham', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(43, 'student24', 'student24@university.edu', '123456', 'Tuan', 'Le', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(44, 'student25', 'student25@university.edu', '123456', 'Uyen', 'Ho', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(45, 'student26', 'student26@university.edu', '123456', 'Vy', 'Vo', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(46, 'student27', 'student27@university.edu', '123456', 'Xuan', 'Bui', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(47, 'student28', 'student28@university.edu', '123456', 'Yen', 'Phan', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(48, 'student29', 'student29@university.edu', '123456', 'Zung', 'Dang', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(49, 'student30', 'student30@university.edu', '123456', 'An', 'Ly', 'student', NULL, '2025-08-14 06:36:22', '2025-08-14 06:36:22'),
(50, 'student31', 'student31@university.edu', '123456', 'Bao', 'Nguyen', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(51, 'student32', 'student32@university.edu', '123456', 'Cam', 'Tran', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(52, 'student33', 'student33@university.edu', '123456', 'Diep', 'Pham', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(53, 'student34', 'student34@university.edu', '123456', 'Em', 'Le', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(54, 'student35', 'student35@university.edu', '123456', 'Giang', 'Ho', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(55, 'student36', 'student36@university.edu', '123456', 'Hieu', 'Vo', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(56, 'student37', 'student37@university.edu', '123456', 'Khanh', 'Bui', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(57, 'student38', 'student38@university.edu', '123456', 'Lan', 'Phan', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(58, 'student39', 'student39@university.edu', '123456', 'Minh', 'Dang', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(59, 'student40', 'student40@university.edu', '123456', 'Nga', 'Ly', 'student', NULL, '2025-08-14 06:36:53', '2025-08-14 06:36:53'),
(60, 'student41', 'student41@university.edu', '123456', 'Phong', 'Nguyen', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(61, 'student42', 'student42@university.edu', '123456', 'Quyen', 'Tran', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(62, 'student43', 'student43@university.edu', '123456', 'Sang', 'Pham', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(63, 'student44', 'student44@university.edu', '123456', 'Thao', 'Le', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(64, 'student45', 'student45@university.edu', '123456', 'Uy', 'Ho', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(65, 'student46', 'student46@university.edu', '123456', 'Vinh', 'Vo', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(66, 'student47', 'student47@university.edu', '123456', 'Xuyen', 'Bui', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(67, 'student48', 'student48@university.edu', '123456', 'Y', 'Phan', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(68, 'student49', 'student49@university.edu', '123456', 'Zan', 'Dang', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(69, 'student50', 'student50@university.edu', '123456', 'Anh', 'Ly', 'student', NULL, '2025-08-14 06:37:25', '2025-08-14 06:37:25'),
(70, 'instructor10', 'instructor10@university.edu', '123456', 'Anh', 'Tran', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(71, 'instructor11', 'instructor11@university.edu', '123456', 'Binh', 'Nguyen', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(72, 'instructor12', 'instructor12@university.edu', '123456', 'Chau', 'Pham', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(73, 'instructor13', 'instructor13@university.edu', '123456', 'Dung', 'Le', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(74, 'instructor14', 'instructor14@university.edu', '123456', 'Hanh', 'Ho', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(75, 'instructor15', 'instructor15@university.edu', '123456', 'Khoa', 'Vo', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(76, 'instructor16', 'instructor16@university.edu', '123456', 'Lam', 'Bui', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(77, 'instructor17', 'instructor17@university.edu', '123456', 'My', 'Phan', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(78, 'instructor18', 'instructor18@university.edu', '123456', 'Nam', 'Dang', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(79, 'instructor19', 'instructor19@university.edu', '123456', 'Oanh', 'Ly', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01'),
(80, 'instructor20', 'instructor20@university.edu', '123456', 'Phat', 'Nguyen', 'instructor', NULL, '2025-08-14 06:38:01', '2025-08-14 06:38:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_submission` (`assignment_id`,`student_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `graded_by` (`graded_by`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_3` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
