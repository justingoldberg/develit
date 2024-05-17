-- MySQL dump 9.11
--
-- Host: localhost    Database: base_tdkom
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

--
-- Table structure for table `StatusServicosPlanos`
--

CREATE TABLE `StatusServicosPlanos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(60) default NULL,
  `cobranca` char(1) default NULL,
  `status` char(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `StatusServicosPlanos`
--

INSERT INTO `StatusServicosPlanos` VALUES (1,'Aguardando Assinatura de Contrato','N','N');
INSERT INTO `StatusServicosPlanos` VALUES (2,'Aguardando Instalação','N','N');
INSERT INTO `StatusServicosPlanos` VALUES (3,'Aguardando Ativação','N','N');
INSERT INTO `StatusServicosPlanos` VALUES (4,'Serviço Habilitado','S','A');
INSERT INTO `StatusServicosPlanos` VALUES (5,'Serviço Suspenso','S','A');
INSERT INTO `StatusServicosPlanos` VALUES (6,'Serviço Inativo','N','I');
INSERT INTO `StatusServicosPlanos` VALUES (7,'Serviço Cancelado','N','C');

