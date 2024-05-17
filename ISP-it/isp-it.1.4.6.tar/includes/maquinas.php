<?
################################################################################
#       Criado por: Leandro Barros Grandinetti - leandro@seumadruga.com.br
#  Data de criação: 01/06/2007
# Ultima alteração: 01/06/2007
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para controle de serviço de suporte
#

# Função para gravação em banco de dados
function dbMaquina($matriz, $tipo){
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if( $tipo=='incluir' ){
		$tmpNome = strtoupper($matriz['nome']);
		# Verificar se host existe
		$tmpBusca = buscaMaquinas("upper(nome)='$tmpNome'", $campo, 'custom', 'id');
		
		# Registro já existe
		if( $tmpBusca && contaConsulta($tmpBusca)>0 ){
			# Mensagem de aviso
			$msg="Registro já existe no banco de dados";
			avisoNOURL("Aviso: Erro ao incluir registro", $msg, 400);
			
			echo "<br>";
			maquinasListarPessoas($modulo, $sub, 'listar', $registro, $matriz);

		}		
		else{
			
			$sql="INSERT INTO $tb[Maquinas] " .
					"(nome, ip, cliente, idEmpresa, data, obs, idSuporte)" .
					"VALUES (" .
					"$matriz[idServicosPlanos], " .
					"'$matriz[nome]', " .
					" ''$matriz[ip]', " .
					"'$matriz[observacao]')";
		}
	} #fecha inclusao
	
	# Excluir
	elseif( $tipo=='excluir' ){
		# Verificar se maquina existe
		$tmpServico = buscaMaquinas($matriz['idMaquina'], 'id', 'igual', 'id');
		
		# Registro já existe
		if( !$tmpServico || contaConsulta($tmpServico)==0 ){
			# Mensagem de aviso
			$msg = "Registro não existe no banco de dados";
			$url = "?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao excluir registro", $msg, $url, 760);
		}
		else{
			$sql = "DELETE FROM $tb[Maquinas] WHERE $tb[Maquinas].id=$matriz[idMaquina]";
		}
	}
	
	# Alterar
	elseif( $tipo=='alterar' ){
		# Verificar se serviço existe
		$tmpServico = buscaMaquinas( $matriz['id'], 'id', 'igual', 'id' );
		
		# Registro já existe
		if( !$tmpServico || contaConsulta($tmpServico)==0 ){
			# Mensagem de aviso
			$msg = "Registro não existe no banco de dados";
			$url = "?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else{
			$sql = "
				UPDATE 
					$tb[Maquinas] 
				SET 
					$tb[Maquinas].idSuporte = '$matriz[idSuporte]',
					$tb[Maquinas].nome = '$matriz[nome]',
					$tb[Maquinas].ip = '$matriz[ip]',
					observacao='$matriz[observacao]'
				WHERE 
					$tb[Maquinas].id = $matriz[idMaquina]";
		}
	}
	elseif( $tipo=='alterarSuporte' ){
		# Verificar se serviço existe
		$tmpServico = buscaMaquinas( $matriz['id'], 'id', 'igual', 'id' );
		# Registro já existe
		if( !$tmpServico || contaConsulta($tmpServico)==0 ){
			# Mensagem de aviso
			$msg = "Registro não existe no banco de dados";
			$url = "?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else{
			$sql = "
				UPDATE 
					$tb[Maquinas] 
				SET 
					$tb[Maquinas].idSuporte = '$matriz[idSuporte]'
				WHERE 
					$tb[Maquinas].id = $matriz[idMaquina]";
		}
	}
	
	
	if( $sql ){ 
		$retorno = consultaSQL($sql, $conn);
		return($retorno); 
	}	
} # fecha função de gravação em banco de dados


# função de busca de maquinas
function buscaMaquinas($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Maquinas] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Maquinas] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Maquinas] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Maquinas] WHERE $texto ORDER BY $ordem";
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
	
}

// Funcao que retorna o total de maquinas cadastradas por pessoa
function maquinasTotalEmUso($idPessoaTipo) {
	global $conn, $tb;
	
	$sql = "SELECT count(*) qtd " .
			"FROM $tb[MaquinasSuporte] " .
			"INNER JOIN $tb[Suporte] " .
			"ON ($tb[MaquinasSuporte].idSuporte = $tb[Suporte].id) " .
			"INNER JOIN $tb[ServicosPlanos] " .
			"ON ($tb[Suporte].idServicoPlano = $tb[ServicosPlanos].id) " .
			"INNER JOIN PlanosPessoas " .
			"ON (ServicosPlanos.idPlano = PlanosPessoas.id) " .
			"INNER JOIN PessoasTipos " .
			"ON (PlanosPessoas.idPessoaTipo = PessoasTipos.id) " .
			"WHERE PessoasTipos.id = $idPessoaTipo";
	$consulta=consultaSQL($sql, $conn);
	# retornar resultado
	$retorno=resultadoSQL($consulta, 0, 'qtd');
	
	return $retorno;
}

