-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2015 at 04:37 PM
-- Server version: 5.5.41
-- PHP Version: 5.5.9-1ubuntu4.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `mydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `privmsgs`
--

CREATE TABLE IF NOT EXISTS `privmsgs` (
  `privmsg_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `privmsg_author` bigint(20) NOT NULL,
  `privmsg_date` varchar(20) NOT NULL,
  `privmsg_subject` varchar(1024) NOT NULL,
  `privmsg_body` varchar(60000) NOT NULL,
  `privmsg_notify` tinyint(1) DEFAULT NULL,
  `privmsg_deleted` tinyint(1) DEFAULT NULL,
  `privmsg_ddate` varchar(20) DEFAULT NULL,
  UNIQUE KEY `privmsg_id` (`privmsg_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `privmsgs`
--

INSERT INTO `privmsgs` (`privmsg_id`, `privmsg_author`, `privmsg_date`, `privmsg_subject`, `privmsg_body`, `privmsg_notify`, `privmsg_deleted`, `privmsg_ddate`) VALUES
(6, 2, '2015-03-19 12:36:28', 'flowers', 'A flower, sometimes known as a bloom or blossom, is the reproductive structure found in flowering plants (plants of the division Magnoliophyta, also called angiosperms). The biological function of a flower is to effect reproduction, usually by providing a mechanism for the union of sperm with eggs. Flowers may facilitate outcrossing (fusion of sperm and eggs from different individuals in a population) or allow selfing (fusion of sperm and egg from the same flower). Some flowers produce diaspores without fertilization (parthenocarpy). Flowers contain sporangia and are the site where gametophytes develop. Flowers give rise to fruit and seeds. Many flowers have evolved to be attractive to animals, so as to cause them to be vectors for the transfer of pollen.', 1, NULL, NULL),
(7, 1, '2015-03-19 12:38:17', 'Fungi', 'A fungus is any member of a large group of eukaryotic organisms that includes microorganisms such as yeasts and molds (British English: moulds), as well as the more familiar mushrooms. These organisms are classified as a kingdom, Fungi, which is separate from plants, animals, protists, and bacteria. One major difference is that fungal cells have cell walls that contain chitin, unlike the cell walls of plants and some protists, which contain cellulose, and unlike the cell walls of bacteria. These and other differences show that the fungi form a single group of related organisms, named the Eumycota (true fungi or Eumycetes), that share a common ancestor (is a monophyletic group). This fungal group is distinct from the structurally similar myxomycetes (slime molds) and oomycetes (water molds). The discipline of biology devoted to the study of fungi is known as mycology (from the Greek ?????, muk?s, meaning "fungus"). Mycology has often been regarded as a branch of botany, even though it is a separate kingdom in biological taxonomy. Genetic studies have shown that fungi are more closely related to animals than to plants.', 1, NULL, NULL),
(8, 2, '2015-03-19 12:39:05', 'Bacteria', 'Bacteria constitute a large domain of prokaryotic microorganisms. Typically a few micrometres in length, bacteria have a number of shapes, ranging from spheres to rods and spirals. Bacteria were among the first life forms to appear on Earth, and are present in most of its habitats. Bacteria inhabit soil, water, acidic hot springs, radioactive waste,[4] and the deep portions of Earth&#039;s crust. Bacteria also live in symbiotic and parasitic relationships with plants and animals. They are also known to have flourished in manned spacecraft.[5]', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `privmsgs_to`
--

CREATE TABLE IF NOT EXISTS `privmsgs_to` (
  `pmto_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pmto_message` bigint(20) NOT NULL,
  `pmto_recipient` bigint(20) NOT NULL,
  `pmto_read` tinyint(1) DEFAULT NULL,
  `pmto_rdate` varchar(20) DEFAULT NULL,
  `pmto_deleted` tinyint(1) DEFAULT NULL,
  `pmto_ddate` varchar(20) DEFAULT NULL,
  `pmto_allownotify` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `pmto_id` (`pmto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `privmsgs_to`
--

INSERT INTO `privmsgs_to` (`pmto_id`, `pmto_message`, `pmto_recipient`, `pmto_read`, `pmto_rdate`, `pmto_deleted`, `pmto_ddate`, `pmto_allownotify`) VALUES
(5, 6, 1, NULL, NULL, NULL, NULL, NULL),
(6, 7, 2, NULL, NULL, NULL, NULL, NULL),
(7, 8, 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`) VALUES
(1, 'FooBar'),
(2, 'FooFoo');

