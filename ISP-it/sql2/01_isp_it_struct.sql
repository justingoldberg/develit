-- MySQL dump 10.9
--
-- Host: localhost    Database: isp_atual
-- ------------------------------------------------------
-- Server version	4.1.12-Debian_1ubuntu3.1-log
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL40' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Aplicacao`
--

DROP TABLE IF EXISTS `Aplicacao`;
CREATE TABLE `Aplicacao` (
  `id` bigint(20) NOT NULL auto_increment,
  `descricao` varchar(100) default '',
  `dtCadastro` datetime default NULL,
  `idUsuario` bigint(20) default '0',
  `status` char(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ArquivoRemessa`
--

DROP TABLE IF EXISTS `ArquivoRemessa`;
CREATE TABLE `ArquivoRemessa` (
  `id` int(11) NOT NULL auto_increment,
  `idArquivo` int(11) NOT NULL default '0',
  `idBanco` int(11) NOT NULL default '0',
  `idFaturamento` int(11) NOT NULL default '0',
  `idUsuario` int(11) NOT NULL default '0',
  `dtArquivo` datetime default NULL,
  `nomeArquivo` varchar(100) default NULL,
  `conteudo` longblob,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ArquivoRetorno`
--

DROP TABLE IF EXISTS `ArquivoRetorno`;
CREATE TABLE `ArquivoRetorno` (
  `id` int(11) NOT NULL auto_increment,
  `idArquivo` int(11) default NULL,
  `idBanco` int(11) default NULL,
  `idUsuario` int(11) default NULL,
  `dtArquivo` datetime default NULL,
  `nomeArquivo` varchar(100) default NULL,
  `conteudo` longblob,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Bancos`
--

DROP TABLE IF EXISTS `Bancos`;
CREATE TABLE `Bancos` (
  `id` int(11) NOT NULL auto_increment,
  `idPessoaTipo` int(11) NOT NULL default '0',
  `numero` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `K_For_FK_Bancos_PessoasTipos` (`idPessoaTipo`)
) TYPE=MyISAM;

--
-- Table structure for table `Bases`
--

DROP TABLE IF EXISTS `Bases`;
CREATE TABLE `Bases` (
  `id` bigint(20) NOT NULL auto_increment,
  `idIfaceServidor` bigint(20) NOT NULL default '0',
  `nome` varchar(255) NOT NULL default '',
  `descricao` varchar(255) NOT NULL default '',
  `status` char(1) default 'A',
  `dtCadastro` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `idIfaceServidor` (`idIfaceServidor`)
) TYPE=MyISAM;

--
-- Table structure for table `Cidades`
--

DROP TABLE IF EXISTS `Cidades`;
CREATE TABLE `Cidades` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(100) default NULL,
  `uf` char(2) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ClienteBanco`
--

DROP TABLE IF EXISTS `ClienteBanco`;
CREATE TABLE `ClienteBanco` (
  `id` bigint(20) NOT NULL auto_increment,
  `idPlanosPessoas` bigint(20) NOT NULL default '0',
  `agencia` varchar(20) NOT NULL default '',
  `contacorrente` varchar(20) NOT NULL default '',
  `identificacao` varchar(14) NOT NULL default '',
  `digAg` char(1) NOT NULL default '',
  `digCC` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idPlanosPessoas` (`idPlanosPessoas`)
) TYPE=MyISAM;

--
-- Table structure for table `ContasAReceber`
--

DROP TABLE IF EXISTS `ContasAReceber`;
CREATE TABLE `ContasAReceber` (
  `id` int(11) NOT NULL auto_increment,
  `idDocumentosGerados` int(11) default NULL,
  `valor` decimal(10,2) default NULL,
  `valorRecebido` decimal(10,2) default NULL,
  `valorJuros` decimal(10,2) default NULL,
  `valorDesconto` decimal(10,2) default NULL,
  `dtCadastro` datetime default NULL,
  `dtVencimento` date default NULL,
  `dtBaixa` datetime default NULL,
  `dtCancelamento` datetime default NULL,
  `obs` text,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idDocumentosGerados` (`idDocumentosGerados`)
) TYPE=MyISAM;

--
-- Table structure for table `ContraPartida`
--

DROP TABLE IF EXISTS `ContraPartida`;
CREATE TABLE `ContraPartida` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `idServicosPlanos` int(11) unsigned NOT NULL default '0',
  `idPessoaTipo` int(11) unsigned NOT NULL default '0',
  `idPlanoDeContasSub` int(11) unsigned NOT NULL default '0',
  `tipoContraPartida` varchar(25) NOT NULL default 'pagar',
  `tipoValor` varchar(15) NOT NULL default 'porcentagem',
  `valor` float(6,2) NOT NULL default '0.00',
  `idVencimento` int(11) NOT NULL default '1',
  `descricao` varchar(255) NOT NULL default '',
  `status` char(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ContraPartidaPadrao`
--

DROP TABLE IF EXISTS `ContraPartidaPadrao`;
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

--
-- Table structure for table `Contratos`
--

DROP TABLE IF EXISTS `Contratos`;
CREATE TABLE `Contratos` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(200) default NULL,
  `descricao` text,
  `dtCadastro` datetime default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ContratosPaginas`
--

DROP TABLE IF EXISTS `ContratosPaginas`;
CREATE TABLE `ContratosPaginas` (
  `id` int(11) NOT NULL auto_increment,
  `idContrato` int(11) default NULL,
  `nomePagina` varchar(200) default NULL,
  `numeroPagina` int(2) default NULL,
  `descricao` text,
  `conteudo` text,
  `dtCadastro` datetime default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ContratosServicosPlanos`
--

DROP TABLE IF EXISTS `ContratosServicosPlanos`;
CREATE TABLE `ContratosServicosPlanos` (
  `id` int(11) NOT NULL auto_increment,
  `idContrato` int(11) default NULL,
  `idServicoPlano` int(11) default NULL,
  `idUsuario` int(11) default NULL,
  `dtEmissao` datetime default NULL,
  `dtAtivacao` datetime default NULL,
  `dtRenovacao` datetime default NULL,
  `dtCancelamento` datetime default NULL,
  `idUsuarioCancelamento` int(11) default NULL,
  `mesValidade` int(3) default NULL,
  `nomeArquivo` varchar(200) default NULL,
  `numeroContrato` varchar(20) default NULL,
  `numeroSequencia` int(11) default NULL,
  `nomePagina` varchar(200) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `DescontoNF`
--

DROP TABLE IF EXISTS `DescontoNF`;
CREATE TABLE `DescontoNF` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `idNF` bigint(20) unsigned NOT NULL default '0',
  `qtde` int(11) NOT NULL default '0',
  `descricao` varchar(255) NOT NULL default '',
  `valorUnit` float(10,2) unsigned NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `idNF` (`idNF`)
) TYPE=MyISAM;

--
-- Table structure for table `DescontosServicosPlanos`
--

DROP TABLE IF EXISTS `DescontosServicosPlanos`;
CREATE TABLE `DescontosServicosPlanos` (
  `id` int(11) NOT NULL auto_increment,
  `idPlano` int(11) NOT NULL default '0',
  `idServicoPlano` int(11) NOT NULL default '0',
  `dtDesconto` date default NULL,
  `dtCancelamento` date default NULL,
  `dtCobranca` date default NULL,
  `valor` decimal(10,2) default NULL,
  `descricao` varchar(200) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `K_For_FK_DescontosServicosPlanos_PlanosPessoas` (`idPlano`),
  KEY `K_For_FK_DescontosServicosPlanos_Servicos` (`idServicoPlano`)
) TYPE=MyISAM;

--
-- Table structure for table `DocumentosGerados`
--

DROP TABLE IF EXISTS `DocumentosGerados`;
CREATE TABLE `DocumentosGerados` (
  `id` int(11) NOT NULL auto_increment,
  `idFaturamento` int(11) default NULL,
  `seqGeracao` int(11) NOT NULL default '0',
  `seqDocumento` int(11) NOT NULL default '0',
  `idPessoaTipo` int(11) NOT NULL default '0',
  `idUsuario` int(11) default NULL,
  `dtGeracao` datetime default NULL,
  `dtAtivacao` datetime default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idPessoaTipo` (`idPessoaTipo`)
) TYPE=MyISAM;

--
-- Table structure for table `DocumentosPessoasTipos`
--

DROP TABLE IF EXISTS `DocumentosPessoasTipos`;
CREATE TABLE `DocumentosPessoasTipos` (
  `idPessoa` int(11) NOT NULL default '0',
  `idTipo` int(11) NOT NULL default '0',
  `documento` varchar(60) default NULL,
  `dtCadastro` datetime default NULL,
  KEY `K_For_FK_DocumentosPessoasTipos_Pessoas` (`idPessoa`),
  KEY `K_For_FK_DocumentosPessoasTipos_TiposDocumentos` (`idTipo`)
) TYPE=MyISAM;

--
-- Table structure for table `Dominios`
--

DROP TABLE IF EXISTS `Dominios`;
CREATE TABLE `Dominios` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(200) default NULL,
  `descricao` text,
  `dtCadastro` datetime default NULL,
  `dtAtivacao` datetime default NULL,
  `dtBloqueio` datetime default NULL,
  `dtCongelamento` datetime default NULL,
  `status` char(1) default NULL,
  `padrao` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `DominiosParametros`
--

DROP TABLE IF EXISTS `DominiosParametros`;
CREATE TABLE `DominiosParametros` (
  `id` int(11) NOT NULL auto_increment,
  `idDominio` int(11) default NULL,
  `idModulo` int(11) default NULL,
  `idParametro` int(11) default NULL,
  `valor` varchar(60) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `DominiosServicosPlanos`
--

DROP TABLE IF EXISTS `DominiosServicosPlanos`;
CREATE TABLE `DominiosServicosPlanos` (
  `id` int(11) NOT NULL auto_increment,
  `idServicosPlanos` int(11) default NULL,
  `idDominio` int(11) default NULL,
  `idPessoasTipos` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Email`
--

DROP TABLE IF EXISTS `Email`;
CREATE TABLE `Email` (
  `id` int(11) NOT NULL auto_increment,
  `idPessoaTipo` int(11) default NULL,
  `idDominio` int(11) default NULL,
  `login` varchar(200) default NULL,
  `senha` varchar(200) default NULL,
  `senhaTexto` varchar(200) default NULL,
  `status` char(1) default NULL,
  `idServicosPlanos` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `EmailAlias`
--

DROP TABLE IF EXISTS `EmailAlias`;
CREATE TABLE `EmailAlias` (
  `id` int(11) NOT NULL auto_increment,
  `idEmail` int(11) default NULL,
  `alias` varchar(200) default NULL,
  `dtCadastro` datetime default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `EmailAutoReply`
--

DROP TABLE IF EXISTS `EmailAutoReply`;
CREATE TABLE `EmailAutoReply` (
  `idEmail` int(11) NOT NULL default '0',
  `texto` text,
  `dtCadastro` datetime default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`idEmail`)
) TYPE=MyISAM;

--
-- Table structure for table `EmailConfig`
--

DROP TABLE IF EXISTS `EmailConfig`;
CREATE TABLE `EmailConfig` (
  `id` int(11) NOT NULL auto_increment,
  `idEmail` int(11) default NULL,
  `idParametro` int(11) default NULL,
  `valor` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `EmailForward`
--

DROP TABLE IF EXISTS `EmailForward`;
CREATE TABLE `EmailForward` (
  `idEmail` int(11) NOT NULL default '0',
  `forward` text,
  `copia` char(1) default NULL,
  `dtCadastro` datetime default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`idEmail`)
) TYPE=MyISAM;

--
-- Table structure for table `Enderecos`
--

DROP TABLE IF EXISTS `Enderecos`;
CREATE TABLE `Enderecos` (
  `idPessoaTipo` int(11) NOT NULL default '0',
  `idTipo` int(11) NOT NULL default '0',
  `idCidade` int(11) NOT NULL default '0',
  `endereco` varchar(200) default NULL,
  `complemento` varchar(200) default NULL,
  `bairro` varchar(100) default NULL,
  `cep` varchar(10) default NULL,
  `pais` varchar(100) default NULL,
  `caixa_postal` varchar(20) default NULL,
  `ddd_fone1` char(3) default NULL,
  `fone1` varchar(30) default NULL,
  `ddd_fone2` char(3) default NULL,
  `fone2` varchar(30) default NULL,
  `ddd_fax` char(3) default NULL,
  `fax` varchar(30) default NULL,
  `email` varchar(100) default NULL,
  KEY `K_For_FK_Enderecos_Cidades` (`idCidade`),
  KEY `K_For_FK_Enderecos_TipoPessoa` (`idPessoaTipo`),
  KEY `K_For_FK_Enderecos_TiposEnderecos` (`idTipo`)
) TYPE=MyISAM;

--
-- Table structure for table `Equipamento`
--

DROP TABLE IF EXISTS `Equipamento`;
CREATE TABLE `Equipamento` (
  `id` bigint(20) NOT NULL auto_increment,
  `idTipo` bigint(20) default NULL,
  `nome` varchar(255) default NULL,
  `descricao` mediumtext,
  `status` char(1) default NULL,
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idTipo` (`idTipo`)
) TYPE=MyISAM;

--
-- Table structure for table `EquipamentoEquiptoCaracteristica`
--

DROP TABLE IF EXISTS `EquipamentoEquiptoCaracteristica`;
CREATE TABLE `EquipamentoEquiptoCaracteristica` (
  `id` bigint(20) NOT NULL auto_increment,
  `idEquipamento` bigint(20) default NULL,
  `idEquiptoCaracteristica` bigint(20) default NULL,
  `valor` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idEquipamento` (`idEquipamento`),
  KEY `idEquiptoCaracteristica` (`idEquiptoCaracteristica`)
) TYPE=MyISAM;

--
-- Table structure for table `EquiptoCaracteristica`
--

DROP TABLE IF EXISTS `EquiptoCaracteristica`;
CREATE TABLE `EquiptoCaracteristica` (
  `id` bigint(20) NOT NULL auto_increment,
  `nome` varchar(255) default NULL,
  `descricao` mediumtext,
  `status` char(1) default NULL,
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `EquiptoTipo`
--

DROP TABLE IF EXISTS `EquiptoTipo`;
CREATE TABLE `EquiptoTipo` (
  `id` bigint(20) NOT NULL auto_increment,
  `nome` varchar(255) default NULL,
  `descricao` mediumtext,
  `status` char(1) default NULL,
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `EquiptoTipoEquiptoCaracteristica`
--

DROP TABLE IF EXISTS `EquiptoTipoEquiptoCaracteristica`;
CREATE TABLE `EquiptoTipoEquiptoCaracteristica` (
  `id` bigint(20) NOT NULL auto_increment,
  `idEquiptoTipo` bigint(20) default NULL,
  `idEquiptoCaracteristica` bigint(20) default NULL,
  `valor` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idEquiptoTipo` (`idEquiptoTipo`),
  KEY `idEquiptoCaracteristica` (`idEquiptoCaracteristica`)
) TYPE=MyISAM;

--
-- Table structure for table `Faturamentos`
--

DROP TABLE IF EXISTS `Faturamentos`;
CREATE TABLE `Faturamentos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(200) default NULL,
  `data` datetime default NULL,
  `idServico` int(11) default NULL,
  `idPOP` int(11) default NULL,
  `idFormaCobranca` int(11) default NULL,
  `idVencimento` int(11) default NULL,
  `mes` int(2) NOT NULL default '0',
  `ano` int(4) NOT NULL default '0',
  `status` char(1) default NULL,
  `remessa` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `FormaCobranca`
--

DROP TABLE IF EXISTS `FormaCobranca`;
CREATE TABLE `FormaCobranca` (
  `id` int(11) NOT NULL auto_increment,
  `idBanco` int(11) NOT NULL default '0',
  `descricao` varchar(200) default NULL,
  `titular` varchar(200) default NULL,
  `cnpj` varchar(20) default NULL,
  `convenio` varchar(100) default NULL,
  `agencia` varchar(10) default NULL,
  `digAgencia` char(2) default NULL,
  `conta` varchar(20) default NULL,
  `digConta` char(2) default NULL,
  `idTipoCarteira` bigint(20) NOT NULL default '0',
  `arquivoremessa` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `K_For_FK_FormaCobranca_Bancos` (`idBanco`),
  KEY `idTipoCarteira` (`idTipoCarteira`)
) TYPE=MyISAM;

--
-- Table structure for table `Grupos`
--

DROP TABLE IF EXISTS `Grupos`;
CREATE TABLE `Grupos` (
  `id` int(9) NOT NULL auto_increment,
  `nome` varchar(50) NOT NULL default '',
  `admin` char(1) default NULL,
  `incluir` char(1) default NULL,
  `excluir` char(1) default NULL,
  `visualizar` char(1) default NULL,
  `alterar` char(1) default NULL,
  `abrir` char(1) default NULL,
  `fechar` char(1) default NULL,
  `comentar` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `GruposServicos`
--

DROP TABLE IF EXISTS `GruposServicos`;
CREATE TABLE `GruposServicos` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(60) default NULL,
  `descricao` text,
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ImpostosPessoas`
--

DROP TABLE IF EXISTS `ImpostosPessoas`;
CREATE TABLE `ImpostosPessoas` (
  `id` int(11) NOT NULL auto_increment,
  `idTipoImposto` int(11) NOT NULL default '0',
  `idPessoa` int(11) NOT NULL default '0',
  `valor` double NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Interfaces`
--

DROP TABLE IF EXISTS `Interfaces`;
CREATE TABLE `Interfaces` (
  `id` bigint(20) NOT NULL auto_increment,
  `idServidor` bigint(20) default NULL,
  `nome` varchar(255) NOT NULL default '',
  `iface` varchar(4) NOT NULL default '',
  `status` char(1) NOT NULL default 'A',
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idServidor` (`idServidor`)
) TYPE=MyISAM;

--
-- Table structure for table `ItensNF`
--

DROP TABLE IF EXISTS `ItensNF`;
CREATE TABLE `ItensNF` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `idNF` bigint(20) unsigned NOT NULL default '0',
  `qtde` int(11) NOT NULL default '0',
  `unid` varchar(5) NOT NULL default '',
  `descricao` mediumtext NOT NULL,
  `valorUnit` float(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `idNF` (`idNF`)
) TYPE=MyISAM;

--
-- Table structure for table `MaodeObra`
--

DROP TABLE IF EXISTS `MaodeObra`;
CREATE TABLE `MaodeObra` (
  `id` bigint(20) NOT NULL auto_increment,
  `descricao` varchar(100) default '',
  `dtCadastro` datetime default NULL,
  `idUsuario` bigint(20) default '0',
  `status` char(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Modulos`
--

DROP TABLE IF EXISTS `Modulos`;
CREATE TABLE `Modulos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(200) default NULL,
  `modulo` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `NotaFiscal`
--

DROP TABLE IF EXISTS `NotaFiscal`;
CREATE TABLE `NotaFiscal` (
  `id` bigint(20) NOT NULL auto_increment,
  `numNF` bigint(20) NOT NULL default '0',
  `razao` varchar(255) NOT NULL default '',
  `cnpj` varchar(18) NOT NULL default '',
  `dtEmissao` datetime NOT NULL default '0000-00-00 00:00:00',
  `endereco` varchar(255) NOT NULL default '',
  `bairro` varchar(255) NOT NULL default '',
  `cep` varchar(10) NOT NULL default '',
  `cidade` varchar(255) NOT NULL default '',
  `fone` varchar(14) NOT NULL default '',
  `uf` char(2) NOT NULL default '',
  `inscrEst` varchar(18) NOT NULL default '',
  `obs` varchar(255) NOT NULL default '',
  `status` char(1) NOT NULL default 'A',
  `idPessoaTipo` int(11) unsigned NOT NULL default '0',
  `natOper` varchar(5) default '',
  `ISSQN` double NOT NULL default '0',
  `idPop` int(11) default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Ocorrencias`
--

DROP TABLE IF EXISTS `Ocorrencias`;
CREATE TABLE `Ocorrencias` (
  `id` int(11) NOT NULL auto_increment,
  `idPessoaTipo` int(11) default NULL,
  `idServicoPlano` int(11) default NULL,
  `idUsuario` int(11) default NULL,
  `idPrioridade` int(11) default NULL,
  `data` datetime default NULL,
  `nome` varchar(200) default NULL,
  `descricao` text,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idPessoaTipo` (`idPessoaTipo`),
  KEY `idServicoPlano` (`idServicoPlano`),
  KEY `idUsuario` (`idUsuario`),
  KEY `idPrioridade` (`idPrioridade`)
) TYPE=MyISAM;

--
-- Table structure for table `OcorrenciasComentarios`
--

DROP TABLE IF EXISTS `OcorrenciasComentarios`;
CREATE TABLE `OcorrenciasComentarios` (
  `id` int(11) NOT NULL auto_increment,
  `idOcorrencia` int(11) default NULL,
  `idUsuario` int(11) default NULL,
  `status` char(1) default NULL,
  `data` datetime default NULL,
  `texto` text,
  PRIMARY KEY  (`id`),
  KEY `idOcorrencia` (`idOcorrencia`),
  KEY `idUsuario` (`idUsuario`)
) TYPE=MyISAM;

--
-- Table structure for table `OcorrenciasOrdemServico`
--

DROP TABLE IF EXISTS `OcorrenciasOrdemServico`;
CREATE TABLE `OcorrenciasOrdemServico` (
  `id` int(11) NOT NULL auto_increment,
  `idOcorrencia` int(11) default NULL,
  `idOrdemServico` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `OcorrenciasTicket`
--

DROP TABLE IF EXISTS `OcorrenciasTicket`;
CREATE TABLE `OcorrenciasTicket` (
  `id` int(11) NOT NULL auto_increment,
  `idOcorrencia` int(11) default NULL,
  `idTicket` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `OrdemServico`
--

DROP TABLE IF EXISTS `OrdemServico`;
CREATE TABLE `OrdemServico` (
  `id` int(11) NOT NULL auto_increment,
  `idPessoaTipo` int(11) default NULL,
  `idServico` int(11) default NULL,
  `idUsuario` int(11) default NULL,
  `idPrioridade` int(11) default NULL,
  `dtCriacao` datetime default NULL,
  `dtExecusao` datetime default NULL,
  `dtConclusao` datetime default NULL,
  `nome` varchar(200) default NULL,
  `descricao` text,
  `status` char(1) default NULL,
  `valor` float(12,2) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `OrdemServicoComentarios`
--

DROP TABLE IF EXISTS `OrdemServicoComentarios`;
CREATE TABLE `OrdemServicoComentarios` (
  `id` int(11) NOT NULL auto_increment,
  `idOrdemServico` int(11) default NULL,
  `idUsuario` int(11) default NULL,
  `status` char(1) default NULL,
  `data` datetime default NULL,
  `texto` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `OrdemServicoDetalhe`
--

DROP TABLE IF EXISTS `OrdemServicoDetalhe`;
CREATE TABLE `OrdemServicoDetalhe` (
  `id` int(11) NOT NULL default '0',
  `idOrdemServico` int(11) NOT NULL default '0',
  `idUsuario` int(11) NOT NULL default '0',
  `idProduto` int(11) NOT NULL default '0',
  `idMaodeObra` int(11) NOT NULL default '0',
  `idAplicacao` int(11) NOT NULL default '0',
  `quantidade` double NOT NULL default '0',
  `valor` double NOT NULL default '0',
  `dtCadastro` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `idOrdemServico` (`idOrdemServico`),
  KEY `idUsuario` (`idUsuario`),
  KEY `idProduto` (`idProduto`),
  KEY `idMaodeObra` (`idMaodeObra`),
  KEY `idAplicacao` (`idAplicacao`)
) TYPE=MyISAM;

--
-- Table structure for table `Parametros`
--

DROP TABLE IF EXISTS `Parametros`;
CREATE TABLE `Parametros` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(200) default NULL,
  `tipo` varchar(20) default 'numero',
  `idUnidade` int(11) NOT NULL default '0',
  `parametro` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `K_For_FK_Parametros_Unidades` (`idUnidade`)
) TYPE=MyISAM;

--
-- Table structure for table `ParametrosArquivosBancos`
--

DROP TABLE IF EXISTS `ParametrosArquivosBancos`;
CREATE TABLE `ParametrosArquivosBancos` (
  `id` int(11) NOT NULL auto_increment,
  `idBanco` int(11) NOT NULL default '0',
  `atributo` char(30) default NULL,
  `valor` char(100) default NULL,
  `descricao` char(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ParametrosConfig`
--

DROP TABLE IF EXISTS `ParametrosConfig`;
CREATE TABLE `ParametrosConfig` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(200) default NULL,
  `parametro` varchar(20) default NULL,
  `valor` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ParametrosModulos`
--

DROP TABLE IF EXISTS `ParametrosModulos`;
CREATE TABLE `ParametrosModulos` (
  `idModulo` int(11) NOT NULL default '0',
  `idParametro` int(11) NOT NULL default '0',
  KEY `K_For_FK_ParametrosModulos_Modulos` (`idModulo`),
  KEY `K_For_FK_ParametrosModulos_Parametros` (`idParametro`)
) TYPE=MyISAM;

--
-- Table structure for table `Pessoas`
--

DROP TABLE IF EXISTS `Pessoas`;
CREATE TABLE `Pessoas` (
  `id` int(11) NOT NULL auto_increment,
  `tipoPessoa` char(1) default NULL,
  `nome` varchar(200) default NULL,
  `razao` varchar(200) default NULL,
  `site` varchar(100) default NULL,
  `mail` varchar(100) default NULL,
  `dtNascimento` datetime default NULL,
  `dtCadastro` datetime default NULL,
  `idPOP` int(11) default NULL,
  `contato` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `PessoasTipos`
--

DROP TABLE IF EXISTS `PessoasTipos`;
CREATE TABLE `PessoasTipos` (
  `id` int(11) NOT NULL auto_increment,
  `idPessoa` int(11) NOT NULL default '0',
  `idTipo` int(11) NOT NULL default '0',
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `K_For_FK_PessoasTipos_Pessoas` (`idPessoa`)
) TYPE=MyISAM;

--
-- Table structure for table `PlanosDocumentosGerados`
--

DROP TABLE IF EXISTS `PlanosDocumentosGerados`;
CREATE TABLE `PlanosDocumentosGerados` (
  `id` int(11) NOT NULL auto_increment,
  `idDocumentoGerado` int(11) NOT NULL default '0',
  `idPlano` int(11) default NULL,
  `idFormaCobranca` int(11) default NULL,
  `idVencimento` int(11) default NULL,
  `dtVencimento` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `idDocumentoGerado` (`idDocumentoGerado`)
) TYPE=MyISAM;

--
-- Table structure for table `PlanosPessoas`
--

DROP TABLE IF EXISTS `PlanosPessoas`;
CREATE TABLE `PlanosPessoas` (
  `id` int(11) NOT NULL auto_increment,
  `idPessoaTipo` int(11) NOT NULL default '0',
  `idVencimento` int(11) NOT NULL default '0',
  `idFormaCobranca` int(11) NOT NULL default '0',
  `dtCadastro` datetime default NULL,
  `dtCancelamento` datetime default NULL,
  `nome` varchar(200) default NULL,
  `especial` char(1) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idVencimento` (`idVencimento`)
) TYPE=MyISAM;

--
-- Table structure for table `Pop`
--

DROP TABLE IF EXISTS `Pop`;
CREATE TABLE `Pop` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(100) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `PopCidade`
--

DROP TABLE IF EXISTS `PopCidade`;
CREATE TABLE `PopCidade` (
  `idPOP` int(11) NOT NULL default '0',
  `idCidade` int(11) NOT NULL default '0',
  `principal` char(1) default NULL
) TYPE=MyISAM;

--
-- Table structure for table `Prioridades`
--

DROP TABLE IF EXISTS `Prioridades`;
CREATE TABLE `Prioridades` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(100) default NULL,
  `texto` text,
  `cor` varchar(20) default NULL,
  `valor` smallint(5) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Produtos`
--

DROP TABLE IF EXISTS `Produtos`;
CREATE TABLE `Produtos` (
  `id` bigint(20) NOT NULL auto_increment,
  `nome` varchar(100) default '',
  `descricao` varchar(255) default '',
  `valor` double default '0',
  `dtCadastro` datetime default NULL,
  `idUsuario` bigint(20) default '0',
  `status` char(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `RadiusGrupos`
--

DROP TABLE IF EXISTS `RadiusGrupos`;
CREATE TABLE `RadiusGrupos` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(200) default NULL,
  `descricao` text,
  `horas` int(6) default NULL,
  `ilimitado` char(1) default NULL,
  `status` char(1) default NULL,
  `comando` text,
  `dtCadastro` datetime default NULL,
  `dtAtivacao` datetime default NULL,
  `dtInativacao` datetime default NULL,
  `dtCancelamento` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `RadiusUsuarios`
--

DROP TABLE IF EXISTS `RadiusUsuarios`;
CREATE TABLE `RadiusUsuarios` (
  `id` int(11) NOT NULL auto_increment,
  `idGrupo` int(11) default NULL,
  `login` varchar(200) default NULL,
  `senha` varchar(200) default NULL,
  `senha_texto` varchar(200) default NULL,
  `dtCadastro` datetime default NULL,
  `dtAtivacao` datetime default NULL,
  `dtInativacao` datetime default NULL,
  `dtCancelamento` datetime default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `RadiusUsuariosPessoasTipos`
--

DROP TABLE IF EXISTS `RadiusUsuariosPessoasTipos`;
CREATE TABLE `RadiusUsuariosPessoasTipos` (
  `id` int(11) NOT NULL auto_increment,
  `idPessoasTipos` int(11) default NULL,
  `idRadiusUsuarios` int(11) default NULL,
  `idServicosPlanos` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `RadiusUsuariosTelefones`
--

DROP TABLE IF EXISTS `RadiusUsuariosTelefones`;
CREATE TABLE `RadiusUsuariosTelefones` (
  `id` int(11) NOT NULL auto_increment,
  `idRadiusUsuarioPessoaTipo` int(11) default NULL,
  `telefone` varchar(20) default NULL,
  `dtCadastro` datetime default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ServicoIVR`
--

DROP TABLE IF EXISTS `ServicoIVR`;
CREATE TABLE `ServicoIVR` (
  `id` bigint(20) NOT NULL auto_increment,
  `idServicoPlano` bigint(20) NOT NULL default '0',
  `idBase` bigint(20) NOT NULL default '0',
  `nome` varchar(255) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `mask` varchar(15) NOT NULL default '',
  `mac` varchar(50) NOT NULL default '',
  `gw` varchar(15) NOT NULL default '',
  `dns1` varchar(15) NOT NULL default '',
  `dns2` varchar(15) NOT NULL default '',
  `status` char(1) default 'A',
  `so` varchar(255) NOT NULL default '',
  `obs` mediumtext,
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idServicoPlano` (`idServicoPlano`),
  KEY `idBase` (`idBase`)
) TYPE=MyISAM;

--
-- Table structure for table `Servicos`
--

DROP TABLE IF EXISTS `Servicos`;
CREATE TABLE `Servicos` (
  `id` int(11) NOT NULL auto_increment,
  `idTipoCobranca` int(11) NOT NULL default '0',
  `nome` varchar(200) default NULL,
  `descricao` text,
  `valor` decimal(10,2) default NULL,
  `idStatusPadrao` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ServicosAdicionais`
--

DROP TABLE IF EXISTS `ServicosAdicionais`;
CREATE TABLE `ServicosAdicionais` (
  `id` int(11) NOT NULL auto_increment,
  `idPlano` int(11) NOT NULL default '0',
  `idServicoPlano` int(11) NOT NULL default '0',
  `idTipoServicoAdicional` int(11) default NULL,
  `nome` varchar(200) default NULL,
  `valor` decimal(10,2) default NULL,
  `dtCadastro` datetime default NULL,
  `dtVencimento` date default NULL,
  `dtCancelamento` date default NULL,
  `dtCobranca` date default NULL,
  `dtEspecial` char(1) default NULL,
  `adicionalFaturamento` char(1) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `K_For_FK_ServicosAdicionais_PlanosPessoas` (`idPlano`),
  KEY `K_For_FK_ServicosAdicionais_Servicos` (`idServicoPlano`)
) TYPE=MyISAM;

--
-- Table structure for table `ServicosContratos`
--

DROP TABLE IF EXISTS `ServicosContratos`;
CREATE TABLE `ServicosContratos` (
  `id` int(11) NOT NULL auto_increment,
  `idServico` int(11) default NULL,
  `idContrato` int(11) default NULL,
  `dtCadastro` datetime default NULL,
  `mesValidade` int(3) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `ServicosGrupos`
--

DROP TABLE IF EXISTS `ServicosGrupos`;
CREATE TABLE `ServicosGrupos` (
  `idGrupos` int(11) NOT NULL default '0',
  `idServico` int(11) NOT NULL default '0',
  KEY `idServico` (`idServico`),
  KEY `idGrupos` (`idGrupos`)
) TYPE=MyISAM;

--
-- Table structure for table `ServicosParametros`
--

DROP TABLE IF EXISTS `ServicosParametros`;
CREATE TABLE `ServicosParametros` (
  `idServico` int(11) NOT NULL default '0',
  `idParametro` int(11) NOT NULL default '0',
  `valor` varchar(20) default NULL
) TYPE=MyISAM;

--
-- Table structure for table `ServicosPlanos`
--

DROP TABLE IF EXISTS `ServicosPlanos`;
CREATE TABLE `ServicosPlanos` (
  `id` int(11) NOT NULL auto_increment,
  `idPlano` int(11) NOT NULL default '0',
  `idServico` int(11) NOT NULL default '0',
  `valor` decimal(10,2) default NULL,
  `dtCadastro` datetime default NULL,
  `dtAtivacao` datetime default NULL,
  `dtInativacao` datetime default NULL,
  `dtCancelamento` datetime default NULL,
  `idStatus` int(11) NOT NULL default '0',
  `diasTrial` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `K_For_FK_ServicosPlanos_PlanosPessoas` (`idPlano`),
  KEY `K_For_FK_ServicosPlanos_Servicos` (`idServico`),
  KEY `idServico` (`idServico`)
) TYPE=MyISAM;

--
-- Table structure for table `ServicosPlanosDocumentosGerados`
--

DROP TABLE IF EXISTS `ServicosPlanosDocumentosGerados`;
CREATE TABLE `ServicosPlanosDocumentosGerados` (
  `id` int(11) NOT NULL auto_increment,
  `idPlanoDocumentoGerado` int(11) default NULL,
  `idServicosPlanos` int(11) default NULL,
  `valor` decimal(10,2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idPlanoDocumentoGerado` (`idPlanoDocumentoGerado`),
  KEY `idServicosPlanos` (`idServicosPlanos`)
) TYPE=MyISAM;

--
-- Table structure for table `ServicosRadiusGrupos`
--

DROP TABLE IF EXISTS `ServicosRadiusGrupos`;
CREATE TABLE `ServicosRadiusGrupos` (
  `id` int(11) NOT NULL auto_increment,
  `idRadiusGrupos` int(11) default NULL,
  `idServicos` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Servidores`
--

DROP TABLE IF EXISTS `Servidores`;
CREATE TABLE `Servidores` (
  `id` bigint(20) NOT NULL auto_increment,
  `nome` varchar(255) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `status` char(1) NOT NULL default 'A',
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `StatusServicosPlanos`
--

DROP TABLE IF EXISTS `StatusServicosPlanos`;
CREATE TABLE `StatusServicosPlanos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(60) default NULL,
  `cobranca` char(1) default NULL,
  `status` char(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `TipoCarteira`
--

DROP TABLE IF EXISTS `TipoCarteira`;
CREATE TABLE `TipoCarteira` (
  `id` bigint(20) NOT NULL auto_increment,
  `nome` varchar(50) NOT NULL default '',
  `descricao` varchar(255) NOT NULL default '',
  `valor` char(1) NOT NULL default 'R',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `TipoCobranca`
--

DROP TABLE IF EXISTS `TipoCobranca`;
CREATE TABLE `TipoCobranca` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(200) default NULL,
  `proporcional` char(1) default NULL,
  `forma` varchar(10) default NULL,
  `tipo` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `TipoPessoa`
--

DROP TABLE IF EXISTS `TipoPessoa`;
CREATE TABLE `TipoPessoa` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(60) default NULL,
  `valor` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `TipoServicoAdicional`
--

DROP TABLE IF EXISTS `TipoServicoAdicional`;
CREATE TABLE `TipoServicoAdicional` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(200) default NULL,
  `descricao` text,
  `dtCadastro` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `TiposDocumentos`
--

DROP TABLE IF EXISTS `TiposDocumentos`;
CREATE TABLE `TiposDocumentos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(60) default NULL,
  `valor` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `TiposEnderecos`
--

DROP TABLE IF EXISTS `TiposEnderecos`;
CREATE TABLE `TiposEnderecos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(60) default NULL,
  `valor` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `TiposImpostos`
--

DROP TABLE IF EXISTS `TiposImpostos`;
CREATE TABLE `TiposImpostos` (
  `id` int(11) NOT NULL auto_increment,
  `tipo` varchar(100) NOT NULL default '',
  `descricao` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Unidades`
--

DROP TABLE IF EXISTS `Unidades`;
CREATE TABLE `Unidades` (
  `id` int(11) NOT NULL auto_increment,
  `unidade` varchar(20) default NULL,
  `descricao` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `Usuarios`
--

DROP TABLE IF EXISTS `Usuarios`;
CREATE TABLE `Usuarios` (
  `id` int(9) NOT NULL auto_increment,
  `login` varchar(20) NOT NULL default '',
  `senha` varchar(40) NOT NULL default '',
  `status` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `UsuariosGrupos`
--

DROP TABLE IF EXISTS `UsuariosGrupos`;
CREATE TABLE `UsuariosGrupos` (
  `idUsuario` int(9) NOT NULL default '0',
  `idGrupo` int(9) NOT NULL default '0',
  PRIMARY KEY  (`idUsuario`,`idGrupo`)
) TYPE=MyISAM;

--
-- Table structure for table `Vencimentos`
--

DROP TABLE IF EXISTS `Vencimentos`;
CREATE TABLE `Vencimentos` (
  `id` int(11) NOT NULL auto_increment,
  `descricao` varchar(200) default NULL,
  `diaVencimento` char(2) default NULL,
  `diaFaturamento` char(2) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `tmpGrupoServico`
--

DROP TABLE IF EXISTS `tmpGrupoServico`;
CREATE TABLE `tmpGrupoServico` (
  `id` bigint(20) NOT NULL auto_increment,
  `grupo` bigint(20) default NULL,
  `cliente` varchar(255) default NULL,
  `valor` double default NULL,
  `recebido` double default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

