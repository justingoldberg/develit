<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 20/06/2003
# Ultima alteração: 18/08/2003
#    Alteração No.: 004
#
# Função:
#    Painel - Funções para cadastros


# Menu principal
function cadastros($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $modulos;
	
	
	#$sessLogin =& $GLOBALS["sessLogin"];
	$sessLogin = $_SESSION["sessLogin"];
	
	# Buscar informações sobre usuario - permissões
	$permissao=buscaPermissaoUsuario($sessLogin[login]);
	

	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		# Menu principal de acesso
		if(!$sub) {
			novaTabela2("[Cadastros]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
						echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left>
						<b class=bold>Cadastros</b><br>
						<span class=normal10>Seção de cadastros  $configAppName.</span>";
					htmlFechaColuna();
				fechaLinhaTabela();
			fechaTabela();
			echo "<br>";
			if(!$acao) {
				novaTabela2SH("center", '100%', 0, 0, 0, $corFundo, $corBorda, 3);
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
							novaTabela2("[Cadastros Principais]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
								$texto=htmlMontaOpcao("Clientes", 'usuario');
								itemTabela($texto, "?modulo=$modulo&sub=clientes", 'Left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Fornecedores", 'usuario');
								itemTabela($texto, "?modulo=$modulo&sub=fornecedores", 'Left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Condomínios", 'condominios');
								itemTabela($texto, "?modulo=$modulo&sub=condominios", 'left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("POP", 'pops');
								itemTabela($texto, "?modulo=$modulo&sub=pop", 'left', $corFundo, 0, 'normal');
							fechaTabela();
						htmlFechaColuna();
						
						itemLinhaTMNOURL('&nbsp;&nbsp;&nbsp;', 'center', 'middle', '1', $corFundo, 0, 'normal10');
						htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
							if( $modulos['controleEstoque'] ){
								novaTabela2("[Produtos & Estoque]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
									$texto = htmlMontaOpcao("Produtos", 'produto');
									itemTabela($texto, "?modulo=$modulo&sub=produtos", 'Left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Estoque de Produtos", 'estoque');
									itemTabela($texto, "?modulo=$modulo&sub=produtosEstoque", 'Left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Produtos Compostos", 'equipamento');
									itemTabela($texto, "?modulo=$modulo&sub=produtoComposto", 'Left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Unidades", 'unidades');
									itemTabela($texto, "?modulo=configuracoes&sub=unidades", 'Left', $corFundo, 0, 'normal');
								fechaTabela();
								echo '<br />';
							}
							novaTabela2("[Equipamentos]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
								$texto=htmlMontaOpcao("Equipamentos", 'equipamento');
								itemTabela($texto, "?modulo=equipamento", 'Left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Tipos de Equipamentos", 'equipamento');
								itemTabela($texto, "?modulo=equiptoTipo", 'Left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Caracteristicas de Equipamentos/Tipos", 'equipamento');
								itemTabela($texto, "?modulo=equiptoCaracteristica", 'Left', $corFundo, 0, 'normal');
							fechaTabela();
						htmlFechaColuna();
					fechaLinhaTabela();
				fechaTabela();
			}
		}
		
		# Pessoas
		elseif($sub=='clientes' || $sub=='fornecedores' || $sub=='condominios') {
			pessoas($modulo, $sub, $acao, $registro, $matriz);	
		}
		elseif($sub=='pop') {
			pop($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='produtos') {
			Produtos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='produtoComposto') {
			ProdutoComposto( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif($sub=='produtosEstoque') {
			ProdutosEstoque( $modulo, $sub, $acao, $registro, $matriz );
		}
		
	}
}


?>