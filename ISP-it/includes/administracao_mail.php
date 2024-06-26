<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 16/10/2003
# Ultima altera��o: 17/12/2003
#    Altera��o No.: 008
#
# Fun��o:
#    Painel - Fun��es para controle de servi�o de radius (grupos)
#
# Fun��o de configura��es

function administracaoMail($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[abrir] && !$permissao[visualizar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		if(!$acao || $acao=='config') {
			# Listar Dominios e contabilizar contas de email
			emailListarDominiosPadrao($modulo, $sub, $acao, $registro, $matriz);
			emailListarDominiosPessoasTipos($modulo, $sub, $acao, $registro, $matriz);
		}
		else {
			if($acao=='listar') emailListarContasDominios($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='adicionar') emailAdicionarContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='excluir') emailExcluirContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='alterar') emailAlterarContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='alias') emailAliasContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='aliasadicionar') emailAdicionarAliasContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='aliasexcluir') emailExcluirAliasContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='aliasalterar') emailAlterarAliasContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='forward') emailForwardContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='forwardadicionar') emailAdicionarForwardContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='forwardexcluir') emailExcluirForwardContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='emailconfig') emailConfigContasDominio($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='autoreply') emailAutoReplyContasDominio($modulo, $sub, $acao, $registro, $matriz);
		}
	}
}




