# Alterações a serem aplicadas para versão 1.0.26

alter table FormaCobranca add idTipoCarteira bigint(20) not null default 0;

create table TipoCarteira (id bigint(20) not null auto_increment primary key, nome varchar(50) not null default '', descricao varchar(255) not null default '', valor char(1) not null default 'R');

insert into TipoCarteira values (0, '112', 'Carteira Registrada 112', 'R');

CREATE TABLE ClienteBanco (
  id bigint(20) NOT NULL auto_increment,
  idPlanosPessoas bigint(20) NOT NULL default '0',
  agencia varchar(20) NOT NULL default '',
  contacorrente varchar(20) NOT NULL default '',
  identificacao varchar(14) NOT NULL default '',
  digAg char(1) NOT NULL default '',
  digCC char(1) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY idPlanosPessoas(idPlanosPessoas)
) TYPE=MyISAM;

alter table Email add column idServicosPlanos int(11) not null default 0;

alter table MaodeObra add column status char(1) not null default 'A';

alter table Aplicacao add column status char(1) not null default 'A';

alter table Produtos add column status char(1) not null default 'A';

DROP TABLE IF NOT EXISTS OrdemServicoDetalhe;

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
  KEY idOrdemServico (idOrdemServico),
  KEY idUsuario (idUsuario),
  KEY idProduto (idProduto),
  KEY idMaodeObra (idMaodeObra),
  KEY idAplicacao (idAplicacao)
) type=MyISAM;

##############################################################################

#definicao de indice na tabela Ocorrencias
ALTER TABLE Ocorrencias ADD INDEX (idPessoaTipo);
ALTER TABLE Ocorrencias ADD INDEX (idServicoPlano);
ALTER TABLE Ocorrencias ADD INDEX (idUsuario);
ALTER TABLE Ocorrencias ADD INDEX (idPrioridade);
#
# definicao de indices na tabela de OcorrenciasComentarios
ALTER TABLE OcorrenciasComentarios ADD INDEX (idOcorrencia);
ALTER TABLE OcorrenciasComentarios ADD INDEX (idUsuario);
#
#

