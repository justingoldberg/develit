-- MySQL dump 9.11
--
-- Host: localhost    Database: base_tdkom
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `TiposEnderecos`
--

CREATE TABLE `TiposEnderecos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(60) default NULL,
  `valor` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `TiposEnderecos`
--

INSERT INTO `TiposEnderecos` VALUES (1,'Endereço de Cobrança','cob');
INSERT INTO `TiposEnderecos` VALUES (2,'Endereço de Correspondência','cor');

