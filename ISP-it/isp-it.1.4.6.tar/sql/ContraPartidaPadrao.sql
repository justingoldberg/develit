-- MySQL dump 9.11
--
-- Host: localhost    Database: isp_ivoz
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `ContraPartidaPadrao`
--

CREATE TABLE `ContraPartidaPadrao` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `idServicosPlanos` int(11) unsigned NOT NULL default '0',
  `idPessoaTipo` int(11) unsigned NOT NULL default '0',
  `idPlanoDeContasSub` int(11) unsigned NOT NULL default '0',
  `tipoContraPartida` varchar(25) NOT NULL default 'pagar',
  `tipoValor` varchar(15) NOT NULL default 'porcentagem',
  `valor` float(6,2) NOT NULL default '0.00',
  `idVencimento` int(1) NOT NULL default '1',
  `descricao` varchar(255) NOT NULL default '',
  `status` char(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

