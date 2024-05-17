-- MySQL dump 9.11
--
-- Host: localhost    Database: base_tdkom
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `TiposDocumentos`
--

CREATE TABLE `TiposDocumentos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(60) default NULL,
  `valor` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `TiposDocumentos`
--

INSERT INTO `TiposDocumentos` VALUES (1,'CPF - Cadastro Pessoa Física','cpf');
INSERT INTO `TiposDocumentos` VALUES (2,'CNPJ - Cadastro Nacional Pessoa Jurídica','cnpj');
INSERT INTO `TiposDocumentos` VALUES (3,'RG - Registro Geral','rg');
INSERT INTO `TiposDocumentos` VALUES (4,'IE - Inscricao Estadual','ie');

