<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 14/10/2002
# Ultima altera��o: 14/10/2002
#    Altera��o No.: 001
#
# Fun��o:
#    Fun��es de conex�o com banco de dados


# Conecta ao MySQL
function conectaMySQL($host, $usuario, $senha)
{
	# Conectar a banco de dados MySQL
	return(mysql_connect($host, $usuario, $senha));
	
} // fecha fun��o conectaMysQL


# Seleciona banco de dados
function selecionaDB($banco, $conn)
{
	# Selecionar banco de dados
	return(mysql_selectdb($banco, $conn));
	
} // fecha fun��o selecionaDB


# Executa query
/**
 * @return array
 * @param string $sql
 * @param dblink $conn
 * @desc Fun��o de consulta SQL
 <b>sql</b> String de consulta SQL
 <b>conn</b> Link para conex�o com banco de dados
*/
function consultaSQL($sql, $conn)
{
	$retorno=mysql_query($sql, $conn);
	if(!$retorno) {
		# Erro - Consulta nao retorna valor
		aviso("Consulta","Erro ao realizar consulta em banco de Dados. <br>Verifique configura��es!", "?modulo=",300);
	}
	
	# retornar consulta
	return($retorno);
	
} #fecha fun��o para executar query no MySQL

# Executa query
function consultaSQLHide($sql, $conn)
{
	$retorno=mysql_query($sql, $conn);
	
	# retornar consulta
	return($retorno);
	
} #fecha fun��o para executar query no MySQL


# Fun��o para contar linhas retornadas pela matriz de consulta SQL

/**
 * @return unknown
 * @param unknown $consulta
 * @desc Fun��o que retorna o numero de registros retornados pela consulta
 */
function contaConsulta($consulta)
{
	# Contar linhas da consulta
	return(mysql_num_rows($consulta));
	
} # fecha fun��o para contagem da consultaSQL


# Mostrar resultado da consulta
/**
 * @return unknown
 * @param unknown $consulta
 * @param unknown $linha
 * @param unknown $coluna
 * @desc Devolve o resultado da consulta especificado pela linha e coluna definidos
*/
function resultadoSQL($consulta, $linha, $coluna){
	
	return(@mysql_result($consulta, $linha, $coluna));
	
} # fecha fun��o para retorno de valor da coluna

/**
 * @return ResultSet
 * @param varchar $texto
 * @param varchar $campo
 * @param varchar $tipo
 * @param varchar $ordem
 * @param varchar $tabela
 * @desc Retorna um resultset da pesquisa no mysql
 $texto  = dado a ser pesquisao
 $campo  = coluna a ser comparada
 $tipo   = todos, contem, igual, custom
 $ordem  = ordem do resultado
 $tabela = nome da tabela
*/
function buscaRegistros($texto, $campo, $tipo, $ordem, $tabela) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if ($tabela == 'MaodeObra')
		$exc= 'WHERE status <> "I"';
		
	if($tipo=='todos') {
		$sql="SELECT 
			$tabela.* 
		FROM 
			$tabela 
		ORDER BY $tabela.$ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT 
			$tabela.*
		FROM 
			$tabela
		WHERE 
			$tabela.$campo LIKE '%$texto%'
		ORDER BY $tabela.$ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT 
			$tabela.* 
		FROM 
			$tabela 
		WHERE 
			$tabela.$campo = '$texto'
		ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT 
			$tabela.* 
		FROM 
			$tabela 
		WHERE 
			$texto 
		ORDER BY $tabela.$ordem";
	}
	
	# Verifica consulta
	if($sql){
		#echo "<br>SQL: $sql";
		$consulta=consultaSQL($sql, $conn);
		# Retornar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		return(0);
	}
	
} # fecha fun��o de busca


/** Busca na tabela um novo ID */
function buscaNovoID($tabela) {

	global $conn, $tb;
	  
	$sql="SELECT count(id) qtde from $tabela";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tabela";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			$retorno=resultadoSQL($consulta, 0, 'id');
			if(!is_numeric($retorno)) $retorno=1;
		}
		else $retorno=1;
	}
	else {
		$retorno=resultadoSQL($consulta, 0, 'qtde')+1;
	}
	return($retorno);
}

function buscaUltimoID($tabela) {

	global $conn, $tb;
	  
	$sql="SELECT count(id) qtde from $tabela";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id) id from $tabela";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			$retorno=resultadoSQL($consulta, 0, 'id');
			if(!is_numeric($retorno)) $retorno=0;
		}
		else $retorno=0;
	}
	else {
		$retorno=0;
	}
	return($retorno);
}

/**
 * Retorna um array de objetos
 *
 * @return array
 * @param Resource $consulta
 */
function getArrayObjetos( $consulta ) {
        $retorno = array();
        if( contaConsulta( $consulta ) ) {
                while( $result = mysql_fetch_object( $consulta ) )  {
                        $retorno[] = $result;
                }
        }
        return $retorno;
}

?>