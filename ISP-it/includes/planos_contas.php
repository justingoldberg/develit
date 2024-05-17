<?php

 
function planoDeContas($modulo, $sub, $acao, $registro, $matriz ){
 
	global $corFundo, $corBorda, $sessLogin;
	
	// sistema de permissao diferente. por funcao ao inves de modulo.
	$titulo = "<b>Tipos de Contas</b>";
	$itens=Array('Adicionar', 'Procurar', 'Listar');
	getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
	
	echo "<br>";
	switch ($acao) {
		case "adicionar":
			planoDeContasAdicionar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case "desativar":
			planoDeContasDesativar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case "ativar":
			planoDeContasAtivar($modulo, $sub, $acao, $registro, $matriz);
			break;
		default:
			planoDeContasListar($modulo, $sub, $acao, $registro, $matriz);
			break;
	}
}


function dbPlanoDeContas ($matriz, $tipo, $subTipo='', $condicao=''  ) {
	global $conn, $tb, $modulo, $sub, $acao;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	
	if ($tipo == 'inserir'){
		if($subTipo == 'planoFilho'){
			$tabelas = $tb[PlanoDeContasSub];
			$campos = Array('idPai', 'nome', 'descricao', 'status');
			$valores = Array($matriz[planoPai], $matriz[nome], $matriz[descricao], $matriz[status]);
		}
		elseif($subTipo == 'planoPai'){
			$tabelas = $tb[PlanoDeContas];
			$campos = Array('nome', 'descricao', 'status');
			$valores = Array($matriz[nome], $matriz[descricao], $matriz[status]);
		}
		
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if ($tipo == 'alterar'){
		$tabelas = $tb[PlanoDeContasSub];
		$condicao = 'id = '.$matriz;
		
		if($subTipo == 'desativar'){
			$campos	 = 'status';
			$valores = 'I';
		}
		elseif($subTipo == 'ativar'){
			$campos	 = 'status';
			$valores = 'A';
		}
		
		$retorno = $bd->alterar($tabelas, $campos, $valores, $condicao);
	}
	
	if ($tipo == 'consultar'){
		$ordem = "$tb[PlanoDeContas].nome";
		if ($subTipo == 'unico'){
			$campos = Array(" $tb[PlanoDeContas].nome",
							" $tb[PlanoDeContasSub].nome nomeSub",
							" $tb[PlanoDeContas].descricao", 
							" $tb[PlanoDeContasSub].descricao descricaoSub",
							" $tb[PlanoDeContas].status",
							" $tb[PlanoDeContasSub].status statusSub",
							" $tb[PlanoDeContas].id",
							" $tb[PlanoDeContasSub].id idSub");
			$tabelas = "$tb[PlanoDeContas] LEFT JOIN $tb[PlanoDeContasSub] 	On ($tb[PlanoDeContas].id = $tb[PlanoDeContasSub].idPai)";				
			$ordem = Array ("$tb[PlanoDeContas].nome", "$tb[PlanoDeContasSub].nome");
		}
		elseif ($subTipo == 'tipoConta'){
			$tabelas = "$tb[PlanoDeContas] INNER JOIN $tb[PlanoDeContasSub] 	On ($tb[PlanoDeContas].id = $tb[PlanoDeContasSub].idPai)";
			$campos = Array ("$tb[PlanoDeContasSub].id", "CONCAT_WS(' - ', $tb[PlanoDeContas].nome , $tb[PlanoDeContasSub].nome) AS nome");
		}
		else{
			$tabelas = "$tb[PlanoDeContas]";
			$campos = Array("id", "nome");
		}	
		
		$retorno = $bd->seleciona($tabelas, $campos, $condicao, '', $ordem);
	}
	
	return ($retorno);
}


function formPlanoDeContas ($modulo, $sub, $acao, $registro, $matriz ) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	echo "<br>";
	novaTabela2("[Tipos de Contas]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	novaLinhaTabela($corFundo, '100%');
	$texto="			
		<form method=post name=matriz action=index.php>
		<input type=hidden name=modulo value=$modulo>
		<input type=hidden name=sub value=$sub>
		<input type=hidden name=acao value=$acao>
		";
		
		itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
	fechaLinhaTabela();
		
	getCampo('text', 'Nome do Plano', 'matriz[nome]', $matriz[nome],'','',20);
	
	$texto= "Conta Pai:<br> <span class=normal8> Caso esta conta seja uma subConta, por favor selecione a conta Pai</span>";
	getCampo('combo', $texto, '', getPlanoDeContasPai(), '');
	
	getCampo('text','Descrição', 'matriz[descricao] ', $matriz[descricao], '','', 50);
	
	$texto = 'Status';
	getCampo('combo', $texto, 'status', formSelectStatusPOP($matriz[status], 'status', 'form')) ; //status do Planos;
	
	
	getBotao('matriz[bntConfirmar]', 'Confirmar');
		
}

function planoDeContasAdicionar ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn;
	
	if(!$matriz[bntConfirmar])
		formPlanoDeContas($modulo, $sub, $acao, $registro, $matriz );
	else{
				
		if ($matriz[nome] && $matriz[descricao]){
			if ($matriz[planoPai])
				$grava = dbPlanoDeContas($matriz, 'inserir', 'planoFilho');
			else
				$grava = dbPlanoDeContas($matriz, 'inserir', 'planoPai');
	
						if ($grava){
				$msg="Registro Gravado Com Sucesso";
				avisoNOURL("Aviso:", $msg, 300);
			}
			else {
				$msg="Ocorreu um erro ao incluir um registro";
				avisoNOURL("ERRO:", $msg, 300);
			}
			echo "<br>";
			planoDeContasListar($modulo, $sub, $acao, $registro, $matriz );	
		}
		else{
			$msg="Favor preencher os campos corretamente";
			avisoNOURL("ERRO:", $msg, 300);
		}	
		
	}
}

