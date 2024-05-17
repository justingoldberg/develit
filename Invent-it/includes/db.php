<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 14/10/2002
# Ultima alteração: 14/10/2002
#    Alteração No.: 001
#
# Função:
#    Funções de conexão com banco de dados


# Conecta ao MySQL
function conectaMySQL($host, $usuario, $senha)
{
	# Conectar a banco de dados MySQL
	return(mysql_connect($host, $usuario, $senha));
	
} // fecha função conectaMysQL


# Seleciona banco de dados
function selecionaDB($banco)
{
	# Selecionar banco de dados
	return(mysql_selectdb($banco));
	
} // fecha função selecionaDB


# Executa query
function consultaSQL($sql, $conn)
{
	$retorno=mysql_query($sql, $conn);
	//echo "<br>SQL: $sql<br>Conn: $conn<br>Retorno: $retorno<br>";
	if(!$retorno)
	{
		# Erro - Consulta nao retorna valor
		aviso("Consulta","Erro ao realizar consulta em banco de Dados. <br>Verifique configurações!", "?modulo=",300);
	}
	
	# retornar consulta
	return($retorno);
	
} #fecha função para executar query no MySQL

# Executa query
function consultaSQLHide($sql, $conn)
{
	$retorno=mysql_query($sql, $conn);
	
	# retornar consulta
	return($retorno);
	
} #fecha função para executar query no MySQL


# Função para contar linhas retornadas pela matriz de consulta SQL
function contaConsulta($consulta)
{
	# Contar linhas da consulta
	return(mysql_num_rows($consulta));
	
} # fecha função para contagem da consultaSQL


# Mostrar resultado da consulta
function resultadoSQL($consulta, $linha, $coluna)
{
	# retornar valor da coluna
	return(mysql_result($consulta, $linha, $coluna));
	
} # fecha função para retorno de valor da coluna


?>
