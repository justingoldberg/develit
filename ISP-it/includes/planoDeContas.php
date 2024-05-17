<?

/**
 * Construtor do modulo Plano de Contas
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContas($modulo, $sub, $acao, $registro, $matriz ){
 
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
		$titulo = "<b>Planos de Contas</b>";
		$itens=Array('Adicionar', 'Listar');
		getHomeModulo($modulo, "", $titulo, $subtitulo, $itens);
		echo "<br>";
		
		if( $sub == "plano_contas_sub" ) {
			planoDeContasSub( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $sub == "plano_contas_detalhe" ) {
			planoDeContasDetalhes( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{//plano de contas pai (padrao)
			switch ($acao) {
				case "adicionar":
					planoDeContasAdicionar($modulo, $sub, $acao, $registro, $matriz);
					break;
				case "alterar":
					planoDeContasAlterar($modulo, $sub, $acao, $registro, $matriz);
					break;
				default:
					planoDeContasListar($modulo, $sub, $acao, $registro, $matriz);
					break;
			}
		}
	}
}

/**
 * Gerencia a tabela Plano de Contas
 *
 * @param array   $matriz
 * @param unknown $tipo
 * @param unknown $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return unknown
 */
function dbPlanoDeContas($matriz, $tipo, $subTipo='', $condicao='', $ordem = '') {
	global $conn, $tb;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	$tabelas = $tb['PlanoDeContas'];
	$campos  = array( 'id', 'nome', 'descricao', 'status' );
	
	if ($tipo == 'inserir'){
		$valores = array( "NULL", $matriz['nome'], $matriz['descricao'], $matriz['status'] );
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if ($tipo == 'alterar'){
		$valores = array( $matriz['nome'], $matriz['descricao'], $matriz['status']);
		array_shift($campos); //retira o campo id da lista de campos
		$retorno = $bd->alterar($tabelas, $campos, $valores, $condicao);
	}
	
	if ($tipo == 'consultar'){
		$retorno = $bd->seleciona($tabelas, $campos, $condicao, '', $ordem);
	}
	
	return ($retorno);
}

/**
 * Lista os Plano de Contas, os Sub-Planos de Contas e os Detalhes do Plano de contas seguindo 
 * sua hierarquia
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasListar($modulo, $sub, $acao, $registro, $matriz){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;
	
	$largura 				= array('2%',		'2%', 		'2%',		'23%',  '36%',       '7%',    '28%');
	$gravata['cabecalho']	= array('',			"",			"",			'Nome',	'Descrição', 'Status', 'Opções');
	$gravata['alinhamento']	= array('right',	'right',	'right',	'left', 'left',      'center', 'left');
		
	novaTabela( "[Listagem de Plano de Contas]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
	
	htmlAbreLinha($corFundo);
		$i = 3;
		itemLinhaTMNOURL($gravata['cabecalho'][$i], "center", 'middle', $largura[$i++], $corFundo, 4, 'tabfundo0');
		itemLinhaTMNOURL($gravata['cabecalho'][$i], "center", 'middle', $largura[$i++], $corFundo, 0, 'tabfundo0');
		itemLinhaTMNOURL($gravata['cabecalho'][$i], "center", 'middle', $largura[$i++], $corFundo, 0, 'tabfundo0');
		itemLinhaTMNOURL($gravata['cabecalho'][$i], "center", 'middle', $largura[$i],	$corFundo, 0, 'tabfundo0');
//		for($i=0;$i<count($largura); $i++){
//			itemLinhaTMNOURL($gravata['cabecalho'][$i], $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
//		}
	htmlFechaLinha();
	
	$planos = dbPlanoDeContas("", "consultar", "", "", "nome");
	
	if ( count ( $planos) ){
		# Mostra o Plano de Contas
		foreach ( $planos as $plano ){
			$default = "<a class=\"link9\" href=?modulo=$modulo&registro=".$plano->id;
			
			$opcoes  = htmlMontaOpcao($default."&acao=alterar>Alterar</a>",'alterar');
//			$opcoes .= ( $planos->status == "A"	? htmlMontaOpcao($default."&acao=ativar>Ativar</a>",'ativar')
//												: htmlMontaOpcao($default."&acao=inativar>Inativar</a>",'desativar') );
			$opcoes .= htmlMontaOpcao($default."&sub=plano_contas_sub&acao=adicionar>Adicionar Sub-conta</a>",'incluir');
			$i = 2;
			$tree = '<span id="mostra' . $plano->id . '" onclick="showTree(\'subplano' . $plano->id . '\')">*</span>';
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $tree , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal9' );
				itemLinhaTMNOURL( $plano->nome , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 3, 'normal9' );
				itemLinhaTMNOURL( $plano->descricao , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal9' );
				itemLinhaTMNOURL( formSelectStatusAtivoInativo($plano->status, "status", "check" ) , $gravata['alinhamento'][$i], 
								  'middle', $largura[$i++], $corFundo, 0, 'normal9' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal9' );
			htmlFechaLinha();
			
			$subPlanos = dbPlanoDeContasSub( "", "consultar", "", "idPlanoDeContas=".$plano->id );
			
			# Mostra o Sub-Plano de Contas
			foreach( $subPlanos as $subPlano ){
				$def = "<a class=\"link9\" href=?modulo=$modulo&registro=".$subPlano->id;
				$opcoes  = htmlMontaOpcao( $def."&sub=plano_contas_sub&acao=alterar>Alterar</a>",'alterar' );
				$opcoes .= htmlMontaOpcao( $def."&sub=plano_contas_detalhe&acao=adicionar>Adicionar Detalhe</a>",'incluir' );
			
				$i = 2;
				htmlAbreLinha( "", "tabfundo81" );
					itemLinhaTMNOURL( "+" , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 2, 'normal9' );
					itemLinhaTMNOURL( $subPlano->nome , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 2, 'normal9' );
					itemLinhaTMNOURL( $subPlano->descricao , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal9' );
					itemLinhaTMNOURL( formSelectStatusAtivoInativo($subPlano->status, "status", "check" ) , $gravata['alinhamento'][$i], 
									  'middle', $largura[$i++], $corFundo, 0, 'normal9' );
					itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal9' );
				htmlFechaLinha();
				
				$detalhesPlanos = dbPlanoDeContasDetalhes( "", "consultar", "", "idPlanoDeContasSub = ".$subPlano->id );
				
				# mostra dos detalhes das sub-contas do plano.
				foreach ( $detalhesPlanos as $detalhe ){
					$def = "<a class=\"link9\" href=\"?modulo=$modulo&registro=".$detalhe->id;
					$opcoes = htmlMontaOpcao( $def."&sub=plano_contas_detalhe&acao=alterar\">Alterar</a>",'alterar' );

					$i = 2;
					
					htmlAbreLinha( "", "tabfundo82" );
						itemLinhaTMNOURL( "-" , $gravata["alinhamento"][$i], "middle", $largura[$i++], $corFundo, 3, 'normal9' );
						itemLinhaTMNOURL( $detalhe->nome, $gravata["alinhamento"][$i], "middle", $largura[$i++], $corFundo, 0, 'normal9' );
						itemLinhaTMNOURL( $detalhe->descricao, $gravata["alinhamento"][$i], "middle", $largura[$i++], $corFundo, 0, 'normal9' );
						itemLinhaTMNOURL( formSelectStatusAtivoInativo($detalhe->status, "status", "check") , $gravata['alinhamento'][$i], 
									  	  'middle', $largura[$i++], $corFundo, 0, 'normal9');
						itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal9');
					htmlFechaLinha();			 
				}
			
			}
		}
	}
	
	fechaTabela();
	
}

/**
 * Exibe o formulario para cadastro/alteracao
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	//echo "<br>";
	novaTabela2("[".( ( $acao == "adicionar" ) ? "Adicionar" : "Alterar")." Plano de Contas]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		
		getCampo('', '', '', '&nbsp;');
		
		getCampo('text', 'Nome do Plano', 'matriz[nome]', $matriz['nome'], '', '', 20);
	
		getCampo('area', 'Descrição', 'matriz[descricao]', $matriz['descricao'],'','',40);
		
		getCampo('combo', "Status", "", formSelectStatusAtivoInativo($matriz['status'], "status", "form") ) ;
		
		getBotao('matriz[bntConfirmar]', 'Confirmar');
		
		fechaFormulario();
			
	fechaTabela();
	
	
}

/**
 * Método para adição do plano de contas
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasAdicionar($modulo, $sub, $acao, $registro, $matriz){
	
	if ( $matriz["bntConfirmar"] && planoDeContasValidarFormulario( $matriz ) ){
		$gravar = dbPlanoDeContas($matriz, 'inserir');
		if( $gravar ) {
			avisoNOURL("Aviso", "Plano de Contas criado com sucesso!", 400);
			echo "<br>";
			planoDeContasListar($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
			echo "<br>";
			planoDeContasFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		planoDeContasFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Metodo para alterar o plano de contas
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasAlterar($modulo, $sub, $acao, $registro, $matriz){
	
	if ( $matriz["bntConfirmar"] && planoDeContasValidarFormulario( $matriz ) ){
		$gravar = dbPlanoDeContas($matriz, 'alterar', "", "id='".$registro."'");
		if( $gravar ) {
			avisoNOURL("Aviso", "Plano de Contas alterado com sucesso!", 400);
			echo "<br>";
			planoDeContasListar($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar dados!", 400);
			echo "<br>";
			planoDeContasFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		$conta = dbPlanoDeContas("", "consultar","", "id='".$registro."'");
		if ( count( $conta ) ){
			$matriz["nome"]		 = $conta[0]->nome;
			$matriz["descricao"] = $conta[0]->descricao;
			$matriz["status"]    = $conta[0]->status;
			planoDeContasFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Plano de Contas!", 400);
			echo "<br>";
			planoDeContasListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}

/**
 * validação do formulário de plano de contas
 *
 * @return boolean
 */

function planoDeContasValidarFormulario($matriz){
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


function getPlanoDeContas( $nome='planoPai', $valor=0, $tipo='form', $size=1, $ativos=0){
	global $tb;
	if ( $valor && $tipo == 'check' )
		$condicao[] = $tb['PlanoDeContasSub'].".id = $valor";
		
	if ( $ativos )
		$condicao[] = $tb['PlanoDeContasSub'].".status = 'A'";

	$planos = dbPlanoDeContas( $matriz, 'consultar', $nome, $condicao );
	
	if ( $tipo == 'form' ){
		$retorno = "<select size=\"$size\" name=matriz[$nome]><option value=\"0\"> Selecione</option>\n";
		if( count($planos) > 0 )
			foreach( $planos as $linha ){
				$opc = ( ( $linha->id == $valor ) ? 'selected' : '' );
				$retorno.="<option value=\"".$linha->id."\" $opc>".$linha->nome."</option>\n";
			}
		$retorno .= "</select>";
	}
	elseif($tipo='check')	
		$retorno = $planos[0]->nome;
		
	return($retorno);	
}

?>