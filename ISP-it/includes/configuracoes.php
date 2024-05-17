<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 20/05/2003
# Ultima altera��o: 17/06/2004
#    Altera��o No.: 012
#
# Fun��o:
#    Painel - Fun��es para configura��es



# Fun��o de configura��es
function config($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		if(!$sub) {
			### Menu principal - usuarios logados apenas
			novaTabela2("[Configura��es]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 1);
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'left', $corFundo, 0, 'tabfundo1');
						echo "<br><img src=".$html[imagem][configuracoes_sistema]." border=0 align=left >
						<b class=bold>Configura��es</b>
						<br><span class=normal10>Configura��es do Sistema $configAppName.</span>";
					htmlFechaColuna();			
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'left', $corFundo, 0, 'tabfundo1');
					novaTabela2("Configura��es Gerais","center", '100%', 0, 3, 1, $corFundo, $corBorda, 6);
						novaLinhaTabela($corFundo, '100%');
							$texto=htmlMontaOpcao("<br>Usu�rios", 'usuario');
							itemLinha($texto, "?modulo=acesso&sub=usuarios", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Grupos", 'grupo');
							itemLinha($texto, "?modulo=acesso&sub=grupos", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Cidades", 'cidades');
							itemLinha($texto, "?modulo=$modulo&sub=cidades", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Documentos", 'documento');
							itemLinha($texto, "?modulo=$modulo&sub=documentos", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Endere�os", 'endereco');
							itemLinha($texto, "?modulo=$modulo&sub=enderecos", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Pessoas", 'pessoa');
							itemLinha($texto, "?modulo=$modulo&sub=pessoas", 'center', $corFundo, 0, 'normal');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							$texto=htmlMontaOpcao("<br>Bancos", 'banco');
							itemLinha($texto, "?modulo=$modulo&sub=bancos", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Cobran�as", 'cobranca');
							itemLinha($texto, "?modulo=$modulo&sub=cobranca", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Vencimentos", 'vencimento');
							itemLinha($texto, "?modulo=$modulo&sub=vencimentos", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Forma Cobran�a", 'forma_cobranca');
							itemLinha($texto, "?modulo=$modulo&sub=forma_cobranca", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Servi�os", 'servicos');
							itemLinha($texto, "?modulo=$modulo&sub=servicos", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Servi�o Adicional", 'servico_adicional');
							itemLinha($texto, "?modulo=$modulo&sub=servicoadicional", 'center', $corFundo, 0, 'normal');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							$texto=htmlMontaOpcao("<br>M�dulos", 'modulo');
							itemLinha($texto, "?modulo=$modulo&sub=modulos", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Par�metros", 'parametros');
							itemLinha($texto, "?modulo=$modulo&sub=parametros", 'center', $corFundo, 0, 'normal');
						
							$texto=htmlMontaOpcao("<br>Status", 'statusp');
							itemLinha($texto, "?modulo=$modulo&sub=status_servicos", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Unidades", 'config');
							itemLinha($texto, "?modulo=$modulo&sub=unidades", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Configura��es<br>Gerais", 'configuracoesgerais');
							itemLinha($texto, "?modulo=$modulo&sub=configuracoes", 'center', $corFundo, 0, 'normal');
							$texto=htmlMontaOpcao("<br>Contratos", 'contrato');
							itemLinha($texto, "?modulo=$modulo&sub=contratos", 'center', $corFundo, 0, 'normal');

						fechaLinhaTabela();
						
						novaLinhaTabela($corFundo, '100%');
							
							$texto=htmlMontaOpcao("<br>Prioridades", 'prioridade');
							itemLinha($texto, "?modulo=$modulo&sub=prioridades", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Grupos de Servi�o", 'contrato');
							itemLinha($texto, "?modulo=$modulo&sub=grupos_servico", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Tipos de Carteira", 'forma_cobranca');
							itemLinha($texto, "?modulo=$modulo&sub=tipoCarteira", 'center', $corFundo, 0, 'normal');							
							
						# Linhas comentadas por n�o estarem finalizados os m�dulos de aplica��es e m�o de obra
						
							//$texto=htmlMontaOpcao("<br>Aplica��es", 'parametros');
							//itemLinha($texto, "?modulo=$modulo&sub=aplicacao", 'center', $corFundo, 0, 'normal');

							//$texto=htmlMontaOpcao("<br>M�o de Obra", 'servicos');
							//itemLinha($texto, "?modulo=$modulo&sub=maodeobra", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Contra Partidas", 'parametros');
							itemLinha($texto, "?modulo=$modulo&sub=contraPartidaPadrao", 'center', $corFundo, 0, 'normal');

						//fechaLinhaTabela();
						
						//novaLinhaTabela($corFundo, '100%');
							
							$texto=htmlMontaOpcao("<br>Tipos de Impostos", 'parametros');
							itemLinha($texto, "?modulo=$modulo&sub=tiposImpostos", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Natureza da Presta��o", 'comentar');
							itemLinha($texto, "?modulo=$modulo&sub=naturezaprestacao", 'center', $corFundo, 0, 'normal');

						fechaLinhaTabela();
						
					fechaTabela();
					htmlFechaColuna();
				htmlFechaLinha();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'left', $corFundo, 0, 'tabfundo1');
					novaTabela2("Configura��es de Servidores","center", '100%', 0, 3, 1, $corFundo, $corBorda, 6);
						novaLinhaTabela($corFundo, '100%');
							$texto=htmlMontaOpcao("<br>Dom�nios", 'dominio');
							itemLinha($texto, "?modulo=$modulo&sub=dominios", 'center', $corFundo, 0, 'normal');

							$texto=htmlMontaOpcao("<br>Radius", 'admradius');
							itemLinha($texto, "?modulo=radius", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Servidores", 'servidores');
							itemLinha($texto, "?modulo=$modulo&sub=servidores", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Interfaces", 'interfaces');
							itemLinha($texto, "?modulo=$modulo&sub=interfaces", 'center', $corFundo, 0, 'normal');
							
							$texto=htmlMontaOpcao("<br>Bases", 'bases');
							itemLinha($texto, "?modulo=$modulo&sub=bases", 'center', $corFundo, 0, 'normal');
							
						fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
				htmlFechaLinha();
			fechaTabela();
		}
		
		# verifica��o dos submodulos
		elseif($sub=='cidades') {
			cidades($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='documentos') {
			tipoDocumentos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='enderecos') {
			tipoEnderecos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='pessoas') {
			tipoPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='cobranca') {
			tipoCobranca($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='vencimentos') {
			vencimentos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='unidades') {
			unidades($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='modulos') {
			modulos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='parametros') {
			parametros($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='bancos') {
			bancos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='forma_cobranca') {
			forma_cobranca($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='servicos') {
			servicos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='status_servicos') {
			statusServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='radius') {
			administracaoRadius($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='dominios') {
			administracaoDominios($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='prioridades') {
			prioridades($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='contratos') {
			contratos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='servicoadicional') {
			tipoServicoAdicional($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='grupos_servico') {
			gruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='aplicacao') {
			aplicacao($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='maodeobra') {
			maodeobra('maodeobra', $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='configuracoes') {
			parametrosConfig($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='servidores') {
			servidores($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='interfaces') {
			interfaces($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='bases') {
			bases($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='tipoCarteira') {
			tipoCarteira($modulo, $sub, $acao, $registro, $matriz);
		}
		
		elseif($sub=='contraPartidaPadrao') {
			contraPartidaPadrao($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='tiposImpostos') {
			tiposImpostos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='naturezaprestacao') {
			NaturezaPrestacao($modulo, $sub, $acao, $registro, $matriz);
		}
	}
	
}

?>
