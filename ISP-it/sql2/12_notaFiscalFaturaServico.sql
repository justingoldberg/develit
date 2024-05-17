--
-- Table structure for table `NotaFiscalServico`
--

CREATE TABLE `NotaFiscalServico` (
  `id` bigint(20) NOT NULL auto_increment,
  `numNF` bigint(20) NOT NULL default '0',
  `razao` varchar(255) NOT NULL default '',
  `cnpj` varchar(18) NOT NULL default '',
  `dtEmissao` datetime NOT NULL default '0000-00-00 00:00:00',
  `endereco` varchar(255) NOT NULL default '',
  `cep` varchar(10) NOT NULL default '',
  `cidade` varchar(255) NOT NULL default '',
  `uf` char(2) NOT NULL default '',
  `inscrEst` varchar(18) NOT NULL default '',
  `obs` varchar(255) NOT NULL default '',
  `status` char(1) NOT NULL default 'A',
  `idPessoaTipo` int(11) unsigned NOT NULL default '0',
  `idNatPrestacao` varchar(5) default '',
  `ICMS` double NOT NULL default '0',
  `idPop` int(11) default '1',
  `dtPrestacao` varchar(40) NOT NULL default '', 
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ItensNFServico`
--

CREATE TABLE `ItensNFServico` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `idNFS` bigint(20) unsigned NOT NULL default '0',
  `discriminacao` mediumtext NOT NULL,
  `valor` float(10,2) NOT NULL default '0.00',
  `isento` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  KEY `idNFS` (`idNFS`)
) TYPE=MyISAM;

--
-- Table structure for table `NaturezaPrestacao`
--

CREATE TABLE `NaturezaPrestacao` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `descricao` mediumtext NOT NULL,
  `codigo` int(11) unsigned NOT NULL default '0',
  `status` char(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


INSERT INTO `ParametrosConfig` (`id`, `descricao`, `parametro`, `valor`)
VALUES (NULL, 'Taxa de ICMS padrão para Serviço de Comunicação', 'icms_padrao', '25');

INSERT INTO `TiposImpostos` (`id`, `tipo`, `descricao`)
VALUES (NULL, 'ICMS', 'Imposto Sobre Circulação de Mercadorias e Prestação de Serviços');