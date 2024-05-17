<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 24/06/2003
# Ultima alteração: 12/12/2003
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para cadastro de pessoas Tipos


# Função de banco de dados - Pessoas
function dbPessoaTipo($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir' || $tipo=='inserir' ) {
	
		if($matriz[id]) {
		
			$tipoPessoa=checkTipoPessoa($matriz[tipoPessoa]);
			
			if($tipoPessoa && is_array($tipoPessoa)) {
				
				$data=dataSistema();
				$dataNascimento=converteData($matriz[dtNascimento],'form','bancodata');
				
				$sql="INSERT INTO $tb[PessoasTipos] VALUES ($matriz[idPessoaTipo],
									'$matriz[id]',
									'$tipoPessoa[id]',
									'$matriz[dtCadastro]')";
			}
		}
	} #fecha inclusao
	
	elseif($tipo=='excluir') {

		$sql="DELETE FROM
				$tb[PessoasTipos]
			  WHERE
				id=$matriz[id]";
				
	} #fecha inclusao
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}



# Função para buscar o NOVO ID da Pessoa
function buscaIDNovoPessoaTipo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from $tb[PessoasTipos]";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tb[PessoasTipos]";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta) {
			$retorno=resultadoSQL($consulta, 0, 'id');
		}
	}
	else {
		//$retorno=resultadoSQL($consulta, 0, 'qtde')+1;
		$retorno=0;
	}
	
	return($retorno);

}



 
/**
 * @return unknown
 * @param unknown $texto
 * @param unknown $campo
 * @param unknown $tipo
 * @param unknown $ordem
 * @desc Retorna um ResultSet com os dados de pessoa<BR>
TEXTO = Conteudo a ser pesquisado
CAMPO = Coluna da tabela a ser pesquisada
TIPO  = TODOS 
ORDEM = campo para order by da tabela
*/
function buscaPessoasTipos($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[PessoasTipos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[PessoasTipos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[PessoasTipos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[PessoasTipos] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
} # fecha função de busca



# Função para checagem de Tipo de Pessoa - Pessoa Tipo
function checkTipoPessoaTipo($idPessoaTipo) {

	# buscar pessoa tipo
	$consultaPessoaTipo=buscaPessoasTipos($idPessoaTipo, 'id', 'igual', 'id');
	
	if($consultaPessoaTipo && contaConsulta($consultaPessoaTipo)>0) {
		# Verificar tipo de pessoa
		$idTipo=resultadoSQL($consultaPessoaTipo, 0, 'idTipo');
		$retorno=checkTipoPessoa($idTipo);
	}
	
	return($retorno);

}


# Função para Dados Pessoas Tipos
/**
 * @return unknown
 * @param unknown $id
 * @desc Retorna um array com os dados de PessoaTipo (id, idPessoa, idTipo, dtCadastro)<BR>
Há também os seguintes arrays embutidos:<BR>
PESSOA, ENDERECO
*/
function dadosPessoasTipos($id, $enderecoPreferencial = "" ) {

	$consulta=buscaPessoasTipos($id, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idPessoa]=resultadoSQL($consulta, 0, 'idPessoa');
		$retorno[idTipo]=resultadoSQL($consulta, 0, 'idTipo');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		
		$cliente=dadosPessoas($retorno[idPessoa]);
		if (! $enderecoPreferencial ){
			$endereco=dadosEndereco($retorno[id]);
		}
		else{
			$enderecosPessoa=buscaEnderecosPessoas($id, 'idPessoaTipo','igual','idTipo');
			$endereco=enderecoSelecionaPreferencial($enderecosPessoa, $enderecoPreferencial);
		}
		
		$retorno[pessoa]=$cliente;
		$retorno[endereco]=$endereco;
		
	}
	
	return($retorno);
}


?>
