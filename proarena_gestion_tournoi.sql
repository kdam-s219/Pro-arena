-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2026 at 03:02 AM
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
-- Database: `proarena_gestion_tournoi`
--

-- --------------------------------------------------------

--
-- Table structure for table `utulisateurs`
--

CREATE TABLE `utulisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `prenom` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('athlete','organisateur','club','admin') NOT NULL,
  `date_d_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `sport_prefere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utulisateurs`
--

INSERT INTO `utulisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `date_d_inscription`, `sport_prefere`) VALUES
(5, 'elmoudni ', 'adam', 'adam24moudni@gmail.com', '$2y$10$BR/ROeuhUldTOIBjhSN7R.YOSdarS6ACoFKyg..xspzC5I7Cb9IEy', 'athlete', '2026-01-30 04:03:30', NULL),
(6, 'FAHD', 'DAHD', 'adamelmoudni8@gmail.com', '$2y$10$D.xqDOgij7TfkZZwsz0CH..EJ3XqfoI.fybUjwMo.qn2dwp.xguQm', 'club', '2026-01-30 04:04:02', NULL),
(7, 'admin', 'admin', 'admin@gmail.com', '$2y$10$LsVW6a./KH0feSpH/V37t.cAWy2PMm6RgepQKVUMkGcyB8P/nbUC2', 'admin', '2026-01-30 04:08:54', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `utulisateurs`
--
ALTER TABLE `utulisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `utulisateurs`
--
ALTER TABLE `utulisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
