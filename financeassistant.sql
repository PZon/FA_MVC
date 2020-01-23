-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2020 at 10:36 AM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `financeassistant`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `idExpenses` smallint(6) NOT NULL,
  `idUser` smallint(6) NOT NULL,
  `idExpensesCat` smallint(6) NOT NULL,
  `userPayMethId` int(6) NOT NULL,
  `expenseDate` date NOT NULL,
  `expenseAmount` decimal(8,2) NOT NULL,
  `expenseDescr` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`idExpenses`, `idUser`, `idExpensesCat`, `userPayMethId`, `expenseDate`, `expenseAmount`, `expenseDescr`) VALUES
(8, 34, 37, 34, '2019-11-07', '13.00', 'modal'),
(12, 34, 37, 37, '2019-11-14', '12.00', 'edit'),
(16, 34, 31, 41, '2019-10-09', '13.00', 'test'),
(17, 34, 42, 42, '2019-11-12', '45.56', 'gift'),
(18, 34, 50, 34, '2019-11-06', '12.56', 'gift'),
(19, 34, 31, 43, '2019-12-04', '130.00', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `ex_cat`
--

CREATE TABLE `ex_cat` (
  `idCatE` smallint(4) NOT NULL,
  `nameCatE` char(20) NOT NULL,
  `Expense_Limit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ex_cat`
--

INSERT INTO `ex_cat` (`idCatE`, `nameCatE`, `Expense_Limit`) VALUES
(1, 'FOOD', NULL),
(2, 'TRANSPORT', NULL),
(3, 'HOME', NULL),
(4, 'PHONE & INTERNET', NULL),
(5, 'CLOTHES', NULL),
(6, 'HEALTH CARE', NULL),
(8, 'KIDS', NULL),
(9, 'ENTERTAINMENT', NULL),
(10, 'TRAVEL', NULL),
(11, 'HOLIDAY', NULL),
(12, 'EDUCATION', NULL),
(13, 'BOOKS', NULL),
(14, 'SAVINGS', NULL),
(15, 'PENSION', NULL),
(16, 'MORTGAGE', NULL),
(17, 'LOANS', NULL),
(18, 'GIFT', NULL),
(19, 'OTHER', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `idIncome` smallint(6) NOT NULL,
  `idUser` smallint(6) NOT NULL,
  `idIncomeCat` smallint(6) NOT NULL,
  `incomeDate` date NOT NULL,
  `incomeAmount` decimal(8,2) NOT NULL,
  `incomeDescr` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`idIncome`, `idUser`, `idIncomeCat`, `incomeDate`, `incomeAmount`, `incomeDescr`) VALUES
(3, 34, 47, '2019-10-01', '123.00', 'some description'),
(34, 34, 48, '2019-10-13', '13.00', 'pazdziernik'),
(35, 34, 31, '2019-10-13', '23.00', '323232'),
(36, 34, 49, '2019-09-01', '12.00', 'wrzesien'),
(45, 34, 38, '2019-11-13', '13.00', 'robocza edytowana'),
(46, 34, 49, '2019-11-13', '13.00', 'edytowana teraz'),
(47, 34, 50, '2019-11-25', '12.12', 'sdsds'),
(48, 34, 31, '2019-12-04', '100.00', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `in_cat`
--

CREATE TABLE `in_cat` (
  `idCatI` smallint(4) NOT NULL,
  `nameCatI` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `in_cat`
--

INSERT INTO `in_cat` (`idCatI`, `nameCatI`) VALUES
(1, 'SALARY'),
(2, 'INTERESTS'),
(3, 'EBAY SALES'),
(4, 'OTHER');

-- --------------------------------------------------------

--
-- Table structure for table `pay_cat`
--

CREATE TABLE `pay_cat` (
  `idCatPay` smallint(4) NOT NULL,
  `nameCatPay` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pay_cat`
--

INSERT INTO `pay_cat` (`idCatPay`, `nameCatPay`) VALUES
(1, 'CASH'),
(2, 'DEBIT CARD'),
(3, 'CREDIT CARD'),
(4, 'PayPal');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `idUser` smallint(6) NOT NULL,
  `Nick` char(20) NOT NULL,
  `Email` char(60) NOT NULL,
  `Password` char(255) NOT NULL,
  `Password_reset_hash` varchar(255) DEFAULT NULL,
  `Password_reset_exp` datetime DEFAULT NULL,
  `activation_hash` varchar(64) DEFAULT NULL,
  `Active` char(1) NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`idUser`, `Nick`, `Email`, `Password`, `Password_reset_hash`, `Password_reset_exp`, `activation_hash`, `Active`) VALUES
(1, 'ADMIN-DON\'T TOUCH', 'admin@admin.pl', '$2y$10$4H4QjRm8GYs5TAA4S2TCFO404zAmIP03ohOgO8T6/zayrLKdXWP0m', NULL, NULL, NULL, 'Y'),
(34, 'NICK', 'nick@nick.pl', '$2y$10$tmSrfCQh7/ONObDk.bj1EOJkTM10rgzD8ixZ82YQ85DazhcIowyQG', NULL, NULL, NULL, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `user_ex_cat`
--

CREATE TABLE `user_ex_cat` (
  `idUserCatEx` smallint(4) NOT NULL,
  `idUser` smallint(6) DEFAULT NULL,
  `nameUserCatEx` char(20) NOT NULL,
  `UExLimit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_ex_cat`
--

INSERT INTO `user_ex_cat` (`idUserCatEx`, `idUser`, `nameUserCatEx`, `UExLimit`) VALUES
(30, 1, 'ADMIN-DON\'T TOUCH', NULL),
(31, 34, 'CAT_1E', 100),
(37, 34, 'EX CATEGORY', 0),
(39, 34, 'CAT_LIMIT', 100),
(42, 34, 'FOOD', NULL),
(43, 34, 'TRANSPORT', NULL),
(44, 34, 'HOME', NULL),
(45, 34, 'PHONE & INTERNET', NULL),
(46, 34, 'CLOTHES', NULL),
(47, 34, 'HEALTH CARE', NULL),
(48, 34, 'KIDS', NULL),
(49, 34, 'ENTERTAINMENT', NULL),
(50, 34, 'TRAVEL', NULL),
(51, 34, 'HOLIDAY', NULL),
(52, 34, 'EDUCATION', NULL),
(53, 34, 'BOOKS', NULL),
(54, 34, 'SAVINGS', NULL),
(55, 34, 'PENSION', NULL),
(56, 34, 'MORTGAGE', NULL),
(57, 34, 'LOANS', NULL),
(58, 34, 'GIFT', NULL),
(59, 34, 'OTHER', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_in_cat`
--

CREATE TABLE `user_in_cat` (
  `idUserCatIn` smallint(4) NOT NULL,
  `idUser` smallint(6) DEFAULT NULL,
  `nameUserCatIn` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_in_cat`
--

INSERT INTO `user_in_cat` (`idUserCatIn`, `idUser`, `nameUserCatIn`) VALUES
(30, 1, 'ADMIN-DON\'T TOUCH'),
(31, 34, 'CAT_2'),
(38, 34, 'CAT_1'),
(47, 34, 'SALARY'),
(48, 34, 'INTERESTS'),
(49, 34, 'EBAY SALES'),
(50, 34, 'OTHER');

-- --------------------------------------------------------

--
-- Table structure for table `user_pay_cat`
--

CREATE TABLE `user_pay_cat` (
  `idUserCatPay` smallint(4) NOT NULL,
  `idUser` smallint(6) DEFAULT NULL,
  `nameUserCatPay` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_pay_cat`
--

INSERT INTO `user_pay_cat` (`idUserCatPay`, `idUser`, `nameUserCatPay`) VALUES
(30, 1, 'ADMIN-DON\'T TOUCH'),
(34, 34, 'CAT_TESTOWA'),
(37, 34, ' USER PAYMENT TYPE'),
(41, 34, 'CASH'),
(42, 34, 'DEBIT CARD'),
(43, 34, 'CREDIT CARD'),
(44, 34, 'PayPal');

-- --------------------------------------------------------

--
-- Table structure for table `user_remembered_logins`
--

CREATE TABLE `user_remembered_logins` (
  `token_hash` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`idExpenses`);

--
-- Indexes for table `ex_cat`
--
ALTER TABLE `ex_cat`
  ADD PRIMARY KEY (`idCatE`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`idIncome`);

--
-- Indexes for table `in_cat`
--
ALTER TABLE `in_cat`
  ADD PRIMARY KEY (`idCatI`);

--
-- Indexes for table `pay_cat`
--
ALTER TABLE `pay_cat`
  ADD PRIMARY KEY (`idCatPay`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Password_reset_hash` (`Password_reset_hash`),
  ADD UNIQUE KEY `activation_hash` (`activation_hash`);

--
-- Indexes for table `user_ex_cat`
--
ALTER TABLE `user_ex_cat`
  ADD PRIMARY KEY (`idUserCatEx`);

--
-- Indexes for table `user_in_cat`
--
ALTER TABLE `user_in_cat`
  ADD PRIMARY KEY (`idUserCatIn`);

--
-- Indexes for table `user_pay_cat`
--
ALTER TABLE `user_pay_cat`
  ADD PRIMARY KEY (`idUserCatPay`);

--
-- Indexes for table `user_remembered_logins`
--
ALTER TABLE `user_remembered_logins`
  ADD PRIMARY KEY (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `idExpenses` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ex_cat`
--
ALTER TABLE `ex_cat`
  MODIFY `idCatE` smallint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `idIncome` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `in_cat`
--
ALTER TABLE `in_cat`
  MODIFY `idCatI` smallint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pay_cat`
--
ALTER TABLE `pay_cat`
  MODIFY `idCatPay` smallint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `idUser` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `user_ex_cat`
--
ALTER TABLE `user_ex_cat`
  MODIFY `idUserCatEx` smallint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `user_in_cat`
--
ALTER TABLE `user_in_cat`
  MODIFY `idUserCatIn` smallint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `user_pay_cat`
--
ALTER TABLE `user_pay_cat`
  MODIFY `idUserCatPay` smallint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