function planoDeContasListar ($modulo, $sub, $acao, $registro, $matriz ) {
	global $tb;
	
	if($matriz[planoPai])
		$condicao = "$tb[PlanoDeContasSub].idPai=".$matriz[planoPai];
	
	$cons = dbPlanoDeContas('','consultar','unico', $condicao);
	
	if ($cons){
		$tabela[exibe][titulo]=1;
		
		$tabela[exibe][filtros][pop] = -1;
		$tabela[exibe][filtros][tipoData] = -1;
		$tabela[exibe][filtros][datas] = -1 ;
		$tabela[exibe][filtros][periodo] = -1;
		$tabela[exibe][filtros][planos]=1;
		
		$tabela[exibe][subMenu]=1;
		$tabela[exibe][total]=0;
		
		$tabela[titulo] = "Tipos De Contas";
		
		$tabela[gravata]    =	Array('Nome',	'Descrição',	'Status',			 	'Opções');
		$tabela[tamanho]    =	Array('25%',	'45%',			'10%',					'10%');
		$tabela[formatos] 	= 	Array('',		'',				'status',	'opcoes');
		$tabela[alinhamento]=   Array('left',	'left',		  	'center',				'center');
		
		$anterior = ''; $l = 0;
		foreach ($cons as $linha){
			if ($anterior != $linha->nome){
				$tabela[detalhe][$l][nome] = "+" . $linha->nome;
				$tabela[detalhe][$l][descricacao] = $linha->descricao;
				$tabela[detalhe][$l][status] = '';
				$tabela[detalhe][$l++][id] = '';
				$tab = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if ($linha->nomeSub){
				$tabela[detalhe][$l][nome] = $tab . $linha->nomeSub;
				$tabela[detalhe][$l][descricacao] = $linha->descricaoSub;
				$tabela[detalhe][$l][status] = $linha->statusSub;
				$tabela[detalhe][$l++][id] = $linha->idSub;
			}
			$anterior = $linha->nome;
		}		
		
		exibeNovaTabela($tabela, $modulo, $sub, $acao, $registro, $matriz);			
	}
	
}

function planoDeContasDesativar($modulo, $sub, $acao, $registro, $matriz ) {
	if($registro)
		$grava = dbPlanoDeContas($registro, 'alterar', 'desativar');
		
	planoDeContasListar($modulo, $sub, $acao, $registro, $matriz );
}

function planoDeContasAtivar($modulo, $sub, $acao, $registro, $matriz ) {
	if($registro)
		$grava = dbPlanoDeContas($registro, 'alterar', 'ativar');
		
	planoDeContasListar($modulo, $sub, $acao, $registro, $matriz );
}

function getPlanoDeContasPai($nome='planoPai', $valor=0, $tipo='form', $size=1, $ativos=0){
	global $tb;
	if ($valor && $tipo=='check')
		$condicao[] = "$tb[PlanoDeContasSub].id = $valor";
		
	if ($ativos)
		$condicao[] = "$tb[PlanoDeContasSub].status = 'A'";

	$planos = dbPlanoDeContas($matriz, 'consultar', $nome, $condicao);
	
	if ($tipo == 'form'){
		$retorno="<select size=$size name=matriz[$nome]><option value=0> Selecione</option>";
		if(count($planos) > 0 )
			foreach($planos as $linha){
				($linha->id == $valor ? $opc= 'selected' : $opc = '');
				$retorno.="<option value=".$linha->id." $opc>".$linha->nome."</option>\n";
			}
		$retorno .= "</select>";
	}
	elseif($tipo='check')	
		$retorno = $planos[0]->nome;
		
	return($retorno);	
}

?>