# Adicionar contas de e-mail
function administracaoMailAdicionarConta($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	echo "<br>";
	$total=radiusTotalContas($registro);
	$totalEmUso=radiusTotalContasEmUso($registro);
	
	if($total <= $totalEmUso) {
		# Limite alcan�ado - nao permitir inclus�o
		$msg="ATEN��O: Limite de contas foi alcan�ado!";
		avisoNOURL("Aviso: Limite de contas alcan�ado", $msg, 400);
		administracaoMenu($modulo, $sub, '', $registro, $matriz);
	}
	else {
	
		# Form de inclusao
		if(!$matriz[bntAdicionar] || !$matriz[login] || !$matriz[senha] || !$matriz[confirma_senha] || ($matriz[senha] != $matriz[confirma_senha])) {
			# Motrar tabela de busca
			novaTabela2("[Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				
				# Valida��es
				if($matriz[senha] != $matriz[confirma_senha]) {
					$validacao="<span class=txtaviso>Senha e Confirma��o de senha n�o conferem!</span><br><br>";
				}
				
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Login: </b><br>
						<span class=normal10>Login de acesso do usu�rio</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[login] size=30 value='$matriz[login]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Senha: </b><br>
						<span class=normal10>Senha de acesso</span>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[senha] size=30 value='$matriz[senha]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Confirma��o de Senha: </b><br>
						<span class=normal10>Senha de acesso</span>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[confirma_senha] size=30 value='$matriz[confirma_senha]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Servi�o: </b><br>
						<span class=normal10>Servi�o a atribuir esta conta</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicoPlanoDial($registro,0,'idServicoPlano','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();			
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b><br>
						<span class=normal10>Status inicial para usuario</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadius('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntAdicionar]) {
			# Conferir campos
			if($matriz[login] && $matriz[senha]) {
			
				# Novo ID de Usuario Radios
				$matriz[idRadiusUsuarios]=radiusNovoIDUsuario();
				$matriz[idPessoaTipo]=$registro;
			
				if($matriz[idRadiusUsuarios]) {
					# Cadastrar em banco de dados
					$grava=radiusDBUsuario($matriz, 'incluir');
				}
				
				# Verificar inclus�o de registro
				if($grava) {
				
					# Incluir Usuario do Radius ao Servico do Plano da Pessoa
					$grava2=radiusDBUsuarioPessoaTipo($matriz, 'incluir');
					
					if(!$grava2) {
						$matriz[id]=$matriz[idRadiusUsuarios];
						radiusDBUsuario($matriz, 'excluir');
					}
					else {
						# Buscar RadiusGrupos para identifica��o de grupo do Radius da conta
						# Buscar ID do Servi�o, no ServicosPlanos
						$grupo=radiusBuscaGrupoServicoPlano($matriz[idServicoPlano]);
						
						# Incluir usuario no radius
						$contaRadius=radiusCriaConta($matriz[login], $matriz[senha], $grupo, $matriz[status]);
					}
					
					# acusar falta de parametros
					# Mensagem de aviso
					echo "<center>";
					$msg="Registro Gravado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);
					echo "</center>";
					
					administracaoMenu($modulo, $sub, 'config', $registro, $matriz);
				}
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				
				$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
			}
		}
	}
}




# Altera��o de conta
function administracaoMailAlterarConta($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	echo "<br>";
	
	$sql="
		SELECT
			$tb[RadiusUsuarios].id id,
			$tb[RadiusUsuarios].login login,
			$tb[RadiusUsuarios].idGrupo idGrupo,
			$tb[RadiusUsuarios].senha_texto,
			$tb[RadiusUsuarios].dtCadastro dtCadastro,
			$tb[RadiusUsuarios].dtAtivacao dtAtivacao,
			$tb[RadiusUsuarios].dtInativacao dtInativacao,
			$tb[RadiusUsuarios].dtCancelamento dtCancelamento,
			$tb[RadiusUsuarios].status status,
			$tb[RadiusUsuariosPessoasTipos].idPessoasTipos idPessoaTipo,
			$tb[RadiusUsuariosPessoasTipos].idServicosPlanos idServicoPlano
		FROM
			$tb[RadiusUsuariosPessoasTipos],
			$tb[RadiusUsuarios]
		WHERE
			$tb[RadiusUsuarios].id=$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios
			AND $tb[RadiusUsuariosPessoasTipos].id = $matriz[id]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		# Form de inclusao
		if(!$matriz[bntAlterar] || !$matriz[senha] || !$matriz[confirma_senha] || ($matriz[senha] != $matriz[confirma_senha])) {

			$id=resultadoSQL($consulta, 0, 'id');
			$matriz[login]=resultadoSQL($consulta, 0, 'login');
			$matriz[senha]=resultadoSQL($consulta, 0, 'senha_texto');
			$matriz[idGrupo]=resultadoSQL($consulta, 0, 'idGrupo');
			$matriz[confirma_senha]=resultadoSQL($consulta, 0, 'senha_texto');
			$matriz[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
			$matriz[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
			$matriz[dtInativacao]=resultadoSQL($consulta, 0, 'dtInativacao');
			$matriz[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
			$matriz[status]=resultadoSQL($consulta, 0, 'status');
			$matriz[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
			
			# Motrar tabela de busca
			novaTabela2("[Altera��o de Senha]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				
				# Valida��es
				if($matriz[bntAlterar] && ($matriz[senha] != $matriz[confirma_senha])) {
					$validacao="<span class=txtaviso>Senha e Confirma��o de senha n�o conferem!</span><br><br>";
				}
				
				novaLinhaTabela($corFundo, '100%');
				$texto="
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idRadiusUsuarios] value=$id>
					<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
					<input type=hidden name=matriz[login] value='$matriz[login]'>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Login: </b>";
					htmlFechaColuna();
					itemLinhaForm("<span class=txtok>$matriz[login]</span>", 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Senha: </b>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[senha] size=30 value='$matriz[senha]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Confirma��o de Senha: </b>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[confirma_senha] size=30 value='$matriz[confirma_senha]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Servi�o: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicoPlanoDial($matriz[idPessoaTipo],$matriz[idServicoPlano],'idServicoPlano','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();			
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadius($matriz[status],'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntAlterar]) {
			# Conferir campos
			if($matriz[senha] && $matriz[confirma_senha]) {
			
				# Cadastrar em banco de dados
				$grava=radiusDBUsuario($matriz, 'senha');
				
				# Verificar inclus�o de registro
				if($grava) {
				
					# Alterar senha do radius
					$gravaRadius=radiusAlterarSenha($matriz);
					
					# acusar falta de parametros
					# Mensagem de aviso
					echo "<center>";
					$msg="Registro Gravado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);
					echo "</center>";
					
					administracaoMenu($modulo, $sub, 'config', $registro, $matriz);
				}
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
			}
		}
	}
	else {
		# registro nao encontrado
		$msg="Registro n�o encontrado!!!";
		$url="?modulo=$modulo&sub=$sub&acao=config";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
}




# Exclus�o de conta
function administracaoMailExcluirConta($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	echo "<br>";
	
	$sql="
		SELECT
			$tb[RadiusUsuarios].id id,
			$tb[RadiusUsuarios].login login,
			$tb[RadiusUsuarios].senha_texto,
			$tb[RadiusUsuarios].dtCadastro dtCadastro,
			$tb[RadiusUsuarios].dtAtivacao dtAtivacao,
			$tb[RadiusUsuarios].dtInativacao dtInativacao,
			$tb[RadiusUsuarios].dtCancelamento dtCancelamento,
			$tb[RadiusUsuarios].status status,
			$tb[RadiusUsuariosPessoasTipos].idPessoasTipos idPessoaTipo,
			$tb[RadiusUsuariosPessoasTipos].idServicosPlanos idServicoPlano
		FROM
			$tb[RadiusUsuariosPessoasTipos],
			$tb[RadiusUsuarios]
		WHERE
			$tb[RadiusUsuarios].id=$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios
			AND $tb[RadiusUsuariosPessoasTipos].id = $matriz[id]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		# Form de inclusao
		if(!$matriz[bntExcluir]) {
		
			$id=resultadoSQL($consulta, 0, 'id');
			$login=resultadoSQL($consulta, 0, 'login');
			$senha=resultadoSQL($consulta, 0, 'senha_texto');
			$confirma_senha=resultadoSQL($consulta, 0, 'senha_texto');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, 0, 'dtAtivacao');
			$dtInativacao=resultadoSQL($consulta, 0, 'dtInativacao');
			$dtCancelamento=resultadoSQL($consulta, 0, 'dtCancelamento');
			$status=resultadoSQL($consulta, 0, 'status');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
			
			# Motrar tabela de busca
			novaTabela2("[Exclus�o de Conta Dial-UP]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idRadiusUsuarios] value=$id>
					<input type=hidden name=matriz[login] value='$login'>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Login: </b>";
					htmlFechaColuna();
					itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Data de Cadastro: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($dtCadastro, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(formatarData($dtAtivacao)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Ativacao: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtAtivacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if(formatarData($dtInativacao)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Inativa��o: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtInativacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if(formatarData($dtCancelamento)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Cancelamento: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtInativacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Servi�o: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicoPlanoDial($idPessoaTipo,$idServicoPlano,'idServicoPlano','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();			
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadius($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntExcluir]) {
			# Cadastrar em banco de dados
			$grava=radiusDBUsuario($matriz, 'excluir');
			
			# Verificar inclus�o de registro
			if($grava) {
				
				$grava2=radiusDBUsuarioPessoaTipo($matriz, 'excluir');
				
				# Excluir conta do radius
				if($grava2) {
					$gravaRadius=radiusExcluirConta($matriz[login], 'S');
				}
				
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<center>";
				$msg="Registro exclu�do com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "</center>";
				
				administracaoMenu($modulo, $sub, 'config', $registro, $matriz);
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
			}
		}
	}
	else {
		# registro nao encontrado
		$msg="Registro n�o encontrado!!!";
		$url="?modulo=$modulo&sub=$sub&acao=config";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
}




# Inativa��o de Conta
function administracaoMailInativarConta($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	echo "<br>";
	
	$sql="
		SELECT
			$tb[RadiusUsuarios].id id,
			$tb[RadiusUsuarios].login login,
			$tb[RadiusUsuarios].senha_texto,
			$tb[RadiusUsuarios].dtCadastro dtCadastro,
			$tb[RadiusUsuarios].dtAtivacao dtAtivacao,
			$tb[RadiusUsuarios].dtInativacao dtInativacao,
			$tb[RadiusUsuarios].dtCancelamento dtCancelamento,
			$tb[RadiusUsuarios].status status,
			$tb[RadiusUsuariosPessoasTipos].idPessoasTipos idPessoaTipo,
			$tb[RadiusUsuariosPessoasTipos].idServicosPlanos idServicoPlano
		FROM
			$tb[RadiusUsuariosPessoasTipos],
			$tb[RadiusUsuarios]
		WHERE
			$tb[RadiusUsuarios].id=$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios
			AND $tb[RadiusUsuariosPessoasTipos].id = $matriz[id]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		# Form de inclusao
		if(!$matriz[bntInativar]) {
		
			$id=resultadoSQL($consulta, 0, 'id');
			$login=resultadoSQL($consulta, 0, 'login');
			$senha=resultadoSQL($consulta, 0, 'senha_texto');
			$confirma_senha=resultadoSQL($consulta, 0, 'senha_texto');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, 0, 'dtAtivacao');
			$dtInativacao=resultadoSQL($consulta, 0, 'dtInativacao');
			$dtCancelamento=resultadoSQL($consulta, 0, 'dtCancelamento');
			$status=resultadoSQL($consulta, 0, 'status');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
			
			# Motrar tabela de busca
			novaTabela2("[Inativa��o de Conta Dial-UP]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idRadiusUsuarios] value=$id>
					<input type=hidden name=matriz[login] value='$login'>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Login: </b>";
					htmlFechaColuna();
					itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Data de Cadastro: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($dtCadastro, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(formatarData($dtInativacao)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Ativacao: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtAtivacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if(formatarData($dtInativacao)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Inativa��o: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtInativacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if(formatarData($dtCancelamento)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Cancelamento: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtInativacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Servi�o: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicoPlanoDial($idPessoaTipo,$idServicoPlano,'idServicoPlano','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();			
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadius($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntInativar] value=Inativar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntInativar]) {
			# Cadastrar em banco de dados
			$grava=radiusDBUsuario($matriz, 'inativar');
			
			# Verificar inclus�o de registro
			if($grava) {
				
				# Inativar usuario do radios
				$gravaRadius=radiusStatusConta($matriz[login], 'I');
				
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<center>";
				$msg="Registro inativado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "</center>";
				
				administracaoMenu($modulo, $sub, 'config', $registro, $matriz);
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
			}
		}
	}
	else {
		# registro nao encontrado
		$msg="Registro n�o encontrado!!!";
		$url="?modulo=$modulo&sub=$sub&acao=config";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
}


# Inativa��o de Conta
function administracaoMailAtivarConta($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	echo "<br>";
	
	$sql="
		SELECT
			$tb[RadiusUsuarios].id id,
			$tb[RadiusUsuarios].login login,
			$tb[RadiusUsuarios].senha_texto,
			$tb[RadiusUsuarios].dtCadastro dtCadastro,
			$tb[RadiusUsuarios].dtAtivacao dtAtivacao,
			$tb[RadiusUsuarios].dtInativacao dtInativacao,
			$tb[RadiusUsuarios].dtCancelamento dtCancelamento,
			$tb[RadiusUsuarios].status status,
			$tb[RadiusUsuariosPessoasTipos].idPessoasTipos idPessoaTipo,
			$tb[RadiusUsuariosPessoasTipos].idServicosPlanos idServicoPlano
		FROM
			$tb[RadiusUsuariosPessoasTipos],
			$tb[RadiusUsuarios]
		WHERE
			$tb[RadiusUsuarios].id=$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios
			AND $tb[RadiusUsuariosPessoasTipos].id = $matriz[id]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		# Form de inclusao
		if(!$matriz[bntAtivar]) {
		
			$id=resultadoSQL($consulta, 0, 'id');
			$login=resultadoSQL($consulta, 0, 'login');
			$senha=resultadoSQL($consulta, 0, 'senha_texto');
			$confirma_senha=resultadoSQL($consulta, 0, 'senha_texto');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, 0, 'dtAtivacao');
			$dtInativacao=resultadoSQL($consulta, 0, 'dtInativacao');
			$dtCancelamento=resultadoSQL($consulta, 0, 'dtCancelamento');
			$status=resultadoSQL($consulta, 0, 'status');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
			
			# Motrar tabela de busca
			novaTabela2("[Ativa��o de Conta Dial-UP]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idRadiusUsuarios] value=$id>
					<input type=hidden name=matriz[login] value='$login'>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Login: </b>";
					htmlFechaColuna();
					itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Data de Cadastro: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($dtCadastro, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(formatarData($dtAtivacao)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Ativacao: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtAtivacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if(formatarData($dtInativacao)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Inativa��o: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtInativacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if(formatarData($dtCancelamento)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Cancelamento: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtInativacao, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Servi�o: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicoPlanoDial($idPessoaTipo,$idServicoPlano,'idServicoPlano','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();			
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadius($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAtivar] value=Ativar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntAtivar]) {
			# Cadastrar em banco de dados
			$grava=radiusDBUsuario($matriz, 'ativar');
			
			# Verificar inclus�o de registro
			if($grava) {
			
				# Inativar usuario do radios
				$gravaRadius=radiusStatusConta($matriz[login], 'A');
				
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<center>";
				$msg="Registro inativado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "</center>";
				
				administracaoMenu($modulo, $sub, 'config', $registro, $matriz);
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
			}
		}
	}
	else {
		# registro nao encontrado
		$msg="Registro n�o encontrado!!!";
		$url="?modulo=$modulo&sub=$sub&acao=config";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
}


?>
