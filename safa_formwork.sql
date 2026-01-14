-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 17, 2025 at 07:45 PM
-- Server version: 10.11.14-MariaDB-cll-lve
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prabinsh_safa_formwork`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'xtzprabin19', '$2y$10$OLG2grsQD470ukz5PLObMOHMfA1II8ncJ9xn5WmTD5GuNX.E1df8S', '2025-11-17 18:14:54'),
(2, 'bishal252', '$2y$10$ZNGh2yAxDT7vSxdt9NJxl.8.mI4VdnvH2ZaZ5JMYGgtaJnD.eatLu', '2025-11-17 18:14:54');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `project_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `name`, `email`, `phone`, `project_type`, `subject`, `message`, `status`, `created_at`) VALUES
(5, 'Prabin Sharma', 'sharmaprabin160@gmail.com', '9761734136', 'Formwork', 'ddd', 'ddd', 'read', '2025-11-17 18:33:46');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('current','completed','past') NOT NULL DEFAULT 'current',
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `project_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `category`, `location`, `description`, `cover_image`, `created_at`, `project_type`) VALUES
(1, 'RSPCA, Yagoona (Stage 1& 2)', 'completed', 'Aus', 'The RSPCA Yagoona Stage 1 & 2 project involves significant redevelopment of the organization\'s primary animal care facility. The works include new building structures, upgraded access routes, improved drainage, concrete works, and enhanced operational spaces designed to support animal welfare and staff efficiency.', 'uploads/projects/1763403818_17747ad9fc502233.jpg', '2025-11-17 18:23:38', NULL),
(2, 'Good Samaritan Catholic Collage_ Hoxton Park', 'completed', 'Aus', 'The Good Samaritan Catholic College project at Hoxton Park features significant improvements across the school campus. The works include construction of new learning spaces, upgraded concrete pathways, outdoor activity zones, retaining structures, and refined access routes.', 'uploads/projects/afad3c20f8f3a85d_691b6926356dd3.64055839.jpg', '2025-11-17 18:25:03', NULL),
(3, 'Trinity College, Regents Park (Stage 2, Phase 2)', 'completed', 'Aus', 'The Trinity College, Regents Park (Stage 2, Phase 2) project delivers key upgrades across the school campus. Works include construction of new access pathways, concrete structures, retaining walls, and modern learning facility enhancements. These improvements support better circulation, functionality, and long-term durability.', 'uploads/projects/1763403949_916ddcc88a55862b.jpg', '2025-11-17 18:25:49', NULL),
(4, 'St Francis Catholic Collage, Edmondson Park (Stage 3)', 'completed', 'Aus', 'Stage 3 of the St Francis Catholic College development involves significant improvements to support the school’s growing campus. Works include new building structures, upgraded pathways, concrete slabs, retaining walls, and enhanced outdoor learning areas. These upgrades improve safety, access, and functionality across key areas of the school.', 'uploads/projects/1763404767_74bac80171b1207d.jpg', '2025-11-17 18:39:27', NULL),
(5, 'Northmead - Townhouses', 'completed', 'Aus', 'The Northmead Townhouse project involves the construction of contemporary multi-dwelling residences. Works include concrete slabs, retaining walls, structural framing, pathways, and external finishes. The project delivers modern, functional, and high-quality living spaces designed for long-term durability and comfort.', 'uploads/projects/1763404814_6b1e86c111573e73.jpg', '2025-11-17 18:40:14', NULL),
(6, 'Parouse Road, Randwick', 'completed', 'Aus', 'The Parouse Road, Randwick project includes a range of residential construction and civil works. The scope covers concrete slabs, pathways, retaining structures, drainage installation, and external landscaping preparation designed to improve access, durability, and overall site functionality.', 'uploads/projects/1763404855_a3fcc9e21e895d5c.jpg', '2025-11-17 18:40:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_images`
--

