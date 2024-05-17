<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 17/09/2003
# Ultima alteração: 29/01/2004
#    Alteração No.: 004
#
# Função:
#    Funções de contas a receber


# função de busca 
function buscaContasReceber($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ContasReceber] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ContasReceber] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ContasReceber] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ContasReceber] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
} # fecha função de busca



# Função para adicionar documentos ao Contas a Receber
function adicionarDocumentosGeradosContasReceber($matriz) {

	# Consultar documentos Gerados com status de "Inativos" 
	# para adicionar ao contas a Receber
	$matServicosAdicionais=array();

	$consultaDocumentosGerados=buscaDocumentosGerados("idFaturamento=$matriz[idFaturamento] AND status='I'", '','custom','id');

	
	$retorno[servicosAdicionais]=0;
	$retorno[contasReceber]=0;

	if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
		# Adicionar documentos ao contas a receber


		for($a=0;$a<contaConsulta($consultaDocumentosGerados);$a++) {
			# identificação do documento
			$idDocumentoGerado=resultadoSQL($consultaDocumentosGerados, $a, 'id');
			$idPessoaTipo=resultadoSQL($consultaDocumentosGerados, $a, 'idPessoaTipo');

			# Valor do Documento Gerado
			$valor=valorDocumentosGerados($idDocumentoGerado);
			
			$matriz[idDocumentosGerados]=$idDocumentoGerado;
			$matriz[valor]=$valor;
			
			# Verificar se documento tem faturamento maior ao faturamento minimo
			/* Procedimentos
			   Checar o valor minimo configurado.
					- Em caso de valor definido, verificar se o boleto não é referente
					  a cobrança de finalização
					- Em caso de valor nao definido, faturar normalmente
			*/

			$parametros=carregaParametrosConfig();

			if(is_numeric($parametros[faturamento_minimo])) $valorMinimo=$parametros[faturamento_minimo];
			$faturaCancelados = $parametros[fatura_cancelados];
			
			//printf(" <br><br>idDocumento: %s , valor: %s , minimo: %s", $matriz[idDocumentosGerados], $matriz[valor], $valorMinimo );
			if($matriz[valor] > 0 && $matriz[valor] >= $valorMinimo) {
				$grava=dbContasReceber($matriz,'incluir');

				$retorno[contasReceber]++;
			}
			elseif($matriz[valor] > 0 && $matriz[valor] < $valorMinimo) {
				# Lançar como serviço adicional
				$matriz[idPessoaTipo]=$idPessoaTipo;
				$grava=adicionarServicoAdicionalFaturaInferior($matriz);
				
				if(!is_array($grava) && $grava == 0) {
					$grava=dbContasReceber($matriz,'incluir');
					$retorno[contasReceber]++;
				}
				else {
					$matServicosAdicionais[]=$grava;
					$retorno[servicosAdicionais]++;
				}
			}
		}
	}

	if(count($matServicosAdicionais)>0) {
		$retorno[lancamentos]=$matServicosAdicionais;
	}
	
	return($retorno);
}


# Função para adicionar documentos ao Contas a Receber
function excluirDocumentosGeradosContasReceber($idFaturamento) {

	# Consultar documentos Gerados com status de "Inativos" 
	# para adicionar ao contas a Receber
	$consultaDocumentosGerados=buscaDocumentosGerados("idFaturamento=$idFaturamento AND status='C'", '','custom','id');
	
	if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
		# Adicionar documentos ao contas a receber
		for($a=0;$a<contaConsulta($consultaDocumentosGerados);$a++) {
			# identificação do documento
			$idDocumentoGerado=resultadoSQL($consultaDocumentosGerados, $a, 'id');
			
			$matriz[idDocumentosGerados]=$idDocumentoGerado;
			
			$grava=dbContasReceber($matriz,'excluir');
		}
	}	
	
}



