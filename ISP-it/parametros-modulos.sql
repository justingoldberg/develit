INSERT INTO Modulos VALUES (1,'ISP-IT - Sistema Integrado de Controle de Provedor','isp');
INSERT INTO Modulos VALUES (2,'Mail - Dominios e Contas de Email','mail');
INSERT INTO Modulos VALUES (3,'Dial-UP - Acesso Discado','dial');
INSERT INTO Modulos VALUES (4,'Web - Dominios e Serviço de Hospedagem','web');
INSERT INTO Modulos VALUES (5,'Dominio - Registro e Hospedagem de Domínios','dominio');
INSERT INTO Modulos VALUES (6,'IVR - Internet Via Radio','ivr');


INSERT INTO Parametros VALUES (1,'Valor mínimo para faturamento - pré-pago','nr',5,'minimo_prepago');
INSERT INTO Parametros VALUES (2,'Valor mínimo para faturamento - pós-pago','nr',5,'minimo_pospago');
INSERT INTO Parametros VALUES (3,'Quantidade de Contas de Email','nr',1,'qtde');
INSERT INTO Parametros VALUES (4,'Quota de espaço em disco','nr',2,'quota');
INSERT INTO Parametros VALUES (5,'Acesso ao Webmail','sn',0,'webmail');
INSERT INTO Parametros VALUES (6,'Anti-Virus','sn',0,'antivirus');
INSERT INTO Parametros VALUES (7,'Filtros de Email','sn',0,'filtros');
INSERT INTO Parametros VALUES (8,'Acesso a Central do Assinante','sn',0,'centralassinante');
INSERT INTO Parametros VALUES (9,'Quantidade de Contas de Acesso Dial-UP','nr',1,'qtde');
INSERT INTO Parametros VALUES (10,'Espaço em disco para Hospedagem','nr',2,'quota');
INSERT INTO Parametros VALUES (11,'Velocidade de Acesso Kbps','nr',3,'velocidade');
INSERT INTO Parametros VALUES (12,'Velocidade de Acesso Mbps','nr',4,'velocidade');
INSERT INTO Parametros VALUES (13,'Horas de acesso','nr',6,'horas');
INSERT INTO Parametros VALUES (14,'Recepção de Mensagens em caixa postal','sn',0,'bounce');
INSERT INTO Parametros VALUES (15,'POP3 - Acesso a mensagens via POP','sn',0,'pop3');
INSERT INTO Parametros VALUES (16,'IMAP - Acesso a mensagens via IMAP','sn',0,'imap');
INSERT INTO Parametros VALUES (17,'Envio de mensagens para domínios externos (relay)','sn',0,'relay');
INSERT INTO Parametros VALUES (18,'Senha - Troca de senha para conta','sn',0,'trocasenha');
INSERT INTO Parametros VALUES (19,'Registro de Dominio','nr',1,'qtde');


INSERT INTO ParametrosModulos VALUES (1,2);
INSERT INTO ParametrosModulos VALUES (1,1);
INSERT INTO ParametrosModulos VALUES (3,9);
INSERT INTO ParametrosModulos VALUES (2,8);
INSERT INTO ParametrosModulos VALUES (2,5);
INSERT INTO ParametrosModulos VALUES (2,6);
INSERT INTO ParametrosModulos VALUES (2,7);
INSERT INTO ParametrosModulos VALUES (2,3);
INSERT INTO ParametrosModulos VALUES (2,4);
INSERT INTO ParametrosModulos VALUES (4,10);
INSERT INTO ParametrosModulos VALUES (2,17);
INSERT INTO ParametrosModulos VALUES (2,16);
INSERT INTO ParametrosModulos VALUES (2,15);
INSERT INTO ParametrosModulos VALUES (2,14);
INSERT INTO ParametrosModulos VALUES (2,18);
INSERT INTO ParametrosModulos VALUES (5,19);
INSERT INTO ParametrosModulos VALUES (5,10);

