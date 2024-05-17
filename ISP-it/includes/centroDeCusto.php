<?

/**
 * Contrutor do modulo Centro de Custo
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function centroDeCusto($modulo, $sub, $acao, $registro, $matriz ){
 
	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin['login'],'login','igual','login');
	
	if(!$permissao['admin']) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		// sistema de permissao diferente. por funcao ao inves de modulo.
		$titulo = "<b>Centros de Custo</b>";
		$itens  = Array( 'Adicionar', 'Listar' );
		getHomeModulo( $modulo, "", $titulo, $subtitulo, $itens );
		echo "<br>";
		
		if ( $sub == "previsao" ){
			centroDeCustoPrevisao( $modulo, $sub, $acao, $registro, $matriz );		
		}	
		else {
			switch ($acao) {
				case "adicionar":
					centroDeCustoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
					break;
				case "alterar":
					centroDeCustoAlterar( $modulo, $sub, $acao, $registro, $matriz );
					break;
				default:
					centroDeCustoListar( $modulo, $sub, $acao, $registro, $matriz );
					break;
			}
		}
	}
}

/**
 * Gerencia a tabela CentroDeCusto
 *
 * @param array   $matriz
 * @param unknown $tipo
 * @param unknown $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return unknown
 */
function dbCentroDeCusto($matriz, $tipo, $subTipo='', $condicao='', $ordem = '') {
	global $conn, $tb;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['CentroDeCusto'];
	$campos  = array( 'id', 'nome', 'descricao', 'status' );
	
	if ( $tipo == 'inserir' ){
		$valores = array( "NULL", $matriz['nome'], $matriz['descricao'], $matriz['status'] );
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if ( $tipo == 'alterar' ){
		$valores = array( $matriz['nome'], $matriz['descricao'], $matriz['status'] );
		array_shift( $campos ); //retira o campo id da lista de campos
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}
	
	if ( $tipo == 'consultar' ){
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	
	return ($retorno);
}


/**
 * Listagem de Centro de Custo
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function centroDeCustoListar( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	$largura 				= array('25%', '39%', '7%', '29%');
	$gravata['cabecalho']   = array('Nome', 'Descrição', 'Status', 'Opções');
	$gravata['alinhamento'] = array('left', 'left', 'center', 'left');
	
	novaTabela("[Listagem de Centros de Custo]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
	
	menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz,4);
	
	htmlAbreLinha($corFundo);
		for($i=0;$i<count($largura); $i++){
			itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
		}
	htmlFechaLinha();
	
	$centros = dbCentroDeCusto("", "consultar", "", "", "nome");
	
	if( count($centros) ){
		foreach ( $centros as $centro ){
			
			$default = "<a href=?modulo=$modulo&registro=".$centro->id;
			
			$opcoes = htmlMontaOpcao($default."&acao=alterar>Alterar</a>",'alterar');
			if ( $centro->status == 'A' )
				$opcoes.= htmlMontaOpcao($default."&sub=previsao&acao=adicionar>Lançar Previsão</a>",'incluir');

			$i = 0;

			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $centro->nome , $gravata[alinhamento][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $centro->descricao , $gravata[alinhamento][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formSelectStatusAtivoInativo($centro->status, "status", "check" ) , $gravata[alinhamento][$i], 
								  'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata[alinhamento][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			
		}
	}
	
	fechaTabela();
	
}

/**
 * Exibe o formulario para cadastro/alteracao
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function centroDeCustoFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	novaTabela2("[".( $acao == "adicionar" ? "Adicionar" : "Alterar")." Centro de Custo]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro ) ;
	
		getCampo('text', 'Nome do Centro de Custo', 'matriz[nome]', $matriz['nome'],'','',20);
	
		getCampo('area', 'Descrição', 'matriz[descricao]', $matriz['descricao'],'','',40);
		
		getCampo('combo', "Status", "", formSelectStatusAtivoInativo($matriz['status'], "status", "form") ) ;
		
		getBotao('matriz[bntConfirmar]', 'Confirmar');
		
		fechaFormulario();
	fechaTabela();
	
}

/**
 * Metodo para adicao do centro de custo
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function centroDeCustoAdicionar($modulo, $sub, $acao, $registro, $matriz){

	if ( $matriz["bntConfirmar"] && centroDeCustoValidarFormulario( $matriz ) ){
		$gravar = dbCentroDeCusto($matriz, 'inserir');
		if( $gravar ) {
			avisoNOURL("Aviso", "Centro de Custo criado com sucesso!", 400);
			echo "<br>";
			centroDeCustoListar($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
			echo "<br>";
			centroDeCustoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		centroDeCustoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
	
}


/**
 * Metodo para alterar o centro de custo
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function centroDeCustoAlterar($modulo, $sub, $acao, $registro, $matriz){
	
	if ( $matriz["bntConfirmar"] && centroDeCustoValidarFormulario( $matriz ) ){
		$gravar = dbCentroDeCusto( $matriz, 'alterar', "", "id='" . $registro . "'" );
		if( $gravar ) {
			avisoNOURL( "Aviso", "Centro de Custo alterado com sucesso!", 400 );
			echo "<br>";
			centroDeCustoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
		else {
			avisoNOURL( "Erro", "Não foi possível gravar dados!", 400 );
			echo "<br>";
			centroDeCustoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		$conta = dbCentroDeCusto( "", "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){
			$matriz["nome"]		 = $conta[0]->nome;
			$matriz["descricao"] = $conta[0]->descricao;
			$matriz["status"]	 = $conta[0]->status;
			centroDeCustoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Centro de Custo!", 400);
			echo "<br>";
			centroDeCustoListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}


/**
 * validacao do formulario de centro de custo
 *
 * @return boolean
 */
function centroDeCustoValidarFormulario( $matriz ){
	$erro = array();
	
	if(empty($matriz["nome"])){
		$erro[] = "Nome do centro de custo não informado";
	}
	
	if(empty($matriz["descricao"])){
		$erro[] = "Descrição do centro de custo não informado";
	}
	
	
	if ( count($erro) > 0 ){
		avisoNOURL( "Aviso", implode( "<br>", $erro), 400 );
		$ret = false;
	}
	else{
		$ret = true;
	}
	
	return $ret;
}

?>