<?

/**
 * Contrutor do modulo Fluxo de Caixa. Ele apenas é utilizando quando deseja que seja 
 * mostrado a listagem, sendo descenessária sua chamava prévia ao Debitar ou Creditar 
 * contas no Fluxo.
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function fluxoDeCaixa( $modulo, $sub, $acao, $registro, $matriz ){
	
	# se não existe pop ele busca a primeira ativa que encontrar.
	if( !$matriz['pop'] ) {
		$consultaPOP	= buscaPOP( 'A','status','igual','nome' );
		$matriz['pop']	= resultadoSQL( $consultaPOP, 0, 'id' );
	}
	
	/*prepara listagem verificando fechamentos anteriores.*/
	fluxoDeCaixaPreparaListagem(  $modulo, $sub, $acao, $registro, $matriz  );
	
	# Exibe o fluxo de caixa do mes atual;
	fluxoDeCaixaListagem( $modulo, $sub, $acao, $registro, $matriz );
}

/**
 * Gerencia a leitura/gravação na tabela FluxoDeCaixa.
 *
 * @param array   $matriz
 * @param unknown $tipo
 * @param unknown $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return array
 */
function dbFluxoDeCaixa($matriz, $tipo, $subTipo='', $condicao='', $ordem = 'data') {
	global $conn, $tb;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['FluxoDeCaixa'];
	$campos  = array( 'id', 'idConta', 'tipo', 'descricao', 'data', 'valor', 'idPop' );
	
	if ( $tipo == 'inserir' ){
		$valores = array( "NULL", $matriz['idConta'], $matriz['tipo'], $matriz['descricao'], $matriz['data'], $matriz['valor'], $matriz['idPop'] );
		$retorno = $bd->inserir($tabelas, $campos, $valores);
		
		/* se ja houver ocorrido fechamento deste periodo recalcular.*/
		if( $retorno && $matriz['tipo'] != 'F' ){

			fluxoDeCaixaAtualizarFechamento( $matriz['idPop'], $matriz['data'] );
		}
	}
		
	if ( $tipo == 'alterar' ){
		$valores = array( $matriz->idConta, $matriz->tipo, $matriz->descricao, $matriz->data, $matriz->valor, $matriz->idPop );
		array_shift( $campos );
		$condicao = array();
		$condicao[] = "id ='".$matriz->id."'";
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}
	
	if ( $tipo == 'consultar' ){
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	
	if( $tipo == 'somarValores'){
		$campos = array( 'tipo', ' sum(valor) as total ');
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, "tipo", 'tipo');
	}
	
	return ($retorno);
}

/**
 * lanca um credito no fluxo de caixa. 
 *
 * @param integer $idConta
 * @param double  $valor
 * @param string  $data
 * @param string  $isEstorno
 */
function fluxoDeCaixaCreditar( $idConta, $valor, $data, $isEstorno = false ){
	
	/*dados necessarios para a descricao do fluxo de caixa.*/
	
	@mysql_query("Entrou na função fluxoDeCaixaCreditar()");
	
	$contaAReceber		= dadosContasReceberId( $idConta );
	$documentosGerados	= documentosGeradosDadosId( $contaAReceber['idDocumentosGerados'] );
	$dadosPessoasTipos	= dadosPessoasTipos( $documentosGerados['idPessoaTipo']);
	$pessoas			= dadosPessoas($dadosPessoasTipos['idPessoa']);
	
	$inserirMatriz = array();
	$inserirMatriz['idConta'] 	= $idConta;
	$inserirMatriz['tipo'] 		= fluxoDeCaixaGetTipoCredito();
	$inserirMatriz['descricao']	= ( $isEstorno 
									? "Crédito de estorno do cliente " . $dadosPessoasTipos['pessoa']['nome'] 
									: "Crédito do cliente " . $dadosPessoasTipos['pessoa']['nome']
								  );

	$inserirMatriz['data']		= $data;
	$inserirMatriz['valor']		= $valor;
	$inserirMatriz['idPop'] 	= $pessoas['idPOP'];
	
	dbFluxoDeCaixa( $inserirMatriz, 'inserir');
	
}

/**
 * lanca um debito no fluxo de caixa
 *
 * @param integer $idConta
 * @param double $valor
 * @param string $data
 */
