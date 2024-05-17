<?

/**
 * Construtor do sub-modulo Detalhes do Plano de Contas 
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasDetalhes( $modulo, $sub, $acao, $registro, $matriz ){
 
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
		switch ( $acao ) {
			case "adicionar":
				planoDeContasDetalhesAdicionar( $modulo, $sub, $acao, $registro, $matriz );
				break;
			case "alterar":
				planoDeContasDetalhesAlterar( $modulo, $sub, $acao, $registro, $matriz );
				break;
		}
	}
}


/**
 * Função de Acesso ao Banco de Dados na tabela PlanoDeContasDetalhes
 *
 * @param array   $matriz
 * @param unknown $tipo
 * @param unknown $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return unknown
 */
function dbPlanoDeContasDetalhes( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	$tabelas = $tb['PlanoDeContasDetalhes'];
	$campos  = array( 'id', 'idPlanoDeContasSub', 'idCentroDeCusto', 'nome', 'descricao', 'status' );
	
	if ( $tipo == 'inserir' ){
		$valores = array( "NULL", $matriz['idPlanoDeContasSub'], $matriz['idCentroDeCusto'], $matriz['nome'], $matriz['descricao'], $matriz['status'] );
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if ( $tipo == 'alterar' ){
		$valores = array( $matriz['idPlanoDeContasSub'], $matriz['idCentroDeCusto'], $matriz['nome'], $matriz['descricao'], $matriz['status'] );
		array_shift($campos); //retira o campo id da lista de campos
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}
	
	if ( $tipo == 'consultar' ){
		if( $subTipo == 'tresNiveis' ) {			  
			$tabelas =	"{$tb['PlanoDeContasDetalhes']}\n".
						"INNER JOIN {$tb['PlanoDeContasSub']} ON ({$tb['PlanoDeContasDetalhes']}.idPlanoDeContasSub = {$tb['PlanoDeContasSub']}.id)\n".
						"INNER JOIN {$tb['PlanoDeContas']} ON ({$tb['PlanoDeContasSub']}.idPlanoDeContas = {$tb['PlanoDeContas']}.id)\n";
			$campos = array( $tb['PlanoDeContas'].'.nome as pai', $tb['PlanoDeContasSub'].'.nome as sub', $tb['PlanoDeContasDetalhes'].'.nome as detalhe' );
		}
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	
	return ( $retorno );
}

/**
 * Exibe o fromulario para cadastro/alteracao
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasDetalhesFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	novaTabela2("[".( $acao == "adicionar" ? "Adicionar" : "Alterar")." Plano de Contas Detalhes]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro ) ;
	
		getCampo('', '', '', '&nbsp;');
		
		getCampo('combo', "Sub-Plano de Contas", "", getSelectObjetos( "matriz[idPlanoDeContasSub]", dbPlanoDeContasSub( "", "consultar" ), "nome", "id", ( $matriz['idPlanoDeContasSub'] ? $matriz['idPlanoDeContasSub'] : $registro) ) ) ;

		$combo = getSelectObjetos("matriz[idCentroDeCusto]", dbCentroDeCusto("", "consultar", "", "status='A'"), "nome", "id", $matriz['idCentroDeCusto'] );
		getCampo('combo', 'Centro de Custo', '', $combo);
		
		getCampo('text', 'Nome do Detalhe do Plano de Contas', 'matriz[nome]', $matriz['nome'],'','',20);
	
		getCampo('area', 'Descrição', 'matriz[descricao]', $matriz['descricao'],'','',40);
		
		getCampo('combo', "Status", "", formSelectStatusAtivoInativo($matriz['status'], "status", "form") );
		
		getBotao('matriz[bntConfirmar]', 'Confirmar');
		
		fechaFormulario();
	fechaTabela();
	
}



/**
 * Metodo para adicao de detalhes do sub-plano de contas
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro (id do plano de contas pai selecionado)
 * @param array   $matriz
 */
function planoDeContasDetalhesAdicionar($modulo, $sub, $acao, $registro, $matriz){
	
	if ( $matriz["bntConfirmar"] && planoDeContasDetalhesValidarFormulario( $matriz ) ){
		$gravar = dbPlanoDeContasDetalhes($matriz, 'inserir');
		if( $gravar ) {
			avisoNOURL("Aviso", " Detalhe do Plano de Contas criado com sucesso!", 400);
			echo "<br>";
			planoDeContasListar($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
			echo "<br>";
			planoDeContasDetalhesFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		$matriz["idPlanoDeContasSub"] = $registro;
		planoDeContasDetalhesFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}



/**
 * Metodo para alterar o detalhes do sub-plano de contas
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function planoDeContasDetalhesAlterar($modulo, $sub, $acao, $registro, $matriz){
	
	if ( $matriz["bntConfirmar"] && planoDeContasDetalhesValidarFormulario( $matriz ) ){
		$gravar = dbPlanoDeContasDetalhes($matriz, 'alterar', "", "id='".$registro."'");
		if( $gravar ) {
			avisoNOURL("Aviso", "Detalhes do Plano de Contas alterado com sucesso!", 400);
			echo "<br>";
			planoDeContasListar($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar dados!", 400);
			echo "<br>";
			planoDeContasDetalhesFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		$conta = dbPlanoDeContasDetalhes("", "consultar","", "id='".$registro."'");
		if ( count( $conta ) ){
			$matriz["idPlanoDeContasSub"] = $conta[0]->idPlanoDeContasSub;
			$matriz["idCentroDeCusto"] = $conta[0]->idCentroDeCusto;
			$matriz["nome"] 	 = $conta[0]->nome;
			$matriz["descricao"] = $conta[0]->descricao;
			$matriz["status"] 	 = $conta[0]->status;
			planoDeContasDetalhesFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar os Detalhes Plano de Contas!", 400);
			echo "<br>";
			planoDeContasListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}

}


/**
 * validacao do formulario de plano de contas
 *
 * @return boolean
 */

function planoDeContasDetalhesValidarFormulario( $matriz ){

	$erro = array();
	
	if(empty($matriz["nome"])){
		$erro[] = "Nome do Detalhe de Plano de Conta não informado";
	}
	
//	if(empty($matriz["descricao"])){
//		$erro[] = "Descrição do Detalhe do Plano de Conta não informado";
//	}
	
	
	if ( count($erro) > 0 ){
		avisoNOURL( "Aviso", implode( "<br>", $erro), 400 );
		$ret = false;
		echo "<BR>";
	}
	else{
		$ret = true;
	}
	
	return $ret;

}

?>