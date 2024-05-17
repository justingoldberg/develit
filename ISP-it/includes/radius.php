<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 28/05/2003
# Ultima alteração: 29/01/2004
#    Alteração No.: 004
#
# Função:
#    Configurações utilizadas pela aplicação - Integração com Servidor Radius



# Listar Grupos do Radius
function radiusGrupos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		### Menu principal - usuarios logados apenas
		novaTabela2("[Administração de Grupos de Usuários Radius]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'normal10');
					radiusProcurarGrupos($modulo, $sub, $acao, $registro, $matriz);
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=radius&sub=grupos&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=radius&sub=grupos&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
	}
}



# Usuários do Radius
function radiusUsuarios($modulo, $sub, $acao, $registro, $matriz) {
		global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		### Menu principal - usuarios logados apenas
		novaTabela2("[Administração de Usuários Radius]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'normal10');
					radiusProcurarGrupos($modulo, $sub, $acao, $registro, $matriz);
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=radius&sub=usuarios&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=radius&sub=usuarios&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
	}
}



# função para conexão com banco de dados do servidor
function conectaRadius() {
	global $radius;
	
	# conectar com banco de dados do grapi e listar cidades
	$conn=conectaMySQL($radius[host], $radius[user], $radius[passwd]);
	
	if($conn) return($conn);
		
} #fecha funcao de conexão com servidor



# Função para busca
function radiusBuscaConta($conta) {
	global $radius;

	$conn=conectaRadius();
	
	if(!$conn) {
		# Aviso de erro de conexão
		$msg="Sistema de cadastramento não está disponível para uso neste momento.";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indisponívei no momento", $msg, $url, '100%');
	}
	else {

		$sql="SELECT * FROM $radius[db].radcheck where username='$conta' AND attribute like '%password%'";
		$consulta=consultaSQL($sql, $conn);
		
		return($consulta);
	}
} #fecha  busca 




# Função para busca
function radiusBuscaUsuariosOnline($conta) {
	global $radius;

	$conn=conectaRadius();
	
	if(!$conn) {
		# Aviso de erro de conexão
		$msg="Sistema de cadastramento não está disponível para uso neste momento.";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indisponívei no momento", $msg, $url, '100%');
	}
	else {

		if($conta) {
			$conta=stripslashes($conta);
			$sql="
				SELECT 
					radacct.RadAcctId id,  
					radacct.UserName login,
					radacct.AcctSessionId telefone,
					radacct.AcctStartTime inicio,
					radacct.AcctStopTime fim,
					radacct.FramedIPAddress ip
				FROM 
					$radius[db].radacct 
				WHERE 
					UserName='$conta' 
					AND AcctStopTime = '' 
				GROUP BY 
					AcctSessionId";
		}
		else $sql="
				SELECT 
					radacct.RadAcctId id,  
					radacct.UserName login,
					radacct.AcctSessionId telefone,
					radacct.AcctStartTime inicio,
					radacct.AcctStopTime fim,
					radacct.FramedIPAddress ip
				FROM 
					$radius[db].radacct 
				WHERE 
					AcctStopTime = '' 
				GROUP BY 
					AcctSessionId";
		
		$consulta=consultaSQL($sql, $conn);
		
		return($consulta);
	}
} #fecha  busca 



# Função para Criação de Conta
function radiusCriaConta($conta, $senha, $grupo, $status) {
	global $radius;

	$conn=conectaRadius();
	
	if(!$conn) {
		# Aviso de erro de conexão
		$msg="Sistema de cadastramento não está disponível para uso neste momento.";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indisponívei no momento", $msg, $url, '100%');
	}
	else {


		if($status=='A' || $status=='T') $atributo='Password';
		elseif($status=='I') $atributo='PasswordInactive';
		else $atributo='Disable';
		
		$sql="INSERT INTO $radius[db].radcheck VALUES (0, '$conta','$atributo','$senha')";
		
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta) {
			# Gravar registro de grupo para usuario
			$sql="INSERT INTO $radius[db].usergroup VALUES (0, '$conta','$grupo')";
			$consulta=consultaSQL($sql, $conn);
		}
				
	}
} #fecha criação




# Função para Criação de Conta
function radiusStatusConta($conta, $status) {
	global $radius;

	$conn=conectaRadius();
	
	if(!$conn) {
		# Aviso de erro de conexão
		$msg="Sistema de cadastramento não está disponível para uso neste momento.";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indisponívei no momento", $msg, $url, '100%');
	}
	else {


		if($status=='A' || $status=='T') $atributo='Password';
		elseif($status=='I') $atributo='PasswordInactive';
		else $atributo='Disable';
		
		$sql="UPDATE $radius[db].radcheck SET attribute='$atributo' WHERE username='$conta'";
		
		$consulta=consultaSQL($sql, $conn);
		
	}
} #fecha criação



# Função para Criação de Conta
function radiusExcluirConta($conta, $extrato) {
	global $radius;

	$conn=conectaRadius();
	
	if(!$conn) {
		# Aviso de erro de conexão
		$msg="Sistema de cadastramento não está disponível para uso neste momento.";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indisponívei no momento", $msg, $url, '100%');
	}
	else {
	
		$sql="DELETE FROM $radius[db].radcheck WHERE username='$conta'";
		$consulta=consultaSQL($sql, $conn);
		
		$sql="DELETE FROM $radius[db].usergroup WHERE username='$conta'";
		$consulta=consultaSQL($sql, $conn);
		
		if($extrato=='S') {
			# Excluir accounting
			$sql="DELETE FROM $radius[db].radacct WHERE username='$conta'";
			$consulta=consultaSQL($sql, $conn);
		}
		
	}
} #fecha criação