# Função para totalização de parametros
function maquinasTotal($idPessoaTipo) {

	global $conn, $tb;

	# Totalizar parametro
	$sql="select " .
			"$tb[PlanosPessoas].id idPlano, " .
			"$tb[PlanosPessoas].nome nomePlano, " .
			"$tb[ServicosPlanos].id idServico, " .
			"$tb[Servicos].nome nomeServico, " .
			"$tb[Modulos].id idModulo, " .
			"$tb[Modulos].modulo, " .
			"$tb[Parametros].descricao nomeParametro, " .
			"$tb[Parametros].parametro, " .
			"$tb[Unidades].unidade, " .
			"$tb[ServicosParametros].valor " .
			"FROM " .
			"$tb[Modulos], " .
			"$tb[Parametros], " .
			"$tb[ParametrosModulos], " .
			"$tb[ServicosParametros], " .
			"$tb[Servicos], " .
			"$tb[ServicosPlanos], " .
			"$tb[StatusServicos], " .
			"$tb[PlanosPessoas], " .
			"$tb[PessoasTipos], " .
			"$tb[Pessoas], " .
			"$tb[Unidades] " .
			"WHERE " .
			"$tb[Modulos].id=$tb[ParametrosModulos].idModulo " .
			"AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id " .
			"AND $tb[Parametros].idUnidade = $tb[Unidades].id " .
			"AND $tb[Parametros].id = $tb[ServicosParametros].idParametro " .
			"AND $tb[ServicosParametros].idServico  = $tb[Servicos].id " .
			"AND $tb[Servicos].id = $tb[ServicosPlanos].idServico " .
			"AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id " .
			"AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id " .
			"AND $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id " .
			"AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id " .
			"AND $tb[Modulos].modulo='maquinas' " .
			"AND ($tb[StatusServicos].status='A' OR $tb[StatusServicos].status='I' OR $tb[StatusServicos].status='T') " .
			"AND $tb[Parametros].parametro='maquinas' " .
			"AND $tb[PessoasTipos].id=$idPessoaTipo " .
			"ORDER BY idServico";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		if(contaConsulta($consulta)==1) {
			# retornar resultado
			$retorno=resultadoSQL($consulta, 0, 'valor');
		}
		else {
			# Contabilizar tudo
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$retorno+=resultadoSQL($consulta, $a, 'valor');
			}
		}
	}
	return($retorno);
}

# Função para totalização de parametros
function maquinaTotalContasServico($idPessoaTipo, $idServicoPlano) {

	global $conn, $tb;

	# Totalizar parametro
	$sql="
		select 
			$tb[PlanosPessoas].id as idPlano, 
			$tb[PlanosPessoas].nome as nomePlano,
			$tb[ServicosPlanos].id as idServico, 
			$tb[Servicos].nome as nomeServico,
			$tb[Modulos].id as idModulo, 
			$tb[Modulos].modulo, 
			$tb[Parametros].descricao as nomeParametro,
			$tb[Parametros].parametro, 
			$tb[Unidades].unidade, 
			$tb[ServicosParametros].valor 
		FROM
			$tb[Modulos], 
			$tb[Parametros], 
			$tb[ParametrosModulos], 
			$tb[ServicosParametros], 
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[StatusServicos], 
			$tb[PlanosPessoas], 
			$tb[PessoasTipos], 
			$tb[Pessoas], 
			$tb[Unidades] 
		WHERE
			$tb[Modulos].id=$tb[ParametrosModulos].idModulo 
			AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id 
			AND $tb[Parametros].idUnidade = $tb[Unidades].id 
			AND $tb[Parametros].id = $tb[ServicosParametros].idParametro 
			AND $tb[ServicosParametros].idServico  = $tb[Servicos].id 
			AND $tb[Servicos].id = $tb[ServicosPlanos].idServico 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
			AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
			AND $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[Modulos].modulo='maquinas'
			AND $tb[Parametros].parametro='maquinas'
			AND $tb[PessoasTipos].id=$idPessoaTipo
			AND $tb[ServicosPlanos].id=$idServicoPlano
		ORDER BY
			idServico";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		if(contaConsulta($consulta)==1) {
			# retornar resultado
			$retorno=resultadoSQL($consulta, 0, 'valor');
		}
		else {
			# Contabilizar tudo
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$retorno+=resultadoSQL($consulta, $a, 'valor');
			}
		}
	}
	return($retorno);
}

