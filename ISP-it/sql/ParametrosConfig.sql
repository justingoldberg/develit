# MySQL dump 8.16
#
# Host: localhost    Database: authentic_it
#--------------------------------------------------------
# Server version	4.0.12-log

#
# Table structure for table 'parametros'
#

CREATE TABLE ParametrosConfig (
  id int(11) NOT NULL auto_increment,
  descricao varchar(200) default NULL,
  parametro varchar(20) default NULL,
  valor varchar(200) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

