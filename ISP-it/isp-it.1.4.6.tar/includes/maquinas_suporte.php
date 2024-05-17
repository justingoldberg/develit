<?php
################################################################################
#       Criado por: Felipe dos Santos Assis - felipeassis@devel-it.com.br
#  Data de cria��o: 06/11/2007
# Ultima altera��o: 06/11/2007
#    Altera��o No.: 000
#
# Fun��o:
#    Painel - Fun��es para gerenciar os registros de relacionamento entre as
#	 tabelas M�quinas do Ticket-IT e Suporte do ISP-IT
#

function dbMaquinasSuporte($campos, $matriz, $tipo){
	global $conn, $tb;
	
	if($tipo == 'inserir'){
		#obtendo novo id para o suporte
		$resultado =  buscaSuporte('', '', 'max', '');
		$novoIdSuporte = mysql_fetch_row($resultado);
		// array para cadastrar quando foi selecionado mais de uma m�quina
		$idMaquinas = $_REQUEST['idMaquinas'];
		for($i = 0; $i < count($idMaquinas); $i ++){
			$sql[$i] = "INSERT INTO $tb[MaquinasSuporte] (idSuporte, idMaquina) " .
					"VALUES($novoIdSuporte[0], $idMaquinas[$i])";
		}
	}
	elseif($tipo == 'buscarMaquinaSuporte'){
		# Query de consulta das m�quinas de acordo com o relacionamento
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
		#substituindo m�quinas
		// array para cadastrar quando foi selecionado mais de uma m�quina
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
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
	
}
?>