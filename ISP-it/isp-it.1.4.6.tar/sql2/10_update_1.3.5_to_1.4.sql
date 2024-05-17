DROP TABLE IF EXISTS `Produtos`;
CREATE TABLE `Produtos` (
  `id` bigint(20) NOT NULL auto_increment,
  `nome` varchar(100) NOT NULL default '',
  `idUnidade` int(11) NOT NULL default '0',
  `marca` varchar(100) NOT NULL default '',
  `modelo` varchar(100) NOT NULL default '',
  `qtdeMinima` int(11) NOT NULL default '0',
  `valorBase` decimal(10,2) NOT NULL default '0.00',
  `valorVenda` decimal(10,2) NOT NULL default '0.00',
  `status` enum('A','I') NOT NULL default 'A',
  `fracionavel` enum('S','N') NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `ProdutosFracionado`;
CREATE TABLE `ProdutosFracionado` (
  `id` bigint(20) NOT NULL auto_increment,
  `idProduto` bigint(20) NOT NULL default '0',
  `idProdutoFracionado` bigint(20) NOT NULL default '0',
  `quantidade` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `ProdutosEstoque`;
CREATE TABLE `ProdutosEstoque` (
  `id` bigint(20) NOT NULL auto_increment,
  `idProduto` bigint(20) NOT NULL default '0',
  `idPop` int(11) NOT NULL default '0',
  `quantidade` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `ProdutoComposto`;
CREATE TABLE `ProdutoComposto` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(100) NOT NULL default '',
  `status` enum('A','I') NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `ItensProdutoComposto`;
CREATE TABLE `ItensProdutoComposto` (
  `id` int(11) NOT NULL auto_increment,
  `idProdutoComposto` bigint(20) NOT NULL default '0',
  `idProduto` bigint(20) NOT NULL default '0',
  `quantidade` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `MovimentoEstoque`;
CREATE TABLE `MovimentoEstoque` (
  `id` int(11) NOT NULL auto_increment,
  `idNFE` int(11) NOT NULL default '0',
  `idOrdemServico` int(11) NOT NULL default '0',
  `idRequisicao` int(11) NOT NULL default '0',
  `descricao` varchar(255) NOT NULL default '',
  `tipo` enum('E','S','R') NOT NULL default 'E',
  `data` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `ItensMovimentoEstoque`;
CREATE TABLE `ItensMovimentoEstoque` (
  `id` int(11) NOT NULL auto_increment,
  `idMovimentoEstoque` int(11) NOT NULL default '0',
  `idProduto` int(11) NOT NULL default '0',
  `quantidade` decimal(10,2) NOT NULL default '0.00',
  `valor` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `EntradaNotaFiscal`;
CREATE TABLE `EntradaNotaFiscal` (
  `id` int(11) NOT NULL auto_increment,
  `idFornecedor` int(11) NOT NULL default '0',
  `idUsuario` int(11) NOT NULL default '0',
  `idPop` int(11) NOT NULL default '0',
  `dataEmissao` datetime NOT NULL default '0000-00-00 00:00:00',
  `dataLancamento` datetime NOT NULL default '0000-00-00 00:00:00',
  `numNF` bigint(20) NOT NULL default '0',
  `lancarCP` enum('S','N') NOT NULL default 'S',
  `status` enum('P','B','C') NOT NULL default 'P',
  PRIMARY KEY  (`id`)
)TYPE=MyISAM;

DROP TABLE IF EXISTS `RequisicaoRetorno`;
CREATE TABLE `RequisicaoRetorno` (
  `id` int(11) NOT NULL auto_increment,
  `idUsuario` int(11) NOT NULL default '0',
  `idPop` int(11) NOT NULL default '0',
  `responsavel` varchar(100) NOT NULL default '',
  `descricao` varchar(255) NOT NULL default '',
  `data` datetime NOT NULL default '0000-00-00 00:00:00',
  `tipo` enum('E','S') NOT NULL default 'E',
  `status` enum('P','B','C') NOT NULL default 'P',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `OrdemServico`;
CREATE TABLE `OrdemServico` (
  `id` int(11) NOT NULL auto_increment,
  `idUsuario` int(11) NOT NULL default '0',
  `idCliente` int(11) NOT NULL default '0',
  `idServicoPlano` int(11) NOT NULL default '0',
  `idPop` int(11) NOT NULL default '0',
  `descricao` varchar(255) NOT NULL default '',
  `responsavel` varchar(100) NOT NULL default '',
  `data` datetime NOT NULL default '0000-00-00 00:00:00',
  `dataPrevisao` datetime NOT NULL default '0000-00-00 00:00:00',
  `dataExecucao` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` enum('P','B','C') NOT NULL default 'P',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

ALTER TABLE `Unidades` ADD COLUMN `estoque` ENUM('S','N') default 'N';
ALTER TABLE `ContasAPagar` ADD COLUMN `idNFE` INT(11) DEFAULT '0';

INSERT INTO `ParametrosConfig` (`id`, `descricao`, `parametro`, `valor`)
VALUES (NULL, 'Libera estoque negativo', 'estoque_negativo', 'N');