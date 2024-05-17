<?

/**
 * Contrutor do sub-modulo Previsão de Centro de Custo
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function centroDeCustoPrevisao( $modulo, $sub, $acao, $registro, $matriz ){
	
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
		switch ($acao) {
			case "adicionar":
				centroDeCustoPrevisaoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
				break;
			
			case "listar_previsao":
				centroDeCustoPrevisaoListar( $modulo, $sub, $acao, $registro, $matriz );
				break;	
			default:
				break;
		}
	}
}

/**
 * Gerencia a tabela Previsão do Centro de Custo
 *
 * @param array $matriz
 * @param unknown $tipo
 * @param unknown $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return unknown
 */
function dbCentroDeCustoPrevisao($matriz, $tipo, $subTipo='', $condicao='', $ordem = '') {
	global $conn, $tb;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	$tabelas = $tb['CentroDeCustoPrevisao'];
	$campos  = array( 'id', 'idCentroDeCusto', 'mes', 'ano', 'valor' );
	
	if ($tipo == 'inserir'){
		$valores = array( "NULL", $matriz['idCentroDeCusto'], $matriz['mes'], $matriz['ano'], $matriz['valor'] );
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if ($tipo == 'alterar'){
		$valores = array( $matriz['idCentroDeCusto'], $matriz['mes'], $matriz['ano'], $matriz['valor']);
		array_shift($campos); //retira o campo id da lista de campos
		$retorno = $bd->alterar($tabelas, $campos, $valores, $condicao);
	}
	
	if ($tipo == 'consultar'){
		$retorno = $bd->seleciona($tabelas, $campos, $condicao, '', $ordem);
	}
	
	if ( $tipo == 'excluir' ){
		$retorno = $bd->excluir( $tabelas, $condicao )	;
	}
	
	return ($retorno);
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
function centroDeCustoPrevisaoFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	//echo "<br>";
	novaTabela2("[Lan&ccedil;ar Previs&atilde;o de Centros de Custo]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro ) ;
		
		
		$largura = array('16%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%');
		$gravata['cabecalho'] = array('Centro de Custo', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 
									  'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
		$gravata['alinhamento'] = array('left', 'right', 'right', 'right', 'right', 'right', 'right', 
										'right', 'right', 'right', 'right', 'right', 'right');
		$anos 	  = array();
		$anoAtual = date("Y");
		for($z = $anoAtual; $z < $anoAtual + 10; $z++ ){
				$anos[] = $z;
		}

		htmlAbreLinha( $corFundo, 'tabfundo0');
			itemLinhaNOURL( "Ano: " . getComboArray( "matriz[ano]", $anos, "", $matriz['ano'], "", ' onchange="javascript:document.forms[0].submit();"' ),'center', $corFundo, 0,'tabfundo1' );
		htmlFechaLinha();
		
		htmlAbreLinha($corFundo);
		htmlAbreColuna('100%', 'center', $corFundo, 0, 'normal10');
		
		echo "<div class=\"layer\">\n";
		novaTabela2SH("center", '960', 0, 2, 1, $corFundo, $corBorda, 13);	

										
		htmlAbreLinha($corFundo);
			for($i=0;$i<count($largura); $i++){
				itemLinhaTMNOURL($gravata['cabecalho'][$i], 'center', 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
			}
		htmlFechaLinha();
	
		$condicao[] = "status = 'A'";
		if ( $registro ) $condicao[] = "id = " . $registro;
		$centros = dbCentroDeCusto("", "consultar", "",$condicao, "nome");
		
		if( count($centros) ){
			foreach ( $centros as $centro ){
					
				$i = 0;
	
				htmlAbreLinha( $corFundo );
					itemLinhaTMNOURL( '<b class="bold9">'.$centro->nome.'</b>' , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
					for( $j = 1; $j <= 12; $j++ ){
						if($j < date("m") && $matriz['ano'] == date("Y") ) {
							$disabled 	= "disabled";
							$classe		= 'numeroboxsmalldisabled';
						}
						else {
							$disabled 	= "";
							$classe		= "numeroboxsmall";
						}
						itemLinhaTMNOURL( getInput('text', 'matriz[CC]['.$centro->id.']['.$j.']', 
												   number_format($matriz['CC'][$centro->id][$j],2,',',''), 
										  ' onblur="formataValor(this.value, this.name);setPrevisao(this.value, '.$centro->id.', '.$j.')"', 7, $classe, $disabled ), 
										  $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
					}
				htmlFechaLinha();
				
			}
		}		

		fechaTabela();
		echo "</div>\n";
		
		htmlFechaColuna();
		htmlFechaLinha();
		
		htmlAbreLinha($corFundo);
		itemLinhaNOURL( getInput( 'submit', 'matriz[bntConfirmar]', 'Confirmar', "", 20, 'submit' ), 
						"center", $corFundo, 13, 'tabfundo1' );
		htmlFechaLinha();
						
		fechaFormulario();
	fechaTabela();
	
}

/**
 * Lança previsão de Centro de Custo
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function centroDeCustoPrevisaoAdicionar( $modulo, $sub, $acao, $registro, $matriz ){
	
	if ( $matriz["bntConfirmar"] ){ //&& centroDeCustoValidarFormulario( $matriz ) ){
		
		#Eliminando o último item de $matriz que é o Botão Confirmar
		
		foreach ( $matriz['CC'] as $idCentroDeCusto => $valor ){
				
			$excluir = dbCentroDeCustoPrevisao(array(), 'excluir','',array("idCentroDeCusto = ".$idCentroDeCusto, "ano = ".$matriz['ano']) );
			
			for( $mes = 1; $mes <= 12; $mes++ ){
				$vetor  = array( 'idCentroDeCusto' => $idCentroDeCusto, 
								 'mes' => $mes, 
								 'ano' => $matriz['ano'], 
								 'valor' => str_replace( ",",".", $valor[$mes] ) );
				if ( $valor[$mes] && ( intval( $valor[$mes] ) != 0 ) ) {
					$gravar = dbCentroDeCustoPrevisao($vetor, 'inserir');
				}
			}
		}
		
		if( $gravar ) {
			avisoNOURL("Aviso", "Previsões Centro de Custo criado com sucesso!", 400);
			echo "<br>";
			centroDeCustoListar($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
			echo "<br>";
			centroDeCustoPrevisaoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		# Prepara os dados para enviar ao Formulario
		$matriz['ano'] = ( $matriz['ano'] ? $matriz['ano'] : date("Y") );
		if( $matriz['ano'] ) {
			
			$condicoes[] = "ano='".$matriz['ano']."'";
			if ( $registro ) $condicoes[] = "idCentroDeCusto = " . intval($registro);
			
			$resultados = dbCentroDeCustoPrevisao('', 'consultar', "", $condicoes,  array('idCentroDeCusto', 'mes') );
			#trata os resultados das previsões do Centro de Custo para passar de parametro na função
			if( is_array( $matriz['CC'] ) ) $matriz['CC'] = ""; //array();
			foreach ( $resultados as $linha ){
				$matriz['CC'][$linha->idCentroDeCusto][$linha->mes] = $linha->valor;
			}
		}
		#Chama formulario
		centroDeCustoPrevisaoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
	
}


/**
 * Exibe a previsão do centro de custo
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function centroDeCustoPrevisaoListar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb;
	
	$matriz['ano'] = ( $matriz['ano'] ? $matriz['ano'] : date("Y") );
	if( $matriz['ano'] ) {
		$resultados = dbCentroDeCustoPrevisao('', 'consultar', "", array("ano='".$matriz['ano']."'"),  array('idCentroDeCusto', 'mes') );
		#trata os resultados das previsões do Centro de Custo para passar de parametro na função
		if( is_array( $matriz['CC'] ) ) $matriz['CC'] = ""; //array();
		foreach ( $resultados as $linha ){
			$matriz['CC'][$linha->idCentroDeCusto][$linha->mes] = $linha->valor;
		}
	}
	
	//echo "<br>";
	novaTabela2("[Previsão de Centros de Custo]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro ) ;
		$largura = array('16%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%', '7%');
		$gravata['cabecalho'] = array('Centro de Custo', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 
									  'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
		$gravata['alinhamento'] = array('left', 'right', 'right', 'right', 'right', 'right', 'right', 
										'right', 'right', 'right', 'right', 'right', 'right');
		$anos 	  = array();
		
		$consulta = consultaSQL( "SELECT MIN(ano) AS menor FROM ".$tb['CentroDeCustoPrevisao'], $conn );
		
		$anoAtual = date("Y");
		$anoInicial = ( contaConsulta( $consulta ) ? resultadoSQL( $consulta, 0, 'menor' ) : $anoAtual );
		
		for($z = $anoInicial; $z < $anoAtual + 10; $z++ ){
				$anos[] = $z;
		}

		htmlAbreLinha( $corFundo, 'tabfundo0');
			itemLinhaNOURL( "Ano: " . getComboArray( "matriz[ano]", $anos, "", $matriz['ano'], "", ' onchange="javascript:document.forms[0].submit();"' ),'center', $corFundo, '13','tabfundo1' );
		htmlFechaLinha();

		htmlAbreLinha($corFundo);
		htmlAbreColuna('100%', 'center', $corFundo, 0, 'normal10');
		
		echo "<div class=\"layer\">\n";
		novaTabela2SH("center", '960', 0, 2, 1, $corFundo, $corBorda, 13);	
		
		htmlAbreLinha($corFundo);
			for($i=0;$i<count($largura); $i++){
				itemLinhaTMNOURL($gravata['cabecalho'][$i], 'center', 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
			}
		htmlFechaLinha();
	
		$centros = dbCentroDeCusto("", "consultar", "", "", "nome");
		
		if( count($centros) ){
			
			foreach ( $centros as $centro ){
					
				$i = 0;
				$corLinha = ( $corLinha == 'normal10' ? 'tabfundo1' : 'normal10' );
				htmlAbreLinha( $corFundo, $corLinha );
					itemLinhaTMNOURL( '<b class="bold9">'.$centro->nome."</b>" , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal9' );
					for( $j = 1; $j <= 12; $j++ ){
						$disabled = ( $j < date("m") ? "disabled" : "");
						itemLinhaTMNOURL( number_format($matriz['CC'][$centro->id][$j],2,',',''), 
										  $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal9' );
					}
				htmlFechaLinha();
				
			}
			
		}		
		
		fechaTabela();
		echo "</div>\n";
		
		htmlFechaColuna();
		htmlFechaLinha();
		
		
		fechaFormulario();
	fechaTabela();	
		
}

?>