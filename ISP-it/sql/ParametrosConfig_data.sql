-- MySQL dump 9.11
--
--
-- Dumping data for table `ParametrosConfig`
--

INSERT INTO `ParametrosConfig` VALUES (1,'Faturamento Mínimo','faturamento_minimo','10');
INSERT INTO `ParametrosConfig` VALUES (2,'Manager - Criação automática/manual de dominios','manager_dominio_add','manual');
INSERT INTO `ParametrosConfig` VALUES (3,'Manager - Exclusão automática/manual de dominios','manager_dominio_del','manual');
INSERT INTO `ParametrosConfig` VALUES (4,'Cadastro de Pessoas com documento único (CPF/CNPJ)','documento_unico','S');
INSERT INTO `ParametrosConfig` VALUES (5,'Caminho da Impressora de Notas Fiscais na Rede','path_impressora','EpsonLX300');
INSERT INTO `ParametrosConfig` VALUES (6,'Instruções Personalizadas dos boletos Itaú','instrucaoitau','Sujeito corte serviço após 20 dias vecto');
INSERT INTO `ParametrosConfig` VALUES (7,'S para gerar cobrancas\ninferiores ao faturamento minimo se nao ouver fatura posterior, ou N\npara nao gerar','fatura_cancelados','N');
INSERT INTO `ParametrosConfig` VALUES (8,'caso deseje que  nao seje  descontado o valor do irrf na nota fiscal setar este atributo com o valor de N','descontar_irrf','S');
INSERT INTO `ParametrosConfig` VALUES (9,'mensagem a ser exibida caso nao descontar irrf','msg_descontar_irrf','IRRF 1,5 :');


INSERT INTO `ParametrosConfig` VALUES (null,'email ou lista de emails, separados por virgulas, que receberao os avisos financeiros.','email_financeiro','gustavomonarin@gmail.com');
INSERT INTO `ParametrosConfig` VALUES (null,'email_inativacao, conteudo da mensagem a ser enviada ao financeiro informando a inativacao do servico. chaves: *servico*, *pessoa*, *dtInativacao*, *dtVencimento*, *valor*','msg_inativacao','A inativacao do servico *servico*  da pessoa *pessoa* gerou um servico adicional no valor de R$ *valor* , com vencimento em *dtVencimento*');
INSERT INTO `ParametrosConfig` VALUES (null,'email_inativacao, assunto do email que sera enviado ao lancar servico adicional pela inativacao de servicos.','msg_inativ_assunto','Inativação de Serviço gerou serviço adicional');
INSERT INTO `ParametrosConfig` VALUES (null,'email_cancelamento, assunto do email que sera enviado ao lancar servico adicional pelo cancelamento de servicos.','msg_cancel_assunto','Cancelamento de Serviço gerou serviço adicional');
INSERT INTO `ParametrosConfig` VALUES (null,'email_cancelamento, conteudo da mensagem a ser enviada ao financeiro informando que o cancelamento do servico gerou servico adicional. chaves: *servico*, *pessoa*, *dtInativacao*, *dtVencimento*, *val','msg_cancelamento','O cancelamento do servico *servico* da pessoa *pessoa* gerou um servico adicional no valor de R$ *valor* , com vencimento em *dtVencimento*');


INSERT INTO `ParametrosConfig` VALUES (null,'email_ativacao, assunto do email que sera enviado ao lancar servico adicional  ou descontos pela ativacao do servico.','msg_ativ_assunto','Ativação de Serviço gerou');
INSERT INTO `ParametrosConfig` VALUES (null,'email_ativacao, conteudo da mensagem a ser enviada ao financeiro informando que a ativacao do servico gerou servico adicional ou desconto. chaves: *servico*, *pessoa*, *dtInativacao*, *dtVencimento*, *val','msg_ativacao','A ativacao do servico *servico* da pessoa *pessoa* gerou um *tipoServico* no valor de R$ *valor* , com vencimento em *dtVencimento*');
