-- MySQL dump 9.11
--
-- Host: localhost    Database: isp_tdkom
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `Unidades`
--

CREATE TABLE `Unidades` (
  `id` int(11) NOT NULL auto_increment,
  `unidade` varchar(20) default NULL,
  `descricao` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `Unidades`
--

INSERT INTO `Unidades` VALUES (1,'UN','Unidade');
INSERT INTO `Unidades` VALUES (2,'MB','Mega Bytes');
INSERT INTO `Unidades` VALUES (3,'Kbps','KBits por segundo');
INSERT INTO `Unidades` VALUES (4,'Mbps','MegaBits por segundo');
INSERT INTO `Unidades` VALUES (5,'R$','Reais');
INSERT INTO `Unidades` VALUES (6,'horas','Horas');

