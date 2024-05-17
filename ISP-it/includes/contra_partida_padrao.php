<?php

function contraPartidaPadrao($modulo, $sub, $acao, $registro, $matriz){
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessPlanos, $tb;

	$titulo = "Padrões de Contra Partida";
	$subtitulo = " Quando adicionado um serviço para um cliente que tenha contra partidas, o sistema buscará" .
			     " os dados abaixo como configuração 'padrão', e irá exibi-las logo após a adição do serviço," .
			     " que poderão ser alteradas neste momento ou em Contra Partidas de cada Serviço";
	$itens = Array("Adicionar", "Listar");
	
	getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens );
	
	$matriz[tabela] = $tb[ContraPartidaPadrao];
	
	#nas passagens posteriores, verificará o índice para saber de onde veio
	$matriz[cpp] = 'contraPartidaPadrao';
	
	if (!$matriz[idContraPartida] )		$matriz[idContraPartida] = $_REQUEST[registro2];

	if (! $matriz[idServicosPlanos] ) $matriz[idServicosPlanos] = $registro;
	
	if($acao == 'adicionar'){
		contraPartidaPadraoAdicionar($modulo, $sub, $acao, $registro , $matriz);
	}
	elseif($acao == 'contrapartidapadraover'){
		$matriz[idContraPartida]=$_REQUEST[registro2];
		contraPartidaver($modulo, $sub, $acao, $registro , $matriz);
	}
	elseif($acao == 'contrapartidadesativar' || $acao == 'contrapartidaativar'){
		contraPartidaAlterarStatus($modulo, $sub, $acao, $registro , $matriz);
	}
	elseif($acao == 'contrapartidapadraoexcluir'){
		contraPartidaPadraoExcluir($modulo, $sub, $acao, $registro , $matriz);
	}
	elseif($acao == 'contrapartidapadraoalterar'){
		contraPartidaAlterar($modulo, $sub, $acao, $registro , $matriz);
	}
	else{	
		contraPartidaPadraoListar($modulo, $sub, $acao, $registro, $matriz);
	}
	
}

function dbContraPartidaPadrao($matriz, $tipo, $subTipo=''){
	global $conn, $tb, $modulo, $sub, $acao;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	
	$tabelas = $tb[ContraPartidaPadrao];
	$campos = '*';
	
	if ($tipo == 'consultar'){
		if ($subTipo == 'servico')
			$condicao =  $tabelas.".idServicosPlanos = ". $matriz;
		
		$retorno = $bd->seleciona($tabelas, $campos, $condicao);	
	}
	
	if ($tipo == 'excluir'){
			$condicao =  $tabelas .".id = " . $matriz[idContraPartida];
			$retorno = $bd->excluir($tabelas, $condicao);
	}
	return ($retorno);
}

function contraPartidaPadraoListar ($modulo, $sub, $acao, $registro, $matriz) {
	contraPartidaListar($modulo, $sub, $acao, 0, $matriz,'contraPartidaPadrao');
}

function contraPartidaPadraoAdicionar ($modulo, $sub, $acao, $registro, $matriz) {
	
	contraPartidaAdicionar($modulo, $sub, $acao, $registro, $matriz);
}

function contraPartidaPadraoExcluir ($modulo, $sub, $acao, $registro, $matriz) {
	
	if ( !$matriz[bntConfirmar] )
		contraPartidaVer($modulo, $sub, $acao, $registro, $matriz);
	
	elseif($matriz[idContraPartida]){
		
		$grava = dbContraPartidaPadrao($matriz, 'excluir');
		
		echo '<br>';				
		if($grava)	avisoNOURL("Aviso:", "Registro Alterado com Sucesso",'');
		else		avisoNOURL("Erro:", "Erro ao Alterar Registro",'');
		
		echo '<br>';
		contraPartidaListar($modulo, $sub, $acao, $registro, $matriz);
	
	
	}
}

function contraPartidaPadraoBuscaValores($idServico) {
	
	if ($idServico)
	$cons = dbContraPartidaPadrao($idServico, 'consultar', 'servico');

	return ($cons);
}