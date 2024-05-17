<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 11/12/2003
# Ultima altera��o: 07/01/2004
#    Altera��o No.: 003
#
# Fun��o:
#    Painel - Fun��es para controle de usuarios radius por pessoas
# 

# fun��o de busca de grupos
function buscaEmailConfig($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[EmailConfig] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[EmailConfig] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[EmailConfig] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[EmailConfig] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
	
} # fecha fun��o de busca de grupos



# Fun��o para grava��o em banco de dados
function dbEmailConfig($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[EmailConfig] VALUES (
			0, 
			'$matriz[idEmail]', 
			'$matriz[idParametro]',
			'$matriz[valor]'
		)";
	} #fecha inclusao
	
	# Excluir
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[EmailConfig] WHERE id=$matriz[id]";
	}
	
	# Excluir
	elseif($tipo=='excluiremail') {
		$sql="DELETE FROM $tb[EmailConfig] WHERE idEmail=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		$sql="
			UPDATE 
				$tb[EmailConfig]
			SET
				valor='$senha'
			WHERE
				id='$matriz[id]'
		";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados



# Funcao para cadastro de usuarios
function emailConfigContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	$idEmail=$matriz[id];
	
	$email=dadosEmail($idEmail);
	
	$matriz[idEmail]=$email[id];
	$matriz[idDominio]=$email[idDominio];
	
	if(!$matriz[bntVerificar] && !$matriz[bntConfirmar]) $matriz[idDominio]=$email[idDominio];
	
	# Form de inclusao
	if(!$matriz[bntAtualizar]) {

		# Visualizar Dominio
		verEmails($modulo, $sub, $acao, $matriz[id], $matriz);
		echo "<br>";

		# Listar parametros em formul�rio
		# Formul�rio de postagem de dados de configura��es e parametros
		formEmailConfig($modulo, $sub, $acao, $registro, $matriz);

	} #fecha form
	else {

		# Gravar informa��es de configura��o
		# Dados da conta de email
		$dominio=dadosDominio($matriz[idDominio]);
		$email=dadosEmail($matriz[idEmail]);
		$matriz[dominio]=$dominio[nome];
		$matriz[login]=$email[login];
		
		## aplicar configura��es
		emailAplicaConfiguracao($matriz);
		## fim de aplica��o de configura��es
		
	
		# Mensagem de aviso
		$msg="Configura��es atualizadas com sucesso!";
		avisoNOURL("Aviso", $msg, 400);
		echo "<br>";
		
		$matriz[id]=$matriz[idDominio];
		emailListarContasDominios($modulo, 'mail', 'listar', "$matriz[idPessoasTipos]:$matriz[idDominio]", $matriz);
	}
} # fecha funcao de inclusao de grupos





# Fun�ao para inclus�o de configura��es para email
function emailAdicionaConfiguracao($idEmail, $idDominio) {

	$dominio=dadosDominio($idDominio);

	$dominiosParametros=carregaParametrosDominio($idDominio, 'mail');

	if(is_array($dominiosParametros)) {
		$campos=array_keys($dominiosParametros);
		$matriz[idEmail]=$idEmail;
		
		for($a=0;$a<count($campos);$a++) {
			# Gravar configura��o
			$matriz[idParametro]=$campos[$a];
			$matriz[valor]=$dominiosParametros[$campos[$a]];
			
			# Gravar no Banco de Dados
			dbEmailConfig($matriz, 'incluir');
		}
	}
}


