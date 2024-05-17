<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 16/10/2003
# Ultima altera��o: 20/10/2003
#    Altera��o No.: 003
#
# Fun��o:
#    Painel - Fun��es para controle de servi�o de radius (grupos)
#
# Fun��o de configura��es

function administracaoDominios($modulo, $sub, $acao, $registro, $matriz) {
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
		
		# Configura��es - Dominios
		if($sub=='dominios') {
		
			### Menu principal - usuarios logados apenas
			novaTabela2("[Administra��o de Dom�nios]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
						echo "<br><img src=".$html[imagem][dominios]." border=0 align=left >
						<b class=bold>Administra��o de Dom�nios</b>
						<br><span class=normal10>A se��o de <b>administra��o de dom�nios</b> permite a manuten��o de 
						dom�nios e possibilita o gerenciamento de contas de e-mail para os usu�rios. Atrav�s desta
						fun��o, devem ser criados apenas os dom�nios padr�o.</span>";
					htmlFechaColuna();			
					$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
					itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
					$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
					itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
					$texto=htmlMontaOpcao("<br>Listar", 'listar');
					itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
				fechaLinhaTabela();
			fechaTabela();
		
			echo "<br>";
			if($acao=='procurar') procurarDominios($modulo, $sub, $acao, $registro, $matriz);
			elseif(!$acao || $acao=='listar') listarDominios($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='adicionar') adicionarDominios($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='alterar') alterarDominios($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='excluir') excluirDominios($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='ver') verDominios($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='parametros') {
				$matriz[id]=$registro;
				listarDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='parametrosadicionar') {
				$tmp=explode(":",$registro);
				$registro=$tmp[0];
				$matriz[id]=$registro;
				adicionarDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='parametrosexcluir') {
				$tmp=explode(":",$registro);
				$registro=$tmp[0];
				$matriz[id]=$registro;
				excluirDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
			}
		}
	}
}

?>
