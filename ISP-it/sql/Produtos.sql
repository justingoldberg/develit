-- MySQL dump 9.10
--
-- Host: localhost    Database: isp
-- ------------------------------------------------------
-- Server version	4.0.18-log

--
-- Table structure for table `Produtos`
--

DROP TABLE IF EXISTS Produtos;
CREATE TABLE Produtos (
  id bigint(20) NOT NULL auto_increment,
  nome varchar(100) default '',
  descricao varchar(255) default '',
  valor double default '0',
  dtCadastro datetime default null,
  idUsuario bigint(20) default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
