-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2023 at 04:47 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `5114asst1`
--

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE `album` (
  `idalbum` int(11) NOT NULL,
  `title` varchar(45) DEFAULT NULL,
  `imageurl` varchar(100) DEFAULT NULL,
  `idcreator` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `album`
--

INSERT INTO `album` (`idalbum`, `title`, `imageurl`, `idcreator`) VALUES
(1, 'halo', 'picture/album.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `creator`
--

CREATE TABLE `creator` (
  `idcreator` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(300) NOT NULL,
  `imageurl` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `creator`
--

INSERT INTO `creator` (`idcreator`, `name`, `password`, `imageurl`) VALUES
(3, 'AlexChu', '202cb962ac59075b964b07152d234b70', 'images/Pompompurin.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `photo`
--

CREATE TABLE `photo` (
  `idphoto` int(11) NOT NULL,
  `title` varchar(45) DEFAULT NULL,
  `imageurl` varchar(100) DEFAULT NULL,
  `comment` varchar(140) DEFAULT NULL,
  `idcreator` int(11) DEFAULT NULL,
  `idalbum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photo`
--

INSERT INTO `photo` (`idphoto`, `title`, `imageurl`, `comment`, `idcreator`, `idalbum`) VALUES
(1, 'A', 'images/123-A.png', 'A Image', 3, 1),
(2, 'B', 'images/123-B.png', 'B Image', 3, 1),
(3, 'C', 'images/123-C.png', 'C Image\r\n', 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`idalbum`),
  ADD KEY `album_creator_idx` (`idcreator`);

--
-- Indexes for table `creator`
--
ALTER TABLE `creator`
  ADD PRIMARY KEY (`idcreator`);

--
-- Indexes for table `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`idphoto`),
  ADD KEY `photo_album_idx` (`idalbum`),
  ADD KEY `photo_creator_idx` (`idcreator`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `album`
--
ALTER TABLE `album`
  MODIFY `idalbum` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `creator`
--
ALTER TABLE `creator`
  MODIFY `idcreator` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `photo`
--
ALTER TABLE `photo`
  MODIFY `idphoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `album`
--
ALTER TABLE `album`
  ADD CONSTRAINT `album_creator` FOREIGN KEY (`idcreator`) REFERENCES `creator` (`idcreator`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `image_album` FOREIGN KEY (`idalbum`) REFERENCES `album` (`idalbum`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `photo_creator` FOREIGN KEY (`idcreator`) REFERENCES `creator` (`idcreator`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
