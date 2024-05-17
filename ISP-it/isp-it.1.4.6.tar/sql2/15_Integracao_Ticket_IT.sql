-- Par�metro que informa se o ISP est? integrado ao ticket ou n?o.
-- Se integrado, ativa os comandos adicionais

INSERT INTO ParametrosConfig (descricao, parametro, valor) VALUES('Define se o ISP esta integrado ou nao ao Ticket-IT. Se integrado, faz a ativacao de opoces extras', 'integrarTicket','N');


-- Cria��o de tabela MaquinasSuporte para ligar relacionamentos entre as tabelas Suporte do ISP e Maquinas do Ticket

-- Parametro de configura��o das m�quinas
INSERT INTO Parametros VALUES('', 'Quantidade de M�quinas por Servi�o', 'nr', '1', 'suporte');

-- M�dulo do gerenciamento de suporte

INSERT INTO Modulos VALUES('', 'Suporte - Quantidade de M�quinas para Suporte', 'suporte');

CREATE TABLE MaquinasSuporte(
	id INT(11) NOT NULL AUTO_INCREMENT,	
	idSuporte INT(11),
	idMaquina INT(11),
	PRIMARY KEY(id)
);
