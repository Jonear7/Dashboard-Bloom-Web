-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2024 at 01:44 PM
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
-- Database: `bloom`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$p2SJkZL0A/EeGR.BSA5p2entnU5oH2XBKb1sWI.ZlSw.Bqg4KnM9q'),
(2, 'ad', '$2y$10$SChRh9zTSie2YnxlYnT8d.SjLetFSU.cLIHFhJTF1LTTq9tQ6OQTK'),
(3, 'hr', '$2y$10$Cyfaku.AUkjA0Yh1H/9NyuszqOV7QDCOYJqAAONChVXvs5FmyUeCO');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `checkin_date` date DEFAULT NULL,
  `checkout_date` date DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `room_number` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'booked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `user_id`, `room_id`, `payment_id`, `checkin_date`, `checkout_date`, `total_price`, `room_number`, `status`) VALUES
(13, 5, 1, NULL, '2024-07-14', '2024-07-15', 40.00, NULL, 'Booked'),
(14, 5, 2, 2, '2024-07-14', '2024-07-15', 40.00, 102, 'Booked'),
(15, 5, 3, 3, '2024-07-14', '2024-07-15', 40.00, 103, 'Booked'),
(16, 5, 4, 4, '2024-07-15', '2024-07-17', 60.00, 201, 'Booked'),
(18, 20, 6, 6, '2024-07-16', '2024-07-19', 90.00, 203, 'Booked'),
(19, 20, 5, 7, '2024-07-16', '2024-07-18', 60.00, 202, 'Booked'),
(21, 22, 7, NULL, '2024-07-15', '2024-07-17', 60.00, NULL, 'Booked');

-- --------------------------------------------------------

--
-- Table structure for table `checkin_out`
--

