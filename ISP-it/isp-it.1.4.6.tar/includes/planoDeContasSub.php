<?

/**
 * Construtor do sub-modulo Sub-Plano de Contas 
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasSub($modulo, $sub, $acao, $registro, $matriz ){
 
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
		//echo "<br>";
		switch ($acao) {
			case "adicionar":
				planoDeContasSubAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				planoDeContasSubAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
}


/**
 * Função de Acesso ao Banco de Dados na tabela PlanoDeContasSub
 *
 * @param array   $matriz
 * @param unknown $tipo
 * @param unknown $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return unknown
 */
function dbPlanoDeContasSub($matriz, $tipo, $subTipo='', $condicao='', $ordem = '') {
	global $conn, $tb;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	$tabelas = $tb['PlanoDeContasSub'];
	$campos  = array( 'id', 'idPlanoDeContas', 'nome', 'descricao', 'status' );
	
	if ($tipo == 'inserir'){
		$valores = array( "NULL", $matriz['idPlanoDeContas'], $matriz['nome'], $matriz['descricao'], $matriz['status'] );
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if ($tipo == 'alterar'){
		$valores = array( $matriz['idPlanoDeContas'], $matriz['nome'], $matriz['descricao'], $matriz['status']);
		array_shift($campos); //retira o campo id da lista de campos
		$retorno = $bd->alterar($tabelas, $campos, $valores, $condicao);
	}
	
	if ($tipo == 'consultar'){
		$retorno = $bd->seleciona($tabelas, $campos, $condicao, '', $ordem);
	}
	
	return ($retorno);
}

/**
 * Exibe o formulario para cadastro/alteração
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasSubFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	novaTabela2("[".( $acao == "adicionar" ? "Adicionar" : "Alterar")." Sub-Plano de Contas]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro ) ;

		getCampo('', '', '', '&nbsp;');
		
		getCampo('combo', "Plano de Contas Pai", "", getSelectObjetos( "matriz[idPlanoDeContas]", dbPlanoDeContas( "", "consultar" ), "nome", "id", $matriz['idPlanoDeContas'] ) ) ;
		
		getCampo('text', 'Nome do Sub-Plano', 'matriz[nome]', $matriz['nome'],'','',20);
	
		getCampo('area', 'Descrição', 'matriz[descricao]', $matriz['descricao'],'','',40);
		
		getCampo('combo', "Status", "", formSelectStatusAtivoInativo($matriz['status'], "status", "form") ) ;
		
		getBotao('matriz[bntConfirmar]', 'Confirmar');
		
		fechaFormulario();
	fechaTabela();
	
}



/**
 * Metodo para adição do sub-plano de contas
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro (id do plano de contas pai selecionado)
 * @param array   $matriz
 */
function planoDeContasSubAdicionar($modulo, $sub, $acao, $registro, $matriz){
	
	if ( $matriz["bntConfirmar"] && planoDeContasSubValidarFormulario( $matriz ) ){
		$gravar = dbPlanoDeContasSub($matriz, 'inserir');
		if( $gravar ) {
			avisoNOURL("Aviso", "Sub-Plano de Contas criado com sucesso!", 400);
			echo "<br>";
			planoDeContasListar($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
			echo "<br>";
			planoDeContasSubFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		if (! $matriz["idPlanoDeContas"] ){
			$matriz["idPlanoDeContas"] =$registro;
		}
		planoDeContasSubFormulario( $modulo, $sub, $acao, "", $matriz );
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
function planoDeContasSubAlterar($modulo, $sub, $acao, $registro, $matriz){
	
	if ( $matriz["bntConfirmar"] && planoDeContasSubValidarFormulario( $matriz ) ){
		$gravar = dbPlanoDeContasSub($matriz, 'alterar', "", "id='".$registro."'");
		if( $gravar ) {
			avisoNOURL("Aviso", "Sub-Plano de Contas alterado com sucesso!", 400);
			echo "<br>";
			planoDeContasListar($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar dados!", 400);
			echo "<br>";
			planoDeContasSubFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		$conta = dbPlanoDeContasSub("", "consultar","", "id='".$registro."'");
		if ( count( $conta ) ){
			$matriz["idPlanoDeContas"]= $conta[0]->idPlanoDeContas;
			$matriz["nome"]= $conta[0]->nome;
			$matriz["descricao"]= $conta[0]->descricao;
			$matriz["status"]= $conta[0]->status;
			planoDeContasSubFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Sub-Plano de Contas!", 400);
			echo "<br>";
			planoDeContasListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}



/**
 * validação do formulario de sub-plano de contas
 *
 * @return boolean
 */
function planoDeContasSubValidarFormulario( $matriz ){
	$erro = array();
	
	if(empty($matriz["nome"])){
		$erro[] = "Nome do Sub-Plano não informado";
	}
	
//	if( empty( $matriz["descricao"] ) ){
//		$erro[] = "Descrição do Sub-Plano não informado";
//	}
	
	
	if ( count($erro) > 0 ){
		avisoNOURL( "Aviso", implode( "<br>", $erro ), 400 );
		$ret = false;
		echo "<BR>";
	}
	else{
		$ret = true;
	}
	
	return $ret;
}


?>