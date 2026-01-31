-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2026 at 08:25 PM
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
  `role` enum('athlete','organisateur','club') NOT NULL,
  `date_d_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `sport_prefere` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utulisateurs`
--

INSERT INTO `utulisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `date_d_inscription`, `sport_prefere`) VALUES
(4, 'ELMOUDNI', 'ADAM', 'adam24moudni@gmail.com', '$2y$10$HHp6FTZmpizP22gNGXwQvuPhbjzKvzGV..z3aGSXpbH6jmGkIlDPy', 'athlete', '2026-01-24 01:53:56', NULL),
(5, 'FAHD', 'adam', 'adamelmoudni8@gmail.com', '$2y$10$2WpW8eybJPGirPD0GZoFme/j2jAo6KwsicXcRU783SzVPBB7W1tqS', 'club', '2026-01-27 04:14:04', NULL),
(6, 'l3witi', '7med', '7medben7med@gmail.com', '$2y$10$MnbIctuOaPIKP9Qo7s9yi.GRgRAHYE5X1KAih8NnOO3jMB.DHNwYK', 'athlete', '2026-01-27 04:59:36', NULL),
(7, 'samiri', 'ali', 'alisamiri@gmail.com', '$2y$10$MuVdKVIUSBo/p.kWT17zlO9Lt6DoLZgEGq2yZd6JAwxieP20hp7xG', 'athlete', '2026-01-27 05:28:42', NULL);

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
 


--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `utulisateurs`