function fluxoDeCaixaDebitar( $idConta, $valor, $data ){
	
	/*dados necessarios para a descricao do fluxo de caixa.*/	
	$matConta['id'] = $idConta;
	$contaAPagar	= dbContasAPagar( $matConta, 'consultar', 'unicoBasico' );
	$detalheConta	= dbPlanoDeContasDetalhes('', 'consultar', '', 'id=' . $contaAPagar[0]->idPlanoDeContasDetalhes );
	$fornecedor		= dadosPessoasTipos( $contaAPagar[0]->idFornecedor );

	
	$inserirMatriz = array();
	$inserirMatriz['idConta']	= $idConta;
	$inserirMatriz['tipo']		= fluxoDeCaixaGetTipoDebito();
	$inserirMatriz['descricao']	= "Debito do(a) " . $detalheConta[0]->nome . " a(o) " . $fornecedor['pessoa']['nome'];
	$inserirMatriz['data']		= $data;
	$inserirMatriz['valor']		= $valor;
	$inserirMatriz['idPop']		= $contaAPagar[0]->idPop;
	
	dbFluxoDeCaixa( $inserirMatriz, 'inserir');
	
}


/**
 * Credita o valor de estorno
 *
 * @param integer $idConta
 * @param float   $valor
 * @param string  $data
 */
function fluxoDeCaixaEstorno( $idConta, $valor, $data ) {
	
	$result = dbFluxoDeCaixa('', 'consultar', '', array( "idConta = '".$idConta."'" ));
	@mysql_query("Entrou na função fluxoDeCaixaEstorno()");
	if( count( $result ) ) {
		$valorEstorno = $valor - $result[0]->valor;

		fluxoDeCaixaCreditar( $idConta, $valorEstorno, $data, true );

	}
	
}

/**
 * Calcula o Fechamento mensal para gravar no banco de dados. (Para fins de performance)
 *
 * @param integer $idPop
 * @param integer $mes
 * @param integer $ano
 * @param boolean $recursivo
 * @return unknown
 */
function fluxoDeCaixaFechar( $idPop, $mes='', $ano='', $recursivo=true ){

	$data = dataSistema();
	
	if( !$mes || ( ( $mes < 0 ) || $mes > 12 ) ){
		$mes = $data['mes'];
	}
	
	if( !$ano || ( ( $ano < 1970 ) || $ano > 2100 ) ){
		$ano = $data['ano'];
	}
	
	$dataAnterior = dataSubtraiMeses( $ano, $mes );
	$fluxoMesAnterior = fluxoDeCaixaBuscarFechamento( $idPop, $dataAnterior['mes'], $dataAnterior['ano'] );
	
	if( !$fluxoMesAnterior && $recursivo ){
		$fluxoMesAnterior->valor = fluxoDeCaixaFechar( $idPop, $dataAnterior['mes'], $dataAnterior['ano'] );
	}
	
	$valor = fluxoDeCaixaCalcularFechamento( $idPop, $dataAnterior['mes'], $dataAnterior['ano'], $fluxoMesAnterior->valor );
	
	$novoFechamento = array();
	$novoFechamento['tipo']			= fluxoDeCaixaGetTipoFechamento();
	$novoFechamento['descricao']	= 'Fechamento de caixa relativo à '.$dataAnterior['mes'].'/'.$dataAnterior['ano'];
	$novoFechamento['data']			= $ano."-".$mes."-01 00:00:00";
	$novoFechamento['valor']		= $valor;
	$novoFechamento['idPop']		= $idPop;
	dbFluxoDeCaixa( $novoFechamento, 'inserir' );
	
	return $valor;
}

/**
 * Atualiza os valores de fechamento de uma terminada $idPop sendo que é atualizado a partir do 
 * período especificado por $data em diante. 
 *
 * @param integer $idPop
 * @param string  $data
 */
