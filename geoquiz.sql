-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2022 at 02:07 AM
-- Server version: 10.3.34-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `geoquiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `attempt`
--

CREATE TABLE `attempt` (
  `id` int(11) NOT NULL,
  `user` varchar(256) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `score` int(11) NOT NULL,
  `id_quiz` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attempt`
--

INSERT INTO `attempt` (`id`, `user`, `date`, `score`, `id_quiz`) VALUES
(14, 'VeryGoodPlayer', '2022-06-13 01:21:09', 4, 1),
(15, 'IntermediatePlayer', '2022-06-13 01:21:29', 2, 1),
(16, 'BadPlayer', '2022-06-13 01:23:50', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `contains`
--

CREATE TABLE `contains` (
  `id_cuestionario` int(11) NOT NULL,
  `id_pregunta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contains`
--

INSERT INTO `contains` (`id_cuestionario`, `id_pregunta`) VALUES
(1, 99),
(1, 100),
(1, 101),
(1, 102),
(2, 103),
(2, 104),
(2, 105),
(2, 106);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `opt1` text NOT NULL,
  `opt2` text NOT NULL,
  `opt3` text NOT NULL,
  `opt4` text NOT NULL,
  `correct_option` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `text`, `opt1`, `opt2`, `opt3`, `opt4`, `correct_option`) VALUES
(99, 'When did the Proterozoic era end?', '2500 MYA', '500 MYA', '1500 MYA', '1000 MYA', 'a'),
(100, 'What is the main focus of investigations for this discipline?', 'Minerals', 'Geological strata', 'Tectonic plate collisions', 'Oceanic sediments', 'b'),
(101, 'What event marks the end of the Permic era?', 'The emergence of the first euchariotic cells', 'The collision of one of Mars\' moons onto its surface', 'A mass extinction event', 'The building of the Pyramids of Giza', 'c'),
(102, 'How many eons are recognized since the formation of the Earth?', '3', '4', '5', 'More than 5', 'b'),
(103, 'What does mineralogy study?', 'Minerals', 'Gases', 'Living organisms', '16th century string instruments', 'a'),
(104, 'What is the accepted color of gold?', 'Blue', 'Metallic yellow', 'Orange', 'Colorless', 'b'),
(105, 'What is the crystal form of quartz?', 'Trigonal or hexagonal', 'Cubic', 'Quartz does not crystallize', 'Pentagonal', 'a'),
(106, 'What is the hardness of lead (PB) according to the Mohs scale?', '6', '1.5', '9', '3', 'b');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'Historical geology', '¿Do you feel strong in palaeogeology? Try your best! (ANSWERS: A B C B)', '2022-06-13 01:20:16'),
(2, 'Mineralogy', 'The cool part of all that dirt. (ANSWERS: A B A B)', '2022-06-13 01:20:26');

-- --------------------------------------------------------

--
-- Table structure for table `restriction`
--

CREATE TABLE `restriction` (
  `id` int(11) NOT NULL,
  `continente` varchar(256) NOT NULL,
  `id_cuestionario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `restriction`
--

INSERT INTO `restriction` (`id`, `continente`, `id_cuestionario`) VALUES
(23, 'africa', 1),
(24, 'asia', 1),
(25, 'oceania', 1),
(26, 'northAmerica', 1),
(27, 'southAmerica', 1),
(28, 'europe', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attempt`
--
ALTER TABLE `attempt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_QUIZ` (`id_quiz`);

--
-- Indexes for table `contains`
--
ALTER TABLE `contains`
  ADD PRIMARY KEY (`id_cuestionario`,`id_pregunta`),
  ADD KEY `FK_PREGUNTA` (`id_pregunta`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restriction`
--
ALTER TABLE `restriction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FOREIGN_KEY` (`id_cuestionario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attempt`
--
ALTER TABLE `attempt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `restriction`
--
ALTER TABLE `restriction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attempt`
--
ALTER TABLE `attempt`
  ADD CONSTRAINT `FK_QUIZ` FOREIGN KEY (`id_quiz`) REFERENCES `quiz` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `contains`
--
ALTER TABLE `contains`
  ADD CONSTRAINT `FK_CUESTIONARIO` FOREIGN KEY (`id_cuestionario`) REFERENCES `quiz` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_PREGUNTA` FOREIGN KEY (`id_pregunta`) REFERENCES `question` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `restriction`
--
ALTER TABLE `restriction`
  ADD CONSTRAINT `FOREIGN_KEY` FOREIGN KEY (`id_cuestionario`) REFERENCES `quiz` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
