-- MySQL dump 9.11
--
-- Host: localhost    Database: base_tdkom
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `TipoPessoa`
--

CREATE TABLE `TipoPessoa` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(60) default NULL,
  `valor` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `TipoPessoa`
--

INSERT INTO `TipoPessoa` VALUES (1,'Clientes','cli');
INSERT INTO `TipoPessoa` VALUES (2,'Fornecedores','for');
INSERT INTO `TipoPessoa` VALUES (3,'Prospects','prosp');
INSERT INTO `TipoPessoa` VALUES (4,'Parceiros','parc');
INSERT INTO `TipoPessoa` VALUES (5,'Bancos','ban');
INSERT INTO `TipoPessoa` VALUES (6,'Condominios','cond');

