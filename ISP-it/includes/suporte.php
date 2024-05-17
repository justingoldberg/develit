<?
################################################################################
#       Criado por: Leandro Barros Grandinetti - leandro@seumadruga.com.br
#  Data de criação: 06/06/2007
# Ultima alteração: 06/06/2007
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para controle de serviço de suporte
#



# Função para gravação em banco de dados
function dbSuporte($matriz, $tipo, $registro = ''){
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if( $tipo=='incluir' ){
		$tmpNome = strtoupper($matriz['idServicoPlano']);
		# Verificar se servico do cliente já possui suporte cadastrado
		$tmpBusca = buscaSuporte("idServicoPlano=$tmpNome", $campo, 'custom', 'id');
		
		# Registro já existe
		if( $tmpBusca && contaConsulta($tmpBusca)>0 ){
			
			# Mensagem de aviso
			$msg = "O suporte para este serviço já existe no banco de dados";
			avisoNOURL("Aviso: Erro ao incluir registro", $msg, 400);
			
			echo "<br>";
			suporteListarTipos($modulo, $sub, 'listar', $registro, $matriz);

		}		
		else{
	
			$sql = "INSERT INTO $tb[Suporte] 
				(idServicoPlano,
				horasExpediente,
				horasForaExpediente,
				prioridade,
				suporteForaExpediente,
				status)
			VALUES (
				$matriz[idServicoPlano], 
				$matriz[horasExpediente], 
				$matriz[horasForaExpediente], 
				'$matriz[prioridade]', 
				'$matriz[suporteForaExpediente]', 
				'$matriz[status]'
			)";
		}
	} #fecha inclusao
	# Excluir
	elseif( $tipo=='excluir' ){
		# Verificar se suporte existe
		$tmpSuporte = buscaSuporte($matriz['idSuporte'], 'id', 'igual', 'id');
		
		# Registro não existe
		if( !$tmpSuporte || contaConsulta($tmpSuporte)==0){
			# Mensagem de aviso
			$msg = "Registro não existe no banco de dados";
			$url = "?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao excluir registro", $msg, $url, 760);
		}
		else {
			$sql = "DELETE FROM $tb[Suporte] WHERE id=$matriz[idSuporte]";
		}
	}
	
	# Alterar
	elseif( $tipo=='alterar' ){
		# Verificar se serviço existe
		$tmpSuporte = buscaSuporte( $matriz['idSuporte'], 'id', 'igual', 'id');
		
		# Registro já existe
		if( !$tmpSuporte || contaConsulta($tmpSuporte)==0 ){
			# Mensagem de aviso
			$msg = "Registro não existe no banco de dados";
			$url = "?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql = "
				UPDATE 
					$tb[Suporte] 
				SET 
					idServicoPlano = '$matriz[idServicoPlano]',
					horasExpediente = '$matriz[horasExpediente]',
					horasForaExpediente = '$matriz[horasForaExpediente]',
					prioridade = '$matriz[prioridade]',
					suporteForaExpediente = '$matriz[suporteForaExpediente]',
					status = '$matriz[status]'
				WHERE 
					id=$matriz[idSuporte]";
		}
	}
	elseif($tipo == 'obterID'){
		 $sql = "SELECT $tb[Suporte].id FROM $tb[Suporte] " .
		 		"INNER JOIN $tb[ServicosPlanos] " .
		 		"ON ($tb[Suporte].idServicoPlano = $tb[ServicosPlanos].id) " .
		 		"INNER JOIN $tb[PlanosPessoas] " .
		 		"ON ($tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id) " .
		 		"INNER JOIN $tb[PessoasTipos] " .
		 		"ON ($tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id) " .
		 		"WHERE PessoasTipos.id = 8";
	}
	elseif($tipo == 'checarStatus'){
		$sql = "SELECT status FROM $tb[Suporte] WHERE id = $registro";
	}
	
	
	if($sql) { 
		$retorno = consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados

# função de busca de suporte
function buscaSuporte($texto, $campo, $tipo, $ordem) {
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if( $tipo=='todos' ){
		$sql = "SELECT * FROM $tb[Suporte] ORDER BY $ordem";
	}
	elseif( $tipo=='contem' ){
		$sql = "SELECT * FROM $tb[Suporte] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif( $tipo=='igual' ){
		$sql = "SELECT * FROM $tb[Suporte] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif( $tipo=='custom' ){
		$sql = "SELECT * FROM $tb[Suporte] WHERE $texto ORDER BY $ordem";
	}
	elseif( $tipo=='max'){
		$sql = "SELECT MAX(id) as id FROM $tb[Suporte] ";
	}
	elseif('linha'){
		$sql = "SELECT * FROM $tb[Suporte] WHERE $campo = $texto";
	}
		
	# Verifica consulta
	if( $sql ){
		$consulta = consultaSQL($sql, $conn);
		# Retornar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pôde ser realizada por falta de parâmetros.";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
}

#Funcao que retorna o total de horas de suporte utilizadas por pessoa
function suporteTotalHorasEmUso($idPessoaTipo) {
	global $conn, $tb;
	
	$sql = "
			select sum({$tb[Suporte]}.horasExpediente + {$tb[Suporte]}.horasForaExpediente) as total 
			from $tb[Suporte] inner join 
			$tb[ServicosPlanos] on ({$tb[Suporte]}.idServicoPlano = {$tb[ServicosPlanos]}.id) inner join 
			$tb[PlanosPessoas] on ({$tb[PlanosPessoas]}.id = {$tb[ServicosPlanos]}.idPlano)
			where {$tb[PlanosPessoas]}.idPessoaTipo = $idPessoaTipo";
	$consulta=consultaSQL($sql, $conn);
	# retornar resultado
	$retorno=resultadoSQL($consulta, 0, 'total');
	
	return $retorno;
}

function suporteTotalEmUso($idPessoaTipo){
	global $conn, $tb;
	$sql = "
			select count(*) as total 
			from $tb[Suporte] inner join 
			$tb[ServicosPlanos] on ({$tb[Suporte]}.idServicoPlano = {$tb[ServicosPlanos]}.id) inner join 
			$tb[PlanosPessoas] on ({$tb[PlanosPessoas]}.id = {$tb[ServicosPlanos]}.idPlano)
			where {$tb[PlanosPessoas]}.idPessoaTipo = $idPessoaTipo";

	$consulta=consultaSQL($sql, $conn);
	# retornar resultado
	$retorno=resultadoSQL($consulta, 0, 'total');
	return $retorno;
}

function suporteServicosDisponivel($idPessoaTipo, $acao = ''){
	global $conn, $tb;
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$campos  = array( "$tb[ServicosPlanos].id as idServicoPlano","$tb[Servicos].nome as nomeServico","$tb[PlanosPessoas].nome as nomePlano");
	$tabelas = "{$tb[Servicos]} " .
			"inner join $tb[ServicosPlanos] on ($tb[Servicos].id = $tb[ServicosPlanos].idServico) " .
			"inner join $tb[ServicosParametros] on ($tb[Servicos].id = $tb[ServicosParametros].idServico) " .
			"inner join $tb[Parametros] on ($tb[ServicosParametros].idParametro = $tb[Parametros].id) " .
			"inner join $tb[PlanosPessoas] on ($tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id) " .
			"left join $tb[Suporte] on ($tb[ServicosPlanos].id = $tb[Suporte].idServicoPlano)";
	
	$condicao = "$tb[Parametros].parametro = 'Suporte' 
			and $tb[PlanosPessoas].idPessoaTipo = $idPessoaTipo 
			AND $tb[ServicosPlanos].idStatus <> 7";
	
	if($acao == 'adicionar'){
		$condicao .= "$tb[Suporte].id is NULL";
	}
	
	$consulta = $bd->seleciona( $tabelas, $campos, $condicao, '', '' );
	
	return $consulta;
}

function suporteTotal($idPessoaTipo) {

	global $conn, $tb;

	# Totalizar parametro
	$sql="
		select count(*) as qtd 
		from $tb[Servicos] left join 
		$tb[ServicosPlanos] on ({$tb[Servicos]}.id = {$tb[ServicosPlanos]}.idServico) left join
		$tb[ServicosParametros] on ({$tb[ServicosParametros]}.idServico = {$tb[Servicos]}.id) left join
		$tb[Parametros] on ({$tb[Parametros]}.id = {$tb[ServicosParametros]}.idParametro) left join
		$tb[ParametrosModulos] on ({$tb[ParametrosModulos]}.idParametro = {$tb[Parametros]}.id) left join
		$tb[Modulos] on ({$tb[Modulos]}.id = {$tb[ParametrosModulos]}.idModulo) left join
		$tb[PlanosPessoas] on ({$tb[PlanosPessoas]}.id = {$tb[ServicosPlanos]}.idPlano) left join
		$tb[PessoasTipos] on ({$tb[PessoasTipos]}.id = {$tb[PlanosPessoas]}.idPessoaTipo)
		where {$tb[Parametros]}.parametro = 'suporte' AND
		{$tb[PessoasTipos]}.id = $idPessoaTipo	
		";
	
	$consulta = consultaSQL($sql, $conn);
	$retorno = resultadoSQL($consulta, $a, 'qtd');
	
	return($retorno);
}
// Função para efetuar o bloqueio do Suporte
// by Felipe Assis (14/11/2007)
/**
 * Função responsável pelo bloqueio de suporte para o cliente
 * @author Felipe dos S. Assis
 * @since 14/11/2007
 * @param unknown_type $modulo
 * @param unknown_type $sub
 * @param unknown_type $acao
 */
function bloqueioSuporte($modulo, $sub, $acao){
		global $tb, $conn;
		$idSuporte = $_REQUEST['idSuporte'];
		$acaoBloqueio = $_REQUEST['acaoBloqueio'];

		if($acaoBloqueio == 'bloquearSuporte'){
			$status = 'I';
			$tituloAcao = "Bloqueio";
			$msgAcao = "bloqueado";
		}
		elseif($acaoBloqueio == 'desbloquearSuporte'){
			$status = 'A';
			$tituloAcao = "Desbloqueio";
			$msgAcao = "desbloqueado";
		}
		
		$isBloqueado = getServicoBloqueado($idSuporte);
		
		// implementar aqui método para consultar cancelamento do serviço
		if($isBloqueado == true && $acaoBloqueio == "desbloquearSuporte"){
			$titulo = $tituloAcao . " de Suporte";
			echo "<br>";
			$msg = "Este suporte não pode ser desbloqueado porque seu Serviço foi cancelado!";
			avisoNOURL($titulo, $msg, 400);
			echo "<br>";
		}
		else{
			$sql = "UPDATE $tb[Suporte] SET status = '$status' WHERE id = $idSuporte";
			$consulta = consultaSQL($sql, $conn);
			if($consulta){
				// montando mensagem de confirmação
				$titulo = "$tituloAcao de Suporte";
				echo "<br>";
				$msg = "Suporte $msgAcao com sucesso!!!";
				avisoNOURL($titulo, $msg, 400);
				echo "<br>";
			}
		}
		
	}

	/**
	 * Função responsável pelo bloqueio automático de Suporte quando um Serviço de Plano é cancelado.
	 * Caso o suporte já esteja bloqueado a função não fará nenhuma alteração
	 * @author Felipe dos Santos Assis
	 * @since 13/06/2008
	 * @param unknown_type $idServicoPlano
	 */
	function bloqueioSuporteAutomatico($idServicoPlano){	
		global $tb, $conn;
		# Obtendo os suportes dos Serviços		
		$sql = "SELECT $tb[Suporte].id, $tb[Suporte].status FROM $tb[Suporte] " .
			   "WHERE $tb[Suporte].idServicoPlano = " . $idServicoPlano . " " .
			   "AND $tb[Suporte].status = 'A'";
		$consulta = consultaSQL($sql, $conn);
		
		$idSuporte = resultadoSQL($consulta, 0, 0);
		
		if(contaConsulta($consulta) > 0){
			$sqlBloqueio = "UPDATE $tb[Suporte] SET $tb[Suporte].status = 'I' " .
						   "WHERE $tb[Suporte].id = " . $idSuporte;
			$consultaBloqueio = consultaSQL($sqlBloqueio, $conn);
		}
	}
?>