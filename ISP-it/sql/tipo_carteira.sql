-- MySQL dump 9.11
--
-- Host: localhost    Database: base_tdkom
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `TipoCarteira`
--

CREATE TABLE `TipoCarteira` (
  `id` bigint(20) NOT NULL auto_increment,
  `nome` varchar(50) NOT NULL default '',
  `descricao` varchar(255) NOT NULL default '',
  `valor` char(1) NOT NULL default 'R',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `TipoCarteira`
--

INSERT INTO `TipoCarteira` VALUES (1,'Duplicata Registrada','Duplicatas\nRegistradas com instrução de cobrança','R');
INSERT INTO `TipoCarteira` VALUES (2,'Duplicata Simples','Duplicata sem\ngeração de cobrança bancaria','S');
INSERT INTO `TipoCarteira` VALUES (3,'Débito Automatico','Gera cobrança\nbancaria','D');