function fluxoDeCaixaAtualizarFechamento( $idPop, $data ){
	$dataFechamento = dataSomaMes( $data, 1 );
	$mesFechamento = substr( $dataFechamento, 5, 2 );
	$anoFechamento = substr( $dataFechamento, 0, 4 );
	
	if( fluxoDeCaixaBuscarFechamento( $idPop, $mesFechamento, $anoFechamento ) ){
		
		$condicao = array();
		$condicao[] = " idPop = '".$idPop."'";
		$condicao[] = " tipo = '". fluxoDeCaixaGetTipoFechamento() ."' ";
		$condicao[] = " data >= '".substr($data, 0, 8)."01 00:00:00'";
		
		$cons = dbFluxoDeCaixa( '', 'consultar', '', $condicao );
		
		$valor = $cons[0]->valor;
		// é um memo
		for( $i = 1; $i < count($cons); $i++ ){
			$novaData = dataSubtraiMeses(substr( $cons[$i]->data, 0, 4 ), substr( $cons[$i]->data, 5, 2 ));

			$valor = fluxoDeCaixaCalcularFechamento( $idPop, $novaData['mes'], $novaData['ano'], $valor );
			$cons[$i]->valor = $valor;
			$cons[$i]->ipPop = $idPop;
			
			dbFluxoDeCaixa( $cons[$i], 'alterar' );
		}
				
	}
}

/**
 * Calcula o fechamento referente a entrada/saida do $mes e $ano especificado referente
 * a um determinado $idPop. É utilizado um valor $retroativo para complemento do calculo.
 *
 * @param integer $idPop
 * @param integer $mes
 * @param integer $ano
 * @param double  $retroativo
 * @return double
 */
function fluxoDeCaixaCalcularFechamento( $idPop, $mes, $ano, $retroativo ){
	$condicao =  array();
	
	$condicao[] = "idPop='".$idPop."'";
	$condicao[] = "data BETWEEN '".$ano."-".$mes."-01 00:00:00' AND '".$ano."-".$mes."-31 23:59:59'";
	$condicao[] = "tipo in ('".fluxoDeCaixaGetTipoCredito()."', '".fluxoDeCaixaGetTipoDebito()."')";
	
	$resultado = dbFluxoDeCaixa('', 'somarValores', '', $condicao);
	
	if( $resultado[0]->tipo == fluxoDeCaixaGetTipoDebito() ){
		$debito		= $resultado[0]->total;
		$credito 	= 0;
	}
	else{
		$credito	= $resultado[0]->total;
		$debito		= $resultado[1]->total;
	}
	
	return ( $credito - $debito +($retroativo) );
}

/**
 * Verifica se existe fechamento de fluxo de caixa referente ao $mes e $ano de um 
 * determinado $pop. Caso encontre o fechamento,, é retornado um objeto com os dados 
 * deste fechamento. Do contrário, retornará false.
 *
 * @param integer $pop
 * @param integer $mes
 * @param integer $ano
 * @return object
 */
function fluxoDeCaixaBuscarFechamento( $pop, $mes = '', $ano='' ){
	$data = dataSistema();
	
	if( !$mes || ( ( $mes < 0 ) || $mes > 12 ) ){
		$mes = $data['mes'];
	}
	
	if( !$ano || ( ( $ano < 1970 ) || $ano > 2100 ) ){
		$ano = $data['ano'];
	}
	
	$condicao = array();
	
	$condicao[] = "idPop='".$pop."'";
	$condicao[] = "data='".$ano."-".$mes."-01 00:00:00'";
	$condicao[] = "tipo='" . fluxoDeCaixaGetTipoFechamento() . "'";
	$result 	= dbFluxoDeCaixa('', 'consultar', '', $condicao);
	return ( count($result) == 1 ? $result[0] : false );
	
}

/**
 * Retorna a sigla utilizada para indicar que é Credito.
 *
 * @return string
 */
function fluxoDeCaixaGetTipoCredito(){
	return "C";
}

/**
 * Retorna a sigla utilizada para indicar que é Debito.
 *
 * @return string
 */
function fluxoDeCaixaGetTipoDebito(){
	return "D";
}

/**
 * Retorna a sigla utilizada para indicar que é Fechamento de fluxo de caixa.
 *
 * @return string
 */
function fluxoDeCaixaGetTipoFechamento(){
	return "F";
}



# Seleciona o periodo
/**
 * Seleciona o periodo com base nos dados de $matriz. Retorna um array de objetos 
 * com as entradas no fluxo de caixa. 
 *
 * @param array $matriz
 * @return array
 */
