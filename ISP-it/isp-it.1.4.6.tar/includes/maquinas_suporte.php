<?php
################################################################################
#       Criado por: Felipe dos Santos Assis - felipeassis@devel-it.com.br
#  Data de criaчуo: 06/11/2007
# Ultima alteraчуo: 06/11/2007
#    Alteraчуo No.: 000
#
# Funчуo:
#    Painel - Funчѕes para gerenciar os registros de relacionamento entre as
#	 tabelas Mсquinas do Ticket-IT e Suporte do ISP-IT
#

function dbMaquinasSuporte($campos, $matriz, $tipo){
	global $conn, $tb;
	
	if($tipo == 'inserir'){
		#obtendo novo id para o suporte
		$resultado =  buscaSuporte('', '', 'max', '');
		$novoIdSuporte = mysql_fetch_row($resultado);
		// array para cadastrar quando foi selecionado mais de uma mсquina
		$idMaquinas = $_REQUEST['idMaquinas'];
		for($i = 0; $i < count($idMaquinas); $i ++){
			$sql[$i] = "INSERT INTO $tb[MaquinasSuporte] (idSuporte, idMaquina) " .
					"VALUES($novoIdSuporte[0], $idMaquinas[$i])";
		}
	}
	elseif($tipo == 'buscarMaquinaSuporte'){
		# Query de consulta das mсquinas de acordo com o relacionamento
		$sql = "SELECT $tb[Maquinas].nome FROM $tb[Maquinas] " .
				"INNER JOIN $tb[MaquinasSuporte] " .
				"ON ($tb[Maquinas].id = $tb[MaquinasSuporte].idMaquina) " .
				"LEFT JOIN Suporte " .
				"ON ($tb[MaquinasSuporte].idSuporte = $tb[Suporte].id) " .
				"WHERE $tb[Suporte].id = $campos";
	}
	elseif($tipo == 'excluir'){
		$sql = "DELETE FROM $tb[MaquinasSuporte] " .
				"WHERE $tb[MaquinasSuporte].idSuporte = $campos";
	}
	elseif($tipo == 'alterar'){
		#substituindo mсquinas
		// array para cadastrar quando foi selecionado mais de uma mсquina
			$sql = "INSERT INTO $tb[MaquinasSuporte] (idSuporte, idMaquina) " .
					"VALUES($matriz[idSuporte], $campos)";
	}
	if(is_array($sql)){
			for($i = 0; $i < count($sql); $i ++){
				$retorno = consultaSQL($sql[$i], $conn);
			}
		}
		else{
			$retorno =consultaSQL($sql, $conn);
		}
	return $retorno;
}

function buscaMaquinasSuporte($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[MaquinasSuporte] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[MaquinasSuporte] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Maquinas] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT $campos from $tb[Maquinas] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		//executando consulta no banco
		$retorno = consultaSQL($sql, $conn);
		return $retorno;
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta nуo pode ser realizada por falta de parтmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrъncia de erro", $msg, $url, 760);
	}
	
}
?>