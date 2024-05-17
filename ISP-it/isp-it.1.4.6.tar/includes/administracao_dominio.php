<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 16/10/2003
# Ultima alteração: 20/10/2003
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para controle de serviço de radius (grupos)
#
# Função de configurações

function administracaoDominios($modulo, $sub, $acao, $registro, $matriz) {
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
		
		# Configurações - Dominios
		if($sub=='dominios') {
		
			### Menu principal - usuarios logados apenas
			novaTabela2("[Administração de Domínios]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
						echo "<br><img src=".$html[imagem][dominios]." border=0 align=left >
						<b class=bold>Administração de Domínios</b>
						<br><span class=normal10>A seção de <b>administração de domínios</b> permite a manutenção de 
						domínios e possibilita o gerenciamento de contas de e-mail para os usuários. Através desta
						função, devem ser criados apenas os domínios padrão.</span>";
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
