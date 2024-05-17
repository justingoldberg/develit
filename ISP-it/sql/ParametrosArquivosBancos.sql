-- MySQL dump 9.11
--
-- Host: localhost    Database: isp
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-2-log

--
-- Table structure for table `ParametrosArquivosBancos`
--

CREATE TABLE `ParametrosArquivosBancos` (
  `id` int(11) NOT NULL auto_increment,
  `idBanco` int(11) NOT NULL default '0',
  `atributo` char(30) default NULL,
  `valor` char(100) default NULL,
  `descricao` char(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `ParametrosArquivosBancos`
--

INSERT INTO `ParametrosArquivosBancos` VALUES (null,0,'arq_remessa_msg1','','mensagem numero 1 a ser exibida no boleto');
INSERT INTO `ParametrosArquivosBancos` VALUES (null,0,'desconto_ate_vencimento','','desconto ao boleto para pagamentos efetuados antes do vencimento');
INSERT INTO `ParametrosArquivosBancos` VALUES (null,0,'arq_remessa_msg2','','parametro a ser exibido na segunda linha disponivel para instrucoes no boleto bancario');