# Função para montar campo de formulario
function formRadiusGrupos($grupo, $campo)
{
	global $conn, $tb, $radius;
	
	$connRadius=conectaRadius();
	
	$sql="SELECT
				groupname
			FROM
				$radius[db].usergroup
			GROUP BY
				groupname
			ORDER BY
				groupname";
				
				
	$consulta=consultaSQL($sql, $connRadius);
	
	$item="<select name=matriz[$campo]>\n";
	
	# Listargem
	$i=0;
	while($i < contaConsulta($consulta)) {
		# Valores dos campos
		$grupoRadius=resultadoSQL($consulta, $i, 'groupname');
		
		# Verificar se deve selecionar o usuario na lista
		if($grupoRadius==$grupo) $opcSelect="selected";
		else $opcSelect="";

		# Mostrar serviço		
		$item.= "<option value=$grupoRadius $opcSelect>$grupoRadius\n";

		#Incrementar contador
		$i++;
	}
	
	$item.="</select>";
	
	return($item);
	
} #fecha funcao de montagem de campo de form



# Função para montar campo de formulario
function formRadiusUsuariosGrupos($grupo, $usuario, $campo)
{
	global $conn, $tb, $radius;
	
	$connRadius=conectaRadius();
	
	$sql="SELECT
				username, groupname
			FROM
				$radius[db].usergroup
			WHERE
				groupname='$grupo'
			GROUP BY
				username
			ORDER BY
				username";
				
				
	$consulta=consultaSQL($sql, $connRadius);
	
	$item="<select name=matriz[$campo]>\n";
	
	# Listargem
	$i=0;
	while($i < contaConsulta($consulta)) {
		# Valores dos campos
		$usuarioRadius=resultadoSQL($consulta, $i, 'username');
		
		# Verificar se deve selecionar o usuario na lista
		if($usuarioRadius==$usuario) $opcSelect="selected";
		else $opcSelect="";

		# Mostrar serviço		
		$item.= "<option value=$usuarioRadius $opcSelect>$usuarioRadius\n";

		#Incrementar contador
		$i++;
	}
	
	$item.="</select>";
	
	return($item);
	
} #fecha funcao de montagem de campo de form


# Função para totalização de valores - recebe matriz com a consulta pronta
function radiusTotalAcesso($matriz) {
	
	$total=array();
	
	# totalizar horas
	for($i=0;$i<contaConsulta($matriz);$i++) {
		$total[horas]+=resultadoSQL($matriz, $i, 'time');
		$total[input]+=resultadoSQL($matriz, $i, 'input');
		$total[output]+=resultadoSQL($matriz, $i, 'output');
		$total[conexoes]++;
	}
	
	return($total);
}



# Função para montar campo de formulario
function radiusUsuariosGrupo($grupo)
{
	global $conn, $tb, $radius;
	
	$connRadius=conectaRadius();
	
	$sql="SELECT
				username, groupname
			FROM
				$radius[db].usergroup
			WHERE
				groupname='$grupo'
			GROUP BY
				username
			ORDER BY
				username";
				
				
	$consulta=consultaSQL($sql, $connRadius);
	
	return($consulta);
	
} #fecha funcao de montagem de campo de form




# formatar tempo de conexao do usuario
function radiusExtratoAcesso($horas) {

	# quebrar tempo
	$matriz[horas]=intval($horas/3600);
	$matriz[minutos]=intval( ($horas/3600 - $matriz[horas]) * 60 );
	$matriz[segundos]=intval(((($horas/3600 - $matriz[horas]) * 60) - $matriz[minutos]) * 60);
	
	if($matriz[horas]) {
		$retorno.="$matriz[horas]h, ";
	}
	
	if($matriz[minutos]) {
		$retorno.="$matriz[minutos]m, ";
	}
	
	$retorno.=" $matriz[segundos]s";
	return($retorno);
}


# Função para listagem de horas do usuario 
function radiusFonesUtilizados($dtInicial, $dtFinal, $usuario) {

	global $corFundo, $corBorda, $radius;

	$connRadius=conectaRadius();
	
	# consulta horas de acesso
	$sql="
		SELECT
			count(CallingStationID) qtde,
			CallingStationID fone 
		FROM
			$radius[db].radacct
		WHERE 
			radacct.username='$usuario'
			AND LEFT(AcctStartTime,10) 
			BETWEEN
				'$dtInicial' AND '$dtFinal 23:59:59'
		GROUP BY fone
		ORDER BY qtde DESC";
				
	$consultaAcesso=consultaSQL($sql, $connRadius);
	
	return($consultaAcesso);
}



function radiusAlterarSenha($matriz)
{
	global $radius;
	
	$connRadius=conectaRadius();
	
	# Procurar conta antes de fazer alteração
	$consulta=radiusBuscaConta($matriz[login]);
	
	if($consulta && contaConsulta($consulta)>0) {
		
		# Buscar usuario no banco de dados
		$sql="UPDATE $radius[db].radcheck set value='$matriz[senha]' where username='$matriz[login]' AND Attribute like '%password%'";
		$consulta=consultaSQL($sql, $connRadius);
		
		return($consulta);
	}
	else {
		# Criar conta com senha nova
		
	}
		
}

?>