# Funções de banco de dados
function dbContasReceber($matriz, $tipo) {

	global $tb, $conn;

	$data=dataSistema();
	
	if($tipo=='incluir') {
		$sql="
			INSERT INTO 
				$tb[ContasReceber] 
			VALUES (
				0,
				'$matriz[idDocumentosGerados]', 
				'$matriz[valor]', 
				0,
				0,
				0,
				'$data[dataBanco]',
				'$matriz[dtVencimento]',
				'',
				'',
				'$matriz[obs]',
				'P'
			)";
	}
	elseif($tipo=='excluir') {
		$sql="
			DELETE FROM 
				$tb[ContasReceber]
			WHERE
				idDocumentosGerados=$matriz[idDocumentosGerados]
		";
	}
	elseif($tipo=='baixar') {

//		/*chama a funcao que verifica se o evento baixar possui contra partidas, executando-as*/
		contraPartidaChecarDocumento($matriz[id], $matriz[valorRecebido]);
		
		
		$matriz[dtBaixa]=converteData($matriz[dtBaixa],'form','banco');
		
		# insere este Credito no fluxo de caixa
		fluxoDeCaixaCreditar($matriz['id'], $matriz['valorRecebido'], $matriz['dtBaixa']);
		
		$sql="
			UPDATE
				$tb[ContasReceber]
			SET
				valorRecebido='$matriz[valorRecebido]',
				valorJuros='$matriz[valorJuros]',
				valorDesconto='$matriz[valorDesconto]',
				dtBaixa='$matriz[dtBaixa]',
				obs='$matriz[obs]',
				status='B'
			WHERE
				id=$matriz[id]
		";
	}
	elseif($tipo=='estornar') {
		$sql="
			UPDATE
				$tb[ContasReceber]
			SET
				valor='$matriz[valor]', 
				valorRecebido='$matriz[valorRecebido]',
				valorJuros='$matriz[valorJuros]',
				valorDesconto='$matriz[valorDesconto]',
				dtBaixa='$matriz[dtBaixa]',
				dtCancelamento='$matriz[dtCancelamento]',
				obs='$matriz[obs]',
				status='$matriz[status]'
			WHERE
				id=$matriz[id]
		";
	}
	elseif($tipo=='cancelar') {
		$sql="
			UPDATE
				$tb[ContasReceber]
			SET
				dtCancelamento='$matriz[dtCancelamento]',
				obs='$matriz[obs]',
				status='C'
			WHERE
				id=$matriz[id]
		";
	}
	
	if($sql) {
		$consulta=consultaSQL($sql, $conn);
	}
	
	return($consulta);
}



# Função para checagem de cobrança de serviço em data especificada
function checkLancamentoCobrancaServico($idServicoPlano, $mes) {

	global $conn, $tb;

	$sql="
		SELECT
			$tb[ContasReceber].idDocumentosGerados, 
			$tb[ContasReceber].dtVencimento, 
			$tb[ContasReceber].valor 
		FROM
			$tb[ContasReceber], 
			$tb[ServicosPlanosDocumentosGerados], 
			$tb[PlanosDocumentosGerados], 
			$tb[DocumentosGerados], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados=$tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].id=$tb[PlanosDocumentosGerados].idDocumentoGerado 
			AND $tb[PlanosDocumentosGerados].id=$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado 
			AND $tb[DocumentosGerados].idFaturamento=$tb[Faturamentos].id
			AND $tb[ServicosPlanosDocumentosGerados].idServicosPlanos=$idServicoPlano 
			AND LEFT(RIGHT($tb[ContasReceber].dtVencimento,5),2)=$mes
			";

	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) return(1);
	else return(0);

}


function dadosContasAReceberConsultado( $consulta ){
	$ret = array();
	
	if ( contaConsulta( $consulta ) == 1 ){
		$ret["id"]	 				= resultadoSQL($consulta, 0, "id");
		$ret["idDocumentosGerados"] = resultadoSQL($consulta, 0, "idDocumentosGerados");
		$ret["valor"]				= resultadoSQL($consulta, 0, "valor");
		$ret["dtVencimento"]		= resultadoSQL($consulta, 0, "dtVencimento");
		$ret["status"]				= resultadoSQL($consulta, 0, "status");
	}
	return $ret;
}

