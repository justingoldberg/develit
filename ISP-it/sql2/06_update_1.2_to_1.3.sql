
--
-- Table structure for table `PopPessoaTipo`
--

DROP TABLE IF EXISTS `PopPessoaTipo`;
CREATE TABLE `PopPessoaTipo` (
  `idPop` int(11) NOT NULL auto_increment,
  `idPessoaTipo` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idPop`),
  KEY `idPessoaTipo` (`idPessoaTipo`)
);

INSERT INTO TipoPessoa VALUES (NULL, 'POP', 'pop');
