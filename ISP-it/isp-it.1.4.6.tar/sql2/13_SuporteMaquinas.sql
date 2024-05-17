CREATE TABLE `Suporte` (
  `id` int(11) NOT NULL auto_increment,
  `idServicoPlano` int(11) NOT NULL default '0',
  `horasExpediente` int(5) NOT NULL default '0',
  `horasForaExpediente` int(5) default NULL,
  `prioridade` char(1) default NULL,
  `suporteForaExpediente` char(1) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
);


CREATE TABLE `Maquinas` (
  `id` int(11) NOT NULL auto_increment,
  `idServicoPlano` int(11) NOT NULL default '0',
  `idSuporte` int(11) default NULL,
  `hostName` varchar(100) default NULL,
  `ip` varchar(15) default NULL,
  `observacao` text,
  PRIMARY KEY  (`id`)
);

insert into Modulos (descricao,modulo) values ('Suporte - Horas de suporte técnico','suporte');
insert into Modulos (descricao,modulo) values ('Maquinas - Servidores com soluções instaladas','maquinas');

insert into Parametros (descricao,tipo,idUnidade,parametro) values ('Horas de Suporte','nr',7,'suporte');
insert into Parametros (descricao,tipo,idUnidade,parametro) values ('Quantidade de maquinas para o serviço','nr',1,'maquinas');

insert into ParametrosModulos (idModulo,idParametro) values ((select id from Modulos where modulo = 'maquinas'),(select id from Parametros where parametro = 'maquinas'));
insert into ParametrosModulos (idModulo,idParametro) values ((select id from Modulos where modulo = 'suporte'),(select id from Parametros where parametro = 'suporte'));