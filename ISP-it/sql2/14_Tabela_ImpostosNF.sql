--
-- Tabela ImpostosNF para armazenar temporáriamente os impostos a serem impressos na Nota Fiscal 
--

CREATE TABLE ImpostosNF(
id BIGINT(11) unsigned NOT NULL auto_increment,
idNF BIGINT(11) unsigned NOT NULL DEFAULT '0',
descricao VARCHAR(255) NOT NULL DEFAULT '',
porcentagem double NOT NULL default '0',
PRIMARY KEY(id),
KEY(idNF)
);

--
-- Querys para inserção dos parâmetros extras para impressão de notas fiscais
--

--
-- Query para inserir o parâmetro que vai definir se o total da nota fiscal deve ser impresso ou não
-- Valor padrão: S (p/ TDkom) (N p/ Devel)

INSERT INTO ParametrosConfig(descricao, parametro, valor) 
VALUES('Caso deseje que nao seja descontado o valor total da nota fiscal setar este atributo com o valor de N.',
 'desconto_total_nota', 'S');
 
INSERT INTO ParametrosConfig(descricao, parametro, valor) 
VALUES('Este parametro define o valor minimo que a nota fiscal deve ter para realizar o desconto dos impostos referentes',
 'valor_min_imposto_nf', '5000');
 
 --
 -- Querys para isersão dos impostos necessários para aplicar descontos na Nota Fiscal
 --
 
INSERT INTO TiposImpostos (tipo, descricao) VALUES ('PIS', 'Impostos sobre o Programa de Integração Social');
INSERT INTO TiposImpostos (tipo, descricao) VALUES ('COFINS', 'Imposto sobre a Contribuição para o Financiamento de Seguridade Social'); 
INSERT INTO TiposImpostos (tipo, descricao) VALUES ('CSSL', 'Imposto sobre a Contribuição Social sobre o Lucro Líquido');
INSERT INTO TiposImpostos (tipo, descricao) VALUES ('IRRF', 'Imposto de Renda Retida na Fonte');