/**
 * busca os dados de uma conta a receber a partir de um documento gerado
 *
 * @param int $idDocumentosGerados
 * 
 * @return array com os dados da conta a receber.
 */
function dadosContasAReceber( $idDocumentosGerados ){
	$ret = array();
	
	$consulta = buscaContasReceber( $idDocumentosGerados, "idDocumentosGerados", "igual", "id");
	
//	
//	if ( contaConsulta( $consulta ) == 1 ){
//		$ret["id"] 				= resultadoSQL($consulta, 0, "id");
//		$ret["valor"]			= resultadoSQL($consulta, 0, "valor");
//		$ret["dtVencimento"]	= resultadoSQL($consulta, 0, "dtVencimento");
//		$ret["status"]			= resultadoSQL($consulta, 0, "status");
//	}
	
	if( count( $consulta ) ) {
		$ret = dadosContasAReceberConsultado( $consulta);
	
		if(!$ret){
			echo "<br>";
			avisoNOURL( "AVISO", "Sem Contas a Receber para o documento ". $idDocumentosGerados, 400);
		}
	
		return $ret;
	}
}

/**
 * busca dados de uma conta a receber a partir de seu id
 *
 * @param int $id
 * @return array Dados da conta a receber
 */
function dadosContasReceberId( $id ){
	
	$consulta = buscaContasReceber( $id, 'id', 'igual', 'id');
	
	$ret = dadosContasAReceberConsultado( $consulta );
	
	if( !$ret ){
		echo "<br>";
		avisoNOURL( "AVISO", "Sem Contas A Receber para o ID " . $id, 400 );
	}
	
	return $ret;
}

/**
 * Função de gerenciamento da tabela Contas a Receber
 *
 * @return unknown
 * @param array   $matriz
 * @param string  $tipo
 * @param string  $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 */
function consultaContasReceber( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;

	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['ContasReceber'];
	$campos  = array( 'id',		'idDocumentosGerados',	            'valor', 			'valorRecebido', 			'valorJuros', 			'valorDesconto', 			'dtCadastro', 
					  'dtVencimento', 			'dtBaixa', 			'dtCancelamento', 			'obs',          'status' );
	$valores = array( 'NULL',	$matriz['idDocumentosGerados'], 	$matriz['valor'],	$matriz['valorRecebido'],	$matriz['valorJuros'],  $matriz['valorDesconto'],   $matriz['dtCadastro'], 
	                  $matriz['dtVencimento'],  $matriz['dtBaixa'], $matriz['dtCancelamento'],  $matriz['obs'], $matriz[ 'status']);

	if ( $tipo == 'consultar' ){
		$tabelas =	"{$tb['ContasReceber']}\n".
						"LEFT JOIN {$tb['DocumentosGerados']} ON ({$tb['ContasReceber']}.idDocumentosGerados = {$tb['DocumentosGerados']}.id)\n".
						"LEFT JOIN {$tb['PessoasTipos']} ON ({$tb['PessoasTipos']}.id = {$tb['DocumentosGerados']}.idPessoaTipo)\n".
						"LEFT JOIN {$tb['Pessoas']} ON ({$tb['PessoasTipos']}.idPessoa = {$tb['Pessoas']}.id)\n".
						"LEFT JOIN {$tb['POP']} ON ({$tb['POP']}.id = {$tb['Pessoas']}.idPop)\n";
		$campos =	array("{$tb['ContasReceber']}.*", "{$tb['Pessoas']}.nome as pessoa", "{$tb['POP']}.nome as pop", "{$tb['PessoasTipos']}.id as idPessoaTipo" );
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	return $retorno;
}

?>
