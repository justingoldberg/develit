ALTER TABLE FormaCobranca ADD COLUMN codFlash char(3);
INSERT INTO TipoCarteira VALUES ('','Duplicata Registrada Layout Mensagem','Duplicatas Registradas com  instrucao de cobranca e mensagem no bloqueto','M');
ALTER TABLE PlanosPessoas ADD COLUMN desconto BOOLEAN NOT NULL DEFAULT 0 AFTER especial;
