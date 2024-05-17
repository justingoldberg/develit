<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 08/01/2004
# Ultima altera��o: 11/01/2005
#    Altera��o No.: 002
#
# Fun��o:
#    Fun��es para p�ginas principal/home


# Menu principal
function home($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$sessLogin = $_SESSION["sessLogin"];
	
	# Buscar informa��es sobre usuario - permiss�es
	$permissao=buscaPermissaoUsuario($sessLogin[login]);


	# Menu principal de acesso
	if(!$sub) {
		# Mostrar menu
		novaTabela2("[HOME]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('55%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][logoPequeno]." border=0 align=left>
					<b class=bold>$configAppName</b><br>
					<span class=normal10>Menu principal de fun��es do sistema. Utilize este menu
					de atalhos para agilizar sua navega��o.</span>";
				htmlFechaColuna();
				htmlAbreColuna('5%', 'left', $corFundo, 0, 'normal');
					echo "&nbsp;";
				htmlFechaColuna();									
				$texto=htmlMontaOpcao("<br>Adicionar<br>M�quina", 'incluir');
				itemLinha($texto, "?modulo=maquina&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Adicionar<br>Empresa", 'incluir');
				itemLinha($texto, "?modulo=empresas&acao=adicionar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		# Listagem / Busca de maquinas
		echo "<br>";
		if(!$acao) $acao='listar';
		procurarMaquinas('maquina', $sub, $acao, $registro, $matriz);
	}
	
	# Modulos
	elseif($sub=='usuarios') {
		# Menu de modulos
		cadastroUsuarios($modulo, $sub, $acao, $registro, $matriz);	
	}

}

?>
