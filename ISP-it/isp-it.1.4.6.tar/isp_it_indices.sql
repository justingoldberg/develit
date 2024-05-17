# Hugo - 12/07/2004
# versao 1.0.24
# Indices para os relatorio de grupos
#
alter table ServicosGrupos add index (idServico);
alter table ServicosGrupos add index (idGrupos);
alter table DocumentosGerados add index(idPessoaTipo);
alter table ContasAReceber add index (idDocumentosGerados);
alter table PlanosDocumentosGerados add index (idDocumentoGerado);
alter table ServicosPlanosDocumentosGerados add index (idPlanoDocumentoGerado);
alter table ServicosPlanosDocumentosGerados add index (idServicosPlanos);
alter table ServicosPlanos add index (idServico);

# Rogério - 21-12-2004
# Aplicacao de indices para relacionamentos
ALTER TABLE PlanosPessoas ADD INDEX idVencimento (idVencimento);
