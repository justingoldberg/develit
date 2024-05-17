-- MySQL dump 10.9
--
-- Host: localhost    Database: isp_tdkom
-- ------------------------------------------------------
-- Server version	4.1.15-Debian_1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `CentroDeCusto`
--

DROP TABLE IF EXISTS `CentroDeCusto`;
CREATE TABLE `CentroDeCusto` (
  `id` int(11) NOT NULL auto_increment,
  `nome` char(50) default NULL,
  `descricao` char(200) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `CentroDeCustoPrevisao`
--

DROP TABLE IF EXISTS `CentroDeCustoPrevisao`;
CREATE TABLE `CentroDeCustoPrevisao` (
  `id` int(11) NOT NULL auto_increment,
  `idCentroDeCusto` int(11) NOT NULL,
  `mes` int(2) NOT NULL,
  `ano` int(4) NOT NULL,
  `valor` decimal(10,2),
  PRIMARY KEY (`id`)
);

--
-- Table structure for table `ContasAPagar`
--

DROP TABLE IF EXISTS `ContasAPagar`;
CREATE TABLE `ContasAPagar` (
  `id` int(11) NOT NULL auto_increment,
  `idPlanoDeContasDetalhes` int(11) NOT NULL default '0',
  `idFornecedor` int(11) NOT NULL default '0',
  `idPop` int(11) NOT NULL default '0',
  `valor` decimal(10,2) NOT NULL default '0.00',
  `valorPago` decimal(10,2) default NULL,
  `dtCadastro` datetime NOT NULL default '0000-00-00 00:00:00',
  `dtVencimento` date default NULL,
  `dtBaixa` datetime default NULL,
  `obs` text NOT NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `PlanoDeContas`
--

DROP TABLE IF EXISTS `PlanoDeContas`;
CREATE TABLE `PlanoDeContas` (
  `id` int(11) NOT NULL auto_increment,
  `nome` char(50) default NULL,
  `descricao` char(200) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `PlanoDeContasSub`
--

DROP TABLE IF EXISTS `PlanoDeContasSub`;
CREATE TABLE `PlanoDeContasSub` (
  `id` int(11) NOT NULL auto_increment,
  `idPlanoDeContas` int(11) NOT NULL default '0',
  `nome` char(50) NOT NULL default '',
  `descricao` char(200) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `PlanoDeContasDetalhes`
--

DROP TABLE IF EXISTS `PlanoDeContasDetalhes`;
CREATE TABLE `PlanoDeContasDetalhes` (
  `id` int(11) NOT NULL auto_increment,
  `idPlanoDeContasSub` int(11) NOT NULL default '0',
  `idCentroDeCusto` int(11) NOT NULL default '0',
  `nome` char(50) NOT NULL default '',
  `descricao` char(200) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

