<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 08/01/2004
# Ultima altera��o: 08/01/2004
#    Altera��o No.: 001
#
# Fun��o:
#    Painel - Fun��es para configura��es



# Fun��o de configura��es
function config($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		if(!$sub) {
			### Menu principal - usuarios logados apenas
			novaTabela2("[Configura��es]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 3);
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
						echo "<br><img src=".$html[imagem][configuracoes_sistema]." border=0 align=left >
						<b class=bold>Configura��es do Sistema</b>
						<br><span class=normal10>Configura��es do Sistema $configAppName.</span>";
					htmlFechaColuna();			
					$texto=htmlMontaOpcao("<br>Usu�rios", 'usuario');
					itemLinha($texto, "?modulo=acesso&sub=usuarios", 'center', $corFundo, 0, 'normal');
					$texto=htmlMontaOpcao("<br>Grupos", 'grupo');
					itemLinha($texto, "?modulo=acesso&sub=grupos", 'center', $corFundo, 0, 'normal');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();			
					$texto=htmlMontaOpcao("<br>Par�metros", 'parametros');
					itemLinha($texto, "?modulo=$modulo&sub=parametros", 'center', $corFundo, 0, 'normal');
					itemLinha('&nbsp;', "#", 'center', $corFundo, 0, 'normal');
				fechaLinhaTabela();
			fechaTabela();
		}
		
		# verifica��o dos submodulos
		elseif($sub=='parametros') {
			parametros($modulo, $sub, $acao, $registro, $matriz);
		}
	}
	
}

?>
