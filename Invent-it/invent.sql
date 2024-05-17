-- MySQL dump 9.09
--
-- Host: localhost    Database: Ticket_IT
-- Server version	4.0.15

--
-- Table structure for table `Empresas`
--

CREATE TABLE Empresas (
  id int(11) NOT NULL auto_increment,
  idPessoaTipo int(11) default NULL,
  nome varchar(200) default NULL,
  dominio varchar(200) default NULL,
  status  char(1) default 'A',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `Parametros`
--

CREATE TABLE Parametros (
  id int(11) NOT NULL auto_increment,
  descricao varchar(200) default NULL,
  parametro varchar(20) default NULL,
  valor text default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `grupos`
--

CREATE TABLE grupos (
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

--
-- Table structure for table `usuarios`
--

CREATE TABLE usuarios (
  id int(9) NOT NULL auto_increment,
  login varchar(20) NOT NULL default '',
  senha varchar(40) NOT NULL default '',
  status char(1) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `usuarios_grupos`
--

CREATE TABLE usuarios_grupos (
  idUsuario int(9) NOT NULL default '0',
  idGrupo int(9) NOT NULL default '0',
  PRIMARY KEY  (idUsuario,idGrupo),
  KEY idUsuario (idUsuario),
  KEY idGrupo (idGrupo)
) TYPE=MyISAM;

CREATE TABLE Maquinas (
  id int(11) NOT NULL auto_increment,
  nome varchar(200) default NULL,
  ip varchar(15) default NULL,
  cliente varchar(200) default NULL,
  idEmpresa int(11) default NULL,
  data datetime default NULL,
  obs text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE Programas (
  id int(11) NOT NULL auto_increment,
  idMaquina int(11) default NULL,
  nome varchar(200) default NULL,
  versao varchar(60) default NULL,
  data datetime default NULL,
  idUsuario int(11) default NULL,
  comentarios text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE Users (
  id int(11) NOT NULL auto_increment,
  idMaquina int(11) default NULL,
  usuario varchar(60) default NULL,
  senha varchar(200) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

INSERT INTO Parametros VALUES (1,'Authentication Passphrase for password view','passphrase','YmF0YXRpbmhhIHF1YW5kbyBuYXNjZQ==');
INSERT INTO Parametros VALUES (2,'PassPhrase`s validation timeout (in seconds)','passphrase_timeout','MzA=');
INSERT INTO Parametros VALUES (3,'Ticket-IT - URL','ticket_url','aHR0cDovL3RpY2tldC5kZXZlbC5pdA==');
INSERT INTO Parametros VALUES (4,'Welcome to the Chat','boas_vindas_chat','SG93IGNhbiBJIGhlbHAgeW91Pw==');
INSERT INTO Parametros VALUES (5,'Integration between Ticket and Invent applications','ticket_invent','dGlja2V0X2FuZF9pbnZlbnQ=');
INSERT INTO Parametros VALUES (6,'Integration between Ticket and ISP applications','ticket_isp','dGlja2V0X2FuZF9pc3A=');

INSERT INTO usuarios VALUES (1,'admin','$1$$CoERg7ynjYLsj2j4glJ34.','A');
INSERT INTO usuarios VALUES (2,'guest','$1$Ou6tpaef$N1JF7ZRnhVATotUtJW/Xy1','A');
INSERT INTO usuarios_grupos VALUES (1,1);
INSERT INTO grupos VALUES (1,'Administrators','S','','','','','','','');

CREATE TABLE agenda (
  id bigint(20) NOT NULL auto_increment,
  idEvento bigint(20) NOT NULL default '0',
  horario datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  KEY idEvento (idEvento)
) TYPE=MyISAM;

CREATE TABLE categorias (
  id int(9) NOT NULL auto_increment,
  nome varchar(50) NOT NULL default '',
  texto text NOT NULL,
  data datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE categorias_grupos (
  idCategoria int(9) NOT NULL default '0',
  idGrupo int(9) NOT NULL default '0',
  PRIMARY KEY  (idCategoria,idGrupo),
  KEY idCategoria (idCategoria),
  KEY idGrupo (idGrupo)
) TYPE=MyISAM;

CREATE TABLE evento (
  id bigint(20) NOT NULL auto_increment,
  idTicket bigint(20) NOT NULL default '0',
  inicio datetime NOT NULL default '0000-00-00 00:00:00',
  duracao float NOT NULL default '0',
  periodicidade char(1) NOT NULL default 'S',
  status char(1) NOT NULL default 'A',
  horario int(3) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY idTicket (idTicket)
) TYPE=MyISAM;

CREATE TABLE perfil (
  id int(11) default NULL,
  notificar_email char(1) default NULL,
  email varchar(200) default NULL,
  titulo_email varchar(200) default NULL,
  idGrupo int(11) default NULL,
  diaInicio int(1) NOT NULL default '1',
  diaFim int(1) NOT NULL default '5',
  horarioInicio int(1) NOT NULL default '9',
  horarioFim int(1) NOT NULL default '18',
  grade char(1) NOT NULL default '1',
  alinhaMenu char(1) default NULL,
  alinhaPrior char(1) default NULL,
  categoriaPadrao int(9) default NULL,
  atualizarUltimos char(1) default NULL,
  ordemComentarios char(1) default NULL,
  comentarGrupo char(1) default 'N',
  KEY idGrupo (idGrupo)
) TYPE=MyISAM;

CREATE TABLE prioridades (
  id int(9) NOT NULL auto_increment,
  nome varchar(100) NOT NULL default '',
  texto text NOT NULL,
  cor varchar(20) NOT NULL default '',
  valor smallint(5) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE processos_ticket (
  idTicket int(9) NOT NULL default '0',
  idUsuario int(9) NOT NULL default '0',
  idStatus int(9) NOT NULL default '0',
  data datetime NOT NULL default '0000-00-00 00:00:00',
  texto text NOT NULL,
  KEY idTicket (idTicket),
  KEY idUsuario (idUsuario),
  KEY idStatus (idStatus),
  KEY idTicket_2 (idTicket,idUsuario,idStatus,data),
  KEY idUsuario_2 (idUsuario),
  KEY idTicket_3 (idTicket),
  KEY idStatus_2 (idStatus)
) TYPE=MyISAM;

CREATE TABLE status (
  id int(9) NOT NULL auto_increment,
  nome varchar(50) NOT NULL default '',
  texto text NOT NULL,
  valor varchar(20) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE ticket (
  id int(9) NOT NULL auto_increment,
  assunto varchar(200) NOT NULL default '',
  data datetime NOT NULL default '0000-00-00 00:00:00',
  texto text NOT NULL,
  idUsuario int(9) NOT NULL default '0',
  idPrioridade int(9) NOT NULL default '0',
  idCategoria int(9) NOT NULL default '0',
  status char(1) default NULL,
  protocolo varchar(15) default NULL,
  PRIMARY KEY  (id),
  KEY idUsuario (idUsuario),
  KEY idPrioridade (idPrioridade),
  KEY idCategoria (idCategoria)
) TYPE=MyISAM;

CREATE TABLE ticket_comentario (
  id int(11) NOT NULL auto_increment,
  idTicket int(9) NOT NULL default '0',
  idUsuario int(9) NOT NULL default '0',
  data datetime NOT NULL default '0000-00-00 00:00:00',
  texto text,
  PRIMARY KEY  (id),
  KEY idTicket (idTicket),
  KEY idUsuario (idUsuario),
  KEY data (data)
) TYPE=MyISAM;

CREATE TABLE ticket_detalhes (
  id int(11) NOT NULL auto_increment,
  idTicket int(9) default NULL,
  parametro varchar(20) default NULL,
  valor text,
  PRIMARY KEY  (id),
  KEY idTicket (idTicket)
) TYPE=MyISAM;

CREATE TABLE ticket_email (
  idTicket int(9) NOT NULL default '0',
  idUsuario int(9) NOT NULL default '0',
  idStatus int(9) NOT NULL default '0',
  dataProcesso datetime NOT NULL default '0000-00-00 00:00:00',
  dataEnvio datetime default NULL,
  email varchar(200) default NULL,
  PRIMARY KEY  (idTicket,idUsuario,idStatus,dataProcesso),
  KEY idTicket (idTicket),
  KEY idUsuario (idUsuario),
  KEY idStatus (idStatus)
) TYPE=MyISAM;

CREATE TABLE ticket_empresa (
  id int(11) NOT NULL auto_increment,
  idMaquina int(11) default NULL,
  idEmpresa int(11) default NULL,
  idTicket int(11) default NULL,
  titulo varchar(200) default NULL,
  data datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE ticket_tempo (
  id int(12) NOT NULL auto_increment,
  idTicket int(12) default NULL,
  idUsuario int(12) default NULL,
  data datetime default NULL,
  segundos int(12) default NULL,
  expediente char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE ticket_chat (
  id int(12) NOT NULL auto_increment,
  idTicket int(12) default NULL,
  data datetime default NULL,
  idUsuario int(12) default NULL,
  status char(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE ticket_chat_conteudo (
  id int(12) NOT NULL auto_increment,
  idChat int(12) default NULL,
  idUsuario int(12) default NULL,
  data datetime default NULL,
  texto text,
  ip varchar(15) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE ticket_feedback (
  id int(12) NOT NULL auto_increment,
  idTicket int(12) default NULL,
  idUsuario int(9),
  data datetime default NULL,
  nota int(2) default NULL,
  comentario VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

create table UsuariosEmpresas (
	id int(11) primary key auto_increment, 
	idEmpresaUsuario int(11), 
	idEmpresa int(11)
) TYPE=MyISAM;

create table EmpresasUsuarios (
	id int(11) primary key auto_increment, 
	login varchar(60), 
	senha varchar(100), 
	senha_texto varchar(100), 
	dtCriacao datetime, 
	dtInativacao datetime, 
	dtCancelamento datetime, 
	email varchar(200), 
	nome varchar(200), 
	fone varchar(40), 
	admin char(1) default 'N', 
	status char(1) default 'I'
) TYPE=MyISAM;

INSERT INTO prioridades VALUES (2,'Low','Priority level 5','#aeadff',9);
INSERT INTO prioridades VALUES (4,'High Average','Priority level 2','#ffb159',2);
INSERT INTO prioridades VALUES (5,'High','Priority level 1','#ff723f',1);
INSERT INTO prioridades VALUES (6,'Low Average','Priority level 4','#dddd90',7);
INSERT INTO prioridades VALUES (7,'Intermediary','Priority level 3','#f2ef5e',5);


INSERT INTO status VALUES (1,'New','New Tickets','N');
INSERT INTO status VALUES (2,'Opened','Tickets opened by the responsible','A');
INSERT INTO status VALUES (3,'Stopped','Ticket stopped for external reasons (dependences, etc)','P');
INSERT INTO status VALUES (8,'Reopened','Tickets Reopened for revision','R');
INSERT INTO status VALUES (7,'Closed','Tickets Closed','F');

INSERT INTO categorias VALUES ('','Solutions','Solutions developed for the company.',NOW());
INSERT INTO categorias VALUES ('','Support','Support technician to the customers.',NOW());
INSERT INTO categorias VALUES ('','Updates','Updates of packages and others.',NOW());
INSERT INTO categorias VALUES ('','Network','Attention related the network.',NOW());

INSERT INTO categorias_grupos VALUES (1, 1);
INSERT INTO categorias_grupos VALUES (2, 1);
INSERT INTO categorias_grupos VALUES (3, 1);
INSERT INTO categorias_grupos VALUES (4, 1);
