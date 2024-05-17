<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/01/2004
# Ultima alteração: 08/01/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para configurações



# Função de configurações
function config($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		if(!$sub) {
			### Menu principal - usuarios logados apenas
			novaTabela2("[Configurações]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 3);
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
						echo "<br><img src=".$html[imagem][configuracoes_sistema]." border=0 align=left >
						<b class=bold>Configurações do Sistema</b>
						<br><span class=normal10>Configurações do Sistema $configAppName.</span>";
					htmlFechaColuna();			
					$texto=htmlMontaOpcao("<br>Usuários", 'usuario');
					itemLinha($texto, "?modulo=acesso&sub=usuarios", 'center', $corFundo, 0, 'normal');
					$texto=htmlMontaOpcao("<br>Grupos", 'grupo');
					itemLinha($texto, "?modulo=acesso&sub=grupos", 'center', $corFundo, 0, 'normal');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();			
					$texto=htmlMontaOpcao("<br>Parâmetros", 'parametros');
					itemLinha($texto, "?modulo=$modulo&sub=parametros", 'center', $corFundo, 0, 'normal');
					itemLinha('&nbsp;', "#", 'center', $corFundo, 0, 'normal');
				fechaLinhaTabela();
			fechaTabela();
		}
		
		# verificação dos submodulos
		elseif($sub=='parametros') {
			parametros($modulo, $sub, $acao, $registro, $matriz);
		}
	}
	
}

?>
