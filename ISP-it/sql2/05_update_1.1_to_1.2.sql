-- MySQL dump 10.9
--
-- Host: localhost    Database: isp_atual
-- ------------------------------------------------------
-- Server version	4.1.12-Debian_1ubuntu3.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `FluxoDeCaixa`
--

DROP TABLE IF EXISTS `FluxoDeCaixa`;
CREATE TABLE `FluxoDeCaixa` (
  `id` int(11) NOT NULL auto_increment,
  `idConta` int(11) unsigned NOT NULL default '0',
  `tipo` char(1) NOT NULL default '',
  `descricao` char(200) NOT NULL default '',
  `data` datetime NOT NULL default '0000-00-00 00:00:00',
  `valor` decimal(10,2) NOT NULL default '0.00',
  `idPop` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

ALTER TABLE ContraPartida CHANGE idPlanoDeContasSub idPlanoDeContasDetalhes INT(11);
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