CREATE TABLE `checkin_out` (
  `checkin_out_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `status` enum('Checked-in','Checked-out') NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `checkin_out`
--

INSERT INTO `checkin_out` (`checkin_out_id`, `booking_id`, `checkin_date`, `checkout_date`, `total_price`, `room_number`, `status`, `user_id`) VALUES
(1, 1, '2024-07-11', '2024-07-11', 200.00, '101', 'Checked-out', 5),
(2, 2, '2024-07-11', '2024-07-11', 150.00, '201', 'Checked-out', 6),
(3, 4, '2024-07-13', '2024-07-12', 90.00, '301', 'Checked-out', 5),
(4, 3, '2024-07-13', '2024-07-12', 80.00, '102', 'Checked-out', 16),
(5, 5, '2024-07-13', '2024-07-14', 100.00, '401', 'Checked-out', 14),
(6, 6, '2024-07-13', '2024-07-14', 80.00, '101', 'Checked-out', 5),
(7, 7, '2024-07-13', '2024-07-14', 40.00, '102', 'Checked-out', 5),
(8, 17, '2024-07-15', '2024-07-15', 90.00, '202', 'Checked-out', 17),
(9, 20, '2024-07-15', '2024-07-15', 90.00, '204', 'Checked-out', 21);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `job_position` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `surname`, `age`, `phone`, `address`, `job_position`, `image_path`) VALUES
(8, 'JJinggg', 'HH', 15, '93992093', 'Sangsaveng', 'Chef', 'uploads/360_F_243123463_zTooub557xEWABDLk0jJklDyLSGl2jrr.jpg'),
(10, 'JASON', 'PARK', 24, '91234146', 'Sangsaven', 'Cleaner', 'uploads/pexels-stefanstefancik-91227.jpg'),
(16, 'Wadda', 'Heck', 35, '99125454', 'India', 'Engineer', 'uploads/pexels-andrea-piacquadio-874158.jpg'),
(22, 'JJ', 'Susanto', 25, '56385674', 'Sysawarth', 'Secretary', 'uploads/pexels-olly-733872.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `payment_online`
--

CREATE TABLE `payment_online` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_image` blob NOT NULL,
  `payment_date` date NOT NULL,
  `payment_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payment_online`
--

INSERT INTO `payment_online` (`payment_id`, `user_id`, `payment_image`, `payment_date`, `payment_total`) VALUES
(1, 5, 0x363636666461323439653736302d33322e6a7067, '2024-06-17', 40.00),
(2, 5, 0x363639336562373433636337362d33332e6a7067, '2024-07-14', 40.00),
(3, 5, 0x363639336665303838313838332d33332e6a7067, '2024-07-14', 40.00),
(4, 5, 0x363639343666326237383266632d33332e6a7067, '2024-07-15', 60.00),
(5, 17, 0x363639343761316231363761612d313030303030303031382e6a7067, '2024-07-15', 90.00),
(6, 20, 0x363639343763316135373532312d313030303030303031382e6a7067, '2024-07-15', 90.00),
(7, 20, 0x363639343763343264383130632d313030303030303031382e6a7067, '2024-07-15', 60.00),
(8, 21, 0x363639343833666430646137322d313030303030303031382e6a7067, '2024-07-15', 90.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment_walk_in`
--

CREATE TABLE `payment_walk_in` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payment_walk_in`
--

INSERT INTO `payment_walk_in` (`payment_id`, `user_id`, `payment_date`, `payment_total`) VALUES
(1, 5, '2024-07-11', 200.00),
(3, 16, '2024-07-12', 80.00),
(4, 14, '2024-07-12', 100.00),
(5, 5, '2024-07-14', 40.00),
(7, 22, '2024-07-15', 60.00);

-- --------------------------------------------------------

--
-- Table structure for table `rmtype`
--

CREATE TABLE `rmtype` (
  `rmtype_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `rmtype`
--

INSERT INTO `rmtype` (`rmtype_id`, `type_name`, `description`, `price`, `image`) VALUES
(1, 'Deluxe', 'Room size 29 m² Comfy beds, 9 – Based on 118 reviews The spacious double room features air conditioning, a tea and coffee maker, as well as a private bathroom boasting a walk-in shower and a hairdryer. The unit has 1 bed.', 40.00, 0x5b22363638653934396239326539332e6a7067222c22363638653934396239333135382e6a7067222c22363638653934396239333433302e6a7067222c22363638653934396239333864652e6a7067222c22363638653934396239336266632e6a7067222c22363638653934396239336536312e6a7067225d),
(2, 'Deluxe', 'Room size 29 m² Comfy beds, 8.9 – Based on 108 reviews No cots or extra beds are available. See more information  The spacious double room features air conditioning, a tea and coffee maker, as well as a private bathroom boasting a walk-in shower and a hairdryer. The unit has 1 bed.', 40.00, 0x5b22363638653934623535383338652e6a7067222c22363638653934623535383736612e6a7067222c22363638653934623535386132632e6a7067222c22363638653934623535386437382e6a7067222c22363638653934623535393039622e6a7067222c22363638653934623535393366642e6a7067225d),
(3, 'Deluxe', 'Room size 29 m² Comfy beds, 8.9 – Based on 108 reviews No cots or extra beds are available. See more information  The spacious double room features air conditioning, a tea and coffee maker, as well as a private bathroom boasting a walk-in shower and a hairdryer. The unit has 1 bed.', 40.00, 0x5b22363638653934643763393332312e6a7067222c22363638653934643763393633342e6a7067222c22363638653934643763393939662e6a7067222c22363638653934643763396432342e6a7067222c22363638653934643763613033392e6a7067222c22363638653934643763613337322e6a7067225d),
(4, 'Superior Double', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this double room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The double room features air conditioning, a tea and coffee maker, a seating area, a dining area, as well as a flat-screen TV with cable channels. The unit has 1 bed.', 30.00, 0x5b22363638653935356663393363362e6a7067222c22363638653935356663393732302e6a7067222c22363638653935356663396133662e6a7067222c22363638653935356663396430362e6a7067222c22363638653935356663396664322e6a7067225d),
(5, 'Superior Double', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this double room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The double room features air conditioning, a tea and coffee maker, a seating area, a dining area, as well as a flat-screen TV with cable channels. The unit has 1 bed.', 30.00, 0x5b22363638653935373764393438322e6a7067222c22363638653935373764393734342e6a7067222c22363638653935373764396138312e6a7067222c22363638653935373764653939332e6a7067222c22363638653935373764656366652e6a7067225d),
(6, 'Superior Double', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this double room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The double room features air conditioning, a tea and coffee maker, a seating area, a dining area, as well as a flat-screen TV with cable channels. The unit has 1 bed.', 30.00, 0x5b22363638653935396662666233322e6a7067222c22363638653935396663633831392e6a7067222c22363638653935396663636232382e6a7067222c22363638653935396663636466322e6a7067222c22363638653935396663643062372e6a7067225d),
(7, 'Superior Double', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this double room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The double room features air conditioning, a tea and coffee maker, a seating area, a dining area, as well as a flat-screen TV with cable channels. The unit has 1 bed.', 30.00, 0x5b22363638653935643130616564352e6a7067222c22363638653935643130623231662e6a7067222c22363638653935643130623466642e6a7067222c22363638653935643130623739372e6a7067222c22363638653935643130623964362e6a7067225d),
(8, 'Superior Double', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this double room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The double room features air conditioning, a tea and coffee maker, a seating area, a dining area, as well as a flat-screen TV with cable channels. The unit has 1 bed.', 30.00, 0x5b22363638653935653735626538392e6a7067222c22363638653935653735633135302e6a7067222c22363638653935653735633466322e6a7067222c22363638653935653735633761342e6a7067222c22363638653935653735636132642e6a7067225d),
(9, 'Superior Double', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this double room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The double room features air conditioning, a tea and coffee maker, a seating area, a dining area, as well as a flat-screen TV with cable channels. The unit has 1 bed.', 30.00, 0x5b22363638653936303964616137662e6a7067222c22363638653936303964616461332e6a7067222c22363638653936303964623031362e6a7067222c22363638653936303964623233362e6a7067222c22363638653936303964623433302e6a7067225d),
(10, 'Superior Twin', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this twin room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The twin room features air conditioning, a tea and coffee maker, a seating area, a dining area and a flat-screen TV with cable channels. The unit has 2 beds.', 30.00, 0x5b22363638653936353832613261332e6a7067222c22363638653936353832613536342e6a7067222c22363638653936353832666634312e6a7067222c22363638653936353833303236372e6a7067222c22363638653936353833303466622e6a7067222c22363638653936353833303830612e6a7067222c22363638653936353833306139352e6a7067222c22363638653936353833333865312e6a7067222c22363638653936353833336265662e6a7067225d),
(11, 'Superior Twin', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this twin room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The twin room features air conditioning, a tea and coffee maker, a seating area, a dining area and a flat-screen TV with cable channels. The unit has 2 beds.', 30.00, 0x5b22363638653936373264646638372e6a7067222c22363638653936373264653561342e6a7067222c22363638653936373264653931352e6a7067222c22363638653936373264656335652e6a7067222c22363638653936373264656631392e6a7067222c22363638653936373264663163372e6a7067222c22363638653936373264663531382e6a7067222c22363638653936373264663830392e6a7067222c22363638653936373264666166622e6a7067225d),
(12, 'Superior Twin', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this twin room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The twin room features air conditioning, a tea and coffee maker, a seating area, a dining area and a flat-screen TV with cable channels. The unit has 2 beds.', 30.00, 0x5b22363638653936383964336432612e6a7067222c22363638653936383964336664382e6a7067222c22363638653936383964343237392e6a7067222c22363638653936383964343537342e6a7067222c22363638653936383964343839332e6a7067222c22363638653936383964346330632e6a7067222c22363638653936383964346639372e6a7067222c22363638653936383964353337342e6a7067222c22363638653936383964353635362e6a7067225d),
(13, 'Superior Twin', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this twin room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The twin room features air conditioning, a tea and coffee maker, a seating area, a dining area and a flat-screen TV with cable channels. The unit has 2 beds.', 30.00, 0x5b22363638653936613263656334642e6a7067222c22363638653936613263656663392e6a7067222c22363638653936613263663238392e6a7067222c22363638653936613263663466372e6a7067222c22363638653936613263663731382e6a7067222c22363638653936613263663961662e6a7067222c22363638653936613263666431352e6a7067222c22363638653936613263666661322e6a7067222c22363638653936613264303238372e6a7067225d),
(14, 'Superior Twin', 'Room size 23 m² Comfy beds, 9 – Based on 118 reviews Featuring free toiletries, this twin room includes a private bathroom with a walk-in shower, a hairdryer and slippers. The twin room features air conditioning, a tea and coffee maker, a seating area, a dining area and a flat-screen TV with cable channels. The unit has 2 beds.', 30.00, 0x5b22363638653936623638393834332e6a7067222c22363638653936623638396232332e6a7067222c22363638653936623638396538662e6a7067222c22363638653936623638613064372e6a7067222c22363638653936623638613438352e6a7067222c22363638653936623638613862662e6a7067222c22363638653936623638616338302e6a7067222c22363638653936623638623663612e6a7067222c22363638653936623638626163352e6a7067225d),
(15, 'Junior', 'Room size 33 m² Comfy beds, 9 – Based on 118 reviews The spacious double room provides air conditioning, a tea and coffee maker, as well as a private bathroom featuring a walk-in shower and a hairdryer. The unit offers 1 bed.', 50.00, 0x5b22363639306536303139376563302e6a7067222c22363639306536303139383233612e6a7067222c22363639306536303139383532302e6a7067222c22363639306536303139383864302e6a7067222c22363639306536303139386261342e6a7067222c22363639306536303139386534322e6a7067222c22363639306536303139393065332e6a7067225d),
(16, 'Junior', 'Room size 33 m² Comfy beds, 9 – Based on 118 reviews The spacious double room provides air conditioning, a tea and coffee maker, as well as a private bathroom featuring a walk-in shower and a hairdryer. The unit offers 1 bed.', 50.00, 0x5b22363639306536313363326536622e6a7067222c22363639306536313363333262372e6a7067222c22363639306536313363333665392e6a7067222c22363639306536313363333963322e6a7067222c22363639306536313363336430302e6a7067222c22363639306536313363343035662e6a7067222c22363639306536313363343333622e6a7067225d);

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `rmtype_id` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `room_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `rmtype_id`, `status`, `room_number`) VALUES
(1, 1, 'available', 101),
(2, 2, 'available', 102),
(3, 3, 'available', 103),
(4, 4, 'available', 201),
(5, 5, 'available', 202),
(6, 6, 'available', 203),
(7, 7, 'available', 204),
(8, 8, 'available', 205),
(9, 9, 'available', 206),
(10, 10, 'available', 301),
(11, 11, 'available', 302),
(12, 12, 'available', 303),
(13, 13, 'available', 304),
(14, 14, 'available', 305),
(15, 15, 'available', 401),
(16, 16, 'available', 402);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `phone`, `password`) VALUES
(5, 'Jonear', 'Jonear@gmail.com', '93992093', '$2y$10$9X2Nca8mnvuO9KCrYTrZ5.be9IamjcIRDpHol8Q/tzc8iDgBeIZ4C'),
(6, 'Jame', 'jochanthoumnivong@gmail.com', '8562093992099', '$2y$10$aAhkBbUiEqCy/1s53gKTXu36YD8g3YvPYwTymBh2wNT7/BHbHZSuq'),
(7, 'Josh', 'jonasseven12@gmail.com', '77777777', '$2y$10$nLlCzZ.akHdRza2merQ/8eVrPEomy.KugXl4axe0SSAu8cWrYp9ji'),
(13, 'Jonas', 'brmasean3@gmail.com', '93992093', '$2y$10$4W2wR7P5xdd0il01TCnGU.qaCPgf/0tXR9SvA0Gmkr4PpIfIpKRQK'),
(14, 'Hero', 'jonasseven11@gmail.com', '8562093992093', '$2y$10$HfS78u9kQABePcwR3zZoiuIfNdF6jWHkNLk0sHAuxXP79QfZ2.Rd2'),
(16, 'JJ', 'brmasean333@gmail.com', '939909333', '$2y$10$6hEu/BQuJmyGO5uvl7U3NeOFXvxX49YUawNaJXfAErlLJqdeDWDXy'),
(17, 'peter', 'peter@gmail.com', '02076016522', '$2y$10$dTw1qYiQbt41J3jWKf6E9OVfzypnrZyRjtC6GmW4AW6UBZD.sb1sK'),
(18, 'peter', 'peter@gmail.com', '02076016522', '$2y$10$RnCYWFDH3r1fJoi7QPeH8ejAsj2/d/iKZCC4mm3f0O2w3QnqNwZ.O'),
(19, 'yuuta', 'yuuta@gmail.com', '93351409', '$2y$10$P/W64z3Wr360H9d9ZeEGhewgTmFDo3skgW6ISq/gTfI9.nGdNms2y'),
(20, 'do', 'dido@gmail.com', '02076016522', '$2y$10$tKaZXoqvN8GnXUPplhHSJOhkGZTMUgO2WSWWDDZTyYsOr82s0x4n.'),
(21, 'Qwe', 'Q@gmail.com', '12345678', '$2y$10$qn.zbhEfs6ORu2kjMqsAwehBngceMuDxLV4TO0mY2BS5K32ePKHBS'),
(22, 'Po', 'Joshou@gmail.com', '9399093', '$2y$10$4K7yYYDMOPGxSX3g/3PkDemaNeFu38qZZwunXC9ZKZcGDIFN7gjKS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `checkin_out`
--
ALTER TABLE `checkin_out`
  ADD PRIMARY KEY (`checkin_out_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_online`
--
ALTER TABLE `payment_online`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payment_walk_in`
--
ALTER TABLE `payment_walk_in`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rmtype`
--
ALTER TABLE `rmtype`
  ADD PRIMARY KEY (`rmtype_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `rmtype_id` (`rmtype_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `checkin_out`
--
ALTER TABLE `checkin_out`
  MODIFY `checkin_out_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `payment_online`
--
ALTER TABLE `payment_online`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment_walk_in`
--
ALTER TABLE `payment_walk_in`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rmtype`
--
ALTER TABLE `rmtype`
  MODIFY `rmtype_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`),
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `payment_online` (`payment_id`);

--
-- Constraints for table `checkin_out`
--
ALTER TABLE `checkin_out`
  ADD CONSTRAINT `checkin_out_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payment_online`
--
ALTER TABLE `payment_online`
  ADD CONSTRAINT `payment_online_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_walk_in`
--
ALTER TABLE `payment_walk_in`
  ADD CONSTRAINT `payment_walk_in_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`rmtype_id`) REFERENCES `rmtype` (`rmtype_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
