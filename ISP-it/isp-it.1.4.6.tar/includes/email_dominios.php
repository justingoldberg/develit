<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/12/2003
# Ultima alteração: 19/02/2004
#    Alteração No.: 010
#
# Função:
#    Painel - Funções para controle de usuarios radius por pessoas
# 

# Funcao para cadastro de usuarios
function emailAdicionarContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn;
	
	# Procurar dominio
	if($matriz[login]) {
		$matriz[login]=mailValidaConta($matriz[login]);
		
		$tmpNome=strtoupper($matriz[login]);
		
		# Verificar se serviço existe
		$tmpBusca=buscaEmails("upper(login)='$tmpNome' AND idDominio=$matriz[idDominio]", $campo, 'custom', 'id');
		
		# Buscar Conta Em servidor de email
		if(!$matriz[forcar_inclusao]) {
			$dominio=dadosDominio($matriz[idDominio]);
			$tmpBuscaManager=vpopmailBuscaUsuario($dominio[nome], $matriz[login], 'pw_name', 'igual', 'pw_name');
		}
		
		if( ($tmpBusca && contaConsulta($tmpBusca)>0) || ($tmpBuscaManager && contaConsulta($tmpBuscaManager)>0) ) {
			$msg="<span class=txtaviso>Conta de E-mail já existente!</span>";
			avisoNOURL("Aviso", $msg, 400);
			echo "<br>";
		}
	}
	
	# Form de inclusao
	if(!$matriz[bntConfirmar] && 
		( $matriz[bntAlterar] 	
			|| !$matriz[bntVerificar] 
			|| !$matriz[login] 
			|| !$matriz[senha_conta] 
			|| !$matriz[senha_confirma] 
			|| ($matriz[senha_conta] != $matriz[senha_confirma]) 
			|| checkContaDominio($matriz[login], $matriz[idDominio]) 
		)) 
	{
	
		# Visualizar Dominio
		verDominios($modulo, $sub, $acao, $matriz[id], $matriz);
		echo "<br>";

		if(!$matriz[bntVerificar]) $matriz[idDominio]=$matriz[id];
		if($matriz[senha_conta] != $matriz[senha_confirma]) $msg="<br><span class=txtaviso>Senha e confirmação de senha devem ser iguais!</span><br><br>";
		elseif(strlen($matriz[login])<=1) $msg="<br><span class=txtaviso>Login precisa conter o mínimo de 2 caracteres</span><br><br>";
		elseif(checkContaDominio($matriz[login], $matriz[idDominio])) $msg="<br><span class=txtaviso>Conta já cadastrada neste domínio!</span><br><br>";
		
		# Motrar tabela de busca
		novaTabela2("[Adicionar]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, "$registro:$matriz[id]");
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro:$matriz[id]>
				&nbsp;$msg";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Conta: </b><br>
					<span class=normal10>Conta de e-mail</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[login] size=30 value='$matriz[login]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Senha: </b><br>
					<span class=normal10>Senha da conta</span>";
				htmlFechaColuna();
				$texto="<input type=password name=matriz[senha_conta] size=30>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Confirmação: </b><br>
					<span class=normal10>Confirmação de senha</span>";
				htmlFechaColuna();
				$texto="<input type=password name=matriz[senha_confirma] size=30>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Domínio: </b><br>
					<span class=normal10>Selecione o Domínio</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectDominioEmail($matriz[idDominio],'idDominio','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status do domínio</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusEmails($matriz[status],'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Forçar inclusão: </b>";
					htmlFechaColuna();
					$obs="&nbsp;<span class=txtaviso>(Não criar no servidor)</span>";
					$texto="<input type=checkbox name=matriz[forcar_inclusao] value='S'>";
					itemLinhaForm($texto.$obs, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntVerificar] value=Verificar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntVerificar]) {
		# Motrar tabela de busca
		novaTabela2("[Confirmação]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro:$matriz[id]>
				<input type=hidden name=matriz[login] value=$matriz[login]>
				<input type=hidden name=matriz[senha_conta] value=$matriz[senha_conta]>
				<input type=hidden name=matriz[senha_confirma] value=$matriz[senha_confirma]>
				<input type=hidden name=matriz[idDominio] value=$matriz[idDominio]>
				<input type=hidden name=matriz[status] value=$matriz[status]>
				<input type=hidden name=matriz[forcar_inclusao] value=$matriz[forcar_inclusao]>
				&nbsp;$msg";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Conta: </b>";
				htmlFechaColuna();
				itemLinhaForm($matriz[login], 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Senha: </b>";
				htmlFechaColuna();
				itemLinhaForm($matriz[senha_conta], 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Domínio: </b>";
				htmlFechaColuna();
				itemLinhaForm(formSelectDominioEmail($matriz[idDominio],'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusEmails($matriz[status],'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Forçar inclusão: </b>";
					htmlFechaColuna();
					if(!$matriz[forcar_inclusao]) $matriz[forcar_inclusao]='N';
					itemLinhaForm(formSelectSimNao($matriz[forcar_inclusao],'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				if(!$msg) {
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit2>";
					$texto.=" <input type=submit name=matriz[bntConfirmar] value=Confirmar class=submit>";
				}
				else {
					if($matriz[forcar_inclusao]=='S') {
						$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit2>";
						$texto.=" <input type=submit name=matriz[bntConfirmar] value=Confirmar class=submit>";
					}
					else {
						$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit2>";
					}
				}
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	elseif($matriz[bntConfirmar]) {
	
		/*
		  Antes de criar a conta de email, verificar quantas contas já existem
		  cadastradas para o dominio informado. Caso não existe nenhuma, executar
		  criação de dominio antes de criação de conta de email no manager.
		*/
		$dominio=dadosDominio($matriz[idDominio]);
		$matriz[dominio]=$dominio[nome];
		
		$consulta=buscaEmails($matriz[idDominio], 'idDominio','igual','id');
		if(!$consulta || contaConsulta($consulta)==0 || ($consulta && contaConsulta($consulta)>0 && $matriz[forcar_inclusao]=='S') ) 		{
			# Criar dominio antes de criar conta de email
			$matriz[dominio]=$dominio[nome];
			$gravaManager=managerComando($matriz, 'dominioadicionar');
		}
	
		# Cadastrar em banco de dados
		$matriz[idEmail]=buscaIDNovoEmail();
		if($dominio[padrao]!='S') {
			$consulta=buscaDominiosServicosPlanos($matriz[idDominio],'idDominio','igual','id');
			$matriz[idServicosPlanos]=resultadoSQL($consulta,0,'idServicosPlanos');
		}
		else {
			# Pegar IDServicoPlano do Servico que contiver emails livres
			$matriz[idServicosPlanos]=0;
			## POPO - ALTERAR
		}

		$grava=dbEmail($matriz, 'incluir');
		
		# Verificar inclusão de registro
		if($grava) {
			
			$matriz[id]=$matriz[idEmail];
			
			# Criar conta no Manager
			if(!$matriz[forcar_inclusao]) {
				$gravaManager=managerComando($matriz, 'emailadicionar');
			}
			
			if($gravaManager || !$matriz[forcar_inclusao]) {
				# Adicionar configuração da conta de email em virtude do serviço
				emailAdicionaConfiguracao($matriz[idEmail], $matriz[idDominio]);
				
				# Adicionar configuração ao email - manager
				if(!$matriz[forcar_inclusao])
					emailAplicaConfiguracaoEmail($matriz);
			
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, '100%');
			}
			else {
				# Excluir conta de email
				# Cadastrar em banco de dados
				$grava=dbEmail($matriz, 'excluir');
			}
			
			echo "<br>";
			$matriz[id]=$matriz[idDominio];
			emailListarContasDominios($modulo, $sub, 'listar', "$matriz[idDominio]", $matriz);
		}
	}
} # fecha funcao de Adicionar Contas de Email no Dominio



# Funcao para cadastro de usuarios
function emailExcluirContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Procurar detalhes sobre Email
	$consulta=buscaEmails($matriz[id],'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Dados do email
		$id=resultadoSQL($consulta, 0, 'id');
		$idDominio=resultadoSQL($consulta, 0, 'idDominio');
		$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$idDominio=resultadoSQL($consulta, 0, 'idDominio');
		$login=resultadoSQL($consulta, 0, 'login');
		$status=resultadoSQL($consulta, 0, 'status');
		
		if(!$matriz[bntExcluir]) {
			# Visualizar Dominio
			verDominios($modulo, $sub, $acao, $idDominio, $matriz);
			echo "<br>";
		
		
			# Motrar tabela de busca
			novaTabela2("[Excluir]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$registro:$idDominio");
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$matriz[idPessoaTipo]:$id>
					<input type=hidden name=matriz[login] value=$login>
					<input type=hidden name=matriz[idDominio] value=$idDominio>
					<input type=hidden name=matriz[id] value=$id>
					&nbsp;$msg";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Conta: </b>";
					htmlFechaColuna();
					itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Domínio: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectDominioEmail($idDominio,'idDominio','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusEmails($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		elseif($matriz[bntExcluir]) {	
			# Cadastrar em banco de dados
			$grava=dbEmail($matriz, 'excluir');
			
			# Verificar inclusão de registro
			if($grava) {
				
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# remover conta no Manager
				$dominio=dadosDominio($matriz[idDominio]);
				$matriz[dominio]=$dominio[nome];
				$gravaManager=managerComando($matriz, 'emailremover');				
				
				# Excluir Configurações
				dbEmailConfig($matriz, 'excluiremail');
				
				# Excluir Alias
				# Buscar alias para remoção
				$consultaAlias=buscaEmailAlias($matriz[id],'idEmail','igual','id');
				if($consultaAlias && contaConsulta($consultaAlias)>0) {
					# Excluir alias do email
					for($a=0;$a<contaConsulta($consultaAlias);$a++) {
						$matriz[alias]=resultadoSQL($consultaAlias, $a, 'alias');
						$gravaManager=managerComando($matriz, 'emailaliasremover');
					}
						
					dbEmailAlias($matriz, 'excluiremail');
				}

				# Excluir Forward
				dbEmailForward($matriz, 'excluir');
				
				# Excluir Auto Resposta
				dbEmailAutoReply($matriz, 'excluiremail');
				
				echo "<br>";
				$matriz[id]=$matriz[idDominio];
				emailListarContasDominios($modulo, $sub, 'listar', "$matriz[idPessoaTipo]:$matriz[idDominio]", $matriz);
			}
		}
	}
	else {
		# nao encontrado
		# Mensagem de aviso
		$msg="E-mail não encontrado!";
		avisoNOURL("Aviso", $msg, 400);
	}
} # fecha funcao de inclusao de grupos


# Funcao para cadastro de usuarios
function emailAlterarContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Procurar detalhes sobre Email
	$consulta=buscaEmails($matriz[id],'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Dados do email
		$id=resultadoSQL($consulta, 0, 'id');
		$idDominio=resultadoSQL($consulta, 0, 'idDominio');
		$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$idDominio=resultadoSQL($consulta, 0, 'idDominio');
		$login=resultadoSQL($consulta, 0, 'login');
		$status=resultadoSQL($consulta, 0, 'status');
		
		if(!$matriz[bntAlterar] || $matriz[senha_conta] != $matriz[senha_confirma]) {
			# Visualizar Dominio
			verDominios($modulo, $sub, $acao, $idDominio, $matriz);
			echo "<br>";
		
			# Motrar tabela de busca
			novaTabela2("[Alteração de Senha]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$registro:$idDominio");
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$matriz[idPessoaTipo]:$id>
					<input type=hidden name=matriz[idDominio] value=$idDominio>
					<input type=hidden name=matriz[login] value=$login>
					<input type=hidden name=matriz[id] value=$id>
					&nbsp;$msg";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Conta: </b>";
					htmlFechaColuna();
					itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Domínio: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectDominioEmail($idDominio,'idDominio','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Senha: </b>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[senha_conta] size=30'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Confirmação: </b>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[senha_confirma] size=30'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusEmails($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		elseif($matriz[bntAlterar]) {	
			# Cadastrar em banco de dados
			$grava=dbEmail($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				
				# Alteração de senha
				$dominio=dadosDominio($matriz[idDominio]);
				$matriz[dominio]=$dominio[nome];
				$gravaManager=managerComando($matriz, 'emailsenha');
				
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Alterado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				$matriz[id]=$matriz[idDominio];
				emailListarContasDominios($modulo, $sub, 'listar', "$matriz[idPessoaTipo]:$matriz[idDominio]", $matriz);
			}
		}
	}
	else {
		# nao encontrado
		# Mensagem de aviso
		$msg="Email não encontrado!";
		avisoNOURL("Aviso", $msg, 400);
	}
} # fecha funcao de inclusao de grupos


# Função para listagem de contas  Radius por Pessoa Tipo
function emailListarDominiosPadrao($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $limite, $html;

	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	
	if($matriz[txtProcurar]) {
		$sqlADD=" AND ( 
			$tb[Dominios].nome like '%$matriz[txtProcurar]%'
			OR $tb[Dominios].descricao like '%$matriz[txtProcurar]%'
		)";
	}

	$sql="
		SELECT 
			$tb[Dominios].nome, 
			$tb[Dominios].id idDominio
		FROM 
			$tb[Dominios]
		WHERE
			$tb[Dominios].padrao='S'
			$sqlADD
		ORDER BY
			$tb[Dominios].nome
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		novaTabela("Dominios Padrão", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'right', $corFundo, 3, 'tabfundo1');
				novaTabela2SH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					novaLinhaTabela($corFundo, '100%');
						$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=config>
						<input type=hidden name=registro value=$idPessoaTipo>
						<b>Procurar por:</b> <input type=text name=matriz[txtProcurar] size=25 value='$matriz[txtProcurar]'>
						<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
						itemLinhaForm($texto, 'center','middle', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();		
			htmlFechaColuna();
			fechaLinhaTabela();
		
			if($consulta && contaConsulta($consulta)>0) {
			
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("Domínio", 'center', '45%', 'tabfundo0');
					itemLinhaTabela('Contas', 'center', '15%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
				fechaLinhaTabela();
			
				for($a=0;$a<contaConsulta($consulta) && $a < $limite[lista][dominios];$a++) {
				
					$idDominio=resultadoSQL($consulta, $a, 'idDominio');
					$nome=resultadoSQL($consulta, $a, 'nome');
					$totalContasDominio=emailTotalContasDominioPadrao($idDominio, $idPessoaTipo);
					$totalContasEmUso=emailTotalContasEmUsoDominio($idDominio, $idPessoaTipo);
					
					if($status=='A') $class='txtok';
					elseif($status=='I') $class='txtaviso';
					elseif($status=='T') $class='txttrial';
					else $class='bold10';
					
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=listar&registro=$idPessoaTipo:$idDominio>Contas</a>",'mail');
					if($totalContasDominio > $totalContasEmUso) $opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=adicionar&registro=$idPessoaTipo:$idDominio>Nova Conta</a>",'incluir');
					
					novaLinhaTabela($corFundo, '100%');
						$nome="<img src=".$html[imagem][dominio]." border=0>$nome";
						itemLinhaTabela($nome, 'left', '45%', 'normal10');
						itemLinhaTabela("$totalContasEmUso / $totalContasDominio", 'center', '15%', 'txtaviso');
						itemLinhaTabela($opcoes, 'left', '40%', 'normal8');
					fechaLinhaTabela();
				}
			}
			else {
				$texto="<span class=txtaviso>Não existem domínios cadastrados!</span>";
				itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
			}
		fechaTabela();
		
		echo "<br>";
	}
}



# Função para listagem de contas  Radius por Pessoa Tipo
function emailListarDominiosPessoasTipos($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $limite, $html;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	
	if($matriz[txtProcurar]) {
		$sqlADD=" AND ( 
			$tb[Dominios].nome like '%$matriz[txtProcurar]%'
			OR $tb[Dominios].descricao like '%$matriz[txtProcurar]%'
		)";
	}

	if($idPessoaTipo) {
		$sql="
			SELECT 
				$tb[Dominios].nome, 
				$tb[Dominios].id idDominio, 
				$tb[DominiosServicosPlanos].idPessoasTipos 
			FROM 
				$tb[Dominios], 
				$tb[DominiosServicosPlanos]
			WHERE
				$tb[Dominios].id=$tb[DominiosServicosPlanos].idDominio 
				AND $tb[DominiosServicosPlanos].idPessoasTipos=$idPessoaTipo
				$sqlADD
			ORDER BY
				$tb[Dominios].nome
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		novaTabela("Dominios", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'right', $corFundo, 3, 'tabfundo1');
				novaTabela2SH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					novaLinhaTabela($corFundo, '100%');
						$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=config>
						<input type=hidden name=registro value=$idPessoaTipo>
						<b>Procurar por:</b> <input type=text name=matriz[txtProcurar] size=25 value='$matriz[txtProcurar]'>
						<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
						itemLinhaForm($texto, 'center','middle', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();		
			htmlFechaColuna();
			fechaLinhaTabela();
		
			if($consulta && contaConsulta($consulta)>0) {
			
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("Domínio", 'center', '45%', 'tabfundo0');
					itemLinhaTabela('Contas', 'center', '15%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
				fechaLinhaTabela();
			
				for($a=0;$a<contaConsulta($consulta) && $a < $limite[lista][dominios];$a++) {
				
					$idDominio=resultadoSQL($consulta, $a, 'idDominio');
					$nome=resultadoSQL($consulta, $a, 'nome');
					
					$totalContasDominio=emailTotalContasDominio($idDominio);
					$totalContasEmUso=emailTotalContasEmUsoDominio($idDominio, $idPessoaTipo);

					
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=listar&registro=$idPessoaTipo:$idDominio>Contas</a>",'mail');
					if($totalContasEmUso < $totalContasDominio) $opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=adicionar&registro=$idPessoaTipo:$idDominio>Nova Conta</a>",'incluir');
					
					# Ativação / Inativação
					if($status=='A') {
						$class='txtok';
					}
					elseif($status=='I') {
						$class='txtaviso';
					}
					else $class='bold10';

					novaLinhaTabela($corFundo, '100%');
						$nome="<img src=".$html[imagem][dominio]." border=0>$nome";
						itemLinhaTabela($nome, 'left', '45%', 'normal10');
						itemLinhaTabela("$totalContasEmUso / $totalContasDominio", 'center', '15%', 'txtaviso');
						itemLinhaTabela($opcoes, 'left', '40%', 'normal8');
					fechaLinhaTabela();
				}
			}
			else {
				$texto="<span class=txtaviso>Não existem domínios cadastrados!</span>";
				itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
			}
		fechaTabela();
	}
}



# Contar contas em uso
function emailTotalContasEmUsoPessoaTipo($idPessoaTipo) {

	global $conn, $tb;

	if($idPessoaTipo) {
	
		$sql="
			SELECT
				$tb[Emails].login, 
				$tb[Dominios].nome
			FROM
				$tb[Dominios], 
				$tb[Emails] 
			WHERE 
				$tb[Emails].idDominio = $tb[Dominios].id 
				AND $tb[Emails].idPessoatipo=$idPessoaTipo
			ORDER BY 
				$tb[Dominios].nome, 
				$tb[Emails].login
		";
			
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) $retorno=contaConsulta($consulta);
		else $retorno=0;
	}
	
	return($retorno);
}



# Contar contas em uso
function emailTotalContasEmUsoDominio($idDominio, $idPessoaTipo) {

	global $conn, $tb;
	
	if($idDominio) {
	
		# Consultar se dominio é um dominio padrão
		$dominio=dadosDominio($idDominio);
		
		if($dominio[padrao]=='N') {
			$sql="
				SELECT
					COUNT($tb[Emails].id) qtde,
					$tb[Dominios].padrao padrao
				FROM
					$tb[Dominios], 
					$tb[Emails] 
				WHERE 
					$tb[Emails].idDominio = $tb[Dominios].id 
					AND $tb[Dominios].id = $idDominio
				GROUP BY
					$tb[Dominios].id
			";
		}
		elseif($dominio[padrao]=='S') {
			$sql="
				SELECT
					COUNT($tb[Emails].id) qtde,
					$tb[Dominios].padrao padrao
				FROM
					$tb[Dominios], 
					$tb[Emails] 
				WHERE 
					$tb[Emails].idDominio = $tb[Dominios].id 
					AND $tb[Emails].idPessoaTipo = $idPessoaTipo 
					AND $tb[Dominios].id = $idDominio
				GROUP BY
					$tb[Dominios].id
			";
		}
				
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) $retorno=resultadoSQL($consulta, 0, 'qtde');
		else $retorno=0;
	}
	else $retorno=0;
	
	return($retorno);
}



# Contar contas em uso
function emailTotalContasDominio($idDominio) {

	global $conn, $tb;

	if($idDominio) {
	
		$sql="
			SELECT
				$tb[DominiosParametros].valor, 
				$tb[Modulos].modulo, 
				$tb[Parametros].parametro 
			FROM
				$tb[Modulos], 
				$tb[Parametros], 
				$tb[Dominios], 
				$tb[DominiosParametros]
			WHERE 
				$tb[Dominios].id=$tb[DominiosParametros].idDominio 
				AND $tb[DominiosParametros].idParametro=$tb[Parametros].id 
				AND $tb[DominiosParametros].idModulo=$tb[Modulos].id 
				AND $tb[Parametros].parametro='qtde'
				AND $tb[Dominios].id=$idDominio
		";	
		
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) $retorno=resultadoSQL($consulta, 0, 'valor');
		else $retorno=0;
	}
	else $retorno=0;
	
	return($retorno);
}


# Contar contas em uso
function emailTotalContasDominioPadrao($idDominio, $idPessoaTipo) {

	global $conn, $tb;

	if($idDominio) {
	
		$sql="
			SELECT
				$tb[ServicosPlanos].id idServicoPlano, 
				$tb[Modulos].modulo, 
				$tb[Parametros].parametro, 
				$tb[ServicosParametros].valor 
			FROM
				$tb[PessoasTipos], 
				$tb[PlanosPessoas], 
				$tb[Servicos], 
				$tb[ServicosPlanos], 
				$tb[ServicosParametros], 
				$tb[StatusServicos], 
				$tb[Parametros], 
				$tb[ParametrosModulos], 
				$tb[Modulos]
			WHERE 
				$tb[PessoasTipos].id=$tb[PlanosPessoas].idPessoaTipo 
				and $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano 
				and $tb[ServicosPlanos].idServico=$tb[Servicos].id 
				and $tb[ServicosParametros].idServico = $tb[Servicos].id 
				and $tb[ServicosParametros].idParametro = $tb[Parametros].id 
				and $tb[Parametros].id = $tb[ParametrosModulos].idParametro  
				and $tb[ParametrosModulos].idModulo = $tb[Modulos].id 
				and $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus 
				and $tb[Modulos].modulo='mail'  
				and $tb[Parametros].parametro='qtde' 
				and $tb[PlanosPessoas].status='A' 
				and ( $tb[StatusServicos].status='A' OR $tb[StatusServicos].status='N')
				and $tb[PessoasTipos].id=$idPessoaTipo 
		";	
		
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			# Verificar os serviços que possuem apenas configuração de email
			# sem configuração de dominio
			$retorno=0;
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$idServicoPlano=resultadoSQL($consulta, $a, 'idServicoPlano');
				$valor=resultadoSQL($consulta, $a, 'valor');
				
				# Buscar configurações de dominio
				$sql="
					SELECT
						$tb[ServicosPlanos].id idServicoPlano, 
						$tb[Modulos].modulo, 
						$tb[Parametros].parametro, 
						$tb[ServicosParametros].valor 
					FROM
						$tb[PessoasTipos], 
						$tb[PlanosPessoas], 
						$tb[Servicos], 
						$tb[ServicosPlanos], 
						$tb[ServicosParametros], 
						$tb[StatusServicos], 
						$tb[Parametros], 
						$tb[ParametrosModulos], 
						$tb[Modulos]
					WHERE 
						$tb[PessoasTipos].id=$tb[PlanosPessoas].idPessoaTipo 
						AND $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano 
						AND $tb[ServicosPlanos].idServico=$tb[Servicos].id 
						AND $tb[ServicosParametros].idServico = $tb[Servicos].id 
						AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
						AND $tb[Parametros].id = $tb[ParametrosModulos].idParametro  
						AND $tb[ParametrosModulos].idModulo = $tb[Modulos].id 
						AND $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus 
						AND $tb[Modulos].modulo='dominio'  
						AND $tb[PlanosPessoas].status='A' 
						AND $tb[StatusServicos].status='A'
						AND $tb[ServicosPlanos].id=$idServicoPlano
				";
				
				$consultaConfig=consultaSQL($sql, $conn);
				
				if($consultaConfig && contaConsulta($consultaConfig)>0) ;# ignorar
				else $retorno+=resultadoSQL($consulta, $a, 'valor');
				
			}
		}
		else $retorno=0;
	}
	else $retorno=0;
	
	return($retorno);
}


# Função para listagem de contas  Radius por Pessoa Tipo
function emailListarContasDominios($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $limite;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	$idDominio=$matriz[id];
	
	$dominio=dadosDominio($idDominio);
	
	# Visualizar Dominio
	verDominios($modulo, $sub, $acao, $matriz[id], $matriz);
	echo "<br>";
	
	if($matriz[txtProcurar]) {
		$sqlADD=" AND ( 
			$tb[Emails].login like '%$matriz[txtProcurar]%'
		)";
	}

	if($idPessoaTipo) {
		if($dominio[padrao]=='N') {
			$sql="
				SELECT 
					$tb[Emails].id id,
					$tb[Emails].login,
					$tb[Emails].status,
					$tb[Dominios].nome, 
					$tb[Dominios].id idDominio, 
					$tb[DominiosServicosPlanos].idPessoasTipos 
				FROM 
					$tb[Emails], 
					$tb[Dominios], 
					$tb[DominiosServicosPlanos]
				WHERE
					$tb[Emails].idDominio=$tb[Dominios].id
					AND $tb[Dominios].id=$tb[DominiosServicosPlanos].idDominio 
					AND $tb[Dominios].id='$matriz[id]'
					$sqlADD
				ORDER BY
					$tb[Emails].login
			";
		}
		else {
			$sql="
				SELECT 
					$tb[Emails].id id,
					$tb[Emails].login,
					$tb[Emails].status,
					$tb[Dominios].nome, 
					$tb[Dominios].id idDominio
				FROM 
					$tb[Emails], 
					$tb[Dominios]
				WHERE
					$tb[Emails].idDominio=$tb[Dominios].id
					AND $tb[Dominios].id=$matriz[id]
					AND $tb[Emails].idPessoaTipo=$idPessoaTipo
					$sqlADD
				ORDER BY
					$tb[Emails].login
			";
		}
		
		$consulta=consultaSQL($sql, $conn);
		
		novaTabela("Contas de Email", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'right', $corFundo, 3, 'tabfundo1');
				novaTabela2SH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
					novaLinhaTabela($corFundo, '100%');
						$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=listar>
						<input type=hidden name=registro value=$idPessoaTipo:$idDominio>
						<b>Procurar por:</b> <input type=text name=matriz[txtProcurar] size=25 value='$matriz[txtProcurar]'>
						<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
						itemLinhaForm($texto, 'center','middle', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();		
			htmlFechaColuna();
			fechaLinhaTabela();
			
			if($dominio[padrao]=='N') $totalContas=emailTotalContasDominio($dominio[id]);
			elseif($dominio[padrao]=='S') $totalContas=emailTotalContasDominioPadrao($dominio[id], $idPessoaTipo);
			$totalContasEmUso=emailTotalContasEmUsoDominio($dominio[id], $idPessoaTipo);
		
			$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=config&registro=$idPessoaTipo>Listar Dominios</a>",'dominio');
			if( $totalContas > $totalContasEmUso) {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=adicionar&registro=$idPessoaTipo:$idDominio>Adicionar</a>",'incluir');
			}
			itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
			
			if($consulta && contaConsulta($consulta)>0) {
				
				# Paginador
				
				$matriz[registro] = $registro.":".$idDominio;
				$urlADD = '&matriz[id]='.$matriz[id];
		
				paginador2($consulta, contaConsulta($consulta), $limite[lista][dominios], $matriz, 'normal', 6, $urlADD);
				

				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("Conta", 'center', '30%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '70%', 'tabfundo0');
				fechaLinhaTabela();
			
				# Setar registro inicial
				if(!$matriz[pagina]) {
					$i=0;
				}
				elseif($matriz[pagina] && is_numeric($matriz[pagina]) ) {
					$i=$matriz[pagina];
				}
				else {
					$i=0;
				}
				
				$limite=$i+$limite[lista][dominios];
//				$i = 0;

				for($a=$i;$a<contaConsulta($consulta) && $a < $limite; $a++) {
				
					$id=resultadoSQL($consulta, $a, 'id');
					$idDominio=resultadoSQL($consulta, $a, 'idDominio');
					$login=resultadoSQL($consulta, $a, 'login');
					$nome=resultadoSQL($consulta, $a, 'nome');
					$status=resultadoSQL($consulta, $a, 'status');
					
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=alterar&registro=$idPessoaTipo:$id>Senha</a>",'senha');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=excluir&registro=$idPessoaTipo:$id>Excluir</a>",'excluir');
					if($dominio[padrao] != "S") {
						# Buscar alias configurados
						# caso encontrar alias configurados, marcar link em negrito
						$consultaAlias=buscaEmailAlias($id, 'idEmail','igual','idEmail');
						if($consultaAlias && contaConsulta($consultaAlias)>0) $class='bold8';
						else $class='';
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=alias&registro=$idPessoaTipo:$id class=$class>Alias</a>",'alias');
					}
					
					# Buscar forward configurados
					# caso encontrar marcar link em negrito
					$tmpConsulta=buscaEmailForward($id, 'idEmail','igual','idEmail');
					if($tmpConsulta && contaConsulta($tmpConsulta)>0) $class='bold8';
					else $class='';
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=forward&registro=$idPessoaTipo:$id class=$class>Forward</a>",'forward');

					# Buscar forward configurados
					# caso encontrar marcar link em negrito
					$tmpConsulta=buscaEmailAutoReply($id, 'idEmail','igual','idEmail');
					if($tmpConsulta && contaConsulta($tmpConsulta)>0) $class='bold8';
					else $class='';
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=autoreply&registro=$idPessoaTipo:$id class=$class>Auto Resp</a>",'autoresposta');

					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=emailconfig&registro=$idPessoaTipo:$id>Config</a>",'emailconfig');
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($login, 'center', '30%', 'normal10');
						itemLinhaTabela($opcoes, 'left nowrap', '70%', 'normal8');
					fechaLinhaTabela();
				}
			}
			else {
				$texto="<span class=txtaviso>Não existem contas de email cadastradas!</span>";
				itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
			}
		fechaTabela();
	}
}


# Função para procurar conta no dominio
function checkContaDominio($conta, $idDominio) {

	$consulta=buscaEmails("login='$conta' AND idDominio=$idDominio", '','custom','id');
	
	if(strlen($conta)<=1) return(1);
	elseif($consulta && contaConsulta($consulta)>0 ) return(1);
	else return(0);
}



# Função para buscar ID do dominio do email
function dadosEmail($idEmail) {

	$consulta=buscaEmails($idEmail, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$retorno[idDominio]=resultadoSQL($consulta, 0, 'idDominio');
		$retorno[login]=resultadoSQL($consulta, 0, 'login');
		$retorno[senhaTexto]=resultadoSQL($consulta, 0, 'senhaTexto');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
	}
	
	
	return($retorno);
}


# Função para procura de serviço
function procurarEmailDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $sessCadastro;
	
	# Tipo de Pessoa
	novaTabela2("[Procurar E-mails]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
		novaLinhaTabela($corFundo, '100%');
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar>
			<b>Procurar por:</b>&nbsp;<input type=text name=matriz[txtProcurarEmail] size=40>";
			
			$texto.="&nbsp;<input type=submit name=matriz[bntProcurarEmail] value=Procurar class=submit>";
			itemLinhaForm($texto, 'center','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso botão procurar seja pressionado
	if($matriz[txtProcurarEmail]) {
	
		# em caso de informar @, quebrar string
		if(strstr($matriz[txtProcurarEmail],"@")) {
			$tmpBusca=explode("@",$matriz[txtProcurarEmail]);
			
			$txtDominio=$tmpBusca[1];
			$txtConta=$tmpBusca[0];
			
			# buscar registros
			$sql="
				SELECT 
					$tb[Emails].id, 
					$tb[Emails].idPessoaTipo, 
					$tb[Emails].idDominio, 
					$tb[Emails].login, 
					$tb[Dominios].nome nomeDominio 
				FROM
					$tb[Emails], 
					$tb[Dominios] 
				WHERE
					$tb[Emails].idDominio=$tb[Dominios].id
					AND (
						UPPER($tb[Emails].login) like '%$matriz[txtProcurarEmail]%' 
						OR UPPER($tb[Emails].login) like '%$txtConta%' 
						OR UPPER($tb[Dominios].nome) like '%$matriz[txtProcurarEmail]%' 
						OR UPPER($tb[Dominios].nome) like '%$txtDominio%' 
						OR UPPER($tb[Dominios].nome) like '%$txtConta%' 
					)
				
			";			
		}
		else {
		
			#buscar registros
			$sql="
				SELECT 
					$tb[Emails].id, 
					$tb[Emails].idPessoaTipo, 
					$tb[Emails].idDominio, 
					$tb[Emails].login, 
					$tb[Dominios].nome nomeDominio 
				FROM
					$tb[Emails], 
					$tb[Dominios] 
				WHERE
					$tb[Emails].idDominio=$tb[Dominios].id
					AND (UPPER($tb[Emails].login) like '%$matriz[txtProcurarEmail]%')
				
			";
		}
				
		$consulta=consultaSQL($sql, $conn);

		echo "<br>";
		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados e-mails cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('E-mails encontrados procurando por ('.$matriz[txtProcurarEmail].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 3, 'txtaviso');

			# Paginador
			$urlADD="&matriz[txtProcurarEmail]=".$matriz[txtProcurarEmail];
			paginador($consulta, contaConsulta($consulta), $limite[lista][pessoas], $registro, 'normal', 3, $urlADD);

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('E-mail', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Nome', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
			fechaLinhaTabela();

			# Setar registro inicial
			if(!$registro) {
				$i=0;
			}
			elseif($registro && is_numeric($registro) ) {
				$i=$registro;
			}
			else {
				$i=0;
			}

			$limite=$i+$limite[lista][pessoas];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
				$login=resultadoSQL($consulta, $i, 'login');
				$idDominio=resultadoSQL($consulta, $i, 'idDominio');
				$tmpCheckPessoa=checkIDTipoPessoa($idTipo);
				
				$dominio=dadosDominio($idDominio);
				
				# Buscar dados da Pessoa
				$consultaPessoa=buscaPessoas($idPessoaTipo, "$tb[PessoasTipos].id",'igual','id');
				if($consultaPessoa && contaConsulta($consultaPessoa)>0) {
					$nomePessoa=resultadoSQL($consultaPessoa, 0, 'nome');
				}
				
				$opcoes=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=ver&registro=$idPessoaTipo:$id>Cadastro</a>",'ver');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$idPessoaTipo>Planos</a>",'planos');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$idPessoaTipo>Financeiro</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=limites&registro=$idPessoaTipo>Administração</a>",'config');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("$login@$dominio[nome]", 'left', '30%', 'normal10');
					itemLinhaTabela("$nomePessoa ", 'left nowrap', '30%', 'normal10');
					itemLinhaTabela($opcoes, 'left', '40%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
		
		# Zerar pesquisa
		$sessCadastro[txtProcurar]='';
		$sessCadastro[bntProcurar]=0;
		fechaTabela();
	} # fecha botão procurar
} #fecha funcao de  procurar 


?>
