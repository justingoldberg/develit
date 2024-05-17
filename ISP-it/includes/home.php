<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 20/05/2003
# Ultima altera��o: 29/01/2004
#    Altera��o No.: 005
#
# Fun��o:
#    Fun��es para p�ginas principal/home


# Menu principal
function home($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $modulos;
	
	$sessLogin = $_SESSION["sessLogin"];
	
	# Buscar informa��es sobre usuario - permiss�es
	$permissao=buscaPermissaoUsuario($sessLogin[login]);


	# Menu principal de acesso
	if(!$sub) {
		# Mostrar menu
		novaTabela2("[HOME]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('55%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][logoPequeno]." border=0 align=left>
					<b class=bold>$configAppName - HOME</b><br>
					<span class=normal10>P�gina principal e visualiza��o de fun��es
					iniciais para o $configAppName.</span>";
				htmlFechaColuna();
				htmlAbreColuna('5%', 'left', $corFundo, 0, 'normal');
					echo "&nbsp;";
				htmlFechaColuna();									
				$texto=htmlMontaOpcao("<br>Cadastros", 'cadastros');
				itemLinha($texto, "?modulo=cadastros", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Lan�amentos", 'menulancamento');
				itemLinha($texto, "?modulo=lancamentos", 'center', $corFundo, 0, 'normal');
				if( temContasAPagar() ){
					$texto=htmlMontaOpcao("<br><font color=red>Contas � Pagar</font>", 'menulancamento');
					itemLinha($texto, "?modulo=contas_a_pagar&acao=listar_pendentes", 'center', $corFundo, 0, 'normal');
				}
				if( $modulos['controleEstoque'] && produtosEstoqueEmFalta() ){
					$texto=htmlMontaOpcao("<br><font color=red>Estoque</font>", 'estoque');
					itemLinha($texto, "?modulo=cadastros&sub=produtosEstoque&acao=listar_emfalta", 'center', $corFundo, 0, 'normal');
				}
				else{
					htmlAbreColuna('5%', 'left', $corFundo, 0, 'normal');
						echo "&nbsp;";
					htmlFechaColuna();
				}
			fechaLinhaTabela();
		fechaTabela();


		# Mostrar busca de pessoas
		echo "<br>";
		procurarPessoas($modulo, $sub, $acao, $registro, $matriz);
		
		echo "<br>";
		procurarEnderecos($modulo, $sub, $acao, $registro, $matriz);
		
		echo "<br>";
		procurarEmailDominio($modulo, $sub, $acao, $registro, $matriz);
		
		echo "<br>";
		procurarDominioServicosPlanos($modulo, $sub, $acao, $registro, $matriz);

		echo "<br>";
		procurarServicosIVR($modulo, $sub, $acao, $registro, $matriz);
		}
	
	# Modulos
	elseif($sub=='usuarios') {
		# Menu de modulos
		cadastroUsuarios($modulo, $sub, $acao, $registro, $matriz);	
	}
	
}

?>
