-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Juin 06, 2015 at 21:54 PM
-- Server version: 5.1.57
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Crée par NathaanGaming et mis a jour pour PDO par Wdes
--

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
CREATE TABLE IF NOT EXISTS `tokens` (
  `accessToken` char(32) COLLATE latin1_general_ci NOT NULL,
  `clientToken` char(255) COLLATE latin1_general_ci NOT NULL,
  `uuid` char(32) COLLATE latin1_general_ci NOT NULL,
  `username` char(16) COLLATE latin1_general_ci NOT NULL,
  UNIQUE KEY `accessToken` (`accessToken`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tokens`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uuid` char(32) COLLATE latin1_general_ci NOT NULL,
  `username` char(16) COLLATE latin1_general_ci NOT NULL,
  `password` char(32) COLLATE latin1_general_ci NOT NULL,
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

