-- MySQL dump 9.11
--
-- Host: localhost    Database: base_tdkom
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `Vencimentos`
--

CREATE TABLE `Vencimentos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(200) default NULL,
  `diaVencimento` char(2) default NULL,
  `diaFaturamento` char(2) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `Vencimentos`
--

INSERT INTO `Vencimentos` VALUES (2,'Dia 10','10','25');
INSERT INTO `Vencimentos` VALUES (3,'Dia 15','15','1');
INSERT INTO `Vencimentos` VALUES (4,'Dia 25','25','10');
INSERT INTO `Vencimentos` VALUES (5,'Dia 5','5','20');
INSERT INTO `Vencimentos` VALUES (6,'Dia 20','20','5');