INSERT INTO `project_images` (`id`, `project_id`, `image_path`, `created_at`) VALUES
(1, 1, 'uploads/projects/1763403818_3d0389826ad69325.jpg', '2025-11-17 18:23:38'),
(2, 1, 'uploads/projects/1763403818_238c5210d4522ab2.jpg', '2025-11-17 18:23:38'),
(3, 1, 'uploads/projects/1763403818_6c9fb4f87b866dcc.jpg', '2025-11-17 18:23:38'),
(4, 1, 'uploads/projects/1763403818_2a59ff851aba88fc.jpg', '2025-11-17 18:23:38'),
(5, 1, 'uploads/projects/1763403818_8e1951b1119f0faa.jpg', '2025-11-17 18:23:38'),
(6, 1, 'uploads/projects/1763403818_d0220a8b7c20a144.jpg', '2025-11-17 18:23:38'),
(7, 1, 'uploads/projects/1763403818_f3343a97078b4541.jpg', '2025-11-17 18:23:38'),
(8, 1, 'uploads/projects/1763403818_68aae2378aa61628.jpg', '2025-11-17 18:23:38'),
(9, 1, 'uploads/projects/1763403818_36c556516a19eea2.jpg', '2025-11-17 18:23:38'),
(10, 1, 'uploads/projects/1763403818_74de54536c85b3fd.jpg', '2025-11-17 18:23:38'),
(11, 1, 'uploads/projects/1763403818_e0c22be5af36635a.jpg', '2025-11-17 18:23:38'),
(12, 1, 'uploads/projects/1763403818_4d711aa826074088.jpg', '2025-11-17 18:23:38'),
(13, 1, 'uploads/projects/1763403818_b9c7e21b4a0ec778.jpg', '2025-11-17 18:23:38'),
(14, 1, 'uploads/projects/1763403818_119da79e28ad93d5.jpg', '2025-11-17 18:23:38'),
(15, 2, 'uploads/projects/1763403903_364858f6d5982b60.jpg', '2025-11-17 18:25:03'),
(16, 2, 'uploads/projects/1763403903_7c3ae2ca3769fbcf.jpg', '2025-11-17 18:25:03'),
(17, 2, 'uploads/projects/1763403903_78caa84117315af1.jpg', '2025-11-17 18:25:03'),
(18, 2, 'uploads/projects/1763403903_c9605a1b6e0dbd6d.jpg', '2025-11-17 18:25:03'),
(19, 2, 'uploads/projects/1763403903_e28d504a0ae3aa4c.jpg', '2025-11-17 18:25:03'),
(20, 2, 'uploads/projects/1763403903_4595028f89b8e1eb.jpg', '2025-11-17 18:25:03'),
(21, 2, 'uploads/projects/1763403903_14833dedb01599a0.jpg', '2025-11-17 18:25:03'),
(22, 2, 'uploads/projects/1763403903_efcdbca1ffad8f3e.jpg', '2025-11-17 18:25:03'),
(23, 2, 'uploads/projects/1763403903_0f5878063af04dad.jpg', '2025-11-17 18:25:03'),
(24, 2, 'uploads/projects/1763403903_5c3d13bcee391ba1.jpg', '2025-11-17 18:25:03'),
(25, 2, 'uploads/projects/1763403903_7bc3e88b38b823a5.jpg', '2025-11-17 18:25:03'),
(26, 2, 'uploads/projects/1763403903_f54ef3defd0a773a.jpg', '2025-11-17 18:25:03'),
(27, 2, 'uploads/projects/1763403903_7279f75a5ac31350.jpg', '2025-11-17 18:25:03'),
(28, 2, 'uploads/projects/1763403903_a64036dd09f61c7a.jpg', '2025-11-17 18:25:03'),
(29, 2, 'uploads/projects/1763403903_c079d0a6c08ee5f1.jpg', '2025-11-17 18:25:03'),
(30, 2, 'uploads/projects/1763403903_4f11f40b2de01701.jpg', '2025-11-17 18:25:03'),
(31, 2, 'uploads/projects/1763403903_d413975dc5eacc60.jpg', '2025-11-17 18:25:03'),
(32, 2, 'uploads/projects/1763403903_1cb3f59716528953.jpg', '2025-11-17 18:25:03'),
(33, 2, 'uploads/projects/1763403903_239c92174c8f42a3.jpg', '2025-11-17 18:25:03'),
(34, 2, 'uploads/projects/1763403903_28bc5c35878a7595.jpg', '2025-11-17 18:25:03'),
(35, 2, 'uploads/projects/1763403903_9a8ee6437f234461.jpg', '2025-11-17 18:25:03'),
(36, 2, 'uploads/projects/1763403903_1c67d8026ff91e00.jpg', '2025-11-17 18:25:03'),
(37, 2, 'uploads/projects/1763403903_4f0df9258e096c32.jpg', '2025-11-17 18:25:03'),
(38, 2, 'uploads/projects/1763403903_7055d881aa94ed46.jpg', '2025-11-17 18:25:03'),
(39, 2, 'uploads/projects/1763403903_7c8372fe31a24cd8.jpg', '2025-11-17 18:25:03'),
(40, 2, 'uploads/projects/1763403903_fb0e998645fa8e6d.jpg', '2025-11-17 18:25:03'),
(41, 2, 'uploads/projects/1763403903_c2cb5d4521de5939.jpg', '2025-11-17 18:25:03'),
(42, 2, 'uploads/projects/1763403903_f3a075182a948224.jpg', '2025-11-17 18:25:03'),
(43, 2, 'uploads/projects/1763403903_7e0a3f167065f698.jpg', '2025-11-17 18:25:03'),
(44, 2, 'uploads/projects/1763403903_d882c6dd20913112.jpg', '2025-11-17 18:25:03'),
(46, 3, 'uploads/projects/1763403949_21f0c86397992b60.jpg', '2025-11-17 18:25:49'),
(47, 3, 'uploads/projects/1763403949_699d1d5ef820e852.jpg', '2025-11-17 18:25:49'),
(48, 3, 'uploads/projects/1763403949_185794110de7a8ff.jpg', '2025-11-17 18:25:49'),
(49, 3, 'uploads/projects/1763403949_549baff2f3c7a746.jpg', '2025-11-17 18:25:49'),
(50, 3, 'uploads/projects/1763403949_16403b9f6b7dadc3.jpg', '2025-11-17 18:25:49'),
(51, 3, 'uploads/projects/1763403949_3ea65319d5cbf668.jpg', '2025-11-17 18:25:49'),
(52, 3, 'uploads/projects/1763403949_455109dfdec09a86.png', '2025-11-17 18:25:49'),
(53, 3, 'uploads/projects/1763403949_5acfb6655ef26f93.jpg', '2025-11-17 18:25:49'),
(54, 4, 'uploads/projects/1763404767_b679344ee71b11ee.jpg', '2025-11-17 18:39:27'),
(55, 4, 'uploads/projects/1763404767_f2e116b50d1043cc.jpg', '2025-11-17 18:39:27'),
(56, 4, 'uploads/projects/1763404767_a6a5afa39bef08e6.jpg', '2025-11-17 18:39:27'),
(57, 4, 'uploads/projects/1763404767_3c79deec2cfcd233.jpg', '2025-11-17 18:39:27'),
(58, 4, 'uploads/projects/1763404767_e5e26337860d68ca.jpg', '2025-11-17 18:39:27'),
(59, 4, 'uploads/projects/1763404767_6394ab63978b8648.jpg', '2025-11-17 18:39:27'),
(60, 4, 'uploads/projects/1763404767_732636308b86e073.jpg', '2025-11-17 18:39:27'),
(61, 4, 'uploads/projects/1763404767_3e721328af87f301.jpg', '2025-11-17 18:39:27'),
(62, 4, 'uploads/projects/1763404767_366b914494875738.jpg', '2025-11-17 18:39:27'),
(63, 4, 'uploads/projects/1763404767_430721adbdff63fb.jpg', '2025-11-17 18:39:27'),
(64, 4, 'uploads/projects/1763404767_93f2eb9c36a307c4.jpg', '2025-11-17 18:39:27'),
(65, 4, 'uploads/projects/1763404767_d4dee0ab9f8f603a.jpg', '2025-11-17 18:39:27'),
(66, 4, 'uploads/projects/1763404767_e304f7e45eae4d52.jpg', '2025-11-17 18:39:27'),
(67, 4, 'uploads/projects/1763404767_176c53012b0e97b0.jpg', '2025-11-17 18:39:27'),
(68, 4, 'uploads/projects/1763404767_0f3a87f09a3916a4.jpg', '2025-11-17 18:39:27'),
(69, 4, 'uploads/projects/1763404767_3b471ba9c9bc3c43.jpg', '2025-11-17 18:39:27'),
(70, 4, 'uploads/projects/1763404767_d4771c528954d91b.jpg', '2025-11-17 18:39:27'),
(71, 4, 'uploads/projects/1763404767_63eaa81684913485.jpg', '2025-11-17 18:39:27'),
(72, 4, 'uploads/projects/1763404767_1a71b86436f5cce0.jpg', '2025-11-17 18:39:27'),
(73, 4, 'uploads/projects/1763404767_7340277cc32c8a3f.jpg', '2025-11-17 18:39:27'),
(74, 4, 'uploads/projects/1763404767_a6ee0376d47ddd8b.jpg', '2025-11-17 18:39:27'),
(75, 4, 'uploads/projects/1763404767_6cabdc97f103c0f2.jpg', '2025-11-17 18:39:27'),
(76, 4, 'uploads/projects/1763404767_4941d8e204fc5dfb.jpg', '2025-11-17 18:39:27'),
(77, 4, 'uploads/projects/1763404767_cc6c26140c08da6e.jpg', '2025-11-17 18:39:27'),
(78, 4, 'uploads/projects/1763404767_026313dc02c3a685.jpg', '2025-11-17 18:39:27'),
(79, 5, 'uploads/projects/1763404814_07a78e0b619354d3.jpg', '2025-11-17 18:40:14'),
(80, 5, 'uploads/projects/1763404814_215beb3a63da491d.jpg', '2025-11-17 18:40:14'),
(81, 5, 'uploads/projects/1763404814_c226cf99768f6d90.jpg', '2025-11-17 18:40:14'),
(82, 5, 'uploads/projects/1763404814_25a5b5dd4b1fde73.jpg', '2025-11-17 18:40:14'),
(83, 5, 'uploads/projects/1763404814_a3ee3a3520a93d05.jpg', '2025-11-17 18:40:14'),
(84, 5, 'uploads/projects/1763404814_1844986bacb99d6d.jpg', '2025-11-17 18:40:14'),
(85, 5, 'uploads/projects/1763404814_0ebd5a6d119f3599.jpg', '2025-11-17 18:40:14'),
(86, 5, 'uploads/projects/1763404814_5178e44ffac1a61b.jpg', '2025-11-17 18:40:14'),
(87, 5, 'uploads/projects/1763404814_71b302543ec929cc.jpg', '2025-11-17 18:40:14'),
(88, 5, 'uploads/projects/1763404814_cb75e98d9809778b.jpg', '2025-11-17 18:40:14'),
(89, 5, 'uploads/projects/1763404814_ca01c57ccaa764be.jpg', '2025-11-17 18:40:14'),
(90, 5, 'uploads/projects/1763404814_97e987aa580ff70e.jpg', '2025-11-17 18:40:14'),
(91, 5, 'uploads/projects/1763404814_0c3acb1210894f21.jpg', '2025-11-17 18:40:14'),
(92, 5, 'uploads/projects/1763404814_0e63bbb4fb6bcbba.jpg', '2025-11-17 18:40:14'),
(93, 5, 'uploads/projects/1763404814_ef09323a9401a126.jpg', '2025-11-17 18:40:14'),
(94, 5, 'uploads/projects/1763404814_849e6c5ebe91b735.jpg', '2025-11-17 18:40:14'),
(95, 6, 'uploads/projects/1763404855_d96f98366f2e6893.jpg', '2025-11-17 18:40:55'),
(96, 6, 'uploads/projects/1763404855_c11b12a1cc7d0420.jpg', '2025-11-17 18:40:55'),
(97, 6, 'uploads/projects/1763404855_7eb23448503e402d.jpg', '2025-11-17 18:40:55'),
(98, 6, 'uploads/projects/1763404855_b6b1a8b38acdf6a9.jpg', '2025-11-17 18:40:55'),
(99, 6, 'uploads/projects/1763404855_6901c1fb2e856aeb.jpg', '2025-11-17 18:40:55'),
(100, 6, 'uploads/projects/1763404855_88aaa68ea774f09c.jpg', '2025-11-17 18:40:55'),
(101, 6, 'uploads/projects/1763404855_e5980779d9c57237.jpg', '2025-11-17 18:40:55'),
(102, 6, 'uploads/projects/1763404855_8943b0f2375165d4.jpg', '2025-11-17 18:40:55'),
(103, 6, 'uploads/projects/1763404855_02ebf9a411ffd44c.jpg', '2025-11-17 18:40:55'),
(104, 6, 'uploads/projects/1763404855_93d464752d5eea04.jpg', '2025-11-17 18:40:55'),
(105, 6, 'uploads/projects/1763404855_c335d648471e3cdf.jpg', '2025-11-17 18:40:55'),
(106, 6, 'uploads/projects/1763404855_2e0c965ac1721ff4.jpg', '2025-11-17 18:40:55'),
(107, 6, 'uploads/projects/1763404855_afb466c8a8f12e01.jpg', '2025-11-17 18:40:55'),
(108, 6, 'uploads/projects/1763404855_523c4c91a6607131.jpg', '2025-11-17 18:40:55'),
(109, 6, 'uploads/projects/1763404855_bb16e327c3bb00ef.jpg', '2025-11-17 18:40:55'),
(110, 6, 'uploads/projects/1763404855_43b6e599104f587c.jpg', '2025-11-17 18:40:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project_images_project_id` (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `project_images`
--
ALTER TABLE `project_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `fk_project_images_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
