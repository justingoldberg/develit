<?

################################################################################
#  Criado por: Felipe dos Santos Assis - felipeassis@devel-it.com.br
#  Data de criação: 30/10/2007
# Ultima alteração: 30/10/2007
# Alteração No.: 000
#
# Função:
# Funções para o gerenciamento dos dados dos clientes entre os bancos do Ticket-IT
# e ISP-IT

	function dbEmpresas($matriz, $tipo, $idPessoaTipo = ''){
		global $tb, $conn;
		
		if($tipo == 'consultaEmpresa'){
			/*
			 * A consulta retorna o cliente cadastrado tanto o ISP quanto no
			 * Ticket e que não esteja com seu suporte bloqueado
			*/
			$sql = "SELECT $tb[Empresas].nome, $tb[Empresas].bloqueio 
					FROM $tb[Empresas] 
					INNER JOIN $tb[PessoasTipos] 
					ON ($tb[Empresas].idPessoaTipo = $tb[PessoasTipos].id) 
					LEFT JOIN $tb[Pessoas] 
					ON ($tb[PessoasTipos].idPessoa = $tb[Pessoas].id) 
					WHERE $tb[PessoasTipos].id = $idPessoaTipo";
			$retorno = consultaSQL($sql, $conn);
		}
		return $retorno;
	}
	
	function bloqueioEmpresa($modulo, $sub, $acao){
		global $tb, $conn;
		
			$idPessoaTipo = $_REQUEST['idPessoaTipo'];

		if($acao == 'bloquear'){
			$bloqueio = 'S';
			$tituloAcao = "Bloqueio";
			$msgAcao = "bloqueado";
		}
		elseif($acao == 'desbloquear'){
			$bloqueio = 'N';
			$tituloAcao = "Desbloqueio";
			$msgAcao = "desbloqueado";
		}
		
		$sql = "UPDATE $tb[Empresas] SET $tb[Empresas].bloqueio = '$bloqueio' WHERE " .
				"$tb[Empresas].idPessoaTipo = $idPessoaTipo";
		
		$consulta = consultaSQL($sql, $conn);
		if($consulta){
			// montando mensagem de confirmação
			$titulo = "$tituloAcao de Cliente";
			echo "<br>";
			$msg = "Cliente $msgAcao com sucesso!!!";
			avisoNOURL($titulo, $msg, 400);
			echo "<br>";
		}
	}
	
	/**
	 * Função responsável pela contagem de máquinas cadastradas no banco de dados do Ticket-IT
	 * @author Felipe dos S. Assis
	 * @since 10/06/2008
	 * @param unknown_type $idPessoaTipo
	 * @return unknown
	 */
	function getMaquinasEmpresa($idPessoaTipo){
		
		global $tb, $conn;
		
		$sql = "SELECT COUNT($tb[Maquinas].id) FROM $tb[Maquinas] " .
			   "INNER JOIN $tb[Empresas] " .
			   "ON ($tb[Maquinas].idEmpresa = $tb[Empresas].id) " .
			   "INNER JOIN $tb[PessoasTipos] " .
			   "ON ($tb[Empresas].idPessoaTipo = $tb[PessoasTipos].id) " .
			   "WHERE PessoasTipos.id = $idPessoaTipo";
		
		$consulta = consultaSQL($sql, $conn);
		if(contaConsulta($consulta) > 0){
			return resultadoSQL($consulta, 0, 0);
		}
		else{
			return 0;
		}
	}
?>