# Contar maquinas em uso
function maquinaTotalContasServicoEmUso($idPessoaTipo, $idServicoPlano) {
	
	global $tb, $conn;

	if($idPessoaTipo) {
		$sql = "
			select count(*) qtd 
			from $tb[Maquinas],$tb[ServicosPlanos],$tb[PlanosPessoas] 
			where $tb[Maquinas].idServicoPlano = $tb[ServicosPlanos].id 
			and $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id
			and $tb[PlanosPessoas].idPessoaTipo = $idPessoaTipo 
			and ServicosPlanos.id = $idServicoPlano			
		";
		
		$consulta=consultaSQL($sql, $conn);
		$retorno=resultadoSQL($consulta, 0, 'qtd');
	}
	
	return($retorno);
}

function maquinasCadastradasPorServico($idServicoPlano){
	global $conn, $tb;
	$bd = new BDIT();
	$bd->setConnection( $conn );
	/* Consulta crua:
	select Maquinas.id,Maquinas.nome from Maquinas where idServicoPlano = $idServicoPlano
	*/
	$campos  = array( "$tb[Maquinas].id","$tb[Maquinas].nome");
	$tabelas = "$tb[Maquinas]";
	$condicao = "idServicoPlano = $idServicoPlano";
	
	$consulta = $bd->seleciona( $tabelas, $campos, $condicao, '', '' );
	
	return $consulta;
}

/*
 * Menu para seleção de máquinas cadastrardas relacionadas ao cliente
 * by Felipe Assis (06/11/2007)
 * */
function selectMaquinaEmpresa($idPessoaTipo, $nomeMaquina = ''){
	global $conn, $tb;
	
	/*
	 * Consultando Máquinas do cliente disponíveis
	 * Query
	*/
	 
	 $sql = "SELECT maq.id, maq.nome FROM $tb[Maquinas] maq " .
	 		"INNER JOIN $tb[Empresas] emp " .
	 		"ON (maq.idEmpresa = emp.id) LEFT JOIN $tb[PessoasTipos] pt " .
	 		"ON (emp.idPessoaTipo = pt.id) LEFT JOIN $tb[Pessoas] p " .
	 		"ON (pt.idPessoa = p.id) WHERE pt.id = $idPessoaTipo"; 
	 	
	 $consulta = consultaSQL($sql, $conn);
	 $tmpMaquina = $nomeMaquina;
	 
	 if($consulta && contaConsulta($consulta) > 0){
	 	$retorno = "<select name='matriz[idMaquina]'>";
	 	if($nomeMaquina || $nomeMaquina != ''){
	 		$idMaquina = resultadoSQL($consulta, 0, 'id');
	 		$retorno .= "<option value='$idMaquina' selected>$tmpMaquina</option>";
	 		// criando opções para as demais máquinas do cliente
	 		for($i = 0; $i < mysql_num_rows($consulta); $i ++){
	 			$dadosMaquina = mysql_fetch_row($consulta);
	 			$idMaquina = $dadosMaquina[0];
	 			$nomeMaquina = $dadosMaquina[1];
	 			//pula o nome da máquina já selecionada
	 			if($nomeMaquina == $tmpMaquina){
	 				continue;
	 			}
	 			else{
	 				$retorno .= "<option value=$idMaquina >$nomeMaquina</option>";
	 			}
	 		}
	 	}
	 	else{
	 	$retorno .= "<option selected>Selecione uma Máquina</option>";
	 		for($i = 0; $i < mysql_num_rows($consulta); $i ++){
	 			$dadosMaquina = mysql_fetch_row($consulta);
	 			$idMaquina = $dadosMaquina[0];
	 			$nomeMaquina = $dadosMaquina[1];
	 			$retorno .= "<option value=$idMaquina >$nomeMaquina</option>";
	 		}
	 	}
	 	$retorno .= "</select>";
	 }
	 else{
	 	$msg = "Atenção, o cliente não tem máquinas disponíveis para realizar este suporte";
	 	$titulo = "Atenção: AsMáquina(s) do cliente não disponível(eis)";
	 	$retorno = avisoNOURL($titulo, $msg, 400);
	 }
	 
	 return $retorno;
}

