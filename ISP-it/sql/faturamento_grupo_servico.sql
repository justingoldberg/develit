SELECT 
Pop.id idPOP, 
Pop.nome nomePOP, 
GruposServicos.id idGrupoServico, 
GruposServicos.nome grupo, 
LEFT(ContasAReceber.dtVencimento, 7) dtVencimento,
SUM(ServicosPlanosDocumentosGerados.valor) valor,
SUM(ContasAReceber.valorJuros) juros, 
SUM(ContasAReceber.valorDesconto) desconto
FROM 	
Pop,
Pessoas, 
PessoasTipos, 
DocumentosGerados, 
ContasAReceber, 
PlanosDocumentosGerados, 
ServicosPlanosDocumentosGerados, 
ServicosPlanos, 
ServicosGrupos, 
GruposServicos 
WHERE
Pop.id = Pessoas.idPOP
AND Pessoas.id = PessoasTipos.idPessoa 
AND PessoasTipos.id=DocumentosGerados.idPessoaTipo 
AND DocumentosGerados.id=ContasAReceber.idDocumentosGerados 
AND PlanosDocumentosGerados.idDocumentoGerado=DocumentosGerados.id  
AND ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado=PlanosDocumentosGerados.id 
AND ServicosPlanos.id=ServicosPlanosDocumentosGerados.idServicosPlanos 
AND ServicosGrupos.idServico=ServicosPlanos.idServico 
AND GruposServicos.id=ServicosGrupos.idGrupos   
AND ContasAReceber.status IN ('P', 'B')
AND ContasAReceber.dtVencimento BETWEEN '2004-06-01' AND '2004-12-31 11:59:59'
GROUP BY GruposServicos.id, Pop.id, LEFT(ContasAReceber.dtVencimento, 7)
ORDER BY Pop.id, GruposServicos.nome, ContasAReceber.dtVencimento 
