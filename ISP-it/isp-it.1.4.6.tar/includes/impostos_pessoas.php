<?php


//+---------------+---------+------+-----+---------+----------------+
//| id            | int(11) |      | PRI | NULL    | auto_increment |
//| idTipoImposto | int(11) |      |     | 0       |                |
//| idPessoa      | int(11) |      |     | 0       |                |
//| valor         | double  |      |     | 0       |                |
//+---------------+---------+------+-----+---------+----------------+

function impostosPessoas( $modulo, $sub, $acao, $registro, $matriz ){
 
	global $corFundo, $corBorda, $sessLogin;
	
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if( !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		verPessoas($modulo, $sub, $acao, $registro, $matriz);	
	
		echo "<br>";
		switch ($acao) {
			case "impostosPessoasAdicionar":
			case "impostosPessoasAlterar":
			case "impostosPessoasExcluir":
			case "impostosPessoasVer":
				if ($matriz['bntConfirmar']){
					$ret = dbImpostosPessoas($matriz, $acao, '', $registro);
					avisoImpostasPessoas($ret, $acao);
					echo "<br>";
					impostosPessoasListar($modulo, $sub, $acao, $registro, $matriz);
				}
				else 
					ImpostosPessoasExibir($modulo, $sub, $acao, $registro, $matriz);
				break;
				
			default: 
				impostosPessoasListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
}



function dbImpostosPessoas($matriz, $tipo, $condicao='', $registro = '') {
	global $conn, $tb, $modulo, $sub, $acao;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	
	$tabelas = $tb[ImpostosPessoas];
	
	$reg = explode(':', $registro); #:'(
	
	if ($tipo == 'impostosPessoasAdicionar' && $matriz['valor'] ){ 
		$campos = Array('id',	'idTipoImposto', 			'idPessoa',				'valor');
		$valores= Array('',		$matriz['idTipoImposto'],	$reg[1],	formatarValores($matriz['valor']));
		
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
	
	if($tipo == 'listar' &&( $reg[1] || $condicao) ){
		$campos = array($tb['TiposImpostos'].'.tipo', $tb[ImpostosPessoas].'.valor', 'concat('."'$reg[0]:$reg[1]:',".$tb[ImpostosPessoas].'.id) id');
		$tabelas = array($tb['TiposImpostos'].' INNER JOIN '. $tb['ImpostosPessoas']. ' ON ('. $tb['TiposImpostos'].'.id = '. $tb['ImpostosPessoas'] .'.idTipoImposto )');
		
		if (!$condicao)
			$condicao = "idPessoa='".$reg[1]."'";

		$retorno = $bd->seleciona($tabelas, $campos, $condicao);
	}
	
	if($tipo == 'ver'){
		$condicao = $tb[ImpostosPessoas].".id = '$reg[2]'";
		
		$campos = array($tb['TiposImpostos'].'.tipo', $tb[ImpostosPessoas].'.valor', 'concat('."'$reg[0]:$reg[1]:',".$tb[ImpostosPessoas].'.id) id');
		$tabelas = array($tb['TiposImpostos'].' INNER JOIN '. $tb['ImpostosPessoas']. ' ON ('. $tb['TiposImpostos'].'.id = '. $tb['ImpostosPessoas'] .'.idTipoImposto )');
			
		$retorno = $bd->seleciona($tabelas, $campos, $condicao);
	}
		
	if ($tipo == 'impostosPessoasAlterar' && $reg[2]){
		$condicao = 'id='.$reg[2];
		$campos= Array('idTipoImposto',			'valor'	);
		$valores= Array($matriz[idTipoImposto],	formatarValores( $matriz[valor]) );
		
		$retorno = $bd->alterar($tabelas, $campos, $valores, $condicao);
	}
	
	if ($tipo == 'impostosPessoasExcluir' && $reg[2]){
		$condicao = "id='".$reg[2]."'";
		$retorno = $bd->excluir($tabelas,$condicao);
	}

	return ($retorno);	
}



function ImpostosPessoasExibir ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	if ($registro && $acao != 'impostosPessoasAdicionar')
		$impostosPessoas = dbImpostosPessoas('', 'ver', '', $registro);
	
	# Motrar tabela de busca
	novaTabela2("[Visualização de Impostos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=impostosPessoasAdicionar&registro=$registro>Adicionar</a>",'incluir');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 2, 'tabfundo1');
	
		#fim das opcoes adicionais
		$texto="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
		itemTabelaNOURL("$texto", 'left', $corFundo, 2, 'tabfundo1');
		
		if ($acao == 'impostosPessoasVer' || $acao == 'impostosPessoasExcluir'){
			$tipo='combo'; 
			$tiposImpostos = $impostosPessoas[0]->tipo;
		}else{
			$tiposImpostos = tiposImpostosSelectTipo($impostosPessoas[0]->tipo, 'idTipoImposto');
			$tipo='text';
		}

		getCampo('combo',	'Imposto',	'', 	 $tiposImpostos);
		getCampo($tipo,	'Porcentagem', 	'matriz[valor]', formatarValoresForm($impostosPessoas[0]->valor),  'onBlur=verificarValor(0,this.value);formataValor(this.value,6)','',8);
		
		if ($acao != 'impostosPessoasVer' || $acao != 'impostosPessoasExcluir')
			getBotao('matriz[bntConfirmar]', 'Confirmar');
			
	fechaTabela();			
}


function impostosPessoasListar ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
		
	$tabela[exibe][titulo]=1;
	$tabela[exibe][subMenu]=1;
	$tabela[exibe][filtros] = 0;
	$tabela[exibe][total]=0;
	$tabela[exibe][menuOpc] = htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=impostosPessoasAdicionar&registro=$registro>Adicionar</a>",'incluir');
	
	$tabela[titulo] = "Impostos";
		
	$tabela[gravata]    =	Array("Imposto",	"Porcentagem",	"Opções");
	$tabela[formatos] 	= 	Array('',			"moeda",		'opcoes');
	$tabela[tamanho]    =	Array('30%',		"30%",			'40%');
	$tabela[alinhamento]=   Array('left',		'center',		'center');

	$tabela[detalhe] = dbImpostosPessoas('', 'listar', '', $registro);
	
	
	exibeNovaTabela($tabela, $modulo, $sub, $acao, $registro, $matriz);
}

function avisoImpostasPessoas($ret, $acao){
	switch ($acao) {
		case "impostosPessoasAdicionar":
			$msg = "Inclusão ";
			break;
		case "impostosPessoasAlterar":
			$msg = "Alteração ";
			break;
		case "impostosPessoasExcluir":
			$msg = "Exclusão ";
			break;
	}
	
	if ($msg && $ret)
		avisoNOURL("Aviso", $msg . ' realizada com sucesso', 500);
	elseif( $msg && !$ret)
		avisoNOURL("Aviso", $msg . ' não realizada', 500);
}

function getImpostoPessoa($idTipoImposto, $idPessoaTipo){
	$dadosPessoa = dadosPessoasTipos($idPessoaTipo);
	
	$condicao[] = " idPessoa = '".$dadosPessoa['idPessoa']."'";
	$condicao[] = " idTipoImposto = '".$idTipoImposto."'";
	
	$cons = dbImpostosPessoas('', 'listar', $condicao);
	
	$valor = 0;
	foreach( $cons as $imposto){
		if ($imposto->valor > 0)
			$valor += $imposto->valor;	
	}

	return ($valor);
}
?>