/*
 * Menu para seleção multipla de máquinas cadastrardas relacionadas ao cliente
 * by Felipe Assis (20/11/2007)
 * */
 function selectMultiMaquinaEmpresa($idPessoaTipo, $acao, $idSuporte = ''){
 	global $conn, $tb;
 	
 	/*
 	 * Consultando máquinas do cliente disponíveis
 	 * consultando máquinas que não estejam recebendo o suporte
 	 * */
 
	$sql = "SELECT $tb[Maquinas].id, $tb[Maquinas].nome FROM $tb[Maquinas] " .
	 			"INNER JOIN $tb[Empresas] " .
	 			"ON($tb[Maquinas].idEmpresa = $tb[Empresas].id) " .
	 			"LEFT JOIN $tb[PessoasTipos] " .
	 			"ON ($tb[Empresas].idPessoaTipo = $tb[PessoasTipos].id) " .
	 			"LEFT JOIN $tb[Pessoas] " .
	 			"ON ($tb[PessoasTipos].idPessoa = $tb[Pessoas].id) " .
	 			"WHERE $tb[PessoasTipos].id = $idPessoaTipo ";
	
	if($acao == 'adicionar'){
		$sqlADD = "AND $tb[Maquinas].id " .
	 		   "NOT IN (SELECT $tb[MaquinasSuporte].idMaquina FROM $tb[MaquinasSuporte])";
	 	$sql .= $sqlADD;
	}
	elseif($acao == 'alterar'){
		 $sqlADD = "AND $tb[Maquinas].id " .
		 		"IN (SELECT $tb[Maquinas].id FROM $tb[Maquinas] " .
		 		"INNER JOIN $tb[MaquinasSuporte] " .
		 		"ON ($tb[Maquinas].id = $tb[MaquinasSuporte].idMaquina) " .
		 		"WHERE $tb[MaquinasSuporte].idSuporte = $idSuporte)";
		 $sql .= $sqlADD;
	}
	
	$consulta = consultaSQL($sql, $conn);
	if($consulta && contaConsulta($consulta)){
		$retorno = "<select multiple name='idMaquinas[]' size='4'>";
		for($i = 0; $i < mysql_num_rows($consulta); $i ++){
			$dadosMaquina = mysql_fetch_row($consulta);
			$idMaquina = $dadosMaquina[0];
			$nomeMaquina = $dadosMaquina[1]; 
			if($acao == 'adicionar'){
			$retorno .= "<option value=$idMaquina>$nomeMaquina</option>";
			}
			elseif($acao == 'alterar'){
				$retorno .= "<option selected value=$idMaquina>$nomeMaquina</option>";
			}
		}
		if($acao == 'alterar'){
			$sqlSub = "SELECT maq.id, maq.nome FROM $tb[Maquinas] maq " .
	 				"INNER JOIN $tb[Empresas] emp " .
	 				"ON(maq.idEmpresa = emp.id) " .
	 				"LEFT JOIN $tb[PessoasTipos] pt " .
	 				"ON (emp.idPessoaTipo = pt.id) " .
	 				"LEFT JOIN $tb[Pessoas] p " .
	 				"ON (pt.idPessoa = p.id) " .
	 				"WHERE pt.id = $idPessoaTipo " .
	 				"AND maq.id " .
	 	    		"NOT IN (SELECT $tb[MaquinasSuporte].idMaquina " .
	 	    		"FROM $tb[MaquinasSuporte] " .
	 	    		"WHERE $tb[MaquinasSuporte].idSuporte = $idSuporte)";
	 	   $consultaSub = consultaSQL($sqlSub, $conn);
	 		if($consultaSub){
	 		   	for($j = 0; $j < mysql_num_rows($consultaSub); $j ++){
	 		   		$dadosMaquinaSub = mysql_fetch_row($consultaSub);
	 		   		$idMaquinaSub = $dadosMaquinaSub[0];
	 		   		$nomeMaquinaSub = $dadosMaquinaSub[1];
	 		   		$retorno .= "<option value=$idMaquinaSub>$nomeMaquinaSub</option>";
	 	 	  	}
	 		} 
		}
		
	 	
		$retorno .= "</select>";
	}
	else{
		$msg = "Atenção, o cliente não tem máquinas disponíveis para realizar este suporte";
	 	$titulo = "Atenção: AsMáquina(s) do cliente não disponível(eis)";
	 	$retorno = avisoNOURL($titulo, $msg, 400);
	}
	return $retorno;
}
?>