function fluxoDeCaixaBuscaPeriodo( $matriz ){
	$data = dataSistema();
	$condicao = array();
	
	if(!empty($matriz['pop']))
		$condicao[] = " idPop = '".$matriz['pop'] ."' ";
	
	$mes = ( $matriz['mes'] ? $matriz['mes'] : date('m'));
	$ano = ( $matriz['ano'] ? $matriz['ano'] : date('Y'));
	
	$condicao[] = " data between '". $ano."-".$mes."-01 00:00:00' and '".$ano."-".$mes."-31 23:59:59'";
	
	$fluxo = dbFluxoDeCaixa( '', 'consultar', '', $condicao );
	
	/*
	
	reservado para colocar o atributo que ira conter os valores do resultado parcial.
	
	*/
	
	return( $fluxo );
}

/**
 * Prepara a listagem do fluxo de caixa, lançando os fechamentos anteriores, caso não 
 * existam.
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function fluxoDeCaixaPreparaListagem( $modulo, $sub, $acao, $registro, $matriz ){
	
	# Se não possui fechamento de caixa para o POP correspondente
	if( !fluxoDeCaixaBuscarFechamento( $matriz['pop'] ) ) {
		
		$condicao[] = "idPop='".$matriz['pop']."'";
		$condicao[] = "tipo='" . fluxoDeCaixaGetTipoFechamento() . "'";
		# Se ja existe fluxo de caixa para meses anteriores 
		if( count( dbFluxoDeCaixa( '', 'consultar', '', $condicao ) ) ) {
			#@mysql_query("FECHAMENTOS ENCONTRADOS. CHAMANDO FUNCAO RECURSIVA");
			fluxoDeCaixaFechar( $matriz['pop'] );
		}
		else {
			//senao fecha com a data da primeira conta lancada.
			$condicao = array( "idPop='".$matriz['pop']."'" );
			$contas = dbFluxoDeCaixa( '', 'consultar', '', $condicao);
			$mes = substr($contas[0]->data, 5, 2);
			$ano = substr($contas[0]->data, 0, 4);
			
			//@mysql_query("NAO ENCONTRADO NENHUM FECHAMENTO, FECHAR MES:".$mes);
			fluxoDeCaixaFechar( $matriz['pop'], $mes, $ano, false );
		}
	}
}

/**
 * Exibe a listagem do Fluxo de Caixa.
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function fluxoDeCaixaListagem(  $modulo, $sub, $acao, $registro, $matriz  ){
	global $corFundo, $corBorda, $sessLogin, $html, $tb, $conn;

	# Configuração da listagem
	$cabecalho		= array( "Doc",		"Descrição",	"Data",		"Crédito",	"Debito", "Saldo");
	$alinhamento	= array( "center",	"left",			"center",	"right",	"right", "right");
	$largura		= array( "9%",		"49%",			"9%",		"11%",		"11%",		'11%'	);
	
		
	# Abre a tabela do relatorio
	novaTabela( "[FLUXO DE CAIXA]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );

	//form ... um dia desses... em um outro lugar...
	abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
	htmlAbreLinha($corFundo);
	htmlAbreColuna("100%","center", $corFundo, count($cabecalho), 'tabfundo1');
	
	novaTabelaSH("center", "100%", 0 , 1, 0,$corFundo, $corBorda,  2 );
	
	htmlAbreLinha( $corFundo );
		$pop = formSelectPOP($matriz[pop], 'pop', 'form');
		$pop .=  getCampoForm('botao', '', 'filtroPOP', 'Ir');
		itemLinhaNOURL($pop, 'center', $corFundo, 0, 'tabfundo1');
		
		$mesAtual = ( $matriz['mes'] ? $matriz['mes'] : date("m") );
		$periodo .= "Mês: " . getComboMeses( "matriz[mes]", $mesAtual, ' onchange="javascript:document.forms[0].submit();"' );
		
		$anos 	  = array();
		$consulta = consultaSQL( "SELECT MIN(YEAR(data)) AS menor FROM ".$tb['FluxoDeCaixa'], $conn );
		$anoAtual = date("Y");
		$anoInicial = ( contaConsulta( $consulta ) ? resultadoSQL( $consulta, 0, 'menor' ) : $anoAtual );
		
		for($z = $anoInicial; $z <= $anoAtual; $z++ ){
				$anos[] = $z;
		}
		
		$periodo.= " Ano: " . getComboArray( "matriz[ano]", $anos, "", ( $matriz['ano'] ? $matriz['ano'] : $anoAtual ), "", ' onchange="javascript:document.forms[0].submit();"' );
		itemLinhaNOURL($periodo, 'right', $corFundo, 0, 'tabfundo1');
	htmlFechaLinha();
	fechaTabela();
	htmlFechaColuna();
	htmlFechaLinha();
	
	fechaFormulario();
	//fim do form
	

	# Exibe a linha de Cabeçalho
	htmlAbreLinha( $corFundo );
	for( $i = 0; $i < count( $cabecalho ); $i++ ) {
		itemLinhaNOURL( $cabecalho[$i], "center", $corFundo, 0, 'tabfundo0' );
	}
	htmlFechaLinha();
	
	# Inicia o total de debito e credito
	$ttCredito	= 0;
	$ttDebito	= 0;
	$saldo		= 0;
	
	#Exibe a listagem dos dados
	$relatorio = fluxoDeCaixaBuscaPeriodo( $matriz );
	
	if(is_array( $relatorio) ) {
		foreach( $relatorio as $linha ) {
		
			if( $linha->tipo == fluxoDeCaixaGetTipoFechamento() ){
				$saldo = $linha->valor;
				$credito = '';
				$debito  = '';
			}

			#Verifica se é debito ou credito, e calcula o total de cada um
			if( $linha->tipo == fluxoDeCaixaGetTipoCredito() ) {
				$ttCredito += $linha->valor;
				$saldo += $linha->valor;
				$credito	= number_format( $linha->valor, 2, ",", "." );
				$debito		= "0,00";
				$doc	 	= "<a href=\"#\" onclick=\"window.open('?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=".$linha->idConta.
							  "', 'ver', 'width=600,height=400,scrollbars=yes')\"><img src=\"".$html['imagem']['lancamento'].
							  "\" border=\"0\" title=\"Ver Documento\"></a>";

			}
			if( $linha->tipo == fluxoDeCaixaGetTipoDebito() ) {
				$ttDebito += $linha->valor;
				$saldo 	-= $linha->valor;
				$credito	= "0,00";
				$debito		= number_format( $linha->valor, 2, ",", "." );
				$doc	 	= "<a href=\"#\" onclick=\"window.open('?modulo=contas_a_pagar&sub=&acao=info&registro=".$linha->idConta.
							  "', 'ver', 'width=600,height=400,scrollbars=yes')\"><img src=\"".$html['imagem']['desconto'].
							  "\" border=\"0\" title=\"Ver Documento\"></a>";
			}
			
			$c = 0;
			
			$saldoFormatado = '<span style="color:'.( ( $saldo > 0) ? "#006000" : "#AA0000" ).'">' . number_format( $saldo	,  2, ",", "." ) . "</span>";
			
			htmlAbreLinha( $corFundo );
			itemLinhaNOURL( $doc,	$alinhamento[$c++], $corBorda, 0, 'normal10' );
			itemLinhaNOURL( $linha->descricao,	$alinhamento[$c++], $corBorda, 0, 'normal10' );
			itemLinhaNOURL( converteData($linha->data, "banco", "formdata" ),		$alinhamento[$c++], $corBorda, 0, 'normal10' );
			itemLinhaNOURL( $credito,			$alinhamento[$c++], $corBorda, 0, 'normal10' );
			itemLinhaNOURL( $debito,			$alinhamento[$c++], $corBorda, 0, 'normal10' );
			
			itemLinhaNOURL( $saldoFormatado, $alinhamento[$c++], $corBorda, 0, 'normal10' );
			htmlFechaLinha();

		}
		# Exibe o total
		$c = 3;
		$saldoFormatado = '<span style="color:'.( ( $saldo > 0) ? "#006000" : "#AA0000" ).';font-weight:bold;">' . number_format( $saldo	,  2, ",", "." ) . "</span>";
		htmlAbreLinha( $corFundo );
		itemLinhaNOURL( "Total",									"right", $corBorda, 3, 'bold10' );
		itemLinhaNOURL( number_format( $ttCredito, 2, ",", "." ),	$alinhamento[$c++], $corBorda, 0, 'bold10' );
		itemLinhaNOURL( number_format( $ttDebito,  2, ",", "." ),	$alinhamento[$c++], $corBorda, 0, 'bold10' );
		itemLinhaNOURL( $saldoFormatado,							$alinhamento[$c++], $corBorda, 0, 'bold10' );
		htmlFechaLinha();
		
	}
	fechaTabela();
		
}

?>