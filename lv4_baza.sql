-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2026 at 11:15 PM
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
-- Database: `lv4_baza`
--

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id` int(10) NOT NULL,
  `korisnicko_ime` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `lozinka` varchar(255) NOT NULL,
  `uloga` enum('korisnik','admin') NOT NULL DEFAULT 'korisnik',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id`, `korisnicko_ime`, `email`, `lozinka`, `uloga`, `created_at`) VALUES
(1, 'Adriano', 'adriano.strganac@gmail.com', '$2y$10$Ce.4miW/aUfF0FWDaKaibenjPzUZGOQMQR4lNDsBqM93r/QQVQjV.', 'korisnik', '2026-05-24 17:49:03');

-- --------------------------------------------------------

--
-- Table structure for table `ocjene`
--

CREATE TABLE `ocjene` (
  `id_korisnika` int(10) NOT NULL,
  `id_slike` int(10) NOT NULL,
  `ocjena` tinyint(1) NOT NULL CHECK (`ocjena` between 1 and 5),
  `vrijeme_ocjene` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ocjene`
--

INSERT INTO `ocjene` (`id_korisnika`, `id_slike`, `ocjena`, `vrijeme_ocjene`) VALUES
(1, 2, 5, '2026-05-24 19:42:28'),
(1, 3, 4, '2026-05-24 19:42:25');

-- --------------------------------------------------------

--
-- Table structure for table `pjesme`
--

CREATE TABLE `pjesme` (
  `id` int(10) NOT NULL,
  `naslov` varchar(200) NOT NULL,
  `izvodac` varchar(200) NOT NULL,
  `zanr` varchar(100) NOT NULL,
  `bpm` int(5) NOT NULL,
  `godina` year(4) NOT NULL,
  `popularnost` decimal(3,1) NOT NULL,
  `raspolozenje` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pjesme`
--

INSERT INTO `pjesme` (`id`, `naslov`, `izvodac`, `zanr`, `bpm`, `godina`, `popularnost`, `raspolozenje`) VALUES
(1, 'Blinding Lights', 'The Weeknd', 'Synthwave', 171, '2020', 4.5, 'Energetic'),
(2, 'Bohemian Rhapsody', 'Queen', 'Rock', 72, '1975', 4.9, 'Dramatic'),
(3, 'Bad Guy', 'Billie Eilish', 'Electropop', 135, '2019', 4.3, 'Dark'),
(4, 'Shape of You', 'Ed Sheeran', 'Pop', 96, '2017', 4.2, 'Happy'),
(5, 'Hotel California', 'Eagles', 'Rock', 74, '1976', 4.8, 'Melancholic'),
(6, 'Levitating', 'Dua Lipa', 'Disco-Pop', 103, '2020', 4.4, 'Cheerful'),
(7, 'Thunderstruck', 'AC/DC', 'Hard Rock', 134, '1990', 4.7, 'Powerful'),
(8, 'Stay', 'The Kid LAROI & Justin Bieber', 'Pop', 170, '2021', 4.1, 'Energetic'),
(9, 'Smells Like Teen Spirit', 'Nirvana', 'Grunge', 117, '1991', 4.9, 'Aggressive'),
(10, 'In the End', 'Linkin Park', 'Nu Metal', 105, '2000', 4.8, 'Emotional'),
(11, 'Superstition', 'Stevie Wonder', 'Funk', 101, '1972', 4.7, 'Groovy'),
(12, 'Don\'t Stop Believin\'', 'Journey', 'Rock', 119, '1981', 4.6, 'Inspirational'),
(13, 'Rolling in the Deep', 'Adele', 'Soul', 105, '2011', 4.7, 'Passionate'),
(14, 'Take Five', 'Dave Brubeck', 'Jazz', 174, '1959', 4.9, 'Relaxed'),
(15, 'One More Time', 'Daft Punk', 'House', 123, '2000', 4.6, 'Festive'),
(16, 'Lose Yourself', 'Eminem', 'Hip Hop', 171, '2002', 4.9, 'Determined'),
(17, 'Dreams', 'Fleetwood Mac', 'Soft Rock', 120, '1977', 4.8, 'Dreamy'),
(18, 'Master of Puppets', 'Metallica', 'Thrash Metal', 212, '1986', 4.9, 'Intense'),
(19, 'Uptown Funk', 'Mark Ronson ft. Bruno Mars', 'Funk', 115, '2014', 4.5, 'Funky'),
(20, 'Billie Jean', 'Michael Jackson', 'Pop', 117, '1982', 4.9, 'Danceable'),
(21, 'Mr. Brightside', 'The Killers', 'Indie Rock', 148, '2004', 4.7, 'Anthemic'),
(22, 'Starboy', 'The Weeknd', 'R&B', 186, '2016', 4.4, 'Moody'),
(23, 'Another One Bites the Dust', 'Queen', 'Funk Rock', 110, '1980', 4.7, 'Confident'),
(24, 'Flowers', 'Miley Cyrus', 'Pop', 118, '2023', 4.3, 'Empowering'),
(25, 'Highway to Hell', 'AC/DC', 'Hard Rock', 116, '1979', 4.8, 'Wild'),
(26, 'Sweet Child O\' Mine', 'Guns N\' Roses', 'Hard Rock', 125, '1987', 4.9, 'Romantic'),
(27, 'Humble', 'Kendrick Lamar', 'Hip Hop', 150, '2017', 4.6, 'Serious'),
(28, 'Seven Nation Army', 'The White Stripes', 'Garage Rock', 124, '2003', 4.7, 'Gritty'),
(29, 'Wake Me Up', 'Avicii', 'EDM', 124, '2013', 4.5, 'Uplifting'),
(30, 'Watermelon Sugar', 'Harry Styles', 'Pop', 95, '2019', 4.2, 'Summer Vibes');

-- --------------------------------------------------------

--
-- Table structure for table `playlista_pjesme`
--

CREATE TABLE `playlista_pjesme` (
  `id_playliste` int(10) NOT NULL,
  `id_pjesme` int(10) NOT NULL,
  `dodano_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlista_pjesme`
--

INSERT INTO `playlista_pjesme` (`id_playliste`, `id_pjesme`, `dodano_at`) VALUES
(6, 1, '2026-05-24 18:03:05'),
(6, 8, '2026-05-24 18:03:14'),
(8, 1, '2026-05-24 18:20:19'),
(8, 2, '2026-05-24 18:20:20'),
(8, 3, '2026-05-24 18:20:19'),
(8, 5, '2026-05-24 18:20:20'),
(11, 1, '2026-05-24 18:44:46'),
(11, 3, '2026-05-24 18:44:49'),
(11, 23, '2026-05-24 18:44:47'),
(12, 1, '2026-05-24 19:41:30'),
(12, 2, '2026-05-24 19:41:31'),
(12, 3, '2026-05-24 19:41:29'),
(12, 4, '2026-05-24 19:41:29'),
(12, 5, '2026-05-24 19:41:32'),
(12, 6, '2026-05-24 19:41:32'),
(12, 7, '2026-05-24 19:41:33'),
(12, 8, '2026-05-24 19:41:33'),
(12, 9, '2026-05-24 19:41:34'),
(12, 10, '2026-05-24 19:41:34'),
(12, 11, '2026-05-24 19:41:35'),
(12, 12, '2026-05-24 19:41:35'),
(12, 13, '2026-05-24 19:41:36'),
(12, 14, '2026-05-24 19:41:36'),
(12, 19, '2026-05-24 19:41:45'),
(12, 20, '2026-05-24 19:41:59'),
(12, 29, '2026-05-24 19:41:28');

-- --------------------------------------------------------

--
-- Table structure for table `playliste`
--

CREATE TABLE `playliste` (
  `id` int(10) NOT NULL,
  `id_korisnika` int(10) NOT NULL,
  `naziv` varchar(200) NOT NULL DEFAULT 'Moja playlista',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playliste`
--

INSERT INTO `playliste` (`id`, `id_korisnika`, `naziv`, `created_at`) VALUES
(6, 1, 'Evening run', '2026-05-24 17:52:57'),
(7, 1, 'Moja playlista', '2026-05-24 18:03:24'),
(8, 1, 'Moja playlista', '2026-05-24 18:09:17'),
(9, 1, 'asdasds', '2026-05-24 18:20:25'),
(10, 1, 'Moja playlista', '2026-05-24 18:26:12'),
(11, 1, 'Moja playlista', '2026-05-24 18:28:15'),
(12, 1, 'Moja playlista', '2026-05-24 18:44:51'),
(13, 1, 'Moja playlista', '2026-05-24 19:42:05');

-- --------------------------------------------------------

--
-- Table structure for table `slike`
--

CREATE TABLE `slike` (
  `id` int(10) NOT NULL,
  `naziv_datoteke` varchar(200) NOT NULL,
  `opis` varchar(300) DEFAULT NULL,
  `putanja` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slike`
--

INSERT INTO `slike` (`id`, `naziv_datoteke`, `opis`, `putanja`) VALUES
(1, 'slika1.jpg', 'Slika 1', 'https://unsplash.it/900/600?image=1'),
(2, 'slika2.jpg', 'Slika 2', 'https://unsplash.it/900/600?image=2'),
(3, 'slika3.jpg', 'Slika 3', 'https://unsplash.it/900/600?image=3'),
(4, 'slika4.jpg', 'Slika 4', 'https://unsplash.it/900/600?image=4'),
(5, 'slika5.jpg', 'Slika 5', 'https://unsplash.it/900/600?image=5'),
(6, 'slika6.jpg', 'Slika 6', 'https://unsplash.it/900/600?image=6'),
(7, 'slika7.jpg', 'Slika 7', 'https://unsplash.it/900/600?image=7'),
(8, 'slika8.jpg', 'Slika 8', 'https://unsplash.it/900/600?image=8'),
(9, 'slika9.jpg', 'Slika 9', 'https://unsplash.it/900/600?image=9'),
(10, 'slika10.jpg', 'Slika 10', 'https://unsplash.it/900/600?image=10'),
(11, 'slika11.jpg', 'Slika 11', 'https://unsplash.it/900/600?image=11'),
(12, 'slika12.jpg', 'Slika 12', 'https://unsplash.it/900/600?image=12'),
(13, 'slika13.jpg', 'Slika 13', 'https://unsplash.it/900/600?image=13'),
(14, 'slika14.jpg', 'Slika 14', 'https://unsplash.it/900/600?image=14'),
(15, 'slika15.jpg', 'Slika 15', 'https://unsplash.it/900/600?image=15'),
(16, 'slika16.jpg', 'Slika 16', 'https://unsplash.it/900/600?image=16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ocjene`
--
ALTER TABLE `ocjene`
  ADD PRIMARY KEY (`id_korisnika`,`id_slike`),
  ADD KEY `id_slike` (`id_slike`);

--
-- Indexes for table `pjesme`
--
ALTER TABLE `pjesme`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playlista_pjesme`
--
ALTER TABLE `playlista_pjesme`
  ADD PRIMARY KEY (`id_playliste`,`id_pjesme`),
  ADD KEY `id_pjesme` (`id_pjesme`);

--
-- Indexes for table `playliste`
--
ALTER TABLE `playliste`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_korisnika` (`id_korisnika`);

--
-- Indexes for table `slike`
--
ALTER TABLE `slike`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pjesme`
--
ALTER TABLE `pjesme`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `playliste`
--
ALTER TABLE `playliste`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `slike`
--
ALTER TABLE `slike`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ocjene`
--
ALTER TABLE `ocjene`
  ADD CONSTRAINT `ocjene_ibfk_1` FOREIGN KEY (`id_korisnika`) REFERENCES `korisnici` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ocjene_ibfk_2` FOREIGN KEY (`id_slike`) REFERENCES `slike` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlista_pjesme`
--
ALTER TABLE `playlista_pjesme`
  ADD CONSTRAINT `playlista_pjesme_ibfk_1` FOREIGN KEY (`id_playliste`) REFERENCES `playliste` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `playlista_pjesme_ibfk_2` FOREIGN KEY (`id_pjesme`) REFERENCES `pjesme` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playliste`
--
ALTER TABLE `playliste`
  ADD CONSTRAINT `playliste_ibfk_1` FOREIGN KEY (`id_korisnika`) REFERENCES `korisnici` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