# Fun�ao para inclus�o de configura��es para email
function emailConfiguraParametro($idEmail, $parametro, $valor) {

	global $conn, $tb;
	
	# Busca ID do parametro
	$infoParametro=buscaDadosParametro($parametro, 'parametro','igual','id');
	
	if($infoParametro && is_array($infoParametro)) {
		# Verificar exist�ncia de parametro para email
		
		$consulta=buscaEmailConfig("idEmail=$idEmail AND idParametro=$infoParametro[id]",'','custom','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			# se existir, atualizar valor do parametro
			$sql="
				UPDATE
					$tb[EmailConfig]
				SET
					valor='$valor'
				WHERE
					idEmail=$idEmail
					AND idParametro=$infoParametro[id]";
					
			$grava=consultaSQL($sql, $conn);
			
		}
		else {
			# se nao existir, incluir parametro
			$sql="
				INSERT INTO
					$tb[EmailConfig]
				VALUES (
					0,
					idEmail=$idEmail,
					idParametro=$infoParametro[id],
					'$valor'
				)";
					
			$grava=consultaSQL($sql, $conn);
		}
	}

}




# Listar parametros do dom�nio
function listarEmailConfig($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;

	
	# Selecionar parametros do dominio
	novaTabela("Configura��es do Email", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
	$consulta=buscaEmailConfig($registro, 'idEmail','igual','id');
	
		if($consulta && contaConsulta($consulta)>0) {

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela("Par�metro", 'center', '80%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '20%', 'tabfundo0');
			fechaLinhaTabela();
		
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$id=resultadoSQL($consulta, $a, 'id');
				$idEmail=resultadoSQL($consulta, $a, 'idEmail');
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$valor=resultadoSQL($consulta, $a, 'valor');
				$parametro=checkParametro($idParametro);
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($parametro[descricao], 'left', '80%', 'normal10');
					itemLinhaTabela($valor, 'center', '20%', 'normal10');
				fechaLinhaTabela();
			}
		}
		else {
			$texto="<span class=txtaviso>Nenhuma configura��o encontrada para email!</span>";
			itemTabelaNOURL($texto, 'left', $corFundo, 2, 'normal10');
		}
	fechaTabela();
}


# Formul�rio de Dados de Configura��o
function formEmailConfig($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;
	

	# Motrar tabela de busca
	novaTabela2("[Configura��es]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, "$registro:$matriz[idDominio]");
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=matriz[idDominio] value=$matriz[idDominio]>
			<input type=hidden name=matriz[idPessoasTipos] value=$matriz[idPessoaTipo]>
			<input type=hidden name=matriz[idEmail] value=$matriz[idEmail]>
			<input type=hidden name=registro value=$registro:$matriz[idEmail]>
			&nbsp;$msg";
			itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		
		$sql="
			SELECT 
				$tb[EmailConfig].idParametro  idParametro, 
				$tb[EmailConfig].valor valor, 
				$tb[Parametros].descricao descricao, 
				$tb[Parametros].parametro parametro, 
				$tb[Parametros].tipo tipoParametro
			FROM
				$tb[EmailConfig], 
				$tb[Parametros]
			WHERE
				$tb[EmailConfig].idParametro=$tb[Parametros].id 
				AND $tb[EmailConfig].idEmail=$matriz[idEmail]
			ORDER BY
				$tb[Parametros].id
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				# Valores
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$valor=resultadoSQL($consulta, $a, 'valor');
				$descricao=resultadoSQL($consulta, $a, 'descricao');
				$tipoParametro=resultadoSQL($consulta, $a, 'tipoParametro');
				
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('60%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>$descricao: </b>";
					htmlFechaColuna();
					itemLinhaForm(formInputParametro($idParametro, $valor, $parametro, 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
		}
		

		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit name=matriz[bntAtualizar] value=Atualizar class=submit>";
			# Bot�o de exclus�o
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
}



# Fun��o para aplica��o de configura��es
function emailAplicaConfiguracao($matriz) {

	# Quota
	if($matriz[quota]) {
		$quota=emailConfiguraParametro($matriz[idEmail], 'quota',$matriz[quota]);
		$gravaManager=managerComando($matriz, 'emailquota');
	}
	
	# Anti-Virus
	if($matriz[antivirus]) {
		$antivirus=emailConfiguraParametro($matriz[idEmail], 'antivirus', $matriz[antivirus]);
		
		if($matriz[antivirus]=='S') $gravaManager=managerComando($matriz, 'emailantivirusadicionar');
		elseif($matriz[antivirus]=='N') $gravaManager=managerComando($matriz, 'emailantivirusremover');
	}
	
	# Filtros
	if($matriz[filtros]) $filtros=emailConfiguraParametro($matriz[idEmail], 'filtros', $matriz[filtros]);

	# Central do Assinante
	if($matriz[centralassinante]) $centralassinante=emailConfiguraParametro($matriz[idEmail], 'centralassinante', $matriz[centralassinante]);

	###
	# Configura��es que fazem grupo
	###
		# Webmail
		if($matriz[webmail]) {
			$webmail=emailConfiguraParametro($matriz[idEmail], 'webmail', $matriz[webmail]);
			
			if($matriz[webmail]=='N') {
				$restricao='imap';
			}
		}

		# POP
		if($matriz[pop3]) {
			$pop3=emailConfiguraParametro($matriz[idEmail], 'pop3', $matriz[pop3]);
		
			if($matriz[pop3]=='N') {
				if($restricao) $restricao.=",";
				$restricao.="pop3";
			}
		}
		
		# SMTP
		if($matriz[relay]) {
			$smtp=emailConfiguraParametro($matriz[idEmail], 'relay', $matriz[relay]);
			
			if($matriz[relay]=='N') {
				if($restricao) $restricao.=",";
				$restricao.="relay";
			}
		}
		
		# IMAP
		if($matriz[imap]) {
			$imap=emailConfiguraParametro($matriz[idEmail], 'imap', $matriz[imap]);
		
			if($matriz[imap]=='N' && (!$matriz[webmail] || $matriz[webmail]=='S')) {
				if($restricao) $restricao.=",";
				$restricao.="imap";
			}
		}
		
		# Troca de senha
		if($matriz[trocasenha]) {
			$trocasenha=emailConfiguraParametro($matriz[idEmail], 'trocasenha', $matriz[trocasenha]);
			
			if($matriz[trocasenha]=='N') {
				if($restricao) $restricao.=",";
				$restricao.="passwd";
			}
		}
		
		# Bounce - Entrega local de Mensagens
		if($matriz[bounce]) {
			$bounce=emailConfiguraParametro($matriz[idEmail], 'bounce', $matriz[bounce]);
			
			if($matriz[bounce]=='N') {
				if($restricao) $restricao.=",";
				$restricao.="bounce";
			}
		}
		
		# Aplicar configura��o
		if($restricao) {
			$matriz[restricao]=$restricao;
			$gravaManager=managerComando($matriz, 'configuracoes');
		}
		else {
			$matriz[restricao]='liberar';
			$gravaManager=managerComando($matriz, 'configuracoes');
		}
	###
	# Fim das configura��es que fazem grupo
	###

}


# Fun��o para aplica��o de configura��es - carregamento de parametros para email
function emailAplicaConfiguracaoEmail($matriz) {

	global $conn, $tb;
	
	# Carregar parametros
	$sql="
		SELECT 
			$tb[Parametros].parametro, 
			$tb[EmailConfig].valor 
		FROM
			$tb[EmailConfig], 
			$tb[Parametros] 
		WHERE 
			$tb[Parametros].id=$tb[EmailConfig].idParametro 
			AND $tb[EmailConfig].idEmail=$matriz[idEmail]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		# Atribuir valores a matriz
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$parametro=resultadoSQL($consulta, $a, 'parametro');
			$matriz[$parametro]=resultadoSQL($consulta, $a, 'valor');
		}
		
		# Aplicar configura��es
		emailAplicaConfiguracao($matriz);
	}
}



# Fun��o para aplica��o de configura��es - carregamento de parametros para email
function emailAplicaConfiguracaoDominio($matriz) {

	global $conn, $tb;

	# Seleciona emails do dominio
	$consulta=buscaEmails($matriz[idDominio], 'idDominio','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Atualizar configura��es das contas de email
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$matriz[idEmail]=resultadoSQL($consulta, $a, 'id');
			emailAplicaConfiguracao($matriz);
		}
	}
}


?>
