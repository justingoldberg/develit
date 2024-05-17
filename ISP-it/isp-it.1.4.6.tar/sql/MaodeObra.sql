-- MySQL dump 9.10
--
-- Host: localhost    Database: isp
-- ------------------------------------------------------
-- Server version	4.0.18-log

--
-- Table structure for table `MaodeObra`
--

DROP TABLE IF EXISTS MaodeObra;
CREATE TABLE MaodeObra (
  id bigint(20) NOT NULL auto_increment,
  descricao varchar(100) default '',
  dtCadastro datetime default NULL,
  idUsuario bigint(20) default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
