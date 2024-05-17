<?php
/**
 * classe para centralizar e gerenciar os impostos controlados pelo sistema
*+-----------+--------------+------+-----+---------+----------------+
*| id        | int(11)      |      | PRI | NULL    | auto_increment |
*| tipo      | varchar(100) |      |     |         |                |
*| descricao | varchar(255) | YES  |     | NULL    |                |
*+-----------+--------------+------+-----+---------+----------------+
*/

function tiposImpostos( $modulo, $sub, $acao, $registro, $matriz ){
 
	global $corFundo, $corBorda, $sessLogin;
	
	// sistema de permissao diferente. por funcao ao inves de modulo.
	$titulo = "<b>Tipos de Impostos</b>";
	$subtitulo = " Cadastro de tipos de impostos" ;
	$itens=Array('Adicionar', 'Procurar', 'Listar');
	getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
	
	echo "<br>";
	switch ($acao) {
		case "adicionar":
		case "alterar":
		case "excluir":
		case "ver":
			if ($matriz['bntConfirmar']){
				dbTiposImpostos($matriz, $acao, '', $registro);
				tiposImpostosListar($modulo, $sub, $acao, $registro, $matriz);
			}
			else 
				tiposImpostosExibir($modulo, $sub, $acao, $registro, $matriz);
			break;
			
		default:
			tiposImpostosListar($modulo, $sub, $acao, $registro, $matriz);
			break;
	}
}



function dbTiposImpostos($matriz, $tipo, $condicao='', $registro = '') {
	global $conn, $tb, $modulo, $sub, $acao;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	
	$tabelas = $tb[TiposImpostos];
	
	if ($tipo == 'adicionar'){
		$campos = Array('id',	'tipo', 			'descricao');
		$valores= Array('',		$matriz['tipo'],	$matriz['descricao']);
		
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
	
	if($tipo == 'listar'){
		$campos = array('tipo', 'descricao', 'id');
		
		$retorno = $bd->seleciona($tabelas, $campos, $condicao);
	}
	
	if($tipo == 'ver'){
		if (!$condicao)
			$condicao = "id = $matriz";	
		$retorno = $bd->seleciona($tabelas, '*', $condicao);
	}
		
	if ($tipo == 'alterar' && $registro){
		$condicao = 'id='.$registro;
		$campos= Array('tipo', 			'descricao'			);
		$valores= Array($matriz[tipo],	$matriz[descricao]);
		
		$retorno = $bd->alterar($tabelas, $campos, $valores, $condicao);
	}
	
	if ($tipo == 'excluir' && $registro){
		$condicao = "id='$registro'";
		$retorno = $bd->excluir($tabelas,$condicao);
	}
	
	return ($retorno);	
}

function tiposImpostosExibir ($modulo, $sub, $acao, $registro, $matriz) {

	if ($registro)
		$tiposImpostos = dbTiposImpostos($registro, 'ver');
		
	if($acao == 'adicionar' || $acao == 'alterar' ){
		$campos= 'text';
		$tabela[formulario]=true;
	}
	if($acao == 'adicionar' || $acao == 'alterar' || $acao == 'excluir' )
		$tabela[exibe][bntConfirmar]=1;
	
	$tabela[titulo] = "Tipos de Impostos";
	$tabela[exibe][titulo]=1;
	
	$tabela[gravata]    =   Array('Tipo de Imposto',			'Descrição');
	$tabela[valores] 	= 	Array($tiposImpostos[0]->tipo,		$tiposImpostos[0]->descricao);
	$tabela[formatos] 	= 	Array($campos,						$campos);
	$tabela[campos]		=	Array('tipo',						'descricao');
	
	exibeFormulario($tabela, $tipo, $modulo, $sub, $acao, $registro);
	
}


function tiposImpostosListar ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
		
	$data=dataSistema();
		
	$tabela[exibe][titulo]=1;
	$tabela[exibe][subMenu]=0;
	$tabela[exibe][filtros] = 0;
	$tabela[exibe][total]=0;
		
	$tabela[titulo] = "Tipos de Impostos";
		
	$tabela[gravata]    =	Array("Tipo", 	"Descrição",	"Opções");
	$tabela[formatos] 	= 	Array('',		'',				'opcoes');
	$tabela[tamanho]    =	Array('20%',	'55%',			'25%');
	$tabela[alinhamento]=   Array('center',	'left',			'center');

	$tabela[detalhe] = dbTiposImpostos('', 'listar');
		
	exibeNovaTabela($tabela, $modulo, $sub, $acao, $registro, $matriz);
	
}

function tiposImpostosSelectTipo ($valor, $nome, $tipo='form') {
	$cons = dbTiposImpostos('', 'listar');

	if ($tipo == 'form'){
		$ret = "<select name=matriz[$nome]>\n";
		for ($i = 0; $i<count($cons); $i++){
			$ck = ($cons[$i]->tipo == $valor ? "selected": "");
			$ret .= "<option value=".$cons[$i]->id . '  '. $ck ."> ".$cons[$i]->tipo."</option>\n";
		}
		$ret .= "</select>";	
	}
	
	return ($ret);
}

function getIdTipoImposto($tipo){
	$condicao = "tipo = '".$tipo."'";

	$cons = dbTiposImpostos('',	'ver', $condicao);
	
	return ($cons[0]->id);
}
?>