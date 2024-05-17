<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 21/08/2003
# Ultima altera��o: 04/12/2003
#    Altera��o No.: 003
#
# Fun��o:
#    Painel - Fun��es para cadastro de tickets


# Fun��o para cadastro
function perfil($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Mostrar menu
	novaTabela2("[Perfil do Usu�rio]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('55%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<br><img src=".$html[imagem][perfilg]." border=0 align=left>
				<b class=bold>Perfil</b><br>
				<br><span class=normal10>Perfil e prefer�ncia do usu�rio para utiliza��o do
				$configAppName.</span>";
			htmlFechaColuna();
			htmlAbreColuna('5%', 'left', $corFundo, 0, 'normal');
				echo "&nbsp;";
			htmlFechaColuna();									
			$texto=htmlMontaOpcao("<br>Alterar Senha", 'chave');
			itemLinha($texto, "?modulo=perfil&acao=senha", 'center', $corFundo, 0, 'normal');
		fechaLinhaTabela();
	fechaTabela();

	if(!$sub) {
		# Mostrar Status caso n�o seja informada a a��o
		# Inclus�o
		if($acao=="senha") {
			perfilSenha($modulo, $sub, $acao, $registro, $matriz);
		}
	}
} #fecha menu principal 


# Altera��o de senha
function perfilSenha($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Identificar se usu�rio � o mesmo que est� sendo trocada a senha
	$idUsuario=buscaIDUsuario($sessLogin[login],'login','igual','id');

	echo "<br>";
	
	if($registro == $idUsuario || !$registro && idUsuario) {
		
		# Form de inclusao
		if(!$matriz[bntAlterar]) {
			# Buscar informa��es do usuario
			$consulta=buscaUsuarios($idUsuario, 'id','igual','id');
			
			#verificar consulta
			if($consulta && contaConsulta($consulta)>0) {
				# receber valores
				$id=resultadoSQL($consulta, 0, 'id');
				$login=resultadoSQL($consulta, 0, 'login');
				$senha=resultadoSQL($consulta, 0, 'senha');
				$status=resultadoSQL($consulta, 0, 'status');
			
				# Motrar tabela de busca
				novaTabela2("[Altera��o de Senha do Usu�rio]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					menuOpcAdicional($modulo, $sub, $acao, $idUsuario);				
					#fim das opcoes adicionais
					novaLinhaTabela($corFundo, '100%');
					$texto="			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=matriz[id] value=$id>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro>
						&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold>Login: </b><br>
							<span class=normal10>Login de acesso do usu�rio</span>";
						htmlFechaColuna();					
						itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold>Senha: </b><br>
							<span class=normal10>Senha de acesso do usu�rio</span>";
						htmlFechaColuna();
						$texto="<input type=password name=matriz[senha] size=20>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold>Confirma��o de Senha: </b><br>
							<span class=normal10>Confirma��o de de Senha de acesso do usu�rio</span>";
						htmlFechaColuna();
						$texto="<input type=password name=matriz[confirma_senha] size=20>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAlterar] value=Alterar>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			}
			# registro nao encontrado
			else {
				# Mensagem de aviso
				$msg="Registro n�o foi encontrado!";
				$url="?modulo=$modulo&sub=$sub&acao=listar";
				aviso("Aviso", $msg, $url, 760);
			}
		} #fecha form
		elseif($matriz[bntAlterar]) {
			# Conferir campos
			if($matriz[senha] && $matriz[confirma_senha]) {
				# conferir senha e confirma��o
				if( $matriz[senha] != $matriz[confirma_senha]){
					# Erro - campo inv�lido
					# Mensagem de aviso
					$msg="Senha informada n�o � igual a confirma��o de senha. Tente novamente";
					$url="?modulo=$modulo&sub=$sub&acao=$acao";
					aviso("Aviso: Dados incorretos", $msg, $url, 760);
				}
				# continuar - campos OK
				else {
					# Cadastrar em banco de dados
					$grava=dbUsuario($matriz, 'alterar');
					
					# Verificar inclus�o de registro
					if($grava) {
						# acusar falta de parametros
						# Mensagem de aviso
						$msg="Registro Gravado com Sucesso!";
						$url="?modulo=$modulo&sub=$sub&acao=listar";
						aviso("Aviso", $msg, $url, 760);
						
						# Atualizar senha na session
						$sessLogin[senha]=$matriz[senha];
					}
					
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
		# Mensagem de aviso
		$msg="Opera��o n�o permitida!";
		$url="?modulo=$modulo";
		aviso("Aviso", $msg, $url, 760);	
	}
}

?>
