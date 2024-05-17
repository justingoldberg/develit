// MySQL dump 8.16
#
# Host: localhost    Database: isp_it
#--------------------------------------------------------
# Server version	4.0.12-log

#
# Table structure for table 'ArquivoRemessa'
#

CREATE TABLE ArquivoRemessa (
  id int(11) NOT NULL auto_increment,
  idArquivo int(11) NOT NULL default '0',
  idBanco int(11) NOT NULL default '0',
  idFaturamento int(11) NOT NULL default '0',
  idUsuario int(11) NOT NULL default '0',
  dtArquivo datetime default NULL,
  nomeArquivo varchar(100) default NULL,
  conteudo longblob,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'ArquivoRetorno'
#

CREATE TABLE ArquivoRetorno (
  id int(11) NOT NULL auto_increment,
  idArquivo int(11) default NULL,
  idBanco int(11) default NULL,
  idUsuario int(11) default NULL,
  dtArquivo datetime default NULL,
  nomeArquivo varchar(100) default NULL,
  conteudo longblob,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Bancos'
#

CREATE TABLE Bancos (
  id int(11) NOT NULL auto_increment,
  idPessoaTipo int(11) NOT NULL default '0',
  numero varchar(10) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY K_For_FK_Bancos_PessoasTipos (idPessoaTipo)
) TYPE=MyISAM;

#
# Table structure for table 'Cidades'
#

CREATE TABLE Cidades (
  id int(11) NOT NULL auto_increment,
  nome varchar(100) default NULL,
  uf char(2) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'ContasAReceber'
#

CREATE TABLE ContasAReceber (
  id int(11) NOT NULL auto_increment,
  idDocumentosGerados int(11) default NULL,
  valor decimal(10,2) default NULL,
  valorRecebido decimal(10,2) default NULL,
  valorJuros decimal(10,2) default NULL,
  valorDesconto decimal(10,2) default NULL,
  dtCadastro datetime default NULL,
  dtVencimento date default NULL,
  dtBaixa datetime default NULL,
  dtCancelamento datetime default NULL,
  obs text,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Contratos'
#

CREATE TABLE Contratos (
  id int(11) NOT NULL auto_increment,
  nome varchar(200) default NULL,
  descricao text,
  dtCadastro datetime default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'ContratosPaginas'
#

CREATE TABLE ContratosPaginas (
  id int(11) NOT NULL auto_increment,
  idContrato int(11) default NULL,
  nomePagina varchar(200) default NULL,
  numeroPagina int(2) default NULL,
  descricao text,
  conteudo text,
  dtCadastro datetime default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'ContratosServicosPlanos'
#

CREATE TABLE ContratosServicosPlanos (
  id int(11) NOT NULL auto_increment,
  idContrato int(11) default NULL,
  idServicoPlano int(11) default NULL,
  idUsuario int(11) default NULL,
  dtEmissao datetime default NULL,
  dtAtivacao datetime default NULL,
  dtRenovacao datetime default NULL,
  dtCancelamento datetime default NULL,
  idUsuarioCancelamento int(11) default NULL,
  mesValidade int(3) default NULL,
  nomeArquivo varchar(200) default NULL,
  numeroContrato varchar(20) default NULL,
  numeroSequencia int(11) default NULL,
  nomePagina varchar(200) default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'DescontosServicosPlanos'
#

CREATE TABLE DescontosServicosPlanos (
  id int(11) NOT NULL auto_increment,
  idPlano int(11) NOT NULL default '0',
  idServicoPlano int(11) NOT NULL default '0',
  dtDesconto date default NULL,
  dtCancelamento date default NULL,
  dtCobranca date default NULL,
  valor decimal(10,2) default NULL,
  descricao varchar(200) default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id),
  KEY K_For_FK_DescontosServicosPlanos_PlanosPessoas (idPlano),
  KEY K_For_FK_DescontosServicosPlanos_Servicos (idServicoPlano)
) TYPE=MyISAM;

#
# Table structure for table 'DocumentosGerados'
#

CREATE TABLE DocumentosGerados (
  id int(11) NOT NULL auto_increment,
  idFaturamento int(11) default NULL,
  seqGeracao int(11) NOT NULL default '0',
  seqDocumento int(11) NOT NULL default '0',
  idPessoaTipo int(11) NOT NULL default '0',
  idUsuario int(11) default NULL,
  dtGeracao datetime default NULL,
  dtAtivacao datetime default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'DocumentosPessoasTipos'
#

CREATE TABLE DocumentosPessoasTipos (
  idPessoa int(11) NOT NULL default '0',
  idTipo int(11) NOT NULL default '0',
  documento varchar(60) default NULL,
  dtCadastro datetime default NULL,
  KEY K_For_FK_DocumentosPessoasTipos_Pessoas (idPessoa),
  KEY K_For_FK_DocumentosPessoasTipos_TiposDocumentos (idTipo)
) TYPE=MyISAM;

#
# Table structure for table 'Dominios'
#

CREATE TABLE Dominios (
  id int(11) NOT NULL auto_increment,
  nome varchar(200) default NULL,
  descricao text,
  dtCadastro datetime default NULL,
  dtAtivacao datetime default NULL,
  dtBloqueio datetime default NULL,
  dtCongelamento datetime default NULL,
  status char(1) default NULL,
  padrao char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'DominiosParametros'
#

CREATE TABLE DominiosParametros (
  id int(11) NOT NULL auto_increment,
  idDominio int(11) default NULL,
  idModulo int(11) default NULL,
  idParametro int(11) default NULL,
  valor varchar(60) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'DominiosServicosPlanos'
#

CREATE TABLE DominiosServicosPlanos (
  id int(11) NOT NULL auto_increment,
  idServicosPlanos int(11) default NULL,
  idDominio int(11) default NULL,
  idPessoasTipos int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Email'
#

CREATE TABLE Email (
  id int(11) NOT NULL auto_increment,
  idPessoaTipo int(11) default NULL,
  idDominio int(11) default NULL,
  idServicosPlanos int(11) not null default '0',
  login varchar(200) default NULL,
  senha varchar(200) default NULL,
  senhaTexto varchar(200) default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id),
  INDEX idServicosPlanos (idServicosPlanos)
) TYPE=MyISAM;

#
# Table structure for table 'EmailAlias'
#

CREATE TABLE EmailAlias (
  id int(11) NOT NULL auto_increment,
  idEmail int(11) default NULL,
  alias varchar(200) default NULL,
  dtCadastro datetime default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'EmailAutoReply'
#

CREATE TABLE EmailAutoReply (
  idEmail int(11) NOT NULL default '0',
  texto text,
  dtCadastro datetime default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (idEmail)
) TYPE=MyISAM;

#
# Table structure for table 'EmailConfig'
#

CREATE TABLE EmailConfig (
  id int(11) NOT NULL auto_increment,
  idEmail int(11) default NULL,
  idParametro int(11) default NULL,
  valor text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'EmailForward'
#

CREATE TABLE EmailForward (
  idEmail int(11) NOT NULL default '0',
  forward text,
  copia char(1) default NULL,
  dtCadastro datetime default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (idEmail)
) TYPE=MyISAM;

#
# Table structure for table 'Enderecos'
#

CREATE TABLE Enderecos (
  idPessoaTipo int(11) NOT NULL default '0',
  idTipo int(11) NOT NULL default '0',
  idCidade int(11) NOT NULL default '0',
  endereco varchar(200) default NULL,
  complemento varchar(200) default NULL,
  bairro varchar(100) default NULL,
  cep varchar(10) default NULL,
  pais varchar(100) default NULL,
  caixa_postal varchar(20) default NULL,
  ddd_fone1 char(3) default NULL,
  fone1 varchar(30) default NULL,
  ddd_fone2 char(3) default NULL,
  fone2 varchar(30) default NULL,
  ddd_fax char(3) default NULL,
  fax varchar(30) default NULL,
  email varchar(100) default NULL,
  KEY K_For_FK_Enderecos_Cidades (idCidade),
  KEY K_For_FK_Enderecos_TipoPessoa (idPessoaTipo),
  KEY K_For_FK_Enderecos_TiposEnderecos (idTipo)
) TYPE=MyISAM;

#
# Table structure for table 'Faturamentos'
#

CREATE TABLE Faturamentos (
  id int(11) NOT NULL auto_increment,
  descricao varchar(200) default NULL,
  data datetime default NULL,
  idServico int(11) default NULL,
  idPOP int(11) default NULL,
  idFormaCobranca int(11) default NULL,
  idVencimento int(11) default NULL,
  mes int(2) NOT NULL default '0',
  ano int(4) NOT NULL default '0',
  status char(1) default NULL,
  remessa char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'FormaCobranca'
#

CREATE TABLE FormaCobranca (
  id int(11) NOT NULL auto_increment,
  idBanco int(11) NOT NULL default '0',
  idTipoCarteira bigint(20) not null default '0',
  descricao varchar(200) default NULL,
  titular varchar(200) default NULL,
  cnpj varchar(20) default NULL,
  convenio varchar(100) default NULL,
  agencia varchar(10) default NULL,
  digAgencia char(2) default NULL,
  conta varchar(20) default NULL,
  digConta char(2) default NULL,
  PRIMARY KEY  (id),
  KEY K_For_FK_FormaCobranca_Bancos (idBanco)
) TYPE=MyISAM;

#
# Table structure for table 'Grupos'
#

CREATE TABLE Grupos (
  id int(9) NOT NULL auto_increment,
  nome varchar(50) NOT NULL default '',
  admin char(1) default NULL,
  incluir char(1) default NULL,
  excluir char(1) default NULL,
  visualizar char(1) default NULL,
  alterar char(1) default NULL,
  abrir char(1) default NULL,
  fechar char(1) default NULL,
  comentar char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'GruposServicos'
#

CREATE TABLE GruposServicos (
  id int(11) NOT NULL auto_increment,
  nome varchar(60) default NULL,
  descricao text,
  dtCadastro datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Modulos'
#

CREATE TABLE Modulos (
  id int(11) NOT NULL auto_increment,
  descricao varchar(200) default NULL,
  modulo varchar(20) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Ocorrencias'
#

CREATE TABLE Ocorrencias (
  id int(11) NOT NULL auto_increment,
  idPessoaTipo int(11) default NULL,
  idServicoPlano int(11) default NULL,
  idUsuario int(11) default NULL,
  idPrioridade int(11) default NULL,
  data datetime default NULL,
  nome varchar(200) default NULL,
  descricao text,
  status char(1) default NULL,
  PRIMARY KEY  (id),
  INDEX (idPessoaTipo),
  INDEX (idServicoPlano),
  INDEX (idUsuario),
  INDEX (idPrioridade)
) TYPE=MyISAM;

#
# Table structure for table 'OcorrenciasComentarios'
#

CREATE TABLE OcorrenciasComentarios (
  id int(11) NOT NULL auto_increment,
  idOcorrencia int(11) default NULL,
  idUsuario int(11) default NULL,
  status char(1) default NULL,
  data datetime default NULL,
  texto text,
  INDEX (idOcorrencia),
  INDEX (idUsuario),
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'OcorrenciasOrdemServico'
#

CREATE TABLE OcorrenciasOrdemServico (
  id int(11) NOT NULL auto_increment,
  idOcorrencia int(11) default NULL,
  idOrdemServico int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'OcorrenciasTicket'
#

CREATE TABLE OcorrenciasTicket (
  id int(11) NOT NULL auto_increment,
  idOcorrencia int(11) default NULL,
  idTicket int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'OrdemServico'
#

CREATE TABLE OrdemServico (
  id int(11) NOT NULL auto_increment,
  idPessoaTipo int(11) default NULL,
  idServico int(11) default NULL,
  idUsuario int(11) default NULL,
  idPrioridade int(11) default NULL,
  dtCriacao datetime default NULL,
  dtExecusao datetime default NULL,
  dtConclusao datetime default NULL,
  nome varchar(200) default NULL,
  descricao text,
  status char(1) default NULL,
  valor float(12,2) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'OrdemServicoComentarios'
#

CREATE TABLE OrdemServicoComentarios (
  id int(11) NOT NULL auto_increment,
  idOrdemServico int(11) default NULL,
  idUsuario int(11) default NULL,
  status char(1) default NULL,
  data datetime default NULL,
  texto text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Parametros'
#

CREATE TABLE Parametros (
  id int(11) NOT NULL auto_increment,
  descricao varchar(200) default NULL,
  tipo varchar(20) default 'numero',
  idUnidade int(11) NOT NULL default '0',
  parametro varchar(20) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'ParametrosModulos'
#

CREATE TABLE ParametrosModulos (
  idModulo int(11) NOT NULL default '0',
  idParametro int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Table structure for table 'Pessoas'
#

CREATE TABLE Pessoas (
  id int(11) NOT NULL auto_increment,
  tipoPessoa char(1) default NULL,
  nome varchar(200) default NULL,
  razao varchar(200) default NULL,
  site varchar(100) default NULL,
  mail varchar(100) default NULL,
  dtNascimento datetime default NULL,
  dtCadastro datetime default NULL,
  idPOP int(11) default NULL,
  contato varchar(200) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'PessoasTipos'
#

CREATE TABLE PessoasTipos (
  id int(11) NOT NULL auto_increment,
  idPessoa int(11) NOT NULL default '0',
  idTipo int(11) NOT NULL default '0',
  dtCadastro datetime default NULL,
  PRIMARY KEY  (id),
  KEY K_For_FK_PessoasTipos_Pessoas (idPessoa)
) TYPE=MyISAM;

#
# Table structure for table 'PlanosDocumentosGerados'
#

CREATE TABLE PlanosDocumentosGerados (
  id int(11) NOT NULL auto_increment,
  idDocumentoGerado int(11) NOT NULL default '0',
  idPlano int(11) default NULL,
  idFormaCobranca int(11) default NULL,
  idVencimento int(11) default NULL,
  dtVencimento date default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'PlanosPessoas'
#

CREATE TABLE PlanosPessoas (
  id int(11) NOT NULL auto_increment,
  idPessoaTipo int(11) NOT NULL default '0',
  idVencimento int(11) NOT NULL default '0',
  idFormaCobranca int(11) NOT NULL default '0',
  dtCadastro datetime default NULL,
  dtCancelamento datetime default NULL,
  nome varchar(200) default NULL,
  especial char(1) default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Pop'
#

CREATE TABLE Pop (
  id int(11) NOT NULL auto_increment,
  nome varchar(100) default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'PopCidade'
#

CREATE TABLE PopCidade (
  idPOP int(11) NOT NULL default '0',
  idCidade int(11) NOT NULL default '0',
  principal char(1) default NULL
) TYPE=MyISAM;

#
# Table structure for table 'Prioridades'
#

CREATE TABLE Prioridades (
  id int(11) NOT NULL auto_increment,
  nome varchar(100) default NULL,
  texto text,
  cor varchar(20) default NULL,
  valor smallint(5) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'RadiusGrupos'
#

CREATE TABLE RadiusGrupos (
  id int(11) NOT NULL auto_increment,
  nome varchar(200) default NULL,
  descricao text,
  horas int(6) default NULL,
  ilimitado char(1) default NULL,
  status char(1) default NULL,
  comando text,
  dtCadastro datetime default NULL,
  dtAtivacao datetime default NULL,
  dtInativacao datetime default NULL,
  dtCancelamento datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'RadiusUsuarios'
#

CREATE TABLE RadiusUsuarios (
  id int(11) NOT NULL auto_increment,
  idGrupo int(11) default NULL,
  login varchar(200) default NULL,
  senha varchar(200) default NULL,
  senha_texto varchar(200) default NULL,
  dtCadastro datetime default NULL,
  dtAtivacao datetime default NULL,
  dtInativacao datetime default NULL,
  dtCancelamento datetime default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'RadiusUsuariosPessoasTipos'
#

CREATE TABLE RadiusUsuariosPessoasTipos (
  id int(11) NOT NULL auto_increment,
  idPessoasTipos int(11) default NULL,
  idRadiusUsuarios int(11) default NULL,
  idServicosPlanos int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'RadiusUsuariosTelefones'
#

CREATE TABLE RadiusUsuariosTelefones (
  id int(11) NOT NULL auto_increment,
  idRadiusUsuarioPessoaTipo int(11) default NULL,
  telefone varchar(20) default NULL,
  dtCadastro datetime default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Servicos'
#

CREATE TABLE Servicos (
  id int(11) NOT NULL auto_increment,
  idTipoCobranca int(11) NOT NULL default '0',
  nome varchar(200) default NULL,
  descricao text,
  valor decimal(10,2) default NULL,
  idStatusPadrao int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'ServicosAdicionais'
#

CREATE TABLE ServicosAdicionais (
  id int(11) NOT NULL auto_increment,
  idPlano int(11) NOT NULL default '0',
  idServicoPlano int(11) NOT NULL default '0',
  idTipoServicoAdicional int(11) default NULL,
  nome varchar(200) default NULL,
  valor decimal(10,2) default NULL,
  dtCadastro datetime default NULL,
  dtVencimento date default NULL,
  dtCancelamento date default NULL,
  dtCobranca date default NULL,
  dtEspecial char(1) default NULL,
  adicionalFaturamento char(1) default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id),
  KEY K_For_FK_ServicosAdicionais_PlanosPessoas (idPlano),
  KEY K_For_FK_ServicosAdicionais_Servicos (idServicoPlano)
) TYPE=MyISAM;

#
# Table structure for table 'ServicosContratos'
#

CREATE TABLE ServicosContratos (
  id int(11) NOT NULL auto_increment,
  idServico int(11) default NULL,
  idContrato int(11) default NULL,
  dtCadastro datetime default NULL,
  mesValidade int(3) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'ServicosGrupos'
#

CREATE TABLE ServicosGrupos (
  idGrupos int(11) NOT NULL default '0',
  idServico int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Table structure for table 'ServicosParametros'
#

CREATE TABLE ServicosParametros (
  idServico int(11) NOT NULL default '0',
  idParametro int(11) NOT NULL default '0',
  valor varchar(20) default NULL
) TYPE=MyISAM;

#
# Table structure for table 'ServicosPlanos'
#

CREATE TABLE ServicosPlanos (
  id int(11) NOT NULL auto_increment,
  idPlano int(11) NOT NULL default '0',
  idServico int(11) NOT NULL default '0',
  valor decimal(10,2) default NULL,
  dtCadastro datetime default NULL,
  dtAtivacao datetime default NULL,
  dtInativacao datetime default NULL,
  dtCancelamento datetime default NULL,
  idStatus int(11) NOT NULL default '0',
  diasTrial int(3) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY K_For_FK_ServicosPlanos_PlanosPessoas (idPlano),
  KEY K_For_FK_ServicosPlanos_Servicos (idServico)
) TYPE=MyISAM;

#
# Table structure for table 'ServicosPlanosDocumentosGerados'
#

CREATE TABLE ServicosPlanosDocumentosGerados (
  id int(11) NOT NULL auto_increment,
  idPlanoDocumentoGerado int(11) default NULL,
  idServicosPlanos int(11) default NULL,
  valor decimal(10,2) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'ServicosRadiusGrupos'
#

CREATE TABLE ServicosRadiusGrupos (
  id int(11) NOT NULL auto_increment,
  idRadiusGrupos int(11) default NULL,
  idServicos int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'StatusServicosPlanos'
#

CREATE TABLE StatusServicosPlanos (
  id int(11) NOT NULL auto_increment,
  descricao varchar(60) default NULL,
  cobranca char(1) default NULL,
  status char(1) NOT NULL default 'A',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'TipoCobranca'
#

CREATE TABLE TipoCobranca (
  id int(11) NOT NULL auto_increment,
  descricao varchar(200) default NULL,
  proporcional char(1) default NULL,
  forma varchar(10) default NULL,
  tipo varchar(20) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'TipoPessoa'
#

CREATE TABLE TipoPessoa (
  id int(11) NOT NULL auto_increment,
  descricao varchar(60) default NULL,
  valor varchar(20) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'TipoServicoAdicional'
#

CREATE TABLE TipoServicoAdicional (
  id int(11) NOT NULL auto_increment,
  nome varchar(200) default NULL,
  descricao text,
  dtCadastro datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'TiposDocumentos'
#

CREATE TABLE TiposDocumentos (
  id int(11) NOT NULL auto_increment,
  descricao varchar(60) default NULL,
  valor varchar(20) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'TiposEnderecos'
#

CREATE TABLE TiposEnderecos (
  id int(11) NOT NULL auto_increment,
  descricao varchar(60) default NULL,
  valor varchar(20) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Unidades'
#

CREATE TABLE Unidades (
  id int(11) NOT NULL auto_increment,
  unidade varchar(20) default NULL,
  descricao varchar(200) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'Usuarios'
#

CREATE TABLE Usuarios (
  id int(9) NOT NULL auto_increment,
  login varchar(20) NOT NULL default '',
  senha varchar(40) NOT NULL default '',
  status char(1) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'UsuariosGrupos'
#

CREATE TABLE UsuariosGrupos (
  idUsuario int(9) NOT NULL default '0',
  idGrupo int(9) NOT NULL default '0',
  PRIMARY KEY  (idUsuario,idGrupo)
) TYPE=MyISAM;

#
# Table structure for table 'Vencimentos'
#

CREATE TABLE Vencimentos (
  id int(11) NOT NULL auto_increment,
  descricao varchar(200) default NULL,
  diaVencimento char(2) default NULL,
  diaFaturamento char(2) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# Hugo - 12/07/2004
# Versao 1.0.24
# Tabelas para o Gerenciamento de Equipamentos 

DROP TABLE IF EXISTS Equipamento;
CREATE TABLE Equipamento (
  id bigint(20) NOT NULL auto_increment,
  idTipo bigint(20),
  nome varchar(255) default NULL,
  descricao mediumtext,
  status char(1) default NULL,
  dtCadastro datetime default NULL,
  index(idTipo), 
  PRIMARY KEY  (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS EquipamentoEquiptoCaracteristica;
CREATE TABLE  EquipamentoEquiptoCaracteristica (
  id bigint(20) NOT NULL auto_increment,
  idEquipamento bigint(20) default NULL,
  idEquiptoCaracteristica bigint(20) default NULL,
  valor varchar(255) default NULL,
  index(idEquipamento),
  index(idEquiptoCaracteristica),
  PRIMARY KEY  (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS EquiptoCaracteristica;
CREATE TABLE EquiptoCaracteristica (
  id bigint(20) NOT NULL auto_increment,
  nome varchar(255) default NULL,
  descricao mediumtext,
  status char(1) default NULL,
  dtCadastro datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS EquiptoTipo;
CREATE TABLE EquiptoTipo (
  id bigint(20) NOT NULL auto_increment,
  nome varchar(255) default NULL,
  descricao mediumtext,
  status char(1) default NULL,
  dtCadastro datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS EquiptoTipoEquiptoCaracteristica;
CREATE TABLE EquiptoTipoEquiptoCaracteristica (
  id bigint(20) NOT NULL auto_increment,
  idEquiptoTipo bigint(20) default NULL,
  idEquiptoCaracteristica bigint(20) default NULL,
  valor varchar(255) default NULL,
  index(idEquiptoTipo),
  index (idEquiptoCaracteristica),
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE Aplicacao (
  id bigint(20) NOT NULL auto_increment,
  descricao varchar(100) default '',
  dtCadastro datetime default NULL,
  idUsuario bigint(20) default '0',
  status char(1) not null default 'A',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE MaodeObra (
  id bigint(20) NOT NULL auto_increment,
  descricao varchar(100) default '',
  dtCadastro datetime default NULL,
  idUsuario bigint(20) default '0',
  status char(1) not null default 'A',
  PRIMARY KEY  (id),
  INDEX idUsuario (idUsuario)
) TYPE=MyISAM;


CREATE TABLE Produtos (
  id bigint(20) NOT NULL auto_increment,
  nome varchar(100) default '',
  descricao varchar(255) default '',
  valor double default '0',
  dtCadastro datetime default null,
  status char(1) not null default 'A',
  idUsuario bigint(20) default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE tmpGrupoServico (
	id BIGINT NOT NULL AUTO_INCREMENT,
	grupo BIGINT NULL,
	cliente VARCHAR(255) NULL,
	valor DOUBLE NULL,
	recebido DOUBLE NULL,
	PRIMARY KEY(id)
) TYPE=MyISAM;


####################################################################
# Hugo Ribeiro
# Versao 1.0.24
# IVR
# ##################################################################

DROP TABLE IF EXISTS Servidores;
CREATE TABLE Servidores (
  id bigint(20) NOT NULL auto_increment,
  nome varchar(255) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  status char(1) NOT NULL default 'A',
  dtCadastro datetime,
  PRIMARY KEY  (id)
) TYPE=MyISAM;


DROP TABLE IF EXISTS Interfaces;
CREATE TABLE Interfaces (
  id bigint(20) NOT NULL auto_increment,
  idServidor bigint(20),
  nome varchar(255) NOT NULL default '',
  iface varchar(4) NOT NULL default '',
  status char(1) NOT NULL default 'A',
  dtCadastro datetime,
  index(idServidor),
  PRIMARY KEY  (id)
) TYPE=MyISAM;


DROP TABLE IF EXISTS Bases;
CREATE TABLE Bases (
  id bigint(20) NOT NULL auto_increment,
  idIfaceServidor bigint(20) NOT NULL default '',
  nome varchar(255) NOT NULL default '',
  descricao varchar(255) NOT NULL default '',
  status char(1) default 'A',
  dtCadastro datetime NOT NULL default '',
  index(idIfaceServidor),
  PRIMARY KEY  (id)
) TYPE=MyISAM;


DROP TABLE IF EXISTS ServicoIVR;
CREATE TABLE ServicoIVR (
  id bigint(20) NOT NULL auto_increment,
  idServicoPlano bigint(20) NOT NULL default '',
  idBase bigint(20) NOT NULL default '',
  nome varchar(255) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  mask varchar(15) NOT NULL default '',
  mac varchar(50) NOT NULL default '',
  gw varchar(15) NOT NULL default '',
  dns1 varchar(15) NOT NULL default '',
  dns2 varchar(15) NOT NULL default '',
  status char(1) default 'A',
  so varchar(255) NOT NULL default '',
  obs mediumtext,
  dtCadastro datetime,
  index(idServicoPlano),
  index(idBase),
  PRIMARY KEY  (id)
) TYPE=MyISAM;


#############################################
# Equipamentos
############################################

DROP TABLE IF EXISTS Equipamento;
CREATE TABLE Equipamento (
  id bigint(20) NOT NULL auto_increment,
  idTipo bigint(20) default NULL,
  nome varchar(255) default NULL,
  descricao mediumtext,
  status char(1) default NULL,
  dtCadastro datetime default NULL,
  PRIMARY KEY  (id),
  KEY idTipo (idTipo)
) TYPE=MyISAM;

DROP TABLE IF EXISTS EquipamentoEquiptoCaracteristica;
CREATE TABLE EquipamentoEquiptoCaracteristica (
  id bigint(20) NOT NULL auto_increment,
  idEquipamento bigint(20) default NULL,
  idEquiptoCaracteristica bigint(20) default NULL,
  valor varchar(255) default NULL,
  PRIMARY KEY  (id),
  KEY idEquipamento (idEquipamento),
  KEY idEquiptoCaracteristica (idEquiptoCaracteristica)
) TYPE=MyISAM;

DROP TABLE IF EXISTS EquiptoCaracteristica;
CREATE TABLE EquiptoCaracteristica (
  id bigint(20) NOT NULL auto_increment,
  nome varchar(255) default NULL,
  descricao mediumtext,
  status char(1) default NULL,
  dtCadastro datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS EquiptoTipo;
CREATE TABLE EquiptoTipo (
  id bigint(20) NOT NULL auto_increment,
  nome varchar(255) default NULL,
  descricao mediumtext,
  status char(1) default NULL,
  dtCadastro datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS EquiptoTipoEquiptoCaracteristica;
CREATE TABLE EquiptoTipoEquiptoCaracteristica (
  id bigint(20) NOT NULL auto_increment,
  idEquiptoTipo bigint(20) default NULL,
  idEquiptoCaracteristica bigint(20) default NULL,
  valor varchar(255) default NULL,
  PRIMARY KEY  (id),
  KEY idEquiptoTipo (idEquiptoTipo),
  KEY idEquiptoCaracteristica (idEquiptoCaracteristica)
) TYPE=MyISAM;


### Kerne - 16/07/2004 - 1.0.24
# Parametros de configuração e customização de integrações

CREATE TABLE ParametrosConfig (
  id int(11) NOT NULL auto_increment,
  descricao varchar(200) default NULL,
  parametro varchar(20) default NULL,
  valor varchar(200) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

### PoPo - 29/12/2004 17:18
# Identificacao da Carteira de Cobranca

CREATE TABLE TipoCarteira (
  id bigint(20) not null auto_increment, 
  nome varchar(50) not null default '', 
  descricao varchar(255) not null default '', 
  valor char(1) not null default 'R',
  PRIMARY KEY (id)
) TYPE=MyISAM;

# Tabela de Relacionamento de Clientes x Bancos para contas de Débito Automático

DROP TABLE IF EXISTS ClienteBanco;
CREATE TABLE ClienteBanco (  
  id bigint(20) NOT NULL auto_increment,  
  idPlanosPessoas bigint(20) NOT NULL default '0',  
  agencia varchar(20) NOT NULL default '',  
  contacorrente varchar(20) NOT NULL default '',
  identificacao varchar(14) NOT NULL default '',
  digAg char(1) NOT NULL default '',
  digCC char(1) NOT NULL default '',
  PRIMARY KEY  (id),
  INDEX idPlanosPessoas (idPlanosPessoas)
) TYPE=MyISAM;


DROP TABLE IF EXISTS OrdemServicoDetalhe;

CREATE TABLE OrdemServicoDetalhe (
  id  int(11) not null default '0',
  idOrdemServico int(11) not null default '0',
  idUsuario int(11) not null default '0',
  idProduto int(11) not null default '0',
  idMaodeObra int(11) not null default '0',
  idAplicacao int(11) not null default '0',
  quantidade double not null default '0.0',
  valor double not null default '0.0',
  dtCadastro datetime not null default '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  INDEX idOrdemServico (idOrdemServico),
  INDEX idUsuario (idUsuario),
  INDEX idProduto (idProduto),
  INDEX idMaodeObra (idMaodeObra),
  INDEX idAplicacao (idAplicacao)
) type MyISAM;

DROP TABLE IF EXISTS NotaFiscal;

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
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS ItensNF;

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

DROP TABLE IF EXISTS DescontoNF;

CREATE TABLE `DescontoNF` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `idNF` bigint(20) unsigned NOT NULL default '0',
  `qtde` int(11) NOT NULL default '0',
  `descricao` varchar(255) NOT NULL default '',
  `valorUnit` float(10,2) unsigned NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `idNF` (`idNF`)
) TYPE=MyISAM;


CREATE TABLE `ParametrosArquivosBancos` (
  `id` int(11) NOT NULL auto_increment,
  `idBanco` int(11) NOT NULL default '0',
  `atributo` char(30) default NULL,
  `valor` char(100) default NULL,
  `descricao` char(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `ParametrosArquivosBancos` VALUES (4,2,'arq_remessa_msg1','Desconto de 12% para pagamento a vista','mensagem numero 1 a ser exibida no boleto');
	    
