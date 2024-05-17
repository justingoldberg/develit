-- MySQL dump 9.11
--
-- Host: localhost    Database: isp_tdkom
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-2-log

--
-- Table structure for table `ContasAPagar`
--

CREATE TABLE `ContasAPagar` (
  `id` int(11) NOT NULL auto_increment,
  `idCentroDeCusto` int(11) NOT NULL default '0',
  `idFornecedor` int(11) NOT NULL default '0',
  `idPlanoContaSub` int(11) NOT NULL default '0',
  `idPop` int(11) NOT NULL default '0',
  `valor` decimal(10,2) NOT NULL default '0.00',
  `valorPago` decimal(10,2) default NULL,
  `dtCadastro` datetime NOT NULL default '0000-00-00 00:00:00',
  `dtVencimento` date default NULL,
  `dtBaixa` datetime default NULL,
  `obs` text not null default '' ,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `PlanoDeContas`
--

CREATE TABLE `PlanoDeContas` (
  `id` int(11) NOT NULL auto_increment,
  `nome` char(50) default NULL,
  `descricao` char(200) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `PlanoDeContasSub`
--

CREATE TABLE `PlanoDeContasSub` (
  `id` int(11) NOT NULL auto_increment,
  `idPai` int(11) NOT NULL default '0',
  `nome` char(50) default NULL,
  `descricao` char(200) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

