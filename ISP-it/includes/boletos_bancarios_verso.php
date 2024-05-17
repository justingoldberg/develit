<?

/**
 * Retorna o html do Verso do Boleto de todos os documentos encontrados no faturamenteo de $registro
 *
 * @param integer $registro
 * @return string
 */
function gerarVersoBoletosFaturamento($registro) {
	$arquivo=""; // inicia o conteudo do arquivo

	$consultaDocumentosGerados = buscaDocumentosGerados( $registro, "idFaturamento", "igual", "id" );
	#recolhe os arquivos html dos documentos
	for ( $i=0; $i<contaConsulta( $consultaDocumentosGerados ); $i++ ){
		# recebe a id do documento gerado
		$id	= resultadoSQL( $consultaDocumentosGerados, $i, "id");
		$arquivo .= gerarVersoBoletosDocumentosGerados( $id );
//		# recebe a id do sacado
//		$idPessoaTipo	= resultadoSQL( $consultaDocumentosGerados, $i, "idPessoaTipo");
//		# deixa o dado no verso
//		$dadosSacado = dadosSacadoBoletoVerso( $idPessoaTipo );
//		# Busca os dados do cedente (pessoa pop)
//		$dadosCedente = dadosCedenteBoletoVerso( $dadosSacado['POP'] );		
//		
//		# recebe o conteudo do documento deste sacado
//		$arquivo .= gerarVersoBoletosDocumentosHTML($dadosCedente, $dadosSacado );
	}

	return $arquivo;
}

/**
 * Retorna o html do verso do boleto do Documento do $registro
 *
 * @param integer $registro
 * @return string
 */
function gerarVersoBoletosDocumentosGerados($registro) {
	$arquivo = ''; //inicia o conteudo do arquivo
	
	# busca os dados da pessoa do documentos (sacado)
	$consultaDocumentosGerados = buscaDocumentosGerados( $registro, "id", "igual", "id" );

	if( $consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados) ) {
		// verifica se este documento tem conta a receber
		$consulta = buscaContasReceber( $registro, "idDocumentosGerados", "igual", "id");
		
		// se tiver então gera o verso
		if( contaConsulta( $consulta ) ) {
			# recebe a id do sacado
			$idPessoaTipo = resultadoSQL( $consultaDocumentosGerados, 0, 'idPessoaTipo');
			# deixa o dado no verso
			$dadosSacado = dadosSacadoBoletoVerso($idPessoaTipo);
			# Busca os dados do cedente (pessoa pop)
			$dadosCedente = dadosCedenteBoletoVerso( $dadosSacado['POP'] );
			# recebe o conteudo do documento deste sacado
			$arquivo .= gerarVersoBoletosDocumentosHTML( $dadosCedente, $dadosSacado);
		}
		
	}
	return $arquivo;
}

/**
 * Retorna o html com os dados do boleto do documento preenchido
 *
 * @param array $dadosCedente
 * @param array $dadosSacado
 * @return unknown
 */
function gerarVersoBoletosDocumentosHTML( &$dadosCedente, &$dadosSacado ) {
	global $html;

	# junta os dados do cedente com os dados do sacado
	$dadosVersoBoleto = array_merge($dadosCedente, $dadosSacado);
	$dadosVersoBoleto[LOGO] = '../../'.$html[imagem][logoMedia];
	$dadosVersoBoleto[BLACK] = '../../'.$html[imagem][black];
	#caminho onde encontra o layout
	$template = 'boleto_bancario_verso';
	# abre o arquivo de template
	$fileHTML=k_templateLoad($template);
	$retorno = k_templateParse($fileHTML, $dadosVersoBoleto );

	return $retorno;
}

/**
 * Retorna os dados do Cedente pronto para ser usado como tag
 *
 * @param integer $idPop
 * @return array
 */
function dadosCedenteBoletoVerso( $idPop ) {
	// faz consulta com a pessoa Pop	
	$conta = dbPopPessoaTipo('', "consultar",'', "idPop='".$idPop."'");

	// se encontrou a pessoa pop
	if(count($conta) ) {
		// pega a idPessoaTipo deste pop
		$idPessoaTipo = $conta[0]->idPessoaTipo;
		// Busca os dados desta pessoa
		$dadosPessoa = dadosPessoasTipos( $idPessoaTipo, 'cob');
		
		if( is_array( $dadosPessoa ) && count( $dadosPessoa ) ) {
			$retorno[RAZAO_CEDENTE] 		= $dadosPessoa["pessoa"]["razao"];
			$retorno[ENDERECO_CEDENTE]		= $dadosPessoa['endereco']['endereco'];
			if( !empty($dadosCedente[complemento]) ) {
				$retorno[ENDERECO_CEDENTE] .= ' - '.$dadosPessoa['endereco']['complemento'];	
			}
			$retorno[BAIRRO_CEDENTE]		= $dadosPessoa['endereco']['bairro'];
			$retorno[CIDADE_CEDENTE]		= $dadosPessoa['endereco']['cidade'];
			$retorno[UF_CEDENTE]			= $dadosPessoa['endereco']['uf'];
			$retorno[CEP_CEDENTE]			= $dadosPessoa['endereco']['cep'];
		}

	}
	
	return $retorno;
}

/**
 * Retorno os dados do Sacado pronto para ser usado como tag
 *
 * @param integer $idPessoaTipo
 * @return array
 */
function dadosSacadoBoletoVerso( $idPessoaTipo ) {
	# carrega o nome
	$dadosPessoa = dadosPessoasTipos( $idPessoaTipo, 'cob');
	if( is_array( $dadosPessoa ) && count( $dadosPessoa ) ) {
		$retorno[NOME_SACADO] 			= ( ( $dadosPessoa["pessoa"]["tipoPessoa"] == "J" && !empty( $dadosPessoa["pessoa"]["razao"] ) )
											  ? $dadosPessoa["pessoa"]["razao"]
											  : $dadosPessoa["pessoa"]["nome"]);
		$retorno[ENDERECO_SACADO]		= $dadosPessoa['endereco']['endereco'];
		if( !empty($dadosPessoa['endereco']['complemento']) ) {
			$retorno[ENDERECO_SACADO]  .= ' - '.$dadosPessoa['endereco']['complemento'];
		}
		$retorno[BAIRRO_SACADO]			= $dadosPessoa['endereco']['bairro'];
		$retorno[CIDADE_SACADO]			= $dadosPessoa['endereco']['cidade'];
		$retorno[UF_SACADO]				= $dadosPessoa['endereco']['uf'];
		$retorno[CEP_SACADO]			= $dadosPessoa['endereco']['cep'];
		$retorno[POP]					= $dadosPessoa['pessoa']['idPOP'];
	}
	return $retorno;
}

?>